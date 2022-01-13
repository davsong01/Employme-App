<?php

namespace App\Http\Controllers;

use PDF;
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
}
