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
        $request->validate(['password' => 'required|string|min:8', 'name' => 'required|string|max:255']);

        $staff = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'is_active' => $request->is_active == '0' ? 0 : 1,
            'role_id'   => 2,
        ]);

        return redirect()->route('staff.index')->withSuccess('Data saved');
    }

    public function edit(User $staff)
    {
        abort_unless((int) $staff->role_id === 2, 404);

        return view('staff.create')->with('staff',$staff);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless((int) $staff->role_id === 2, 404);

        $request->validate([
            'username' => 'required|unique:users,username,' . $staff->id . ',id,deleted_at,NULL',
            'password' => 'nullable|string|min:8',
            'name'     => 'sometimes|required|string|max:255',
            'is_active' => 'nullable|in:0,1',
        ]);

        // Only these fields may be changed from the form — never role_id, point, upline, etc.
        $data = $request->only(['name', 'username', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);
        return redirect()->route('staff.index')->withSuccess('Data updated');
    }

    public function destroy(User $staff)
    {
        abort_unless((int) $staff->role_id === 2, 404);
        if ((int) $staff->id === (int) Auth::id()) {
            return redirect()->route('staff.index')->withErrors('You cannot delete your own account.');
        }

        $staff->delete();

        return redirect()->route('staff.index')->withSuccess('Data deleted');
    }

}
