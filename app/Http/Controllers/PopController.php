<?php

namespace App\Http\Controllers;

use PDF;
use Carbon;
use App\Pop;
use App\User;
use App\Program;
use App\Location;
use App\Settings;
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

        $transactions = Pop::with('program')->Ordered('date', 'DESC')->get();

        $i = 1;

        return view('dashboard.admin.payments.pop', compact('transactions', 'i') );
    }

    public function create(){
        $trainings = Program::select('id', 'p_end', 'p_name','p_amount','close_registration')->where('id', '<>', 1)->where('close_registration', 0)->where('p_end', '>', date('Y-m-d'))->ORDERBY('created_at', 'DESC')->get();
        // $locations = Location::select('title')->distinct()->get();
        
        return view('pop')
            ->with('trainings', $trainings);
            // ->with('locations', $locations);
    }

    public function store(Request $request){
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required | numeric',
            'bank' => 'sometimes',
            'amount' => 'required | numeric',
            'training' => 'required | numeric',
            'currency' => 'sometimes',
            'currency_symbol' => 'sometimes',
            // 'location' => 'nullable',
            'date' => 'date',
            'file' => 'required|max:2048|image',
        ]);
        
        //handle file
        $file = $data['name'].'-'.date('D-s');
        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $request->file('file')->storeAs('pop', $file.'.'.$extension  ,'uploads');
        $date = \Carbon\Carbon::parse($data['date'] . ' ' . now()->format('h:i:s'));

        // Check if already uploaded same pop
        $popCheck = Pop::whereEmail($data['email'])->whereAmount($data['amount'])->whereProgramId($data['training'])->count();
        if ($popCheck > 0) {
            return back()->with('error', 'You have already uploaded proof of payment for this training and with the same amount, kindly wait while an administrator approves your request');
        }

        if (isset($user) && !empty($user)) {
            $check = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training']])->where('balance', '<', 1)->count();

            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }

        // Check if user already paid for same program
        $user = User::whereEmail($data['email'])->value('id');
        if(isset($user) && !empty($user)){
            $check = DB::table('program_user')->where(['user_id' => $user, 'program_id' => $data['training']])->where('balance', '<', 1)->count();
            
            if ($check > 0) {
                return back()->with('error', 'You are already registered for this training! Kindly login with your email address and password');
            }
        }
       
        try{
            //Store new pop
            $pop = Pop::create([
                'name' => $data['name'],
                'email' =>  $data['email'],
                'phone' =>  $data['phone'],
                'bank' =>  $data['bank'],
                'amount' =>  $data['amount'],
                'program_id' =>  $data['training'],
                'currency' =>  $data['currency'],
                'currency_symbol' =>  $data['currency_symbol'],
                // 'location' =>  $data['location'],
                'date' => $data['date'],
                'file' => $filePath,
            ]);

            //Prepare Attachment
            $data['pop'] = base_path() . '/uploads' . '/' . $filePath;
            $data['training'] = Program::where('id', $data['training'])->value('p_name');
            $data['location'] = null;

            Mail::to(\App\Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL'))->send(new POPemail($data));

        }catch(\Exception $e) {
            // return back()->with('error', $e->getMessage());
        }  
        
        //Send mail to admin
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
        $allDetails = [
            'programFee' => $pop->program->p_amount,
            'programName' => $pop->program->p_name,
            'programAbbr' => $pop->program->p_abbr,
            'location' => $pop->location,
            'bookingForm' => $pop->program->booking_form,
            'programEarlyBird' => $pop->program->e_amount,
            'invoice_id' => $this->getReference('SYS_ADMIN'),
            'trans_id' => $this->getReference('SYS_ADMIN'),

            //Get User details
            'name' => $pop->name,
            'email' => $pop->email,
            'phone' => $pop->phone,
            'password' => bcrypt('12345'),
            'program_id' => $pop->prpgram_id,
            'amount' => $pop->amount,
            't_type' => $pop->bank,
            'currency' => $pop->currency,
            'currency_symbol' => $pop->currency_symbol,
            'date' => $pop->date,
        ];
        

        if($pop->amount > $allDetails['programFee']){
            return back()->with('warning', 'Student cannot pay more than program fee');
        }else
        {
            if(isset($pop->location)){
                $location = $pop->location;
            }else $location = ' ' ;
            $role_id = "Student";

            //check amount against payment
            if($allDetails['amount'] == $allDetails['programEarlyBird']){
                $balance = $allDetails['programEarlyBird'] - $allDetails['amount'];
                $message = $this->dosubscript2($allDetails['balance']);
                // $payment_type = 'EB';
            }else{
            $balance = $allDetails['programFee'] - $allDetails['amount'];
            
            $message = $this->dosubscript1($balance);
            // $payment_type = 'Full';
            }
            
            $paymentStatus =  $this->paymentStatus($balance);
            
            //Check if email exists in the system and attach it to the new pregram to that email
            $user = User::where('email', $allDetails['email'])->first();
            if(!$user){
                //save to database
                $user = User::updateOrCreate([
                    'name' => $allDetails['name'],
                    'email' => $allDetails['email'],
                    't_phone' => $allDetails['phone'],
                    'password' => $allDetails['password'],
                    'role_id' => $allDetails['role_id'],
       
                ]); 
            }

            // Check if pop has been approved initially
            // $check = DB::table('program_user')->where(['user_id'=>$user->id, 'program_id'=>$pop->program_id])->count();
            // if($check > 1){
            //     return back()->with('error', 'Payment details already exists in the system!');
            // }

            $user->programs()->attach($pop->program_id, [
                    'created_at' =>  date("Y-m-d H:i:s"),
                    't_amount' => $allDetails['amount'],
                    't_type' => $allDetails['t_type'],
                    't_location' => $allDetails['location'],
                    'paymentStatus' => $paymentStatus,
                    'balance' => $balance,
                    'transid' =>  $allDetails['invoice_id'],
                    'invoice_id' =>  $allDetails['invoice_id'],
                    'currency' => $allDetails['currency'],
                    'currency_symbol' => $allDetails['currency_symbol'],
                    'created_at' => $pop->date,
                ] );

            //send email 
            $data = [
                'name' => $allDetails['name'],
                'email' => $allDetails['email'],
                'bank' => $allDetails['t_type'],
                'amount' => $allDetails['amount'],
                'invoice_id' =>  $allDetails['invoice_id'],
                'transid' => $allDetails['invoice_id'],
                'programFee' => $allDetails['programFee'],
                'programName' => $allDetails['programName'],
                'programAbbr' => $allDetails['programAbbr'],
                'balance' => $balance,
                'message' => $message,
                'currency' => $allDetails['currency'],
                'currency_symbol' => $allDetails['currency_symbol'],
                'created_at' => $pop->date,
                'booking_form' => !is_null($allDetails['bookingForm']) ? base_path() . '/uploads'.'/'. $allDetails['bookingForm'] : null,
            ];
           
            // $pdf = PDF::loadView('emails.receipt', compact('data'));
            // return view('emails.receipt', compact('data'));
            $this->sendWelcomeMail($data);
            // dd($data);
           
            $pop->delete();
        return redirect(route('payments.index'))->with('message', 'Student added succesfully'); 
      
        }
    }

    public function reconcile(){
        $users = User::where('role_id', 'Student')->get();
        foreach($users as $user){
            //get user extra details
            $user->programs()->attach($user->program_id, [
                    'created_at' =>  $user->created_at,
                    't_amount' => $user->t_amount,
                    't_type' => $user->t_type,
                    't_location' => $user->location,
                    'paymentStatus' => $user->paymentStatus,
                    'balance' => $user->balance,
                    'invoice_id' =>  $user->invoice_id,
                ] );
            
        }
        return back()->with('message', 'All user details have been moved succesfully');
    }

    public function getfile($filename){
        if(auth()->user()->role_id != 'Admin'){
            return abort(404);
        }

        $realpath = base_path() . '/uploads/pop'. '/' .$filename;
        return response()->download($realpath);
    }

    public function destroy(Pop $pop){
        unlink( base_path() . '/uploads'.'/'. $pop->file);
        $pop->delete();
        return back()->with('message', 'Pop succesfully deleted');
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