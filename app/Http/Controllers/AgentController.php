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
use DB;

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
        $loginUser = Auth::user();

        $rules = [
            'limit' => ['required', 'numeric'],
        ];

        if ($loginUser->role_id !== 1 && !is_null($loginUser->limit)) { // not admin
            $rules['limit'][] = 'max:' . $loginUser->limit;
        }

        $request->validate($rules);

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
            $loginUser->update(['point'=>$point_after]);
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

    public function viewlog(User $agent)
    {
        return view('agent.viewlog')->with('agent',$agent);
    }

    public function update(Request $request, User $agent)
    {
        $user = Auth::user();

        $rules = [
            'limit' => ['required', 'numeric'],
        ];

        if ($user->role_id !== 1 && !is_null($user->limit)) { // not admin
            $rules['limit'][] = 'max:' . $user->limit;
        }

        $request->validate($rules);

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
        if($request->limit != $agent->limit){
            $agent->save_history()->create([
                'field_name' => 'limit',
                'old_value' => $agent->limit,
                'new_value' => $request->limit,
            ]);

            $difference = $agent->limit - $request->limit;
            $this->updateDownlines($agent->downlines, 'limit', $difference);
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

    public function addPoint(Request $request, User $agent)
    {
        $request->validate([
            'point' => 'required|integer|min:1',
        ]);

        $loginUser = Auth::user();

        DB::transaction(function () use ($agent, $request, $loginUser) {

            $agent = User::where('id', $agent->id)
                ->lockForUpdate()
                ->first();

            $point_before = $agent->point;
            $point_after  = $point_before + $request->point;

            $agent->update([
                'point' => $point_after,
            ]);

            PointHistory::create([
                'agent_id'     => $agent->id,
                'point_before' => $point_before,
                'point'        => $request->point,
                'point_after'  => $point_after,
                'description'  => 'Top Up From ' . $loginUser->username,
            ]);


            if($loginUser->role_id == 3){
                $loginuser_point_before = $loginUser->point;
                $loginuser_point_after = $loginUser->point - $request->point;
                PointHistory::create([
                    'agent_id' => $loginUser->id,
                    'point_before' => $loginuser_point_before,
                    'point' => -$request->point,
                    'point_after' => $loginuser_point_after,
                    'description' => 'Transfer To '.$agent->username
                ]);
                $loginUser->update(['point'=>$loginuser_point_after]);
            }
        });

        return back()->with('success', 'Point added successfully');
    }

    public function deductPoint(Request $request, User $agent)
    {
        $request->validate([
            'point' => 'required|integer|min:1',
        ]);

        $loginUser = Auth::user();

        DB::transaction(function () use ($agent, $request, $loginUser) {

            $agent = User::where('id', $agent->id)
                ->lockForUpdate()
                ->firstOrFail();

            $point_before = $agent->point;
            $point_after  = $point_before - $request->point;

            if ($point_after < 0) {
                throw new \Exception('Insufficient points');
            }

            $agent->update([
                'point' => $point_after,
            ]);

            PointHistory::create([
                'agent_id'     => $agent->id,
                'point_before' => $point_before,
                'point'        => -$request->point,
                'point_after'  => $point_after,
                'description'  => 'Deducted by ' . $loginUser->username,
            ]);

            if($loginUser->role_id == 3){
                $loginuser_point_before = $loginUser->point;
                $loginuser_point_after = $loginUser->point + $request->point;
                PointHistory::create([
                    'agent_id' => $loginUser->id,
                    'point_before' => $loginuser_point_before,
                    'point' => $request->point,
                    'point_after' => $loginuser_point_after,
                    'description' => 'Deducted '.$agent->username
                ]);
                $loginUser->update(['point'=>$loginuser_point_after]);
            }
        });

        return back()->with('success', 'Point deducted successfully');
    }
}
