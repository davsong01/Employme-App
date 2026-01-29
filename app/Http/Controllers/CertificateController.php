<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\DB;
use App\Models\FacilitatorTraining;
use App\Http\Controllers\Controller;
use App\Models\CertificateStatusLog;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Models\CertificateGenerationHistory;
use App\Models\CertificateRegenerationRequest;
use App\Models\CertificateRegenerationTemplate;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userid = resolveAuthUser()->id;
        $i = 1;
        if (checkRoleHas(['Admin','Grader','Facilitator'])) {
            if(checkRoleHas(['Admin'])){
                $programs = Program::withCount('certificates')->where('id', '<>', 1)
                // ->whereNULL('parent_id')
                ->orderBy('created_at', 'desc')
                ->get();
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
            if ($program->show_certificate == 0) {
                return back()->with('error', 'Certificates not yet out for this training!');
            }
            
            if ($program->allow_payment_restrictions_for_certificates == 'yes') {
                if ($transaction->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $transaction->currency_symbol . number_format($transaction->balance) . ' in order to get view/download certificate');
                }
            }

            if ($program->only_certified_should_see_certificate == 'yes') {
                $details = certificationStatusNew($transaction->training_result, $program, resolveAuthUser());
                
                $certification_status = $details->certification_status ?? NULL;
                if (!$certification_status || $certification_status == 'NOT CERTIFIED') {
                    return back()->with('error', 'You must be certified before you can view certificate');
                }
            }

            $certificate = Certificate::with(['user'])->where('user_id', resolveAuthUser()->id)->whereProgramId($request->p_id)->first();
            
            if (!isset($certificate)) {
                return back()->with('error', 'Certificate for selected program is not ready at this time, please try again or consult admin');
            }

            if ($certificate->show_certificate() == 'Disabled') {
                return back()->with('error', 'Certificate Unavailable at the moment, please check back');
            }
            
            $regenerationRequests = CertificateRegenerationRequest::where('user_id', auth()->user()->id)->with('program')->latest()->get();
            $pendingRegenerationRequests = CertificateRegenerationRequest::where('user_id', auth()->user()->id)->where('status','pending')->first();

            return view('dashboard.student.certificates.index', compact('certificate', 'program', 'regenerationRequests', 'pendingRegenerationRequests'));
        }
        return back();
    }

    public function enable($id)
    {

        if (checkRoleHas(['Admin'])) {
            $program = Program::findorfail($id);
            $program->show_certificate = 1;
            $program->save();

            return back()->with('message', 'Participants of this program can now download certificates');
        }
        return back();
    }

    public function disable($id)
    {
        if (checkRoleHas(['Admin'])) {
            $program = Program::findorfail($id);
            $program->show_certificate = 0;
            $program->save();

            return back()->with('message', 'Participants of this program can no longer download certificates');
        }
        return back();
    }

    public function certificateRegenerationRequests()
    {
        $programs = Program::whereHas('regenerationTemplate')
            ->where('id', '<>', 1)
            ->get();

        $regenerationRequests = CertificateRegenerationRequest::with(['program','user','certificate'])->latest()->get();

        $pendingUserIds = CertificateRegenerationRequest::where('status', 'pending')->pluck('user_id');

        // Fetch users without a pending request (optional, only include if you need it)
        $users = User::whereNotIn('id', $pendingUserIds)->orderBy('created_at', 'DESC')->withCount('certificates')->get();
        
        return view('dashboard.admin.certificates.requests', compact('programs', 'regenerationRequests', 'users'));
    }


    public function deleteCertificateTemplate(CertificateRegenerationTemplate $template){
        if (file_exists(base_path() . '/uploads/certificates' . '/' . $template->auto_certificate_settings['auto_certificate_template'])) {
            unlink(base_path() . '/uploads/certificate_templates' . '/' .  $template->auto_certificate_settings['auto_certificate_template']);
        }
        
        $template->delete();
        return back()->with('message', 'Template deleted Successfully');
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

    public function updateGenerationRequestStatus(Request $request, $id){
        $request->validate([
            'action' => 'required|in:approve,decline',
        ]);
        
        $regenerationRequest = CertificateRegenerationRequest::findOrFail($id);
        
        if($request->prefix__ != '/admin'){
            if ($regenerationRequest->status !== 'pending') {
                return back()->with('error', 'Request already processed.');
            }
        }

        $regenerationRequest->status = $request->action === 'approve' ? 'approved' : 'declined';
        
        if($regenerationRequest->status == 'approved'){
            $existingCertificate = Certificate::where('user_id', $request->user_id)->where('program_id', $request->program_id)->first();
            
            $program = Program::where('id', $request->program_id)->first();
            $user = User::where('id', $request->user_id)->first();
            $template = $program->regenerationTemplate;
            
            if(!$template){
                return back()->with('error','Certificate Regeneration Template not found!');
            }

            $location = base_path('uploads/certificates');
            
            if(!$existingCertificate){
                $newCertificate = generateCertificate($request, $program->id, $location, $user, null, $template);

                $cert = Certificate::updateOrCreate(['user_id' =>  $request->user_id, 'program_id' => $request->program_id], [
                    'user_id' => $request->user_id,
                    'file' => $newCertificate['name'],
                    'certificate_number' => $newCertificate['certificate_number'],
                    'date_issued' => $newCertificate['date_issued'] ?? null,
                    'program_id' => $request->program_id,
                    'allow_new_certificate_request' => 0,
                ]);

                $regenerationRequest->update([
                    'certificate_id' => $cert->id
                ]);
            }else{
                $newCertificate = generateCertificate($request, $program->id, $location, $user, $existingCertificate, $template);

                $this->createCertificateHistory($existingCertificate);
                $existingCertificate->update([
                    'allow_new_certificate_request' => 0,
                    'file' => $newCertificate['name'],
                ]);
            }

            $realpath = base_path() . '/uploads' . '/certificates/' . $newCertificate['name'];

            if (!file_exists($realpath)) {
                return back()->with('error', 'There was an error generating a new certificate.');
            }

            $name = $user->name;

            $details = [
                'subject' => 'Your Certificate for ' . $program->p_name,
                'email' => $user->email,
                // 'email' => 'davsong16@gmail.com',
                'content' => "<p>Dear {$name},<br><br>Your certificate for the training <strong>{$program->p_name}</strong> has been successfully generated.<br><br>Please find your certificate attached.<br><br>Regards</p>",
                'type' => 'bulk',
                'attachments' => [$realpath],
            ];
            
            $this->sendGenericEmail($details);
        }

        $regenerationRequest->save();

        return back()->with('message', 'Request has been ' . $regenerationRequest->status . '.');
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
                'date_issued' => !empty($request['date_issued'])
                    ? Carbon::parse($request['date_issued'])->format('jS \d\a\y \o\f F, Y')
                    : now()->format('jS \d\a\y \o\f F, Y'),
            ]);

            Transaction::where(['user_id' => $request->user_id, 'program_id' => $request->p_id])->update(['show_certificate' => 0]);

            return back()->with('message', ' certificate succesfully added');
            // return redirect(route('certificates.create'))->with('message', ' certificate succesfully added'); 
        }

        return abort(404);
    }

    public function certificateRegenerationTemplates()
    {
        // Get all templates with their related programs
        $templates = CertificateRegenerationTemplate::with([
            'certificatePrograms' => function ($query) {
                $query->select('programs.id', 'p_name');
            }
        ])->get();

        $attachedProgramIds = DB::table('certificate_programs')->pluck('program_id');

        $programs = Program::withCount('users')
            ->where('id', '<>', 1)
            // ->whereNotIn('id', $attachedProgramIds)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('dashboard.admin.certificates.certificate-regeneration-templates', compact('programs', 'templates', 'attachedProgramIds'));
    }


    public function saveCertificateTemplate(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'auto_certificate_template' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'text_type' => 'array',
            'text_type.*' => 'nullable|string',
            'text_type_face.*' => 'nullable|string',
            'auto_certificate_name_font_size.*' => 'nullable|numeric',
            'auto_certificate_name_font_weight.*' => 'nullable|numeric',
            'auto_certificate_top_offset.*' => 'nullable|numeric',
            'auto_certificate_left_offset.*' => 'nullable|numeric',
            'auto_certificate_color.*' => 'nullable|string',
        ]);
        
        // Upload certificate file
        if ($request->hasFile('auto_certificate_template')) {
            $name = uniqid(9) . '.' . $request->auto_certificate_template->getClientOriginalExtension();
            $request->auto_certificate_template->storeAs('certificate_templates', $name, 'uploads');

            $autoSettings['auto_certificate_template'] = 'certificate_templates/' . $name;
        }


        // Process settings
        $settings = [];
        if ($request->has('text_type')) {
            foreach ($request->text_type as $index => $type) {
                $settings[] = [
                    'text_type' => $type ?? '',
                    'text_type_face' => $request->text_type_face[$index] ?? '',
                    'auto_certificate_name_font_size' => $request->auto_certificate_name_font_size[$index] ?? '',
                    'auto_certificate_name_font_weight' => $request->auto_certificate_name_font_weight[$index] ?? '',
                    'auto_certificate_top_offset' => $request->auto_certificate_top_offset[$index] ?? '',
                    'auto_certificate_left_offset' => $request->auto_certificate_left_offset[$index] ?? '',
                    'auto_certificate_color' => $request->auto_certificate_color[$index] ?? '#000000',
                ];
            }
        }

        $autoSettings['settings'] = $settings;
        $autoSettings["auto_certificate_status"] = "yes";

        $template = CertificateRegenerationTemplate::create([
            'name' => $request->input('name'),
            'auto_certificate_settings' => $autoSettings,
        ]);

        app('App\Http\Controllers\Admin\ProgramController')->deleteAllFilesInAPublicFolder('certificate_previews');


        // $template->certificatePrograms()->attach($request->program_ids);
        $template->certificatePrograms()->sync($request->program_ids);
        return back()->with('message', 'Certificate template saved successfully!');
    }

    public function serveTemplate($id)
    {
        $template = CertificateRegenerationTemplate::findOrFail($id);
        $path = $template->auto_certificate_settings['auto_certificate_template'] ?? null;
        
        if (!$path || !file_exists(base_path('uploads/' . $path))) {
            abort(404);
        }

        return response()->file(base_path('uploads/' . $path));
    }

    public function updateCertificateTemplate(Request $request, CertificateRegenerationTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'auto_certificate_template' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'text_type' => 'array',
            'text_type.*' => 'nullable|string',
            'text_type_face.*' => 'nullable|string',
            'auto_certificate_name_font_size.*' => 'nullable|numeric',
            'auto_certificate_name_font_weight.*' => 'nullable|numeric',
            'auto_certificate_top_offset.*' => 'nullable|numeric',
            'auto_certificate_left_offset.*' => 'nullable|numeric',
            'auto_certificate_color.*' => 'nullable|string',
        ]);
        // dd($request->all());
        // Upload certificate file
        if ($request->hasFile('auto_certificate_template')) {
            $name = uniqid(9) . '.' . $request->auto_certificate_template->getClientOriginalExtension();
            $request->auto_certificate_template->storeAs('certificate_templates', $name, 'uploads');

            $autoSettings['auto_certificate_template'] = 'certificate_templates/' . $name;
        }


        // Process settings
        $settings = [];
        if ($request->has('text_type')) {
            foreach ($request->text_type as $index => $type) {
                $settings[] = [
                    'text_type' => $type ?? '',
                    'text_type_face' => $request->text_type_face[$index] ?? '',
                    'auto_certificate_name_font_size' => $request->auto_certificate_name_font_size[$index] ?? '',
                    'auto_certificate_name_font_weight' => $request->auto_certificate_name_font_weight[$index] ?? '',
                    'auto_certificate_top_offset' => $request->auto_certificate_top_offset[$index] ?? '',
                    'auto_certificate_left_offset' => $request->auto_certificate_left_offset[$index] ?? '',
                    'auto_certificate_color' => $request->auto_certificate_color[$index] ?? '#000000',
                ];
            }
        }

        $autoSettings['settings'] = $settings;
        $autoSettings["auto_certificate_status"] = "yes";

        $template->update([
            'name' => $request->input('name'),
            'auto_certificate_settings' => $autoSettings,
        ]);

        app('App\Http\Controllers\Admin\ProgramController')->deleteAllFilesInAPublicFolder('certificate_previews');


        $template->certificatePrograms()->sync($request->program_ids);
        return back()->with('message', 'Certificate template updated successfully!');
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
        return back()->with('message', 'certificate succesfully deleted');        
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

    // public function generateCertificates(Request $request, $program_id, $internal = false)
    // {
    //     $pick = $request->pick;
    //     $show_certificate = $request->show_certificate ?? 0;
    //     set_time_limit(0);

    //     $cron_task = $request->use_cron;

    //     if (!empty($cron_task) && $cron_task == 'yes') {
    //         // Cron
    //         $payload = $request->except(['use_cron', 'prefix__', '_token']);
    //         $payload['program_id'] = $program_id;

    //         UtilityCronTask::updateOrcreate([
    //             'name' => 'Certificate Generation Task for Training Id: ' . $program_id,
    //             'key' => 'certificate-generation',
    //             'payload' => $payload
    //         ]);

    //         return back()->with('message', 'Succesfully logged to cron task');
    //     }

    //     // Check if the user has the required roles
    //     if ($internal) {
    //         $check = true;
    //     } else {
    //         $check = checkRoleHas(['Admin','Grader','Facilitator']);
    //     }

    //     if ($check) {
    //         $tracker = UtilityTracker::firstOrCreate(
    //             ['key' => 'AGC-' . $program_id],
    //             ['key' => 'AGC-' . $program_id, 'start' => 0, 'end' => 0]
    //         );
    //         $end = $tracker->end;

    //         // Fetch the transactions based on program ID, show_certificate status, and tracker end value
    //         $transactions = Transaction::with(['user', 'certificate'])
    //         ->where('program_id', $program_id)
    //             ->where('show_certificate', 0)
    //             ->whereDoesntHave('certificate', function ($query) use ($program_id) {
    //                 $query->where('program_id', $program_id);
    //             });

    //         if (!empty($cron_task) && $cron_task == 'yes') {
    //             $transactions = $transactions->where('id', '>', $end);
    //         }

    //         $transactions = $transactions->take($pick)->get();

    //         // Check if there are any eligible transactions
    //         if ($transactions->isEmpty()) {
    //             if ($internal) {
    //                 return [
    //                     'status' => 'failed',
    //                     'message' => 'Looks like all certificates have been generated!',
    //                 ];
    //             }
    //             return back()->with('error', 'Looks like all certificates have been generated!');
    //         }

    //         // Loop through each transaction
    //         foreach ($transactions as $transaction) {
    //             // Certificate storage location
    //             $location = base_path('uploads/certificates');

    //             // Generate the certificate
    //             $certificate = generateCertificate($request, $program_id, $location, $transaction->user);
    //             if (!$certificate) {
    //                 continue;
    //                 // \Log::info('Certificate Generation Error');
    //             }

    //             // Save the certificate to the database
    //             Certificate::updateOrCreate(['user_id' =>  $transaction->user_id, 'program_id' => $program_id], [
    //                 'user_id' =>  $transaction->user_id,
    //                 'file' => $certificate['name'],
    //                 'certificate_number' => $certificate['certificate_number'],
    //                 'program_id' => $program_id,
    //                 'date_issued' => $certificate['date_issued'],
    //             ]);

    //             // Leave the show_certificate as 0 as per your request
    //             $transaction->show_certificate = $show_certificate;
    //             $transaction->save();

    //             // Update tracker with the current transaction ID
    //             $tracker->update([
    //                 'start' => $end,
    //                 'end' => $transaction->id
    //             ]);
    //         }

    //         if ($internal) {
    //             return [
    //                 'status' => 'success',
    //                 'message' => 'Certificates successfully autogenerated',
    //             ];
    //         }

    //         return back()->with('success', 'Certificates successfully autogenerated');
    //     }

    //     return back()->with('error', 'You do not have permission to perform this action.');
    // }

    public function generateCertificates(Request $request, $program_id, $internal = false)
    {
        $pick = (int) ($request->pick ?? 50);
        
        $show_certificate = $request->show_certificate ?? 0;
        set_time_limit(0);

        $cron_task = $request->use_cron ?? null;

        // If scheduled via UI to create a cron task, just log it (existing behavior)
        if (!empty($cron_task) && $cron_task === 'yes' && !$internal) {
            $payload = $request->except(['use_cron', 'prefix__', '_token']);
            $payload['program_id'] = $program_id;

            UtilityCronTask::updateOrCreate(
                ['key' => 'certificate-generation', 'name' => 'Certificate Generation Task for Training Id: ' . $program_id],
                ['payload' => $payload, 'status' => 'pending']
            );

            return back()->with('message', 'Successfully logged to cron task');
        }

        // Permission check
        if ($internal) {
            $check = true;
        } else {
            $check = checkRoleHas(['Admin', 'Grader', 'Facilitator']);
        }

        if (! $check) {
            return $internal
                ? ['status' => 'failed', 'message' => 'Permission denied']
                : back()->with('error', 'You do not have permission to perform this action.');
        }

        // Ensure tracker exists
        $tracker = UtilityTracker::firstOrCreate(
            ['key' => 'AGC-' . $program_id],
            ['start' => 0, 'end' => 0]
        );

        $previousEnd = (int) $tracker->end;

        // Build base query for eligible transactions
        $transactionsQuery = Transaction::with(['user', 'certificate'])
            ->where('program_id', $program_id)
            // ->where('show_certificate', 0)
            ->whereDoesntHave('certificate', function ($q) use ($program_id) {
                $q->where('program_id', $program_id);
            });
        
        // If running from cron, advance from tracker end
        if (!empty($cron_task) && $cron_task === 'yes') {
            $transactionsQuery->where('id', '>', $previousEnd);
        }

        // Ensure deterministic order so tracker moves forward
        $transactions = $transactionsQuery->orderBy('id', 'asc')
            ->take($pick)
            ->get();
        
        if ($transactions->isEmpty()) {
            // No more transactions to process
            if ($internal) {
                return [
                    'status' => 'completed',
                    'message' => 'Looks like all certificates have been generated!',
                ];
            }
            return back()->with('error', 'Looks like all certificates have been generated!');
        }

        $lastProcessedId = null;
        $processed = 0;

        // Use DB transaction to keep DB consistent per-batch (optional but recommended)
        DB::beginTransaction();
        try {
            foreach ($transactions as $transaction) {
                $location = base_path('uploads/certificates');

                $certificate = generateCertificate($request, $program_id, $location, $transaction->user);

                // if generation failed for this record we skip but continue others
                if (! $certificate) {
                    // optional: log failure for this user/transaction
                    // \Log::warning("Certificate generation failed for transaction id {$transaction->id}");
                    continue;
                }

                Certificate::updateOrCreate(
                    ['user_id' => $transaction->user_id, 'program_id' => $program_id],
                    [
                        'user_id' => $transaction->user_id,
                        'file' => $certificate['name'],
                        'certificate_number' => $certificate['certificate_number'],
                        'program_id' => $program_id,
                        'date_issued' => $certificate['date_issued'],
                    ]
                );

                // keep show_certificate as requested and save transaction
                $transaction->show_certificate = $show_certificate;
                $transaction->save();

                $lastProcessedId = $transaction->id;
                $processed++;
            }

            // If we processed at least one record update the tracker once
            if ($processed > 0 && $lastProcessedId !== null) {
                $tracker->update([
                    'start' => $previousEnd,
                    'end' => $lastProcessedId,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error("Certificate batch generation failed for program {$program_id}: " . $e->getMessage());
            
            if ($internal) {
                return ['status' => 'failed', 'message' => 'Internal error. Check logs.'];
            }
            return back()->with('error', 'An error occurred while generating certificates. Check logs.');
        }

        // If called from internal (cron runner), return status structure
        if ($internal) {
            return [
                'status' => 'success',
                'processed' => $processed,
                'message' => 'Certificates processed for program ' . $program_id,
            ];
        }

        return back()->with('success', 'Certificates successfully autogenerated. Processed: ' . $processed);
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

    public function certificateRegenerationTemplatePreview(Request $request)
    {
        try {
            $location = 'certificate_previews';
            $certificate = generateCertificate($request->all(), null, $location);
            // dd($certificate);
            return response()->json([
                'preview_image_path' => '/certificate_previews/' . $certificate['name'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage() . ' in ' . $th->getFile() . ' on line ' . $th->getLine(),
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

    function createRegenerationRequest(Request $request, Certificate $certificate){
        if (CertificateRegenerationRequest::where('user_id', $certificate->user_id)
            ->where('program_id', $certificate->program_id)
            ->where('status', 'pending')
            ->exists()
        ) {
            return back()->with('error', 'You have a pending request, please try again later!');
        }

        CertificateRegenerationRequest::create([
            'meta' => $request->except(['_token', 'prefix__']),
            'program_id' => $certificate->program_id,
            'certificate_id' => $certificate->id,
            'user_id' => $certificate->user_id,
            'preferred_date_of_issue' => $request->date_issued,
            'status' => 'pending',
        ]);

        return back()->with('message', 'Certificate Generation application successful, we will contact you as soon as the certificate is generated.');
    }

    function adminCreateRegenerationRequest(Request $request)
    {
        if (CertificateRegenerationRequest::where('user_id', $request->user_id)
            ->where('program_id', $request->program_id)
            ->where('status', 'pending')
            ->exists()
        ) {
            return back()->with('error', 'There is a pending request already, please try again later!');
        }

        // Ensure this user has this program
        $check = Transaction::where(['user_id' => $request->user_id, 'program_id' => $request->program_id])->first();

        if(!$check){
            return back()->with('error', 'This User did not register for this program!');
        }

        if ($check->balance > 0) {
            return back()->with('error', 'This User still has pending balance of !'. $check->balance . ' for the selected program/group');
        }
        
        $existingCertificate = Certificate::where('user_id', $request->user_id)->where('program_id', $request->program_id)->first();
        $request['action'] = 'approve';

        $certRequest = CertificateRegenerationRequest::create([
            'meta' => $request->except(['_token', 'prefix__']),
            'program_id' => $request->program_id,
            'certificate_id' => $existingCertificate?->id,
            'user_id' => $request->user_id,
            'preferred_date_of_issue' => $request->date_issued,
            'status' => 'approved',
        ]);
        
        $this->updateGenerationRequestStatus($request, $certRequest->id);
        
        return back()->with('message', 'Operation successful');
    }

    // public function generateNewCertificate(Request $request, Certificate $certificate){
    //     // if($certificate->allow_new_certificate_request != 0){
    //     //     return back()->with('error', 'It seems this certificate has already been regenerated. Please use the download button below to obtain a copy.');
    //     // }
    //     $location = base_path('uploads/certificates');
    //     $newCertificate = generateCertificate($request, $certificate->program_id, $location, null, $certificate);
    //     $this->createCertificateHistory($certificate);

    //     $certificate->update([
    //         'allow_new_certificate_request' => 0,
    //         'file' => $newCertificate['name'],
    //     ]);
        
    //     $realpath = base_path() . '/uploads' . '/certificates/' . $newCertificate['name'];
        
    //     if (!file_exists($realpath)) {
    //         return back()->with('error', 'There was an error generating a new certificate.');
    //     }

    //     return response()->download($realpath);
    // }
}
