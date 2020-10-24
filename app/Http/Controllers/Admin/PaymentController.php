<?php

namespace App\Http\Controllers\Admin;
use DB;
use PDF;
use App\Pop;
use App\Role;
use App\User;
use App\Program;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
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

        if(Auth::user()->role_id == "Admin"){
            $transactions = DB::table('program_user')->orderBy('id', 'ASC')->get();

            $pops = Pop::with('program')->Ordered('date', 'DESC')->get();

            $i = 1;

            foreach($transactions as $transaction){
                $transaction->name = User::where('id', $transaction->user_id)->value('name'); 
                $transaction->email = User::where('id', $transaction->user_id)->value('email'); 
                $transaction->program = Program::select('p_name')->where('id', $transaction->program_id)->first();
            }
// dd($transactions);
        
          return view('dashboard.admin.payments.index', compact('transactions', 'i', 'pops') );

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
            $transaction->name = User::whereId($transaction->user_id)->value('name');
            $program_details = Program::select('p_name', 'p_amount')->whereId($transaction->program_id)->first();
            $transaction->p_name = $program_details->p_name;
            $transaction->p_amount = $program_details->p_amount;

            if(Auth::user()->role_id == "Admin"){
            return view('dashboard.admin.transactions.edit', compact('transaction'));
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

    public function printReceipt($id){
        $transaction = DB::table('program_user')->where('id', $id)->first();
      
        if(Auth::user()->role_id == "Student"){
            if(!$transaction){
                return back()->with('warning', 'Unauthorized Action'); 
            }
            if($transaction->user_id <> auth()->user()->id){
                return back()->with('warning', 'Unauthorized Action');
            }
        }
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
            
            return view('emails.printreceipt', compact('data', 'details'));

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

      public function update(Request $request, $id){

        $transaction = DB::table('program_user')->whereId($id)->first();
       
        $user = User::findorFail($transaction->user_id);
     
        //check amount against payment
        $programFee = $request->program_amount;

        $newamount = $transaction->t_amount + $request->amount;
        if($newamount > $programFee){
            return back()->with('warning', 'Student cannot pay more than program fee');
        }else 

        $balance = $programFee - $newamount;
        $message = $this->dosubscript1($balance);
        $paymentStatus =  $this->paymentStatus($balance);
        
        //update the program table here @ column fully paid or partly paid
        

        DB::table('program_user')->whereId($transaction->id)->update([
            't_amount' => $newamount,
           'balance' => $balance,
            't_type' => $request['bank'],
            't_location' => $request['location'],
            'transid' => $request['transaction_id'],
            'paymentStatus' =>  $paymentStatus,
        ]);

        return back()->with('message', 'Transaction updated successfully');
    }

    public function destroy($id){
        $transaction = DB::table('program_user')->whereId($id)->delete();
        
        return back()->with('message', 'Transaction has been deleted forever');
        
    }

}