<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Result;
use App\Models\Program;
use App\Models\Certificate;
use App\Models\Transaction;
use App\Models\ScoreSetting;
use Illuminate\Http\Request;
use App\Models\UtilityTracker;
use Illuminate\Support\Carbon;
use App\Models\UtilityCronTask;
use App\Models\FacilitatorTraining;
use App\Http\Controllers\Controller;
use App\Models\CertificateGenerationHistory;
use App\Models\CertificateStatusLog;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userid = resolveAuthUser()->id;
        $i = 1;
        if (checkRoleHas(['Admin','Grader','Facilitator'])) {
            if(checkRoleHas(['Admin'])){
                $programs = Program::withCount('certificates')->where('id', '<>', 1)->whereNULL('parent_id')->orderBy('created_at', 'desc')->get();
                return view('dashboard.admin.certificates.selecttraining', compact('programs', 'i'));
            }else{
                $programs = FacilitatorTraining::whereUserId(resolveAuthUser()->id)->get();
                if ($programs->count() > 0) {
                    foreach ($programs as $program) {
                        $program['id'] = $program->program_id;
                        $program['p_name'] = Program::whereId($program->program_id)->value('p_name');

                        $program['certificates_count'] = Certificate::whereProgramId($program->program_id)->count();
                    }
                }

                return view('dashboard.admin.certificates.selecttraining', compact('programs', 'i'));
            }

        }

        if (checkRoleHas(['Student'])) {
            $transaction = Transaction::where('program_id',  $request->p_id)->where('user_id', resolveAuthUser()->id)->first();

            $program = $transaction->program;
            
            // Checks
            if ($program->allow_payment_restrictions_for_certificates == 'yes') {
                if ($transaction->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $user_balance->currency_symbol . number_format($user_balance->balance) . ' in order to get view/download certificate');
                }
            }

            // if (!empty($program->scoreSettings)) {
                
            if ($program->only_certified_should_see_certificate == 'yes') {
                $details = certificationStatusNew($transaction->training_result, $program, resolveAuthUser());
                
                $certification_status = $details->certification_status ?? NULL;
                if (!$certification_status || $certification_status == 'NOT CERTIFIED') {
                    return back()->with('error', 'You must be certified before you can view certificate');
                }
            }

            // }
            
            $certificate = Certificate::with(['user'])->where('user_id', resolveAuthUser()->id)->whereProgramId($request->p_id)->first();
            
            if (!isset($certificate)) {
                return back()->with('error', 'Certificate for selected program is not ready at this time, please try again or consult admin');
            }

            if ($certificate->show_certificate() == 'Disabled') {
                return back()->with('error', 'Certificate Unavailable at the moment, please check back');
            }

            return view('dashboard.student.certificates.index', compact('certificate', 'program'));
        }
        return back();
    }

    public function adminCertificates($program_id)
    {
    }

    public function create()
    {
        if (checkRoleHas(['Admin','Grader','Facilitator'])) {
            $programs = Program::withCount('users')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
            return view('dashboard.admin.certificates.create', compact('programs'));
        }
    }

    public function certificateStatus($user_id, $program_id, $status, $certificate_id)
    {
        $transaction = Transaction::where(['user_id' => $user_id, 'program_id' => $program_id])->first();
        $transaction->update(['show_certificate' => $status]);

        return back()->with('message', 'Status updated successfully');
    }

    public function selectUser(Request $request, $program_id)
    {
        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            $i = 1;
            $users = DB::table('program_user')->where('program_id', $request->program_id)->get();
            $certificates = Certificate::with(['user', 'program','certificateHistory'])->where('program_id', $request->program_id)->orderBy('created_at', 'desc')->get();
            
            foreach ($users as $user) {
                $user->name = User::whereId($user->user_id)->value('name');
                $user->certificates_count = Certificate::whereUserId($user->user_id)->whereProgramId($request->program_id)->count();
            }

            $program = Program::find($program_id);
            $score_settings = ScoreSetting::whereProgramId($request->program_id)->first();

            $p_id = $program->id;
            $p_name = $program->p_name;
            $certificate_settings = $program->auto_certificate_settings;

            return view('dashboard.admin.certificates.createcert', compact('users', 'p_id', 'p_name', 'certificates', 'i', 'score_settings','certificate_settings'));
        }
        return back();
    }

    public function save(Request $request)
    {
        if (checkRoleHas(['Admin','Grader','Facilitator'])) {
            $data = $this->validate($request, [
                'user_id' => 'required',
                'certificate' => 'required | max:3048 | mimes:pdf,doc,docx,jpg,jpeg,png',
                'p_id' => 'required'
            ]);

            $file = $data['certificate'];

            $imagePath = $file->storeAs('certificates', $file->getClientOriginalName(), 'uploads');
            $program = Program::find($request->p_id);
            $user = User::find($request->user_id);

            $certificate_number = generateCertificateNumber($program, $user);
            $certificte = Certificate::updateOrCreate(['user_id' =>  $request->user_id, 'program_id' => $request->p_id], [
                'user_id' =>  $request->user_id,
                'file' => $file->getClientOriginalName(),
                'program_id' => $request->p_id,
                'certificate_number' => $certificate_number,
            ]);

            Transaction::where(['user_id' => $request->user_id, 'program_id' => $request->p_id])->update(['show_certificate' => 0]);

            return back()->with('message', ' certificate succesfully added');
            // return redirect(route('certificates.create'))->with('message', ' certificate succesfully added'); 
        }

        return abort(404);
    }

    public function show(certificate $certificate)
    {
        return view('dashboard.admin.certificates.edit')->with('certificate', $certificate)->with('programs', Program::orderBy('created_at', 'desc')->get());
    }

    public function destroy(certificate $certificate, $internal=false)
    {
        $certificate_count = certificate::where('file', $certificate->file)->count();

        if ($certificate_count <= 1) {
            if (file_exists(base_path() . '/uploads/certificates' . '/' . $certificate->file)) {
                unlink(base_path() . '/uploads/certificates' . '/' . $certificate->file);
            }
        }

        Transaction::where(['program_id' => $certificate->program_id, 'user_id' => $certificate->user_id])->update(['show_certificate' => 0]);

        $certificate->delete();

        UtilityTracker::where(['key' => 'AGC-' . $certificate->program_id])->update([
            'start' => 0,
            'end' => 0
        ]);

        if($internal){
            return true;
        }
        //delete certificate from storage           
        return redirect('certificates')->with('message', 'certificate succesfully deleted');
    }

    public function getfile($filename)
    {
        $realpath = base_path() . '/uploads/certificates' . '/' . $filename;
        return response()->download($realpath);
    }

    public function modify(Request $request){
        set_time_limit(7600);

        $action = $request->action == 'disable' ? 0 : 1;
        $transactions = Transaction::with('user')->whereIn('user_id', $request->data)->where('program_id', $request->program_id);
    
        if(in_array($request->action, ['enable','disable'])){
            $transactions->update(['show_certificate' => $action]);
        }
        
        if($request->action == 'regenerate-certificate'){
            if (checkRoleHas(['Admin','Grader','Facilitator'])) {
                foreach ($transactions->get() as $transaction) {
                    $location = base_path('uploads/certificates');

                    $certificate = generateCertificate($request, $request->program_id, $location, $transaction->user);
                    ;

                    if (!$certificate) {
                        continue;
                        \Log::info('Certificate Generation Error');
                    }
                
                    Certificate::updateOrCreate(['user_id' =>  $transaction->user_id, 'program_id' => $request->program_id],[
                        'user_id' => $transaction->user_id,
                        'file' => $certificate['name'],
                        'certificate_number' => $certificate['certificate_number'],
                        'program_id' => $request->program_id,
                    ]);
                    
                    $transaction->show_certificate = 0;
                    $transaction->save();
                }

                return back()->with('Certificate successfully autugenerated');
            }
        }

        if ($request->action == 'delete-certificate') {
            if (checkRoleHas(['Admin','Grader','Facilitator'])) {
                $certificates = Certificate::whereIn('user_id', $request->data)->where('program_id', $request->program_id)->get();
                
                foreach ($certificates as $certificate) {
                    $this->destroy($certificate, true);
                }

                return back()->with('Certificate successfully autugenerated');
            }
        }
        
        return response()->json(['message' => 'success'], 200);
        
    }

    public function generateCertificates(Request $request, $program_id, $internal = false)
    {
        $pick = $request->pick;
        $show_certificate = $request->show_certificate ?? 0;
        set_time_limit(0);

        $cron_task = $request->use_cron;

        if (!empty($cron_task) && $cron_task == 'yes') {
            // Cron
            $payload = $request->except(['use_cron', 'prefix__']);
            $payload['program_id'] = $program_id;

            UtilityCronTask::updateOrcreate([
                'name' => 'Certificate Generation Task for Training Id: ' . $program_id,
                'key' => 'certificate-generation',
                'payload' => $payload
            ]);

            return back()->with('message', 'Succesfully logged to cron task');
        }

        // Check if the user has the required roles

        if ($internal) {
            $check = true;
        } else {
            $check = checkRoleHas(['Admin','Grader','Facilitator']);
        }

        if ($check) {
            $tracker = UtilityTracker::firstOrCreate(
                ['key' => 'AGC-' . $program_id],
                ['key' => 'AGC-' . $program_id, 'start' => 0, 'end' => 0]
            );
            $end = $tracker->end;

            // Fetch the transactions based on program ID, show_certificate status, and tracker end value
            $transactions = Transaction::with(['user', 'certificate'])
            ->where('program_id', $program_id)
                ->where('show_certificate', 0)
                ->whereDoesntHave('certificate', function ($query) use ($program_id) {
                    $query->where('program_id', $program_id);
                });

            if (!empty($cron_task) && $cron_task == 'yes') {
                $transactions = $transactions->where('id', '>', $end);
            }

            $transactions = $transactions->take($pick)->get();

            // Check if there are any eligible transactions
            if ($transactions->isEmpty()) {
                if ($internal) {
                    return [
                        'status' => 'failed',
                        'message' => 'Looks like all certificates have been generated!',
                    ];
                }
                return back()->with('error', 'Looks like all certificates have been generated!');
            }

            // Loop through each transaction
            foreach ($transactions as $transaction) {
                // Certificate storage location
                $location = base_path('uploads/certificates');

                // Generate the certificate
                $certificate = generateCertificate($request, $program_id, $location, $transaction->user);
                if (!$certificate) {
                    continue;
                    \Log::info('Certificate Generation Error');
                }

                // Save the certificate to the database
                Certificate::updateOrCreate(['user_id' =>  $transaction->user_id, 'program_id' => $program_id], [
                    'user_id' =>  $transaction->user_id,
                    'file' => $certificate['name'],
                    'certificate_number' => $certificate['certificate_number'],
                    'program_id' => $program_id,
                ]);

                // Leave the show_certificate as 0 as per your request
                $transaction->show_certificate = $show_certificate;
                $transaction->save();

                // Update tracker with the current transaction ID
                $tracker->update([
                    'start' => $end,
                    'end' => $transaction->id
                ]);
            }

            if ($internal) {
                return [
                    'status' => 'success',
                    'message' => 'Certificates successfully autogenerated',
                ];
            }

            return back()->with('success', 'Certificates successfully autogenerated');
        }

        return back()->with('error', 'You do not have permission to perform this action.');
    }

    public function newCertificateGeneration(Certificate $certificate_id, $status){
        $program = Program::select('id', 'auto_certificate_settings')->find($certificate_id->program_id);
        if(empty($program->auto_certificate_settings)){
            return back()->with('error', 'No Auto Certificate settings for this program!');
        }
        
        $certificate_id->update([
            'allow_new_certificate_request' => $status
        ]);

        return back()->with('message', 'Enabled succesfully');
    }

    public function createCertificateHistory($certificate){
        CertificateGenerationHistory::create([
            'certificate_id'=> $certificate->id,
            'file' => $certificate->file
        ]);
    }

    public function generateCertificatePreview(Request $request, $program_id)
    {
        try {
            $location = 'certificate_previews';
            $certificate = generateCertificate($request->all(), $program_id, $location);

            return response()->json([
                'preview_image_path' => '/certificate_previews/' . $certificate['name'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function clearAllPreviews(){

    }

    public function clearDuplicates($program_id){
        $duplicateIds = DB::table('certificates as t1')
        ->select('t1.id')
        ->join('certificates as t2', function ($join) {
            $join->on('t1.program_id', '=', 't2.program_id')
                ->on('t1.user_id', '=', 't2.user_id')
                ->whereRaw('t1.id > t2.id'); 
        })
        ->pluck('t1.id');

        DB::table('certificates')
        ->whereIn('id', $duplicateIds)
        ->delete();

        return back()->with('message', count($duplicateIds).' Duplicates removed successfully');
    }


    public function verifyCertificate(Request $request, CertificateService $certificate){
        if(!empty($request->certificate_number)){
            $response = $certificate->verify($request->certificate_number);
        }else{
            $response = null;
        }
        
        return view('verify-certificate', compact('response'));
    }

    public function certificateVerificationLogs(Request $request)
    {
        $logs = CertificateStatusLog::latest();
        $i = 1;

        if (!empty($request->certificate_number)) {
            $logs = $logs->where('certificate_number', $request->certificate_number);
        }else{
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
    
            $logs = $logs->where('created_at', '>=', $startOfWeek);
        }

        $logs = $logs->paginate(50);

        return view('dashboard.admin.certificates.certificate-verification-logs', compact('logs','i'));
    }

    public function truncateVerificationLogs(){
        CertificateStatusLog::truncate();

        return back()->with('message', 'All records Truncated!');
    }

    public function generateNewCertificate(Request $request, Certificate $certificate){
        // if($certificate->allow_new_certificate_request != 0){
        //     return back()->with('error', 'It seems this certificate has already been regenerated. Please use the download button below to obtain a copy.');
        // }
        $location = base_path('uploads/certificates');
        $newCertificate = generateCertificate($request, $certificate->program_id, $location, null, $certificate);
        
        $this->createCertificateHistory($certificate);
        $certificate->update([
            'allow_new_certificate_request' => 0,
            'file' => $newCertificate['name'],
        ]);
        
        $realpath = base_path() . '/uploads' . '/certificates/' . $newCertificate['name'];
        
        if (!file_exists($realpath)) {
            return back()->with('error', 'There was an error generating a new certificate.');
        }

        return response()->download($realpath);
    }
}
