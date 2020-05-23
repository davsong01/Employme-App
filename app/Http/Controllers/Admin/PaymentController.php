<?php

namespace App\Http\Controllers\Admin;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcomemail;
use App\Role;
use App\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $i = 1;
        //$users = User::all();
        $users = User::where('role_id', '=', "Student")->orderBy('updated_at', 'DESC')->get();
        $programs = Program::all();
       if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.payments.index', compact('users', 'i', 'programs') );

        }
        if(Auth::user()->role_id == "Teacher"){
            return back();
        }
        if(Auth::user()->role_id == "Student"){          
            $users = User::where('email', '=', Auth::user()->email)->orderBy('updated_at', 'DESC')->get();
            return view('dashboard.student.payments.index', compact('users'));
            return back();
        }
    }
}
   