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

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = User::where('role_id',1)->get();

        return view('admin.index')->with('admin',$admin);
    }

    public function create()
    {
        return view('admin.create');
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
        $request->merge(['password' => Hash::make($request->password),'role_id'=>1]);

        $admin = User::create($request->all());

        return redirect()->route('admin.index')->withSuccess('Data saved');
    }

    public function edit(User $admin)
    {
        return view('admin.create')->with('admin',$admin);
    }

    public function update(Request $request, User $admin)
    {
        if($request->password !=null){
            $request->merge(['password' => Hash::make($request->password)]);
        }else{
            $request->request->remove('password');
        }
        // dd($request->all());
        $admin->update($request->all());
        return redirect()->route('admin.index')->withSuccess('Data updated');
    }

    public function destroy(User $admin)
    {
        $admin->delete();

        return redirect()->route('admin.index')->withSuccess('Data deleted');
    }

}
