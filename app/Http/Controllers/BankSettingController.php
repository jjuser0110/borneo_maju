<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\BankSetting;
use App\Models\Stock;
use App\Models\Profit;
use App\Models\StockLog;

use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;

class BankSettingController extends Controller
{
    public function index(Request $request)
    {
        $bank_setting = BankSetting::orderBy('position', 'ASC')->get();

        return view('bank_setting.index')->with('bank_setting',$bank_setting);
    }

    public function create()
    {
        $bank = Bank::all();
        return view('bank_setting.create')->with('bank', $bank);
    }

    public function store(Request $request)
    {
        $bank_setting = BankSetting::create($request->all());

        return redirect()->route('bank_setting.index')->withSuccess('Data saved');
    }

    public function edit(BankSetting $bank_setting)
    {
        $bank = Bank::all();
        return view('bank_setting.create')->with('bank_setting',$bank_setting)->with('bank', $bank);
    }

    public function update(Request $request, BankSetting $bank_setting)
    {
        $bank_setting->update($request->all());
        return redirect()->route('bank_setting.index')->withSuccess('Data updated');
    }

    public function destroy(BankSetting $bank_setting)
    {
        $bank_setting->delete();

        return redirect()->route('bank_setting.index')->withSuccess('Data deleted');
    }

    public function adjust_money(Request $request)
    {
        DB::transaction(function () use ($request) {
            $bank_setting = BankSetting::lockForUpdate()->findOrFail($request->bank_setting_id);

            $amount       = (float) $request->amount;
            $prev_amount  = (float) $bank_setting->amount;
            $after_amount = round($prev_amount + $amount, 2);

            $type = $amount > 0
                ? 'adjustment_add'
                : 'adjustment_subtract';

            $bank_setting->bank_logs()->create([
                'bank_setting_id' => $bank_setting->id,
                'type'           => $type,
                'remarks'        => $request->remarks,
                'prev_amount'    => $prev_amount,
                'amount'         => abs($amount),
                'after_amount'   => $after_amount,
            ]);

            $bank_setting->update([
                'amount' => $after_amount,
            ]);
        });

        return back()->withSuccess('Amount adjusted');
    }

    public function viewlog(Request $request, BankSetting $bank_setting)
    {
        /*
        |--------------------------------------------------------------------------
        | Stocks
        |--------------------------------------------------------------------------
        | Default newest stock first.
        | DataTables can then sort the loaded rows by Balance, MYR, IDR, etc.
        */

        $stocks = $bank_setting->stocks()
            ->orderByDesc('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Bank Logs
        |--------------------------------------------------------------------------
        | Default date range = today
        */

        $dateFrom = $request->input('date_from', now()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        $types = $request->input('type', []);

        // Make sure type is always an array
        if (!is_array($types)) {
            $types = [$types];
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        | Using startOfDay/endOfDay instead of whereDate().
        | This is better especially when created_at is indexed.
        */

        $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
        $endDate   = \Carbon\Carbon::parse($dateTo)->endOfDay();


        $bank_logs = $bank_setting->bank_logs()
            ->whereBetween('created_at', [
                $startDate,
                $endDate
            ])

            ->when(!empty($types), function ($query) use ($types) {
                $query->whereIn('type', $types);
            })

            ->orderByDesc('created_at')
            ->get()

            ->map(function ($log) {

                $log->signed_amount =
                    $log->after_amount - $log->prev_amount;

                return $log;
            });


        return view('bank_setting.viewlog', compact(
            'bank_setting',
            'stocks',
            'bank_logs',
            'dateFrom',
            'dateTo',
            'types'
        ));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'bank_setting_id' => 'required',
            'idr_rate'        => 'required|numeric|min:0',
            'myr_amount'      => 'required|numeric|min:0',
            'idr_amount'      => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // $expectedIdr = (int) round($request->myr_amount * $request->idr_rate);
            // $actualIdr   = (int) round($request->idr_amount);

            // if ($expectedIdr !== $actualIdr) {
            //     throw new \Exception(
            //         'IDR amount does not match MYR amount × IDR rate'
            //     );
            // }

            $bank_setting = BankSetting::lockForUpdate()->findOrFail($request->bank_setting_id);

            $amount       = (float) $request->idr_amount;
            $prev_amount  = (float) $bank_setting->amount;
            $after_amount = round($prev_amount + $amount, 2);

            if ($request->type == 'stock_in') {
                $type = $amount > 0 ? 'stock_in' : 'stock_out';
            } else if ($request->type == 'transfer_in') {
                $type = $amount > 0 ? 'transfer_in' : 'transfer_out';
            }


            $stock = $bank_setting->stocks()->create([
                'idr_rate'    => $request->idr_rate,
                'myr_amount'  => $request->myr_amount,
                'idr_amount'  => $amount,
                'idr_balance' => $amount,
            ]);

            $bank_setting->bank_logs()->create([
                'bank_setting_id' => $bank_setting->id,
                'type'            => $type ?? $request->type,
                'remarks'         => null,
                'prev_amount'     => $prev_amount,
                'amount'          => abs($amount),
                'after_amount'    => $after_amount,
                'remarks'         => $request->remarks,
            ]);

            $bank_setting->update([
                'amount' => $after_amount,
            ]);
        });

        return back()->withSuccess('Stock added');
    }

    public function view_stock_log(Stock $stock)
    {
        return view('bank_setting.stocklog', compact('stock'));
    }

    public function destroy_stock(Stock $stock)
    {
        try {

            DB::transaction(function () use ($stock) {

                $stock = Stock::lockForUpdate()->findOrFail($stock->id);

                if (($stock->idr_amount - $stock->idr_balance) > 0) {
                    throw new \Exception('Stock has been used. Unable to delete stock.');
                }

                $bank_setting = BankSetting::lockForUpdate()->findOrFail($stock->bank_setting_id);

                $amount       = (float) -$stock->idr_balance;
                $prev_amount  = (float) $bank_setting->amount;
                $after_amount = round($prev_amount + $amount, 2);

                $bank_setting->bank_logs()->create([
                    'bank_setting_id' => $bank_setting->id,
                    'type'            => 'stock_delete',
                    'remarks'         => null,
                    'prev_amount'     => $prev_amount,
                    'amount'          => abs($amount),
                    'after_amount'    => $after_amount,
                ]);

                $bank_setting->update([
                    'amount' => $after_amount,
                ]);

                $stock->delete();
            });

            return redirect()->route('bank_setting.viewlog', $stock->bank_setting_id)->withSuccess('Data deleted');

        } catch (\Exception $e) {

            return back()->withErrors($e->getMessage());
        }
    }

    public function updateStockBalance(Request $request)
    {
        $request->validate([
            'stock_id'    => 'required|exists:stocks,id',
            'amount'      => 'required|numeric',
            'type'        => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $stock = Stock::lockForUpdate()->findOrFail($request->stock_id);

                if ($request->amount > $stock->idr_balance) {
                    throw new \Exception('Amount exceeds available balance.');
                }

                $bank_setting = BankSetting::lockForUpdate()->findOrFail($stock->bank_setting_id);

                $amount       = (float) $request->amount;
                $prev_amount  = (float) $bank_setting->amount;
                $after_amount = round($prev_amount - $amount, 2);

                if ($request->type == 'stock_adjust') {
                    $type = $amount < 0
                        ? 'stock_adjust_in'
                        : 'stock_adjust_out';
                } else if ($request->type == 'transfer') {
                    $type = $amount < 0
                        ? 'transfer_in'
                        : 'transfer_out';
                }


                $bank_setting->bank_logs()->create([
                    'bank_setting_id' => $bank_setting->id,
                    'type'            => $type ?? $request->type,
                    'remarks'         => $request->remarks,
                    'prev_amount'     => $prev_amount,
                    'amount'          => abs($amount),
                    'after_amount'    => $after_amount,
                ]);

                $bank_setting->update([
                    'amount' => $after_amount,
                ]);

                // Update stock balance
                $stock->update([
                    'idr_balance' => $stock->idr_balance - $amount
                ]);

                $stock->stock_logs()->create([
                    'bank_setting_id'   => $stock->bank_setting_id,
                    'idr_amount'        => $request->amount,
                    'stock_idr_rate'    => $stock->idr_rate,
                    'capital_used'      => round($request->amount / $stock->idr_rate, 2),
                    'remarks'           => $request->remarks,
                ]);
            });

            return back()->withSuccess('Data updated');
        } catch (\Exception $e) {

            return back()->withErrors($e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->order as $item) {
                BankSetting::where('id', $item['id'])
                    ->update(['position' => $item['position']]);
            }
        });

        return response()->json(['status' => 'success']);
    }

    public function editRate(Request $request)
    {
        DB::transaction(function () use ($request) {

            $stock = Stock::with('stock_logs')
                ->findOrFail($request->stock_id);

            $newRate = (float) $request->new_rate;

            $newMyrAmount = round(
                $stock->idr_amount / $newRate,
                2
            );

            $stock->update([
                'idr_rate'  => $newRate,
                'myr_amount' => $newMyrAmount,
            ]);

            foreach ($stock->stock_logs as $log) {

                $capitalUsed = round(
                    $log->idr_amount / $newRate,
                    2
                );

                $log->update([
                    'stock_idr_rate' => $newRate,
                    'capital_used'   => $capitalUsed,
                ]);

                $profit = Profit::where('order_id', $log->order_id)
                    ->first();

                if ($profit) {
                    $allCapitalUsed = StockLog::where('order_id', $log->order_id)
                        ->sum('capital_used');
                    $profitAmount = round(
                        $profit->amount_received - $allCapitalUsed,
                        2
                    );

                    $profit->update([
                        'capital_used' => $allCapitalUsed,
                        'profit'       => $profitAmount,
                    ]);
                }
            }
        });

        return back()->withSuccess('Rate updated successfully');
    }
}
