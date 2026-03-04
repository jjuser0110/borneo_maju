<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\BankLog;
use App\Models\BankSetting;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Profit;
use App\Models\Stock;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function sales_report(Request $request)
    {
        /** ---------------------------
         *  LOGIN USER
         * ---------------------------- */
        if ($request->user_id > 0) {
            $login_user = User::findOrFail($request->user_id);
        } else {
            $login_user = Auth::user();
        }

        /** ---------------------------
         *  DATE RANGE
         * ---------------------------- */
        $date_from = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfDay();

        $date_to = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        /** ---------------------------
         *  GET AGENTS
         * ---------------------------- */
        if ($login_user->role_id == 1) {
            $role1Users = User::where('role_id', 1)->pluck('id');
            $agentsQuery = User::whereIn('upline', $role1Users);
        } else {
            $agentsQuery = User::where('upline', $login_user->id);
        }

        /** ---------------------------
         *  AGENTS WITH TOTALS
         * ---------------------------- */
        $agents = $agentsQuery
            ->withCount([
                'order_details as total_order' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ])
            ->withSum([
                'order_details as total_idr' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ], 'idr_amount')
            ->withSum([
                'order_details as total_myr' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ], 'myr_amount')
            ->withSum([
                'order_details as total_processing_fees' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ], 'processing_fees')
            ->withSum([
                'order_details as total_do_up' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ], 'do_up')
            ->withSum([
                'order_details as total_agent_do_up' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    });
                }
            ], 'agent_do_up')
            ->get();

        /** ---------------------------
         *  MY OWN ORDERS
         * ---------------------------- */
        $my_order = $login_user->orders()
            ->where('status', 'completed')
            ->whereBetween('order_datetime', [$date_from, $date_to])
            ->withSum('orderDetails', 'do_up')
            ->withSum('orderDetails', 'profit')
            ->get();

        /** ---------------------------
         *  VIEW
         * ---------------------------- */

        $total_collected = $agents->sum('total_do_up') + $my_order->sum('total_amount');
        $total_do_up = $login_user->order_details()->whereHas('order', function ($o) use ($date_from, $date_to) {
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed');
                    })->sum('do_up') ?? 0;
        $earning = $total_collected - $total_do_up;
        // dd($my_order);
        return view('report.sales_report', [
            'agents'     => $agents,
            'my_order'   => $my_order,
            'date_from'  => $date_from->format('Y-m-d'),
            'date_to'    => $date_to->format('Y-m-d'),
            'total_collected' => $total_collected,
            'total_do_up' => $total_do_up,
            'earning'    => $earning,
        ]);
    }

    public function daily_report(Request $request)
    {
        /** ---------------------------
         *  LOGIN USER
         * ---------------------------- */
        if (Auth::user()->role_id !== 1) {
            return back()->withErrors('Access denied');
        }

        /** ---------------------------
         *  DATE RANGE
         * ---------------------------- */
        $date = $request->date;

        $date_from = $request->date
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::now()->startOfDay();

        $date_to = $request->date
            ? Carbon::parse($request->date)->endOfDay()
            : Carbon::now()->endOfDay();

        $stock_in = Stock::where('created_at', '>=', $date_from)
            ->where('created_at', '<=', $date_to)
            ->sum('idr_amount');

        $orders = Order::where('status_at', '>=', $date_from)
            ->where('status_at', '<=', $date_to)
            ->where('status', 'completed');

        $stock_out = $orders->sum('idr_amount');

        $profits = Profit::whereHas('order', function ($q) use ($date_from, $date_to) {
            $q->whereBetween('status_at', [$date_from, $date_to]);
        });
        $capital_used     = $profits->sum('capital_used');
        $amount_received  = $profits->sum('amount_received');
        $profit           = $profits->sum('profit');

        $bankSettings = BankSetting::all();
        $bankLogs = BankLog::select(
                'bank_setting_id',
                DB::raw("
                    SUM(
                        CASE
                            WHEN type = 'stock_in' THEN amount
                            WHEN type = 'stock_delete' THEN -amount
                            ELSE 0
                        END
                    ) as total_stock_in
                "),
                DB::raw("
                    SUM(
                        CASE
                            WHEN type = 'stock_out' THEN amount
                            ELSE 0
                        END
                    ) as total_stock_out
                ")
            )
            ->whereDate('created_at', $date)
            ->groupBy('bank_setting_id')
            ->get()
            ->keyBy('bank_setting_id');

        return view('report.daily_report', compact(
            'date',
            'stock_in',
            'stock_out',
            'capital_used',
            'amount_received',
            'profit',
            'bankSettings',
            'bankLogs',
        ));
    }

}
