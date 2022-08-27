<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Coupon;
use App\Program;
use App\Settings;
use App\CouponUser;
use App\Http\Requests;
use App\TempTransaction;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{

    public function checkout(Request $request){
        $training = json_decode($request->training, true);
       
        if($request->type == 'full'){
            $amount = $training['p_amount'];
        }elseif($request->type == 'part'){
            $amount = $training['p_amount'] /2;
        }elseif($request->type == 'earlybird'){
            $amount = $training['e_amount'];
        }else{
            return back()->with('error', 'Invalid Payment Type selection');
        }

        $type = $request->type;
        // inject facilitator details
        if($request->has('facilitator')){
            Session::put('facilitator', $request->facilitator);
            Session::put('facilitator_id', $request->facilitator_id);
            Session::put('facilitator_name', $request->facilitator_name);
            Session::put('facilitator_license', $request->facilitator_license);
        }

        // dd(Session::get('facilitator'));
       
        return view('checkout', compact('amount', 'training', 'type'));
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
        
        $this->validate(request(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'quantity' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        if($template == 'contai'){
            $request['amount'] = \Session::get('exchange_rate') * $request['amount'] * 100;
            $type = json_decode($request['metadata'], true);
            
            $pid = $type['pid'];
            $coupon_id = $type['coupon_id'];
            $facilitator_id = $type['facilitator'];
            $payment_type = $type['type'];
            $type['name'] = $request['name'];
            $type['phone'] = $request['phone'];
            
            $response = $this->verifyCoupon($request, $type['pid']);
           
            if(is_null($response)){
                // Modify amount to suit program
                if ($type['type'] == 'full') {
                    $request['amount'] = Program::where('id', $type['pid'])->value('p_amount') * 100;
                }

                if($type['type'] == 'earlybird'){
                    $request['amount'] = Program::where('id', $type['pid'])->value('e_amount') * 100;
                }

                if ($type['type'] == 'part') {
                    $request['amount'] = (Program::where('id', $type['pid'])->value('p_amount') * 100) / 2;
                }
                
                // $request['amount'] *= Session::get('exchange_rate');
             
            }else{
                // Modify coupon_id in metadata
                $type['coupon_id'] = $response['id'];
                $coupon_id = $response['id'];
            }

            $type = json_encode($type);
            
            if(isset($facilitator_id) && !empty($facilitator_id)){
                // Set payment mode if money is going somewhere else
                try {
                    $payment_mode = User::with('payment_modes')->where('id', $facilitator_id)->first();
                   
                    if (isset($payment_mode->payment_modes) &&  !empty($payment_mode->payment_modes)) {
                        $public = $payment_mode->payment_modes->public_key;
                        $secret = $payment_mode->payment_modes->secret_key;
                        $email = $payment_mode->payment_modes->merchant_email;
                      
                        Config::set('paystack.publicKey', $public);
                        Config::set('paystack.secretKey', $secret);
                        Config::set('paystack.merchantEmail', $email);
                    }

                } catch (\Exception $th) {
                   \Log::error($th->getMessage());
                }
                
            }
           
            $request['metadata'] = $type;
            $this->createTempDetails($request, $payment_type, $pid, $coupon_id, $facilitator_id);
           
            try{
                return Paystack::getAuthorizationUrl()->redirectNow();
            }catch(\Exception $e) {
                \Log::info($e->getMessage());
                return abort(500);
            } 

        }
        
        if($template == 'default'){
        
            if(isset($type['type']) && $type['type'] == 'balance'){
            
                //Check if userid tallys with trainingid
                $check = DB::table('program_user')->whereUserId($type['user_id'])->whereProgramId($type['pid'])->first();
                
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
              
                \Log::info($e->getMessage());
                return abort(500);
                // return Redirect::back()->with('error', 'The paystack token has expired. Please refresh the page and try again'); 
            }  
        }
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        $paymentDetails['data']['amount'] = $paymentDetails['data']['amount']/100;
        $template = Settings::first()->templateName->name;
        $program = Program::where('id', $paymentDetails['data']['metadata']['pid'])->first();

        if($template == 'contai'){
            $temp = TempTransaction::where('email', $paymentDetails['data']['customer']['email'])->where('program_id', $paymentDetails['data']['metadata']['pid'])->first();
            if(isset($temp) && !empty($temp)){
              
                // Compare
                if($temp->type == 'full'){
                    // Sort coupon
                    if(isset($temp->coupon_id)){
                        $c = Coupon::where('id', $temp->coupon_id)->first();
                        $coupon = $c->amount;
                        $createdBy = $c->facilitator_id;
                    }else{
                        $coupon = 0;
                        $createdBy = 0;
                    }

                    $expectedAmount = $this->confirmProgramAmount($temp->program_id, 'p_amount') - $coupon;
                  
                    if($expectedAmount == $paymentDetails['data']['amount']){
                        $earnings = $this->getEarnings(($temp->amount/100), $coupon, $createdBy, $program, $paymentDetails['data']['metadata']['facilitator'] ?? NULL);
                        
                        $balance = 0;
                        $payment_type = 'Full';
                        $message = 'Full payment';
                        $coupon_applied = $c ?? NULL;
                        $paymentStatus =  1;                        
                    }
                }elseif($temp->type == 'part'){
                    $expectedAmount = ($this->confirmProgramAmount($temp->program_id, 'p_amount')/2);
                    $balance = $program->p_amount -  $expectedAmount;
                    $payment_type = 'Part';
                    $message = 'Part payment';
                    $coupon_applied = $c ?? NULL;
                    $paymentStatus =  1;
                    $earnings = $this->getEarnings(($temp->amount/100), NULL, '', $program, $paymentDetails['data']['metadata']['facilitator']);
                    
                }elseif($temp->type == 'earlybird'){
                    $balance = 0;
                    $payment_type = 'Full';
                    $message = 'Earlybird payment';
                    $paymentStatus =  1;
                    $earnings = $this->getEarnings(($temp->amount / 100), NULL, '', $program, $paymentDetails['data']['metadata']['facilitator']);

                }elseif($temp->type == 'balance'){
                // Do nothing, something must have gone wrong
                }
               
                // process data
                // Get training details
                $data = $this->prepareTrainingDetails($program, $paymentDetails,$paymentDetails['data']['amount']);
               
                $data['balance'] = $balance;
                $data['payment_type'] = $payment_type;
                $data['message'] = $message;
                $data['paymentStatus'] =  $paymentStatus;
                $c = $c ?? NULL; // Coupon
               
                $data = $this->createUserAndAttachProgramAndUpdateEarnings($data, $earnings, $c);
               
                if(isset($c) && !empty($c)){
                    $this->updateCoupon($c->id, $data['email'], $data['program_id']);
                } 
                $this->deleteFromTemp($temp);
                $data['currency'] = \Session::get('currency');
                $data['currency_symbol'] = \Session::get('currency_symbol');
                $data['exchange_rate'] = \Session::get('exchange_rate');
                $this->sendWelcomeMail($data);

                // Login User in
                Auth::loginUsingId($data['user_id']);

                // $data = [
                //     "programFee" => 25000,
                //     "programName" => "Microsoft & Accounting Applications Master Class 2020(Lagos)",
                //     "programAbbr" => "#MAAT2020L",
                //     "bookingForm" => "bookingforms/MAAT2020 forms.pdf",
                //     "invoice_id" => "Invoice874",
                //     "name" => "asas",
                //     "email" => "ass@adasad.com",
                //     "phone" => "23232323",
                //     "password" => "$2y$10$MT17dVxZ0A8B.1LwTuM8YuJ4tI/sbcochC6hSTrhpT7ZXZl2HxN1.",
                //     "program_id" => 8,
                //     "amount" => 25000,
                //     "t_type" => "PAYSTACK",
                //     "location" => " ",
                //     "role_id" => "Student",
                //     "transid" => "87UYe0GnRRukil9zbQwEYo3UA",
                //     "balance" => 0,
                //     "payment_type" => "Full",
                //     "message" => "Full payment",
                //     "paymentStatus" => 1,
                //     "facilitator_id" => null,
                //     "coupon_amount" => null,
                //     "coupon_id" => null,
                //     "coupon_code" => null,
                //     "booking_form" => "C:\xampp\htdocs\Laravel Projects\Employme-App/uploads/bookingforms/MAAT2020 forms.pdf",
                //     "admin_earning" => 6250.0,
                //     "facilitator_earning" => 0,
                //     "tech_earning" => 6250.0,
                //     "faculty_earning" => 6250.0,
                //     "other_earning" => 0.0,
                //     "user_id" => 477
                // ]

                //include thankyou page
  
                return view('thankyou', compact('data'));

            }
            
            return redirect(route('welcome'));
            // dd($paymentDetails, 'as');
            // Compare details with details in temp table
        }
       
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