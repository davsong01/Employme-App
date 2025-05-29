<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\User;
use App\Http\Requests;
use App\Models\Coupon;
use App\Models\Wallet;
use App\Models\Program;
use App\Models\Settings;
use App\Models\PaymentMode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    public function checkout(Request $request){
        $training = json_decode($request->training, true);
        
        $trainingObject = json_decode(json_encode(json_decode($request->training)));
        $trainingObject->currencies = json_decode(json_encode($trainingObject->currencies), true);
        
        $modes = null;
        $location = $request->location ?? null;
        $preferred_timing = $request->preferred_timing ?? null;
        if($request->has('modes')){
            // Get mode amount 
            $modes = $request->modes;
            $amount = $this->getModeAmount($request->modes,$request->type,$request->training);
            if(!$amount){
                return back()->with('error', 'Invalid Amount');
            }
        }else{
            if($request->type == 'full'){
                $amount = $training['p_amount'];
            }elseif($request->type == 'part'){
                $amount = $training['p_amount'] /2;
            }elseif($request->type == 'earlybird'){
                $amount = $training['e_amount'];
            }else{
                return back()->with('error', 'Invalid Payment Type selection');
            }
        }
        
        $type = $request->type;
        
        // inject facilitator details
        if($request->has('facilitator')){
            Session::put('facilitator', $request->facilitator);
            Session::put('facilitator_id', $request->facilitator_id);
            Session::put('facilitator_name', $request->facilitator_name);
            Session::put('facilitator_license', $request->facilitator_license);
        }

        $payment_modes = $this->getPaymentModes();
        
        return view('checkout', compact('amount', 'training', 'type', 'payment_modes','modes','location', 'preferred_timing', 'trainingObject'));
    }

   
    

    public function getPaymentModes(){
        $payment_modes = PaymentMode::where('status', 'active')->get();
        if(Session::has('facilitator_id')){
            $mode_id = User::where('id', Session::get('facilitator_id'))->first()->payment_mode;
            if(isset($mode_id) && !empty($mode_id)){
                $modes = PaymentMode::where('id', $mode_id)->where('status', 'active')->get();
                if (isset($modes) && !empty($modes)) {
                    // $admins->merge($users)
                    $payment_modes = $modes;
                }
            }
        }
        return $payment_modes;
    }

    public function validateCoupon(Request $request){
        $verifyCoupon = $this->getCouponValue($request->code, $request->pid);
        $response = null;
        if(!is_null($verifyCoupon)){
            $response = $this->getCouponUsage($request->code, $request->email, $request->pid, $request->price);
        }

        return response()->json($response);
    }


    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        $template = Settings::first()->templateName->name;
        
        if ($request->user_program && $request->type == 'balance') {
            $data = DB::table('program_user')->find($request->user_program);

            $request['amount'] = $data->balance;
            $request['payment_mode'] = $data->payment_mode;
            $request['preferred_timing'] = $data->preferred_timing;
            $request['currency'] = $data->currency;
            $type['type'] = 'balance';
            
            try {
                $url = $this->queryProcessor($request, $data);
                
                if($url){
                    return redirect()->away($url);
                }else{
                    return back()->with('error', 'Something went wrong, Kindly try a different payment method!');
                }
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
                return abort(500);
            }
        }
        
        $this->validate(request(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'quantity' => 'required|numeric',
            'currency' => 'required|string',
            "amount"=>'required',
            "coupon"=>'sometimes',
            "metadata"=>'sometimes',
            "payment_mode"=>'required',
            "preferred_timing" => 'nullable',
        ]);

        if($template == 'contai'){
            $request['amount'] = \Session::get('exchange_rate') * $request['amount'];
            
            $type = json_decode($request['metadata'], true);
            
            $pid = $type['pid'];
            $coupon_id = $type['coupon_id'];
            $facilitator_id = $type['facilitator'];
            $request['payment_type'] = $type['type'];

            $type['name'] = $request['name'];
            $type['phone'] = $request['phone'];
            $training = Program::where('id', $type['pid'])->first();
            $response = $this->verifyCoupon($request, $type['pid']);

            $resolve_to_ids = collect($training->resolve_to_ids ?? [])
                ->push($training->id)
                ->unique()
                ->values()
                ->all();

            $trainingsToResolveTo = Program::whereIn('id', $resolve_to_ids)->get();
            
            // Free training
            if ($request->payment_type == 'full' && $training->p_amount == 0) {
                foreach ($trainingsToResolveTo as $singleTraining) {
                    $data = $this->prepareFreeTrainingDetails($singleTraining, $request);
                    $data['payment_type'] = 'Full';
                    $data['message'] = 'Full payment';
                    $data['paymentStatus'] = 1;
                    $data['currency_symbol'] = '&#x20A6;';
                    $data['balance'] = 0;
                    
                    $data = $this->createUserAndAttachProgramAndUpdateEarnings($data, []);
                }
            }

            if ($request->payment_type == 'full' && $training->p_amount == 0) {
                $this->sendWelcomeMail($data);

                // Login User in
                Auth::loginUsingId($data['user_id']);
                return view('thankyou', compact('data'));
            }
            

            if(is_null($response)){
                // Modify amount to suit program
                if($request->has('modes')){
                    // Get mode amount 
                    $modes = $request->modes;
                    $amount = $this->getModeAmount($request->modes,$request->payment_type,$training);
                }else{
                    if ($type['type'] == 'full') {
                        $request['amount'] = $training->p_amount;
                    }

                    if($type['type'] == 'earlybird'){
                        $request['amount'] = $training->e_amount;
                    }

                    if ($type['type'] == 'part') {
                        $request['amount'] = ($training->p_amount) / 2;
                    }
                }
            }else{
                // Modify coupon_id in metadata
                $type['coupon_id'] = $response['id'];
                $coupon_id = $response['id'];
                
                if($response['grand_total'] <= 0){
                    $request->request->add(['reference' => $request->reference]);
                    $request['transid'] = $this->getReference('PYSTK');

                    $metadata = json_decode($request->metadata, true);
                    $metadata['coupon_id'] = $response['id'];
                    $request['reference'] = $request['transid'];
                    $request['metadata'] = $metadata;
                   
                    // Create temp user
                    $tempDetails = app('app\Http\Controllers\Controller')->createTempDetails($request, $request->payment_mode);
                    
                    // handle gateway call back normally
                    $req = new \App\Http\Controllers\PaymentController();
                    $response = $req->handleGatewayCallback($request, 'zero-amount');
                    if(resolveAuthUser()){
                        return redirect(url('/dashboard'));
                    }
                }

            }

            // Pay from wallet
            if ($request->payment_mode == 'wallet') {
                $request['user_id'] = resolveAuthUser()->id;
                $request['p_id'] = $pid;
                $response = $this->payFromAccount($request, 'frontent-wallet');

                if (isset($response['status']) && $response['status'] == 'failed') {
                    return redirect(url('trainings/' . $pid))->with('error', $response['message']);
                }

                if (isset($response['status']) && $response['status'] == 'success') {
                    return redirect(route($response['route']))->with('message', $response['message']);
                }
            }


            // Pay with Transfer
            if($request->has('payment_mode') && $request->payment_mode == 0){
                $request->request->add(['reference' => $request->reference]);
                $request['transid'] = 'BT-'.rand(11111111,9999999);
                $metadata = json_decode($request->metadata, true);
                $metadata['coupon_id'] = $response['id'] ?? null;
                $request['metadata'] = $metadata;
                
                $tempDetails = app('app\Http\Controllers\Controller')->createTempDetails($request, $request->payment_mode);
                
                if($request->payment_type == 'earlybird'){
                    $request['extraCurrencies'] = getAmountExtraCurrencies($training, $type, $training->e_amount, 'yes');
                    // $request['extraCurrencies'] = getAmountExtraCurrencies($training, $request->payment_type, $training->amount);
                }else{
                    $request['extraCurrencies'] = getAmountExtraCurrencies($training, $request->payment_type, $training->amount);
                }
                
                $data = $request->all();
                
                \Session::put('data', $data);
                return redirect(route('upload-proof-of-payment'));
                
            }

            // Create temp user and redirect
            $request['metadata'] = $type;
            
            try{
                $url = $this->queryProcessor($request);
                if(!is_null($url)){
                    return redirect()->away($url);
                }else{
                    return redirect(url('trainings/' . $pid))->with('error', 'Something went wrong, Kindly try again!');
                }
            }catch(\Exception $e) {
                // dd($e->getMessage(), $e->getFile(), $e->getLine());
                \Log::info($e->getMessage());
                
                return redirect(url('trainings/' . $pid))->with('error', 'Something went while verifying payment, Kindly contact admin!');
            } 

        }
        
        if($template == 'default'){
        
            if(isset($type['type']) && $type['type'] == 'balance'){
            
                //Check if userid tallys with trainingid
                $check = DB::table('program_user')->whereUserId($type['user_id'])->whereProgramId($type['pid'])->first();
                
                if(($check->balance * 100) == $request->amount){
                    try{
                        return Paystack::getAuthorizationUrl()->redirectNow();
                    }catch(\Exception $e) {
                        return Redirect::back()->with('error', 'The paystack token has expired. Please refresh the page and try again');
                    }  
                }else return Redirect::back()->with('error', 'Invalid Transaction');
            }

            try{
                return Paystack::getAuthorizationUrl()->redirectNow();
            }catch(\Exception $e) {
              
                \Log::info($e->getMessage());
                return abort(500);
                // return Redirect::back()->with('error', 'The paystack token has expired. Please refresh the page and try again'); 
            }  
        }
    }

    public function queryProcessor($request,$data=null, $query_only = null){
        $mode = PaymentMode::find($request->payment_mode ?? $request->provider);
        if(isset($mode) && !empty($mode)){
            if($mode->processor == 'paystack'){
                if(!empty($query_only)){
                    $url = app('App\Http\Controllers\PaymentProcessor\PaystackController')->query($request, $mode, $data, $query_only);
                }else{
                    $url = app('App\Http\Controllers\PaymentProcessor\PaystackController')->query($request,$mode, $data);
                }
            }

            if ($mode->processor == 'coinbase') {
                if (!empty($query_only)) {
                    $url = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->query($request, $mode, $data,$query_only);
                } else {
                    $url = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->query($request, $mode, $data);
                }

               
            }
        }
        // redirect away
        return $url;
        
    }
    
    public function verifyProcessor($reference, $temp, $verify_only=false){
        
        $mode = PaymentMode::find($temp->payment_mode);
            
        if (isset($mode) && !empty($mode)) {
            if ($mode->processor == 'paystack') {
                $response = app('App\Http\Controllers\PaymentProcessor\PaystackController')->verify($reference, $mode);
                
                $status = $response['data']['status'] ?? 'failed';
            }
            if ($mode->processor == 'coinbase') {
                $response = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->verify($reference, $mode, $temp);

                $status = $response['data']['status'] ?? 'failed';
            }
        }
        
        if (isset($status) && $status == 'success') {
            return $response;
        }else{
            if(!empty($verify_only)){
                return 'failed';
            }
            return back()->with('error', 'Payment was not succusful');
        }
    }

    public function handleGatewayCallback(Request $request, $is_zero_coupon=null)
    {
        $balance_payment = DB::table('program_user')->whereNotNull('balance_transaction_id')->where('balance_transaction_id', $request->reference)->first();
        
        if($balance_payment){
            //process as balance
            $response = $this->verifyProcessor($request->reference, $balance_payment);

            $status = $response['data']['status'] ?? 'failed';
        }else{
            $temp = TempTransaction::where('transid', $request->reference)->first();

            if(!$temp){
                // Wallet payment
                $wallet = Wallet::where('transaction_id', $request->reference)->first();
                
                if($wallet){
                    $wallet->payment_mode = $wallet->provider;
                    $status = $this->verifyProcessor($request->reference, $wallet, $wallet->gateway, 'verify-only');
                    
                    if(!empty($status) && $status == 'success'){
                        $data = ['status' => 'approved'];

                        $update = app('App\Http\Controllers\WalletController')->updateWallet($request->reference,$data);
                        
                        if(isset($update) && $update == 'success'){
                            return redirect(route('home'))->with('message', number_format($wallet->amount). ' Account TopUp successful');
                        }
                    }
                }

            }

            if (!$temp) {
                return redirect(route('home'));
            } else {
                if(is_null($is_zero_coupon)){
                    $response = $this->verifyProcessor($request->reference, $temp);
                    $status = $response['data']['status'] ?? 'failed';
                }else{
                    $status = 'success';
                }
            }
        }
        
        if($status == 'success'){
            if ($balance_payment) {
                $data['type'] = 'balance';
                
                $data['currency_symbol'] = $balance_payment->currency_symbol;
                $data['amount'] = $balance_payment->balance;
                $data['email'] = User::whereId($balance_payment->user_id)->value('email');
                $data['programName'] = Program::where('id', $balance_payment->program_id)->value('p_name');

                // Do facilitator stuff here later
                $old = Transaction::where('balance_transaction_id', $request->reference)->first();
                $old->update([
                    'balance' => 0,
                    'paymentStatus' => 1,
                    'paymentType' => 'Full',
                    'amount' => $balance_payment->amount + $balance_payment->balance,
                    'balance_paid' => $balance_payment->balance,
                    'balance_amount_paid' => now(),
                ]);

                PaymentThread::create([
                    'program_id' => $old->program_id,
                    'user_id' => $old->user_id,
                    'payment_id' => $old->id,
                    'transaction_id' => $request->reference,
                    'parent_transaction_id' => $old->transid,
                    'amount' => $balance_payment->balance
                ]);
                
                $this->sendWelcomeMail($data);

                return redirect(route('trainings.show', ['p_id' => $balance_payment->program_id]))->with('message','Balance payment received!');

            }else{
                $paymentDetails = $temp;
            }
        }
        
        $template = Settings::first()->templateName->name;
        $mainProgram = $temp->program;
        
        if($template == 'contai'){
            // $temp = TempTransaction::where('email', $paymentDetails->email)->where('program_id', $paymentDetails->program_id)->first();
            if(isset($temp) && !empty($temp)){
                // Compare
                $resolve_to_ids = collect($mainProgram->resolve_to_ids ?? [])
                    ->unique()
                    ->values()
                    ->all();
                $resolve_to_ids = [91,86];
                $trainings = Program::whereIn('id', $resolve_to_ids)->get();
                
                $user = TransactionService::createOrUpdateParticipant([
                    'email' => $paymentDetails->email,
                    'name' => $paymentDetails->name,
                    'phone' => $paymentDetails->phone,
                    'roles' => "Student",
                    'staffId' => $request->staffId,
                    'password' => bcrypt('12345'),
                ]);

                $getInvoiceId = TransactionService::getInvoiceId();

                try {
                    DB::beginTransaction();
                    $allDetails = [];

                    if(!empty($trainings)){
                        foreach($trainings as $training){
                            $transactionDetails = TransactionService::getTransactionDetails($temp, $training, $mainProgram, $paymentDetails);
                            $data = TransactionService::prepareTrainingDetails($training, $temp, $transactionDetails);
                            $allData = array_merge($transactionDetails, $data);
                            $allData['invoice_id'] = $getInvoiceId;
                            $allData['transid'] = $temp->transid;
                            $allData['payment_id'] = $temp->id;
                            $attach = TransactionService::assignTrainingToUser($allData, $user);
                            
                            $allDetails[$training->id] = $allData;
                        }
                    }
                    
                    $temp->update([
                        'status' => 'completed',
                        'meta' => $allData,
                        'payload' => $response ?? null
                    ]);
                    
                    return view('emails.receipt', compact('allData', 'allDetails'));

                    TransactionService::sendWelcomeMail($allData);

                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollback();
                    dd($th->getMessage());
                    //throw $th;
                }
                dd('hon');
                Auth::loginUsingId($allData['user_id']);
                
                return view('thankyou', compact('data'));

            }
            
            return redirect(route('welcome'));
            // Compare details with details in temp table
        }
       
    }

    public function payFromAccount(Request $request, $source=null){
        $user_id = $request->user_id ?? resolveAuthUser()->id;
        $user = User::where('id', $user_id)->first();
        
        $old = Transaction::where('user_id',$user->id)->where('program_id', $request->p_id)->get();
        
        $existing = $old->sum('amount');
        $existingTransaction = $old->first();
        $program = Program::where('id', $request->p_id)->first();
        $training_fee = $program->p_amount;
        $account_balance = $user->account_balance;
        $amount_to_pay = $training_fee - $existing;
        $balance = $training_fee - ($request->amount + $existing);
        // $balance = $old->balance;

        
        if (!empty($existingTransaction) && $existingTransaction->balance < 1) {
            if(isset($source) && $source == 'frontent-wallet'){
                return [
                    'status' => 'failed',
                    'message' => 'You are already enrolled for this training!',
                ];
            }else{
                return back()->with('error', 'You are already enrolled for this training!');
            }

            $balance = $training_fee;

        }else{
            $balance = $existingTransaction->balance;
        }
        
        // else{
        //     $balance = $training_fee;
        // }
        if(!empty($existingTransaction)){
            $request['type'] = 'balance';
        }else{
            $request['type'] = 'fresh';
        }
        
        if($account_balance < $balance){
            if (isset($source) && $source == 'frontent-wallet') {
                return [
                    'status' => 'failed',
                    'message' => 'Insufficient Account Balance!',
                ];
            } else {
                return back()->with('error', 'Insufficient Account Balance');
            }
        }

        if(($request->amount + $existing) > $training_fee){
            if (isset($source) && $source == 'frontent-wallet') {
                return [
                    'status' => 'failed',
                    'message' => 'You cannot pay more than ' . $amount_to_pay . ' for this training!',
                ];
            } else {
                return back()->with('error', 'You cannot pay more than '.$amount_to_pay. ' for this training!');
            }
        }
        // Process transaction
        $allDetails['programFee'] = $program->p_amount;
        $allDetails['program_id'] = $program->id;
        $allDetails['programName'] = $program->p_name;
        $allDetails['programAbbr'] = $program->p_abbr;
        $allDetails['bookingForm'] = $program->booking_form;
        $allDetails['programEarlyBird'] = $program->e_amount;
        $allDetails['name'] = $user->name;
        $allDetails['email'] = $user->email;
        $allDetails['phone'] = $user->phone;
        $allDetails['t_type'] = 'wallet';
        $allDetails['currency'] = $existingTransaction->currency ?? \Session::get('currency');
        $allDetails['currency_symbol'] = $existingTransaction->currency_symbol ?? \Session::get('currency_symbol');
        $allDetails['message'] = $this->dosubscript1($balance);
        $allDetails['paymentStatus'] = $this->paymentStatus($balance);
        $total_amount_paid = $request->amount + $existing;
        $allDetails['amount'] = $request->amount;

        if ($request->type == 'balance') {
            $allDetails['balance_transaction_id'] = $this->getReference('USER_TOP_UP_BAL');
            $allDetails['transaction_id'] = $existingTransaction->transid;
            $allDetails['invoice_id'] = $existingTransaction->invoice_id;
            $allDetails['balance'] = $existingTransaction->balance - $request->amount;

            if($allDetails['balance'] < 1){
                $data['payment_type'] = 'Full';
                $data['message'] = 'Full payment';
                $data['paymentStatus'] = 1;
            }else{
                $data['payment_type'] = 'Part';
                $data['message'] = 'Part payment';
                $data['paymentStatus'] = 0;
            }
        } else {
            $balance = $training_fee - $request->amount;

            if ($balance < 1) {
                $data['payment_type'] = 'Full';
                $data['message'] = 'Full payment';
                $data['paymentStatus'] = 1;
            } else {
                $data['payment_type'] = 'Part';
                $data['message'] = 'Part payment';
                $data['paymentStatus'] = 0;
            }

            $allDetails['transaction_id'] = $existingTransaction->transid ?? $this->getReference('WLTUP');
            $allDetails['invoice_id'] = $this->getInvoiceId();
            $allDetails['balance'] = $balance;
            $allDetails['transid'] = $this->getReference('USER_WALLET');
        }

        // $data['payment_type'] = 'Part';
        // $data['message'] = 'Part payment';
        // $data['paymentStatus'] = 1;
        
        // Log wallet
        $wallet['amount'] = abs($request->amount);
        $wallet['transaction_id'] = $allDetails['transaction_id'];
        $wallet['type'] = 'debit';
        $wallet['method'] = 'wallet';
        $wallet['provider'] = 'SYSTEM';
        $wallet['status'] = 'approved';
        $wallet['user_id'] = $user->id;
        
        app('App\Http\Controllers\WalletController')->logWallet($wallet);
        
        if($request['type'] == 'balance'){
            $existingTransaction->update([
                'amount' => $total_amount_paid,
                'paymentStatus' => $allDetails['paymentStatus'],
                'balance' => $allDetails['balance']
            ]);

            PaymentThread::create([
                'program_id' => $existingTransaction->program_id,
                'user_id' => $existingTransaction->user_id,
                'payment_id' => $existingTransaction->id,
                'transaction_id' => $allDetails['balance_transaction_id'],
                'parent_transaction_id' => $existingTransaction->transid,
                't_type' => 'wallet',
                'amount' => $request->amount
            ]);

            $data['training'] = $allDetails['balance_transaction_id'];
            $data['invoice_id'] = $existingTransaction->transid;
            $data['transid'] = $existingTransaction->transid;
            $data['type'] = 'payment.from.toup';
            $data['t_type'] = 'wallet';
            $data['email'] = resolveAuthUser()->email;
            $data['programName'] = $program->p_name;
            $data['programAbbr'] = $program->p_abbr;
            $data['programFee'] = $existingTransaction->amount + $existingTransaction->balance;
            $data['amount'] = $allDetails['amount'];
            $data['total_amount_paid'] = $total_amount_paid;
            $data['balance'] = $allDetails['balance'];
            $data['name'] = resolveAuthUser()->name;
            $data['currency_symbol'] = $existingTransaction->currency_symbol;
            
            $this->sendWelcomeMail($data);
        }else{
            $user->programs()->attach($allDetails['program_id'], [
                'amount' => $allDetails['amount'],
                't_type' => 'wallet',
                't_location' => $allDetails['location'] ?? null,
                'paymentStatus' => $allDetails['paymentStatus'],
                'balance' => $allDetails['balance'],
                'transid' =>  $allDetails['transaction_id'],
                'invoice_id' =>  $allDetails['invoice_id'],
                'currency' => $allDetails['currency'],
                'currency_symbol' => $allDetails['currency_symbol'],
                'created_at' => $allDetails['date'] ?? now(),
                'coupon_id' => $allDetails['coupon_id'] ?? null,
                'coupon_amount' => $allDetails['coupon_amount'] ?? null,
                'coupon_code' => $allDetails['coupon_code'] ?? null,
                'training_mode' => $allDetails['training_mode'] ?? null,
                'preferred_timing' => $allDetails['preferred_timing'] ?? null,
            ]);

            $payment = Transaction::where(['user_id' => $user->id, 'program_id' => $allDetails['program_id']])->first();
            PaymentThread::create([
                'program_id' => $allDetails['program_id'],
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transid,
                'parent_transaction_id' => $payment->transid,
                't_type' => 'wallet',
                'amount' => $request->amount
            ]);

            $data['training'] = $payment->transid;
            $data['invoice_id'] = $allDetails['invoice_id'];
            $data['transid'] = $allDetails['transid'];
            $data['type'] = 'initial';
            $data['t_type'] = 'wallet';
            $data['email'] = resolveAuthUser()->email;
            $data['name'] = resolveAuthUser()->name;
            $data['programName'] = $program->p_name;
            $data['total_amount_paid'] = $total_amount_paid;
            $data['programAbbr'] = $program->p_abbr;
            $data['programFee'] = $program->p_amount;
            $data['balance'] = $allDetails['balance'];
            $data['amount'] = $allDetails['amount'];
            $data['currency_symbol'] = $allDetails['currency_symbol'];
            
        
            $this->sendWelcomeMail($data);
        }

        if (isset($source) && $source == 'frontent-wallet') {
            return [
                'status' => 'success',
                'message' => 'Transaction successful',
                'route' => 'home',
            ];
        } else {
            return redirect(route('home'))->with('message', 'Transaction successful');
        }

    }

    
    public function accountTopUp(Request $request, $type){
        if($request->type == 'virtual'){
        
            $mode = PaymentMode::find($request->payment_mode);

            $data = $this->validate($request, [
                'amount' => 'required',
            ]);
            $request['provider'] = $request->payment_mode;
            $request['email'] = resolveAuthUser()->email;
            
            $response = $this->queryProcessor($request, [],'query-only');
            
            if ($response) {
                $data = [
                    'amount' => abs($data['amount']),
                    'transaction_id' => $response['transaction_id'],
                    'type' => 'credit',
                    'method' => 'virtual',
                    'provider' => $request->payment_mode,
                    'user_id' => resolveAuthUser()->id,
                ];

                app('App\Http\Controllers\WalletController')->logWallet($data);
               
                return redirect()->away($response['url']);
            } else {
                return back()->with('error', 'Something went wrong, Kindly try a different payment method!');
            }

        }else{
            $data = $this->validate($request, [
                'amount' => 'required',
                'pop' => 'required|max:2048|image',
            ]);

            $trans = $this->getInvoiceId();

            if (!empty($request->pop)) {
                $pop = $this->uploadImage($request->pop, 'pops');
            }

            $data = [
                'amount' => abs($data['amount']),
                'proof_of_payment' => $pop,
                'transaction_id' => $trans,
                'type' => 'credit',
                'method' => 'manual',
                'provider' => 'SYSTEM',
                'user_id' => resolveAuthUser()->id,
            ];

            app('App\Http\Controllers\WalletController')->logWallet($data);
            // send email
            $data['pop'] = public_path() .  '/pops/' . $pop;
            $data['type'] = 'manual.wallet.topup';
            $data['email'] = 'davsong16@gmail.com';
            $data['name'] = resolveAuthUser()->name;
            // Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
            $data['participant_email'] = resolveAuthUser()->email;
            $data['realfilename'] = $pop;

            $this->sendWelcomeMail($data);
        }
        
        return back()->with('message', 'Payment logged, your account balance will be updated as soon as payment is confirmed');
    }

    
    //set balance and determine user receipt values
    private function dosubscript1($balance){

        if($balance <= 0){
            return 'Full payment';
        }else return 'Part payment';
    }

    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }else return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }else return 'Part payment';
    }
}