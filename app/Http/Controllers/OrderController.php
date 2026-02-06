<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PointHistory;
use App\Models\OrderDetail;
use App\Models\User;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\RunningNumber;
use Carbon\Carbon;
use App\Models\Bank;
use App\Models\BankSetting;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $date_from = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfDay();

        $date_to = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $loginUser = Auth::user();
        if($loginUser->role_id!=3){
            $order = Order::whereBetween('order_datetime', [$date_from, $date_to])->get();
        }else{
            $order = Order::where('user_id', $loginUser->id)->whereBetween('order_datetime', [$date_from, $date_to])->get();
        }

        return view('order.index', [
            'order'    => $order,
            'date_from' => $date_from->format('Y-m-d'),
            'date_to'   => $date_to->format('Y-m-d'),
        ]);
    }

    public function pending(Request $request)
    {
        $loginUser = Auth::user();
        $order = Order::whereIn('status', ["pending", "processing"])->get();
        $pending = true;

        return view('order.pending')->with('order',$order)->with('pending', $pending);
    }

    public function view(Request $request, Order $order)
    {
        $loginUser = Auth::user();
        $order->update(['status'=>'processing']);
        $bankSettings = BankSetting::where('is_active', 1)->get();

        return view('order.view')->with('order',$order)->with('bankSettings', $bankSettings);
    }

    public function view_details(Request $request, Order $order)
    {
        $loginUser = Auth::user();
        $view = true;

        return view('order.view')->with('order',$order)->with('view', $view);
    }

    public function create()
    {
        $loginUser = Auth::user();
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->month;

        $check = RunningNumber::where('name', 'order')->where('code', $loginUser->username)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$check) {
            $check = RunningNumber::create([
                'code' => $loginUser->username,
                'name' => 'order',
                'year' => $year,
                'month' => $month,
                'no_of_digit_behind' => 4,
                'running_no' => 1,
            ]);
        }

        $order_no = $check->code .
            $check->year .
            sprintf('%02d', $check->month) .
            sprintf('%0' . $check->no_of_digit_behind . 'd', $check->running_no);

        $bank = Bank::all();
        return view('order.create', [
            'order_no' => $order_no,
            'code' => $check->code,
            'year' => $check->year,
            'month' => $check->month,
            'bank' => $bank,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $loginUser = Auth::user();
        $total_amount = round($request->myr_amount + $request->processing_fees, 2);
        $request->merge(['user_id' => $loginUser->id, 'total_amount' => $total_amount]);
        $order = Order::create($request->all());
        $check = RunningNumber::where('code', $request->code)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->first();
        $check->increment('running_no');

        $point_before = $loginUser->point;
        $point_after = round($loginUser->point - $request->myr_amount, 2);
        PointHistory::create([
            'agent_id' => $loginUser->id,
            'point_before' => $point_before,
            'point' => $request->myr_amount,
            'point_after' => $point_after,
            'description' => 'Order '.$order->order_no
        ]);
        $loginUser->update(['point'=>$point_after]);

        return redirect()->route('order.index')->withSuccess('Data saved');
    }

    public function edit(Order $order)
    {
        $bank = Bank::all();
        return view('order.create', [
            'order' => $order,
            'bank' => $bank,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        if($order->status != 'pending'){
            return redirect()->route('order.index')->withError('Only pending order can be updated');
        }
        $total_amount = round($request->myr_amount + $request->processing_fees, 2);
        $request->merge(['total_amount' => $total_amount]);
        $order->update($request->all());
        return redirect()->route('order.index')->withSuccess('Data updated');
    }

    public function destroy(Order $order)
    {
        if($order->status == 'pending'){
            $order->delete();
        }else{
            return redirect()->route('order.index')->withError('Only pending order can be deleted');
        }

        return redirect()->route('order.index')->withSuccess('Data deleted');
    }

    public function pending_update(Request $request, Order $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('order.edit', $order)->withErrors('Order is completed.');
        }

        try {
            DB::transaction(function () use ($request, $order) {

                $bank_setting = BankSetting::lockForUpdate()->findOrFail($request->bank_setting_id);

                if ($bank_setting->amount < $order->idr_amount) {
                    throw new \Exception('Amount in selected bank is less than order amount.');
                }

                // Update order
                $order->update([
                    'status'          => 'completed',
                    'status_at'       => now(),
                    'status_by_id'    => Auth::id(),
                    'bank_setting_id' => $request->bank_setting_id,
                    'remarks'         => $request->remarks,
                ]);

                // Receipt upload
                if ($request->hasFile('receipt')) {
                    $upload = $this->upload($request->receipt, 'receipt', $order->id);

                    $order->file_attachments()->create([
                        'file_name' => $upload['file_name'],
                        'file_path' => $upload['file_path'],
                        'file_type' => $upload['file_type'],
                    ]);
                }

                $idrAmount   = $order->idr_amount;
                $orderTotal  = $order->total_amount;

                // Start from order owner
                $currentUser = $order->user;
                $amount_received = 0;

                while ($currentUser) {
                    if($currentUser->role_id == 1){
                        // Admin does not get commission
                        break;
                    }
                    $myrAmount = round($idrAmount / $currentUser->idr_rate, 2);
                    $totalAmount = round($myrAmount + $currentUser->processing_fees, 2);
                    $profit = round($orderTotal - $totalAmount, 2);

                    $order_details = OrderDetail::create([
                        'order_id'         => $order->id,
                        'user_id'          => $currentUser->id,
                        'idr_amount'       => $idrAmount,
                        'idr_rate'         => $currentUser->idr_rate,
                        'myr_amount'       => $myrAmount,
                        'processing_fees'  => $currentUser->processing_fees,
                        'total_amount'     => $totalAmount,
                        'upline'           => $currentUser->upline,
                        'do_up'            => $totalAmount,
                        'profit'           => $profit,
                    ]);

                    // Move up
                    $orderTotal = $totalAmount;
                    $currentUser = $currentUser->upline
                        ? User::find($currentUser->upline)
                        : null;

                    if($currentUser && $currentUser->role_id != 1){
                        $myrAmount2 = round($idrAmount / $currentUser->idr_rate, 2);
                        $totalAmount2 = round($myrAmount2 + $currentUser->processing_fees, 2);
                        $order_details->update([
                            'agent_do_up' => $totalAmount2,
                        ]);
                    } else {
                        $amount_received = $order_details->do_up;
                    }
                }

                $qty_needed = $idrAmount;
                $totalDeducted = 0;
                while ($qty_needed > 0) {
                    $stock = Stock::where('bank_setting_id', $bank_setting->id)
                        ->where('idr_balance', '>', 0)
                        ->orderBy('created_at', 'ASC')
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        $take = $qty_needed;

                        $totalDeducted += $take;
                        break;
                    }

                    $take = min($qty_needed, $stock->idr_balance);

                    $stock->stock_logs()->create([
                        'bank_setting_id' => $bank_setting->id,
                        'order_id'        => $order->id,
                        'idr_amount'      => $take,
                        'stock_idr_rate'  => $stock->idr_rate,
                        'capital_used'    => round($take / $stock->idr_rate, 2),
                    ]);

                    $stock->update([
                        'idr_balance' => $stock->idr_balance - $take,
                    ]);

                    $totalDeducted += $take;
                    $qty_needed -= $take;
                }

                $amount       = (float) $totalDeducted;
                $prev_amount  = (float) $bank_setting->amount;
                $after_amount = round($prev_amount - $amount, 2);

                $type = 'stock_out';

                $bank_setting->bank_logs()->create([
                    'bank_setting_id' => $bank_setting->id,
                    'order_id'        => $order->id,
                    'type'            => $type,
                    'remarks'         => 'Order ' . $order->order_no,
                    'prev_amount'     => $prev_amount,
                    'amount'          => abs($amount),
                    'after_amount'    => $after_amount,
                ]);

                $bank_setting->update([
                    'amount' => $after_amount,
                ]);

                $capital_used = $order->stock_logs()->sum('capital_used');
                $profit = $amount_received - $capital_used;
                $order->profit()->create([
                    'amount_received' => $amount_received,
                    'capital_used'    => $capital_used,
                    'profit'          => $profit,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route('order.index')->withSuccess('Data updated');
    }
}
