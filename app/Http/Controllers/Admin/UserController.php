<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\User;
use App\Result;
use App\Program;
use App\Location;
use App\Settings;
use App\Mail\Email;
use App\Transaction;
use App\UpdateMails;
use App\Mail\Welcomemail;
use App\Exports\UsersExport;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function importExport($p_id)
    {
        if (Auth::user()->role_id == "Admin" || Auth::user()->role_id == "Facilitator") {

            return view('dashboard.admin.users.import', compact('p_id'));
        }
        return abort(404);
    }

    public function downloadBulkSample($filename)
    {
        $realpath = base_path() . '/uploads/' . $filename;
        return response()->download($realpath);
    }

    public function import(Request $request)
    {
        if (Auth::user()->role_id == "Admin" || Auth::user()->role_id == "Facilitator") {

            $this->validate(request(), [
                'file' => 'required|
				mimetypes:xlsv,xlsx,xls,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
				application/excel,application/x-excel,application/x-msexcel,text/comma-seperated-values, text/csv'
            ], [
                'file.mimetypes' => 'The file must be a file of type: xlsx'
            ]);

            try {
                Excel::import(new UsersImport($request->p_id), request()->file('file'));
            } catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();
                return back()->with('error', $error);
            }
            return back()->with('message', 'Data has been imported succesfully');
        }
        return abort(404);
    }

    public function index()
    {
        $i = 1;
        $users = User::withCount('programs')->where('role_id', 'Student')->orderBy('created_at', 'DESC')->get();

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.users.index', compact('users', 'i'));
        } elseif (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            $programs = FacilitatorTraining::whereUserId(Auth::user()->id)->pluck('program_id');
            $users = Transaction::with('user')->whereIn('program_id', $programs)->orderBy('created_at', 'DESC')
                ->get();
            return view('dashboard.teacher.users.index', compact('users', 'i', 'programs'));
        }
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
        $result = Result::whereId($result_id)->first();

        // if(is_null($result->certification_test_details)){
        //     return back()->with('error', 'User has not written certification test');
        // }

        User::whereId($user_id)->update(['redotest' => 0]);
        $result->endRedoTest();

        return back()->with('message', 'Update Successful');
    }

    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $users = User::orderBy('created_at', 'DESC');
            $locations = Location::select('title')->orderBy('created_at', 'DESC')->get();
            $user = User::all();

            $programs =  Program::select('id', 'p_end', 'p_name', 'p_amount', 'close_registration')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

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
                    't_phone' => $data['phone'],
                    'password' => bcrypt($data['password']),
                    'role_id' => $data['role'],
                    'gender' => $data['gender'],
                ]);
            }

            $user->programs()->attach($request->training, [
                'created_at' =>  date("Y-m-d H:i:s"),
                't_amount' => $data['amount'],
                't_type' => $data['bank'],
                't_location' => $data['location'],
                'transid' => $data['transaction_id'],
                'paymenttype' => $payment_type,
                'paymentStatus' => $paymentStatus,
                'balance' => $balance,
                'invoice_id' =>  $invoice_id,
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

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $user = User::findorFail($id);
            $program = Program::all();

            if ($user->t_amount == $user->program->e_amount) {
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
                'amount' => $user->t_amount,
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
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
            $associated = Transaction::whereUserId($user->id)->pluck('program_id')->toArray() ?? null;

            return view('dashboard.admin.users.edit', compact('programs', 'user', 'associated'));
        }

        if (!empty(array_intersect(graderRoles(), Auth::user()->role())) || Auth::user()->role_id == "Facilitator") {
            $programs = FacilitatorTraining::whereUserId(Auth::user()->id)->pluck('program_id');
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
        if ($request['password']) {
            $password = bcrypt($request['password']);
        } else $password = $user->password;

        try {
            if (Auth::user()->role_id != 'Admin') {
                $programs = FacilitatorTraining::whereUserId(Auth::user()->id)->pluck('program_id');
                $count = Transaction::whereUserId($user->id)->whereIn('program_id', $programs)->count();

                if ($count < 1) {
                    return back();
                }
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                't_phone' => $request->phone,
                'password' => $password,
                'role_id' => $request->role,
                'gender' => $request->gender,
            ]);

            if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
                // Get User programs and pop out of array
                $user_programs = DB::table('program_user')->where('user_id', $user->id)->pluck('program_id')->toArray();
                $newTrainings = $request['training'];
                $trainings = array_unique(array_merge($user_programs, $newTrainings));
                $toBeDeleted = array_diff($user_programs, $newTrainings);
                // dd($user_programs, $toBeDeleted, $trainings, $newTrainings);
                foreach ($trainings as $value) {
                    if (!in_array($value, $user_programs)) {
                        $training = Program::find($value);
                        if ($training) {
                            $user->programs()->attach($training->id, [
                                'created_at' =>  date("Y-m-d H:i:s"),
                                'invoice_id' => date('YmdH') . '-' . rand(1111, 9999) . '-' . 'SYS_ADMIN',
                                'transid' => date('YmdH') . '-' . rand(1111, 9999) . '-' . 'SYS_ADMIN',
                                't_amount' => $training->p_amount,
                                't_amount' => $training->p_amount,
                                't_type' => 'System Admin',
                                't_location' => null,
                                'paymentStatus' => 1,
                                'balance' => 0,
                                'invoice_id' =>  'Invoice' . $user->id,
                            ]);
                        }
                    }

                    if (in_array($value, $toBeDeleted)) {
                        DB::table('program_user')->where('user_id', $user->id)->where('program_id', $value)->delete();
                    }
                }
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return back()->with('error', $error);
        }


        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return back()->with('message', 'Update successfully');
        }
        return back();
    }

    public function destroy(User $user)
    {

        $user->programs()->detach();

        $user->delete();

        return redirect('users')->with('message', 'user deleted successfully');
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
        $name = auth()->user()->name;

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
                Mail::to(\App\Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL'))->send(new Email($data, $name, $subject));
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
