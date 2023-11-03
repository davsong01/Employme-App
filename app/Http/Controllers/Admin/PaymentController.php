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
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
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
    public function index()
    {
        $i = 1;

        if (Auth::user()->role_id == "Admin") {
            // $transactions = Transaction::with('program','user')->orderBy('program_user.id', 'DESC')->get();

            $transactions = DB::table('program_user')->orderBy('created_at', 'DESC')
                ->join("programs", "program_user.program_id", "=", "programs.id")
                ->join("users", "users.id", "=", "program_user.user_id")
                ->select("program_user.*", "users.name", "users.email", "users.t_phone", "programs.p_name", "programs.modes", "programs.locations", "coupon_amount", "coupon_id", "coupon_code", "currency", "program_user.t_type")
                ->get();
            $i = 1;
            $pops = Pop::with('program')->Ordered('date', 'DESC')->get();

            return view('dashboard.admin.payments.index', compact('transactions', 'i', 'pops'));
        }
        if (Auth::user()->role_id == "Teacher" || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            return back();
        }
        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {

            $transactiondetails = DB::table('program_user')->where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->get();

            foreach ($transactiondetails as $details) {
                $details->programs = Program::select('p_name', 'p_amount')->where('id', $details->program_id)->get()->toArray();
                $details->p_name = $details->programs[0]['p_name'];
                $details->p_amount = $details->programs[0]['p_amount'];
            }
            // dd($transactiondetails->balance);
            return view('dashboard.student.payments.index', compact('transactiondetails'));
        }
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

        if (Auth::user()->role_id == "Admin") {
            return view('dashboard.admin.transactions.edit', compact('transaction', 'locations', 'modes', 'coupons'));
        }
        return back();
    }

    public function show(Request $request, $id)
    {
        $transaction = DB::table('program_user')->where('id', $id)->first();

        if (Auth::user()->role_id == "Admin") {
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

                DB::table('program_user')->whereId($transaction->id)->update([
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
        //update the program table here @ column fully paid or partly paid
        DB::table('program_user')->whereId($transaction->id)->update([
            't_amount' => $newamount,
            'balance' => $balance,
            't_type' => $request['bank'] ?? null,
            't_location' => $request['location'],
            'training_mode' => $request['training_mode'],
            'transid' => $request['transaction_id'],
            'paymentStatus' =>  $paymentStatus,
        ]);


        return back()->with('message', 'Transaction updated successfully');
    }

    public function destroy($id)
    {
        DB::table('program_user')->whereId($id)->delete();

        return back()->with('message', 'Transaction has been deleted forever');
    }
}
