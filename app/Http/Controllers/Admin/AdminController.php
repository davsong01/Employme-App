<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MockController;
use App\Models\CompanyUserTraining;
use App\Models\Material;
use App\Models\Pop;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\Facades\FastExcel;


class AdminController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $request['prefix__'] = request()->route()->getPrefix();

        return view('auth.login');
    }

    // public function login(Request $request){
    //     $credentials = $request->only('email', 'password');
    //     session()->flush();

    //     if (Auth::guard('admin')->attempt($credentials)) {
    //         $user = Auth::guard('admin')->user();
    //         $user->update(['last_login' => now()]);

    //         return redirect()->route('admin.home');
    //     }

    //     return redirect()->back()->with('error', 'Invalid credentials');
    // }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        session()->flush();

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_login' => now()]);
            
            return redirect()->intended(route('admin.home'));
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }


    public function index(Request $request)
    {
        if (checkRoleHas(['Admin'])) {
            $data = Program::all();

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

            return view('dashboard.admin.dashboard', compact('programCount',  'requests', 'userowing', 'userCount', 'i', 'materialCount', 'pending_payments'));
        }


        if (checkRoleHas(['Facilitator', 'Grader'])) {
            // $data = Program::all();
            $user = resolveAuthUser();
            $user_trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
            
            //get number of users and materials for this faciliator/grader
            $user->programCount =  count($user_trainings);

            $transactions = Transaction::where('facilitator_id', $user->id);
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
            $thisusertransactions = Transaction::whereHas('program', function ($query) {
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

    public function dashboard()
    {
        $programs = resolveAuthUser()->trainings()->whereHas('program', function ($query) {
            $query->where('program_lock', 0);
        })->get();
        
        foreach($programs as $program){
            $program->user_count = Transaction::where('program_id', $program->program_id)->count();
        }
        return view('dashboard.company.dashboard', compact('programs'));
    }

    public function profile()
    {
        // Logic for profile view
        return view('company_user.profile');
    }

    public function logout()
    {
        if(request()->prefix__ == '/admin'){
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }else{
            Auth::guard('company_user')->logout();
            return redirect()->route('company_user.login');
        }
    }

    public function participants(Request $request){
        $i = 1;

        $programs = resolveAuthUser()->trainings()->whereHas('program', function ($query) {
            $query->where('program_lock', 0);
        })->pluck('program_id')->toArray();

        $users = User::withCount('programs')->orderBy('created_at', 'DESC')->whereHas('programs', function ($query) use ($programs) {
            $query->whereIn('program_id', $programs);
        });

        if (!empty($request->email)) {
            $users = $users->where('email', $request->email);
        }

        if (!empty($request->name)) {
            $users = $users->where('name', 'LIKE', "%{$request->name}%");
        }

        if (!empty($request->phone)) {
            $users = $users->where('phone', $request->phone);
        }

        $users = User::withCount('programs')->orderBy('created_at', 'DESC')->whereHas('programs', function ($query) use ($programs) {
            $query->whereIn('program_id', $programs);
        });

        if (!empty($request->program_id)) {
            $users = $users->whereHas('programs', function ($query) use ($request) {
                $query->where('program_user.program_id', $request->program_id);
            });
        }

        $records = $users->count();
        $users = $users->paginate(adminPaginationRecords());
        
        $allPrograms = Program::whereIn('id', $programs)->select('id', 'p_name', 'p_end', 'is_closed', 'created_at')->orderBy('created_at', 'DESC')->get();

        return view('dashboard.company.users.index', compact('users', 'i', 'records','programs', 'allPrograms'));

    }

    public function pretest()
    {
        $trainings = resolveAuthUser()->trainings()->whereHas('program', function ($query) {
            $query->where('program_lock', 0);
        })->pluck('program_id')->toArray();

        $programs = Program::whereIn('id', $trainings)->whereHas('mocks', function ($query) {
            return $query->orderby('created_at', 'DESC');
        })->orderby('created_at', 'DESC')->get();

        $i = 1;
        
        return view('dashboard.company.pretests.selecttraining', compact('programs', 'i'));
    }

    public function getgrades(Request $request, $id)
    {
        $mock = new  MockController();
        return $mock->getgrades($request, $id, true);
        $request->pid = $id;
    }

    public function postTest()
    {
        $trainings = resolveAuthUser()->trainings()->whereHas('program', function ($query) {
            $query->where('program_lock', 0);
        })->pluck('program_id')->toArray();
        
        $programs = Program::whereIn('id', $trainings)->whereHas('mocks', function ($query) {
            return $query->orderby('created_at', 'DESC');
        })->orderby('created_at', 'DESC')->get();

        $i = 1;

        return view('dashboard.company.posttests.selecttraining', compact('programs', 'i'));
    }

    public function getPostTesGrades(Request $request, $id)
    {
        $result = new ResultController();
        return $result->getgrades($request, $id, true);
        $request->pid = $id;
    }
}
