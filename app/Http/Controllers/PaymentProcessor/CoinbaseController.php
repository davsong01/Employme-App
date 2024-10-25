<?php

namespace App\Http\Controllers\PaymentProcessor;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CoinbaseController extends Controller
{
    public function query($request, $mode){
        // Query Payment
        if (isset($request->user_program)) {
            $request['transid'] = $this->getReference('CNBSE');
            DB::table('program_user')->whereId($request->user_program)->update(['balance_transaction_id' => $request['transid']]);
        } else {
            $request['transid'] = $this->getReference('CNBSE');
            $temp = $this->createTempDetails($request, $mode->id);
        }

        $logo = url('/').'/'.Settings::value('logo');
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
            "logo_url" =>$logo,
            "local_price" => [
                "amount" => $request->amount,
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
            $temp->update(['payload' => json_encode($response->data)]);
           
            return $response->data->hosted_url;
        } else {
            return NULL;
        }
 
        $result = json_decode($result);
       
        if(isset($result) && !empty($result)){
            // Create temp details
            return $result->data->authorization_url;
        }else{
            return NULL;
        }
    }

    public function verify($reference, $mode, $temp)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.commerce.coinbase.com/charges/" . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "X-CC-Api-Key: " . $mode->secret_key . "",
                "Content-Type: application/json",
                "Cache-Control: no-cache",
                "X-CC-Version: 2018-03-22"
            ),
        ));
        $response = curl_exec($curl);
        $response = json_decode($response);

        if (isset($response->data) && !empty($response->data->payments)) {
            if ($response->data->payments[0]->status == 'CONFIRMED') {
                $status = 'success';
            }else{
                $status = $response->data->payments[0]->status;
            }
            try {
                if($temp){
                    $temp->update(['payload' => json_encode($response->data)]);
                }
            } catch (\Throwable $th) {
                \Log::info($th->getMessage());
            }
        }
        return $status;
    }

}