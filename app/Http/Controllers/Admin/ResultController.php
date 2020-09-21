<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\User;
use App\Module;
use App\Result;
use App\Program;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use intervention\Image\Facades\Image;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       if(Auth::user()->role_id == "Admin"){
            $i = 1;
            
            $users = User::with(['results', 'program'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->where('role_id', '<>', 'Grader')->where('role_id', '<>', 'Facilitator')->get();
        
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
           
            return view('dashboard.admin.results.index', compact('users', 'i') );
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
        // $data = request()->validate([
        //     'id' => 'required',
        //     'program_id' => 'required',
        //     'workbookscore' => 'required|numeric|min:0|max:35',
        //     'emailscore' => 'required|numeric|min:0|max:20',
        //     'roleplayscore' => 'required|numeric|min:0|max:25',
        //     'certificationscore' => 'required|numeric|min:0|max:20',
        //     'passmark' => 'required|numeric|min:50|max:100',
        //     'id' => 'required',
        // ]);
        // $total = $data['workbookscore'] + $data['emailscore'] + $data['roleplayscore'] + $data['certificationscore'];
        // $passmark = $data['passmark'];
        // $status = $this->certification($total, $passmark);

        // Result::create([
        //     'user_id' => $data['id'],
        //     'program_id' => $data['program_id'],
        //     'workbookscore' => $data['workbookscore'],
        //     'emailscore' => $data['emailscore'],
        //     'roleplayscore' => $data['roleplayscore'],
        //     'certificationscore' => $data['certificationscore'],
        //     'passmark' => $data['passmark'],
        //     'total' => $total,
        //     'status' => $status,
        // ]);

        // DB::table('users')
        //     ->where("id", '=', $data['id'])
        //     ->update(['hasResult'=> '1']);
        // return back()->with('message', 'Result saved');
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

        return view('dashboard.admin.results.edit', compact('user_results', 'array', 'i'));
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
            $result = Result::with('program', 'module', 'user')->where('user_id', Auth::user()->id)->get();    
            $program = Program::find($request->p_id);

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
                
                if($details['class_test_score'] <= 0 || $details['certification_test_score'] <= 0 || $details['role_play_score'] <= 0 || $details['email_test_score'] <= 0 ){
                    return back()->with('error', 'Your result is being processed, please check back later or notify your facilitator');
                }

                if($details['total_score'] >= $details['passmark']){
                    $details['status'] = 'CERTIFIED';
                }else $details['status'] = 'NOT CERTIFIED';         
                
               
                return view('dashboard.admin.results.show', compact('details', 'program'));   
            } 
            return redirect('/dashboard')->with('error', 'Result not found for current user, please try again later');
        }
        elseif(Auth::user()->role_id == "Teacher"){   
            $result = Result::where('user_id', $id)->first();
            $resultcount = (count($result));
            return view('dashboard.admin.results.show', compact('result'));
        }
        return redirect('/');
    }
    
    public function update(Request $request, $id)
    {

        $result = Result::findOrFail($id);
        
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
       
        return redirect('results')->with('message', 'User Scores have been updated successfully');
    }

   
    public function destroy(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){
            $users_results = Result::where('user_id', $request->id)->get();

            //delete all user results
            foreach($users_results as $results){
                $results->delete();
            };
            
            return redirect('results')->with('message', 'All Post Test Results for this user have been deleted successfully');
        } return back();
    }

    //return certification status
    private function certification($total, $passmark){
        if($total >= $passmark){
            return 'CERTIFIED';
        }return 'NOT CERTIFIED';
    }
}
