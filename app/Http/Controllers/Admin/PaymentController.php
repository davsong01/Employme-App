<?php

namespace App\Http\Controllers\Admin;
use PDF;
use App\Role;
use App\User;
use App\Program;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $transactions = DB::table('program_user')->orderBy('program_id', 'DESC')->get();

        foreach($transactions as $transaction){
            $transaction->details = User::select('name', 'email')->where('id', $transaction->user_id)->first(); 
            $transaction->program = Program::select('p_name')->where('id', $transaction->program_id)->first();
        }

        if(Auth::user()->role_id == "Admin"){
          return view('dashboard.admin.payments.index', compact('transactions', 'i') );

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

    public function edit($id){
            $transaction = DB::table('program_user')->whereId($id)->first();
            if(Auth::user()->role_id == "Admin"){
            return view('dashboard.admin.transactions.edit', compact('programs','user'));
    }return back();
    }

    public function show(Request $request, $id){
        $transaction = DB::table('program_user')->where('id', $id)->first();

        if(Auth::user()->role_id == "Admin"){
        //get user details
        $user = User::findorFail($transaction->user_id);

        if($transaction->t_amount == $user->programs[0]['e_amount']){
            $message = $this->dosubscript2($transaction->balance);
         }else{
        $message = $this->dosubscript1($user->balance);
         }

        //determine the program details
        $details = [
            'programFee' => $user->programs[0]['p_amount'],
            'programName' => $user->programs[0]['p_name'],
            'programAbbr' => $user->programs[0]['p_abbr'],
            'balance' => $transaction->balance,
            'message' => $message,
            'booking_form' => base_path() . '/uploads'.'/'. $user->program[0]['booking_form'],
            'invoice_id' =>  $transaction->invoice_id,
            'message' => $message,
        ];
  
        $data = [
            'name' =>$user->name,
            'email' =>$user->email,
            'bank' =>$user->t_type,
            'amount' =>$transaction->t_amount,
        ];
        //generate pdf from receipt view
        $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
        
        //send user mails
        // return view('emails.receipt', compact('data', 'details'));
        Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
        
        return back()->with('message', 'Receipt sent succesfully'); 
    }else return back();
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance){
        if($balance <= 0){
            return 'Full payment';
        }return 'Part payment';
    }
    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }return 0;
    }
    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }return 'Part payment';
    }

}