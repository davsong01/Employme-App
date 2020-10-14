<?php

namespace App\Http\Controllers\Admin;
use PDF;
use App\Role;
use App\User;
use App\Program;
use App\Material;
use App\Mail\Welcomemail;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function index()
    {
        $i = 1;
        //$users = User::all();
        $users = User::with('trainings')->where('role_id', "Facilitator")->orWhere('role_id', 'Grader')->get();
        $names = [];
        
        foreach($users as $user){
            
            foreach($user->trainings as $trainings){
                $trainingp_name = Program::whereId($trainings->program_id)->value('p_name');

                array_push($names, $trainingp_name);
            }
           
            $user->p_names =  $names ;
        }
        
        if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.teachers.index', compact('users', 'i') );
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
        // dd(request()->all());
        
            $data = request()->validate([
            'name' => 'required | min:5',
            'email' =>'required | email',
            'password' => 'required',
            'role'=>'required',
            'training' => 'nullable',
            ]);
        if($data['role'] == "Facilitator" || $data['role'] == "Grader"){    
           $facilitator = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role'],
            ]);

            //attach facilitator to program
            foreach($data['training'] as $training){
                FacilitatorTraining::create([
                    'user_id' => $facilitator->id,
                    'program_id' =>$training
                ]);
            }
            
            return redirect(route('teachers.index'))->with('message', 'Facilitator added succesfully'); 
        }
        
        if($data['role'] == "Admin"){ 
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role'],
            ]); 

            return redirect(route('teachers.index'))->with('message', 'Admin added succesfully'); 
        }
        
      
        }

    public function show($id)
    {

    }

    public function edit($id)
    { 
        $user = User::findorFail($id);
        $programs = Program::all();
        foreach($programs as $program){
            $program['associated'] = FacilitatorTraining::whereUserId($user->id)->get();
        }

    //    dd($programs);
       
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
