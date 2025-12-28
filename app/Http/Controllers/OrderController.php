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
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $loginUser = Auth::user();
        if($loginUser->role_id!=3){
            $order = Order::all();
        }else{
            $order = Order::where('user_id', $loginUser->id)->get();
        }

        return view('order.index')->with('order',$order);
    }

    public function pending(Request $request)
    {
        $loginUser = Auth::user();
        $order = Order::where('status', "pending")->get();
        $pending = true;

        return view('order.pending')->with('order',$order)->with('pending', $pending);
    }

    public function view(Request $request, Order $order)
    {
        $loginUser = Auth::user();

        return view('order.view')->with('order',$order);
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
        $total_amount = round($request->myr_amount + $request->processing_fees, 2);
        $request->merge(['total_amount' => $total_amount]);
        $order->update($request->all());
        return redirect()->route('order.index')->withSuccess('Data updated');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('order.index')->withSuccess('Data deleted');
    }

    public function pending_update(Request $request, Order $order)
    {
        if ($order->status === 'completed' || $request->status !== 'completed') {
            return redirect()->route('order.index')->withSuccess('Data updated');
        }

        DB::transaction(function () use ($request, $order) {

            // Update order
            $order->update([
                'status' => 'completed',
                'status_at' => now(),
                'status_by_id' => Auth::id(),
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

            while ($currentUser) {
                if($currentUser->role_id == 1){
                    // Admin does not get commission
                    break;
                }
                $myrAmount = round($idrAmount / $currentUser->idr_rate, 2);
                $totalAmount = round($myrAmount + $currentUser->processing_fees, 2);
                $profit = round($orderTotal - $totalAmount, 2);

                OrderDetail::create([
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
            }
        });

        return redirect()->route('order.index')->withSuccess('Data updated');
    }


}
