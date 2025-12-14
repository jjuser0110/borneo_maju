<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::where('role_id',2)->get();

        return view('staff.index')->with('staff',$staff);
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator);
        }
        $request->merge(['password' => Hash::make($request->password),'role_id'=>2]);

        $staff = User::create($request->all());

        return redirect()->route('staff.index')->withSuccess('Data saved');
    }

    public function edit(User $staff)
    {
        return view('staff.create')->with('staff',$staff);
    }

    public function update(Request $request, User $staff)
    {
        if($request->password !=null){
            $request->merge(['password' => Hash::make($request->password)]);
        }else{
            $request->request->remove('password');
        }
        // dd($request->all());
        $staff->update($request->all());
        return redirect()->route('staff.index')->withSuccess('Data updated');
    }

    public function destroy(User $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->withSuccess('Data deleted');
    }

}
