<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

}
