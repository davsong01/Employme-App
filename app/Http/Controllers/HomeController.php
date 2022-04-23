<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Pop;
use App\User;
use Calendar;
use App\Module;
use App\Picture;

use App\Program;
use App\Material;
use App\FacilitatorTraining;
use Illuminate\Http\Request;

class HomeController extends Controller
{

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
       $currentUser = User::findOrFail(Auth::user()->id)->programs()->get();

        if(Auth::user()->role_id == "Admin" ){
            
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

            //Get all Programs
            $programCount = Program::where('id', '<>', 1)->count();

            //Get all students
            $users = User::where('role_id', 'Student')->get();
            $userCount = $users->count();

            //Get pending payments
            $pending_payments = Pop::all()->count();

            //Get Users owing
            foreach($users as $user){
               $users['userowing'] = DB::table('program_user')->where('balance', '>', 0)->count();
            }
            if(isset($users['userowing']))
                $userowing = ($users['userowing']);
            else
                $userowing = null;

            $materialCount = Material::count();
            $i = 0;

            $requests = $request;

            return view('dashboard.admin.dashboard', compact('programCount', 'calendar','requests', 'userowing', 'userCount', 'i', 'materialCount', 'pending_payments'));

        }

         if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader" ){

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

            //Get all Programs
            $programCount = Program::where('id', '<>', 1)->count();

            //Get all students
            $users = User::where('role_id', 'Student')->get();


            //Get pending payments
            $pending_payments = Pop::all()->count();

            //Get Users owing
            foreach($users as $user){
               $users['userowing'] = DB::table('program_user')->where('user_id', $user->id)->where('balance', '>', 0)->count();
            }
            $userowing = ($users['userowing']);

            //get number of users and materials for this faciliator/grader
            $facilitator_programs = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();
            $i = 0;
            $userCount = 0;
            $materialCount = 0;

            if($facilitator_programs->count() > 0){

                foreach($facilitator_programs as $programs){
                    $userCount = DB::table('program_user')->whereProgramId($programs->program_id)->count() + $userCount;
                    $materialCount = Material::whereProgramId($programs->program_id)->count() + $materialCount;
                }
            }

            $requests = $request;

            return view('dashboard.admin.dashboard', compact('programCount', 'calendar','requests', 'userowing', 'userCount', 'i', 'materialCount', 'pending_payments'));
         }

        if(Auth::user()->role_id == "Student"){

            //get enabled module Tests for this user
            $thisusertransactions = DB::table('program_user')->where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();

            foreach($thisusertransactions as $transactions){
                $transactions->modules = Module::where('program_id', $transactions->program_id)->where('status', 1)->count();
                $transactions->materials = Material::where('program_id', $transactions->program_id)->count();
                $transactions->p_name =  Program::where('id', $transactions->program_id)->value('p_name');
                $transactions->p_id =  Program::where('id', $transactions->program_id)->value('id');
            }

            return view('dashboard.student.dashboard', compact('thisusertransactions' ));
        }

    }

    public function trainings($id){
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

        if(Auth::user()->role_id == "Student"){
            //Get Length of training
            $program = Program::findOrFail($id);

            $trainingStartDate = $program->p_start;
            $trainingEndDate = $program->p_end;

            $datetime1 = new \DateTime($trainingStartDate);
            $datetime2 = new \DateTime($trainingEndDate);

            $interval = $datetime1->diff($datetime2);
            $lengthofTraining = $interval->format('%R%a') + 1;

            //Get current length
            $trainingStartDate = $program->p_start;
            $trainingEndDate = $program->p_end;

            $date1 = new \DateTime($trainingStartDate);
            $date2 = new \DateTime("now");

            $length = $date1->diff($date2);

            // check if training is still in progress
            if( date("Y-m-d") >= $trainingStartDate && date("Y-m-d") <= $trainingEndDate)
            {
                $trainingProg =  (($length->days) * 100)/$lengthofTraining;
                $trainingProgress = number_format($trainingProg , 2);
            }
            // check if training has started
            elseif( $trainingEndDate > date("Y-m-d"))
            {
                $trainingProgress = 0;
            }
            // check if training has ended
            else if( $trainingEndDate < date("Y-m-d")) {
                $trainingProgress = 100;
            }

            //get materials count
            $materialsCount = Material::where('program_id', $program->id)->count();
            $paid = DB::table('program_user')->where('program_id', $program->id)->where('user_id', auth()->user()->id)->value('t_amount');
            $balance = DB::table('program_user')->where('program_id', $program->id)->where('user_id', auth()->user()->id)->value('balance');

            return view('dashboard.student.trainings', compact('calendar', 'materialsCount',  'trainingProgress', 'paid', 'balance', 'program' ));
        }else return abort(404);
    }

    public function demo(){
        return view('dashboard.admin.demo');
    }
}
