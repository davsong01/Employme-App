<?php

namespace App\Http\Controllers;

use App\Module;
use App\Program;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestsController extends Controller
{
    
    public function index()
    {
       if(Auth::user()->role_id == "Student"){
           $i = 1;
            $modules = Module::with('questions')->where('program_id', Auth::user()->program->id)->where('status', 1)->get();

            return view('dashboard.student.tests.index', compact('modules', 'i') );
         }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $questions = Question::with('module')->where('module_id', $id)->get();      
        $i = 1;

        foreach($questions as $question){
            $program = $question->module->program->p_name;
            $time= $question->module->time;
            $module_title= $question->module->title;
        }

        return view('dashboard.student.tests.quizz', compact('questions', 'i', 'program','module_title', 'time'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

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
