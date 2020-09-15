<?php

namespace App\Http\Controllers;

use PDF;
use App\Pop;
use App\User;
use App\Program;
use App\Mail\POPemail;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class PopController extends Controller
{
    public function index(){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        $transactions = Pop::with('program')->get();

        $i = 1;

        return view('dashboard.admin.payments.pop', compact('transactions', 'i') );
    }

    public function create(){
        $trainings = Program::select('id', 'p_end', 'p_name', 'close_registration')->where('id', '<>', 1)->where('close_registration', 0)->where('p_end', '>', date('Y-m-d'))->ORDERBY('created_at', 'DESC')->get();

        return view('pop')->with('trainings', $trainings);
    }

    public function store(Request $request){
        
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required | numeric',
            'bank' => 'required',
            'amount' => 'required | numeric',
            'training' => 'required | numeric',
            'location' => 'nullable',
            'file' => 'required | max:2048 | mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        
        //handle file
        $file = $data['name'].'-'.date('D-s');
        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $request->file('file')->storeAs('pop', $file.'.'.$extension  ,'uploads');

        //Store new pop
        $pop = Pop::create([
            'name' => $data['name'],
            'email' =>  $data['email'],
            'phone' =>  $data['phone'],
            'bank' =>  $data['bank'],
            'amount' =>  $data['amount'],
            'program_id' =>  $data['training'],
            'location' =>  $data['location'],
            'file' => $filePath,
        ]);
        
        //Prepare Attachment
        $data['pop'] = base_path() . '/uploads'.'/'. $filePath;
        $data['training'] = Program::where('id', $data['training'])->value('p_name');

        //Send mail to admin
        Mail::to(config('custom.official_email'))->send(new POPemail($data));
        return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
    }

    public function show(Pop $pop){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        //Check if program exist for the incoming training
        $user = User::where('email', $pop->email)->first();
        if($user){
            $check = DB::table('program_user')->whereProgramId($pop->training)->whereUserId
            ($user->id)->get();
        
            if($check->count() > 0){
                return back()->with('error', 'Participant has already paid for this training');
            }
        }
        //determine the program details
        $programFee = $pop->program->p_amount;
        $programName = $pop->program->p_name;
        $programAbbr = $pop->program->p_abbr;
        $bookingForm = $pop->program->booking_form;
        $programEarlyBird = $pop->program->e_amount;
        $invoice_id = 'Invoice'.rand(10, 100);

        //Get User details
        $name = $pop->name;
        $email = $pop->email;
        $phone = $pop->phone;
        $password = bcrypt('12345');
        $program_id = $pop->prpgram_id;
        $amount = $pop->amount;
        $t_type = $pop->bank;
        

        if($pop->amount > $programFee){
            return back()->with('warning', 'Student cannot pay more than program fee');
        }else
        {
            if(isset($pop->location)){
                $location = $pop->location;
            }else $location = ' ' ;
            $role_id = "Student";

            //check amount against payment
            if($amount == $programEarlyBird){
                $balance = $programEarlyBird - $amount;
                $message = $this->dosubscript2($balance);
                // $payment_type = 'EB';
            }else{
            $balance = $programFee - $amount;
            
            $message = $this->dosubscript1($balance);
            // $payment_type = 'Full';
            }
            
            $paymentStatus =  $this->paymentStatus($balance);

            //Check if email exists in the system and attach it to the new pregram to that email
            $user = User::where('email', $email)->first();
            if(!$user){
                //save to database
                $user = User::updateOrCreate([
                    'name' => $name,
                    'email' => $email,
                    't_phone' => $phone,
                    'password' => $password,
                    'role_id' => $role_id,
                ]); 
            }

            $user->programs()->attach($pop->program_id, [
                    'created_at' =>  date("Y-m-d H:i:s"),
                    't_amount' => $amount,
                    't_type' => $t_type,
                    't_location' => $location,
                    'paymentStatus' => $paymentStatus,
                    'balance' => $balance,
                    'invoice_id' =>  $invoice_id,
                ] );

            //send email
            $details = [
                'programFee' => $programFee,
                'programName' => $programName,
                'programAbbr' => $programAbbr,
                'balance' => $balance,
                'message' => $message,
                'booking_form' => base_path() . '/uploads'.'/'.$bookingForm,
                'invoice_id' =>  $invoice_id,
            ];
      
            $data = [
                'name' =>$name,
                'email' =>$email,
                'bank' =>$t_type,
                'amount' =>$amount,
            ];

            $pdf = PDF::loadView('emails.receipt', compact('data', 'details'));
            // return view('emails.receipt', compact('data', 'details'));
            Mail::to($data['email'])->send(new Welcomemail($data, $details, $pdf));
                
        
        return back()->with('message', 'Student added succesfully'); 
      
        }
    }

    public function getfile($filename){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        $realpath = base_path() . '/uploads/pop'. '/' .$filename;
        return response()->download($realpath);
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance){

        if($balance <= 0){
            return 'Full payment';
        }else return 'Part payment';
    }

    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }elseif ($balance > 0){
        } return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }else return 'Part payment';
    }
}
