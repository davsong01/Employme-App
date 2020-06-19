<?php

namespace App\Http\Controllers;

use App\Module;
use App\Result;
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

            foreach($modules as $module){
                
                $module_check = Result::where('module_id', $module->id)->where('user_id', auth()->user()->id)->get();
               
                if($module_check->count() > 0){
                    $module['completed'] = 1;
                }else{ 
                $module['completed'] = 0;
                }
            }
            //  dd($modules);
            return view('dashboard.student.tests.index', compact('modules', 'i') );
         }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $class_test_details = array_except($request->all(), ['_token', 'mod_id', 'id']);

        $certification_test_details = array_except($request->all(), ['_token', 'mod_id', 'id']);
      
        foreach($certification_test_details as $key => $value)
        {
            // print_r(str_word_count($certification_test_details[$key]);
            if(str_word_count($certification_test_details[$key]) > 500 ){
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }
        
        $check = Result::where('user_id', auth()->user()->id)->where('module_id', $request->mod_id)->count();
        
        if($check > 0){
            return back()->with('error', 'You have already taken this test, Please click "My Tests" on the left navigation bar to take an available test!');
        };
        
        $module = Module::findOrFail($request->mod_id);
       
        $questions = $module->questions->toarray();
        $no_of_questions = count($questions);
        $score = 0;
        

        if($module->type == 'Certification Test'){
            try{
                $results = Result::create([
                   'program_id' => $module->program->id,
                   'user_id' => Auth::user()->id,
                   'module_id' => $module->id,
                   'certification_test_details' => json_encode($certification_test_details),
                    ]);
               
                }catch (\Illuminate\Database\QueryException $ex) {
                    $error = $ex->getMessage();        
                    return back()->with('error',  'Something went wrong, please try again');
                }
        }

        elseif($module->type == 'Class Test'){
            foreach($questions as $question){
                $question_id = $question['id'];          
                if($request[$question_id] == $question['correct']){
                    $score = $score + 1;
                }else{
                    $score;
                }
            }

            try{
                if($module->type == 'Class Test'){
                     $results = Result::create([
                    'program_id' => $module->program->id,
                    'user_id' => Auth::user()->id,
                    'module_id' => $module->id,
                    'class_test_score' => $score,
                    'class_test_details' =>  json_encode($class_test_details),
                    ]);
                }
    
            }catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();        
                return back()->with('error', 'something went wrong, please take test again');
            }
        }
       
        return redirect(route('tests.results'));
        
    }

    public function userresults(){
        if(Auth::user()->role_id == "Student"){
            $i = 1;
            
            $results = Result::with('module')->where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get();
            // dd($results);
            return view('dashboard.student.tests.result', compact('results', 'i') );
          }
    }


    public function show($id)
    {
        $questions = Question::with('module')->where('module_id', $id)->get();      
        $i = 1;

        $module_type = Module::where('id', $id)->value('type');

        foreach($questions as $question){
            $program = $question->module->program->p_name;
            $time= $question->module->time;
            $module_title = $question->module->title;
        }

        if($module_type == 'Class Test'){
            return view('dashboard.student.tests.quizz', compact('questions', 'i', 'program','module_title', 'time'));
        }
        if($module_type == 'Certification Test'){
            return view('dashboard.student.tests.certification', compact('questions', 'i', 'program','module_title', 'time'));
        }
    }

    
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
