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
use Intervention\Image\Facades\Image;

class TeacherController extends Controller
{
    public function index()
    {
        $i = 1;
        //$users = User::all();
       
        $users = User::select('id', 'off_season_availability','name', 'earnings', 'email','profile_picture', 'role_id', 'created_at')->distinct()->with('trainings')->where('role_id', "Facilitator")->orWhere('role_id', 'Grader')->orderBy('created_at', 'DESC')->get();
        
        
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
            $data = request()->validate([
            'name' => 'required | min:5',
            'profile' => 'nullable',
            'file' => 'nullable',
            'email' =>'required | email',
            'password' => 'required',
            'role'=>'required',
            'training' => 'nullable',
            'off_season_availability' => 'nullable',
            'earning_per_head' => 'nullable'
            ]);

        if(request()->has('file')){ 
        
            $imgName = $request->file->getClientOriginalName();
            
            $picture = Image::make($request->file)->resize(100, 100);
            
            $picture->save('profiles/'.'/'.$imgName);
        }
          
        if($data['role'] == "Facilitator" || $data['role'] == "Grader"){    
           $facilitator = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'profile' => $data['profile'],
                'off_season_availability' => $data['off_season_availability'],
                'profile_picture' => $imgName,
                'password' => bcrypt($data['password']),
                'role_id' => $data['role'],
                'earning_per_head' => $data['earning_per_head'],
            ]);

            if($request->has('training')){
                //attach facilitator to program
                foreach($data['training'] as $training){
                    FacilitatorTraining::create([
                        'user_id' => $facilitator->id,
                        'program_id' =>$training
                    ]);
                }
            }
            return redirect(route('teachers.index'))->with('message', 'Facilitator added succesfully'); 
        }
        
        if($data['role'] == "Admin"){ 
            User::create([
                'name' => $data['name'],
                'profile' => $data['profile'],
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
        $students = $user->students()->get();
       
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
        return view('dashboard.admin.teachers.edit', compact('programs','user', 'students', 'i'));
    }return back();
}

    public function update(Request $request, $id)
    {
      
        $user = User::findorFail($id);
        if($request['password']){
            $user-> password = bcrypt($request['password']);
        };
       
        if(request()->has('file')){ 
            $imgName = $request->file->getClientOriginalName();
            $picture = Image::make($request->file)->resize(100, 100);
            $picture->save('profiles/'.'/'.$imgName);
        }
          
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->t_phone = $request['phone'];
        $user->role_id = $request['role'];
        $user->profile = $request['profile'];
        $user->profile_picture = $imgName ?? $user->profile_picture;
        $user->earning_per_head = $request['earning_per_head'];
        $user->off_season_availability = $request['off_season_availability'];
        
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
