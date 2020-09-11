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
        $users = DB::table('program_user')->orderBy('program_id', 'DESC')->get();

        foreach($users as $user){
            $user->details = User::select('name', 'email')->where('id', $user->user_id)->first(); 
            $user->program = Program::select('p_name')->first();
        }
        

        if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.payments.index', compact('users', 'i') );

        }
        if(Auth::user()->role_id == "Teacher" || Auth::user()->role_id == "Grader"){
            return back();
        }
        if(Auth::user()->role_id == "Student"){  

            $transactiondetails = DB::table('program_user')->where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
            foreach($transactiondetails as $details){
                $details->programs = Program::select('p_name', 'p_amount')->where('id', $details->program_id)->get()->toArray();
                $details->p_name = $details->programs[0]['p_name'];
                $details->p_amount = $details->programs[0]['p_amount'];
            }
            // dd($transactiondetails->balance);
            return view('dashboard.student.payments.index', compact('transactiondetails'));
        }
    }
}
   