<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Coupon;
use App\Program;
use App\Settings;
use App\CouponUser;
use App\TempTransaction;
use App\Mail\Welcomemail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $check = 98;

    protected function getReference($prefix){
        date_default_timezone_set("Africa/Lagos");
        return $prefix.'-'.date('YmdHi') . '-' . rand(11111111, 99999999);
    } 

    protected function sendWelcomeMail($data){
        set_time_limit(360);
        try {
            if(isset($data['invoice_id'])){
                $pdf = PDF::loadView('emails.receipt', compact('data'));
            }else $pdf = null;
            Mail::to($data['email'])->send(new Welcomemail($data, $pdf));
        } catch(\Exception $e){
            // Get error here
            Log::error($e);

            return false;
        }
        return;
        // return view('emails.receipt', compact('data'));
    }

    protected function attachProgram($user, $program_id, $amount, $t_type, $location, $transid, $payment_type, $paymentStatus, $balance, $invoice_id){
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
    }
   
    public function creditFacilitator($facilitator, $program){
        $facilitator->update([
            'earnings' => $facilitator->earnings + $facilitator->earning_per_head,
        ]);
        
        return;
    }

    public function getCouponUsage($code, $email, $pid, $price){
        
        $coupon = Coupon::where('code', $code)->first();
        
        if(isset($coupon) && !empty($coupon)){
            $usage = CouponUser::where('coupon_id', $coupon->id)->where('email', $email)->first();
            
            if(isset($usage)){
                if($usage->status == 1){
                    return NULL;
                }
                
            }else{
                CouponUser::Create([
                    'email' => $email,
                    'coupon_id' => $coupon->id,
                    'status' => 0,
                    'program_id' => $pid,
                ]);
            }
            
            $coupon_amount = $coupon->amount;
            $coupon_id = $coupon->id;
            $coupon_code = $coupon->code;

            return [
                'amount' => $coupon_amount,
                'id' => $coupon_id,
                'code' => $coupon_code,
                'grand_total' => $price -$coupon_amount,
            ];
        }

        return null;
        
    }

    public function getCouponValue($code){
        $coupon = Coupon::where('code', $code)->first();
        if(isset($coupon) && !empty($coupon)){
            $coupon_amount = $coupon->amount;
            $coupon_id = $coupon->id;
            $coupon_code = $coupon->code;

            $facilitator = $coupon->facilitator_id;
            
            return [
                'amount' => $coupon_amount,
                'id' => $coupon_id,
                'code' => $coupon_code,
            ];
        }
        return;
    }

    public function getEligibleEarners(){
        
    }

    public function verifyCoupon($request, $pid){
        $type['pid'] = $pid;
        
        if($request->coupon && !empty($request->coupon)){
            $verifyCoupon = $this->getCouponValue($request->coupon);

            if(!is_null($verifyCoupon)){
                $response = $this->getCouponUsage($request->coupon, $request->email, $pid, 2);
            }else{
                $response = null;
            }

        }else{
            $response = null;
        }
        return $response;
    }

    public function createTempDetails($request, $payment_mode){
       
        $temp = TempTransaction::where('email', $request->email)->first();
            if (isset($temp) && !empty($temp)) {
                $temp->update([
                    'type' => $request->payment_type,
                    'name' => $request->name,
                    'program_id' => $request['metadata']['pid'],
                    'coupon_id' =>  $request['metadata']['coupon_id'],
                    'facilitator_id' => $request['metadata']['facilitator'],
                    'amount' =>  $request['amount'],
                    'transid' =>  $request->transid,
                    'payment_mode' => $payment_mode,
                    'location' => $request->location ?? NULL
                ]);
            } else {
                try {
                    $temp = TempTransaction::create([
                        'email' => $request->email,
                        'type' => $request->payment_type,
                        'program_id' => $request['metadata']['pid'],
                        'coupon_id' =>  $request['metadata']['coupon_id'],
                        'facilitator_id' => $request['metadata']['facilitator'],
                        'amount' =>  $request['amount'],
                        'transid' =>  $request->transid,
                        'payment_mode' => $payment_mode,
                        'name' => $request->name,
                        'location' => $request->location ?? NULL
                    ]);
                } catch (\Throwable $th) {
                    return $th->getMessage();
                }
            }
        
        return $temp;
    }

    public function confirmProgramAmount($pid, $type){
        $program_amount = Program::where('id', $pid)->first()->$type;
       
        return $program_amount;
    }

    public function getEarnings($amount, $coupon, $createdBy, $program, $programFacilitator = NULL){
        // Admin created coupon
        if(is_null($coupon)){
            return [
                'facilitator' => $data['facilitator_percent'] ?? 0,
                'admin' => $data['admin_percent'] ?? 0,
                'tech' => $data['tech_percent'] ?? 0,
                'faculty' =>  $data['faculty_percent'] ?? 0,
                'other' => $data['other_percent'] ?? 0,
            ];
        }
       
        if($coupon > 0){
            $coupon = $coupon;
        }else{
            $coupon = 0;
        }
       
        if($createdBy == 0){
            $toShare = $amount - $coupon;
        }else{
            $toShare = $amount;
        }
        
        $data['tech_percent'] = ($toShare * $program->tech_percent) /100;
        $data['faculty_percent'] =($toShare * $program->faculty_percent) /100;
        $data['admin_percent'] =($toShare * $program->admin_percent) /100;
        $data['other_percent'] = ($toShare * $program->other_percent) /100;

        if(isset($programFacilitator)){
            if($createdBy == $programFacilitator){
                $data['facilitator_percent'] = (($toShare * $program->facilitator_percent) /100) - $coupon;
            }else{
                $data[ 'facilitator_percent'] = (($toShare * $program->facilitator_percent) / 100);
            }
        }else{
            $data['facilitator_percent'] = 0;
        }
    
        return [
            'facilitator' => $data['facilitator_percent'] ?? 0,
            'admin' => $data['admin_percent'] ?? 0,
            'tech' => $data['tech_percent'] ?? 0,
            'faculty' =>  $data['faculty_percent'] ?? 0,
            'other' => $data['other_percent'] ?? 0,
        ];
    }

    protected function getInvoiceId($id){
        date_default_timezone_set("Africa/Lagos");
        $invoice_id = date("YmdHi"). '-' .$id. '-'.rand(10000, 99999);
        return $invoice_id;
    }

    protected function prepareTrainingDetails($program, $paymentDetails, $amount){
       
        $training = $program;
        $data['programFee'] = $training->p_amount;
        $data['programName'] = $training->p_name;
        $data['programAbbr'] = $training->p_abbr;
        $data['bookingForm'] = $training->booking_form;
        
        //Create User details
        $data['name'] = $paymentDetails->name;
        $data['email'] = $paymentDetails->email;
        $data['phone'] = $paymentDetails->phone;

        $data['password'] = bcrypt('12345');
        $data['program_id'] = $training->id;
        $data['amount'] = $amount;
        $data['t_type'] = "PAYSTACK";

        // Create Facilitator details
        if (isset($paymentDetails->facilitator_id)) {
            $data['facilitator_id'] = $paymentDetails->facilitator_id;
            $data['facilitator_name'] = User::where('id', $paymentDetails->facilitator_id)->value('name');
        }
          
        if(isset($paymentDetails->location)){
            $data['location'] = $paymentDetails->location; 
        }else $data['location'] = ' ' ;

        $data['role_id'] = "Student";
        $data['transid'] = $paymentDetails->transid;
    
        return $data;
    }

    public function createUserAndAttachProgramAndUpdateEarnings($data, $earnings, $coupon = NULL){
        //Check if email exists in the system and attach it to the new program to that email
        $user = User::where('email', $data['email'])->first();
        
        $data['coupon_amount'] = NULL;
        $data['coupon_id'] = NULL;
        $data['coupon_code'] = NULL;
       
        if(!is_null($coupon)){
            // $data['facilitator_id'] = $coupon->facilitator_id;
            $data['coupon_amount'] = $coupon->amount;
            $data['coupon_id'] = $coupon->id;
            $data['coupon_code'] = $coupon->code;
        }
        
        $data['booking_form'] = !is_null($data['bookingForm']) ? base_path() . '/uploads'.'/'. $data['bookingForm'] : null;
        $data['admin_earning'] = $earnings['admin'];
        $data['facilitator_earning'] = $earnings['facilitator'];
        $data['tech_earning'] = $earnings['tech'];
        $data['faculty_earning'] = $earnings['faculty'];
        $data['other_earning'] = $earnings['other'];

        // if user doesnt exist, create new user and attach program
        if (!isset($user)) {
            //save to database
            $user = User::updateOrCreate([
                'name' => $data['name'],
                'email' => $data['email'],
                't_phone' => $data['phone'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
            ]); 
        }

        $data['invoice_id'] = $this->getInvoiceId($user->id);
     
        //If program id is not in array of user program, attach program
        $userPrograms = DB::table('program_user')->where('user_id', $user->id)->where('program_id', $data['program_id'])->count();
        
        if( $userPrograms < 1){
            // Attach program
            $user->programs()->attach( $data['program_id'], [
                'created_at' =>  date("Y-m-d H:i:s"),
                't_amount' => $data['amount'], 
                't_type' => $data['t_type'], 
                't_location' => $data['location'], 
                'transid' => $data['transid'], 
                'paymenttype' => $data['payment_type'], 
                'paymentStatus' => $data['paymentStatus'], 
                'balance' => $data['balance'], 
                'invoice_id' => $data['invoice_id'],
                'facilitator_id' => $data['facilitator_id'] ?? NULL,
                'coupon_amount' => $data['coupon_amount'] ?? NULL,
                'coupon_id' => $data['coupon_id'] ?? NULL,
                'coupon_code' => $data['coupon_code'] ?? NULL,
                'admin_earning'=> $data['admin_earning'] ?? NULL,
                'facilitator_earning' => $data['facilitator_earning'] ?? NULL,
                'tech_earning' => $data['tech_earning'] ?? NULL,
                'faculty_earning' => $data['faculty_earning'] ?? NULL,
                'other_earning' => $data['other_earning'] ?? NULL,
                'currency' =>  \Session::get('currency'),
               
            ] );
        } 
        else{
            $data['user_id'] = $user->id;
            
            return $data;
            // return view('emails.thankyou', compact('data'));
            // dd('Duplicate transaction detected, please check your email for login instructions. You may close this tab now');
        }
   
        $data['user_id'] = $user->id;

        return $data;
    }

    public function updateCoupon($c, $email, $pid){
        $coupon = CouponUser::where('coupon_id', $c)->where('email', $email)->where('program_id', $pid)->first();
       
        try {
            $coupon->status = 1;
            $coupon->save();
        } catch (\Throwable $th) {
            
        }
       
        return;
    }
    public function deleteFromTemp($temp){
        $temp->delete();

        return;
    }

    public function getUserDetails($user_id){
        $user = User::where('id', $user_id)->select('name', 'email', 't_phone')->first();
        return $user;
    }

    public function uploadImage($file, $folder)
    {

        $imageName = uniqid(9) . '.' . $file->getClientOriginalExtension();

        if (!is_dir($folder)) {
            mkdir($folder);
        }

        $file->move(public_path($folder), $imageName);
        return $imageName;
    }

    public function deleteImage($image)
    {

        if (file_exists(public_path($image))) {
            unlink(public_path($image));
        }

        return;
    }

}
