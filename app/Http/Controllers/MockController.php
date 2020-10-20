<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Mocks;
use App\Module;
use App\Result;
use App\Program;
use App\Question;
use App\ScoreSetting;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Redirect;

class MockController extends Controller
{
    
     public function pretest(){
        if(auth()->user()->role_id == 'Admin'){
            //Select only programs that have results
            $programs = Program::whereHas('mocks', function ($query) {
                    return $query->orderby('created_at', 'DESC');
            })->get();

            $i = 1;
            return view('dashboard.admin.mocks.selecttraining', compact('programs', 'i'));
        }

        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            //select all programs for this user
            $teacher_programs = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();
            
            //Select only programs that have results
            foreach($teacher_programs as $programs){
                 $programs['p_name'] = Program::whereId($programs->program_id)->wherehasmock(1)->value('p_name');
                //  = Program::whereId($programs->program_id)->value('p_name');
            } 

            $i = 1;
            return view('dashboard.teacher.mocks.selecttraining', compact('teacher_programs', 'i'));
        }

       
    }

    public function index(Request $request)
    {
       if(Auth::user()->role_id == "Student"){

            // $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            // if($user_balance->balance > 0){
            //     return back()->with('error', 'Please Pay your balance of '. config('custom.default_currency').$user_balance->balance. ' in order to get access to take tests');
            // }

            $i = 1;

            $program = Program::find($request->p_id);
            $modules = Module::with('questions')->where('program_id', $request->p_id)->whereType(0)->get();
            
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

    public function getgrades(Request $request, $id)
    {
        $request->pid = $id;
        if(Auth::user()->role_id == "Admin"){
            $i = 1;

            $users = DB::table('program_user')->select('user_id')->distinct()->whereProgramId($request->pid)->get();
           
            foreach($users as $user){
                $results = Mocks::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();
                
                    $user->total_cert_score = 0;
                    $user->total_class_test_score = 0;
                    $user->total_email_test_score = 0;
                    $user->total_role_play_score = 0;

                    $user->program_id = $request->pid;
                    $user->program_ct_score_settings = 0;
                    $user->passmark = 0; 
                    $user->created_at = NULL;
                    $user->class_test_module_count = Module::where('program_id', $request->pid)->where('type', 'Class Test')->count(); 
                    $user->marked_by = '';
                    $user->grader = '';

                    $score_settings = ScoreSetting::select(['class_test', 'passmark'])->whereProgramId($request->pid)->first();
                    $user->program_ct_score_settings = $score_settings->class_test;
                    $user->passmark = $score_settings->passmark;
                    $user->result_id = 0;
                    $user->name =  User::where('id', $user->user_id)->value('name');
                    $user->final_ct_score = 0;
                    $user->total_class_test_score = 0;
                    $user->obtainable = 0;

                    foreach($results as $result){                      
                        $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score; 
                        $user->created_at = $result->created_at; 
                        $user->marked_by = $result->marked_by;
                        $user->grader = $result->grader;
                        $user->result_id = NULL;
                        $user->total_email_test_score = $result->email_test_score + $user->total_email_test_score;
                        
                        
                        if($result->module->type == 'Class Test'){
                            
                            $u =  Module::where('type', 0)->where('program_id', $request->pid)->get();

                            $obtainable = array();
                            
                            foreach($u as $t){
                                $questions = array_push($obtainable, $t->questions->count());
                            }

                            $user->obtainable = array_sum($obtainable);
                          
                            if($u->count() > 0){
                               
                                $user->total_class_test_score = $result->class_test_score + $user->total_class_test_score;
                            
                            }
                                     
                        }     
                        
                        if($result->module->type == 'Certification Test'){
                            
                            $user->total_cert_score = $result->certification_test_score +  $user->total_cert_score;

                            $user->result_id = $result->id; 
                            $user->certification_test_details = 1;
                           
                        } 
                        $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);

                        
                $program_name = Program::whereId($request->pid)->value('p_name');
            
                    }
               
            }

            return view('dashboard.admin.mocks.index', compact('users', 'i', 'program_name') );
        }
            
        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            $i = 1;

            $users = DB::table('program_user')->select('user_id')->distinct()->whereProgramId($request->pid)->get();
           
            foreach($users as $user){
                $results = Mocks::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();
                
                    $user->total_cert_score = 0;
                    $user->total_class_test_score = 0;
                    $user->total_email_test_score = 0;
                    $user->total_role_play_score = 0;

                    $user->program_id = $request->pid;
                    $user->program_ct_score_settings = 0;
                    $user->passmark = 0; 
                    $user->created_at = NULL;
                    $user->class_test_module_count = Module::where('program_id', $request->pid)->where('type', 'Class Test')->count(); 
                    $user->marked_by = '';
                    $user->grader = '';

                    $score_settings = ScoreSetting::select(['class_test', 'passmark'])->whereProgramId($request->pid)->first();
                    $user->program_ct_score_settings = $score_settings->class_test;
                    $user->passmark = $score_settings->passmark;
                    $user->result_id = 0;
                    $user->name =  User::where('id', $user->user_id)->value('name');
                    $user->final_ct_score = 0;
                    $user->total_class_test_score = 0;
                    $user->obtainable = 0;

                    foreach($results as $result){                      
                        $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score; 
                        $user->created_at = $result->created_at; 
                        $user->marked_by = $result->marked_by;
                        $user->grader = $result->grader;
                        $user->result_id = NULL;
                        $user->total_email_test_score = $result->email_test_score + $user->total_email_test_score;
                        
                        
                        if($result->module->type == 'Class Test'){
                            
                            $u =  Module::where('type', 0)->where('program_id', $request->pid)->get();

                            $obtainable = array();
                            
                            foreach($u as $t){
                                $questions = array_push($obtainable, $t->questions->count());
                            }

                            $user->obtainable = array_sum($obtainable);
                          
                            if($u->count() > 0){
                               
                                $user->total_class_test_score = $result->class_test_score + $user->total_class_test_score;
                            
                            }
                                     
                        }     
                        
                        if($result->module->type == 'Certification Test'){
                            
                            $user->total_cert_score = $result->certification_test_score +  $user->total_cert_score;

                            $user->result_id = $result->id; 
                            $user->certification_test_details = 1;
                           
                        } 
                        $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);

                        
                    $program_name = Program::whereId($request->pid)->value('p_name');
            
                }
               
            }

            return view('dashboard.admin.mocks.index', compact('users', 'i', 'program_name') );
        }
    }
    
    public function add($uid, $modid){
        $user_results = Result::with(['program', 'user'])->where('id', $modid)->where('user_id', $uid)->first();

        $i = 1;
        $array = json_decode($user_results->certification_test_details, true);
       
        foreach($array as $key => $value)
        {
            $array[Question::where('id',$key)->value('title')] = $value;
            unset($array[$key]);
        }

        return view('dashboard.admin.mocks.edit', compact('user_results', 'array', 'i'));
    }
    //  public function mockresults(Request $request)
    // {
    
    //    if(Auth::user()->role_id == "Admin"){
    //         $i = 1;
            
    //         // $users = User::with(['mocks', 'programs'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Grader')->where('role_id', '<>', 'Facilitator')->get();

    //         //Check Mock Table for this user with this program Id
    //         $users = DB::table('program_user')->select('id')->whereUserId(3)->whereProgramId(14)->get();

    //         foreach($users as $user){   
    //             $user['pid'] = $request->pid; 
    //             $user['total_ct_score'] = 0;
    //             $user['total_cert_score'] = 0;
    //             $user['program_score_settings'] = 0;
    //             $user['total_email_test_score'] = 0;

    //             $user['class_test_module_count'] = Module::where('program_id', $user['pid'])->where('type', 'Class Test')->count();  
    //             $user['total_role_play_score'] = 0;
    //             if(isset($user->mocks)){
    //                 foreach($user->mocks as $results){
                        
    //                     $user['marked_by'] = $results->marked_by; 
    //                     $user['grader'] = $results->grader;
    //                     $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
    //                     $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 
                  
    //                     $user['module'] = $results->module->id;
                  
    //                     if($results->module->type == 'Class Test'){
                           
    //                         $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
    //                         //$user['modules'] = $results->module->count();
    //                         $user['program_score_settings'] = $results->program->scoresettings->class_test;
    //                         //print_r( $results->module->questions->count());
    //                         $user['program_score_settings'] = $results->program->scoresettings->class_test;
                           
    //                         $u =  Module::where('type', 0)->get();
    //                         $obtainable = array();
    //                         foreach($u as $t){
    //                             $questions = array_push($obtainable, $t->questions->count());
    //                         }
    //                         $obtainable = array_sum($obtainable);
                            
    //                         $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) / $obtainable;

    //                         $user['test_score'] = round($user['test_score'] , 0);
                   
    //                     }
                        
    //                     if($results->module->type == 'Certification Test'){
    //                         $user['result_id'] = $results->id;
    //                     }
                        
    //                     $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score'];       
    //                 } 
    //             }
        
    //         }
    //        dd($users);
    //         return view('dashboard.admin.mocks.index', compact('users', 'i') );
    //     }
            
    //     if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
      
    //         $i = 1;
    //         //$results = Result::with(['user', 'program', 'module'])->orderBy('id', 'DESC')->get();
    
    //         $users = User::with(['results'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Facilitator')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Grader')->where('program_id', auth()->user()->program->id)->get();
           
    //         foreach($users as $user){
            
    //             $user['total_ct_score'] = 0;
    //             $user['total_cert_score'] = 0;
    //             $user['program_score_settings'] = 0;
    //             $user['total_email_test_score'] = 0;

    //             $user['class_test_module_count'] = Module::where('program_id', $user->program->id)->where('type', 'Class Test')->count();  
    //             $user['total_role_play_score'] = 0;
    //             if(isset($user->results)){
    //                 foreach($user->results as $results){
    //                     $user['marked_by'] = $results->marked_by; 
    //                     $user['grader'] = $results->grader;
    //                     $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
    //                     $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 

    //                     // $user['result_id'] = $results->id;

    //                 $user['module'] = $results->module->id;
    //                     if($results->module->type == 'Class Test'){
    //                         $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
    //                         //$user['modules'] = $results->module->count();
    //                         $user['program_score_settings'] = $results->program->scoresettings->class_test;
    //                         //print_r( $results->module->questions->count());
    //                         $user['program_score_settings'] = $results->program->scoresettings->class_test;
    //                         // print_r( $results->modules->questions->count());
    //                         // print_r($results->program->questions->count());
                            
    //                         // $user['no_of_questions'] = $results->program->questions->count() + $user['no_of_questions'];
    //                         // print_r($results->program->questions->count());
    //                         $u =  Module::where('type', 0)->get();
    //                         $obtainable = array();
    //                         foreach($u as $t){
    //                             $questions = array_push($obtainable, $t->questions->count());
    //                         }
    //                         $obtainable = array_sum($obtainable);
    //                         // dd( $user['total_ct_score'], $obtainable);
    //                         $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) / $obtainable;

    //                         $user['test_score'] = round($user['test_score'] , 0);
                   
    //                     }
                        
    //                     if($results->module->type == 'Certification Test'){
    //                         $user['result_id'] = $results->id;
    //                     }
                        
    //                     $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score'];       
    //                 } 
    //             }
        
    //         }
            
    //             return view('dashboard.admin.results.index', compact('users', 'i') );
    //     } return redirect('/dashboard');
    // }
    
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

    
     public function update(Mocks $mock, Request $request)
    {
        
        try{
            if(Auth::user()->role_id == 'Facilitator'){
                $marked_by = Auth::user()->name;
                $roleplayscore = $request->roleplayscore;
                $grader = $mock->grader;
                $email_test_score = $mock->email_test_score;
                $certification_score = $mock->certification_test_score;
            }

            if(Auth::user()->role_id == 'Admin'){
                $marked_by = $mock->marked_by;
                $roleplayscore = $request->roleplayscore;
                $grader = 'Admin';
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }

            if(Auth::user()->role_id == 'Grader'){
                $marked_by = $mock->marked_by;
                $roleplayscore = $mock->role_play_score;
                $grader = Auth::user()->name;
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }
            
            $mock->marked_by = $marked_by;
            $mock->grader = $grader;
            $mock->certification_test_score = $certification_score;
            $mock->role_play_score = $roleplayscore;
            $mock->email_test_score = $email_test_score;
            
            // dd($mock->id);
            $mock->save();

        }catch(PDOException $ex){
            return back()->with('error', $ex->getMessage());
        }

        return Redirect::to(route('mocks.getgrades', $mock->program_id))->with('message', 'User Scores have been updated successfully');
    
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
