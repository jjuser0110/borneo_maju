<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Cost;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CostController extends Controller
{
    public function index(Request $request)
    {
        $cost = Cost::all();

        return view('cost.index')->with('cost',$cost);
    }

    public function create()
    {
        return view('cost.create');
    }

    public function store(Request $request)
    {
        $cost = Cost::create($request->all());

        return redirect()->route('cost.index')->withSuccess('Data saved');
    }

    public function edit(Cost $cost)
    {
        return view('cost.create')->with('cost',$cost);
    }

    public function update(Request $request, Cost $cost)
    {
        $cost->update($request->all());
        return redirect()->route('cost.index')->withSuccess('Data updated');
    }

    public function destroy(Cost $cost)
    {
        $cost->delete();

        return redirect()->route('cost.index')->withSuccess('Data deleted');
    }

}
