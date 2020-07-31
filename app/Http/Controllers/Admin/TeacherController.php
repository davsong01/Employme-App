<?php

namespace App\Http\Controllers\Admin;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcomemail;
use App\Role;
use App\Program;
use App\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class TeacherController extends Controller
{
    public function index()
    {
        $i = 1;
        //$users = User::all();
        $users = User::where('role_id', "Facilitator")->orWhere('role_id', 'Grader')->get();
        $programs = Program::where('id', '<>', 1)->get();
        
       if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.teachers.index', compact('users', 'i', 'programs') );
        }
    }
    
    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
            return view('dashboard.admin.teachers.create', compact('programs'));
        }return back();
    }
    public function store(Request $request)
    {        
        //dd(request()->all());
        $data = request()->validate([
            'name' => 'required | min:5',
            'email' =>'required | email',
            'phone' =>'required',
            'password' => 'required',
            'role'=>'required',
            'training' => 'required',
        ]);
        
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            't_phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role'],
            'program_id' => $data['training'],
        ]);
        
        return back()->with('message', 'Facilitator added succesfully'); 
      
        }

    public function show($id)
    {

    }

    public function edit($id)
    { 
            $user = User::findorFail($id);
            $programs = Program::all();
        if(Auth::user()->role_id == "Admin"){
        return view('dashboard.admin.teachers.edit', compact('programs','user'));
    }return back();
}

    public function update(Request $request, $id)
    {
        
        $user = User::findorFail($id);
        if($request['password']){
            $user-> password = bcrypt($request['password']);
        };
       
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->t_phone = $request['phone'];
        $user->program_id = $request['training'];
        $user->role_id = $request['role'];

        $user->save();
        //I used return redirect so as to avoid creating new instances of the user and program class
        if(Auth::user()->role_id == "Admin"){
        return redirect('teachers')->with('message', 'Facilitator updated successfully');
        } return back();
    
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('teachers')->with('message', 'Facilitator deleted successfully');
    }
    
}
