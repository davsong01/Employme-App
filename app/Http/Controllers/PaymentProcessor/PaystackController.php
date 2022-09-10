<?php

namespace App\Http\Controllers\PaymentProcessor;

use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PaystackController extends Controller
{
    public function query($request, $mode){
    
        $request['transid'] = $this->getReference('PYSTK');

        $this->createTempDetails($request, $mode->id);
        $url = "https://api.paystack.co/transaction/initialize";
       
        $fields = [
            'email' => $request->email,
            'amount' => $request->amount * 100,
            'reference' =>  $request['transid'],
            'callback_url' => url('/'). '/payment/callback',
            'currency'=>$mode->currency,
        ];
       
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$mode->secret_key,
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post

        $result = curl_exec($ch);
        $result = json_decode($result);
       
        if(isset($result) && !empty($result)){
            // Create temp details
            return $result->data->authorization_url;
        }else{
            return NULL;
        }
       
    }

    public function verify($reference, $mode){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$mode->secret_key,
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            return $response->data->status;
        }
    }

    // public function transactionId(){
    // }
}