<?php

namespace App\Http\Controllers;

use App\Models\Pop;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Settings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PopController extends Controller
{
    public function index()
    {
        // Get attempted payments
        if (empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return abort(404);
        }

        $transactions =  TempTransaction::with(['coupon', 'program'])->orderBy('created_at', 'DESC')->get();
        // $transactions = Pop::with('program','user','temp')->Ordered('date', 'DESC')->get();
        $i = 1;
        $official_email = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');

        return view('dashboard.admin.payments.pop', compact('transactions', 'i'));
    }

    public function create()
    {
        $trainings = Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')
        ->doesntHave('children')
        ->where('id', '<>', 1)
        ->where('close_registration', 0)
        ->where('p_end', '>', date('Y-m-d'))
        ->orderBy('created_at', 'DESC')
        ->get();
        
        if(isset(session()->get('data')['metadata']['pid'])){
            $accounts = getAccounts(session()->get('data')['metadata']['pid']);
        }else{
            $accounts = getAccounts();
        }
        
        return view('pop')
            ->with('trainings', $trainings)
            ->with('accounts', $accounts);
    }

    public function store(Request $request)
    {
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

        $file = Str::random(10);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $request->file('file')->storeAs('payments', $file . '.' . $extension, 'uploads');
        
        $date = \Carbon\Carbon::parse($data['date'] . ' ' . now()->format('h:i:s'));

        // Check if already uploaded same pop
        // $popCheck = Pop::whereEmail($data['email'])->whereAmount($data['amount'])->whereProgramId($data['training'])->count();
        $popCheck = Pop::whereEmail($data['email'])->whereProgramId($data['training'])->count();

        // if ($popCheck > 0) {
        //     return back()->with('error', 'You have already uploaded proof of payment for this training and with the same amount, kindly wait while an administrator approves your request');
        // }

        if (isset($user) && !empty($user)) {
            $check = DB::table('pop')->where(['user_id' => $user, 'program_id' => $data['training']])->where('balance', '<', 1)->count();

            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }

        // Check if user already paid for same program
        $user = User::whereEmail($data['email'])->value('id');
        if (isset($user) && !empty($user)) {
            $validate = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training']]);
            $check = $validate->where('balance', '<', 1)->count();
            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        } else {
            $type = 'Fresh Payment' ?? null;
        }

        // Get temp transaction 
        $temp = TempTransaction::where('email', $data['email'])->where('program_id', $data['training'])->first();
        $data['location'] = $temp->location ?? null;
        $data['training_mode'] = $temp->training_mode ?? null;
        
        try {
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
                'file' => base64_encode($filePath),
            ]);
            
            //Prepare Attachment
            $data['pop'] = base_path() . '/uploads' . '/' . $filePath;
            $data['training'] = Program::where('id', $data['training'])->value('p_name');
            $data['type'] = 'pop';
            $data['email'] = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
            $data['participant_email'] = $pop->email;
            $data['realfilename'] = $file . '.' . $extension;
            
            $this->sendWelcomeMail($data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            // return back()->with('error', $e->getMessage());
        }

        return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
    }

    public function show(Pop $pop)
    {
        if (empty(array_intersect(adminRoles(), Auth::user()->role()))) {
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
        
        $allDetails = [];
        if (isset($existingTransaction) && $existingTransaction['balance'] > 0) {
            $allDetails['balance_transaction_id'] = $this->getReference('SYS_ADMIN_BAL');
            $allDetails['existingTransaction'] =  $existingTransaction['transaction'];
            $allDetails['programFee'] = $pop->program->e_amount > 0 ? $pop->program->e_amount : $pop->program->p_amount;

            if ($pop->amount > $existingTransaction['balance']) {
                return back()->with('error', 'User has already paid: ' . $existingTransaction['transaction']->t_amount . '; Balance payment should be ' . $existingTransaction['balance']);
            }
            $allDetails['amount'] = $existingTransaction['transaction']->t_amount + $pop->amount;

            if ($pop->amount >= $existingTransaction['balance']) {
                $balance = 0;
            } else {
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
            $allDetails['preferred_timing'] = $pop->temp->preferred_timing;
            
            $user = $this->updateUserDetails($allDetails);
            $data = $this->updateOrCreateTransaction($user, $allDetails);
        } else {

            if ($pop->program->e_amount > 0) {
                if ($pop->program->e_amount > $pop->amount) {
                    $allDetails['programFee'] = $pop->program->p_amount;
                } else {
                    $allDetails['programFee'] = $pop->program->e_amount;
                }
            } else {
                $allDetails['programFee'] = $pop->program->p_amount;
            }

            if ($pop->amount > $allDetails['programFee']) {
                return back()->with('error', 'User cannot pay more than program fee, you may need to check early bird payment');
            }

            // dd($allDetails['programFee'], 'fd', $pop->program);
            if (isset($pop->temp->training_mode) && !empty($pop->temp->training_mode)) {
                $mode = $pop->temp->training_mode;
                $amount = app('App\Http\Controllers\PaymentController')->getModeAmount($mode, $pop->temp->type, $pop->program);
                if ($amount) {
                    $expectedAmount = $amount;

                    $modes = $pop->program->modes;
                    
                    if(!empty($modes)){
                        $modes = json_decode($modes, true);
                    }else{

                    }
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
            $allDetails['preferred_timing'] = $pop->temp->preferred_timing ?? null;
            $allDetails['t_type'] = $pop->temp->type ?? null;
            
            $user = $this->updateUserDetails($allDetails);

            // request->coupon, $request->email, $pid, $request['amount']
            // $temp = TempTransaction::where(['user'])
            $coupon = Coupon::where('id', $pop->coupon_id)->first();

            if ($coupon) {
                $user->coupon = $coupon;
                $response = $this->verifyCoupon($user, $pop->program_id, 'admin');

                if ($response['grand_total'] <= 0) {
                    $allDetails['message'] = $this->dosubscript1(0);
                    $allDetails['balance'] = 0;
                    $allDetails['paymentStatus'] = $this->paymentStatus(0);
                    $allDetails['paymenttype'] = $this->paymentStatus(0);
                    $allDetails['coupon_amount'] = $response['amount'];
                    $allDetails['coupon_id'] = $response['id'];
                    $allDetails['coupon_code'] = $response['code'];
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
            'preferred_timing' => $allDetails['preferred_timing'] ?? null,
            'location' => $allDetails['location'],

            'booking_form' => !is_null($allDetails['bookingForm']) ? base_path() . '/uploads' . '/' . $allDetails['bookingForm'] : null,
        ];
        $data['type'] = 'initial';

        $this->sendWelcomeMail($data);

        $pop->delete();
        return redirect(route('payments.index'))->with('message', 'Student added succesfully');
    }

    public function update(Pop $pop, Request $request){
        $pop->update($request->except(['template', '_token', '_method', 'template']));
        return back()->with('message', 'Update Successful');
    }
    
    public function getExistingTransactionAndBalance($pop)
    {
        if (isset($pop->user->id)) {
            $existingTransactions = DB::table('program_user')->where(['user_id' => $pop->user->id, 'program_id' => $pop->program_id])->first();
        } else {
            return [
                'balance' => 0,
                'transaction' => null,
            ];
        }

        $programAmount = $pop->program->e_amount > 0 ? $pop->program->e_amount : $pop->program->p_amount;

        if (isset($existingTransactions) && !empty($existingTransactions)) {
            // $balance = $programAmount - $existingTransactions->t_amount;
            $balance = $existingTransactions->balance;
        } else {
            $balance = 0;
        }

        return [
            'balance' => $balance,
            'transaction' => $existingTransactions,
        ];
    }

    public function tempDestroy($id)
    {
        $trans = TempTransaction::find($id);
        $trans->delete();
        return back()->with('message', 'Delete successful');
    }

    public function reconcile()
    {
        $users = User::where('role_id', 'Student')->get();
        foreach ($users as $user) {
            //get user extra details
            $user->programs()->attach($user->program_id, [
                'created_at' =>  $user->created_at,
                't_amount' => $user->t_amount,
                't_type' => $user->t_type,
                't_location' => $user->location,
                'paymentStatus' => $user->paymentStatus,
                'balance' => $user->balance,
                'invoice_id' =>  $user->invoice_id,
            ]);
        }
        return back()->with('message', 'All user details have been moved succesfully');
    }

    public function getfile($filename)
    {
        if (empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return abort(404);
        }

        $realpath = base_path() . '/uploads/pop' . '/' . $filename;
        return response()->download($realpath);
    }

    public function destroy(Pop $pop)
    {
        if (file_exists(base_path() . '/uploads' . '/' . $pop->file)) {
            unlink(base_path() . '/uploads' . '/' . $pop->file);
        }
        $pop->delete();
        return back()->with('message', 'Pop succesfully deleted');
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance)
    {
        if ($balance <= 0) {
            return 'Full payment';
        } else return 'Part payment';
    }

    //return payment status
    private function paymentStatus($balance)
    {
        if ($balance <= 0) {
            return 1;
        } elseif ($balance > 0) {
        }
        return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance)
    {
        if ($balance <= 0) {
            return 'Earlybird payment';
        } else return 'Part payment';
    }
}
