<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\Pop;
use App\Role;
use App\User;
use App\Coupon;
use App\Program;
use App\Transaction;
use App\Models\Wallet;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
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
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $transactions = Transaction::with('program:id,p_name,modes,locations,allow_preferred_timing','user:id,name,email,t_phone')->orderBy('created_at', 'DESC');
            
            $i = 1;
            
            if (!empty($request->email)) {
                $transactions = $transactions->whereHas('user', function ($query) use ($request) {
                    $query->where('email', $request->email);
                });
            }

            if (!empty($request->name)) {
                $transactions = $transactions->whereHas('user', function ($query) use ($request) {
                    $query->where('name','LIKE', "%{$request->name}%");
                });
            }

            if (!empty($request->phone)) {
                $transactions = $transactions->where('user', function ($query) use ($request) {
                    $query->where('t_phone', $request->phone);
                });
            }
            // dd($request->type);
            if (!empty($request->type)) {
                $transactions = $transactions->where('t_type', $request->type);
            }

            if (!empty($request->program_id)) {
                $transactions = $transactions->where('program_id', $request->program_id);
            }
            
            if (!empty($request->from) && !empty($request->to)) {
                $transactions = $transactions->whereBetween('created_at', [$request->from." 00:00:00", $request->to. " 23:59:59"]);
            }

            $records = $transactions->count();
            $transactions = $transactions->paginate(50);
            $types = Transaction::select('t_type')
            ->distinct()
            ->whereNotNull('t_type') 
            ->whereNotIn('t_type',['0']) 
            ->get();

            $pops = Pop::with('program')->Ordered('date', 'DESC')->get();
            $allPrograms = Program::select('id', 'p_name', 'p_end', 'close_registration', 'created_at')->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.payments.index', compact('transactions', 'i', 'pops','records','allPrograms','types'));
        }
        if (!empty(array_intersect(teacherRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            return back();
        }
        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {

            $transactiondetails = Transaction::with('paymentthreads')->where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->get();

            foreach ($transactiondetails as $details) {
                $details->programs = Program::select('p_name', 'p_amount')->where('id', $details->program_id)->get()->toArray();
                $details->p_name = $details->programs[0]['p_name'];
                $details->p_amount = $details->programs[0]['p_amount'];
            }
            // dd($transactiondetails->balance);
            return view('dashboard.student.payments.index', compact('transactiondetails'));
        }
    }

    public function proofOfPaymentHistory(Request $request)
    {
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {;

            $pops = Pop::with('program')->Ordered('date', 'DESC')->get();

            return view('dashboard.admin.payments.popfull', compact('i', 'pops'));
        }
    }
    
    public function paymentHistory()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
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
            'admin_id' => auth()->user()->id
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
        $transaction = Transaction::with(['coupon', 'program', 'user'])->whereId($id)->first();

        $transaction->name = User::whereId($transaction->user_id)->value('name');
        $program_details = Program::select('p_name', 'p_amount', 'modes', 'locations')->whereId($transaction->program_id)->first();
        $coupons = Coupon::whereProgramId($transaction->program_id)->get();

        $transaction->p_name = $program_details->p_name;
        $transaction->p_amount = $program_details->p_amount;

        $modes =  (isset($program_details->modes) && !empty($program_details->modes)) ? json_decode($program_details->modes) : [];
        $locations =  (isset($program_details->locations) && !empty($program_details->locations)) ? json_decode($program_details->locations) : [];
        // determine balance

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.transactions.edit', compact('transaction', 'locations', 'modes', 'coupons'));
        }
        return back();
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)->first();
       
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            //get user details
            $user = User::findorFail($transaction->user_id);

            if ($transaction->t_amount == $user->programs[0]['e_amount']) {
                $message = $this->dosubscript2($transaction->balance);
            } else {
                $message = $this->dosubscript1($user->balance);
            }

            //determine the program details
            $details = [
                'programFee' => $user->programs[0]['p_amount'],
                'programName' => $user->programs[0]['p_name'],
                'programAbbr' => $user->programs[0]['p_abbr'],
                'balance' => $transaction->balance,
                'message' => $message,
                'booking_form' => isset($user->programs[0]['booking_form']) ? base_path() . '/uploads' . '/' . $user->programs[0]['booking_form'] : NULL,
                'invoice_id' =>  $transaction->invoice_id,
                'currency' => $transaction->currency,
                'transid' =>  $transaction->transid,
                't_type' =>  $transaction->t_type
            ];

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'bank' => $user->t_type,
                'booking_form' => isset($user->programs[0]['booking_form']) ? base_path() . '/uploads' . '/' . $user->programs[0]['booking_form'] : NULL,
                'amount' => $transaction->t_amount,
                'training_mode' => $transaction->training_mode ?? null,
                'location' => $transaction->t_location ?? null,
                'currency_symbol' => $transaction->currency ?? null,
            ];

            //generate pdf from receipt view

            //send user mails
            // return view('emails.receipt', compact('data', 'details'));
            $data['type'] = 'initial';
            $data = array_merge($data, $details);
            $pdf = PDF::loadView('emails.printreceipt', compact('data'));
            // return view('emails.printreceipt', compact('data', 'details'));

            try {
                // to admin
                // $this->sendWelcomeMail($data, $pdf);
                // to user
                $this->sendWelcomeMail($data, $pdf);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with('message', 'Receipt sent succesfully');
        } else return back();
    }

    public function printReceipt($id)
    {
        $transaction = Transaction::with(['coupon', 'program'])->where('id', $id)->first();

        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            if (!$transaction) {
                return back()->with('warning', 'Unauthorized Action');
            }
            if ($transaction->user_id <> auth()->user()->id) {
                return back()->with('warning', 'Unauthorized Action');
            }
        }
        //get user details
        $user = User::findorFail($transaction->user_id);

        if ($transaction->t_amount == $user->programs[0]['e_amount']) {
            $message = $this->dosubscript2($transaction->balance);
        } else {
            $message = $this->dosubscript1($user->balance);
        }

        //determine the program details
        if (isset($transaction->t_location) && !empty($transaction->t_location)) {
            $locations = $transaction->program->locations;

            if (isset($locations) && !empty($locations)) {
                $locations = json_decode($locations, true);
                $training_address = $locations[$transaction->t_location] ?? $transaction->t_location;
            }
        }
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'bank' => $user->t_type,
            'amount' => $transaction->t_amount,
            'programFee' => $user->programs[0]['p_amount'],
            'programName' => $user->programs[0]['p_name'],
            'programAbbr' => $user->programs[0]['p_abbr'],
            'balance' => $transaction->balance,
            'message' => $message,
            'booking_form' => $user->programs[0]['booking_form'],
            'invoice_id' =>  $transaction->invoice_id,
            'message' => $message,
            'currency' => $transaction->currency,
            'transid' =>  $transaction->transid ?? null,
            'invoice_id' =>  $transaction->invoice_id ?? null,
            't_type' =>  $transaction->t_type,
            'coupon_id' =>  $transaction->coupon->id ?? null,
            'coupon_code' =>  $transaction->coupon->code ?? null,
            'coupon_amount' =>  $transaction->coupon->amount ?? null,
            'training_mode' => $transaction->training_mode ?? null,
            'location' => $transaction->t_location ?? null,
            'location_address' => $training_address ?? null,
            'created_at' =>  $transaction->created_at ?? null,
        ];


        //generate pdf from receipt view
        $pdf = PDF::loadView('emails.receipt', compact('data'));

        return view('emails.printreceipt', compact('data'));
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
        $transaction = Transaction::with('user', 'program')->whereId($id)->first();

        $user = $transaction->user;
        $programFee = $request->program_amount;

        //check amount against payment
        if ($request->has('training_mode') && !empty($request->training_mode)) {
            $modes =  (isset($transaction->program->modes) && !empty($transaction->program->modes)) ? json_decode($transaction->program->modes, true) : [];
            if (!empty($modes)) {
                $programFee = $modes[$transaction->training_mode];
            }
        }

        
        $newamount = $transaction->t_amount + $request->amount;
        $balance = $programFee - $newamount;
        
        // Checks for coupon code
        // && $request->coupon_id != $transaction->coupon_id
        if ($request->has('coupon_id') && !empty($request->coupon_id)) {
            $request['email'] = $transaction->user->email;
            $coupon = Coupon::whereId($request->coupon_id)->first();
            $request['coupon'] = $coupon->code;
            $request['amount'] = $newamount;
            $request2 = (object) $request->all();

            $response = $this->verifyCoupon($request2, $transaction->program_id);

            if (isset($response['amount'])) {
                // make this amount already paid for the student
                $amount = $transaction->t_amount + $response['amount'];
                $balance = $programFee - $amount;

                $transaction->update([
                    'coupon_id' => $response['id'],
                    'coupon_amount' => $response['amount'],
                    'coupon_code' => $response['code'],
                ]);
                $this->updateCouponStatus($transaction->user->email, $response['id'], $transaction->program_id);
            }
        }


        $message = $this->dosubscript1($balance);
        $paymentStatus =  $this->paymentStatus($balance);

        if ($newamount > $programFee) {
            return back()->with('warning', 'Student cannot pay more than program fee');
        }
        
        if ($request->funds_source == 'wallet') {
            // Does he have enough in wallet
            $account_balance = $user->account_balance;

            if($account_balance < $request->amount){
                return back()->with('error', 'Insufficient funds in user account balance');
            }
            $t_type = 'wallet';
            // log a credit
            $wallet['amount'] = abs($request->amount);
            $wallet['transaction_id'] = $transaction->transid ?? $transaction->invoice_id;
            $wallet['type'] = 'debit';
            $wallet['method'] = 'wallet';
            $wallet['provider'] = 'ADMIN TOPUP';
            $wallet['status'] = 'approved';
            $wallet['user_id'] = $user->id;
            $wallet['admin_id'] = auth()->user()->id;

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
            $wallet['admin_id'] = auth()->user()->id;

            app('App\Http\Controllers\WalletController')->logWallet($wallet);

            // log a debit
            $wallet['amount'] = abs($request->amount);
            $wallet['transaction_id'] = $transaction->transid ?? $transaction->invoice_id;
            $wallet['type'] = 'debit';
            $wallet['method'] = 'offline';
            $wallet['provider'] = 'ADMIN TOPUP';
            $wallet['status'] = 'approved';
            $wallet['user_id'] = $user->id;
            $wallet['admin_id'] = auth()->user()->id;

            app('App\Http\Controllers\WalletController')->logWallet($wallet);
        }

        //update the program table here @ column fully paid or partly paid
        $transaction->update([
            't_amount' => $newamount,
            'balance' => $balance,
            't_type' => $request['bank'] ?? null,
            't_location' => $request['location'],
            'training_mode' => $request['training_mode'],
            'admin_id' => auth()->user()->id,
            'paymentStatus' =>  $paymentStatus,
        ]);

        $reference = $this->getReference('ADMIN_TOP_UP_WALLET');

        PaymentThread::create([
            'program_id' => $transaction->program_id,
            'user_id' => $transaction->user_id,
            'payment_id' => $transaction->id,
            'transaction_id' => $reference,
            't_type' => $t_type,
            'admin_id' => auth()->user()->id,
            'parent_transaction_id' => $transaction->transid ?? $transaction->invoice_id,
            'amount' => abs($request->amount),
        ]);
        
        return back()->with('message', 'Transaction updated successfully');
    }

    public function destroy($id)
    {
        DB::table('program_user')->whereId($id)->delete();
        return back()->with('message', 'Transaction has been deleted forever');
    }
}
