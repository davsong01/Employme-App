<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Pop;
use App\Models\User;
use Calendar;
use App\Models\Module;
use App\Models\Picture;

use App\Models\Program;
use App\Models\Material;
use Carbon\Carbon;
use App\Models\PaymentMode;
use App\Models\Transaction;
use App\Models\Models\Wallet;
use App\Models\FacilitatorTraining;
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
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $events = [];
            $data = Program::all();
            
            // $calendar = Calendar::addEvents($events);
            $calendar = [];

            //Get all Programs
            $programCount = Program::where('id', '<>', 1)->count();

            //Get all students
            $users = User::where('role_id', 'Student')->get();
            $userCount = $users->count();

            //Get pending payments
            $pending_payments = Pop::all()->count();

            //Get Users owing
            foreach ($users as $user) {
                $users['userowing'] = Transaction::where('balance', '>', 0)->count();
            }
            if (isset($users['userowing']))
                $userowing = ($users['userowing']);
            else
                $userowing = null;

            $materialCount = Material::count();
            $i = 0;

            $requests = $request;

            return view('dashboard.admin.dashboard', compact('programCount', 'calendar', 'requests', 'userowing', 'userCount', 'i', 'materialCount', 'pending_payments'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $events = [];
            $data = Program::all();
            // if($data->count()){
            // foreach ($data as $key => $value) {
            //     $events[] = Calendar::event(
            //         $value->p_name,
            //         true,
            //         new \DateTime($value->p_start),
            //         new \DateTime($value->p_end.' +1 day')
            //     );
            // }
            // }

            $calendar = [];

            //get number of users and materials for this faciliator/grader
            $user = Auth::user();
            $details = DB::table('facilitator_trainings')->where('user_id', $user->id);
            $user->programCount = $details->distinct()->count();
            $transactions = DB::table('program_user')->where('facilitator_id', $user->id);
            $user->students_count = $transactions->count();
            $user->earnings = $transactions->sum('facilitator_earning');


            $user->trainings->map(function ($q) {
                $q->p_name = Program::whereId($q->program_id)->value('p_name');
                $q->materials = Material::where('program_id', $q->program_id)->count();
                $user['materials'] = $q->materials;
                return $q;
            });

            $materialCount = $user->trainings->sum('materials');

            $requests = $request;
            $i = 1;

            return view('dashboard.admin.dashboard', compact('calendar', 'requests',  'i', 'user', 'materialCount'));
        }
        
        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            //get enabled module Tests for this user
            $thisusertransactions = Transaction::whereHas('program', function($query){
                $query->where('program_lock', 0);
            })->where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
            foreach ($thisusertransactions as $transactions) {
                $transactions->modules = Module::where('program_id', $transactions->program_id)->where('status', 1)->count();
                $transactions->materials = Material::where('program_id', $transactions->program_id)->count();
                $transactions->p_name =  Program::where('id', $transactions->program_id)->value('p_name');
                $transactions->p_id =  Program::where('id', $transactions->program_id)->value('id');
            }

            $account_balance = app('App\Http\Controllers\WalletController')->getWalletBalance(auth()->user()->id);
            
            $topup_programs = Program::where('allow_preferred_timing', 'yes')->where('p_end', '>', Carbon::now())->get();
            
            return view('dashboard.student.dashboard', compact('thisusertransactions', 'account_balance', 'topup_programs'));
        }
    }

    public function balanceCheckout(Request $request)
    {
        $data = DB::table('program_user')
            ->where('program_id', $request->p_id)
            ->where('user_id', auth()->user()->id)
            ->where('balance', '>', 0)
            ->first();
        // dd($data);
        $program = Program::whereId($request->p_id)->first();

        $payment_mode =  PaymentMode::where('id', $data->payment_mode)->first();
        
        return view('dashboard.student.balance_checkout', compact('data', 'payment_mode', 'program'));
    }

    public function trainings($id)
    {
        //Get calendar details
        $events = [];
        $data = Program::all();
        // if($data->count()){
        // foreach ($data as $key => $value) {
        //     $events[] = Calendar::event(
        //         $value->p_name,
        //         true,
        //         new \DateTime($value->p_start),
        //         new \DateTime($value->p_end.' +1 day')
        //     );
        // }
        // }

        $calendar = [];

        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
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
            if (date("Y-m-d") >= $trainingStartDate && date("Y-m-d") <= $trainingEndDate) {
                $trainingProg =  (($length->days) * 100) / $lengthofTraining;
                $trainingProgress = number_format($trainingProg, 2);
            }
            // check if training has started
            elseif ($trainingEndDate > date("Y-m-d")) {
                $trainingProgress = 0;
            }
            // check if training has ended
            else if ($trainingEndDate < date("Y-m-d")) {
                $trainingProgress = 100;
            }

            //get materials count
            $materialsCount = Material::where('program_id', $program->id)->count();
            $data = DB::table('program_user')->where('program_id', $program->id)->where('user_id', auth()->user()->id);
            $paid = $data->value('currency_symbol') . number_format($data->value('t_amount'));
            $balance = $data->value('balance');
            $currency_symbol = $data->value('currency_symbol');
            $facilitator = $data->value('facilitator_id');

            if ($facilitator) {
                $facilitator = User::select('name')->whereId($facilitator)->value('name');
            } else {
                $facilitaor = null;
            }

            return view('dashboard.student.trainings', compact('currency_symbol', 'facilitator', 'calendar', 'materialsCount',  'trainingProgress', 'paid', 'balance', 'program'));
        } else return abort(404);
    }

    public function downloadProgramBrochure()
    {
        auth()->user()->update([
            'downloaded_catalogue' => 1
        ]);

        // $realpath = public_path() . '/catalogue.pdf';
        $realpath = realpath('./catalogue.pdf');
        // dd($realpath);
        return response()->download($realpath);
    }
    public function demo()
    {
        return view('dashboard.admin.demo');
    }
}
