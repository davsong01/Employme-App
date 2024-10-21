<?php

namespace App\Http\Controllers\Company;

use App\Models\User;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\CompanyUser;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CompanyUserTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MockController;
use Rap2hpoutre\FastExcel\Facades\FastExcel;
use App\Http\Controllers\Admin\ResultController;


class CompanyUserController extends Controller
{
    public function showLoginForm()
    {
        return view('company_users.login');
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (Auth::guard('company_user')->attempt($credentials)) {
            $user = Auth::guard('company_user')->user();

            $user->update(['last_login' => now()]);

            return redirect()->route('company_user.dashboard');
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }


    public function dashboard()
    {
        $programs = auth()->user()->trainings()->whereHas('program', function ($query) {
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
        Auth::guard('company_user')->logout();
        return redirect()->route('company_user.login');
    }

    // public function index()
    // {
    //     $i = 1;

    //     $users = CompanyUser::select('id', 'name', 'email', 'created_at', 'phone', 'permissions', 'status')
    //         ->distinct()->with('trainings')
    //         ->orderBy('created_at', 'DESC')->get();
        
    //     if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
    //         return view('dashboard.admin.company.index', compact('users', 'i'));
    //     }
    // }

    public function participants(Request $request){
        $i = 1;

        $programs = auth()->user()->trainings()->whereHas('program', function ($query) {
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
            $users = $users->where('t_phone', $request->phone);
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
        $users = $users->paginate(50);
        
        $allPrograms = Program::whereIn('id', $programs)->select('id', 'p_name', 'p_end', 'close_registration', 'created_at')->orderBy('created_at', 'DESC')->get();

        return view('dashboard.company.users.index', compact('users', 'i', 'records','programs', 'allPrograms'));

    }

    public function pretest()
    {
        $trainings = auth()->user()->trainings()->whereHas('program', function ($query) {
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
        $trainings = auth()->user()->trainings()->whereHas('program', function ($query) {
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