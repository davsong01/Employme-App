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
        //$results = Result::with(['user', 'program', 'module'])->orderBy('id', 'DESC')->get();

        $users = User::with(['results'])->orderBy('id', 'DESC')->where('role_id', '<>', 'Admin')->get();

        foreach($users as $user){
           
            $user['total_ct_score'] = 0;
            $user['total_cert_score'] = 0;
            $user['program_score_settings'] = 0;
            $user['no_of_questions'] = 0;
            $user['total_email_test_score'] = 0;

            $user['class_test_module_count'] = Module::where('program_id', $user->program->id)->where('type', 'Class Test')->count();  
            $user['total_role_play_score'] = 0;
            if(isset($user->results)){
                foreach($user->results as $results){

                    $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
                    $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 

                    // $user['result_id'] = $results->id;

                   $user['module'] = $results->module->id;
                    if($results->module->type == 'Class Test'){
                        $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
                        //$user['modules'] = $results->module->count();
                        $user['program_score_settings'] = $results->program->scoresettings->class_test;
                        //print_r( $results->module->questions->count());
                        $user['no_of_questions'] = $results->module->questions->count() + $user['no_of_questions'];
                       
                        $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) /$user['no_of_questions'];

                        $user['test_score'] = round($user['test_score'] , 0);
                        // $user['result_id'] = $results->id;
                        //print_r($r);
                    }

                    if($results->module->type == 'Certification Test'){
                        $user['result_id'] = $results->id;
                    }
                    
                    $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score'];  
                   
            } 
        }
       
        }
        // dd($users);
          return view('dashboard.admin.results.index', compact('users', 'i') );
        }
         
          return redirect('/dashboard');
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->get();
            $users = User::where('role_id', '<>', "Admin")->where('role_id', '<>', "Teacher")->where('hasResult', '<>', 1)->orderBy('created_at', 'DESC')->get();
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
    
    public function show(Result $result, $id)
    {      
        
        if(Auth::user()->role_id == "Admin"){
        $result = Result::where('user_id', $id)->first();
           //dd($result->user_id);
        $resultcount = (count($result));
        return view('dashboard.admin.results.show', compact('result'));
        } 
        elseif(Auth::user()->role_id == "Student" AND(Auth::user()->id == $id)) {   
            $result = Result::where('user_id', Auth::user()->id)->first();

            if($result){
                if(Auth::user()->balance > 0){
                    return back()->with('error', 'Dear '.Auth::user()->name.', Please pay your balance of '. Auth::user()->balance.' in order to view/print your result');
                }
                return view('dashboard.admin.results.show', compact('result'));   
            } 
            return back()->with('error', 'Result not found for current user, please try again later');
        }
        elseif(Auth::user()->role_id == "Teacher"){   
            $result = Result::where('user_id', $id)->first();
            $resultcount = (count($result));
            return view('dashboard.admin.results.show', compact('result'));
        }
            return redirect('/');
    

        }
        
    public function edit($id)
    {
        if(Auth::user()->role_id == "Admin"){
            $results = Result::where('user_id', $id)->first();

            return view('dashboard.admin.results.edit', compact('results'));
    }elseif(Auth::user()->role_id == "Teacher"){
            $results = Result::where('user_id', $id)->first();
            return view('dashboard.teacher.results.edit', compact('results'));
} 
    return back();
}

    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        DB::table('results')
            ->where('id', $id)
            ->update([
                'certification_test_score' => $request->certification_score,
                'role_play_score' => $request->roleplayscore,
                'email_test_score' => $request->emailscore,
                ]);
       

        return redirect('results')->with('message', 'User Scores have been updated successfully');
    }

   
    public function destroy(Result $result, Request $id)
    {

        if(Auth::user()->role_id == "Admin"){

            //change user result status back to no result
            $user = User::where('id', $result->user_id)->first();
            $user->hasResult = 0; 

            //update user
            $user->save();

            //delete result
            $result->delete();

            return redirect('results')->with('message', 'result deleted successfully');
        } return back();
    }

    //return certification status
    private function certification($total, $passmark){
        if($total >= $passmark){
            return 'CERTIFIED';
        }return 'NOT CERTIFIED';
    }
}
