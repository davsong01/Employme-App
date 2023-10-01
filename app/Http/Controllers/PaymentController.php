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
use App\PaymentMode;
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
        $modes = null;
        $location = $request->location ?? null;
        if($request->has('modes')){
            // Get mode amount 
            $modes = $request->modes;
            $amount = $this->getModeAmount($request->modes,$request->type,$request->training);
            if(!$amount){
                return back()->with('error', 'Invalid Amount');
            }
        }else{
            if($request->type == 'full'){
                $amount = $training['p_amount'];
            }elseif($request->type == 'part'){
                $amount = $training['p_amount'] /2;
            }elseif($request->type == 'earlybird'){
                $amount = $training['e_amount'];
            }else{
                return back()->with('error', 'Invalid Payment Type selection');
            }
        }
        
        $type = $request->type;
     
        // inject facilitator details
        if($request->has('facilitator')){
            Session::put('facilitator', $request->facilitator);
            Session::put('facilitator_id', $request->facilitator_id);
            Session::put('facilitator_name', $request->facilitator_name);
            Session::put('facilitator_license', $request->facilitator_license);
        }

        $payment_modes = $this->getPaymentModes();
        
        return view('checkout', compact('amount', 'training', 'type', 'payment_modes','modes','location'));
    }

    public function getModeAmount($mode,$type,$program){
        $amount = null;
        $program = json_decode($program);
        if(isset($program->show_modes) && $program->show_modes == 'yes'){
            $modes = json_decode($program->modes);
        }
        
        if(!empty($modes)){
            // check type
            if($type == 'full'){
                $amount = $modes->$mode;
            }
            if($type == 'part'){
                $amount = $modes->$mode/2;
            }
        }

        return $amount;
       
    }


    public function getPaymentModes(){
        $payment_modes = PaymentMode::where('status', 'active')->get();
        if(Session::has('facilitator_id')){
            $mode_id = User::where('id', Session::get('facilitator_id'))->first()->payment_mode;
            if(isset($mode_id) && !empty($mode_id)){
                $modes = PaymentMode::where('id', $mode_id)->where('status', 'active')->get();
                if (isset($modes) && !empty($modes)) {
                    // $admins->merge($users)
                    $payment_modes = $modes;
                }
            }
        }
        return $payment_modes;
    }

    public function validateCoupon(Request $request){
       
        $verifyCoupon = $this->getCouponValue($request->code, $request->pid);
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
        
        if ($request->user_program && $request->type == 'balance') {
            $data = DB::table('program_user')->find($request->user_program);

            $request['amount'] = $data->balance;
            $request['payment_mode'] = $data->payment_mode;
            $request['currency'] = $data->currency;
            $type['type'] = 'balance';
            
            try {
                $url = $this->queryProcessor($request, $data);
                
                if($url){
                    return redirect()->away($url);
                }else{
                    return back()->with('error', 'Something went wrong, Kindly try a different payment method!');
                }
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
                return abort(500);
            }
        }
        
        $this->validate(request(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'quantity' => 'required|numeric',
            'currency' => 'required|string',
            "amount"=>'required',
            "coupon"=>'sometimes',
            "metadata"=>'sometimes',
            "payment_mode"=>'required',
        ]);
       
        if($template == 'contai'){
            $request['amount'] = \Session::get('exchange_rate') * $request['amount'];
            
            $type = json_decode($request['metadata'], true);
          
            $pid = $type['pid'];
            $coupon_id = $type['coupon_id'];
            $facilitator_id = $type['facilitator'];
            $request['payment_type'] = $type['type'];
            $type['name'] = $request['name'];
            $type['phone'] = $request['phone'];
            $training = Program::where('id', $type['pid'])->first();
            $response = $this->verifyCoupon($request, $type['pid']);
           
            if(is_null($response)){
                // Modify amount to suit program
                if($request->has('modes')){
                    // Get mode amount 
                    $modes = $request->modes;
                    $amount = $this->getModeAmount($request->modes,$request->payment_type,$training);
                }else{
                    if ($type['type'] == 'full') {
                        $request['amount'] = $training->p_amount;
                    }

                    if($type['type'] == 'earlybird'){
                        $request['amount'] = $training->e_amount;
                    }

                    if ($type['type'] == 'part') {
                        $request['amount'] = ($training->p_amount) / 2;
                    }
                }
            }else{
                // Modify coupon_id in metadata
                $type['coupon_id'] = $response['id'];
                $coupon_id = $response['id'];

                if($response['grand_total'] <= 0){
                    $request->request->add(['reference' => $request->reference]);
                    $request['transid'] = $this->getReference('PYSTK');

                    $metadata = json_decode($request->metadata, true);
                    $metadata['coupon_id'] = $response['id'];
                    $request['reference'] = $request['transid'];
                    $request['metadata'] = $metadata;
                    // Create temp user
                    $tempDetails = app('app\Http\Controllers\Controller')->createTempDetails($request, $request->payment_mode);
                    
                    // handle gateway call back normally
                    $req = new \App\Http\Controllers\PaymentController();
                    $response = $req->handleGatewayCallback($request, 'zero-amount');
                    if(Auth::user()){
                        return redirect(url('/dashboard'));
                    }
                }

            }

            // Pay with Transfer
            if($request->has('payment_mode') && $request->payment_mode == 0){
                $request->request->add(['reference' => $request->reference]);
                $request['transid'] = 'BT-'.rand(11111111,9999999);
                $metadata = json_decode($request->metadata, true);
                $metadata['coupon_id'] = $response['id'] ?? null;
                $request['metadata'] = $metadata;
                $tempDetails = app('app\Http\Controllers\Controller')->createTempDetails($request, $request->payment_mode);
                $data = $request->all();
                
                \Session::put('data', $data);
                return redirect('pop/create');
                
            }

            // Free training
            if($request->payment_type == 'full' && $training->p_amount == 0){
                $data = $this->prepareFreeTrainingDetails($training, $request);
                // $data['balance'] = $balance;
                // $data['payment_type'] = $payment_type;
                $data['payment_type'] = 'Full';
                $data['message'] = 'Full payment';
                $data['paymentStatus'] = 1;
                $data['currency_symbol'] = '&#x20A6;';
                $data['balance'] = 0;

                // $c = $c ?? NULL; // Coupon
                $data = $this->createUserAndAttachProgramAndUpdateEarnings($data, []);
                // $this->deleteFromTemp($temp);
               
                $this->sendWelcomeMail($data);
                
                // Login User in
                Auth::loginUsingId($data['user_id']);
                return view('thankyou', compact('data'));

            }
           
            // Create temp user and redirect
            $request['metadata'] = $type;
           
            try{
                $url = $this->queryProcessor($request);
                if(!is_null($url)){
                    return redirect()->away($url);
                }else{
                    return redirect(url('trainings/' . $pid))->with('error', 'Something went wrong, Kindly try again!');
                }
            }catch(\Exception $e) {
                \Log::info($e->getMessage());
                
                return redirect(url('trainings/' . $pid))->with('error', 'Something went wrong, Kindly try a different payment method!');
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

    public function queryProcessor($request,$data=null){
        $mode = PaymentMode::find($request->payment_mode);
       
        if(isset($mode) && !empty($mode)){
            if($mode->processor == 'paystack'){
                $url = app('App\Http\Controllers\PaymentProcessor\PaystackController')->query($request,$mode, $data);
            }

            if ($mode->processor == 'coinbase') {
                $url = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->query($request, $mode, $data);
            }
        }
        // redirect away
        return $url;
        
    }
    
    public function verifyProcessor($reference, $temp){
        $mode = PaymentMode::find($temp->payment_mode);
       
        if (isset($mode) && !empty($mode)) {
            if ($mode->processor == 'paystack') {
                $status = app('App\Http\Controllers\PaymentProcessor\PaystackController')->verify($reference, $mode);
            }
            if ($mode->processor == 'coinbase') {
                $status = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->verify($reference, $mode, $temp);
            }
        }
        
        if (isset($status) && $status == 'success') {
            return $status;
        }else{
            return back()->with('error', 'Payment was not succusful');
        }
    }

    public function handleGatewayCallback(Request $request, $is_zero_coupon=null)
    {
        $balance_payment = DB::table('program_user')->whereNotNull('balance_transaction_id')->where('balance_transaction_id', $request->reference)->first();

        if($balance_payment){
            //process as balance
            $status = $this->verifyProcessor($request->reference, $balance_payment);
        }else{
            $temp = TempTransaction::where('transid', $request->reference)->first();
            
            if (!$temp) {
                return redirect(route('home'));
            } else {
                if(is_null($is_zero_coupon)){
                    $status = $this->verifyProcessor($request->reference, $temp);
                }else{
                    $status = 'success';
                }
            }
        }
        if($status == 'success'){
            if ($balance_payment) {
                $data['type'] = 'balance';
               
                $data['currency_symbol'] = $balance_payment->currency_symbol;
                $data['amount'] = $balance_payment->balance;
                $data['email'] = User::whereId($balance_payment->user_id)->value('email');
                $data['programName'] = Program::where('id', $balance_payment->program_id)->value('p_name');
                // Do facilitator stuff here later
                DB::table('program_user')->where('balance_transaction_id', $request->reference)->update([
                    'balance' => 0,
                    'paymentStatus' => 1,
                    'paymentType' => 'Full',
                    't_amount' => $balance_payment->t_amount + $balance_payment->balance,
                    'balance_paid' => $balance_payment->balance,
                    'balance_amount_paid' => now(),
                ]);
               
                $this->sendWelcomeMail($data);

                return redirect(route('trainings.show', ['p_id' => $balance_payment->program_id]))->with('message','Balance payment received!');

            }else{
                $paymentDetails = $temp;
            }
        }
        
        $template = Settings::first()->templateName->name;
        $program = Program::where('id', $paymentDetails->program_id)->first();
        
        if($template == 'contai'){
            // $temp = TempTransaction::where('email', $paymentDetails->email)->where('program_id', $paymentDetails->program_id)->first();
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
                   
                    if(isset($temp->training_mode) && !empty($temp->training_mode)){
                        $mode_amount = $this->getModeAmount($temp->training_mode,$temp->type,$program);

                        if($mode_amount) $expectedAmount = $mode_amount - $coupon;
                    }else{
                        $expectedAmount = $this->confirmProgramAmount($temp->program_id, 'p_amount') - $coupon;
                    }
                  
                    if($expectedAmount == $paymentDetails->amount){
                        $earnings = $this->getEarnings(($temp->amount), $coupon, $createdBy, $program, $paymentDetails->facilitator_id ?? NULL);
                        
                        $balance = 0;
                        $payment_type = 'Full';
                        $message = 'Full payment';
                        $coupon_applied = $c ?? NULL;
                        $paymentStatus =  1;                        
                    }
                }elseif($temp->type == 'part'){
                    if(isset($temp->training_mode) && !empty($temp->training_mode)){
                        $mode_amount = $this->getModeAmount($temp->training_mode,$temp->type,$program);

                        if($mode_amount){
                            $expectedAmount = $mode_amount;
                            
                            $modes = $program->modes;
                            $modes = json_decode($modes, true);
                            // dd($temp->training_mode);
                            $amt = $modes[$temp->training_mode];
                            
                            $balance = $amt - $expectedAmount;

                        }
                    }else{
                        $expectedAmount = ($this->confirmProgramAmount($temp->program_id, 'p_amount')/2);

                        $balance = $program->p_amount - $expectedAmount;
                    }

                    $payment_type = 'Part';
                    $message = 'Part payment';
                    $coupon_applied = $c ?? NULL;
                    $paymentStatus =  1;
                    $earnings = $this->getEarnings(($temp->amount), NULL, '', $program, $paymentDetails->facilitator_id);
                    
                }elseif($temp->type == 'earlybird'){
                    $balance = 0;
                    $payment_type = 'Full';
                    $message = 'Earlybird payment';
                    $paymentStatus =  1;
                    $earnings = $this->getEarnings(($temp->amount), NULL, '', $program, $paymentDetails->facilitator_id);

                }elseif($temp->type == 'balance'){
                // Do nothing, something must have gone wrong
                }
                // process data
                // Get training details
                $data = $this->prepareTrainingDetails($program, $paymentDetails,$paymentDetails->amount);
               
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