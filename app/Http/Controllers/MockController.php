<?php

namespace App\Http\Controllers;

use App\User;
use App\Mocks;
use App\Module;
use App\Result;
use App\Program;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MockController extends Controller
{
    
    public function index(Request $request)
    {
       if(Auth::user()->role_id == "Student"){

            $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            if($user_balance->balance > 0){
                return back()->with('error', 'Please Pay your balance of '. config('custom.default_currency').$user_balance->balance. ' in order to get access to take tests');
            }

            $i = 1;

            $program = Program::find($request->p_id);
            $modules = Module::with('questions')->where('program_id', $request->p_id)->get();
            
            foreach($modules as $module){
                $module_check = Mocks::where('module_id', $module->id)->where('user_id', auth()->user()->id)->get();
                
                if($module_check->count() > 0){
                    $module['completed'] = 1;
                }else{ 
                $module['completed'] = 0;
                }
            }
            //  dd($modules);
            return view('dashboard.student.pretests.index', compact('modules', 'i', 'program') );
         }
    }

    public function create()
    {
        //
    }

     public function grade($uid, $modid){
         
        $user_results = Mocks::with(['program', 'user'])->where('id', $modid)->where('user_id', $uid)->first();

        $i = 1;
        $array = json_decode($user_results->certification_test_details, true);
       
        foreach($array as $key => $value)
        {
            $array[Question::where('id',$key)->value('title')] = $value;
            unset($array[$key]);
        }

        return view('dashboard.admin.mocks.edit', compact('user_results', 'array', 'i'));
    }

     public function mockresults()
    {
       if(Auth::user()->role_id == "Admin"){
            $i = 1;
            
            $users = User::with(['mocks', 'program'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Grader')->where('role_id', '<>', 'Facilitator')->get();
    
            foreach($users as $user){        
                $user['total_ct_score'] = 0;
                $user['total_cert_score'] = 0;
                $user['program_score_settings'] = 0;
                $user['total_email_test_score'] = 0;

                $user['class_test_module_count'] = Module::where('program_id', $user->program->id)->where('type', 'Class Test')->count();  
                $user['total_role_play_score'] = 0;
                if(isset($user->mocks)){
                    foreach($user->mocks as $results){
                        $user['marked_by'] = $results->marked_by; 
                        $user['grader'] = $results->grader;
                        $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
                        $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 
                  
                        $user['module'] = $results->module->id;
                  
                        if($results->module->type == 'Class Test'){
                           
                            $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
                            //$user['modules'] = $results->module->count();
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                            //print_r( $results->module->questions->count());
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                           
                            $u =  Module::where('type', 0)->get();
                            $obtainable = array();
                            foreach($u as $t){
                                $questions = array_push($obtainable, $t->questions->count());
                            }
                            $obtainable = array_sum($obtainable);
                            
                            $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) / $obtainable;

                            $user['test_score'] = round($user['test_score'] , 0);
                   
                        }
                        
                        if($results->module->type == 'Certification Test'){
                            $user['result_id'] = $results->id;
                        }
                        
                        $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score'];       
                    } 
                }
        
            }
           
            return view('dashboard.admin.mocks.index', compact('users', 'i') );
        }
            
        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
      
            $i = 1;
            //$results = Result::with(['user', 'program', 'module'])->orderBy('id', 'DESC')->get();
    
            $users = User::with(['results'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Facilitator')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Grader')->where('program_id', auth()->user()->program->id)->get();
           
            foreach($users as $user){
            
                $user['total_ct_score'] = 0;
                $user['total_cert_score'] = 0;
                $user['program_score_settings'] = 0;
                $user['total_email_test_score'] = 0;

                $user['class_test_module_count'] = Module::where('program_id', $user->program->id)->where('type', 'Class Test')->count();  
                $user['total_role_play_score'] = 0;
                if(isset($user->results)){
                    foreach($user->results as $results){
                        $user['marked_by'] = $results->marked_by; 
                        $user['grader'] = $results->grader;
                        $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
                        $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 

                        // $user['result_id'] = $results->id;

                    $user['module'] = $results->module->id;
                        if($results->module->type == 'Class Test'){
                            $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
                            //$user['modules'] = $results->module->count();
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                            //print_r( $results->module->questions->count());
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                            // print_r( $results->modules->questions->count());
                            // print_r($results->program->questions->count());
                            
                            // $user['no_of_questions'] = $results->program->questions->count() + $user['no_of_questions'];
                            // print_r($results->program->questions->count());
                            $u =  Module::where('type', 0)->get();
                            $obtainable = array();
                            foreach($u as $t){
                                $questions = array_push($obtainable, $t->questions->count());
                            }
                            $obtainable = array_sum($obtainable);
                            // dd( $user['total_ct_score'], $obtainable);
                            $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) / $obtainable;

                            $user['test_score'] = round($user['test_score'] , 0);
                   
                        }
                        
                        if($results->module->type == 'Certification Test'){
                            $user['result_id'] = $results->id;
                        }
                        
                        $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score'];       
                    } 
                }
        
            }
            
                return view('dashboard.admin.results.index', compact('users', 'i') );
        } return redirect('/dashboard');
    }
    
    public function store(Request $request)
    {
        $program = Program::find($request->p_id);
   
        $class_test_details = array_except($request->all(), ['_token', 'mod_id', 'id', 'p_id']);

        $certification_test_details = array_except($request->all(), ['_token', 'mod_id', 'id', 'p_id']);
      
        foreach($certification_test_details as $key => $value)
        {
            // print_r(str_word_count($certification_test_details[$key]);
            if(str_word_count($certification_test_details[$key]) > 500 ){
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }
        
        $check = Mocks::where('user_id', auth()->user()->id)->where('module_id', $request->mod_id)->count();
        
        if($check > 0){
            return back()->with('error', 'You have already taken this test, Please click "Pre Class Tests" on the left navigation bar to take an available test!');
        };
        
        $module = Module::findOrFail($request->mod_id);
       
        $questions = $module->questions->toarray();
        $no_of_questions = count($questions);
        $score = 0;
       
        if($module->type == 'Certification Test'){
            try{
                $results = Mocks::create([
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
                     $results = Mocks::create([
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
        
       return Redirect::to('mocks?p_id='. $program->id)->with('message', 'Pre class Test results have been recorded, you will be able to view results after post class tests');
        
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
            return view('dashboard.student.pretests.quizz', compact('questions', 'i', 'program','program_name','module_title', 'time'));
        }
        if($module_type == 'Certification Test'){
            return view('dashboard.student.pretests.certification', compact('questions', 'i', 'program','program_name','module_title', 'time'));
        }
    }

    
    public function edit($id)
    {
        //
    }

    
     public function update(Request $request, $id)
    {

        $result = Mocks::findOrFail($id);
        
        try{
            if(Auth::user()->role_id == 'Facilitator'){
                $marked_by = Auth::user()->name;
                $roleplayscore = $request->roleplayscore;
                $grader = $result->grader;
                $email_test_score = $result->email_test_score;
                $certification_score = $result->certification_test_score;
            }

            if(Auth::user()->role_id == 'Admin'){
                $marked_by = $result->marked_by;
                $roleplayscore = $request->roleplayscore;
                $grader = 'Admin';
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }

            if(Auth::user()->role_id == 'Grader'){
                $marked_by = $result->marked_by;
                $roleplayscore = $result->role_play_score;
                $grader = Auth::user()->name;
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }
            
            $result->marked_by = $marked_by;
            $result->grader = $grader;
            $result->certification_test_score = $certification_score;
            $result->role_play_score = $roleplayscore;
            $result->email_test_score = $email_test_score;
            
            // dd($result->id);
            $result->save();

        }catch(PDOException $ex){
            return back()->with('error', $ex->getMessage());
        }
       
        return redirect('pretestresults')->with('message', 'User Scores have been updated successfully');
    }

    public function destroy(Request $request)
    {
       if(Auth::user()->role_id == "Admin"){
            $users_results = Mocks::where('user_id', $request->id)->get();

            //delete all user results
            foreach($users_results as $results){
                $results->delete();
            };
            

            return redirect('mockresults')->with('message', 'All Pre Test Results for this user have been deleted successfully');
        } return back();
    }
}
