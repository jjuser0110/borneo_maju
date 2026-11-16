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
        $request->validate(['password' => 'required|string|min:8', 'name' => 'required|string|max:255']);

        $admin = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'is_active' => $request->is_active == '0' ? 0 : 1,
            'role_id'   => 1,
        ]);

        return redirect()->route('admin.index')->withSuccess('Data saved');
    }

    public function edit(User $admin)
    {
        abort_unless((int) $admin->role_id === 1, 404);

        return view('admin.create')->with('admin',$admin);
    }

    public function update(Request $request, User $admin)
    {
        abort_unless((int) $admin->role_id === 1, 404);

        $request->validate([
            'username' => 'required|unique:users,username,' . $admin->id . ',id,deleted_at,NULL',
            'password' => 'nullable|string|min:8',
            'name'     => 'sometimes|required|string|max:255',
            'is_active' => 'nullable|in:0,1',
        ]);

        // Only these fields may be changed from the form — never role_id, point, upline, etc.
        $data = $request->only(['name', 'username', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);
        return redirect()->route('admin.index')->withSuccess('Data updated');
    }

    public function destroy(User $admin)
    {
        abort_unless((int) $admin->role_id === 1, 404);
        if ((int) $admin->id === (int) Auth::id()) {
            return redirect()->route('admin.index')->withErrors('You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.index')->withSuccess('Data deleted');
    }

}
