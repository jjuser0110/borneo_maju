<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function change_language(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,bm,cn',
        ]);
        // dd($request->all());
        app()->setLocale($request->language);
        session(['locale' => $request->language]);

        // dd(session('locale'));
        return redirect()->back();
    }

    public function index(Request $request)
    {
        $date_from = $request->date_from
        ? Carbon::parse($request->date_from)->startOfDay()
        : Carbon::now()->startOfDay();

        $date_to = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $today_order = Order::whereBetween('created_at', [$date_from, $date_to])->get();
        $order_count = $today_order->count();
        $total_idr_amount = $today_order->sum('idr_amount');

        $orderDetails = OrderDetail::whereBetween('created_at', [$date_from, $date_to])->whereIn('upline',[1,2])->get();
        $total_myr = $orderDetails->sum('myr_amount');
        $total_processing_fees = $orderDetails->sum('processing_fees');


        $date_from = $date_from->format('Y-m-d');
        $date_to   = $date_to->format('Y-m-d');
        return view('home',compact('date_from', 'date_to', 'order_count', 'total_idr_amount', 'total_myr', 'total_processing_fees'));
    }

    public function my_point_logs(Request $request)
    {
        return view('my_point_log');
    }

    public function change_password(Request $request){
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);


        if ($validator->fails()) {
            $message = "";
            foreach($validator->messages()->messages() as $m){
                foreach($m as $mm){
                    $message .=$mm.'\n';
                }
            }
            return redirect()->back()->withInfo($message);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('home')->withSuccess('Password changed successfully.');
    }

    public function removeimage($image_id){
        $file = FileAttachment::find($image_id);
        if($file){
            $file->delete();
            return redirect()->back()->withSuccess('Image removed successfully');
        }
        return redirect()->back()->withErrors('Image not found');
    }
}
