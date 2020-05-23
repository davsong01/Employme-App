<?php

namespace App\Http\Controllers\Admin;

use App\Program;
use App\User;
use App\Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use DB;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $i = 1;
        $results = Result::orderBy('created_at', 'DESC')->get();
       //$users = DB::table('users')->where('role_id', '<>', "Admin")->get();
        $programs = Program::where('id', '<>', 1)->get();
       if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.results.index', compact('results', 'i', 'programs') );
        }elseif(Auth::user()->role_id == "Teacher"){
            $results = Result::where('program_id', '=', Auth::user()->program_id )->orderBy('created_at', 'DESC')->get();
            $programs = Result::where('program_id', '=', Auth::user()->program_id )->orderBy('created_at', 'DESC')->get();
            return view('dashboard.teacher.results.index', compact('results', 'i', 'programs') );
          }return redirect('/dashboard');
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
        $data = request()->validate([
            'id' => 'required',
            'program_id' => 'required',
            'workbookscore' => 'required|numeric|min:0|max:35',
            'emailscore' => 'required|numeric|min:0|max:20',
            'roleplayscore' => 'required|numeric|min:0|max:25',
            'certificationscore' => 'required|numeric|min:0|max:20',
            'passmark' => 'required|numeric|min:50|max:100',
            'id' => 'required',
        ]);
        $total = $data['workbookscore'] + $data['emailscore'] + $data['roleplayscore'] + $data['certificationscore'];
        $passmark = $data['passmark'];
        $status = $this->certification($total, $passmark);

        Result::create([
            'user_id' => $data['id'],
            'program_id' => $data['program_id'],
            'workbookscore' => $data['workbookscore'],
            'emailscore' => $data['emailscore'],
            'roleplayscore' => $data['roleplayscore'],
            'certificationscore' => $data['certificationscore'],
            'passmark' => $data['passmark'],
            'total' => $total,
            'status' => $status,
        ]);

        DB::table('users')
            ->where("id", '=', $data['id'])
            ->update(['hasResult'=> '1']);
        return back()->with('message', 'Result saved');
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
        
        $result = Result::where('user_id', $id)->first();
        
        $result->workbookscore = $request['workbookscore'];
        $result->emailscore = $request['emailscore'];
        $result->roleplayscore = $request['roleplayscore'];
        $result->certificationscore = $request['certificationscore'];

        $total = $request['workbookscore'] + $request['emailscore'] + $request['roleplayscore'] + $request['certificationscore'];

        $result->total = $total; 

        $result->save();
        
        return redirect('results')->with('message', 'result updated successfully');
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
