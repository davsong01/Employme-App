<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Settings;
use App\Mail\Welcomemail;
use App\Models\CouponUser;
use App\Models\PaymentMode;
use App\Models\Transaction;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

    public function sendWelcomeMail($data, $pdf = null)
    {
        set_time_limit(360);
        $transaction = $data['transaction'];
        
        $provider = $this->emailProvider();
        
        if ($provider == 'default') {
            $pdf = !empty($transaction->invoice_id)
                ? PDF::loadView('emails.printreceipt', compact('transaction'))
                : null;
            
            // return $pdf->stream('receipt-preview.pdf'); // preview pdf only

            try {
                if (env('ENT') == 'local') {
                    // \Log::info(['email' => $data]);
                    $data['subject'] = $this->emailContent($data)['subject'];
                    $data['content'] = $this->emailContent($data)['content'];
                    
                    $transaction->email = 'davsong16@gmail.com';
                    // $data['type'] = 'initial';
                    
                    // return (new \App\Mail\Welcomemail($data, $pdf))->render(); // preview email

                    Mail::to($transaction->email)->send(new Welcomemail($data, $pdf));
                } else {
                    $data['subject'] = $this->emailContent($data)['subject'];
                    $data['content'] = $this->emailContent($data)['content'];

                    Mail::to($transaction->email)->send(new Welcomemail($data, $pdf));
                }
            } catch (\Exception $e) {
                // dd($e->getMessage(), $e->getFile(), $e->getLine());
                return false;
            }
        } else {
            if (!empty($transaction->invoice_id)) {
                $pdf = !empty($transaction->invoice_id)
                    ? PDF::loadView('emails.printreceipt', compact('transaction'))
                    : null;
                if (env('ENT') == 'local') {
                    $file = 'receipts/' . $data['invoice_id'] . ".pdf";
                    $filepath = public_path() . '/' . $file;
                } else {
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

            if (isset($data['type']) && $data['type'] == 'pop') {
                // $data['attachments'] = $data['pop'];
                $data['attachments'] = [
                    'filename' => $data['realfilename'],
                    'filepath' => $data['pop'],
                    'file' => 'uploads/pop/' . $data['realfilename'],
                ];
            }

            $this->sendEmailWithElastic($data);
        }
        
        return;
    }

    public function sendGenericEmail($data)
    {
        set_time_limit(360);
        // return view('emails.receipt', compact('data'));
        $provider = $this->emailProvider();
        
        if ($provider == 'default') {
            try {
                if(env('ENT') == 'local'){
                    // Mail::to($data['email'])->send(new Welcomemail($data, $data));

                    \Log::info(['email_data' => $data]);
                }else{
                    Mail::to($data['email'])->send(new Welcomemail($data, $data));
                }
            } catch (\Exception $e) {
                return false;
            }
        } else {
            $this->sendEmailWithElastic($data);
        }
        
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
        if (env('ENT') == 'local') {
            \Log::info(['email' => $data]);
        } else {
            $url = 'https://api.elasticemail.com/v2/email/send';
            // dd($data);
    
            if(isset($data['attachments']) && !empty($data['attachments'])){
                $filename = $data['attachments']['filename'] ?? null;
                $file_name_with_full_path = $data['attachments']['filepath'] ?? null;
                $filetype = "application/pdf"; // Change correspondingly to the file type  
            }
            try{
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

                return;
        
            }catch(Exception $ex){
                \Log::info(['email sending error' => $ex->getMessage()]);
            }
        }
    }

    public function emailProvider(){
        $email_provider = Settings::first()->email_provider;
        return $email_provider;
    }

    protected function attachProgram($user, $program_id, $amount, $t_type, $location, $transid, $payment_type, $paymentStatus, $balance, $invoice_id, $payload){
        $user->programs()->attach($program_id, [
            'created_at' =>  date("Y-m-d H:i:s"),
            'amount' => $amount,
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
                    'preferred_timing' => $request['preferred_timing'] ?? null,
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
                        'preferred_timing' => $request['preferred_timing'] ?? null,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'location' => $request->location ?? NULL,
                        'training_mode' => $request->modes ?? NULL,

                    ]);
                } catch (\Throwable $th) {
                    // dd($th->getMessage().'Line: ' .$th->getLine());
                    return $th->getMessage().'Line: ' .$th->getLine();
                }
            }
        return $temp;
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

        $data['roles'] = "Student";
        $data['transid'] = $paymentDetails->transid;
        $data['t_location'] = $paymentDetails->location;
        $data['training_mode'] = $paymentDetails->training_mode;
        $data['preferred_timing'] = $paymentDetails->preferred_timing;
       
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
        $data['staffID'] = isset($request->staffID) ? $request->staffID : $request['staffID'];
        $data['phone'] = isset($request->phone) ? $request->phone : $request['phone'];
        $data['phone'] = isset($request->phone) ? $request->phone : $request['phone'];
        $data['password'] = bcrypt('12345');
        $data['program_id'] = $training->id;
        $data['amount'] = $training->p_amount;
        $data['t_type'] = ($bulk == false) ? 'Free Training' : 'Bulk Import';
        $data['payload'] = '';
        $data['metadata'] = $request['metadata'] ?? null;
        
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

        $data['roles'] = "Student";
        $data['transid'] = $this->getInvoiceId();
        $data['t_location'] = $data['location'] ?? '';
        $data['training_mode'] = isset($request->training_mode) ? $request->training_mode : $request['training_mode'] ?? '';
        $data['preferred_timing'] = isset($request->preferred_timing) ? $request->preferred_timing : $request['preferred_timing'] ?? '';
        
        return $data;
    }

    public function createUserAndAttachProgramAndUpdateEarnings($data, $earnings, $coupon = NULL){
        //Check if email exists in the system and attach it to the new program to that email
        // $user = User::where('email', $data['email'])->first();
        if(Auth::check() && empty($data['new'])){
            $user = resolveAuthUser();
        }else{
            // Check if user exists previously
            $existingUser = User::where(['email' => $data['email']])->first();
            
            if($existingUser){
                $user = $existingUser;
            }else{
                $user = User::updateOrCreate(['email' => $data['email']], [
                    'name' => $data['name'] ?? 'N/A',
                    'password' => $data['password'],
                    'roles' => $data['roles'],
                ]); 
            }
        }
        
        // Update 
        $user->name = $data['name'] ?? 'N/A';
        $user->staffID = $data['staffID'] ?? null;
        $user->metadata = $data['metadata'] ?? null;

        $user->phone = $data['phone'];
        $user->save();
        
        $data['coupon_amount'] = NULL;
        $data['coupon_id'] = NULL;
        $data['coupon_code'] = NULL;
        
        if(!is_null($coupon)){
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

        $data['invoice_id'] = $this->getInvoiceId($user->id);
        
        //If program id is not in array of user program, attach program
        $userPrograms = Transaction::where('user_id', $user->id)->where('program_id', $data['program_id'])->count();
        
        if( $userPrograms < 1 ){
            // Attach program
            $user->programs()->attach( $data['program_id'], [
                'created_at' =>  date("Y-m-d H:i:s"),
                'amount' => $data['amount'], 
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
                'preferred_timing' => $data['preferred_timing'] ?? null,
            ] );

            $attach = Transaction::where('user_id', $user->id)->where('program_id', $data['program_id'])->first();
        } 
        else{
            $data['user_id'] = $user->id;
            
            return $data;
        }
        
        $data['user_id'] = $user->id;
        $data['payment_id'] = $attach->id;
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
        $user = User::where('id', $user_id)->select('name', 'email', 'phone')->first();
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

    public function storeFileInUploadsDiskAndEncodeInDb($file, $folder, $preferred_name=null)
    {
        $filename = !empty($preferred_name) ? $preferred_name : uniqid(9) . '.' . $file->getClientOriginalExtension();

        $decodedFilename = base64_encode($filename);
        
        $file->storeAs($folder . '/', $filename, 'uploads');
        
        return $decodedFilename;
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

    public function deleteAllFilesInAPublicFolder($folderName)
    {
        $folderPath = public_path($folderName);

        if (File::exists($folderPath)) {
            $files = File::files($folderPath);

            // Loop through the files and delete each one
            foreach ($files as $file) {
                File::delete($file);
            }

            return true;
        } else {
            return false;
        }
    }


    public function showCatalogue($program){
        // Check program
        $status = false;
        if($program->show_catalogue_popup =='yes' && resolveAuthUser()->downloaded_catalogue == 'no'){
            $status = true;
        }

        return $status;
    }

    public function emailContent($data)
    {
        $content = "";
         
        if ($data['type'] == 'payment.from.toup') {
            $content .= "<strong>Dear " . $data['name'] . ",</strong><br><br>";

            $subject = 'E - Receipt';
            $content .= '<div>
            <p style="text-align:justify !important">Your payment of ' . $data['currency_symbol'] . $data['amount'] . ' for ' . $data['programName'] . ' has been received.<br><br></p>
            </div>';
        }elseif ($data['type'] == 'balance') {
            $content .= "<strong>Dear " . $data['name'] . ",</strong><br><br>";

            $subject = 'E - Receipt';
            $content .= '<div>
            <p style="text-align:justify !important">Your balance payment of ' . $data['currency_symbol'] . $data['amount'] . ' for ' . $data['programName'] . ' has been received.<br><br>You can now access all sections of your portal!</p>
            </div>';
        } elseif ($data['type'] == 'initial') {
            $content .= "<strong>Dear " . $data['name'] . ",</strong><br><br>";

            $subject = 'E - Receipt';
            $content .= '<span style="text-align:justify !important">Your ' . $data['message'] . ' of ' . $data['currency_symbol'] . $data['amount'] . ' for the ' . $data['programName'] .' ('. $data['programAbbr'] . ')'.' via ' . $data['t_type'] . ' has been received. <br><br></span>
            <span><strong style="color:red">NOTE: </strong>Attached to this email are your E-receipt, booking form (if available) and feedback form (if available) which you are to print and bring along with you to the training center (NOT APPLICABLE FOR OUR ONLINE TRAININGS).</strong> <br><br></span>
            <span>Your customized portal is where you can view/download study materials for this training, view your payment history and do much more. <br><br></span>
            <span><strong>Your customized portal login details are:</strong> <br><br>
            Username: ' . $data['email'] . ' <br>
            Password: 12345 <small> <strong>(Use existing password if you are a returning participant)</strong> </small>
            </span><br><br><a href="' . config('app.url') . '/login' . '"><button style="background: green;text-decoration: none;padding: 10px;color: white;">Login to your Portal here</button></a><br></br><br>';
        }elseif ($data['type'] == 'pop'){
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
        } elseif ($data['type'] == 'manual.wallet.topup') {
            $subject = 'Account top up Proof of Manual Payment Uploaded';
            $content .= "<strong>Dear Admin</strong>,<br>
                <p>Please find below proof of manual account top up details with file attached </p>
                Name: " . $data['name'] . "<br>
                Email: " . $data['participant_email'] . "<br>
                Amount: " .Settings::value('DEFAULT_CURRENCY').number_format($data['amount']) . "<br>
                Date Uploaded: " . now() . "<br>";
            
            $content .= '<a href="' . config('app.url') . '/payment-history' . '"><button style="background: green;text-decoration: none;padding: 10px;color: white;">Login to confirm Payment</button></a><br><br>Regards';
        }
       
        return ['content'=>$content,'subject'=>$subject];
    }

    public function adminMenus($type=null,$parent=null)
    {
        $menus = [
            // Menus
            // [
            //     'id' => 1,
            //     'name' => 'Admin Dashboard',
            //     'route' => 'home',
            //     'type' => 'menu',
            //     'order' => 1,
            //     'parentId' => null
            // ],
            [
                'id' => 2,
                'name' => 'Student Management',
                'route' => 'users.index',
                'type' => 'menu',
                'order' => 2,
                'icon_class' => 'fa fa-users',
                'parentId' => null
            ],
            // Student Management
                [
                    'id' => 20,
                    'name' => 'Add New Participant',
                    'route' => 'users.create',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 2
                ],
                [
                    'id' => 21,
                    'name' => 'View Participant',
                    'route' => 'users.edit',
                    'type' => 'access',
                    'order' => 2,
                    'parentId' => 2
                ],
                [
                    'id' => 22,
                    'name' => 'Update Participant',
                    'route' => 'users.update',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 2
                ],
                [
                    'id' => 23,
                    'name' => 'Peek Participant',
                    'route' => 'impersonate',
                    'type' => 'access',
                    'order' => 4,
                    'parentId' => 2
                ],
                [
                    'id' => 24,
                    'name' => 'Delete Participant',
                    'route' => 'users.destroy',
                    'type' => 'access',
                    'order' => 5,
                    'parentId' => 2
                ],
            [
                'id' => 3,
                'name' => 'Staff Management',
                'route' => 'teachers.index',
                'type' => 'menu',
                'order' => 3,
                'icon_class' => 'fas fas fa-user',
                'parentId' => null
            ],
                [
                    'id' => 31,
                    'name' => 'Add Staff',
                    'route' => 'teachers.create',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 3
                ],
                [
                    'id' => 32,
                    'name' => 'View Staff',
                    'route' => 'teachers.edit',
                    'type' => 'access',
                    'order' => 2,
                    'parentId' => 3
                ],
                [
                    'id' => 33,
                    'name' => 'Update Staff',
                    'route' => 'teachers.update',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 3
                ],
                [
                    'id' => 34,
                    'name' => 'Peek Staff',
                    'route' => 'admin-impersonate',
                    'type' => 'access',
                    'order' => 4,
                    'parentId' => 3
                ],
                [
                    'id' => 35,
                    'name' => 'Delete Staff',
                    'route' => 'teachers.destroy',
                    'type' => 'access',
                    'order' => 5,
                    'parentId' => 3
                ],
                [
                    'id' => 36,
                    'name' => 'Update Staff Menus',
                    'route' => 'teachers.update.menu',
                    'type' => 'access',
                    'order' => 6,
                    'parentId' => 3
                ],
                [
                    'id' => 37,
                    'name' => 'Update Staff Training Access',
                    'route' => 'teachers.update.training.access',
                    'type' => 'access',
                    'order' => 7,
                    'parentId' => 3
                ],
                [
                    'id' => 38,
                    'name' => 'View Staff Referral Details',
                    'route' => 'teachers.view.referral.details',
                    'type' => 'access',
                    'order' => 8,
                    'parentId' => 3
                ],
                [
                    'id' => 39,
                    'name' => 'Edit Role and status',
                    'route' => 'teachers.role.status',
                    'type' => 'access',
                    'order' => 9,
                    'parentId' => 3
                ],
            
            [
                'id' => 4,
                'name' => 'Company Admin Management',
                'route' => 'companyuser.index',
                'type' => 'menu',
                'order' => 4,
                'icon_class' => 'fa fa-solid fa-building',
                'parentId' => null
            ],
                [
                    'id' => 41,
                    'name' => 'Add Company Admin',
                    'route' => 'companyuser.create',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 4
                ],
                [
                    'id' => 42,
                    'name' => 'View Company Admin',
                    'route' => 'companyuser.edit',
                    'type' => 'access',
                    'order' => 2,
                    'parentId' => 4
                ],
                [
                    'id' => 43,
                    'name' => 'Update Company Admin',
                    'route' => 'companyuser.update',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 4
                ],
                [
                    'id' => 44,
                    'name' => 'Delete Company Admin',
                    'route' => 'companyuser.destroy',
                    'type' => 'access',
                    'order' => 4,
                    'parentId' => 4
                ],
            [
                'id' => 5,
                'name' => 'Training Management',
                'route' => 'training.management',
                'type' => 'menu',
                'order' => 5,
                'icon_class' => 'fas fa-chalkboard-teacher',
                'parentId' => null
            ],
                [
                    'id' => 51,
                    'name' => 'Add Training',
                    'route' => 'programs.create',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 5
                ],
            [
                'id' => 6,
                'name' => 'View all Trainings',
                'route' => 'programs.index',
                'type' => 'menu',
                'parentId' => 5
            ],
            [
                'id' => 61,
                'name' => 'Grouped Trainings',
                'route' => 'groupedprogram.index',
                'type' => 'menu',
                'parentId' => 5
            ],
            [
                'id' => 7,
                'name' => 'Trashed Trainings',
                'route' => 'programs.trashed',
                'type' => 'menu',
                'parentId' => 5
            ],
            [
                'id' => 8,
                'name' => 'Coupons',
                'route' => 'coupon.index',
                'type' => 'menu',
                'order' => 6,
                'icon_class' => 'fa fa-gift',
                'parentId' => null
            ],
                [
                    'id' => 81,
                    'name' => 'Add Coupon',
                    'route' => 'coupon.create',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 8
                ],
                [
                    'id' => 82,
                    'name' => 'View Coupon',
                    'route' => 'coupon.edit',
                    'type' => 'access',
                    'order' => 2,
                    'parentId' => 8
                ],
                [
                    'id' => 83,
                    'name' => 'Update Coupon',
                    'route' => 'coupon.update',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 8
                ],
                [
                    'id' => 84,
                    'name' => 'View Coupon Usage',
                    'route' => 'coupon.show',
                    'type' => 'access',
                    'order' => 4,
                    'parentId' => 8
                ],
                [
                    'id' => 85,
                    'name' => 'Delete Coupon',
                    'route' => 'coupon.destroy',
                    'type' => 'access',
                    'order' => 5,
                    'parentId' => 8
                ],
                
            [
                'id' => 9,
                'name' => 'Financials',
                'route' => 'financials',
                'type' => 'menu',
                'order' => 7,
                'icon_class' => 'far fa-money-bill-alt',
                'parentId' => null
            ],
            [
                'id' => 10,
                'name' => 'Attempted Payments',
                'route' => 'pop.index',
                'type' => 'menu',
                'parentId' => 9
            ],
            [
                'id' => 11,
                'name' => 'Proof of Payment',
                'route' => 'proof.payment',
                'type' => 'menu',
                'parentId' => 9
            ],
                [
                    'id' => 111,
                    'name' => 'Edit/Update Proof of Payment',
                    'route' => 'pop.edit',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 9
                ],
                
                [
                    'id' => 113,
                    'name' => 'Approve Proof of Payment',
                    'route' => 'pop.show',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 9
                ],
                [
                    'id' => 114,
                    'name' => 'Delete Proof of Payment',
                    'route' => 'pop.destroy',
                    'type' => 'access',
                    'order' => 4,
                    'parentId' => 9
                ],
            [
                'id' => 12,
                'name' => 'Transactions',
                'route' => 'payments.index',
                'type' => 'menu',
                'parentId' => 9
            ],
                [
                    'id' => 121,
                    'name' => 'Update Transaction',
                    'route' => 'payments.edit',
                    'type' => 'access',
                    'order' => 1,
                    'parentId' => 9
                ],
                [
                    'id' => 122,
                    'name' => 'Send Transaction Receipt',
                    'route' => 'payments.show',
                    'type' => 'access',
                    'order' => 2,
                    'parentId' => 9
                ],
                
                [
                    'id' => 123,
                    'name' => 'Delete Transaction',
                    'route' => 'payments.destroy',
                    'type' => 'access',
                    'order' => 3,
                    'parentId' => 9
                ],
            [
                'id' => 13,
                'name' => 'CRM Tool',
                'route' => 'complains.index',
                'type' => 'menu',
                'order' => 8,
                'icon_class' => 'far fa-comments',
                'parentId' => null
            ],
            [
                'id' => 14,
                'name' => 'LMS',
                'route' => 'lms',
                'type' => 'menu',
                'order' => 9,
                'icon_class' => 'fa fa-clipboard-list',
                'parentId' => null
            ],
            [
                'id' => 15,
                'name' => 'Materials',
                'route' => 'materials.index',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 15,
                'name' => 'Modules',
                'route' => 'modules.index',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 16,
                'name' => 'Questions',
                'route' => 'questions.index',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 17,
                'name' => 'Pre Test results',
                'route' => 'pretest.select',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 18,
                'name' => 'Post Test Results',
                'route' => 'posttest.results',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 19,
                'name' => 'Certificates',
                'route' => 'certificates.index',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 199,
                'name' => 'Certificates Templates',
                'route' => 'certificates.regeneration.templates',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 199,
                'name' => 'Certificate Gen. Requests',
                'route' => 'certificates.regeneration.requests',
                'type' => 'menu',
                'parentId' => 14
            ],
            [
                'id' => 20,
                'name' => 'Score Settings',
                'route' => 'scoreSettings.index',
                'type' => 'menu',
                'parentId' => 14
            ],
            
            [
                'id' => 21,
                'name' => 'Email Participants',
                'route' => 'users.mail',
                'type' => 'menu',
                'order' => 12,
                'icon_class' => 'fa fa-envelope',
                'parentId' => null
            ],
            [
                'id' => 22,
                'name' => 'Payment modes',
                'route' => 'payment-modes.index',
                'type' => 'menu',
                'order' => 15,
                'icon_class' => 'fa fa-credit-card',
                'parentId' => null
            ],
            [
                'id' => 23,
                'name' => 'Currency Management',
                'route' => 'currency.index',
                'type' => 'menu',
                'order' => 16,
                'icon_class' => 'fa fa-money-bill-wave',
                'parentId' => null
            ],
            [
                'id' => 26,
                'name' => 'Blacklist Management',
                'route' => 'blacklist.index',
                'type' => 'menu',
                'order' => 17,
                'icon_class' => 'fa fa-ban',
                'parentId' => null
            ],
            [
                'id' => 43,
                'name' => 'General Settings',
                'route' => 'settings.index',
                'order' => 20,
                'type' => 'menu',
                'icon_class' => 'fa fa-cog',
                'parentId' => null
            ],
            [
                'id' => 53,
                'name' => 'Certificate Verification Logs',
                'route' => 'certificate.verification.logs',
                'type' => 'menu',
                'order' => 10,
                'icon_class' => 'fa fa-certificate',
                'parentId' => null
            ],
            [
                'id' => 54,
                'name' => 'Truncate Verification Logs',
                'route' => 'truncate.verification.log',
                'type' => 'access',
                'order' => 1,
                'parentId' => 53
            ],
        ];

        $allmenus = collect($menus);

        if ($parent) {
            $allmenus = $allmenus->where('parentId', $parent);
        }

        if($type){
            $allmenus = $allmenus->where('type', $type);
            if($type == 'access'){
                return $allmenus;
            }
        }

        // Group children by parentId
        $grouped = $allmenus->groupBy('parentId');
        
        // Map parents and attach children
        $nestedMenus = $grouped->get(null, collect())->map(function ($parent) use ($grouped) {
            return array_merge($parent, [
                'children' => $grouped->get($parent['id'], collect())->toArray(),
            ]);
        });
        
        return $nestedMenus;
    }

    public function flattenedMenus($menus)
    {
        $routes = [];

        foreach ($menus as $menu) {
            if (isset($menu['route'])) {
                $routes[] = $menu['route'];
            }

            if (!empty($menu['children'])) {
                $routes = array_merge($routes, $this->flattenedMenus($menu['children']));
            }
        }

        return $routes;
    }

    public function companyMenus()
    {
        $menus = [
            // Categories
            [
                'id' => 1,
                'name' => 'Organization Dashboard',
                'route' => 'dashboard',
                'isParent' => 'yes',
            ],
            [
                'id' => 2,
                'name' => 'Participant Management',
                'route' => 'users.index',
                'isParent' => 'yes',
            ],
            
            [
                'id' => 3,
                'name' => 'LMS',
                'route' => '',
                'isParent' => 'yes',
            ],
            [
                'id' => 4,
                'name' => 'Pre Test Results',
                'route' => 'pretest.select',
                'isParent' => 'no',
                'parentId' => 3

            ],
            [
                'id' => 5,
                'name' => 'Post Tests Results',
                'route' => 'posttest.results',
                'isParent' => 'no',
                'parentId' => 3
            ],
            [
                'id' => 6,
                'name' => 'CRM Tool',
                'route' => 'complains.index',
                'isParent' => 'yes',
            ],
        ];

        return $menus;
    }

    public function adminTrainingPermissions($children=null)
    {
        $permissions = [
            [
                'id' => 1,
                'name' => 'Training CRUD',
                'order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Modules CRUD',
                'order' => 3,
            ],
            [
                'id' => 8,
                'name' => 'Materials CRUD',
                'order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Questions CRUD',
                'order' => 3,
            ],

            [
                'id' => 4,
                'name' => 'Certificate Actions',
                'order' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Tests Actions',
                'order' => 5,
            ],
            [
                'id' => 7,
                'name' => 'Score Settings Actions',
                'order' => 7,
            ],
            
            // Children
            [
                'id' => 1,
                'name' => 'Export Participant\'s details',
                'route' => 'program.detailsexport',
                'order' => 1,
                'category_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'Edit Training',
                'route' => 'programs.edit',
                'order' => 1,
                'category_id' => 1
            ],
            [
                'id' => 3,
                'name' => 'Disable CRM',
                'route' => 'crm.hide',
                'order' => 3,
                'category_id' => 1
            ],
            [
                'id' => 4,
                'name' => 'Enable CRM',
                'route' => 'crm.show',
                'order' => 4,
                'category_id' => 1
            ],
            [
                'id' => 5,
                'name' => 'Disable Result',
                'route' => 'results.disable',
                'order' => 5,
                'category_id' => 1
            ],
            [
                'id' => 6,
                'name' => 'Enable Result',
                'route' => 'results.enable',
                'order' => 6,
                'category_id' => 1
            ],
            [
                'id' => 51,
                'name' => 'Disable Certificate',
                'route' => 'certificates.disable',
                'order' => 5,
                'category_id' => 1
            ],
            [
                'id' => 61,
                'name' => 'Enable Certificate',
                'route' => 'certificates.enable',
                'order' => 6,
                'category_id' => 1
            ],
            [
                'id' => 7,
                'name' => 'Reset Password',
                'route' => 'password.reset',
                'order' => 7,
                'category_id' => 1
            ],
            [
                'id' => 8,
                'name' => 'Close Registration',
                'route' => 'registration.close',
                'order' => 8,
                'category_id' => 1
            ],
            [
                'id' => 9,
                'name' => 'Extend Registration',
                'route' => 'registration.open',
                'order' => 9,
                'category_id' => 1
            ],
            [
                'id' => 10,
                'name' => 'Open Earlybird',
                'route' => 'earlybird.open',
                'order' => 10,
                'category_id' => 1
            ],
            [
                'id' => 11,
                'name' => 'Close Earlybird',
                'route' => 'earlybird.close',
                'order' => 11,
                'category_id' => 1
            ],
            [
                'id' => 12,
                'name' => 'Clone Training',
                'route' => 'training.clone',
                'order' => 12,
                'category_id' => 1
            ],
            [
                'id' => 13,
                'name' => 'Bulk Import',
                'route' => 'training.import',
                'order' => 13,
                'category_id' => 1
            ],
            [
                'id' => 14,
                'name' => 'Trash Training',
                'route' => 'programs.destroy',
                'order' => 14,
                'category_id' => 1
            ],
            [
                'id' => 15,
                'name' => 'View Modules',
                'route' => 'modules.index',
                'order' => 15,
                'category_id' => 2
            ],
            [
                'id' => 15,
                'name' => 'Add Modules',
                'route' => 'modules.create',
                'order' => 15,
                'category_id' => 2
            ],
            [
                'id' => 16,
                'name' => 'Edit Modules',
                'route' => 'modules.edit',
                'order' => 16,
                'category_id' => 2
            ],
            [
                'id' => 17,
                'name' => 'Update Modules',
                'route' => 'modules.update',
                'order' => 17,
                'category_id' => 2
            ],
            [
                'id' => 18,
                'name' => 'Clone Modules',
                'route' => 'modules.show',
                'order' => 18,
                'category_id' => 2
            ],
            [
                'id' => 18,
                'name' => 'Enable Modules',
                'route' => 'modules.enable',
                'order' => 18,
                'category_id' => 2
            ],
            [
                'id' => 18,
                'name' => 'Disable Modules',
                'route' => 'modules.disable',
                'order' => 18,
                'category_id' => 2
            ],
            [
                'id' => 19,
                'name' => 'Delete Modules',
                'route' => 'modules.destroy',
                'order' => 18,
                'category_id' => 2
            ],
            [
                'id' => 20,
                'name' => 'Add Questions',
                'route' => 'questions.store',
                'order' => 20,
                'category_id' => 3
            ],
            
            [
                'id' => 22,
                'name' => 'Import Questions',
                'route' => 'questions.import',
                'order' => 22,
                'category_id' => 3
            ],
            [
                'id' => 23,
                'name' => 'Edit Questions',
                'route' => 'questions.edit',
                'order' => 23,
                'category_id' => 3
            ],
            [
                'id' => 23,
                'name' => 'Update Questions',
                'route' => 'questions.update',
                'order' => 23,
                'category_id' => 3
            ],
            [
                'id' => 24,
                'name' => 'Delete Questions',
                'route' => 'questions.destroy',
                'order' => 24,
                'category_id' => 3
            ],
            [
                'id' => 25,
                'name' => 'View Questions',
                'route' => 'single.program.questions.index',
                'order' => 25,
                'category_id' => 3
            ],
            [
                'id' => 80,
                'name' => 'View Materials',
                'route' => 'material.program.select',
                'order' => 1,
                'category_id' => 8
            ],
            [
                'id' => 81,
                'name' => 'Add Materials',
                'route' => 'materials.store',
                'order' => 2,
                'category_id' => 8
            ],
            [
                'id' => 22,
                'name' => 'Download Materials',
                'route' => 'getmaterial',
                'order' => 3,
                'category_id' => 8
            ],
            [
                'id' => 23,
                'name' => 'Clone Materials',
                'route' => 'material.clone',
                'order' => 4,
                'category_id' => 8
            ],
            [
                'id' => 23,
                'name' => 'Delete Materials',
                'route' => 'materials.destroy',
                'order' => 5,
                'category_id' => 8
            ],
            [
                'id' => 25,
                'name' => 'Perform Certificate Actions',
                'route' => 'certificate-actions',
                'order' => 25,
                'category_id' => 4
            ],
            [
                'id' => 26,
                'name' => 'Download Certificate',
                'route' => '',
                'order' => 26,
                'category_id' => 4
            ],
            [
                'id' => 27,
                'name' => 'Delete Certificate',
                'route' => 'certificates.destroy',
                'order' => 27,
                'category_id' => 4
            ],
            [
                'id' => 28,
                'name' => 'Add New Score Setting',
                'route' => 'scoreSettings.create',
                'order' => 28,
                'category_id' => 7
            ],
            [
                'id' => 29,
                'name' => 'Edit Score Setting',
                'route' => 'scoreSettings.edit',
                'order' => 29,
                'category_id' => 7
            ],
            [
                'id' => 29,
                'name' => 'Update Score Setting',
                'route' => 'scoreSettings.update',
                'order' => 29,
                'category_id' => 7
            ],
            [
                'id' => 30,
                'name' => 'Delete Score Setting',
                'route' => 'scoreSettings.destroy',
                'order' => 30,
                'category_id' => 7
            ],
            [
                'id' => 30,
                'name' => 'View Test Results',
                'route' => 'view.tests',
                'order' => 30,
                'category_id' => 5
            ],
            [
                'id' => 31,
                'name' => 'Export Test Results',
                'route' => 'result.export',
                'order' => 31,
                'category_id' => 5
            ],
            [
                'id' => 32,
                'name' => 'View Certification Score',
                'route' => 'view-certification-score',
                'order' => 32,
                'category_id' => 5
            ],
            [
                'id' => 33,
                'name' => 'View Roleplay Score',
                'route' => 'view-roleplay-score',
                'order' => 33,
                'category_id' => 5
            ],
            [
                'id' => 34,
                'name' => 'View Email Score',
                'route' => 'view-email-score',
                'order' => 34,
                'category_id' => 5
            ],
            [
                'id' => 35,
                'name' => 'View CRM Score',
                'route' => 'view-crm-score',
                'order' => 35,
                'category_id' => 5
            ],
            [
                'id' => 36,
                'name' => 'View Class Tests Score',
                'route' => 'view-class-score',
                'order' => 36,
                'category_id' => 5
            ],
            [
                'id' => 36,
                'name' => 'View Total Scores',
                'route' => 'view-total-score',
                'order' => 36,
                'category_id' => 5
            ],
            
            [
                'id' => 37,
                'name' => 'Update Certification Score',
                'route' => 'update-certification-score',
                'order' => 37,
                'category_id' => 5
            ],
            [
                'id' => 38,
                'name' => 'Update Roleplay Score',
                'route' => 'update-roleplay-score',
                'order' => 38,
                'category_id' => 5
            ],
            [
                'id' => 39,
                'name' => 'Update Email Score',
                'route' => 'update-email-score',
                'order' => 39,
                'category_id' => 5
            ],
            [
                'id' => 40,
                'name' => 'Update CRM Score',
                'route' => 'update-crm-score',
                'order' => 40,
                'category_id' => 5
            ],
            [
                'id' => 41,
                'name' => 'View Single Post Test Result',
                'route' => 'results.add',
                'order' => 41,
                'category_id' => 5
            ],
            [
                'id' => 41,
                'name' => 'Add/Update Grader\'s Comment',
                'route' => 'results.grader',
                'order' => 41,
                'category_id' => 5
            ],
            [
                'id' => 41,
                'name' => 'Add/Update Facilitator\'s Comment',
                'route' => 'results.facilitator',
                'order' => 41,
                'category_id' => 5
            ],
            [
                'id' => 41,
                'name' => 'Enable Post Test Resit',
                'route' => 'results.destroy',
                'order' => 41,
                'category_id' => 5
            ],
            
            [
                'id' => 42,
                'name' => 'Stop Resit Process',
                'route' => 'stopredotest',
                'order' => 42,
                'category_id' => 5
            ],
            [
                'id' => 41,
                'name' => 'View Single Pre Test Result',
                'route' => 'mocks.add',
                'order' => 41,
                'category_id' => 5
            ],
            
        ];

        $permissions = collect($permissions)->sortBy('order');
        
        if($children){
            $permissions = $permissions->whereNotNull('category_id');
            return $permissions;
        }else{
            // Group children by parentId
            $permissions = $permissions->groupBy('category_id');
            
            // Map parents and attach children
            $permissions = $permissions->get(null, collect())->map(function ($parent) use ($permissions) {
                return array_merge($parent, [
                    'children' => $permissions->get($parent['id'], collect())
                    ->sortBy('order')
                    ->toArray(),
                ]);
            });

            return $permissions;
        }
        
    }


    public function printNameOnCertificate(){
        // Import the Intervention Image class

        // Specify the path to your input image
        $inputImagePath = base_path('uploads/certificates/b.jpg');
        //status,size, top_offset, left_offset, color
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

    public function updateOrCreateTransaction($user, $allDetails)
    {

        if (isset($allDetails['existingTransaction'])) {
            $existingTransaction = DB::table('program_user')->where('id', $allDetails['existingTransaction']->id)
                ->update([
                    'amount' => $allDetails['amount'],
                    't_type' => $allDetails['t_type'],
                    't_location' => $allDetails['location'],
                    'paymentStatus' => $allDetails['paymentStatus'],
                    // 'training_mode' => $allDetails['training_mode'] ?? null,
                    'balance' => $allDetails['balance'],
                    'currency' => $allDetails['currency'],
                    'currency_symbol' => $allDetails['currency_symbol'],
                    'balance_transaction_id' => $allDetails['balance_transaction_id'],
                    'balance_paid' => $allDetails['date'],
                    'balance_amount_paid' => $allDetails['current_paid_amount'],
                    'coupon_id' => $allDetails['coupon_id'] ?? null,
                    'coupon_amount' => $allDetails['coupon_amount'] ?? null,
                    'coupon_code' => $allDetails['coupon_code'] ?? null,
                    'training_mode' => $allDetails['training_mode'] ?? null,
                    'preferred_timing' => $allDetails['preferred_timing'] ?? null,
                ]);
        } else {

            $programUser = $user->programs()->attach($allDetails['program_id'], [
                'amount' => $allDetails['amount'],
                't_type' => $allDetails['t_type'],
                't_location' => $allDetails['location'],
                'paymentStatus' => $allDetails['paymentStatus'],
                'balance' => $allDetails['balance'],
                'transid' =>  $allDetails['transaction_id'],
                'invoice_id' =>  $allDetails['invoice_id'],
                'currency' => $allDetails['currency'],
                'currency_symbol' => $allDetails['currency_symbol'],
                'created_at' => $allDetails['date'],
                'coupon_id' => $allDetails['coupon_id'] ?? null,
                'coupon_amount' => $allDetails['coupon_amount'] ?? null,
                'coupon_code' => $allDetails['coupon_code'] ?? null,
                'training_mode' => $allDetails['training_mode'] ?? null,
                'preferred_timing' => $allDetails['preferred_timing'] ?? null,
            ]);
        }
        // Update existing payment if 

        return $allDetails;
    }

    public function updateUserDetails($allDetails)
    {
        $user = User::where('email', $allDetails['email'])->first();
        if (!$user) {
            //save to database
            $user = User::Create([
                'name' => $allDetails['name'],
                'email' => $allDetails['email'],
                'phone' => $allDetails['phone'],
                'password' => bcrypt('12345'),
                'roles' => $allDetails['roles'],
            ]);
        } else {
            $user->update([
                'name' => $allDetails['name'],
                'phone' => $allDetails['phone'],
            ]);
        }

        return $user;
    }

}
