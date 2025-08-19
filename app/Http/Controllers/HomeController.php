<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Pop;
use App\Models\User;
use App\Models\Module;

use App\Models\Program;
use App\Models\Material;
use Carbon\Carbon;
use App\Models\PaymentMode;
use App\Models\TempTransaction;
use App\Models\Transaction;
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
        $currentUser = User::findOrFail(resolveAuthUser()->id)->programs()->get();
        
        if (checkRoleHas(['Admin'])) {

            $data = Program::all();
            
            //Get all Programs
            $programCount = Program::where('id', '<>', 1)->count();

            //Get all students
            $users = User::where('roles', 'Student')->get();
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

            return view('dashboard.admin.dashboard', compact('programCount', 'requests', 'userowing', 'userCount', 'i', 'materialCount', 'pending_payments'));
        }


        if (checkRoleHas(['Facilitator', 'Grader'])) {

            $events = [];
            $data = Program::all();
            
            //get number of users and materials for this faciliator/grader
            $user = resolveAuthUser();
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

            return view('dashboard.admin.dashboard', compact('requests',  'i', 'user', 'materialCount'));
        }
        
        
        if (checkRoleHas(['Student'])) {
            //get enabled module Tests for this user
            $thisusertransactions = Transaction::whereHas('program', function($query){
                $query->where('program_lock', 0);
            })->where('user_id', resolveAuthUser()->id)->orderBy('created_at', 'DESC')->get();
            foreach ($thisusertransactions as $transactions) {
                $transactions->modules = Module::where('program_id', $transactions->program_id)->where('status', 1)->count();
                $transactions->materials = Material::where('program_id', $transactions->program_id)->count();
                $transactions->p_name =  Program::where('id', $transactions->program_id)->value('p_name');
                $transactions->p_id =  Program::where('id', $transactions->program_id)->value('id');
            }

            $account_balance = app('App\Http\Controllers\WalletController')->getWalletBalance(resolveAuthUser()->id);
            
            $topup_programs = Program::where('allow_preferred_timing', 'yes')->where('p_end', '>', Carbon::now())->get();
            
            return view('dashboard.student.dashboard', compact('thisusertransactions', 'account_balance', 'topup_programs'));
        }
    }

    public function balanceCheckout(Request $request)
    {
        $data = DB::table('program_user')
            ->where('program_id', $request->p_id)
            ->where('user_id', resolveAuthUser()->id)
            ->where('balance', '>', 0)
            ->first();
        // dd($data);
        $program = Program::whereId($request->p_id)->first();

        $payment_mode =  PaymentMode::where('id', $data->payment_mode)->first();
        // dd($payment_mode, $data->payment_mode, $request->all());
        return view('dashboard.student.balance_checkout', compact('data', 'payment_mode', 'program'));
    }

    public function trainings($id)
    {
        if (checkRoleHas(['Student'])){
            //Get Length of training
            $program = Program::find($id);

            //get materials count
            $materialsCount = Material::where('program_id', $program->id)->count();

            $data = TempTransaction::whereRaw('JSON_CONTAINS(program_ids, ?)',[json_encode($program->id)]
            )->where('user_id', resolveAuthUser()->id)->first() ?? collect([]);
            // $programId = $program->id;

            // $data = TempTransaction::where(function ($q) use ($programId) {
            //     $q->whereRaw('JSON_CONTAINS(program_ids, ?)', [json_encode((int) $programId)])
            //         ->orWhereRaw('JSON_CONTAINS(program_ids, ?)', [json_encode((string) $programId)]);
            // })
            // ->where('user_id', resolveAuthUser()->id)
            // ->first() ?? collect([]);

            $paid = $data->currency_symbol . number_format($data->amount);
            $balance = $data->balance;
            $currency_symbol = $data->currency_symbol;
            $facilitator = $data->facilitator_id;

            if ($facilitator) {
                $facilitator = User::select('name')->whereId($facilitator)->value('name');
            } else {
                $facilitaor = null;
            }

            return view('dashboard.student.trainings', compact('currency_symbol', 'facilitator', 'materialsCount', 'paid', 'balance', 'program'));
        } else return abort(404);
    }

    public function downloadProgramBrochure()
    {
        resolveAuthUser()->update([
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
