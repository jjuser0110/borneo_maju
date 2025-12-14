<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PointHistory;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $loginUser = Auth::user();
        if($loginUser->role_id == 3){
            $agent = User::where('role_id',3)->where('upline',$loginUser->id)->get();
        }else{
            $adminstaff_ids = User::where('role_id',[1,2])->pluck('id')->toArray();
            $agent = User::where('role_id',3)->whereIn('upline',$adminstaff_ids)->get();
        }

        return view('agent.index')->with('agent',$agent);
    }

    public function create()
    {
        return view('agent.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $loginUser = Auth::user();
        if($loginUser->role_id == 3){
            $request->merge(['username'=> $loginUser->username.$request->username]);
        }

        $checkusername = User::where('username',$request->username)->first();
        if($checkusername){
            return redirect()->back()->withErrors('Username already taken')->withInput();
        }
        
        $request->merge(['password' => Hash::make($request->password),'role_id'=>3,'upline'=>Auth::user()->id]);

        $agent = User::create($request->all());

        PointHistory::create([
            'agent_id' => $agent->id,
            'point_before' => 0,
            'point' => $request->point,
            'point_after' => $request->point,
            'description' => 'Top Up From '.$loginUser->username
        ]);

        if($loginUser->role_id == 3){
            $point_before = $loginUser->point;
            $point_after = $loginUser->point - $request->point;
            PointHistory::create([
                'agent_id' => $loginUser->id,
                'point_before' => $point_before,
                'point' => $request->point,
                'point_after' => $point_after,
                'description' => 'Transfer To '.$agent->username
            ]);
        }
        
        $agent->save_history()->create([
            'field_name' => 'idr_rate',
            'old_value' => null,
            'new_value' => $request->idr_rate,
        ]);
        
        $agent->save_history()->create([
            'field_name' => 'processing_fees',
            'old_value' => null,
            'new_value' => $request->processing_fees,
        ]);

        return redirect()->route('agent.index')->withSuccess('Data saved');
    }

    public function edit(User $agent)
    {
        return view('agent.create')->with('agent',$agent);
    }

    public function downline(User $agent)
    {
        return view('agent.downline')->with('agent',$agent);
    }

    public function update(Request $request, User $agent)
    {
        if($request->password !=null){
            $request->merge(['password' => Hash::make($request->password)]);
        }else{
            $request->request->remove('password');
        }
        
        if($request->idr_rate != $agent->idr_rate){
            $agent->save_history()->create([
                'field_name' => 'idr_rate',
                'old_value' => $agent->idr_rate,
                'new_value' => $request->idr_rate,
            ]);

            $difference = $agent->idr_rate - $request->idr_rate;
            $this->updateDownlines($agent->downlines, 'idr_rate', $difference);
        }
        if($request->processing_fees != $agent->processing_fees){
            $agent->save_history()->create([
                'field_name' => 'processing_fees',
                'old_value' => $agent->processing_fees,
                'new_value' => $request->processing_fees,
            ]);
            
            $difference = $agent->processing_fees - $request->processing_fees;
            $this->updateDownlines($agent->downlines, 'processing_fees', $difference);
        }
        $agent->update($request->all());
        return redirect()->route('agent.index')->withSuccess('Data updated');
    }

    public function destroy(User $agent)
    {
        $agent->delete();

        return redirect()->route('agent.index')->withSuccess('Data deleted');
    }

    private function updateDownlines($downlines, $field, $difference)
    {
        foreach ($downlines as $downline) {
            $before = $downline->$field;
            $after = $before - $difference;

            $downline->update([$field => $after]);
            $downline->save_history()->create([
                'field_name' => $field,
                'old_value' => $before,
                'new_value' => $after,
            ]);

            if ($downline->downlines->count() > 0) {
                $this->updateDownlines($downline->downlines, $field, $difference);
            }
        }
    }


}
