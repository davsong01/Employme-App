<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\Mail\Email;
use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Result;
use App\Models\Program;
use App\Models\Location;
use App\Models\Settings;
use App\Mail\Welcomemail;
use App\Models\Transaction;
use App\Models\UpdateMails;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Models\PaymentThread;
use App\Services\ExcelService;
use Illuminate\Support\Carbon;
use App\Models\TempTransaction;
use App\Services\CouponService;
use App\Services\PaymentService;
use App\Models\FacilitatorTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{

    public function importExport($p_id, $source = 'program')
    {
        if ($source === 'program') {
            $program = Program::select('id', 'p_name', 'p_amount', 'e_amount', 'early_bird_active_till')
                ->where('id', $p_id)
                ->firstOrFail();

            $programs = Program::isNotArchived()
                ->withCount('fullyPaid')
                ->where('id', '<>', $p_id)
                ->get();

            $coupons = Coupon::where('program_id', $p_id)->get(['id', 'code', 'type', 'amount']);
        } else {
            $program = Group::select('id', 'p_name', 'p_amount', 'e_amount', 'early_bird_active_till')
                ->where('id', $p_id)
                ->firstOrFail();

            $programs = Group::isActive()
                ->select('id', 'p_name', 'p_amount', 'e_amount', 'early_bird_active_till')
                ->where('id', '<>', $p_id)
                ->get();

            $coupons = Coupon::where('group_id', $p_id)->get(['id', 'code', 'type', 'amount']);
        }


        if (!checkRoleHas(['Admin', 'Facilitator'])) {
            return abort(404);
        }

        return view('dashboard.admin.users.import', compact('program', 'programs', 'source','coupons'));
    }


    public function downloadBulkSample($filename)
    {
        $realpath = base_path() . '/uploads/' . $filename;
        return response()->download($realpath);
    }

    public function import(Request $request)
    {
        $isPackage = $request->is_package ?? 0;

        $oldProgram = Program::select('id', 'p_name', 'p_amount')
            ->where('id', $request->import_from)
            ->first();

        if ($isPackage) {
            $program = Group::find($request->p_id);
            $data['programIds'] = $program?->programs->pluck('id')->toArray() ?? [];
            $couponCheck = Coupon::where('id', $request->coupon_id)->where('group_id', $request->p_id)->first();
        } else {
            $program = Program::find($request->p_id);
            $data['programIds'] = $program ? [$program->id] : [];
            $couponCheck = Coupon::where('id', $request->coupon_id)->where('program_id', $request->p_id)->first();
        }

        $count = 0;

        if (checkRoleHas(['Admin', 'Facilitator'])) {
            $this->validate(
                request(),
                [
                    'file' => 'sometimes|
                    mimetypes:xlsv,xlsx,xls,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                    application/excel,application/x-excel,application/x-msexcel,text/comma-seperated-values, text/csv',
                    'import_from' => 'sometimes',
                    'start_date' => 'sometimes'
                ],
                [
                    'file.mimetypes' => 'The file must be a file of type: xlsx'
                ]
            );

            try {
                $participants = [];

                // From Excel file
                if ($request->hasFile('file')) {
                    $fileParticipants = ExcelService::import($request->file('file'));

                    $fileParticipants = array_map(function ($participant) {
                        return array_merge($participant, ['source' => 'file']);
                    }, $fileParticipants);

                    $participants = array_merge($participants, $fileParticipants);
                }

                // From old program
                if (!empty($request->import_from)) {
                    set_time_limit(3600);

                    $oldParticipants = Transaction::with([
                        'user',
                        'paymentLog' => function ($q) {
                            $q->where('balance', '<', 1)
                                ->where('status', 'complete');
                        }
                    ])
                        ->where('program_id', $request->import_from)
                        ->whereHas('paymentLog', function ($q) {
                            $q->where('balance', '<', 1)
                                ->where('status', 'complete');
                        });

                    if (!empty($request->start_date)) {
                        $startDate = Carbon::parse($request->start_date);
                        $oldParticipants->whereDate('created_at', '>=', $startDate);
                    }

                    $oldParticipants = $oldParticipants->get()->map(function ($transaction) {
                        return [
                            'email'    => $transaction->user->email,
                            'name'     => $transaction->user->name,
                            'location' => $transaction->user->location ?? '',
                            'phone'    => $transaction->user->phone ?? '',
                            'gender'   => $transaction->user->gender ?? '',
                            'source'   => 'training',
                        ];
                    })->toArray();

                    $participants = array_merge($participants, $oldParticipants);
                }

                // Now loop merged participants
                foreach ($participants as $participant) {
                    $prepareData = [
                        'program' => $program,
                        'isPackage' => $isPackage,
                        'participant' => $participant,
                        'couponCheck' => $couponCheck,
                        'send_email' => $request->send_email,
                        'amount_to_use' => $request->amount_to_use ?? $program->p_amount,
                        'data' => $data, // programIDs
                        'transaction_status' => 'complete',
                        'payment_mode' => 0,
                        'payment_type' => 'full',
                        'amountPaid' => $request->amount_to_use ?? $program->p_amount,
                        'remarks' => $request->remarks,
                        't_type' => 'Transfer',
                        'transid' =>  PaymentService::getReference('SYS-ADMIN'),
                        'invoiceId' =>  PaymentService::getInvoiceId(),
                    ];
                    
                    $addProgram = PaymentService::adminAddNewParticipant($prepareData);
                    
                    if($addProgram['status']){
                        $count++;
                    }else{
                        $count--;
                    }
                    
                }

                $extraMessage = $count . ' Participants Imported';
                if ($oldProgram) {
                    $extraMessage .= ' from ' . $oldProgram->p_name;
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                return back()->with('error', $ex->getMessage());
            }
            
            return back()->with('message', 'Participants have been imported successfully. ' . $extraMessage);
        }

        return abort(404);
    }


    public function index(Request $request)
    {
        $i = 1;

        if(checkRoleHas(['Admin'])){
            $users = User::withCount('programs')->orderBy('created_at', 'DESC');
        }

        if (checkRoleHas(['Facilitator','Grader'])) {
            $users = resolveAuthUser()->trainerStudents();
        }

        if (!empty($request->email)) {
            $users = $users->where('email',$request->email);
        }

        if (!empty($request->name)) {
            $users = $users->where('name', 'LIKE', "%{$request->name}%");
        }

        if (!empty($request->phone)) {
            $users = $users->where('phone', $request->phone);
        }

        if (!empty($request->staffID)) {
            $users = $users->where('staffID', $request->staffID);
        }

        if (!empty($request->program_id)) {
            $users = $users->whereHas('programs', function ($query) use ($request) {
                $query->where('program_user.program_id', $request->program_id);
            });
        }

        if ($request->boolean('is_blacklisted')) {
            $users->where(function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('blacklists')
                        ->whereColumn('blacklists.value', 'users.email')
                        ->whereStatus(1);
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('blacklists')
                        ->whereColumn('blacklists.value', 'users.phone')
                        ->whereStatus(1);
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('blacklists')
                        ->whereColumn('blacklists.value', 'users.staffID')
                        ->whereStatus(1);
                });
            });
        }


        $records = $users->count();
        
        $users = $users->paginate(adminPaginationRecords());
        
        if (checkRoleHas(['Admin'])) {
            $programs = Program::select('id', 'p_name', 'p_end', 'is_closed', 'created_at')->orderBy('created_at','DESC')->get();
        }

        if (checkRoleHas(['Facilitator', 'Grader'])) {
            $programs = resolveAuthUser()->userTrainings()->get();
        }

        $allPrograms = $programs;
        
        return view('dashboard.admin.users.index', compact('users', 'i','records', 'allPrograms'));
    }

    public function redotest($id)
    {

        $programs = DB::table('program_user')->whereUserId($id)->get();

        foreach ($programs as $program) {
            $program->name = Program::whereId($program->program_id)->value('p_name');
        }

        return view('dashboard.admin.users.redotest', compact('programs', 'id'));
    }

    public function saveredotest(Request $request)
    {
        // $user = User::find($request->user_id);
        $result = Result::whereUserId($request->user_id)->first();

        $result->startRedoStatus();

        return redirect(route('users.index'))->with('message', 'Update Successful');
    }

    public function stopredotest($user_id, $result_id)
    {
        if (checkRoleHas(['Admin']) || in_array(22, Auth::user()->Permissions())) {
            $result = Result::whereId($result_id)->first();
    
            // if(is_null($result->certification_test_details)){
            //     return back()->with('error', 'User has not written certification test');
            // }

            User::whereId($user_id)->update(['redotest' => 0]);
            
            return back()->with('message', 'Update Successful');

        }else{
            return back()->with('error', 'You are not allowed to access this resource');

        }
    }

    public function create()
    {
        if(checkRoleHas(['Admin'])) {

            $users = User::orderBy('created_at', 'DESC');
            $locations = Location::select('title')->orderBy('created_at', 'DESC')->get();
            $user = User::all();

            $programs =  Program::select('id', 'p_end', 'p_name', 'p_amount', 'is_closed')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.users.create', compact('users', 'user', 'programs', 'locations'));
        }
        return back();
    }

    public function store(Request $request)
    {
        //Check if program exist for the incoming training
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $check = DB::table('program_user')->whereProgramId($request->training)->whereUserId($user->id)->get();

            if ($check->count() > 0) {
                return back()->with('error', 'Participant has already paid for this training');
            }
        }

        //determine the program details
        $details = Program::findorFail($request['training']);
        $programFee = $details->p_amount;
        $programName = $details->p_name;
        $programAbbr = $details->p_abbr;
        $bookingForm = $details->booking_form;
        $programEarlyBird = $details->e_amount;
        $invoice_id = $this->getReference('SYS-ADMIN');

        if ($request['amount'] > $programFee) {
            return back()->with('warning', 'Student cannot pay more than program fee');
        } else {
            //check if earlybird bypass was checked
            if (!$request['earlybird']) {
                //go ahead and do normal balance
                if ($request['amount'] == $programEarlyBird) {
                    $balance = $programEarlyBird - $request['amount'];
                    $message = $this->dosubscript2($balance);
                    $payment_type = 'EB';
                } else {
                    $balance = $programFee - $request['amount'];
                    $message = $this->dosubscript1($balance);
                    $payment_type = 'Full';
                }
                $paymentStatus =  $this->paymentStatus($balance);
            } else {
                //check amount against payment
                $balance = $programFee - $request['amount'];
                $message = $this->dosubscript2($balance);
                $paymentStatus =  $this->paymentStatus($balance);
                $payment_type = 'Full';
            }

            //update the program table here @ column fully paid or partly paid
            // $this->programStat($request['training'], $paymentStatus);

            $data = request()->validate([
                'name' => 'required | min:5',
                'email' => 'required | email',
                'phone' => 'required',
                'training' => 'required',
                'amount' => 'required',
                'bank' => 'required',
                'location' => 'nullable',
                'password' => 'required',
                'role' => 'required',
                'gender' => 'nullable',
                'transaction_id' => 'nullable',
                'invoice_id' => '',
            ]);
            
            //Check if email exists in the system and attach it to the new pregram to that email
            if (!$user) {
                //save to database
                $user = User::Create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => bcrypt($data['password']),
                    'roles' => $data['role'],
                    'gender' => $data['gender'],
                ]);
            }

            $transid = $data['transaction_id'] ?: PaymentService::getReference('SYS-ADMIN');
            $transactionType = $balance > 0 ? 'part' : (($data['amount'] == $programEarlyBird) ? 'earlybird' : 'full');

            $transaction = PaymentService::logTransaction([
                'email' => $user->email,
                'type' => $transactionType,
                'payment_type' => $transactionType,
                'program_id' => $details->id,
                'coupon_id' => null,
                'facilitator_id' => null,
                'amount' => $data['amount'],
                'transid' => $transid,
                'invoice_id' => $invoice_id,
                'payment_mode' => 0,
                'preferred_timing' => null,
                'name' => $user->name,
                'phone' => $user->phone,
                'location' => $data['location'],
                'training_mode' => null,
                'meta' => null,
                'is_package' => 0,
                'status' => 'complete',
                'balance' => $balance,
                't_type' => $data['bank'],
                'program_ids' => [(int) $details->id],
                'currency' => 'NGN',
                'currency_symbol' => '₦',
                'expected_amount' => $programFee,
            ]);

            PaymentService::createUserAndAttachPrograms($transaction);
            $transaction = $transaction->fresh();

            PaymentThread::create([
                'program_id' => $transaction->program_id,
                'user_id' => $transaction->user_id,
                'payment_id' => $transaction->id,
                'transaction_id' => PaymentService::getReference('PYTHRD'),
                't_type' => strtolower($transaction->t_type),
                'parent_transaction_id' => $transaction->transid,
                'amount' => $transaction->amount,
            ]);

            //send mail here
            $details = [
                'programFee' => $programFee,
                'programName' => $programName,
                'programAbbr' => $programAbbr,
                'balance' => $balance,
                'message' => $message,
                'booking_form' => base_path() . '/uploads' . '/' . $bookingForm,
                'invoice_id' =>  $invoice_id,
            ];

            // $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            // return view('emails.receipt', compact('data', 'details'));

            return back()->with('message', 'Student added succesfully');
        }
    }

    public function show($id)
    //tweaked this to send mails
    {

        if(checkRoleHas(['Admin'])) {
            $user = User::findorFail($id);
            $program = Program::all();

            if ($user->amount == $user->program->e_amount) {
                $message = $this->dosubscript2($user->balance);
            } else {
                $message = $this->dosubscript1($user->balance);
            }
            //determine the program details
            $details = [
                'programFee' => $user->program->p_amount,
                'programName' => $user->program->p_name,
                'programAbbr' => $user->program->p_abbr,
                'balance' => $user->balance,
                'message' => $user->$message,
                'booking_form' => base_path() . '/uploads' . '/' . $user->program->booking_form,
                'invoice_id' =>  $user->invoice_id,
                'message' => $message,
            ];

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'bank' => $user->t_type,
                'amount' => $user->amount,
            ];
            //generate pdf from receipt view
            $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            return view('emails.receipt', compact('data', 'details'));
            
            return back()->with('message', 'Receipt sent succesfully');
        }
        return back();
    }

    public function edit($id)
    {
        $user = User::findorFail($id);
        $programs = Program::where('id', '<>', 1)->get();

        if(checkRoleHas(['Admin'])) {
            $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
            $associated = Transaction::whereUserId($user->id)->pluck('program_id')->toArray() ?? null;
            $currentTransaction = TempTransaction::where('user_id', $user->id)
                ->where('status', 'complete')
                ->orderByDesc('created_at')
                ->first();

            return view('dashboard.admin.users.edit', compact('programs', 'user', 'associated', 'currentTransaction'));
        }

        if (checkRoleHas(['Facilitator', 'Grader'])) {
            $programs = FacilitatorTraining::whereUserId(resolveAuthUser()->id)->pluck('program_id');
            
            $count = Transaction::whereUserId($user->id)->whereIn('program_id', $programs)->count();

            if ($count < 1) {
                return back();
            }

            return view('dashboard.admin.users.edit', compact('programs', 'user'));
        }

        return back();
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Africa/Lagos");
        $user = User::findorFail($id);
        
        if ($request->filled('password')) {
            $password = bcrypt($request->password);
        } else {
            $password = $user->password;
        }
        $role = $request->filled('role') ? $request->role : $user->roles;

        try {
            DB::beginTransaction();

            if (checkRoleHas(['Facilitaor','Grader'])) {
                $user_trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $count = Transaction::whereUserId($user->id)->whereIn('program_id', $user_trainings)->count();

                if ($count < 1) {
                    return back();
                }
            }
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $password,
                'roles' => $role,
                'gender' => $request->gender,
            ]);
            
            if(checkRoleHas(['Admin'])) {
                $user->update([
                    'job_title' => $request->job_title,
                    'staffID' => $request->staffID,
                ]);


                // Get User programs and pop out of array
                $user_programs = DB::table('program_user')
                    ->where('user_id', $user->id)
                    ->pluck('program_id')
                    ->toArray();

                $newTrainings = $request['training']; // assumed array of IDs
                $selectedTrainingIds = collect($newTrainings)
                    ->map(fn ($programId) => (int) $programId)
                    ->unique()
                    ->values()
                    ->all();

                $toBeDeleted = array_diff($user_programs, $selectedTrainingIds);              // remove
                $selectedPrograms = Program::whereIn('id', $selectedTrainingIds)->get();
                $paymentTransid = $request->input('payment_transid');
                $existingTransaction = $paymentTransid
                    ? TempTransaction::where('user_id', $user->id)
                        ->where('transid', $paymentTransid)
                        ->where('status', 'complete')
                        ->first()
                    : null;

                // Handle program removals
                foreach ($toBeDeleted as $programId) {
                    DB::table('program_user')
                        ->where('user_id', $user->id)
                        ->where('program_id', $programId)
                        ->delete();
                }

                $syncTransid = null;
                if ($existingTransaction) {
                    $updateData = [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'program_ids' => $selectedTrainingIds,
                    ];

                    if (! $existingTransaction->is_package && !empty($selectedTrainingIds)) {
                        $updateData['program_id'] = $selectedTrainingIds[0];
                    }

                    $existingTransaction->update($updateData);
                    $syncTransid = $existingTransaction->transid;
                } elseif (!empty($selectedTrainingIds)) {
                    $amount = (float) $selectedPrograms->sum('p_amount');
                    $primaryProgramId = $selectedTrainingIds[0];
                    $transid = PaymentService::getReference('SYS-ADMIN');
                    $invoiceId = PaymentService::getInvoiceId();

                    $transaction = PaymentService::logTransaction([
                        'email' => $user->email,
                        'type' => 'full',
                        'payment_type' => 'full',
                        'program_id' => $primaryProgramId,
                        'coupon_id' => null,
                        'facilitator_id' => null,
                        'amount' => $amount,
                        'transid' => $transid,
                        'invoice_id' => $invoiceId,
                        'payment_mode' => 0,
                        'preferred_timing' => null,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'location' => null,
                        'training_mode' => null,
                        'meta' => null,
                        'is_package' => 0,
                        'status' => 'complete',
                        'balance' => 0,
                        't_type' => 'Transfer',
                        'program_ids' => $selectedTrainingIds,
                        'currency' => 'NGN',
                        'currency_symbol' => '₦',
                        'expected_amount' => $amount,
                    ]);

                    PaymentService::createUserAndAttachPrograms($transaction);
                    $transaction = $transaction->fresh();
                    $syncTransid = $transaction->transid;

                    PaymentThread::create([
                        'program_id' => $transaction->program_id,
                        'user_id' => $transaction->user_id,
                        'payment_id' => $transaction->id,
                        'transaction_id' => PaymentService::getReference('PYTHRD'),
                        't_type' => strtolower($transaction->t_type),
                        'parent_transaction_id' => $transaction->transid,
                        'amount' => $transaction->amount,
                    ]);
                }

                if (!empty($selectedTrainingIds) && !empty($syncTransid)) {
                    foreach ($selectedTrainingIds as $programId) {
                        Transaction::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'program_id' => $programId,
                            ],
                            [
                                'transid' => $syncTransid,
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            $error = $e->getMessage();
            return back()->with('error', $error);
        }


        if(checkRoleHas(['Admin'])) {
            return back()->with('message', 'Updated successfully');
        }
        return back();
    }

    public function destroy(User $user)
    {
        $user->programs()->detach();

        $user->delete();

        return back()->with('message', 'user deleted successfully');
    }

    public function mails()
    {

        $i = 1;
        $programs = Program::withCount('users')->where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
        $users = DB::table('program_user')->select('user_id')->orderby('created_at', 'DESC')->get();

        foreach ($users as $user) {
            $details = User::select('name', 'email')->whereId($user->user_id)->get();

            $user->name = $details->pluck('name')[0];
            $user->email = $details->pluck('email')[0];
        }

        $updateemails = UpdateMails::orderby('created_at', 'DESC')->get();
        return view('dashboard.admin.users.email', compact('programs', 'updateemails', 'i', 'users'));
    }

    public function emailHistory($id)
    {
        $email = UpdateMails::findOrFail($id);

        return view('dashboard.admin.users.emailhistory', compact('email'));
    }
    public function sendmail(Request $request)
    {
        ini_set('max_execution_time', 300); //5 minutes
        $data = $this->validate($request, [
            'type' => 'required | alpha',
            'subject' => 'required | min: 5',
            'content' => 'required | min: 10',
            'selectedemail' => 'nullable',
            'program' => 'nullable'
        ]);

        $data = $request->content;
        $subject = $request->subject;
        $name = resolveAuthUser()->name;

        if ($request->has('bulkrecipients') && $request->program == NULL && $request->type == 'bulkrecipients') {
            $recipients = preg_replace('#\s+#', ',', trim($request->bulkrecipients));
            $recipients = explode(",", $recipients);
            $program = 'Selected Recipients';

            foreach ($recipients as $recipient) {
                $name = User::whereEmail($recipient)->value('name');
                $name = $name ?? 'Participant';
                $details['name'] = $name;
                $details['subject'] = $subject;
                $details['content'] = $data;
                $details['type'] = 'bulk';
                $details['email'] = $recipient;

                $this->sendGenericEmail($details);

                // $this->sendWelcomeMail($details);
                // Mail::to($recipient)->send(new Email($data, $name, $subject));       
            }
        }

        if ($request->has('selectedemail') && $request->program == NULL && $request->type == 'selected') {
            $recipients = $request->selectedemail;
            $program = 'Selected Recipients';

            try {
                //code...
                Mail::to(Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL'))->send(new Email($data, $name, $subject));
            } catch (\Throwable $th) {
                //throw $th;
            }

            $details['subject'] = $request->subject;
            $details['content'] = $request->content;
            $details['type'] = 'bulk';

            foreach ($recipients as $recipient) {
                $name = User::whereEmail($recipient)->value('name');

                $this->sendGenericEmail($details);
                // Mail::to($recipient)->send(new Email($data, $name, $subject));       
            }
        }

        if ($request->has('program') && $request->program <> NULL && $request->type == 'bulk') {

            $recipients = DB::table('program_user')->where('program_id', $request->program)->get();

            $program = Program::where('id', $request->program)->value('p_name');

            $email = Settings::first()->value('OFFICIAL_EMAIL');

            try {
                // Mail::to($email)->send(new Email($data, $name, $subject));
                //code...
            } catch (\Throwable $th) {
                //throw $th;
            }
            $details['subject'] = $request->subject;
            $details['content'] = $request->content;
            $details['type'] = 'bulk';

            foreach ($recipients as $recipient) {
                $name = User::whereId($recipient->user_id)->value('name');
                $recipient->email = User::whereId($recipient->user_id)->value('email');
                $details['name'] = $name;
                $details['email'] = $recipient->email;
                $this->sendGenericEmail($details);
                // Mail::to($recipient->email)->send(new Email($data, $name, $subject));       
            }
        }

        if (count(Mail::failures()) > 0) {
            $error = array('The following emails were not sent:');

            foreach (Mail::failures() as $email_address) {
                $error = array_push($error,  -$email_address);
            }
            //return view with error
            return back()->with('error', $error);
        } else {
            $message =  "All " . count($recipients) . " emails were successfully sent!";

            UpdateMails::create([
                'sender' => Auth::user()->name,
                'program' => $program,
                'subject' => $request->subject,
                'content' => $request->content,
                'noofemails' => count($recipients),
            ]);

            //return view with success
            return back()->with('message', $message);
        }
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
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

    //update program payment statistics when adding new user
    // private function programStat($program_id, $paymentStatus){
    //     $program = Program::findorFail($program_id);
    //     if($paymentStatus == 1)
    //     $program->f_paid = $program->f_paid + 1;
    //     if($paymentStatus == 0)
    //     $program->p_paid = $program->p_paid + 1;
    //     $program->save(); 
    // }

    //update program payment statistics when adding new user
    // private function programStat2($program_id, $paymentStatus){
    //     $program = Program::findorFail($program_id);
    //     if($paymentStatus == 1){
    //         $program->f_paid = $program->f_paid + 1;
    //         $program->p_paid = $program->p_paid - 1;
    //     }
    //     if($paymentStatus == 0){
    //         $program->p_paid = $program->p_paid;
    //         $program->p_paid = $program->p_paid;
    //     }
    //     $program->save(); 
    // }
}
