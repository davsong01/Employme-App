<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\Models\Pop;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Wallet;
use App\Models\Program;
use App\Mail\Welcomemail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (canUserAccessPermission(['payments.index']) && !checkRoleHas(['Student'])) {
            $transactions = TempTransaction::with('group:id,p_name','program:id,p_name,modes,locations,allow_preferred_timing', 'paymentthreads','user:id,name,email,phone,last_login','coupon')->orderBy('created_at', 'DESC');
            
            if (!empty($request->transid)) {
                $transactions = $transactions->where('transid', $request->transid);
            }

            if (!empty($request->email)) {
                $transactions = $transactions->where('email', $request->email);
            }

            if (!empty($request->name)) {
                $transactions = $transactions->where('name','LIKE', "%{$request->name}%");
            }

            if (!empty($request->phone)) {
                $transactions = $transactions->where('phone', $request->phone);
            }

            if (!empty($request->type)) {
                $transactions = $transactions->where('type', $request->type);
            }

            if (!empty($request->channel)) {
                $transactions = $transactions->where('t_type', $request->channel);
            }

            if (!empty($request->program_id)) {
                $transactions = $transactions->where('program_id', $request->program_id);
            }

            if (!empty($request->package_id)) {
                $transactions = $transactions->where('program_id', $request->package_id);
            }

            if (!empty($request->coupon_id)) {
                $transactions = $transactions->where('coupon_id', $request->coupon_id);
            }
            
            if (!empty($request->from) && !empty($request->to)) {
                $transactions = $transactions->whereBetween('created_at', [$request->from." 00:00:00", $request->to. " 23:59:59"]);
            }

            if (!empty($request->status)) {
                $transactions = $transactions->where('status', $request->status);
            }
            

            $records = $transactions->count();
            
            $transactions = $transactions->paginate(50);

            $pops = Pop::with('program')->Ordered('date', 'DESC')->get();
            $allPrograms = Program::select('id', 'p_name', 'p_end', 'close_registration', 'created_at')->orderBy('created_at', 'DESC')->get();
            $allPackages = Group::select('id', 'p_name', 'p_end', 'created_at')->orderBy('created_at', 'DESC')->get();
            $allCoupons = Coupon::latest()->get();


            return view('dashboard.admin.payments.index', compact('transactions', 'pops','records','allPrograms', 'allPackages', 'allCoupons'));
        }
        
        if (checkRoleHas(['Student'])){
            $transactiondetails = Transaction::with('paymentthreads')->where('user_id', '=', resolveAuthUser()->id)->orderBy('created_at', 'DESC')->get();

            foreach ($transactiondetails as $details) {
                $details->programs = Program::select('p_name', 'p_amount')->where('id', $details->program_id)->get()->toArray();
                $details->p_name = $details->programs[0]['p_name'];
                $details->p_amount = $details->programs[0]['p_amount'];
            }
            
            return view('dashboard.student.payments.index', compact('transactiondetails'));
        }
    }

    public function proofOfPaymentHistory(Request $request)
    {
        $i = 1;
        if(!checkRoleHas(['Facilitator','Admin','Grader'])){
            return back();
        }
        
        $pops = Pop::with('program:id,p_name,p_amount,e_amount,p_end,close_registration')->Ordered('date', 'DESC')->get();
        
        $programs = Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')
            ->doesntHave('children')
            ->where('id', '<>', 1)
            // ->where('close_registration', 0)
            // ->where('p_end', '>', date('Y-m-d'))
            ->orderBy('created_at', 'DESC')
            ->get();

        $packages = Group::isActive()->with(['programs' => function ($q) {
            $q->mainActiveProgramsWithIsClosed();
        }])->get();
        // dd($pops);
        
        return view('dashboard.admin.payments.popfull', compact('i', 'pops','programs','packages'));

    }
    
    public function paymentHistory()
    {
        if(checkRoleHas(['Admin'])) {
            $wallets = app('App\Http\Controllers\WalletController')->getWalletHistory();
            $balance = app('App\Http\Controllers\WalletController')->getGlobalWalletBalance();
        }else{
            abort(404);
        }

        return view('dashboard.admin.payments.wallets', compact('wallets', 'balance'));
    }

    public function approveWalletTransaction($wallet_id){
        $wallet = Wallet::where('id', $wallet_id)->update([
            'status' => 'approved',
            'admin_id' => resolveAuthUser()->id
        ]);

        return back()->with('message', 'TopUp successfully Approved');
    }

    public function deleteWalletTransaction(Wallet $wallet_id)
    {
        $this->deleteImage('pop/'. $wallet_id->proof_of_payment);
       
        $wallet_id->delete();
        return back()->with('message', 'Pop succesfully deleted');
    }

    public function edit($id)
    {
        // $transaction = DB::table('program_user')->whereId($id)->first();
        $transaction = TempTransaction::with('group:id,p_name', 'program:id,p_name,modes,locations,allow_preferred_timing', 'paymentthreads', 'user:id,name,email,phone,last_login', 'coupon')->whereId($id)->first();

        $modes =  (isset($program_details->modes) && !empty($program_details->modes)) ? json_decode($program_details->modes) : [];
        $locations =  (isset($program_details->locations) && !empty($program_details->locations)) ? json_decode($program_details->locations) : [];
        // determine balance

        $checks = [
            'payments.edit',
        ];

        $permissions = canUserAccessPermission($checks);

        if ($permissions['payments.edit']) {
            return view('dashboard.admin.transactions.partial_edit', compact('transaction', 'locations', 'modes'));
        }
        
        return back();
    }

    public function show(Request $request, $id)
    {
        $transaction = TempTransaction::where('id', $id)->first();
        
        if(checkRoleHas(['Admin'])) {
            try {
                $data = $this->prepareTrainingDetails($transaction->related, $transaction, $transaction->amount);
                
                $data['currency'] = $transaction->currency;
                $data['currency_symbol'] = $transaction->currency_symbol;
                $data['exchange_rate'] = $transaction->exchange_rate;

                $data['type'] = 'initial';
                $data['name'] = $transaction->user->name;
                $data['transaction'] = $transaction;
                $data['program'] = $transaction->related;

                $data['payment_type'] = ucfirst($transaction->type);
                $data['amount'] = $transaction->amount;
                $data['message'] = ucfirst($transaction->type). ' payment';

                $this->sendWelcomeMail($data);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with('message', 'Receipt sent succesfully');
        } else return back();
    }

    public function printReceipt($id)
    {
        $transaction = TempTransaction::with(['coupon', 'program'])->where('id', $id)->first();
        
        if (checkRoleHas(['Student'])){
            if (!$transaction) {
                return back()->with('warning', 'Unauthorized Action');
            }
            if ($transaction->user_id <> resolveAuthUser()->id) {
                return back()->with('warning', 'Unauthorized Action');
            }
        }

        //generate pdf from receipt view
        $pdf = PDF::loadView('emails.printreceipt', compact('transaction'));
        
        return view('emails.printreceipt', compact('transaction'));
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance)
    {
        if ($balance <= 0) {
            return 'Full payment';
        }
        return 'Part payment';
    }
    //return payment status
    private function paymentStatus($balance)
    {
        if ($balance <= 0) {
            return 1;
        }
        return 0;
    }
    //return message for if earlybird is not checked
    private function dosubscript2($balance)
    {
        if ($balance <= 0) {
            return 'Earlybird payment';
        }
        return 'Part payment';
    }

    public function update(Request $request, $id)
    {
        if(!canUserAccessPermission(['payments.edit'])){
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this resource',
                'transaction_id' => $id,
            ]);
        }

        $transaction = TempTransaction::with('user', 'program')->whereId($id)->first();
        
        $user = $transaction->user;
        $programFee = $request->program_amount;

        //check amount against payment
        if ($request->has('training_mode') && !empty($request->training_mode)) {
            $modes =  (isset($transaction->program->modes) && !empty($transaction->program->modes)) ? json_decode($transaction->program->modes, true) : [];
            if (!empty($modes)) {
                $programFee = $modes[$transaction->training_mode];
            }
        }

        $balance = $transaction->balance ?? 0;
        
        // Checks for coupon code
        // && $request->coupon_id != $transaction->coupon_id
        if ($request->has('coupon_id') && !empty($request->coupon_id)) {
            $request['email'] = $transaction->user->email;
            $coupon = Coupon::whereId($request->coupon_id)->first();
            $request['coupon'] = $coupon->code;
            $request2 = (object) $request->all();

            $response = $this->verifyCoupon($request2, $transaction->program_id);

            if (isset($response['amount'])) {
                // make this amount already paid for the student
                $amount = $transaction->amount + $response['amount'];
                $balance = $balance - $amount;

                $transaction->update([
                    'coupon_id' => $response['id'],
                    'coupon_amount' => $response['amount'],
                    'coupon_code' => $response['code'],
                ]);
                $this->updateCouponStatus($transaction->user->email, $response['id'], $transaction->program_id);
            }
        }


        $this->dosubscript1($balance);
        $paymentStatus =  $this->paymentStatus($balance);
        $newamount = $request->amount;
        
        if($balance > 1){

        }

        if ($newamount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Student cannot pay more than program fee',
                'transaction_id' => $id,
            ]);
        }

        // if ($balance < 1) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Balance is not required',
        //         'transaction_id' => $id,
        //     ]);
        // }
        
        // complete this later
        if ($request->funds_source == 'Wallet') {
            $account_balance = $user->account_balance;

            if($account_balance < $request->amount){
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient funds in user account balance',
                    'transaction_id' => $id,
                ]);
            }

            $t_type = 'wallet';
            
            $wallet['amount'] = abs($request->amount);
            $wallet['transaction_id'] = $transaction->transid ?? $transaction->invoice_id;
            $wallet['type'] = 'debit';
            $wallet['method'] = 'wallet';
            $wallet['provider'] = 'ADMIN TOPUP';
            $wallet['status'] = 'approved';
            $wallet['user_id'] = $user->id;
            $wallet['admin_id'] = resolveAuthUser()->id;

            app('App\Http\Controllers\WalletController')->logWallet($wallet);
        }else{
            $t_type = 'wallet';

            $wallet['amount'] = abs($request->amount);
            $wallet['transaction_id'] = $transaction->transid ?? $transaction->invoice_id;
            $wallet['type'] = 'credit';
            $wallet['method'] = 'offline';
            $wallet['provider'] = 'ADMIN TOPUP';
            $wallet['status'] = 'approved';
            $wallet['user_id'] = $user->id;
            $wallet['admin_id'] = resolveAuthUser()->id;

            app('App\Http\Controllers\WalletController')->logWallet($wallet);

            // log a debit
            $wallet['amount'] = abs($request->amount);
            $wallet['transaction_id'] = $transaction->transid ?? $transaction->invoice_id;
            $wallet['type'] = 'debit';
            $wallet['method'] = 'offline';
            $wallet['provider'] = 'ADMIN TOPUP';
            $wallet['status'] = 'approved';
            $wallet['user_id'] = $user->id;
            $wallet['admin_id'] = resolveAuthUser()->id;

            app('App\Http\Controllers\WalletController')->logWallet($wallet);
        }

        //update the program table here @ column fully paid or partly paid
        $bData = [
            'amount' => $request->amount,
            't_type' => $request->funds_source ??  $transaction->t_type,
        ];
        
        $response = PaymentService::handleBalancePayment($transaction, $transaction->balance, $bData);

        $transaction = $transaction->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'transaction_id' => $id,
            'new_amount' => number_format($transaction->amount),
            'new_balance' => number_format($transaction->balance)
        ]);
    }

    public function destroy($id)
    {
        DB::table('program_user')->whereId($id)->delete();
        return back()->with('message', 'Transaction has been deleted forever');
    }
}
