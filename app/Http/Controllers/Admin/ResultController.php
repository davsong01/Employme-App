<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\User;
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

class ResultController extends Controller
{
    public function index(){

    }

    public function posttest(){
        $i = 1;

        if(Auth::user()->role_id == "Admin"){
            //Select only programs that have results
            $programs = Program::whereHas('results', function ($query) {
                    return $query;
            })->orderby('created_at', 'DESC')->get();

            foreach($programs as $program){
                $program['result_count'] = Result::whereProgramId($program->id)->count();
            }

            return view('dashboard.admin.results.selecttraining', compact('programs', 'i'));
  
        }
        
        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
  
            $programs = FacilitatorTraining::whereUserId(auth()->user()->id)->get();
            
            if($programs->count() > 0){
                foreach($programs as $program){
                    $program['p_name'] = Program::whereId($program->program_id)->value('p_name');
                    
                    $program['result_count'] = Result::whereProgramId($program->program_id)->count();
                }
            }
        }

        return view('dashboard.teacher.results.selecttraining', compact('programs', 'i'));
    }

    public function getgrades(Request $request, $id)
    {
        
        $request->pid = $id;
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
            
            $users = DB::table('program_user')->select('user_id')->distinct()->whereProgramId($request->pid)->get();            

            // dd($status->getRedoStatus());
            foreach($users as $user){
                $results = Result::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();
                
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
                    // $user->name =  User::where('id', $user->user_id)->value('name');

                    $userdetails = User::find($user->user_id);
                    $user->name = $userdetails->name;
                    $user->redotest = $userdetails->redotest;

                    $user->final_ct_score = 0;
                    $user->total_class_test_score = 0;
                    $user->obtainable = 0;

                    foreach($results as $result){                      
                        $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score; 
                        $user->updated_at = $result->updated_at; 
                       
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
                            $user->marked_by = $result->marked_by;
                            $user->grader = $result->grader;
                            $user->result_id = $result->id;
                          
                        }
                          
                    }
                    if($user->obtainable > 0){
                        $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);
                    }     
            }
            $program_name = Program::whereId($request->pid)->value('p_name');
            
            return view('dashboard.admin.results.index', compact('users', 'i', 'program_name') );
        }
            
        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            
            $users = DB::table('program_user')->select('user_id')->distinct()->whereProgramId($request->pid)->get();
            
            foreach($users as $user){
              
                $results = Result::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();
                
                    $user->total_cert_score = 0;
                    $user->total_class_test_score = 0;
                    $user->total_email_test_score = 0;
                    $user->total_role_play_score = 0;
                    $user->total_cert_score = 0;
                    $user->program_id = $request->pid;
                    $user->program_ct_score_settings = 0;
                    $user->passmark = 0; 
                    $user->updated_at = NULL;
                    $user->class_test_module_count = Module::where('program_id', $request->pid)->where('type', 'Class Test')->count(); 
                    $user->marked_by = '';
                    $user->grader = '';

                    $score_settings = ScoreSetting::select(['class_test', 'passmark'])->whereProgramId($request->pid)->first();
                    $user->program_ct_score_settings = $score_settings->class_test;
                    $user->passmark = $score_settings->passmark;
                    $user->result_id = 0;

                    // $user->name =  User::where('id', $user->user_id)->value('name');
                    $userdetails = User::find($user->user_id);
                    $user->name = $userdetails->name;
                    $user->redotest = $userdetails->redotest;

                    $user->final_ct_score = 0;
                    $user->total_class_test_score = 0;
                    $user->obtainable = 0;

                    foreach($results as $result){                      
                        $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score; 
                        $user->updated_at = $result->updated_at; 
                        
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
                            $user->marked_by = $result->marked_by;
                            $user->grader = $result->grader;
                            $user->result_id = $result->id;
                          
                        }
                          
                    }
                    if($user->obtainable > 0){
                        $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);
                    }     
            }

            $program_name = Program::whereId($request->pid)->value('p_name');
          
            return view('dashboard.admin.results.index', compact('users', 'i', 'program_name') );
        }
        
        return redirect('/dashboard');
    }

    
    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->get();
            $users = User::where('role_id', '<>', "Admin")->where('role_id', '<>', "Teacher")->where('role_id', '<>', "Grader")->where('hasResult', '<>', 1)->orderBy('created_at', 'DESC')->get();
                return view('dashboard.admin.results.create', compact('users', 'programs'));
                }
            elseif(Auth::user()->role_id == "Teacher"){
                $programs = Program::where('id', '=', Auth::user()->program_id)->get();
                $users = User::where('role_id', '=', "Student")->where('hasResult', '<>', 1)->where('program_id', '=', Auth::user()->program_id)->orderBy('created_at', 'DESC')->get();
                    //return view('dashboard.admin.results.create', compact('users', 'programs'));
                    }else
                        return redirect('/dashboard');
    }

    public function store(Request $request)
    {

    }

    public function add(Request $request, $uid, $modid){
        $result_id = $modid;
        $program = Program::select('id', 'p_name')->with('scoresettings')->whereId($request->pid)->first();

        $user_results = Result::with(['user', 'module'])->where('user_id', $uid)->whereProgramId($program->id)->where('certification_test_details', '<>', NULL)->get();

        $i = 1;
        $details['certification_score'] = 0;
        $details['email_test_score'] = 0;
        $details['role_play_score'] = 0;
        $details['user_name'] = "";
        $details['allow_editing'] = 0;

        foreach($user_results as $results){
            $details['certification_score'] = $results->certification_test_score + $details['certification_score'];
            $details['email_test_score'] = $results->email_test_score +  $details['email_test_score'];
            $details['role_play_score'] = $results->role_play_score +  $details['role_play_score'];
            $results['module_title'] = $results->module->title;
            $details['user_name'] = $results->user->name;
            $details['allow_editing'] = 1;

            $questions = json_decode($results->certification_test_details, true);
                if(!$questions){
                    $results['title'] = 'User is re-writing test';
                    $results['answer'] = 'User is re-writing test';
                   
                }
                foreach($questions as $key=>$value){
                    $results['title'] = Question::whereId($key)->value('title');
                    $results['answer'] = $value;
                   ;
                   
                }

                unset($results['certification_test_details']);
                unset($results['certification_test_score']);
                unset($results['role_play_score']);
                unset($results['email_test_score']);
        }

        return view('dashboard.admin.results.edit', compact('user_results', 'i', 'result_id', 'program', 'details'));
    }
    
    public function enable($id){

        if(Auth::user()->role_id == "Admin"){

            $program = Program::findorfail($id);
    
            $program->hasresult = 1;
    
            $program->save();
        
            return back()->with('message', 'Participants of this program can now print their statement of result');
        }return back();
    }

    public function disable($id){
        if(Auth::user()->role_id == "Admin"){

            $program = Program::findorfail($id);
    
            $program->hasresult = 0;
    
            $program->save();

            return back()->with('message', 'Participants of this program can no longer print their statement of result');
        }return back();
    }

    public function show($id, Request $request)
    {    
        if(Auth::user()->role_id == "Student" || Auth::user()->id == $id){ 
            
            $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            
            if($user_balance->balance > 0){
                return back()->with('error', 'Please Pay your balance of '. config('custom.default_currency').$user_balance->balance. ' in order to get access to view results');
            }

            $result = Result::with('program', 'module', 'user')->where('user_id', Auth::user()->id)->whereProgramId($request->p_id)->get();    
            
            $program = Program::find($request->p_id);
            if($program->hasresult == 0){
                return back()->with('error', 'Results for this program have not been enabled, Please check back!');
            }
            //Check if balance
            $balance = DB::table('program_user')->whereUserId(Auth::user()->id)->whereProgramId($program->id)->value('balance');

            if($result->count() > 0){
                if($balance > 0){
                    return back()->with('error', 'Dear '.Auth::user()->name.', Please pay your balance of '. $balance.' in order to view/print your result');
                }
                
                $details = array();
                
                $class = 0;
                $email = 0;
                $roleplay = 0; 
                $certification = 0;
                $modules = Module::with('questions')->where('type', 'Class Test')->where('program_id', $program->id)->get();
                
                $obtainable = array();
                
                foreach($modules as $module){
                    $questions = array_push($obtainable, $module->questions->count());
                }

                $obtainable = array_sum($obtainable);

               foreach($result as $t){    
                $class = $t['class_test_score'] + $class;
                $email =  $t['email_test_score'] + $email;
                $roleplay =  $t['role_play_score'] + $roleplay;
                $certification =  $t['certification_test_score'] + $certification;
               
                $t['program'] = $t->program->p_name;
                $t['passmark'] = $program->scoresettings->passmark;
                $t['ct_set_score'] = $program->scoresettings->class_test;
                $t['name'] = $t->user->name;
                
                }
             
                $details['class_test_score'] = ($class  *  $t['ct_set_score']) /  $obtainable;
                $details['class_test_score'] = round($details['class_test_score'] , 0);

                $details['email_test_score'] = $email;
                $details['role_play_score'] = $roleplay;
                $details['certification_test_score'] = $certification;
            
                $details['total_score'] = $details['class_test_score'] + $email + $roleplay + $certification;
                
                $details['passmark'] = $t['passmark'];

                $details['program'] = $t['program'];
                $details['name'] = $t['name'];

                // if($details['class_test_score'] <= 0 || $details['certification_test_score'] <= 0 ){
                // if($details['class_test_score'] <= 0 || $details['certification_test_score'] <= 0 ){
                //     return back()->with('error', 'Your result is being processed, please check back later or notify your facilitator');
                // }
                
                if($details['total_score'] >= $details['passmark']){
                    $details['status'] = 'CERTIFIED';
                }else $details['status'] = 'NOT CERTIFIED';         
                
                return view('dashboard.admin.results.show', compact('details', 'program'));   
            } 

            return redirect('/dashboard')->with('error', 'Result not found! Looks like you did not take the tests, please contact program coordinator');
        }
        elseif(Auth::user()->role_id == "Teacher"){   
            $result = Result::where('user_id', $id)->first();
            $resultcount = (count($result));
            return view('dashboard.admin.results.show', compact('result'));
        }
        return redirect('/');
    }
    
    public function update(Result $result, Request $request)
    { 
        
        try{
            if(Auth::user()->role_id == 'Facilitator'){
                $result->marked_by = Auth::user()->name;
                $result->role_play_score = $request->roleplayscore;                
            }

            if(Auth::user()->role_id == 'Grader'){
                $result->certification_test_score = $request->certification_score;
                $result->grader = Auth::user()->name;
                $result->email_test_score = $request->emailscore;
                
            }
            
            if(Auth::user()->role_id == 'Admin'){
                $result->role_play_score = $request->roleplayscore;
                $result->email_test_score = $request->emailscore;
                $result->certification_test_score = $request->certification_score;
            }

            $result->save();

        }catch(PDOException $ex){
            return back()->with('error', $ex->getMessage());
        }
      return Redirect::to(route('results.getgrades', $result->program_id))->with('message', 'User Scores have been updated successfully');
    }

   
    public function destroy(Request $request, $result)
    {
         
        if(Auth::user()->role_id == "Admin"){
            $users_results = Result::whereProgramId($request->pid)->where('user_id', $request->uid)->get();
            // dd($users_results);
            foreach($users_results as $results){
                $results->certification_test_details = NULL;
                $results->certification_test_score = NULL;
                $results->grader = NULL;
                // $results->certification_test
                $results->save();
            };

            $user = User::find($request->uid);
            $user->redotest = $request->pid;
            $user->save();
        
            return back()->with('message', 'All Post Test Certification Test details for this user have been deleted successfully');
        } return back();
    }

    //return certification status
    private function certification($total, $passmark){
        if($total >= $passmark){
            return 'CERTIFIED';
        }return 'NOT CERTIFIED';
    }
}
