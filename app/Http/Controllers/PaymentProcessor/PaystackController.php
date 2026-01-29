<?php

namespace App\Http\Controllers\PaymentProcessor;

use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class PaystackController extends Controller
{
    public function query($transaction, $query_only=null){
        $isBalance = $transaction->is_balance ?? false; // Add this later to the temp
        
        if(empty($query_only)){
            if ($isBalance) {
                // $request['transid'] = $transaction->transid;
                // DB::table('program_user')->whereId($request->user_program)->update(['balance_transaction_id' => $request['transid']]);
            } else {
                // $request['transid'] = $transaction->transid;
                // dd($request->all())l
                // app('app\Http\Controllers\Controller')->createTempDetails($request, $mode->id);
            }
        }else{
            // $request['transid'] = $transaction->transid;
        }
        
        $url = "https://api.paystack.co/transaction/initialize";
        
        // Convert amount using payment mode exchange rate
        $fields = [
            'email' => $transaction->email,
            'amount' => $transaction->amount * $transaction->paymentMode->exchange_rate * 100,
            'reference' =>  $transaction->transid,
            'callback_url' => url('/'). '/payment/callback',
            'currency'=> $transaction->paymentMode->currency,
        ];

        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$transaction->paymentMode->secret_key,
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post

        $result = curl_exec($ch);
        $result = json_decode($result);
        
        try {
            if (!empty($query_only)) {
                return [
                    'url' => $result->data->authorization_url,
                    'transaction_id' => $transaction->transid,
                    'status' => $result->status,
                    'message' => $result->message ?? null
                ];
            }

            return [
                'url' => $result->data->authorization_url ?? null,
                'transaction_id' => $transaction->transid ?? null,
                'status' => $result->status,
                'message' => $result->message ?? null
            ];
        } catch (\Exception $th) {
            return;
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
}