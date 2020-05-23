Public function accept(Request $request)
{
     //   dd($request['pid'], $request['t?reference']);
    $reference = $request['t?reference'];
    if($reference){
    
    }
    $result = array();
//The parameter after verify/ is the transaction reference to be verified
    $url = 'https://api.paystack.co/transaction/verify/'.$reference;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
    $ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer sk_test_aaca18115edcd1e67cd88e38a981a79ecf0fc49c']
    );
    $request = curl_exec($ch);
    curl_close($ch);

    if ($request) {
        $result = json_decode($request, true);
        // print_r($result);
        if($result){
        if($result['data']){
            //something came in
            if($result['data']['status'] == 'success'){

            //print_r ($result);
            dd($result);
                $amount = $transaction_amount;
                $name = $transaction_name;
                $email = $transaction_email;
                $phone =  $transaction_phone;
                $message = $transaction_message;
                $ref = $transaction_id;
                $date = $transaction_date;
            
            //get transaction details
            $transaction_id = $result['data']['reference'];
            $transaction_amount = $result['data']['amount'];
            $transaction_email = $result['data']['customer']['email'];
            $transaction_name = $result['data']['customer']['first_name']." ".$result['data']['customer']['last_name'];
            $transaction_phone = $result['data']['customer']['phone'];
            $transaction_message = $result['data']['status'];
            echo '<br>';
                $date = $result['data']['paid_at'];
            $transaction_date = trim($date, "00Z");
            }
            
            else{
            // the transaction was not successful, do not deliver value'
            //print_r($result);  //uncomment this line to inspect the result, to check why it failed.
            echo "Transaction was not successful: Last gateway response was: ".$result['data']['gateway_response'];
            }
        }else{
            echo $result['message'];
            print_r($result);
        }

        }else{
        //print_r($result);
        die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
        }
    }else{
        //var_dump($request);
        die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
    }
        }