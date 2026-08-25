<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\User;
use App\Models\Group;
use App\Http\Requests;
use App\Models\Coupon;
use App\Models\Wallet;
use App\Models\Program;
use App\Models\Settings;
use App\Models\PaymentMode;
use App\Models\Transaction;
use App\Models\GroupProgram;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use App\Services\CouponService;
use App\Services\PaymentService;
use App\Services\BlacklistService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\PaymentController;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    private function isEarlyBirdActive($training): bool
    {
        if ((float) data_get($training, 'e_amount', 0) <= 0) {
            return false;
        }

        $activeTill = data_get($training, 'early_bird_active_till');

        if (empty($activeTill)) {
            return false;
        }

        try {
            return now()->lessThanOrEqualTo(\Carbon\Carbon::parse($activeTill));
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function checkout(Request $request){
        if(!empty($request->package)){
            $training = json_decode($request->package, true);
            $trainingObject = json_decode(json_encode(json_decode($request->package)));
            $trainingObject->currencies = json_decode(json_encode($trainingObject->currencies), true);
            
            $modes = null;
            $location = $request->location ?? null;
            $preferred_timing = $request->preferred_timing ?? null;

            if ($request->has('modes')) {
                // Get mode amount 
                $modes = $request->modes;
                $amount = $this->getModeAmount($request->modes, $request->type, $request->training);
                if (!$amount) {
                    return back()->with('error', 'Invalid Amount');
                }
            } else {
                if ($request->type == 'full') {
                    if ($this->isEarlyBirdActive($training)) {
                        $amount = $training['e_amount'];
                        $type = 'earlybird';
                    } else {
                        $amount = $training['p_amount'];
                    }
                } elseif ($request->type == 'part') {
                    $amount = $training['p_amount'] / 2;
                } elseif ($request->type == 'earlybird') {
                    if ($this->isEarlyBirdActive($training)) {
                        $amount = $training['e_amount'];
                        $type = 'earlybird';
                    } else {
                        $amount = $training['p_amount'];
                        $type = 'full';
                    }
                } else {
                    return back()->with('error', 'Invalid Payment Type selection');
                }
            }
            $type = $type ?? $request->type;
            
            // inject facilitator details
            if ($request->has('facilitator')) {
                Session::put('facilitator', $request->facilitator);
                Session::put('facilitator_id', $request->facilitator_id);
                Session::put('facilitator_name', $request->facilitator_name);
                Session::put('facilitator_license', $request->facilitator_license);
            }

        }else{
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
                    if ($this->isEarlyBirdActive($training)) {
                        $amount = $training['e_amount'];
                        $type = 'earlybird';
                    } else {
                        $amount = $training['p_amount'];
                    }
                }elseif($request->type == 'part'){
                    $amount = $training['p_amount'] /2;
                }elseif($request->type == 'earlybird'){
                    if ($this->isEarlyBirdActive($training)) {
                        $amount = $training['e_amount'];
                        $type = 'earlybird';
                    } else {
                        $amount = $training['p_amount'];
                        $type = 'full';
                    }
                }else{
                    return back()->with('error', 'Invalid Payment Type selection');
                }
            }
            
            $type = $type ?? $request->type;
        }

        // inject facilitator details
        if($request->has('facilitator')){
            Session::put('facilitator', $request->facilitator);
            Session::put('facilitator_id', $request->facilitator_id);
            Session::put('facilitator_name', $request->facilitator_name);
            Session::put('facilitator_license', $request->facilitator_license);
        }

        $payment_modes = $this->getPaymentModes();

        if(auth()->user() && auth()->user()->email == 'davsong16@gmail.com'){
            $payment_modes = PaymentMode::all();
        }

        $isPackage = !empty($request->package) ? true : false;
        
        return view('checkout', compact('amount', 'training', 'type', 'payment_modes','modes','location', 'preferred_timing', 'trainingObject', 'isPackage'));
    }

    public function getModeAmount($mode,$type,$program){
        $amount = null;
        $program = json_decode($program);
        if(isset($program->show_modes) && $program->show_modes == 'yes'){
            $modes = $program->modes;
        }
        
        if(!empty($modes)){
            // check type
            if($type == 'full'){
                $amount = $modes->$mode;
            }
            if($type == 'part'){
                $amount = $modes->$mode/2;
            }
        }
    
        return $amount;
    }


    public function getPaymentModes(){
        $payment_modes = PaymentMode::where('status', 'active')->get();
        if(Session::has('facilitator_id')){
            $facilitator = User::find(Session::get('facilitator_id'));
            $mode_id = $facilitator?->payment_mode;

            if (!$facilitator) {
                Session::forget(['facilitator_id', 'facilitator_name', 'facilitator_license']);
            }
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
        $payment_type = $request->payment_type;
        $isPackage = $request->isPackage;

        if ($isPackage) {
            $couponCheck = Coupon::where('code', $request->code)->where('group_id', $request->pid)->first();
            $program = Group::find($request->pid);
        } else {
            $couponCheck = Coupon::where('code', $request->code)->where('program_id', $request->pid)->first();
            $program = Program::find($request->pid);
        }
        
        $couponData = CouponService::getCouponData($payment_type, $couponCheck, $isPackage, $request->price, $program, $request->email);
        
        if(isset($couponData['status']) && $couponData['status'] == true){
            return response()->json([
                'amount' => $couponData['discount'],
                'id' => $couponCheck->id,
                'code' => $couponCheck->code,
                'grand_total' => $request->price - $couponData['discount'],

            ]);
        }else{
            return null;
        }
    }


    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        if (BlacklistService::checkByValues([
            'email' => $request->email,
            'phone' => $request->phone,
        ])) {
            return back()->with('danger', 'BLTD: Something went wrong, Please contact Admin');
        }

        $template = Settings::first()->templateName->name;
        
        if ($request->user_program && $request->type == 'balance') {
            $data = DB::table('program_user')->find($request->user_program);

            if (!$data) {
                return back()->with('error', 'The referenced payment record no longer exists.');
            }

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

        $request['amount'] = \Session::get('exchange_rate') * $request['amount'];

        $type = $metadata = json_decode($request['metadata'], true);
        
        $pid = $type['pid'];
        $coupon_id = $type['coupon_id'];
        $facilitator_id = $type['facilitator'];
        $request['payment_type'] = $type['type'];
        $type['name'] = $request['name'];
        $type['phone'] = $request['phone'];
        
        $isPackage = $type['isPackage'];
        
        if($isPackage){
            $training = Group::where('id', $pid)->first();
            $programIds = $training?->programs->pluck('id')->toArray() ?? [];
            $couponCheck = Coupon::where('code', $request->coupon)->where('group_id', $pid)->first();

            $returnUrl = url('packages/' . $training->slug);
        }else{
            $training = Program::where('id', $pid)->first();
            $programIds = $training ? [(int) $training->id] : [];
            $couponCheck = Coupon::where('code', $request->coupon)->where('program_id', $pid)->first();
            $returnUrl = url('trainings/' . $training->slug);
        }

        $payment_mode = [];
        if($request->payment_mode){
            if($request->payment_mode == 0){
                $payment_mode = [
                    'name' => 'Bank Transfer',
                    'exchange_rate' => 1,
                ] ;
            }else{
                $paymentMode = PaymentMode::where('id', $request->payment_mode)->first();
                $payment_mode = [
                    'name' => $paymentMode->name,
                    'exchange_rate' => $paymentMode->exchange_rate,
                ];
            }
        }

        $metadata['payment_mode'] = $payment_mode;
        
        // check if this user already has these programs
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $check = Transaction::where('user_id', $user->id)
                ->whereIn('program_id', $programIds)
                ->count();
            
            
            if ($check > 0) {
                if (resolveAuthUser() && resolveAuthUser()->id === $user->id) {
                    return redirect(url('/dashboard'));
                }else{
                    return back()->with('error', 'You have already registered for this program');
                }
            }
        }

        // start implementation
        $t_type = $request->payment_mode == 0 ? 'Transfer' : 'Online';
        $transid = PaymentService::getReference();
        $invoiceId = PaymentService::getInvoiceId();
        $participant = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $calculateAmount = PaymentService::calculatePaymentBreakdown($training, $type['type'], $request->amount, $request->modes);
        $computedAmount = $calculateAmount['total_due'];
        
        // $expectedAmount
        if ($couponCheck) {
            // Apply coupon to amount
            $couponData = CouponService::getCouponData($type['type'], $couponCheck, $isPackage, $computedAmount, $training, $participant['email']);

            if ($couponData['status']) {
                if ($isPackage) {
                    $couponData['group_id'] = $training->id;
                } else {
                    $couponData['program_id'] = $training->id;
                }

                $couponData['email'] = $participant['email'];
                $couponData['transactionId'] = $transid;
                $couponData['isPackage'] = $isPackage;

                $computedAmount = $couponData['total_due'] ?? $computedAmount;

                $couponTransaction = CouponService::initiateCoupon($couponData);
            }
        }
        
        $balance = $computedAmount - $request->amount + ($couponData['amount'] ?? 0);
        
        $isFreeTraining = $request->payment_type == 'full' && $training->p_amount == 0 ? true : false;
        
        $transactionArray = [
            'email'             => $request->email,
            'type'              => $request->payment_type,
            'program_id'        => $training->id,
            'coupon_id'         => isset($couponData) && $couponData['status'] == 1 ? $couponData['coupon_id'] : null,
            'facilitator_id'    => null,
            'expected_amount' => $calculateAmount['total_due'] ?? null,
            'amount'            => $request->amount,
            "discount"          => $couponData['discount'] ?? 0,
            'transid'           => $transid,
            'invoice_id'        => $invoiceId,
            'payment_mode'      => $request->payment_mode,
            'preferred_timing'  => null,
            'name'              => $request->name,
            'phone'             => $request->phone,
            'is_package'        => $isPackage ?? 0,
            'status'            => 'initiated',
            'balance'           => $balance,
            't_type'            => $t_type,
            'program_ids'       => $programIds,
            'currency'          => "NGN",
            'currency_symbol'   => "₦",
            "coupon_code"       => isset($couponData) && $couponData['status'] == 1 ? $couponData['code'] : null,
            "coupon_amount"       => isset($couponData) && $couponData['status'] == 1 ? $couponData['discount'] : null,
            'location' => $request->location ?? null,
            'training_mode' => $request->modes ?? NULL,
            'exchange_rate' => $metadata['payment_mode']['exchange_rate'] ?? null,
            'meta' => $metadata,
        ];
        
        $transaction = PaymentService::logTransaction($transactionArray);
        
        // Free training, will sort later
        if ($isFreeTraining) {
            $data['balance'] = $balance;
            $data['programs'] = $transaction->allPrograms()->toArray();
            $data['payment_type'] = $transaction->type;

            $data['type'] = $request->payment_type;
            $data['message'] = $balance > 0 ? 'Part payment' : 'Full payment';
            $data['paymentStatus'] = $balance > 0 ? 0 : 1;

            $data['currency'] = $transaction->currency;
            $data['currency_symbol'] = $transaction->currency_symbol;
            $data['exchange_rate'] = $transaction->exchange_rate;
            $data['type'] = 'initial';
            $data['t_type'] = $t_type;
            $data['amount'] = $transaction->amount;
            $data['email'] = $participant['email'];
            $data['programName'] = $training->p_name;
            $data['programAbbr'] = $training->p_abbr;
            $data['name'] = $transaction->name;
            $data['transaction'] = $transaction;
            $data['program'] = $training;
            
            $this->sendWelcomeMail($data);
            if ($request->payment_type == 'full' && $training->p_amount == 0) {
                $this->sendWelcomeMail($data);
                
                // Login User in
                Auth::loginUsingId($data['user_id']);
                return view('thankyou', compact('data'));
            }


            $req = new PaymentController();
            $response = $req->handleGatewayCallback($transaction, 'zero-amount');
        }

        // handle gateway call back normally

        // Pay from wallet
        // Work on this later
        // if ($request->payment_mode == 'wallet') {
        //     $request['user_id'] = resolveAuthUser()->id;
        //     $request['p_id'] = $pid;
        //     $response = $this->payFromAccount($request, 'frontent-wallet');

        //     if (isset($response['status']) && $response['status'] == 'failed') {
        //         return redirect(url('trainings/' . $pid))->with('error', $response['message']);
        //     }

        //     if (isset($response['status']) && $response['status'] == 'success') {
        //         return redirect(route($response['route']))->with('message', $response['message']);
        //     }
        // }
        
        // Bank transfer
        if ($request->has('payment_mode') && $request->payment_mode == 0) {
            $extraCurrencies = PaymentService::getConvertedCurrency($transaction);
            
            $data = [
                'extraCurrencies' => $extraCurrencies,
                'transaction' => $transaction,
                'groups' => Group::with(['programs' => function ($q) {
                    $q->mainActivePrograms();
                }])
                ->isActive()
                ->latest()
                ->get()
            ];
            
            \Session::put('data', $data);
            return redirect(route('upload-proof-of-payment'));
        }

        // Create temp user and redirect

        try{
            $urlResponse = $this->queryProcessor($transaction);
            
            if($urlResponse['status']){
                return redirect()->away($urlResponse['url']);
            }else{
                return redirect($returnUrl)->with('error', $urlResponse['message'] ?? 'Something went wrong, Kindly try again!');
            }
        }catch(\Exception $e) {
            // dd($e->getMessage(), $e->getFile(), $e->getLine());
            \Log::info($e->getMessage());
            return redirect($returnUrl)->with('error', 'Something went while verifying payment, Kindly contact admin!');
        } 
    }


    public function queryProcessor($transaction, $query_only = null){
        $mode = $transaction->paymentMode;
        
        if(isset($mode) && !empty($mode)){
            if($mode->processor == 'paystack'){
                if(!empty($query_only)){
                    $urlResponse = app('App\Http\Controllers\PaymentProcessor\PaystackController')->query($transaction, $query_only);
                }else{
                    $urlResponse = app('App\Http\Controllers\PaymentProcessor\PaystackController')->query($transaction);
                }
            }
            
            if ($mode->processor == 'coinbase') {
                if (!empty($query_only)) {
                    $urlResponse = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->query($transaction,$query_only);
                } else {
                    $urlResponse = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->query($transaction);
                }
            }
        }
        // redirect away
        return $urlResponse;
    }
    
    public function verifyProcessor($reference, $temp, $verify_only=false){
        
        $mode = PaymentMode::find($temp->payment_mode);
            
        if (isset($mode) && !empty($mode)) {
            if ($mode->processor == 'paystack') {
                $status = app('App\Http\Controllers\PaymentProcessor\PaystackController')->verify($reference, $mode);
            }
            if ($mode->processor == 'coinbase') {
                $status = app('App\Http\Controllers\PaymentProcessor\CoinbaseController')->verify($reference, $mode, $temp);
            }
        }
        
        if (isset($status) && $status == 'success') {
            return $status;
        }else{
            if(!empty($verify_only)){
                return 'failed';
            }
            return back()->with('error', 'Payment was not succusful');
        }
    }

    public function handleGatewayCallback(Request $request, $is_zero_coupon=null)
    {
        $transaction = null;
        $balance_payment = DB::table('program_user')->whereNotNull('balance_transaction_id')->where('balance_transaction_id', $request->reference)->first();
        
        if($balance_payment){
            //process as balance
            $status = $this->verifyProcessor($request->reference, $balance_payment);
            if ($status === 'success') {
                return redirect(route('home'))->with('message', 'Payment successful');
            }

            return redirect(route('home'))->with('error', 'Payment was not successful');
        }else{
            $transaction = TempTransaction::where('transid', $request->reference)->where('status','initiated')->where('t_type', 'Online')->first();
            
            // http://127.0.0.1:8000/payment/callback?trxref=202509030913-23474548&reference=202509030913-23474548

            // if(!$temp){
            //     // Wallet payment
            //     $temp = Wallet::where('transaction_id', $request->reference)->first();

            //     if($temp){
            //         $temp->payment_mode = $temp->provider;
            //         $status = $this->verifyProcessor($request->reference, $temp, $temp->gateway, 'verify-only');

            //         if(!empty($status) && $status == 'success'){
            //             $data = ['status' => 'approved'];

            //             $update = app('App\Http\Controllers\WalletController')->updateWallet($request->reference,$data);

            //             if(isset($update) && $update == 'success'){
            //                 return redirect(route('home'))->with('message', number_format($temp->amount). ' Account TopUp successful');
            //             }
            //         }
            //     }

            // }
            
            if (!$transaction) {
                return redirect(route('home'));
            } else {
                if(is_null($is_zero_coupon)){
                    $status = $this->verifyProcessor($request->reference, $transaction);
                }else{
                    $status = 'success';
                }
            }
        }
        
        if($status == 'success'){
            if ($balance_payment) {
                // Handle user balance payment in app
            }
        }
        
        $template = Settings::first()->templateName->name;
        
        if(! $transaction) {
            return redirect(route('home'));
        }

        if($transaction->is_package){
            $program = Group::where('id', $transaction->program_id)->first();
        }else{
            $program = Program::where('id', $transaction->program_id)->first();
        }
        
        if($template == 'contai'){
            if(isset($transaction) && !empty($transaction)){
                // abstracted this
                $balance = $transaction->balance;
                // Compare
                if($transaction->type == 'full'){
                    $payment_type = 'Full';
                    $message = 'Full payment';
                    $coupon_applied = $transaction->coupon ?? NULL;
                    $paymentStatus =  1;                        
                }elseif($transaction->type == 'part'){
                    $payment_type = 'Part';
                    $message = 'Part payment';
                    $coupon_applied = $transaction->coupon ?? NULL;
                    $paymentStatus =  0;
                }elseif($transaction->type == 'earlybird'){
                    $payment_type = 'Full';
                    $message = 'Earlybird payment';
                    $paymentStatus =  1;
                }elseif($transaction->type == 'balance'){
                // Do nothing, something must have gone wrong
                }
                
                try {
                    DB::beginTransaction();

                    PaymentService::createUserAndAttachPrograms($transaction);
                    $transaction = $transaction->fresh();

                    PaymentThread::create([
                        'program_id'   => $transaction->program_id,
                        'admin_id'      => auth()->guard('admin')->user()->id ?? null,
                        'user_id'      => $transaction->user_id,
                        'payment_id'   => $transaction->id,
                        'transaction_id' => self::getReference('PYTHRD'),
                        't_type'       => strtolower($transaction->t_type),
                        'parent_transaction_id' => $transaction->transid,
                        'amount'       => $transaction->amount,
                    ]);
                    
                    if (!empty($transaction->coupon_id)) {
                        $couponTransaction = CouponService::getCouponTransactionFromTransaction($transaction);
                        CouponService::completeCoupon($couponTransaction);
                    }
                    $transaction->update([
                        'status' => 'complete',
                    ]);
                    
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    $rand = rand(1111,9999);
                    logger()->info(['Payment Error: '. $rand => $th->getMessage()]);

                    return redirect(route('home'))->with('error', 'An error occured, please contact Support with this error code: '. $rand);
                }
                // to this
                // Send email
                $data['balance'] = $balance;
                $data['programs'] = $transaction->allPrograms()->toArray();
                $data['payment_type'] = $transaction->type;

                $data['message'] = $message;
                $data['paymentStatus'] = $paymentStatus;

                $data['currency'] = $transaction->currency;
                $data['currency_symbol'] = $transaction->currency_symbol;
                $data['exchange_rate'] = $transaction->exchange_rate;
                $data['type'] = 'initial';
                $data['t_type'] = $transaction->meta['payment_mode']['name'] ?? 'Online';
                $data['amount'] = $transaction->amount;
                $data['email'] = $transaction->email;
                $data['programName'] = $program->p_name;
                $data['programAbbr'] = $program->p_abbr;
                $data['name'] = $transaction->name;
                $data['transaction'] = $transaction;
                $data['program'] = $program;
                
                $this->sendWelcomeMail($data);

                // Login User in
                Auth::loginUsingId($transaction->user_id);

                //include thankyou page
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
        $allDetails['currency'] = $existingTransaction?->currency ?? \Session::get('currency');
        $allDetails['currency_symbol'] = $existingTransaction?->currency_symbol ?? \Session::get('currency_symbol');
        $allDetails['message'] = $this->dosubscript1($balance);
        $allDetails['paymentStatus'] = $this->paymentStatus($balance);
        $total_amount_paid = $request->amount + $existing;
        $allDetails['amount'] = $request->amount;

        if ($request->type == 'balance') {
            $allDetails['balance_transaction_id'] = $this->getReference('USER_TOP_UP_BAL');
            $allDetails['transaction_id'] = $existingTransaction?->transid;
            $allDetails['invoice_id'] = $existingTransaction?->invoice_id;
            $allDetails['balance'] = ($existingTransaction?->balance ?? 0) - $request->amount;

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
                'amount'       => $total_amount_paid,

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
            if (! $payment) {
                return back()->with('error', 'Unable to locate completed payment record.');
            }
            PaymentThread::create([
                'program_id' => $allDetails['program_id'],
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transid,
                'parent_transaction_id' => $payment->transid,
                't_type' => 'wallet',
                'amount' => $payment->amount
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
