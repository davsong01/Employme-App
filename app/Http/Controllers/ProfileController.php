<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    
    public function edit($id)
    {
        $user = User::whereId($id)->first();

        if(Auth::user()->role_id == "Admin" && $id == Auth::user()->id){
        return view('dashboard.admin.profiles.edit', compact('user'));
        }
        elseif(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            $i = 1;
            $programs = FacilitatorTraining::with('programName')->whereUserId($user->id)->get();
            $single_program = Program::whereOffSeason(1)->first();
           
            // $program = $programs->where('off_season')
            $students = $user->students()->get();
            return view('dashboard.admin.profiles.edit_facilitator', compact('i', 'user', 'programs', 'single_program','students'));
        }
        elseif(Auth::user()->role_id == "Student" && $id == Auth::user()->id){
            return view('dashboard.student.profiles.edit', compact('user'));
        }
        return back();
    }
   
    public function update(Request $request, $id)
    {   
      
        $user = User::findorFail(Auth::user()->id);
       
        $user->name = $request->name;
        $user->t_phone = $request->t_phone;
        $user->gender = $request->gender;

        if(auth()->user()->role_id == 'Facilitator' || auth()->user()->role_id == 'Grader'){
            $user->off_season_availability = $request->off_season_availability;
            $user->profile = $request->profile;
            
        } 
        if($request['password']){
            $user->password = bcrypt($request['password']);
        };

        if(request()->has('file')){ 
           
            $imgName = $request->file->getClientOriginalName();
            
            $picture = Image::make($request->file)->resize(100, 100);
            
            $picture->save('profiles/'.'/'.$imgName);

            $user->update([
                'profile_picture' => $imgName,
            ]);
        }
        
        $user->save();
       
        return back()->with('message', 'Profile update successful');
    
    }
    private function validateRequest(){
        return tap(request()->validate([
        'name' => 'required',
        't_phone' =>'required | numeric | min:9',
        'gender' => 'required',
        ]), function (){
            if (request()->hasFile('file')){
                request()->validate([
                    'file' =>'mimes:jpeg,png,jpg|max:1000',
                ]);
            }
        });
    
    }
    
    public function showFacilitator($id){
        $facilitators = User::whereRoleId('Facilitator')->whereOffSeasonAvailability(1)->get();
        $program = Program::find($id);
        return view('dashboard.student.profiles.facilitators', compact('facilitators', 'program'));
    }

    public function saveFacilitator(Request $request){
        // Check if user already has facilitator
        if(auth()->user()->facilitator_id){
            return redirect(route('trainings.show', $request['program_id']))->with('error', 'You have already selected a facilitator');
        }
        // Update user with selected facilitator and update facilitator points
        $facilitator = User::find($request['facilitator_id']);
        $program = Program::find($request['program_id']);
      
        auth()->user()->update(['facilitator_id' => $request['facilitator_id']]);
        
        $this->creditFacilitator($facilitator, $program);
        
        // Add Training to facilitator list
        $counter = FacilitatorTraining::whereProgramId($request['program_id'])->whereUserId($request['facilitator_id'])->count();
   
        if($counter < 1){
            FacilitatorTraining::UpdateorCreate([
                'user_id' => $request['facilitator_id'],
                'program_id' =>$request['program_id']
            ]);
        }
    
        // Notify facilitator
        $details = [];
        $data = [
            'type' => 'notify_facilitator',
            'name' => $facilitator->name,
            'email' => $facilitator->email,
            'student_name' => auth()->user()->name,
            'student_email' => auth()->user()->email,
            'student_phone' => auth()->user()->t_phone,
            'program_name' => $program->p_name,
            'date' => now(),
        ];

        $this->sendWelcomeMail($details, $data);

        return redirect(route('trainings.show', $program->id))->with('message', 'Facilitator selected succesfully');

    }
    
}
