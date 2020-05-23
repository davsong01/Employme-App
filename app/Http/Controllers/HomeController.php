<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use Calendar;
use App\Module;
use App\Picture;
use App\Program;

use App\Material;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        //Get calendar details
        $events = [];
        $data = Program::all();
        if($data->count()){
           foreach ($data as $key => $value) {
             $events[] = Calendar::event(
                 $value->p_name,
                 true,
                 new \DateTime($value->p_start),
                 new \DateTime($value->p_end.' +1 day')
             );
           }
        }
 
       $calendar = Calendar::addEvents($events); 
       $currentUser = DB::table('users')->where('id', '==', Auth::user()->id)->get(); 
        $currentUsermaterialsCount = Material::where('program_id', '=', Auth::user()->program_id)->get()->count();
      
        if(Auth::user()->role_id == "Admin"){
            $programCount = Program::count();
            //count number of users owing
            $userowing = $userCount = DB::table('users')->where([
                'role_id' => 'Student',
                'paymentStatus' => 0,
            ])->get()->count();
            //count number of users present
            $userCount = DB::table('users')->where('role_id', '=', 'Student')->get()->count();
            $materialCount = Material::count();
            $i = 0;
           $requests = $request;
            
        return view('dashboard.admin.dashboard', compact('programCount', 'calendar','requests', 'userowing', 'userCount', 'i', 'materialCount'));

        } 
        if(Auth::user()->role_id == "Teacher"){
            
            return view('dashboard.teacher.dashboard', compact('currentUser', 'calendar','currentUsermaterialsCount'));
        }
        if(Auth::user()->role_id == "Student"){
            
            //Get Length of training
            $trainingStartDate = Auth::user()->program->p_start;
            $trainingEndDate = Auth::user()->program->p_end;

            $datetime1 = new \DateTime($trainingStartDate);
            $datetime2 = new \DateTime($trainingEndDate);

            $interval = $datetime1->diff($datetime2);
            $lengthofTraining = $interval->format('%R%a') + 1;

            //Get current lenngth
            $trainingStartDate = Auth::user()->program->p_start;
            $trainingEndDate = Auth::user()->program->p_end;

            $date1 = new \DateTime($trainingStartDate);
            $date2 = new \DateTime("now");
         
            $length = $date1->diff($date2);
            
            //dd($length->days); 
            //check if training is still in progress
            if( date("Y-m-d") >= $trainingStartDate && date("Y-m-d") <= $trainingEndDate)
            {
                $trainingProg =  (($length->days) * 100)/$lengthofTraining;
                $trainingProgress = number_format($trainingProg , 2);
            }
            //check if training has started
            elseif( $trainingEndDate > date("Y-m-d"))
            {
                $trainingProgress = 0;
            }
            //check if training has ended
            else if( $trainingEndDate < date("Y-m-d")) {
                $trainingProgress = 100;
            }
  
            //get enabled module Tests for this user
            $modules = Module::where('program_id', Auth()->user()->program->id)->where('status', 1)->get();
            $module_count = $modules->count();

            return view('dashboard.student.dashboard', compact('modules','currentUser', 'calendar','currentUsermaterialsCount', 'trainingProgress'));
    }
    } 
}
