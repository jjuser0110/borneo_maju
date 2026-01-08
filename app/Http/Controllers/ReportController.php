<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\User;
use App\Models\Order;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales_report(Request $request)
    {
        if($request->user_id >0){
            $login_user = User::find($request->user_id);
        }else{
            $login_user = Auth::user();
        }
        $date_from = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfDay();

        $date_to = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        // Determine agents
        if ($login_user->role_id == 1) {
            $allrole1 = User::where('role_id', 1)->pluck('id');
            $agents = User::whereIn('upline', $allrole1);
        } else {
            $agents = User::where('upline', $login_user->id);
        }

        // Aggregate orders per agent
        $agents = $agents
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
                    $q->whereHas('order', fn ($o) =>
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed')
                    );
                }
            ], 'idr_amount')
            ->withSum([
                'order_details as total_myr' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', fn ($o) =>
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed')
                    );
                }
            ], 'myr_amount')
            ->withSum([
                'order_details as total_processing_fees' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', fn ($o) =>
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed')
                    );
                }
            ], 'processing_fees')
            ->withSum([
                'order_details as total_do_up' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', fn ($o) =>
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed')
                    );
                }
            ], 'do_up')
            ->withSum([
                'order_details as total_profit' => function ($q) use ($date_from, $date_to) {
                    $q->whereHas('order', fn ($o) =>
                        $o->whereBetween('order_datetime', [$date_from, $date_to])
                        ->where('status', 'completed')
                    );
                }
            ], 'profit')
            ->get();

        return view('report.sales_report', [
            'agents'    => $agents,
            'date_from' => $date_from->format('Y-m-d'),
            'date_to'   => $date_to->format('Y-m-d'),
        ]);
    }

}
