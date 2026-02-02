<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\BankSetting;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BankSettingController extends Controller
{
    public function index(Request $request)
    {
        $bank_setting = BankSetting::all();

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
        $bank_setting = BankSetting::find($request->bank_setting_id);

        dd($request->all());
        $amount = $request->amount;
        $prev_amount = $bank_setting->amount;
        $after_amount = round($prev_amount + $amount, 2);

        if($amount > 0){
            $type = 'adjustment-add';
        }else{
            $type = 'adjustment-subtract';
        }
        $bank_log = $bank_setting->bank_logs()->create([
            'bank_setting_id' => $bank_setting->id,
            'type' => $type,
            'remarks' => $request->remarks,
            'prev_amount' => $prev_amount,
            'amount' => abs($amount),
            'after_amount' => $after_amount,
        ]);
        $bank_setting->amount = $after_amount;
        $bank_setting->save();

        return redirect()->route('bank_setting.index')->withSuccess('Amount adjusted');
    }

}
