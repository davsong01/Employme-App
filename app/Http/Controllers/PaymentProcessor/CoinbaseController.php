<?php

namespace App\Http\Controllers\PaymentProcessor;

use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CoinbaseController extends Controller
{
    public function query($request, $mode){
        // Query Payment
        $request['transid'] = $this->getReference('CNBSE');
        
        $temp = $this->createTempDetails($request, $mode->id);
       
        $headers = array(
            "Content-Type: application/json",
            "X-CC-Api-Key: " . $mode->secret_key,
            "X-CC-Version: 2018-03-22",
        );
        
        $d = [
            'email' => $request->email,
            'name' => $request->name,
            "pricing_type" => 'fixed_price',
            "redirect_url" => url('/') . '/payment/callback'. '/' .  $request['transid'],
            "local_price" => [
                "amount" => $data['amount'],
                "currency" => $mode->currency,
            ],
            "metadata" => [
                'reference' => $request['transid'],
            ],
        ];

        $url = "https://api.commerce.coinbase.com/charges";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($d));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        if (isset($response) && isset($response->data)) {
            return [
                'code' => $response->data->code,
                'fee_rate' => $response->data->fee_rate,
                'rid' => $response->data->id,
                'reference' => $response->data->metadata->reference,
                'package_id' => $response->data->metadata->package_id,
                'investor_id' => $response->data->metadata->customer_id,
                'created_at' => date('Y-m-d h:i:s', strtotime($response->data->created_at)),
                'expires_at' => Carbon::parse($response->data->expires_at)->addHour(),
                'amount' => $response->data->pricing->local->amount,
                'currency' => $response->data->pricing->local->currency,
                'url' => $response->data->hosted_url
            ];
        } else {
            return NULL;
        }
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