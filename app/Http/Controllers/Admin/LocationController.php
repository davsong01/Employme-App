<?php

namespace App\Http\Controllers\Admin;

use App\Program;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{

    public function index()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $i = 1;

            $locations = Location::orderBy('created_at', 'desc')->get();

            return view('dashboard.admin.locations.index', compact('i', 'locations'));
        }
    }

    public function create()
    {
        $programs = Program::with('locations')->where('id', '<>', 1)->get();

        return view('dashboard.admin.locations.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required | min: 2',
            'program_id' => 'required | integer'
        ]);

        $location = Location::firstOrCreate([
            'title' => $request->title,
            'program_id' => $request->program_id,
        ]);


        return redirect(route('locations.index'))->with('message', 'Location has been added successfully');
    }


    public function show(Location $location)
    {
    }

    public function edit(Location $location)
    {
        $programs = Program::with('locations')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
        return view('dashboard.admin.locations.edit', compact('location', 'programs'));
    }

    public function update(Request $request, Location $location)
    {
        $request = $request->except(['_token', '_method']);

        $location->updateorcreate($request);

        return back()->with('message', 'Update Successful');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect(route('locations.index'))->with('message', 'Delete successful!');
    }
}
