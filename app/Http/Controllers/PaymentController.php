<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcomemail;
use Paystack;
use PDF;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        if($paymentDetails['status'] == 'true'){
            $email = $paymentDetails['data']['customer']['email'];
            $id = $paymentDetails['data']['metadata']['userid'];
            $amount = $paymentDetails['data']['amount']/100;
            $programid = $paymentDetails['data']['metadata']['pid'];
          
            $user = User::findorFail($id);
                if($id != $user->id || $amount != $user->balance) {
                    dd('Please ensure you are logged in and try payment again');
                }else{
                    //update db and send email
                    
                    $programFee = $user->program->p_amount;
                    $newamount = $amount + $user->t_amount;
                    $balance = $programFee - $newamount;
                 
                    if($balance <= 0){
                        $message = 'Full Payment';
                        $paymentStatus = 1;
                    }else{
                        $message = 'Part Payment';
                        $paymentStatus = 0;
                    }

                    $user->t_amount = $newamount;
                    $user->balance = $balance;
                    $user->paymentStatus =  $paymentStatus;
                
                    $user->save();
                    //Send recceipt here
                    $details = [
                    'programFee' => $programFee,
                    'programName' => $user->program->p_name,
                    'programAbbr' => $user->program->p_abbr,
                    'balance' => $balance,
                    'message' => $message,
                    'booking_form' => base_path() . '/uploads'.'/'. $user->program->booking_form,
                    'invoice_id' =>  $user->invoice_id,
          
                ];
                
                $data = [
                    'name' =>$user->name,
                    'email' =>$user->email,
                    'bank' =>$user->t_type,
                    'amount' =>$newamount,
                ];
        
                  $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
               // return view('emails.receipt', compact('data', 'details'));
                 Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
                //end of send update email
        
                   //Redirect to payment history here
                  
                    return redirect('/')->with('message', 'Balance payment succesful');
                    } return back();
                
             
                        
                    };
                    
                    
                }
                
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }
