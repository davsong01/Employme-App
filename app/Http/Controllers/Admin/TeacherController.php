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
        $users = User::where('role_id', "Facilitator")->get();
        $programs = Program::where('id', '<>', 1)->get();
        
       if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.teachers.index', compact('users', 'i', 'programs') );
        }
    }
    
    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $users = User::orderBy('created_at', 'DESC');
            $user = User::all();
        $programs = Program::all();
            return view('dashboard.admin.teachers.create', compact('users', 'user', 'programs'));
        }return back();
    }
    public function store(Request $request)
    {        
        $data = request()->validate([
            'name' => 'required | min:5',
            'email' =>'required | email',
            'phone' =>'required',
            'password' => 'required',
            'role'=>'required',
            'training' => 'required',
            'gender' => '',    
        ]);
        
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            't_phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role'],
            'program_id' => $data['training'],
            'gender' => $data['gender'],
        ]);
        
        return back()->with('message', 'User added succesfully'); 
      
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
        $user->gender = $request['gender'];

        $user->save();
        //I used return redirect so as to avoid creating new instances of the user and program class
        if(Auth::user()->role_id == "Admin"){
        return redirect('teachers')->with('message', 'user updated successfully');
        } return back();
    
    }
    public function destroy(User $user)
    {
        $user->delete();
        return redirect('teachers')->with('message', 'user deleted successfully');
    }
    
}
