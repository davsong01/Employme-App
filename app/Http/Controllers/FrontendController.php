<?php

namespace App\Http\Controllers;

use App\Program;
use App\Location;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        if($search){
            $trainings = Program::where('p_name', 'LIKE', "%{$search}%")->where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();

        }else{
            $trainings = Program::where('p_name', 'LIKE', "%{$search}%")->where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
        }

        return view('welcome', compact('trainings'));
    }

    public function show($id)
    {
        $training = Program::findOrFail($id);
        $locations = Location::select('title')->distinct()->whereProgramId($training->id)->get();

        return view('single_training', compact('training', 'locations'));
    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/trainings'. '/' .$filename;
        return response()->download($realpath);
    }  
}
