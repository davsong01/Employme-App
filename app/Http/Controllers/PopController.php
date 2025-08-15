<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pop;
use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Settings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use App\Services\PaymentService;
use App\Services\BlacklistService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPackageTools\Package;

class PopController extends Controller
{
    public function index()
    {
        // Get attempted payments
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return route('home');
        }

        $transactions =  TempTransaction::with(['coupon', 'program:id,p_amount,e_amount,created_at'])->orderBy('created_at', 'DESC')->get();
        $i = 1;
        $official_email = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
        
        return view('dashboard.admin.payments.pop', compact('transactions', 'i'));
    }

    public function create()
    {
        // $trainings = Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')
        // ->doesntHave('children')
        // ->where('id', '<>', 1)
        // ->where('close_registration', 0)
        // ->where('close_registration', 0)
        // ->where('p_end', '>', date('Y-m-d'))
        // ->orderBy('created_at', 'DESC')
        // ->get();
        $trainings = Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')->mainActivePrograms()->get();

        $groups = Group::isActive()->with(['programs' => function ($q) {
            $q->mainActiveProgramsWithIsClosed();
        }])->get();
        
        if(isset(session()->get('data')['metadata']['pid'])){
            $accounts = getAccounts(session()->get('data')['metadata']['pid']);
        }else{
            $accounts = getAccounts();
        }
        
        return view('pop')
            ->with('trainings', $trainings)
            ->with('groups', $groups)
            ->with('accounts', $accounts);
    }

    public function store(Request $request)
    {
        if (BlacklistService::checkByValues([
            'email' => $request->email,
            'phone' => $request->phone,
        ])) {
            return back()->with('danger', 'BLTD: Something went wrong, Please contact Admin');
        }
        
        $program_type = $request->program_type;
        $request['training_id'] = $program_type == 'package' ? $request->package_id : $request->training_id;
        
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required | numeric',
            'bank' => 'sometimes',
            'amount' => 'required | numeric',
            'training_id' => 'required | numeric',
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
        
        $date = Carbon::parse($data['date'] . ' ' . now()->format('h:i:s'));

        // Check if already uploaded same pop
        if($program_type == 'package'){
            $popCheck = Pop::whereEmail($data['email'])->whereGroupId($data['training_id'])->where('is_package', 1)->where('amount', $data['amount'])->count();
            $program = Group::where('id', $data['training_id'])->first();
        }else{
            $popCheck = Pop::whereEmail($data['email'])->whereProgramId($data['training_id'])->where('is_package', 0)->where('amount', $data['amount'])->count();
            $program = Program::where('id', $data['training_id'])->first();
        }

        if ($popCheck > 0) {
            return back()->with('error', "You have already uploaded proof of payment for this {$program_type} and with the same amount, kindly wait while an administrator approves your request");
        }

        if (isset($user) && !empty($user)) {
            $check = DB::table('pop')->where(['user_id' => $user, 'program_id' => $data['training_id']])->where('balance', '<', 1)->count();

            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }

        // Check if user already paid for same program
        $user = User::whereEmail($data['email'])->value('id');
        if (isset($user) && !empty($user)) {
            $validate = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training_id']]);
            $check = $validate->where('balance', '<', 1)->count();
            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        } else {
            $type = 'Fresh Payment' ?? null;
        }
        // Get temp transaction 
        $temp = TempTransaction::where('email', $data['email'])->where('program_id', $data['training_id'])->first();
        
        $data['location'] = $temp->location ?? null;
        $data['training_mode'] = $temp->training_mode ?? null;
        
        $storeData = [
            'name' => $data['name'],
            'email' =>  $data['email'],
            'phone' =>  $data['phone'],
            'bank' =>  $data['bank'],
            'coupon_id' =>  $data['coupon_id'],
            'amount' =>  $data['amount'],
            'is_package' =>  $program_type == 'package' ? 1 : 0,
            'currency' =>  $data['currency'],
            'currency_symbol' =>  $data['currency_symbol'],
            'is_fresh' => $type ?? null,
            'temp_transaction_id' => $temp->id ?? null,
            'location' =>  $data['location'] ?? null,
            'date' =>  $date,
            'file' => base64_encode($filePath),
        ];

        if($program_type == 'package'){
            $storeData['group_id'] = $data['training_id'];
        }else{
            $storeData['program_id'] = $data['training_id'];
        }

        try {
            //Store new pop
            $pop = Pop::create($storeData);
            
            //Prepare Attachment
            $data['pop'] = base_path() . '/uploads' . '/' . $filePath;
            $data['training'] = $program->p_name;
            
            $data['type'] = 'pop';
            $data['email'] = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
            $data['participant_email'] = $pop->email;
            $data['realfilename'] = $file . '.' . $extension;
            $data['transaction'] = $temp;
            
            $this->sendWelcomeMail($data);
        } catch (\Exception $e) {
            // dd($e->getMessage(), $e->getLine().$e->getFile());
            \Log::info($e->getMessage());
            return back()->with('error', 'Something happened or you have already uploaded POP');
        }

        return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
    }

    public function show(Pop $pop)
    {

        try {
            if (isset($pop->user) && !empty($pop->user)) {
                $check = DB::table('program_user')->where(['user_id' => $pop->user->id, 'program_id' => $pop->program_id])->where('balance', '<', 1)->count();

                if ($check > 0) {
                    return back()->with('error', 'Participant already registered for this training!');
                }
            }

            // Try to see if this is balance payment
            $existingTransaction = PaymentService::getExistingTransactionAndBalance($pop);
            $program = $pop->related;
            $isPackage = $pop->is_package;
            
            if ($pop->amount > $program->p_amount) {
                return back()->with('error', 'Cannot pay above ' . $program->p_amount);
            }
            
            if (isset($existingTransaction) && isset($existingTransaction['transaction'])){
                $transaction = $existingTransaction['transaction'];
                $isNew = false;
                
                // Check if there is a balance
                if($existingTransaction['balance'] > 0){
                    $isBalancePayment = true;
                    $expectedAmount = $existingTransaction['balance'];
                }else{
                    $isBalancePayment = false;
                    $expectedAmount = $existingTransaction['balance'];
                }

                $balance = $expectedAmount - $pop->amount;
                
                if ($pop->amount > $expectedAmount) {
                    return back()->with('error', 'Cannot pay above ' . $expectedAmount);
                }

                $type = $balance > 0 ? 'part' : 'full';
                
                $transaction->update([
                    'type' => $type,
                    'amount' => $transaction->pop->amount,
                    'balance' => $balance,
                ]);
                
                PaymentThread::create([
                    'program_id' => $transaction->program_id,
                    'user_id' => $transaction->user_id,
                    'payment_id' => $transaction->id,
                    'transaction_id' => PaymentService::getReference('PYTHRD'),
                    't_type' => strtolower($transaction->paymentMode->processor ?? 'TRANSFER'),
                    'parent_transaction_id' => $transaction->transid,
                    'amount' => $pop->amount,
                ]);

                // $pop->delete();

                return redirect(route('payments.index'))->with('message', 'Balance Payment added succesfully');
            }else{
                $isNew = true;
                $expectedAmount = $program->early_bird_status ? $program->e_amount : $program->p_amount;
                $balance = $expectedAmount - $pop->amount;

                if ($pop->amount > $expectedAmount) {
                    return back()->with('error', 'Cannot pay above ' . $expectedAmount);
                }

                if ($program->early_bird_status && $pop->amount ==  $program->e_amount) {
                    $type = 'earlybird';
                    $message = 'Earlybird payment';
                    $paymentStatus =  1;
                } else {
                    $type = $balance > 0 ? 'part' : 'full';
                    $message = $balance > 0 ? 'Part payment' : 'Full payment';
                    $paymentStatus = $balance > 0 ? 0 : 1;
                }
                
                // $program 
                $t_type = 'Transfer';
                $transid = 'BT-' . rand(11111111, 9999999);
                $invoiceId = PaymentService::getInvoiceId();
                
                if ($isPackage) {
                    $group = Group::where('id', $pop->group_id)->first();
                    $programIds = $group->programs->pluck('id')->toArray();
                } else {
                    $programIds = [$pop->program_id];
                }

                $metadata = [
                    'pid'        => $pop->related->id,
                    'facilitator' => null,
                    'coupon_id'  => null,
                    'type'       => $type ?? null,
                    'isPackage'     => $isPackage
                ];

                $transactionArray = [
                    'email' => $pop->email,
                    'type' => $type,
                    'program_id' => $program->id,
                    'coupon_id' =>  null,
                    'facilitator_id' => null,
                    'amount' =>  $pop->amount,
                    'transid' =>  $transid,
                    'invoice_id' => $invoiceId,
                    'payment_mode' => 0,
                    'preferred_timing' => null,
                    'name' => $pop->name,
                    'phone' => $pop->phone,
                    'location' => $pop->location ?? null,
                    'training_mode' => null,
                    'meta' => $metadata,
                    'is_package' => $isPackage,
                    'status' => 'complete',
                    'balance' => $balance,
                    't_type' => $t_type,
                    'program_ids' => $programIds,
                    'currency' => $pop->currency,
                    'currency_symbol' => $pop->currency_symbol,
                ];
                dd('ho;ld');
                $transaction = PaymentService::initiateTransaction($transactionArray);
                $data = $this->prepareTrainingDetails($program, $transaction, $transaction->amount);

                $data['balance'] = $balance;
                $data['programs'] = $transaction->allPrograms()->toArray();
                $data['payment_type'] = $transaction->type;
                $data['message'] = $message;
                $data['paymentStatus'] =  $paymentStatus;
                
                PaymentService::createUserAndAttachPrograms($transaction);
                $transaction = $transaction->fresh();
                
                $data['currency'] = \Session::get('currency');
                $data['currency_symbol'] = \Session::get('currency_symbol');
                $data['exchange_rate'] = \Session::get('exchange_rate');
    
                $data['type'] = 'initial';
                $data['name'] = $transaction->name;
                $data['transaction'] = $transaction;
                $data['program'] = $program;
    
                PaymentThread::create([
                    'program_id' => $transaction->program_id,
                    'user_id' => $transaction->user_id,
                    'payment_id' => $transaction->id,
                    'transaction_id' => PaymentService::getReference('PYTHRD'),
                    't_type' => strtolower($transaction->paymentMode->processor ?? 'TRANSFER'),
                    'parent_transaction_id' => $transaction->transid,
                    'amount' => $pop->amount,
                ]);
            }
            
            // $pop->delete();

            if($isNew){
                $this->sendWelcomeMail($data);
            }

            // $transaction//////
        }catch(\Exception $e){
            DB::rollback();
            dd($e->getMessage(), ' File: '.$e->getFile(), ' Line: ' . $e->getLine());
        }

        return redirect(route('payments.index'))->with('message', 'Student added succesfully');
    }

    public function update(Pop $pop, Request $request){
        $pop->update($request->except(['template', '_token', '_method', 'template', 'prefix__']));
        return back()->with('message', 'Update Successful');
    }
    

    public function tempDestroy($id)
    {
        $trans = TempTransaction::find($id);
        $trans->delete();
        return back()->with('message', 'Delete successful');
    }

    public function reconcile()
    {
        $users = User::where('roles', 'Student')->get();
        foreach ($users as $user) {
            //get user extra details
            $user->programs()->attach($user->program_id, [
                'created_at' =>  $user->created_at,
                'amount' => $user->amount,
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
        if (checkRoleHas(['Admin', 'Facilitaor', 'Grader'])) {
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
