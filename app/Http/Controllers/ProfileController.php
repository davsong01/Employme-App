<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Material;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function showFacilitator(Request $request){
        $instructor = DB::table('program_user')->where('program_id', $request->p_id)->where('user_id', auth::user()->id)->first();
        $facilitator = User::find($instructor->facilitator_id);
        if(!$facilitator){
            return back()->with('error', 'No Instructor found!');
        }
        $program = Program::find($request->p_id);

        return view('dashboard.student.profiles.facilitators', compact('facilitator', 'program'));

    }

    // public function showFacilitator($id)
    // {
    //     $facilitators = User::whereRoleId('Facilitator')->whereOffSeasonAvailability(1)->get();
    //     $program = Program::find($id);
    //     return view('dashboard.student.profiles.facilitators', compact('facilitators', 'program'));
    // }
    
    public function edit($id)
    {
        $user = User::with('trainings')->whereId($id)->first();

        if(Auth::user()->role_id == "Admin" && $id == Auth::user()->id){
            return view('dashboard.admin.profiles.edit', compact('user'));
        }
        elseif(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
        
            $other_details = DB::table('program_user')->where('facilitator_id', $user->id);
            $user->students_count = $other_details->count();
            $user->earnings = $other_details->sum('facilitator_earning');

            $programs = FacilitatorTraining::count();

            //get number of users and materials for this faciliator/grader
            $user = Auth::user();
            $details = DB::table('facilitator_trainings')->where('user_id', $user->id);
            $user->programCount = $details->distinct()->count();
            $transactions = DB::table('program_user')->where('facilitator_id', $user->id);
            $user->students_count = $transactions->count();
            $user->earnings = $transactions->sum('facilitator_earning');
           
            return view('dashboard.admin.profiles.edit_facilitator', compact('programs', 'user'));
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
        $user->t_phone = $request->phone;
        $user->gender = $request->gender;
        
        if(auth()->user()->role_id == 'Facilitator' || auth()->user()->role_id == 'Grader'){
            $user->off_season_availability = $request->off_season;
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
