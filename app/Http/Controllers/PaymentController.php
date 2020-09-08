<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Paystack;
use App\Program;
use App\Http\Requests;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        //dd($request->all());
        //Get Training ID and price sent
        $pid = json_decode($request['metadata'], true);
        $pid = $pid['pid'];
        $amount = $request->amount/100;
        
        //Find corresponding Training
        $program = Program::findorFail($pid);

        //check if sent price is same as real price or earlybird or half paymet
        if($amount <> $program->p_amount && $amount <> $program->e_amount && $amount <> ($program->p_amount/2)){
            return Redirect::back()->with('msg', 'No match found for payment type');
        }
        
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->with('msg', 'The paystack token has expired. Please refresh the page and try again.');
        }  
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        if($paymentDetails['data']['status'] === 'success'){

            $training = Program::where('id', $paymentDetails['data']['metadata']['pid'])->first();
       
            //Get Training Details
            $programFee = $training->p_amount;
            $programName = $training->p_name;
            $programAbbr = $training->p_abbr;
            $bookingForm = $training->booking_form;
            $programEarlyBird = $training->e_amount;
            $invoice_id = 'Invoice'.rand(10, 100);

            //Create User details
            $name = $paymentDetails['data'] ['metadata']['name'];
            $email = $paymentDetails['data']['customer']['email'];
            $phone = $paymentDetails['data'] ['metadata']['phone'];
            $password = bcrypt('12345');
            $program_id = $paymentDetails['data']['metadata']['pid'];
            $amount = $paymentDetails['data']['amount']/100;
            $t_type = "PAYSTACK";

            if(isset($paymentDetails['data']['metadata']['location'])){
                $location = $paymentDetails['data']['metadata']['location']; 
            }else $location = ' ' ;
            $role_id = "Student";
            $transid = $paymentDetails['data']['reference'];
            
            //Check if user email exists for the program id and redirect them to login if yes
            // $user_email = User::where('email', $email)->first();
            // $user = DB::table('program_user')->where('program_id', $program_id)->first();
            // // dd($user_email, $user, $email);
            // if($user){
            //     if($user_email == $email && $user->program_id == $program_id){
            //         return redirect(route('home'));
            //     }
            // }

            //check amount against payment
            if($amount == $programEarlyBird){
                $balance = $programEarlyBird - $amount;
                $message = $this->dosubscript2($balance);
                $payment_type = 'EB';
            }else{
            $balance = $programFee - $amount;
            $message = $this->dosubscript1($balance);
            $payment_type = 'Full';
            }
            
            $paymentStatus =  $this->paymentStatus($balance);

            // check if email exists in the system and attach it to the new pregram to that email
            $user = User::where('email', $email)->first();
            if(!$user){
                //save to database
                $user = User::updateOrCreate([
                    'name' => $name,
                    'email' => $email,
                    't_phone' => $phone,
                    'password' => $password,
                    'role_id' => $role_id,
                ]);
                
            }
            
            $user->programs()->attach($program_id, [
                    'created_at' =>  date("Y-m-d H:i:s"),
                    't_amount' => $amount,
                    't_type' => $t_type,
                    't_location' => $location,
                    'transid' => $transid,
                    'paymenttype' => $payment_type,
                    'paymentStatus' => $paymentStatus,
                    'balance' => $balance,
                    'invoice_id' =>  $invoice_id,
                ] );

            //send email
            $details = [
                'programFee' => $programFee,
                'programName' => $programName,
                'programAbbr' => $programAbbr,
                'balance' => $balance,
                'message' => $message,
                'booking_form' => base_path() . '/uploads'.'/'.$bookingForm,
                'invoice_id' =>  $invoice_id,

            ];
      
            $data = [
                'name' =>$name,
                'email' =>$email,
                'bank' =>$t_type,
                'amount' =>$amount,
            ];

  
            $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            // return view('emails.receipt', compact('data', 'details'));
            Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
                
            //include thankyou page
            return view('emails.thankyou', compact('data',  'details'));

        }dd('Transaction failed! We have not received any money from you.');
     
    }

    private function process($paymentDetails){
        
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance){

        if($balance <= 0){
            return 'Full payment';
        }else return 'Part payment';
    }

    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }else return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }else return 'Part payment';
    }
}