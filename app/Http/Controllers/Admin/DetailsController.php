<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Program;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role_id == "Admin"){
        $programs = Program::where('id', '<>', 1)->get();
        return view('dashboard.admin.details.index', compact('programs'));
    }return back();
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){
        $type = $request->type;
        $training = $request->program_id;
        $programs = Program::where('id', '<>', 1)->get();
        $count = 0;
        $results = DB::table('users')->where('program_id', '=', $training)->where('role_id', '<>', "Admin")->get();
        $count = count($results);
        return view('dashboard.admin.details.show', compact('programs', 'results', 'count', 'type'));
    }return back();
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     *  the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
