<?php

namespace App\Http\Controllers;
use App\User;
use App\Program;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Mail\Welcomemail;
use PDF;

class PayController extends Controller
{
    Public function process(Request $request)
{
$reference = substr($_GET['t'], 11);
$id = $_GET['id'];

$data = array();
//The parameter after verify/ is the transaction reference to be verified
$url = 'https://api.paystack.co/transaction/verify/'.$reference;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt(
  $ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer sk_test_46f59b74eef7eb749b6f782e27c6bb69859fb590']
);
$request = curl_exec($ch);
curl_close($ch);

if ($request) {
  $data = json_decode($request, true);
  //print_r($data);
  if($data){
    if($data['data']){
      //something came in
      if($data['data']['status'] == 'success'){
        // the transaction was successful, you can deliver value

      //determine the program details
      $training = $id;
      $programFee = Program::findorFail($training)->p_amount;
      $programName = Program::findorFail($training)->p_name;
      $programAbbr = Program::findorFail($training)->p_abbr;
      $bookingForm = Program::findorFail($training)->booking_form;
      $programEarlyBird = Program::findorFail($training)->e_amount;
      $invoice_id = 'Invoice'.rand(10, 100);
      
      $error_codes = [
              //Error 101: Amount being paid is greater than  program fee
              //Error 102: User has initially paid full amount
              '1' => 'ERROR 101: Details not saved, please contact administrator with details',
              '2' => 'Error 102: User has initially paid full amount'
              ];
              
        //get transaction details
        $name = $data['data']['customer']['first_name']." ".$data['data']['customer']['last_name'];
        $email = $data['data']['customer']['email'];
        $phone = $data['data']['customer']['phone'];
        $password = bcrypt('12345');
        $program_id = $training; 
        $amount = ($data['data']['amount'])/100;
        $t_type = "PAYSTACK";
        
        //check if location data is present
        if(isset($data['data']['metadata']['custom_fields'][0]['value'])){
            $location = $data['data']['metadata']['custom_fields'][0]['value']; 
        }else $location = ' ' ;

        $role_id = "Student";
        $transid = $data['data']['reference'];
        
      //check if email exist for the program id
      $user = User::where('program_id', $id)->where('email', $email)->first();
        if($user){
            //dd($user->email);
            
          //update user payment details and send receipt
          $newamount = $user->t_amount + $amount;
          if($newamount > $programFee){
              dd($error_codes['1']);
          }else 
          $balance = $programFee - $newamount;
          $message = $this->dosubscript1($balance);
          $paymentStatus =  $this->paymentStatus($balance);
          
          $user->t_amount = $newamount;
          $user->balance = $balance;
          $user->transid = $transid;
          $user->paymentStatus =  $paymentStatus;
  
          $user->save();
            //end of update user details
          
          //send Update Email
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
          'amount' =>$newamount,
      ];

        $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
      //  return view('emails.receipt', compact('data', 'details'));
        Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
      //end of send update email
      
      //return thank you page
      return view('emails.thankyou', compact('data',  'details')); 
        
        }else{
      //go ahead and create new user cos email doesn't exit for this program yet     
      
      //Determine payment type and balance
      if($amount > $programFee){
          dd($error_codes['1']);
      }else
      {
          
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
      
          //save to database
            User::create([
          'name' => $name,
          'email' => $email,
          't_phone' => $phone,
          'password' => $password,
          'program_id' => $program_id,
          't_amount' => $amount,
          't_type' => $t_type,
          't_location' => $location,
          'role_id' => $role_id,
          'transid' => $transid,
          'paymenttype' => $payment_type,
          'paymentStatus' => $paymentStatus,
          'balance' => $balance,
          'invoice_id' =>  $invoice_id,
      ]);
      
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
        //return view('emails.receipt', compact('data', 'details'));
      Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
        
      //include thankyou page
        return view('emails.thankyou', compact('data',  'details'));

      //dd($payment_type, $name, $location, $invoice_id, $balance, $message, $paymentStatus, 'storage/'.$bookingForm, $programName);

      }
  }
      }else{
        // the transaction was not successful, do not deliver value'
        //print_r($data);  //uncomment this line to inspect the result, to check why it failed.
        echo "Transaction was not successful: Last gateway response was: ".$data['data']['gateway_response'];
      }
    }else{
      echo $data['message'];
      //print_r($data);
    }

  }else{
    //print_r($data);
    die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
  }
}else{
  //var_dump($request);
  die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
}
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
