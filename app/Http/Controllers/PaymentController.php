<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Coupon;
use App\Program;
use App\Settings;
use App\CouponUser;
use App\Http\Requests;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{

    public function checkout(Request $request){

        $amount = $request->amount;

        // inject facilitator details
        if($request->has('facilitator')){
            Session::put('facilitator', $request->facilitator);
            Session::put('facilitator_id', $request->facilitator_id);
            Session::put('facilitator_name', $request->facilitator_name);
            Session::put('facilitator_license', $request->facilitator_license);
        }

        $training = json_decode($request->training, true);
        
        return view('checkout', compact('amount', 'training'));
 
    }

    public function validateCoupon(Request $request){
        $verifyCoupon = $this->getCouponValue($request->code);
        $response = null;
        if(!is_null($verifyCoupon)){
            $response = $this->getCouponUsage($request->code, $request->email, $request->pid, $request->price);
        }
     
        return response()->json($response);
    }
    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
    
        $template = Settings::first()->templateName->name;
            
        if($template == 'contai'){
            $request['amount'] = $request['amount'] * 100;
            $type = json_decode($request['metadata'], true);
            if($request->coupon && !empty($request->coupon)){
                $verifyCoupon = $this->getCouponValue($request->coupon);

                if(!is_null($verifyCoupon)){
                    $response = $this->getCouponUsage($request->coupon, $request->email, $type['pid'], 2);
                }else{
                    $response = null;
                }

                if(is_null($response)){
                    // Modify amount to suit program
                    $request['amount'] = Program::where('id', $type['pid'])->value('p_amount') * 100;
                }else{
                    // Modify coupon_id in metadata
                    $type['coupon_id'] = $response['id'];
                    $type = json_encode($type);
                    $request['metadata'] = $type;
                }
            }

            try{
                return Paystack::getAuthorizationUrl()->redirectNow();
            }catch(\Exception $e) {
                dd($e->getMessage());
                \Log::info($e->getMessage());
                return abort(500);
            } 

        }
        
        if($template == 'default'){
        
            if(isset($type['type']) && $type['type'] == 'balance'){
            
                //Check if userid tallys with trainingid
                $check = DB::table('program_user')->whereUserId($type['user_id'])->whereProgramId($type['p_id'])->first();
                
                if(($check->balance * 100) == $request->amount){
                    try{
                        return Paystack::getAuthorizationUrl()->redirectNow();
                    }catch(\Exception $e) {
                        return Redirect::back()->with('error', 'The paystack token has expired. Please refresh the page and try again');
                    }  
                }else return Redirect::back()->with('error', 'Invalid Transaction');
            }

            
            
            try{
                return Paystack::getAuthorizationUrl()->redirectNow();
            }catch(\Exception $e) {
                dd($e->getMessage());
                \Log::info($e->getMessage());
                return abort(500);
                // return Redirect::back()->with('error', 'The paystack token has expired. Please refresh the page and try again'); 
            }  
        }
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
      
        if($paymentDetails['data']['status'] === 'success'){

            if(isset($paymentDetails['data']['metadata']['type']) && $paymentDetails['data']['metadata']['type'] == 'balance'){
                //Get training details details
                $training = Program::where('id', $paymentDetails['data']['metadata']['p_id'])->first();
                $programFee = $training->p_amount;
                $programName = $training->p_name;
                $programAbbr = $training->p_abbr;
                $invoice_id = 'Invoice'.rand(10, 100);

                //Get Transaction details
                $amount = $paymentDetails['data']['amount']/100;
                $t_type = "PAYSTACK";

                //Get User details
                $user = DB::table('program_user')->where('user_id', $paymentDetails['data']['metadata']['user_id'])->where('program_id', $training->id)->first();
                $balance = $amount - $user->balance;
                $message = $this->dosubscript1($balance);
                // dd($user->program_id);
                $attributes = [
                    't_amount' => $user->t_amount + $amount,
                    'balance' => $balance,
                    'invoice_id' => $invoice_id,
                    'paymentStatus' => $this->paymentStatus($balance),
                    't_type' => $t_type,
                    'transid' => $paymentDetails['data']['reference'],
                    'updated_at' => now()
                ];

                $training->users()->updateExistingPivot($paymentDetails['data']['metadata']['user_id'], $attributes);

                //Send Notification
                $details = [
                    'programFee' => $programFee,
                    'programName' => $programName,
                    'programAbbr' => $programAbbr,
                    'balance' => $balance,
                    'message' => 'Full Payment',
                    'invoice_id' =>  $invoice_id,
                ];
      
                $data = [
                    'name' => $paymentDetails['data'] ['metadata']['name'],
                    'email' => $paymentDetails['data']['customer']['email'],
                    'bank' => $t_type,
                    'amount' => $user->t_amount + $amount,
                    'type'=> 'balance',
                    'amount' => $amount,
                ];
                
              
                $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
                // return view('emails.receipt', compact('data', 'details'));
                Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
                    
                //include thankyou page
                return redirect(route('trainings.show', $training->id))->with('message', 'Balance Payment Succesful');

            }else
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

            //Check if email exists in the system and attach it to the new program to that email
            $user = User::where('email', $email)->first();
            // if user doesnt exist, create new user and attach program
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
            //If program id is not in array of user program, attach program
            $userPrograms = DB::table('program_user')->where('user_id', $user->id)->where('program_id', $program_id)->count();

            if( $userPrograms < 1){
                // Attach program
                $this->attachProgram($user, $program_id, $amount, $t_type, $location, $transid, $payment_type, $paymentStatus, $balance, $invoice_id);
               
                //prepare and send email
                $details = [
                    'programFee' => $programFee,
                    'programName' => $programName,
                    'programAbbr' => $programAbbr,
                    'balance' => $balance,
                    'message' => $message,
                    'booking_form' => !is_null($bookingForm) ? base_path() . '/uploads'.'/'. $bookingForm : null,
                    'invoice_id' =>  $invoice_id,
                ];
        
                $data = [
                    'name' =>$name,
                    'email' =>$email,
                    'bank' =>$t_type,
                    'amount' =>$amount,
                ];

                $this->sendWelcomeMail($details, $data);
                
                //include thankyou page
                return view('emails.thankyou', compact('data',  'details'));

            }else{
                dd('Duplicate transaction detected, please check your email for login instructions. You may close this tab now');
            }
        
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