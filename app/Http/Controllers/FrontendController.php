<?php

namespace App\Http\Controllers;

use App\Program;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
         $search = $request->query('search');

        if($search){
            $trainings = Program::where('p_name', 'LIKE', "%{$search}%")->where('id', '<>', 1)->ORDERBY('created_at', 'DESC')->get();
            // dd($trainings);
        }else{
            $trainings = Program::where('p_name', 'LIKE', "%{$search}%")->where('id', '<>', 1)->ORDERBY('created_at', 'DESC')->get();
        }

        return view('welcome', compact('trainings'));
    }

    public function show($id)
    {
        $training = Program::findOrFail($id);

        return view('single_training', compact('training'));
    }
}
