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
        $trainings = Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')->mainActivePrograms()->get();
        $today = now()->toDateString();
        
        $groups = Group::isActive()->with(['programs' => function ($q) {
            $q->mainActiveProgramsWithIsClosed();
        }])->whereDate('p_start', '>', $today)->get();
        
        if (isset(session()->get('data')['metadata']['pid'])) {
            $accounts = getAccounts(session()->get('data')['metadata']['pid']);
        } else {
            $accounts = getAccounts();
        }

        return view('pop')
            ->with('trainings', $trainings)
            ->with('groups', $groups)
            ->with('accounts', $accounts);
    }

    // public function store(Request $request)
    // {
    //     if (BlacklistService::checkByValues([
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //     ])) {
    //         return back()->with('danger', 'BLTD: Something went wrong, Please contact Admin');
    //     }

    //     $program_type = $request->program_type;
    //     $request['training_id'] = $program_type == 'package' ? $request->package_id : $request->training_id;

    //     $data = $this->validate($request, [
    //         'name' => 'required',
    //         'email' => 'required',
    //         'phone' => 'required | numeric',
    //         'bank' => 'sometimes',
    //         'amount' => 'required | numeric',
    //         'training_id' => 'required | numeric',
    //         'currency' => 'sometimes',
    //         'currency_symbol' => 'sometimes',
    //         'coupon_id' => 'nullable',
    //         'date' => 'date',
    //         'file' => 'required|max:2048|image',
    //     ]);

    //     // Remove data from session
    //     \Session::forget(['data']);

    //     $file = Str::random(10);
    //     $extension = $request->file('file')->getClientOriginalExtension();
    //     $filePath = $request->file('file')->storeAs('payments', $file . '.' . $extension, 'uploads');

    //     $date = Carbon::parse($data['date'] . ' ' . now()->format('h:i:s'));

    //     // Check if already uploaded same pop
    //     if ($program_type == 'package') {
    //         $popCheck = Pop::whereEmail($data['email'])->whereGroupId($data['training_id'])->where('is_package', 1)->where('amount', $data['amount'])->count();
    //         $program = Group::where('id', $data['training_id'])->first();
    //     } else {
    //         $popCheck = Pop::whereEmail($data['email'])->whereProgramId($data['training_id'])->where('is_package', 0)->where('amount', $data['amount'])->count();
    //         $program = Program::where('id', $data['training_id'])->first();
    //     }

    //     if ($popCheck > 0) {
    //         return back()->with('error', "You have already uploaded proof of payment for this {$program_type} and with the same amount, kindly wait while an administrator approves your request");
    //     }

    //     if (isset($user) && !empty($user)) {
    //         $check = DB::table('pop')->where(['user_id' => $user, 'program_id' => $data['training_id']])->where('balance', '<', 1)->count();

    //         if ($check > 0) {
    //             return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
    //         }
    //     }

    //     // Check if user already paid for same program
    //     $user = User::whereEmail($data['email'])->value('id');
    //     if (isset($user) && !empty($user)) {
    //         $validate = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training_id']]);
    //         $check = $validate->where('balance', '<', 1)->count();
    //         if ($check > 0) {
    //             return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
    //         }
    //     } else {
    //         $type = 'Fresh Payment' ?? null;
    //     }
    //     // Get temp transaction 
    //     if($validate){
    //         $transaction = TempTransaction::where('email', $data['email'])->where('program_id', $data['training_id'])->where('status','initiated')->first();

    //     }
        
    //     $data['location'] = $transaction->location ?? null;
    //     $data['training_mode'] = $transaction->training_mode ?? null;

    //     $storeData = [
    //         'name' => $data['name'],
    //         'email' =>  $data['email'],
    //         'phone' =>  $data['phone'],
    //         'bank' =>  $data['bank'],
    //         'coupon_id' =>  $data['coupon_id'],
    //         'amount' =>  $data['amount'],
    //         'is_package' =>  $program_type == 'package' ? 1 : 0,
    //         'currency' =>  $data['currency'],
    //         'currency_symbol' =>  $data['currency_symbol'],
    //         'is_fresh' => $type ?? null,
    //         'temp_transaction_id' => $transaction->id ?? null,
    //         'location' =>  $data['location'] ?? null,
    //         'date' =>  $date,
    //         'file' => base64_encode($filePath),
    //     ];
        
    //     if ($program_type == 'package') {
    //         $storeData['group_id'] = $data['training_id'];
    //     } else {
    //         $storeData['program_id'] = $data['training_id'];
    //     }

    //     try {
    //         //Store new pop
    //         $pop = Pop::create($storeData);

    //         //Prepare Attachment
    //         $data['pop'] = base_path() . '/uploads' . '/' . $filePath;
    //         $data['training'] = $program->p_name;

    //         $data['type'] = 'pop';
    //         $data['email'] = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');
    //         $data['participant_email'] = $pop->email;
    //         $data['realfilename'] = $file . '.' . $extension;
    //         $data['transaction'] = $transaction;

    //         $this->sendWelcomeMail($data);
    //     } catch (\Exception $e) {
    //         // dd($e->getMessage(), $e->getLine().$e->getFile());
    //         \Log::info($e->getMessage());
    //         return back()->with('error', 'Something happened or you have already uploaded POP');
    //     }

    //     return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
    // }
    public function store(Request $request)
    {
        if (BlacklistService::checkByValues([
            'email' => $request->email,
            'phone' => $request->phone,
        ])) {
            return back()->with(
                'danger',
                'BLTD: Something went wrong, Please contact Admin'
            );
        }

        $programType = $request->program_type;

        $request->merge([
            'training_id' => $programType === 'package'
                ? $request->package_id
                : $request->training_id,
        ]);

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'bank' => 'nullable|string',
            'amount' => 'required|numeric',
            'training_id' => 'required|numeric',
            'currency' => 'nullable|string',
            'currency_symbol' => 'nullable|string',
            'coupon_id' => 'nullable',
            'date' => 'required|date',
            'file' => 'required|image|max:2048',
        ]);

        session()->forget('data');

        /**
         * ---------------------------------
         * FILE UPLOAD
         * ---------------------------------
         */
        $file = $request->file('file');

        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

        $filePath = $file->storeAs(
            'payments',
            $fileName,
            'uploads'
        );

        /**
         * ---------------------------------
         * PROGRAM / PACKAGE
         * ---------------------------------
         */
        if ($programType === 'package') {

            $program = Group::find($data['training_id']);

            $duplicatePop = Pop::where([
                'email' => $data['email'],
                'group_id' => $data['training_id'],
                'is_package' => 1,
                'amount' => $data['amount'],
            ])->exists();

        } else {

            $program = Program::find($data['training_id']);

            $duplicatePop = Pop::where([
                'email' => $data['email'],
                'program_id' => $data['training_id'],
                'is_package' => 0,
                'amount' => $data['amount'],
            ])->exists();
        }

        if ($duplicatePop) {
            return back()->with(
                'error',
                "You have already uploaded proof of payment for this {$programType} with the same amount. Kindly wait for approval."
            );
        }

        $userId = User::where('email', $data['email'])->value('id');

        $isFreshPayment = empty($userId);

        $transaction = null;

        if ($userId) {

            $alreadyRegistered = DB::table('program_user')
                ->where([
                    'user_id' => $userId,
                    'program_id' => $data['training_id'],
                ])
                ->where('balance', '<', 1)
                ->exists();

            if ($alreadyRegistered) {
                return back()->with(
                    'error',
                    'You are already registered for this training! Kindly login with your email and password.'
                );
            }

            $programUserExists = DB::table('program_user')
                ->where('user_id', $userId)
                ->where('program_id', $data['training_id'])
                ->exists();

            if ($programUserExists) {

                $transaction = TempTransaction::where([
                    'email' => $data['email'],
                    'program_id' => $data['training_id'],
                    'status' => 'initiated',
                ])->first();
            }
        }

        $date = Carbon::parse(
            $data['date'] . ' ' . now()->format('H:i:s')
        );

        $storeData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'bank' => $data['bank'] ?? null,
            'coupon_id' => $data['coupon_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? null,
            'currency_symbol' => $data['currency_symbol'] ?? null,
            'is_package' => $programType === 'package' ? 1 : 0,
            'is_fresh' => $isFreshPayment ? 'Fresh Payment' : null,
            'temp_transaction_id' => $transaction?->id,
            'location' => $transaction?->location,
            'date' => $date,
            'file' => base64_encode($filePath),
        ];

        if ($programType === 'package') {
            $storeData['group_id'] = $data['training_id'];
        } else {
            $storeData['program_id'] = $data['training_id'];
        }

        DB::beginTransaction();
       
        try {
            $pop = Pop::create($storeData);
            $pop->training = $program->p_name;
            
            DB::commit();
            
            try {
                $mailData = [
                    'pop' => base_path('uploads/' . $filePath),
                    'training' => $program?->p_name,
                    'type' => 'pop',
                    'email' => Settings::value('OFFICIAL_EMAIL'),
                    'record' => $pop,
                    'realfilename' => $fileName,
                    'transaction' => $transaction,
                ];
                $this->sendWelcomeMail($mailData);
            } catch (\Throwable $th) {

            }
   
            return back()->with(
                'message',
                'Your proof of payment has been received. We will confirm and issue your E-receipt ASAP.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error($e->getMessage());

            return back()->with(
                'error',
                'Something went wrong or the POP already exists.'
            );
        }
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
            $transaction = $pop->temp;
            
            if(!$transaction){
                $isPackage = $pop->is_package ?? 0;
                
                if ($isPackage) {
                    $program = Group::find($pop->group_id);
                    $data['programIds'] = $program?->programs->pluck('id')->toArray() ?? [];
                    $couponCheck = Coupon::where('id', $pop->coupon_id)->where('group_id', $pop->group_id)->first();
                } else {
                    $program = Program::find($pop->program_id);
                    $data['programIds'] = $program ? [$pop->program_id] : [];
                    $couponCheck = Coupon::where('id', $pop->coupon_id)->where('program_id', $pop->program_id)->first();
                }

                $participant = [
                    'name' => $pop->name,
                    'email' => $pop->email,
                    'phone' => $pop->phone,
                ];
                
                $prepareData = [
                    'program' => $program,
                    'remarks' => $pop->remarks ?? null,
                    'isPackage' => $isPackage,
                    'participant' => $participant,
                    'couponCheck' => $couponCheck,
                    'send_email' => 'yes',
                    'amount_to_use' => $program->p_amount,
                    'data' => $data, // programIDs
                    'transaction_status' => 'complete',
                    'payment_mode' => 0,
                    'payment_type' => 'full',
                    'amountPaid' => $pop->amount,
                    't_type' => 'Transfer',
                    'transid' =>  PaymentService::getReference('SYS-ADMIN'),
                    'invoiceId' =>  PaymentService::getInvoiceId(),
                ];
                
                $addProgram = PaymentService::adminAddNewParticipant($prepareData);
                
                if ($addProgram['status']) {
                    $pop->delete();
                    return redirect(route('payments.index'))->with('message', 'Student added succesfully');
                }else{

                    return redirect(route('payments.index'))->with('error', $addProgram['message'] ?? 'Something Happened');
                }
            }else{
                // Its either there is existing transaction or not, if there is, then
                if ($transaction->balance > 0) {
                    $bData = [
                        'amount' => $pop->amount,
                        't_type' => $pop->t_type,
                    ];
    
                    $response = PaymentService::handleBalancePayment($transaction, $transaction->balance, $bData);
    
                    if ($response['status']) {
                        $pop->delete();
    
                        return redirect(route('payments.index'))->with('message', 'Balance Payment added succesfully');
                    } else {
                        return back()->with('error', $response['message']);
                    }
                } else {
                    $isPackage = $pop->is_package ?? 0;

                    if ($isPackage) {
                        $program = Group::find($pop->group_id);
                        $data['programIds'] = $program?->programs->pluck('id')->toArray() ?? [];
                        $couponCheck = Coupon::where('id', $pop->coupon_id)->where('group_id', $pop->group_id)->first();
                    } else {
                        $program = Program::find($pop->program_id);
                        $data['programIds'] = $program ? [$pop->program_id] : [];
                        $couponCheck = Coupon::where('id', $pop->coupon_id)->where('program_id', $pop->program_id)->first();
                    }
                    
                    // Start new implementation
                    $completePayment = PaymentService::completePayment($transaction);
                    
                    if($completePayment['status']){
                        $data['balance'] = $completePayment['balance'];
                        $data['programs'] = $transaction->allPrograms()->toArray();
                        $data['payment_type'] = $transaction->type;
        
                        $data['message'] = $completePayment['message'];
                        $data['paymentStatus'] = $completePayment['paymentStatus'];
        
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
                        
                        $pop->delete();
    
                        return redirect(route('payments.index'))->with('message', 'Student added succesfully');
                    }else {
                        return back()->with('error', $addProgram['message'] ?? 'Something went wrong');
                    }
                    // End new implementation
                }
            }

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage(), ' File: ' . $e->getFile(), ' Line: ' . $e->getLine());
        }
    }

    public function update(Pop $pop, Request $request)
    {

        $pop->update($request->except(['template', '_token', '_method', 'template', 'prefix__', 'transId', 'delete_transaction', 'transid']));

        if(!empty($request->delete_transaction)){
            $pop->temp->delete();

            $pop->update([
                'temp_transaction_id' => null,
            ]);

        }
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
