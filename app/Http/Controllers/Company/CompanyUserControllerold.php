<?php

namespace App\Http\Controllers\Company;

use App\Program;
use App\Models\CompanyUser;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CompanyUserTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class CompanyUserControllerold extends Controller
{
    public function showLoginForm()
    {
        return view('company_users.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('company_user')->attempt($credentials)) {
            return redirect()->intended(route('company_user.dashboard'));
        }
    
        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function dashboard()
    {
        $programs = auth()->user()->trainings()->whereHas('program', function ($query) {
            $query->where('program_lock', 0);
        })->get();

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


    public function index()
    {
        $i = 1;

        $users = CompanyUser::select('id', 'name', 'email', 'created_at', 'phone', 'permissions', 'status')
            ->distinct()->with('trainings')
            ->orderBy('created_at', 'DESC')->get();
        
        // $users->map(function ($users) {
        //     $details = CompanyUserTraining::where('company_user_id', $users->id);
        //     $users->program_count = $details->distinct()->count();
        //     $transactions = DB::table('program_user')->where('facilitator_id', $users->id);
        //     $users->students_count = $transactions->count();
        //     $users->earnings = $transactions->sum('facilitator_earning');
        //     $users->image = (filter_var($users->profile_picture, FILTER_VALIDATE_URL) !== false) ? $users->profile_picture : url('/') . '/avatars' . '/' . $users->profile_picture;

        //     return $users;
        // });
        
        // foreach ($users as $user) {
        //     $names = [];
        //     foreach ($user->trainings as $trainings) {
        //         if (isset($trainings)) {
        //             $trainingp_name = Program::whereId($trainings->program_id)->value('p_name');
        //             array_push($names, $trainingp_name);
        //         } else;
        //     }

        //     $user->p_names =  $names;
        // }

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.company.index', compact('users', 'i'));
        }
    }
}