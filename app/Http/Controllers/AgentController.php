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
            $adminstaff_ids = User::whereIn('role_id',[1,2])->pluck('id')->toArray();
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
            'username'        => ['required', 'string', 'max:255'],
            'password'        => ['required', 'string', 'min:8'],
            'name'            => ['required', 'string', 'max:255'],
            'is_active'       => ['nullable', 'in:0,1'],
            'idr_rate'        => ['required', 'numeric', 'min:0'],
            'processing_fees' => ['required', 'numeric', 'min:0'],
            'point'           => ['nullable', 'numeric', 'min:0'],
            'limit'           => ['required', 'numeric', 'min:0'],
            'idr_limit'       => ['required', 'numeric', 'min:0'],
        ];

        if ($loginUser->role_id !== 1 && !is_null($loginUser->limit)) { // not admin
            $rules['limit'][] = 'max:' . $loginUser->limit;
        }

        if ($loginUser->role_id !== 1 && !is_null($loginUser->idr_limit)) { // not admin
            $rules['idr_limit'][] = 'max:' . $loginUser->idr_limit;
        }

        $request->validate($rules);

        $username = $request->username;
        if($loginUser->role_id == 3){
            $username = $loginUser->username.$request->username;
        }

        $checkusername = User::withTrashed()->where('username',$username)->first();
        if($checkusername){
            return redirect()->back()->withErrors('Username already taken')->withInput();
        }

        $point = round((float) ($request->point ?? 0), 2);

        try {
            DB::transaction(function () use ($request, $loginUser, $username, $point) {
                // Agents pay for the starting points out of their own balance.
                if ($loginUser->role_id == 3) {
                    $loginUser = User::where('id', $loginUser->id)->lockForUpdate()->first();
                    if ($loginUser->point < $point) {
                        throw new \Exception('Insufficient points to transfer.');
                    }
                }

                // Only these fields come from the form; role and upline are fixed here.
                $agent = User::create([
                    'name'            => $request->name,
                    'username'        => $username,
                    'password'        => Hash::make($request->password),
                    'is_active'       => $request->is_active == '0' ? 0 : 1,
                    'idr_rate'        => $request->idr_rate,
                    'processing_fees' => $request->processing_fees,
                    'limit'           => $request->limit,
                    'idr_limit'       => $request->idr_limit,
                    'point'           => $point,
                    'role_id'         => 3,
                    'upline'          => $loginUser->id,
                ]);

                PointHistory::create([
                    'agent_id' => $agent->id,
                    'point_before' => 0,
                    'point' => $point,
                    'point_after' => $point,
                    'description' => 'Top Up From '.$loginUser->username
                ]);

                if($loginUser->role_id == 3){
                    $point_before = $loginUser->point;
                    $point_after = round($loginUser->point - $point, 2);
                    PointHistory::create([
                        'agent_id' => $loginUser->id,
                        'point_before' => $point_before,
                        'point' => $point,
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
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->route('agent.index')->withSuccess('Data saved');
    }

    public function edit(User $agent)
    {
        abort_unless(Auth::user()->canManage($agent), 403);

        return view('agent.create')->with('agent',$agent);
    }

    public function downline(User $agent)
    {
        abort_unless(Auth::user()->canView($agent), 403);

        return view('agent.downline')->with('agent',$agent);
    }

    public function viewlog(User $agent)
    {
        abort_unless(Auth::user()->canView($agent), 403);

        return view('agent.viewlog')->with('agent',$agent);
    }

    public function update(Request $request, User $agent)
    {
        $user = Auth::user();
        abort_unless($user->canManage($agent), 403);

        $rules = [
            'password'        => ['nullable', 'string', 'min:8'],
            'name'            => ['sometimes', 'required', 'string', 'max:255'],
            'is_active'       => ['nullable', 'in:0,1'],
            'idr_rate'        => ['required', 'numeric', 'min:0'],
            'processing_fees' => ['required', 'numeric', 'min:0'],
            'limit'           => ['required', 'numeric', 'min:0'],
            'idr_limit'       => ['required', 'numeric', 'min:0'],
        ];

        if ($user->role_id !== 1 && !is_null($user->limit)) { // not admin
            $rules['limit'][] = 'max:' . $user->limit;
        }

        if ($user->role_id !== 1 && !is_null($user->idr_limit)) { // not admin
            $rules['idr_limit'][] = 'max:' . $user->idr_limit;
        }

        $request->validate($rules);

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

        if($request->idr_limit != $agent->idr_limit){
            $agent->save_history()->create([
                'field_name' => 'idr_limit',
                'old_value' => $agent->idr_limit,
                'new_value' => $request->idr_limit,
            ]);

            $difference = $agent->idr_limit - $request->idr_limit;
            $this->updateDownlines($agent->downlines, 'idr_limit', $difference);
        }
        // Only these fields may be changed: never username, point, role_id or upline.
        $data = $request->only(['name', 'is_active', 'idr_rate', 'processing_fees', 'limit', 'idr_limit']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $agent->update($data);
        return redirect()->route('agent.index')->withSuccess('Data updated');
    }

    public function destroy(User $agent)
    {
        abort_unless($agent->isAgent(), 404);

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
        abort_unless($loginUser->canManage($agent), 403);

        // Check if agent has enough points before starting transaction
        if ($loginUser->role_id == 3 && $loginUser->point < $request->point) {
            return back()->withErrors('Insufficient points to transfer.');
        }

        try{
            DB::transaction(function () use ($agent, $request, $loginUser) {
                $agent = User::where('id', $agent->id)
                    ->lockForUpdate()
                    ->firstOrFail();

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
                    $loginUser = User::where('id', $loginUser->id)->lockForUpdate()->first();

                    if ($loginUser->point < $request->point) {
                            throw new \Exception('Insufficient points');
                        }
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
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function deductPoint(Request $request, User $agent)
    {
        $request->validate([
            'point' => 'required|integer|min:1',
        ]);

        $loginUser = Auth::user();
        abort_unless($loginUser->canManage($agent), 403);

        try {
            DB::transaction(function () use ($agent, $request, $loginUser) {
                $agent = User::where('id', $agent->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $point_before = $agent->point;
                $point_after  = $point_before - $request->point;

                if ($point_after < 0) {
                    throw new \Exception('Insufficient points: Cannot deduct more than the current balance.');
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
                    $loginUser = User::where('id', $loginUser->id)->lockForUpdate()->first();

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
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
