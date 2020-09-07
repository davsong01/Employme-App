<?php
require '../admin/connect.php';
$reference = $_GET['reference'];
$result = array();
//The parameter after verify/ is the transaction reference to be verified
$url = 'https://api.paystack.co/transaction/verify/'.$reference;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt(
  $ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer sk_test_9ef3c9b3235ec79555f13bae938717223d79e380']
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
         
         
        // $transaction_array = array("id"=>$transaction_id, "amount"=>$transaction_amount, "email"=>$transaction_email, "fname"=>$transaction_fname, "lname"=>$transaction_lname, "phone"=> $transaction_phone,  "date"=>$transaction_date, "mesage"=>$transaction_message);
         
         //$transaction_logs = json_encode($transaction_array);
           //echo json_encode($transaction_array);
         $query = "SELECT * from tbl_user WHERE email = '{$transaction_email}'";
        $r = mysqli_query($conn, $query);
        if(!$r){
          die("QUERRY FAILED".mysqli_error($conn));
      } 
            
      if ($r->num_rows == 0){
        //echo "User does not exist, move to transaction log only";
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            // prepare sql and bind parameters
            $stmt = $conn->prepare("INSERT INTO transaction_logs (amount, name, phone, email, date, message, ref)
            VALUES (:amount, :name, :phone, :email, :date, :message, :ref)");
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':ref', $ref);

            $amount = $transaction_amount;
            $name = $transaction_name;
            $email = $transaction_email;
            $phone =  $transaction_phone;
            $message = $transaction_message;
            $ref = $transaction_id;
            $date = $transaction_date;

            $stmt->execute();
            
            
            ?>
        <?php 
            }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            }
        $conn = null;

      }
        //include thank you page here
          include 'thankyou.php';
         // print_r ($result['data']['gateway_response'])."<br>";
         // echo $result['data']['paid_at']."<br>";
          //echo $result['data']['email']."<br>";
          //echo $result['data']['first_name']."<br>";
          //echo data.status;
         
        }else{
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