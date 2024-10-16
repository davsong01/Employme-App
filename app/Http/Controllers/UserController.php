<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\User;
use App\Models\Program;
use App\Mail\Email;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\UpdateMails;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {

        $i = 1;
        //$users = User::all();
        $users = User::where('role_id', '=', "Student")->orderBy('created_at', 'DESC')->get();
        //$users = DB::table('users')->where('role_id', '<>', "Admin")->get();
        $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC');
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.users.index', compact('users', 'i', 'programs'));
        } else if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $users = User::where([
                'role_id' => "Student",
                'program_id' => Auth::user()->program_id,
            ])->orderBy('created_at', 'DESC')

                ->get();
            return view('dashboard.teacher.users.index', compact('users', 'i', 'programs'));
        }
    }

    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $users = User::orderBy('created_at', 'DESC');
            $user = User::all();
            $programs = Program::select('id', 'p_end', 'p_name', 'close_registration')->where('id', '<>', 1)->where('close_registration', 0)->where('p_end', '>', date('Y-m-d'))->ORDERBY('created_at', 'DESC')->get();
            return view('dashboard.admin.users.create', compact('users', 'user', 'programs'));
        }
        return back();
    }

    public function store(Request $request)
    {
        //determine the program details
        $programFee = Program::findorFail($request['training'])->p_amount;
        $programName = Program::findorFail($request['training'])->p_name;
        $programAbbr = Program::findorFail($request['training'])->p_abbr;
        $bookingForm = Program::findorFail($request['training'])->booking_form;
        $programEarlyBird = Program::findorFail($request['training'])->e_amount;
        $invoice_id = 'Invoice' . rand(10, 100);

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
                'gender' => '',
                'transaction_id' => '',
                'invoice_id' => '',

            ]);

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                't_phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'program_id' => $data['training'],
                't_amount' => $data['amount'],
                't_type' => $data['bank'],
                't_location' => $data['location'],
                'role_id' => $data['role'],
                'gender' => $data['gender'],
                'transid' => $data['transaction_id'],
                'paymenttype' => $payment_type,
                'paymentStatus' => $paymentStatus,
                // 'bank' => $data['bank'],
                'balance' => $balance,
                'invoice_id' =>  $invoice_id,
                'profile_picture' => 'avatar.jpg',
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

            $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));

            return back()->with('message', 'Student added succesfully');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)

    {
        //This has been move to Admin/PaymentController     
    }

    public function edit($id)
    {
        $user = User::findorFail($id);
        $programs = Program::where('id', '<>', 1)->get();
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            return view('dashboard.admin.users.edit', compact('programs', 'user'));
        }
        return back();
    }


    public function update(Request $request, $id)
    {

        $user = User::findorFail($id);
        if ($request['password']) {
            $user->password = bcrypt($request['password']);
        };
        //check amount against payment
        $programFee = Program::findorFail($request['training'])->p_amount;

        $newamount = $user->t_amount + $request['amount'];
        if ($newamount > $programFee) {
            return back()->with('warning', 'Student cannot pay more than program fee');
        } else
            $balance = $programFee - $newamount;
        $message = $this->dosubscript1($balance);
        $paymentStatus =  $this->paymentStatus($balance);

        //update the program table here @ column fully paid or partly paid
        // $this->programStat2($request['training'], $paymentStatus);

        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->t_phone = $request['phone'];
        $user->program_id = $request['training'];
        $user->t_amount = $newamount;
        $user->balance = $balance;
        $user->t_type = $request['bank'];
        $user->t_location = $request['location'];
        $user->role_id = $request['role'];
        $user->gender = $request['gender'];
        // $user->bank = $request['bank'];
        $user->transid = $request['transaction_id'];
        $user->paymentStatus =  $paymentStatus;

        $user->save();
        //I used return redirect so as to avoid creating new instances of the user and program class
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return redirect('users')->with('message', 'user updated successfully');
        }
        return back();
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect('users')->with('message', 'user deleted successfully');
    }

    public function mails()
    {
        $i = 1;
        $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
        $updateemails = UpdateMails::orderby('created_at', 'DESC')->get();
        return view('dashboard.admin.users.email', compact('programs', 'updateemails', 'i'));
    }

    public function emailHistory($id)
    {
        $email = UpdateMails::findOrFail($id);

        return view('dashboard.admin.users.emailhistory', compact('email'));
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
