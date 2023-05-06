<?php

namespace App\Http\Controllers;

use App\Mocks;
use App\Module;
use App\Result;
use App\Program;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TestsController extends Controller
{
    public function index(Request $request)
    {
        
       if(Auth::user()->role_id == "Student"){
            $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            if($user_balance->balance > 0){
                return back()->with('error', 'Please Pay your balance of '. $user_balance->currency_symbol . number_format($user_balance->balance). ' in order to get access to tests');
            }  

            $i = 1;

            $program = Program::find($request->p_id);
            $modules = Module::with('questions')->where('program_id', $program->id)->where('status', 1)->get();
            
            if(Auth::user()->redotest == $program->id){
               $modules = Module::with('questions')->where('program_id', $program->id)->get();
            }
            
            //Check if user has taken pre tests and return back if otherwise
                if($program->hasmock == 1){
 
                    $expected_pre_class_tests = Module::ClassTests($program->id)->count();
                    
                    $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', auth()->user()->id)->count();
                    if($completed_pre_class_tests < $expected_pre_class_tests ){
                        return Redirect::to('mocks?p_id='.$program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Post Class Tests');
                    }                     
                }

            foreach($modules as $module){
                $module_check = Result::where('module_id', $module->id)->where('user_id', auth()->user()->id)->get();
               
                $redo_check = Result::where('module_id', $module->id)->where('user_id', auth()->user()->id)->where('role_play_score', '<>', NULL)->where('email_test_score', '<>', NULL)->get();

                foreach($redo_check as $check){
                    if($check->certification_test_details != NULL){
                        $module['redo'] = 1;
                    }else{ 
                    $module['redo'] = 0;
                    }
                }
                if($module_check->count() > 0){
                    $module['completed'] = 1;
                }else{ 
                $module['completed'] = 0;
                }
            }
           
                         
            return view('dashboard.student.tests.index', compact('modules', 'i', 'program') );
         }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $program = Program::find($request->p_id);
   
        $class_test_details = array_except($request->all(), ['_token', 'mod_id', 'id']);
      
        if(sizeof($class_test_details) < 2){
            return back()->with('error', 'You must answer at least 1 question');
        };

        $certification_test_details = array_except($request->all(), ['_token', 'mod_id', 'id', 'p_id']);
        
        foreach($certification_test_details as $key => $value)
        {
             if((!isset($certification_test_details[$key]))){
                return back()->with('error', 'You must answer at least 1 question');
            };
            
            if(str_word_count($certification_test_details[$key]) > 500 ){
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }
     
        $check = Result::where('user_id', auth()->user()->id)->where('module_id', $request->mod_id)->first();
       
        if(auth()->user()->redotest == 0){
        if($check != NULL){
            return back()->with('error', 'You have already taken this test, Please click "My Tests" on the left navigation bar to take an available test!');
        };
        }

        if($check != NULL && auth()->user()->redotest != 0){
            $check->certification_test_details = json_encode($certification_test_details);
           
            $check->save();
        }
        else{


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
                    'class_test_details' => json_encode($class_test_details),
                    ]);
                }
    
            }catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();        
                return back()->with('error', 'something went wrong, please take test again');
            }
        }
        }
        return Redirect::to('userresults?p_id='. $program->id);
        
    }

    public function userresults(Request $request){
        
        if(Auth::user()->role_id == "Student"){
            
            $i = 1;
            $program = Program::find($request->p_id);
            $hasmock = $program->hasmock;
            $user_balance = DB::table('program_user')->where('program_id',  $program->id)->where('user_id', auth()->user()->id)->first();
                if($user_balance->balance > 0){
                    return back()->with('error', 'Please Pay your balance of '. $user_balance->currency_symbol . number_format($user_balance->balance) . ' in order to access tests');
                }   

                //Check if user has taken pre tests and return back if otherwise
                if($program->hasmock == 1){
                    $expected_pre_class_tests = Module::ClassTests($program->id)->count();
                    $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', auth()->user()->id)->count();
                    
                    if($completed_pre_class_tests < $expected_pre_class_tests ){
                            return Redirect::to('mocks?p_id='.$program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Post Class Tests');
                    }                     
                }

            $results = Result::with('module')->where('user_id', auth()->user()->id)->whereProgramId($program->id)->orderBy('module_id', 'DESC')->get(); 
             
            $cert_score = Result::where('user_id', auth()->user()->id)->whereProgramId($program->id)->sum('certification_test_score');
           
            //If cert score is 0 or less, it is recorded as processing
                        
            foreach($results as $result){
                $result['certification_test_score'] =  $cert_score;
            }
           
            $mock_results = Mocks::with('module')->where('user_id', auth()->user()->id)->whereProgramId($program->id)->orderBy('module_id', 'DESC')->get();    
       
            return view('dashboard.student.tests.result', compact('results','i', 'program', 'mock_results', 'hasmock') );
        }
    }


    public function show($id, Request $request)
    {
        $questions = Question::with('module')->where('module_id', $id)->get();      
        $i = 1;
        
        //check if registered module
        $questionsarray = $questions->toArray();
        if($questionsarray[0]['module']['program_id'] <> $request->p_id ){
            return abort(404);
        };

        $module_type = Module::where('id', $id)->value('type');

        foreach($questions as $question){
            $program_name = $question->module->program->p_name;
            $program = $question->module->program()->first();
            $time= $question->module->time;
            $module_title = $question->module->title;
        }
      
        if($module_type == 'Class Test'){
            return view('dashboard.student.tests.quizz', compact('questions', 'i', 'program','program_name','module_title', 'time'));
        }
        if($module_type == 'Certification Test'){
            return view('dashboard.student.tests.certification', compact('questions', 'i', 'program','program_name','module_title', 'time'));
        }
    }

    public function userResultComments(Request $request, $id){
        $comments = Result::where('id',$id)->first();
        $program = Program::find($request->p_id);
        return view('dashboard.student.tests.result_comments',compact('comments','id','program'));
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
