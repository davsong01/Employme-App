<?php

namespace App\Http\Controllers;

use PDF;
use App\Coupon;
use App\Program;
use App\Settings;
use App\CouponUser;
use App\Mail\Welcomemail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $check = 98;

    protected function sendWelcomeMail($details, $data){
        set_time_limit(360);
        try {
            if(isset($details['invoice_id'])){
                $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            }else $pdf = null;
           
            Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
        } catch(\Exception $e){
            // Get error here
            dd($e->getMessage());
            Log::error($e);
            return false;
        }
        
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

    public function confirmProgramAmount($pid, $type){
        $program_amount = Program::where('id', $pid)->first()->$type;
       
        return $program_amount;
    }

    public function getEarnings($amount, $coupon, $createdBy, $program, $programFacilitator = NULL){
        // Admin created coupon
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
                $data['facilitator_percent'] = (($toShare * $program->facilitator_percent) /100);
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

    protected function getInvoiceId(){
        return 'Invoice'.rand(10, 100);
    }
}
