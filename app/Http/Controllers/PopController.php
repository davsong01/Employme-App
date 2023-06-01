<?php

namespace App\Http\Controllers;

use PDF;
use Carbon;
use App\Pop;
use App\User;
use App\Coupon;
use App\Program;
use App\Location;
use App\Settings;
use App\Mail\POPemail;
use App\Mail\Welcomemail;
use App\TempTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class PopController extends Controller
{
    public function index(){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        $transactions = Pop::with('program','user','temp')->Ordered('date', 'DESC')->get();
        $i = 1;
        dd($transactions);
        return view('dashboard.admin.payments.pop', compact('transactions', 'i') );
    }

    public function create(){
        $trainings = Program::select('id', 'p_end', 'p_name','p_amount','close_registration')->where('id', '<>', 1)->where('close_registration', 0)->where('p_end', '>', date('Y-m-d'))->ORDERBY('created_at', 'DESC')->get();
        // $locations = Location::select('title')->distinct()->get();
        
        return view('pop')
            ->with('trainings', $trainings);
            // ->with('locations', $locations);
    }

    public function store(Request $request){
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required | numeric',
            'bank' => 'sometimes',
            'amount' => 'required | numeric',
            'training' => 'required | numeric',
            'currency' => 'sometimes',
            'currency_symbol' => 'sometimes',
            'coupon_id' => 'nullable',
            'date' => 'date',
            'file' => 'required|max:2048|image',
        ]);
       
        // Remove data from session
        \Session::forget(['data']);

        //handle file
        $file = $data['name'].'-'.date('D-s');
        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $request->file('file')->storeAs('pop', $file.'.'.$extension  ,'uploads');
        $date = \Carbon\Carbon::parse($data['date'] . ' ' . now()->format('h:i:s'));

        // Check if already uploaded same pop
        // $popCheck = Pop::whereEmail($data['email'])->whereAmount($data['amount'])->whereProgramId($data['training'])->count();
        $popCheck = Pop::whereEmail($data['email'])->whereProgramId($data['training'])->count();
      
        if ($popCheck > 0) {
            return back()->with('error', 'You have already uploaded proof of payment for this training and with the same amount, kindly wait while an administrator approves your request');
        }

        if (isset($user) && !empty($user)) {
            $check = DB::table('pop')->where(['user_id' => $user, 'program_id' => $data['training']])->where('balance', '<', 1)->count();

            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }

        // Check if user already paid for same program
        $user = User::whereEmail($data['email'])->value('id');
        if(isset($user) && !empty($user)){
            $validate = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training']]);
            $check = $validate->where('balance', '<', 1)->count();
            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }else{
            $type = 'Fresh Payment' ?? null;
        }
       
        // Get temp transaction 
        $temp = TempTransaction::where('email', $data['email'])->where('program_id', $data['training'])->first();
        $data['location'] = $temp->location ?? null;
        $data['training_mode'] = $temp->training_mode ?? null;

        try{
            //Store new pop
            $pop = Pop::create([
                'name' => $data['name'],
                'email' =>  $data['email'],
                'phone' =>  $data['phone'],
                'bank' =>  $data['bank'],
                'coupon_id' =>  $data['coupon_id'],
                'amount' =>  $data['amount'],
                'program_id' =>  $data['training'],
                'currency' =>  $data['currency'],
                'currency_symbol' =>  $data['currency_symbol'],
                'is_fresh' => $type ?? null,
                'temp_transaction_id' => $temp->id ?? null,
                'location' =>  $data['location'] ?? null,
                'date' =>  $date,
                'file' => $filePath,
            ]);

            //Prepare Attachment
            $data['pop'] = base_path() . '/uploads' . '/' . $filePath;
            $data['training'] = Program::where('id', $data['training'])->value('p_name');
            $data['type'] = 'pop';
            $data['email'] = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
            $this->sendWelcomeMail($data);
            // Mail::to(Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL'))->send(new POPemail($data));

        }catch(\Exception $e) {
            // dd($e->getMessage());
            // return back()->with('error', $e->getMessage());
        }
        
        //Send mail to admin
        return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
           
    }

    public function show(Pop $pop){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        //Check if program exist for the incoming training
        // $user = User::whereEmail($data['email'])->value('id');
        if (isset($pop->user) && !empty($pop->user)) {
            $check = DB::table('program_user')->where(['user_id' => $pop->user->id, 'program_id' => $pop->program_id])->where('balance', '<', 1)->count();
           
            if ($check > 0) {
                return back()->with('error', 'Participant already registered for this training!');
            }
        }

        // If balance
        // Try to see if this is balance payment
        $existingTransaction = $this->getExistingTransactionAndBalance($pop);
        
        // dd($existingTransaction);
        $allDetails = [];
        if(isset($existingTransaction) && $existingTransaction['balance'] > 0){
            $allDetails['balance_transaction_id'] = $this->getReference('SYS_ADMIN_BAL');
            $allDetails['existingTransaction'] =  $existingTransaction['transaction'];
            $allDetails['programFee'] = $pop->program->e_amount > 0 ? $pop->program->e_amount : $pop->program->p_amount;
            
            if($pop->amount > $existingTransaction['balance']){
                return back()->with('error','User has already paid: '. $existingTransaction['transaction']->t_amount.'; Balance payment should be '. $existingTransaction['balance']); 
            }
            $allDetails['amount'] = $existingTransaction['transaction']->t_amount + $pop->amount;
            
            if($pop->amount >= $existingTransaction['balance']){
                $balance = 0;
            }else{
                $balance = $existingTransaction['balance'] - $pop->amount;
            }
            
            $allDetails['programFee'] = $pop->program->t_amount;
            $allDetails['program_id'] = $pop->program_id;
            $allDetails['programName'] = $pop->program->p_name;
            $allDetails['programAbbr'] = $pop->program->p_abbr;
            $allDetails['location'] = $pop->location;
            $allDetails['bookingForm'] = $pop->program->booking_form;
            $allDetails['programEarlyBird'] = $pop->program->e_amount;
            $allDetails['location'] = $pop->location;
            $allDetails['name'] = $pop->name;
            $allDetails['email'] = $pop->email;
            $allDetails['phone'] = $pop->phone;
            $allDetails['password'] = bcrypt('12345');
            $allDetails['t_type'] = $pop->bank;
            $allDetails['currency'] = $pop->currency;
            $allDetails['currency_symbol'] = $pop->currency_symbol;
            $allDetails['date'] = $pop->date;
            $allDetails['role_id'] = 'Student';
            $allDetails['message'] = $this->dosubscript1($balance);
            $allDetails['paymentStatus'] = $this->paymentStatus($balance);
            $allDetails['balance'] = $balance;
            $allDetails['current_paid_amount'] = $pop->amount;
            $allDetails['invoice_id'] = $existingTransaction['transaction']->invoice_id;
            $allDetails['transaction_id'] = $existingTransaction['transaction']->transid;

            $user = $this->updateUserDetails($allDetails);
            $data = $this->updateOrCreateTransaction($user, $allDetails);
           
        }else{
           
            if($pop->program->e_amount > 0){ 
                if($pop->program->e_amount > $pop->amount ){
                    $allDetails['programFee'] = $pop->program->p_amount;
                } else {
                    $allDetails['programFee'] = $pop->program->e_amount;
                }
            }else{
                $allDetails['programFee'] = $pop->program->p_amount;
            }

            if ($pop->amount > $allDetails['programFee']) {
                return back()->with('error', 'User cannot pay more than program fee, you may need to check early bird payment');
            }
            
            // dd($allDetails['programFee'], 'fd', $pop->program);
            if(isset($pop->temp->training_mode) && !empty($pop->temp->training_mode)){
                $mode = $pop->temp->training_mode;
                $amount = app('App\Http\Controllers\PaymentController')->getModeAmount($mode,$pop->temp->type,$pop->program);
                if ($amount) {
                    $expectedAmount = $amount;

                    $modes = $pop->program->modes;
                    $modes = json_decode($modes, true);
                    // dd($temp->training_mode);
                    $amt = $modes[$pop->temp->training_mode];

                    $balance = $amt - $expectedAmount;
                }
            } else {
                $balance = $allDetails['programFee'] - $pop->amount;
            }

            $allDetails['transaction_id'] = $this->getReference('SYS_ADMIN');
            $allDetails['program_id'] = $pop->program_id;
            $allDetails['programName'] = $pop->program->p_name;
            $allDetails['programAbbr'] = $pop->program->p_abbr;
            $allDetails['location'] = $pop->location;
            $allDetails['bookingForm'] = $pop->program->booking_form;
            $allDetails['programEarlyBird'] = $pop->program->e_amount;
            $allDetails['invoice_id'] =  $this->getInvoiceId($pop->user->id ?? null);
            $allDetails['location'] = $pop->location;
            $allDetails['name'] = $pop->name;
            $allDetails['email'] = $pop->email;
            $allDetails['phone'] = $pop->phone;
            $allDetails['password'] = bcrypt('12345');
            $allDetails['amount'] = $pop->amount;
            $allDetails['t_type'] = $pop->bank;
            $allDetails['currency'] = $pop->currency;
            $allDetails['currency_symbol'] = $pop->currency_symbol;
            $allDetails['date'] = $pop->date;
            $allDetails['role_id'] = 'Student';
            $allDetails['message'] = $pop->program->e_amount > 0 ?  $this->dosubscript2($balance) : $this->dosubscript1($balance);
            $allDetails['paymentStatus'] = $this->paymentStatus($balance);
            $allDetails['paymenttype'] = $this->paymentStatus(0);
            $allDetails['balance'] = $balance;
            $allDetails['training_mode'] = $pop->temp->training_mode ?? null;
            $allDetails['t_type'] = $pop->temp->type ?? null;
            
            $user = $this->updateUserDetails($allDetails);
           
            // request->coupon, $request->email, $pid, $request['amount']
            // $temp = TempTransaction::where(['user'])
            $coupon = Coupon::where('id',$pop->coupon_id)->first();
            
            if($coupon){
                $user->coupon = $coupon;
                $response = $this->verifyCoupon($user, $pop->program_id, 'admin');

                if($response['grand_total'] <= 0){
                    $allDetails['message'] = $this->dosubscript1(0);
                    $allDetails['balance'] = 0;
                    $allDetails['paymentStatus'] = $this->paymentStatus(0);
                    $allDetails['paymenttype'] = $this->paymentStatus(0);
                    $allDetails['coupon_amount'] = $response ['amount'];
                    $allDetails['coupon_id'] = $response ['id'];
                    $allDetails['coupon_code'] = $response ['code'];
                }
            }
          
            $data = $this->updateOrCreateTransaction($user, $allDetails);
        }

        //determine the program details
        $data = [
            'name' => $allDetails['name'],
            'email' => $allDetails['email'],
            'bank' => $allDetails['t_type'],
            'amount' => $allDetails['amount'],
            'invoice_id' =>  $allDetails['invoice_id'],
            'transid' => $allDetails['transaction_id'],
            'programFee' => $allDetails['programFee'],
            'programName' => $allDetails['programName'],
            'programAbbr' => $allDetails['programAbbr'],
            'balance' => $balance,
            'message' => $allDetails['message'],
            't_type' => $allDetails['t_type'],
            'currency' => $allDetails['currency'],
            'currency_symbol' => $allDetails['currency_symbol'],
            'created_at' => $allDetails['date'],
            'training_mode' => $allDetails['training_mode'] ?? null,
            'location' => $allDetails['location'],

            'booking_form' => !is_null($allDetails['bookingForm']) ? base_path() . '/uploads' . '/' . $allDetails['bookingForm'] : null,
        ];

        $pdf = PDF::loadView('emails.printreceipt', compact('data'));
        // return view('emails.receipt', compact('data'));
        $this->sendWelcomeMail($data);
        $pop->delete();
        return redirect(route('payments.index'))->with('message', 'Student added succesfully'); 

    }

    public function updateOrCreateTransaction($user, $allDetails){
       
        if(isset($allDetails['existingTransaction'])){
            $existingTransaction = DB::table('program_user')->where('id',$allDetails['existingTransaction']->id)
                ->update([
                    't_amount' => $allDetails['amount'],
                    't_type' => $allDetails['t_type'],
                    't_location' => $allDetails['location'],
                    'paymentStatus' => $allDetails['paymentStatus'],
                    // 'training_mode' => $allDetails['training_mode'] ?? null,
                    'balance' => $allDetails['balance'],
                    'currency' => $allDetails['currency'],
                    'currency_symbol' => $allDetails['currency_symbol'],
                    'balance_transaction_id' => $allDetails['balance_transaction_id'],
                    'balance_paid' => $allDetails['date'],
                    'balance_amount_paid' => $allDetails['current_paid_amount'],
                    'coupon_id' => $allDetails['coupon_id'] ?? null,
                    'coupon_amount' => $allDetails['coupon_amount'] ?? null,
                    'coupon_code' => $allDetails['coupon_code'] ?? null,
                    'training_mode' => $allDetails['training_mode'] ?? null,
                ]);
        }else{
            
            $programUser = $user->programs()->attach($allDetails['program_id'], [
                't_amount' => $allDetails['amount'],
                't_type' => $allDetails['t_type'],
                't_location' => $allDetails['location'],
                'paymentStatus' => $allDetails['paymentStatus'],
                'balance' => $allDetails['balance'],
                'transid' =>  $allDetails['transaction_id'],
                'invoice_id' =>  $allDetails['invoice_id'],
                'currency' => $allDetails['currency'],
                'currency_symbol' => $allDetails['currency_symbol'],
                'created_at' => $allDetails['date'],
                'coupon_id' => $allDetails['coupon_id'] ?? null,
                'coupon_amount' => $allDetails['coupon_amount'] ?? null,
                'coupon_code' => $allDetails['coupon_code'] ?? null,
                'training_mode' => $allDetails['training_mode'] ?? null,
            ]);
        }
        // Update existing payment if 
        
        return $allDetails;

    }

    public function updateUserDetails($allDetails){
        $user = User::where('email', $allDetails['email'])->first();
        if (!$user) {
            //save to database
            $user = User::Create([
                'name' => $allDetails['name'],
                'email' => $allDetails['email'],
                't_phone' => $allDetails['phone'],
                'password' => bcrypt('12345'),
                'role_id' => $allDetails['role_id'],
            ]);
        }else{
            $user->update([
                'name' => $allDetails['name'],
                't_phone' => $allDetails['phone'],
            ]);
        }
       
        return $user;
    }

    public function getExistingTransactionAndBalance($pop){
        if(isset($pop->user->id)){
            $existingTransactions = DB::table('program_user')->where(['user_id' => $pop->user->id, 'program_id' => $pop->program_id])->first();
        }else{
            return [
                'balance' => 0,
                'transaction' => null,
            ];

        }

        $programAmount = $pop->program->e_amount > 0 ? $pop->program->e_amount : $pop->program->p_amount;      
      
        if(isset($existingTransactions) && !empty($existingTransactions)){
            // $balance = $programAmount - $existingTransactions->t_amount;
            $balance = $existingTransactions->balance;
            
        }else{
            $balance = 0;
        }
              
        return [
            'balance'=> $balance,
            'transaction' => $existingTransactions,
        ];
    }

    public function reconcile(){
        $users = User::where('role_id', 'Student')->get();
        foreach($users as $user){
            //get user extra details
            $user->programs()->attach($user->program_id, [
                    'created_at' =>  $user->created_at,
                    't_amount' => $user->t_amount,
                    't_type' => $user->t_type,
                    't_location' => $user->location,
                    'paymentStatus' => $user->paymentStatus,
                    'balance' => $user->balance,
                    'invoice_id' =>  $user->invoice_id,
                ] );
            
        }
        return back()->with('message', 'All user details have been moved succesfully');
    }

    public function getfile($filename){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        $realpath = base_path() . '/uploads/pop'. '/' .$filename;
        return response()->download($realpath);
    }

    public function destroy(Pop $pop){
        unlink( base_path() . '/uploads'.'/'. $pop->file);
        $pop->delete();
        return back()->with('message', 'Pop succesfully deleted');
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
        }elseif ($balance > 0){
        } return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }else return 'Part payment';
    }

}