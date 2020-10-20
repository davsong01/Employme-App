<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\User;
use App\Program;
use App\Mail\Email;
use App\UpdateMails;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
   
    public function index()
    {

        $i = 1;
        //$users = User::all();
       $users = User::where('role_id', 'Student')->orderBy('created_at', 'DESC')->get();
       //$users = DB::table('users')->where('role_id', '<>', "Admin")->get();
       $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC');
       if(Auth::user()->role_id == "Admin"){
         
          return view('dashboard.admin.users.index', compact('users', 'i', 'programs') );
        }elseif(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
           $users = User::where([
            'role_id' => "Student",
            'program_id' => Auth::user()->program_id,
           ])->orderBy('created_at', 'DESC')
           
           ->get();
            return view('dashboard.teacher.users.index', compact('users', 'i', 'programs') );
        }
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
    

            $users = User::orderBy('created_at', 'DESC');

            $user = User::all();

            $programs =  Program::select('id', 'p_end', 'p_name', 'close_registration')->where('id', '<>', 1)->ORDERBY('created_at', 'DESC')->get();

            return view('dashboard.admin.users.create', compact('users', 'user', 'programs'));
    }return back();
}

    public function store(Request $request)
    {

         //Check if program exist for the incoming training
        $user = User::where('email', $request->email)->first();

        if($user){
            $check = DB::table('program_user')->whereProgramId($request->training)->whereUserId
            ($user->id)->get();
            
            if($check->count() > 0){
                return back()->with('error', 'Participant has already paid for this training');
            }
        }

        //determine the program details
        $programFee = Program::findorFail($request['training'])->p_amount;
        $programName = Program::findorFail($request['training'])->p_name;
        $programAbbr = Program::findorFail($request['training'])->p_abbr;
        $bookingForm = Program::findorFail($request['training'])->booking_form;
        $programEarlyBird = Program::findorFail($request['training'])->e_amount;
        $invoice_id = 'Invoice'.rand(10, 100);
        
        if($request['amount'] > $programFee){
            return back()->with('warning', 'Student cannot pay more than program fee');
        }else
       {
        //check if earlybird bypass was checked
        if(!$request['earlybird']){
             //go ahead and do normal balance
             if($request['amount'] == $programEarlyBird){
                $balance = $programEarlyBird - $request['amount'];
                $message = $this->dosubscript2($balance);
                $payment_type = 'EB';
             }else{
            $balance = $programFee - $request['amount'];
            $message = $this->dosubscript1($balance);
            $payment_type = 'Full';
             }
            $paymentStatus =  $this->paymentStatus($balance);
            
           
        }else {
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
            'email' =>'required | email',
            'phone' =>'required',
            'training' =>'required',
            'amount' => 'required',
            'bank' =>'required',
            'location'=>'nullable',
            'password' => 'required',
            'role'=>'required',
            'gender' => 'nullable',
            'transaction_id' => 'nullable',
            'invoice_id' => '',
        ]);

          //Check if email exists in the system and attach it to the new pregram to that email
            if(!$user){
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
          
            // dd($data['training']);
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
            ] );

        //send mail here
        $details = [
            'programFee' => $programFee,
            'programName' => $programName,
            'programAbbr' => $programAbbr,
            'balance' => $balance,
            'message' => $message,
            'booking_form' => base_path() . '/uploads'.'/'. $bookingForm,
            'invoice_id' =>  $invoice_id,
        ];

        $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
        // return view('emails.receipt', compact('data', 'details'));
        Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
        
        return back()->with('message', 'Student added succesfully'); 
      
        }
    }

    public function show($id)
    //tweaked this to send mails
    {
        if(Auth::user()->role_id == "Admin"){
        $user = User::findorFail($id);
        $program = Program::all();
        
        if($user->t_amount == $user->program->e_amount){
            $message = $this->dosubscript2($user->balance);
         }else{
        $message = $this->dosubscript1($user->balance);
         }
        //determine the program details
        $details = [
            'programFee' => $user->program->p_amount,
            'programName' => $user->program->p_name,
            'programAbbr' => $user->program->p_abbr,
            'balance' => $user->balance,
            'message' => $user->$message,
            'booking_form' => base_path() . '/uploads'.'/'. $user->program->booking_form,
            'invoice_id' =>  $user->invoice_id,
            'message' =>$message,
        ];
  
        $data = [
            'name' =>$user->name,
            'email' =>$user->email,
            'bank' =>$user->t_type,
            'amount' =>$user->t_amount,
        ];
        //generate pdf from receipt view
        $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
        //return view('emails.receipt', compact('data', 'details'));
        Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
        
        return back()->with('message', 'Receipt sent succesfully'); 
    }return back();
    }

    public function edit($id)
    { 
            $user = User::findorFail($id);
            $programs = Program::where('id', '<>', 1)->get();
        if(Auth::user()->role_id == "Admin"){
        return view('dashboard.admin.users.edit', compact('programs','user'));
    }return back();
}

    public function update(Request $request, $id)
    {

        $user = User::findorFail($id);
        if($request['password']){
            $password = bcrypt($request['password']);
        }else $password = $user->password;

         $user->update([
            'name' => $request->name,
            'email' => $request->email,
            't_phone' => $request->phone,
            'password' => $password,
            'role_id' => $request->role,
            'gender' =>$request->gender,
        ]);  
        
        //I used return redirect so as to avoid creating new instances of the user and program class
        if(Auth::user()->role_id == "Admin"){
        return back()->with('message', 'Update successfully');
        } return back();
    
    }
    public function destroy(User $user)
    {  
        
        $user->programs()->detach();

        $user->delete();

        return redirect('users')->with('message', 'user deleted successfully');
    }

    public function mails(){
        $i = 1;
        $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get(); 
        $updateemails = UpdateMails::orderby('created_at', 'DESC')->get();
        return view('dashboard.admin.users.email', compact('programs', 'updateemails', 'i') );
    }

    public function emailHistory($id){
        $email = UpdateMails::findOrFail($id);

        return view('dashboard.admin.users.emailhistory', compact('email') );
    }
    public function sendmail(Request $request){
     
        $data = $this->validate($request, [
            'program' => 'required | numeric',
            'subject' => 'required | min: 5',
            'content' => 'required | min: 10'
        ]);

        $recipients = DB::table('program_user')->where('program_id', $request->program)->get();
        $data = $request->content;
        $subject = $request->subject;
        // dd($recipients);'
        $name = auth()->user()->name;
        Mail::to(config('custom.official_email'))->send(new Email($data, $name, $subject));
        foreach($recipients as $recipient){
            $name = User::whereId($recipient->user_id)->value('name');
            $recipient->email = User::whereId($recipient->user_id)->value('email');
       
            Mail::to($recipient->email)->send(new Email($data, $name, $subject));       
        }
      
        if( count(Mail::failures()) > 0 ) {
            $error = array('The following emails were not sent:');
            
               foreach(Mail::failures() as $email_address) {
                   $error = array_push($error,  - $email_address);
                }
            //return view with error
            return back()->with('error', $error);

            } else {
                $message =  "All ". count($recipients). " emails were successfully sent!";

                UpdateMails::create([
                    'sender' => Auth::user()->name,
                    'program' => Program::where('id', $request->program )->value('p_name'),
                    'subject' => $request->subject,
                    'content' => $request->content,
                    'noofemails' => count($recipients),
                ]);

                //return view with success
                return back()->with('message', $message);
            } 
    }
    // return view('dashboard.admin.users.email', compact('programs') );

    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
    
    //set balance and determine user receipt values
    private function dosubscript1($balance){
        if($balance <= 0){
            return 'Full payment';
        }return 'Part payment';
    }
    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }return 0;
    }
    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }return 'Part payment';
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
