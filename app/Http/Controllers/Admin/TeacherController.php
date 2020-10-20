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
       
        $users = User::select('id', 'name', 'email', 'role_id')->distinct()->with('trainings')->where('role_id', "Facilitator")->orWhere('role_id', 'Grader')->orderBy('created_at', 'DESC')->get();
        
        
        foreach($users as $user){
            $names = [];
            foreach($user->trainings as $trainings){
               
                if(isset($trainings)){
                    $trainingp_name = Program::whereId($trainings->program_id)->value('p_name');

                    array_push($names, $trainingp_name);
                }else;
                
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
       
        $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

        foreach($programs as $program){
            $program['is_associated'] = FacilitatorTraining::whereUserId($user->id)->whereProgramId($program->id)->value('program_id');
        }

      
        // $programs_associated = FacilitatorTraining::whereUserId($user->id)->get();
    //    dd($programs_associated);
       
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
        $user->role_id = $request['role'];

        //Delete corresponding Facilitator Program details
        $facilitator = FacilitatorTraining::whereUserId($user->id);

        if(!isset($facilitator) && !isset($request['training'])){
            return back()->with('error', 'Facilitator is not attached to any training, please add a training to facilitator');
        }
       
        $facilitator->delete();

        foreach($request['training'] as $training){
            FacilitatorTraining::UpdateorCreate([
                'user_id' => $user->id,
                'program_id' =>$training
            ]);
        }
        $user->save();
       
        if(Auth::user()->role_id == "Admin"){
        return redirect('teachers')->with('message', 'Facilitator updated successfully');
        } return back();
    
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        //Delete corresponding programs
        $programs = FacilitatorTraining::whereUserId($user->id)->delete();

        $user->delete();
        return redirect('teachers')->with('message', 'Facilitator deleted successfully');
    }
    
}
