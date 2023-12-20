<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Coupon;
use App\Program;
use App\Settings;
use Carbon\Carbon;
use App\CouponUser;
use App\PaymentMode;
use App\TempTransaction;
use App\Mail\Welcomemail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $check = 98;

    protected function getReference($prefix){
        date_default_timezone_set("Africa/Lagos");
        return $prefix.'-'.date('YmdHi') . '-' . rand(11111111, 99999999);
    }

    public function getInvoiceId($id = null)
    {
        date_default_timezone_set("Africa/Lagos");
        if(isset($id) && !empty($id)){
            $invoice_id = date("YmdHi") . '-' . $id . '-' . rand(10000, 99999);
        }else{
            $invoice_id = date("YmdHi") . '-' . rand(10000, 99999);
        }
        return $invoice_id;
    }
    
    public function sendWelcomeMail($data,$pdf=null){
        set_time_limit(360);
       
        // return view('emails.receipt', compact('data'));
        $provider = $this->emailProvider();
       
        if($provider == 'default'){
            if (isset($data['invoice_id'])) {
                $pdf = PDF::loadView('emails.printreceipt', compact('data'));
            } else $pdf = null;
            try {
                Mail::to($data['email'])->send(new Welcomemail($data, $pdf));
            } catch(\Exception $e){
                // Get error here
                return false;
            }

        }else{
           
            if (isset($data['invoice_id'])) {
                $pdf = PDF::loadView('emails.printreceipt', compact('data'));
                if(env('ENT') == 'local'){
                    $file = 'receipts/' . $data['invoice_id'] . ".pdf";
                    $filepath = public_path() . '/' . $file;
                }else{
                    $file = base_path() . '/receipts/' . $data['invoice_id'] . ".pdf";
                    $filepath = $file;
                }
                $filename = $data['invoice_id'] . ".pdf";

                file_put_contents($file, $pdf->output());
                $data['type'] = 'initial';
                $data['attachments'] = [
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'file' => $file,
                ];
               
            }
           
            if(isset($data['type']) && $data['type'] == 'pop'){
                // $data['attachments'] = $data['pop'];
                $data['attachments'] = [
                    'filename' => $data['realfilename'],
                    'filepath' => $data['pop'],
                    'file' => 'uploads/pop/'.$data['realfilename'],
                ];

            }
            
            $this->sendEmailWithElastic($data);
        }
        // \Log::info(['email'=> $data]);

        return;
    }

    public function sendGenericEmail($data)
    {
        set_time_limit(360);

        // return view('emails.receipt', compact('data'));
        $provider = $this->emailProvider();

        if ($provider == 'default') {
           
            try {
                Mail::to($data['email'])->send(new Welcomemail($data, $data));
            } catch (\Exception $e) {
                // Get error here
                return false;
            }
        } else {
           
            $this->sendEmailWithElastic($data);
        }
        // \Log::info(['email'=> $data]);

        return;
    }

    public function sendEmailWithElastic($data){
        // $setting = Settings::first();
        // $last_sent_elastic_email = '';
        // $elastic_email_count = '';

        // // check if last sent - now is up to 24 hours
        // $sent = Carbon::now() - $lastsent;
        // $lastsent = $setting->last_sent_elastic_email;
        // // \Log::info('Emails sending start at: ' .now());
        // if (  $sent >= 24) {
        //     dd('greater');
        // }else{
        //     dd('sddsd');
        // }
        // if ($now >= $hourlyRate) {

        // }

        // if($setting->elastic_email_count == 100){
        //     return back()->with('error', '100 emails have been sent with Elastic today');
        // }
        
        
        $url = 'https://api.elasticemail.com/v2/email/send';
        // dd($data);
       
        if(isset($data['attachments']) && !empty($data['attachments'])){
            $filename = $data['attachments']['filename'] ?? null;
            $file_name_with_full_path = $data['attachments']['filepath'] ?? null;
            $filetype = "application/pdf"; // Change correspondingly to the file type  
        }
        // try{
            $post = [
                'from' => 'training.employme@gmail.com',
                'fromName' => env('APP_NAME'),
                'apikey' => env('ELASTIC_KEY'),
                'subject' => $this->emailContent($data)['subject'],
                'to' => $data['email'],
                'bodyHtml' => $this->emailContent($data)['content'],
                'isTransactional' => false,
               
                // 'attachments' => $data['attachments'],
            ];
           
            if (isset($data['attachments']) && !empty($data['attachments'])) {
                $post['file_1'] = new \CurlFile($file_name_with_full_path, $filetype, $filename);
            }
           
            // get the file name and send in attachment
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            $result=curl_exec ($ch);
            curl_close ($ch);

            // Delete the attachment
            if (isset($data['attachments']) && !empty($data['attachments'])) {
                $this->deleteImage($data['attachments']['file']);
            }
            
            // if(isset($result) && $result->success == true){
            //     $setting->update[
            //         '' => $setting + 1
            //     ];
            // }

           
            return;
    
        // catch(Exception $ex){
        //     echo $ex->getMessage();
        // }
  
    }

    public function emailProvider(){
        $email_provider = Settings::first()->email_provider;
        return $email_provider;
    }

    protected function attachProgram($user, $program_id, $amount, $t_type, $location, $transid, $payment_type, $paymentStatus, $balance, $invoice_id, $payload){
        $user->programs()->attach($program_id, [
            'created_at' =>  date("Y-m-d H:i:s"),
            't_amount' => $amount,
            't_type' => $t_type,
            't_location' => $location,
            'transid' => $transid,
            'paymenttype' => $payment_type,
            'paymentStatus' => $paymentStatus,
            'balance' => $balance,
            'invoice_id' =>  $invoice_id,
            'payload' =>  $payload,
        ] );
    }
   
    public function creditFacilitator($facilitator, $program){
        $facilitator->update([
            'earnings' => $facilitator->earnings + $facilitator->earning_per_head,
        ]);
        
        return;
    }

    public function getCouponUsage($code, $email, $pid, $price, $admin=null){
        if(!is_null($admin)){
            $coupon = $code;
        }else{
            $coupon = Coupon::where('code', $code)->first();
        }
 
        if(!isset($coupon->id)){
            $coupon = Coupon::where('code', $code)->first();
        }
       
        if(isset($coupon) && !empty($coupon)){
            $usage = CouponUser::where('coupon_id', $coupon->id)->where('email', $email)->first();
           
            if(isset($usage)){
                if($usage->status == 1){
                    return NULL;
                }
                
            }else{
                CouponUser::Create([
                    'email' => $email,
                    'coupon_id' => $coupon->id,
                    'status' => 0,
                    'program_id' => $pid,
                ]);
            }
            
            $coupon_amount = $coupon->amount;
            $coupon_id = $coupon->id;
            $coupon_code = $coupon->code;
            
            return [
                'amount' => $coupon_amount,
                'id' => $coupon_id,
                'code' => $coupon_code,
                'grand_total' => $price - $coupon_amount,
            ];
        }

        return null;
        
    }

    public function getCouponValue($code, $pid=null, $admin=null){
        if(!is_null($admin)){
            $coupon = $code;
        }else{
            $coupon = Coupon::where('code', $code)->where('program_id', $pid)->first();
        }

        if(isset($coupon) && !empty($coupon)){
            $coupon_amount = $coupon->amount;
            $coupon_id = $coupon->id;
            $coupon_code = $coupon->code;

            $facilitator = $coupon->facilitator_id;
            
            return [
                'amount' => $coupon_amount,
                'id' => $coupon_id,
                'code' => $coupon_code,
            ];
        }
        return;
    }

    public function updateCouponStatus($email, $coupon_id, $program_id){
        // Check if user has used coupon for this program and delete
        $old = CouponUser::where(['email' => $email, 'program_id' => $program_id])->first();
        
        if(isset($old) && !empty($old)){
            $old->delete();
        }
        $c = CouponUser::where(['email' => $email, 'coupon_id' => $coupon_id, 'program_id' => $program_id])->
        update([
            'status' => 1,
            'coupon_id' => $coupon_id
        ]);
        
        return;
    }
    public function getEligibleEarners(){
        
    }

    public function verifyCoupon($request, $pid, $admin=null){
       
        $type['pid'] = $pid;
        if($request->coupon && !empty($request->coupon)){
            $verifyCoupon = $this->getCouponValue($request->coupon, $pid, $admin);
           
            if(!is_null($verifyCoupon)){
                $amount = $request->amount ?? $request['amount'];
                $response = $this->getCouponUsage($request->coupon, $request->email, $pid, $amount,'admin');
               
            }else{
                $response = null;
            }

        }else{
            $response = null;
        }
       
        return $response;
    }

    public function createTempDetails($request, $payment_mode){
        $temp = TempTransaction::where('email', $request->email)->first();
            if (isset($temp) && !empty($temp)) {
                $temp->update([
                    'type' => $request->payment_type,
                    'name' => $request->name,
                    'program_id' => $request['metadata']['pid'],
                    'coupon_id' =>  $request['metadata']['coupon_id'],
                    'facilitator_id' => $request['metadata']['facilitator'],
                    'amount' =>  $request['amount'],
                    'transid' =>  $request->transid,
                    'payment_mode' => $payment_mode,
                    'phone' => $request->phone,
                    'location' => $request->location ?? NULL,
                    'training_mode' => $request->modes ?? NULL,
                ]);
            } else {
                try {
                    $temp = TempTransaction::create([
                        'email' => $request->email,
                        'type' => $request->payment_type,
                        'program_id' => $request['metadata']['pid'],
                        'coupon_id' =>  $request['metadata']['coupon_id'],
                        'facilitator_id' => $request['metadata']['facilitator'],
                        'amount' =>  $request['amount'],
                        'transid' =>  $request->transid,
                        'payment_mode' => $payment_mode,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'location' => $request->location ?? NULL,
                        'training_mode' => $request->modes ?? NULL,

                    ]);
                } catch (\Throwable $th) {
                    dd($th->getMessage().'Line: ' .$th->getLine());
                    return $th->getMessage().'Line: ' .$th->getLine();
                }
            }
        return $temp;
    }

    public function confirmProgramAmount($pid, $type){
        $program_amount = Program::where('id', $pid)->first()->$type;
       
        return $program_amount;
    }

    public function getEarnings($amount, $coupon, $createdBy, $program, $programFacilitator = NULL){
        // Admin created coupon
        if(is_null($coupon)){
            return [
                'facilitator' => $data['facilitator_percent'] ?? 0,
                'admin' => $data['admin_percent'] ?? 0,
                'tech' => $data['tech_percent'] ?? 0,
                'faculty' =>  $data['faculty_percent'] ?? 0,
                'other' => $data['other_percent'] ?? 0,
            ];
        }
       
        if($coupon > 0){
            $coupon = $coupon;
        }else{
            $coupon = 0;
        }
       
        if($createdBy == 0){
            $toShare = $amount - $coupon;
        }else{
            $toShare = $amount;
        }
        
        $data['tech_percent'] = ($toShare * $program->tech_percent) /100;
        $data['faculty_percent'] =($toShare * $program->faculty_percent) /100;
        $data['admin_percent'] =($toShare * $program->admin_percent) /100;
        $data['other_percent'] = ($toShare * $program->other_percent) /100;

        if(isset($programFacilitator)){
            if($createdBy == $programFacilitator){
                $data['facilitator_percent'] = (($toShare * $program->facilitator_percent) /100) - $coupon;
            }else{
                $data[ 'facilitator_percent'] = (($toShare * $program->facilitator_percent) / 100);
            }
        }else{
            $data['facilitator_percent'] = 0;
        }
    
        return [
            'facilitator' => $data['facilitator_percent'] ?? 0,
            'admin' => $data['admin_percent'] ?? 0,
            'tech' => $data['tech_percent'] ?? 0,
            'faculty' =>  $data['faculty_percent'] ?? 0,
            'other' => $data['other_percent'] ?? 0,
        ];
    }

    protected function prepareTrainingDetails($program, $paymentDetails, $amount){

        $payment_mode = PaymentMode::find($paymentDetails->payment_mode);
        
        $processor = $payment_mode->processor ?? null;
        $training = $program;
        $data['programFee'] = $training->p_amount;
        $data['programName'] = $training->p_name;
        $data['programAbbr'] = $training->p_abbr;
        $data['bookingForm'] = $training->booking_form;

        //Create User details
        $data['name'] = $paymentDetails->name;
        $data['email'] = $paymentDetails->email;
        $data['phone'] = $paymentDetails->phone;
        $data['t_phone'] = $paymentDetails->phone;

        $data['password'] = bcrypt('12345');
        $data['program_id'] = $training->id;
        $data['amount'] = $amount;
        $data['t_type'] = strtoupper($processor) ?? 'bank_transfer';
        $data['payload'] = $paymentDetails->payload;
        
        // Create Facilitator details
        if (isset($paymentDetails->facilitator_id)) {
            $data['facilitator_id'] = $paymentDetails->facilitator_id;
            $data['facilitator_name'] = User::where('id', $paymentDetails->facilitator_id)->value('name');
        }
          
        if(isset($paymentDetails->location)){
            $data['location'] = $paymentDetails->location; 
        }else $data['location'] = ' ' ;
        
        $data['paymentModeDetails'] = [
            'id' => $payment_mode->id ?? 0,
            'type' => $payment_mode->type ?? 'bank_transfer',
            'processor' => $payment_mode->processor ?? 'bank_transfer',
            'currency' => $payment_mode->currency ?? '&#x20A6;',
            'currency_symbol' => $payment_mode->currency_symbol ?? '&#x20A6;',
            'exchange_rate' => $payment_mode->exchange_rate ?? 1,
        ];

        $data['role_id'] = "Student";
        $data['transid'] = $paymentDetails->transid;
        $data['t_location'] = $paymentDetails->location;
        $data['training_mode'] = $paymentDetails->training_mode;
       
        return $data;
    }

    public function prepareFreeTrainingDetails($training, $request, $bulk = false)
    {
        $data['programFee'] = $training->p_amount;
        $data['programName'] = $training->p_name;
        $data['programAbbr'] = $training->p_abbr;
        $data['bookingForm'] = $training->booking_form;
        //Create User details
        $data['name'] = isset($request->name) ? $request->name : $request['name'];
        $data['email'] = isset($request->email) ? $request->email : $request['email'];
        $data['phone'] = isset($request->phone) ? $request->phone : $request['phone'];
        $data['t_phone'] = isset($request->phone) ? $request->phone : $request['phone'];

        $data['password'] = bcrypt('12345');
        $data['program_id'] = $training->id;
        $data['amount'] = $training->p_amount;
        $data['t_type'] = ($bulk == false) ? 'Free Training' : 'Bulk Import';
        $data['payload'] = '';
       
        // Create Facilitator details
        if (!empty(\Session::get('facilitator_id'))) {
            $data['facilitator_id'] = \Session::get('facilitator_id');
            $data['facilitator_name'] = User::where('id', \Session::get('facilitator_id'))->value('name');
        }

        $data['location'] = isset($request->location) ? $request->location : $request['location'] ?? '';
    
        $data['paymentModeDetails'] = [
            'id' => 0,
            'type' => ($bulk == false) ? 'FREE TRAINING' : 'Bulk Import',
            'processor' => 'Bulk Import',
            'currency' => '',
            'currency_symbol' => '&#x20A6;',
            'exchange_rate' => 1,
        ];

        $data['role_id'] = "Student";
        $data['transid'] = $this->getInvoiceId();
        $data['t_location'] = $data['location'] ?? '';
        $data['training_mode'] = isset($request->training_mode) ? $request->training_mode : $request['training_mode'] ?? '';
        return $data;
    }

    public function createUserAndAttachProgramAndUpdateEarnings($data, $earnings, $coupon = NULL){
        //Check if email exists in the system and attach it to the new program to that email
        // $user = User::where('email', $data['email'])->first();
        
        $user = User::updateOrCreate(['email' => $data['email']], [
            'name' => $data['name'],
            't_phone' => $data['t_phone'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
        ]); 
       
        $data['coupon_amount'] = NULL;
        $data['coupon_id'] = NULL;
        $data['coupon_code'] = NULL;
       
        if(!is_null($coupon)){
            // $data['facilitator_id'] = $coupon->facilitator_id;
            $data['coupon_amount'] = $coupon->amount;
            $data['coupon_id'] = $coupon->id;
            $data['coupon_code'] = $coupon->code;
        }
        
        $data['booking_form'] = !is_null($data['bookingForm']) ? base_path() . '/uploads'.'/'. $data['bookingForm'] : null;
        $data['admin_earning'] = $earnings['admin'] ?? NULL;
        $data['facilitator_earning'] = $earnings['facilitator'] ?? NULL;
        $data['tech_earning'] = $earnings['tech'] ?? NULL;
        $data['faculty_earning'] = $earnings['faculty'] ?? NULL;
        $data['other_earning'] = $earnings['other'] ?? NULL;

        // if user doesnt exist, create new user and attach program
        // if (!isset($user)) {
        //     //save to database
        //     $user = User::updateOrCreate(['email' => $data['email']],[
        //         'name' => $data['name'],
        //         't_phone' => $data['t_phone'],
        //         'password' => $data['password'],
        //         'role_id' => $data['role_id'],
        //     ]); 
        // }
       
        $data['invoice_id'] = $this->getInvoiceId($user->id);
     
        //If program id is not in array of user program, attach program
        $userPrograms = DB::table('program_user')->where('user_id', $user->id)->where('program_id', $data['program_id'])->count();
       
        if( $userPrograms < 1){
            // Attach program
            $user->programs()->attach( $data['program_id'], [
                'created_at' =>  date("Y-m-d H:i:s"),
                't_amount' => $data['amount'], 
                't_type' => $data['t_type'], 
                't_location' => $data['location'], 
                'training_mode' => $data['training_mode'],
                'transid' => $data['transid'], 
                'paymenttype' => $data['payment_type'], 
                'paymentStatus' => $data['paymentStatus'], 
                'balance' => $data['balance'], 
                'invoice_id' => $data['invoice_id'],
                'facilitator_id' => $data['facilitator_id'] ?? NULL,
                'coupon_amount' => $data['coupon_amount'] ?? NULL,
                'coupon_id' => $data['coupon_id'] ?? NULL,
                'coupon_code' => $data['coupon_code'] ?? NULL,
                'admin_earning'=> $data['admin_earning'] ?? NULL,
                'facilitator_earning' => $data['facilitator_earning'] ?? NULL,
                'tech_earning' => $data['tech_earning'] ?? NULL,
                'faculty_earning' => $data['faculty_earning'] ?? NULL,
                'other_earning' => $data['other_earning'] ?? NULL,
                'currency' =>  \Session::get('currency'),
                'payload' => $data['payload'],
                'payment_mode' => $data['paymentModeDetails']['id'],
                
            ] );
        } 
       
        else{
            $data['user_id'] = $user->id;
            
            return $data;
        }
   
        $data['user_id'] = $user->id;
        
        return $data;
    }

    public function updateCoupon($c, $email, $pid){
        $coupon = CouponUser::where('coupon_id', $c)->where('email', $email)->where('program_id', $pid)->first();
       
        try {
            $coupon->status = 1;
            $coupon->save();
        } catch (\Throwable $th) {
            
        }
       
        return;
    }
    public function deleteFromTemp($temp){
        $temp->delete();

        return;
    }

    public function getUserDetails($user_id){
        $user = User::where('id', $user_id)->select('name', 'email', 't_phone')->first();
        return $user;
    }

    public function uploadImage($file, $folder, $width=null, $height=null)
    {
        $imageName = uniqid(9) . '.' . $file->getClientOriginalExtension();
       
        if (!is_dir($folder)) {
            mkdir($folder);
        }
      
        $imageFile = Image::make($file);
        if(isset($width) && isset($height)){
            $imageFile->resize($width, $height);
        }
        
        $imageFile->save($folder.'/' . $imageName);
    
        return $imageName;
    }

    public function uploadFileToUploads($image, $type, $folder, $width = null, $height = null){
        $file = uniqid(9) . '.' . $image->getClientOriginalExtension();
        
        if($type == 'image'){
            $image = Image::make($image)->resize($width, $height);
            Storage::disk('uploads')->put($folder.'/'. $file, (string) $image->encode());
        }
        if($type == 'booking_form'){
            $filePath = $image->storeAs('bookingforms', $file, 'uploads');
        }
       
        return $file;
    }
    
    public function deleteImage($image)
    {

        if (file_exists(public_path($image))) {
            unlink(public_path($image));
        }

        return;
    }

    public function deleteUploadsFile($imagePath)
    {

        if (file_exists(base_path().$imagePath)) {
            unlink(base_path().$imagePath);
        }

        return;
    }

    public function showCatalogue($program){
        // Check program
        $status = false;
        if($program->show_catalogue_popup =='yes' && auth()->user()->downloaded_catalogue == 'no'){
            $status = true;
        }

        return $status;
    }

    public function emailContent($data)
    {
        $content = "";
        if ($data['type'] == 'balance') {
            $content .= "<strong>Dear " . $data['name'] . ",</strong><br><br>";

            $subject = 'E - Receipt';
            $content .= '<div>
            <p style="text-align:justify !important">Your balance payment of ' . $data['currency_symbol'] . $data['amount'] . ' for ' . $data['programName'] . ' has been received.<br><br>You can now access all sections of your portal!</p>
            </div>';
        } else if ($data['type'] == 'initial') {
            $content .= "<strong>Dear " . $data['name'] . ",</strong><br><br>";

            $subject = 'E - Receipt';
            $content .= '<span style="text-align:justify !important">Your ' . $data['message'] . ' of ' . $data['currency_symbol'] . $data['amount'] . ' for the ' . $data['programName'] .' ('. $data['programAbbr'] . ')'.' via ' . $data['t_type'] . ' has been received. <br><br></span>
            <span><strong style="color:red">NOTE: </strong>Attached to this email are your E-receipt, booking form (if available) and feedback form (if available) which you are to print and bring along with you to the training center (NOT APPLICABLE FOR OUR ONLINE TRAININGS).</strong> <br><br></span>
            <span>Your customized portal is where you can view/download study materials for this training, view your payment history and do much more. <br><br></span>
            <span><strong>Your customized portal login details are:</strong> <br><br>
            Username: ' . $data['email'] . ' <br>
            Password: 12345 <small> <strong>(Use existing password if you are a returning participant)</strong> </small>
            </span><br><br><a href="' . config('app.url') . '/login' . '"><button style="background: green;text-decoration: none;padding: 10px;color: white;">Login to your Portal here</button></a><br></br><br>';
        }else if ($data['type'] == 'pop'){
            $subject = 'Proof of Payment Uploaded';
            $content .= "<strong>Dear Admin</strong>,<br>
                <p>Please find below proof of payment details with file attached </p>
                Name: ".$data['name']."<br>
                Email: ".$data['participant_email']. "<br>
                Phone: ".$data['phone']. "<br>
                Bank: ".$data['bank']. "<br>
                Amount: ".$data['amount']. "<br>
                Training: ".$data['training']. "<br>
                Date of Payment: ".$data['date']."<br>";

            if(isset($data['location'])){
                $content.= "Location: ".$data['location'];
            }

            if(isset($data['training_mode'])){
                $content .= "Training Mode: ".$data['training_mode'];
            }

            $content .= '<a href="' . config('app.url') . '/login' . '"><button style="background: green;text-decoration: none;padding: 10px;color: white;">Login to confirm Participant</button></a><br><br>Regards';
        }elseif($data['type'] == 'bulk'){
            $subject = $data['subject'];
            $content = $data['content'];
        }

        return ['content'=>$content,'subject'=>$subject];
    }

    public function adminMenus(){
        $menus = [
            [
                'id' => 1,
                'name' => 'Admin Dashboard',
                'route' => 'dashboard',
            ],
            [
                'id' => 2,
                'name' => 'Student Management',
                'route' => 'users.index',
            ],
            [
                'id' => 3,
                'name' => 'View All Facilitators',
                'route' => 'teachers.index',
            ],
            [
                'id' => 4,
                'name' => 'View all Trainings',
                'route' => 'programs.index',
            ],
            [
                'id' => 5,
                'name' => 'Trashed Trainings',
                'route' => 'programs.trashed',
            ],
            [
                'id' => 6,
                'name' => 'View All study Materials',
                'route' => 'materials.index',
            ],
            [
                'id' => 7,
                'name' => 'Coupons',
                'route' => 'coupon.index',
            ],
            [
                'id' => 8,
                'name' => 'Transactions',
                'route' => 'payments.index',
            ],
            [
                'id' => 9,
                'name' => 'Attempted Payments',
                'route' => 'pop.index',
            ],
            [
                'id' => 10,
                'name' => 'CRM Tool',
                'route' => 'complains.index',
            ],
            [
                'id' => 11,
                'name' => 'Modules',
                'route' => 'modules.index',
            ],
            [
                'id' => 12,
                'name' => 'Questions',
                'route' => 'questions.index',
            ],
            [
                'id' => 13,
                'name' => 'Pre Test Grades',
                'route' => 'pretest.select',
            ],
            [
                'id' => 14,
                'name' => 'Grades',
                'route' => 'posttest.results',
            ],
            [
                'id' => 15,
                'name' => 'Certificates',
                'route' => 'certificates.index',
            ],
            [
                'id' => 16,
                'name' => 'Score Settings',
                'route' => 'scoreSettings.index',
            ],
            [
                'id' => 17,
                'name' => 'Email Participants',
                'route' => 'users.mail',
            ],
            [
                'id' => 18,
                'name' => 'Payment modes',
                'route' => 'payment-modes.index',
            ],
            [
                'id' => 19,
                'name' => 'Settings',
                'route' => 'settings.edit',
            ],

            [
                'id' => 20,
                'name' => 'Update User',
                'route' => 'users.update',
            ],
            [
                'id' => 21,
                'name' => 'Add Modules',
                'route' => 'modules.create',
            ],

            [
                'id' => 22,
                'name' => 'Enable Resit for participants',
                'route' => 'modules.create',
            ],
        ];

        return $menus;
    } 

    public function printNameOnCertificate(){
        // Import the Intervention Image class

        // Specify the path to your input image
        $inputImagePath = base_path('uploads/certificates/b.jpg');
        
        //  = public_path('images/input.jpg');

        // Open an image file
        $image = Image::make($inputImagePath);
        $size = 150;
        // if number of letters in 
        // Add text to the image
        $image->text('Uchechukwu Emmanuel', 300, 1070, function($font) {
            $font->file(public_path('Pesaro-Bold.ttf'));
            $font->size(150);
            $font->color('#e10000');
            // $font->weight(700);
            // $font->align('left');
            // $font->valign('top');
        });

        // Save or display the modified image
        $outputImagePath = base_path('uploads/certificates/image.jpg');
        $image->save($outputImagePath);
        dd('finished');
        // ...


    }
}
