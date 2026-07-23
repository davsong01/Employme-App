<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Module;
use App\Models\Result;
use App\Models\Program;
use App\Models\Currency;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\TempTransaction;
use DavidOghi\CertificateGeneration\Services\CertificateManager as PackageCertificateManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;


if (!function_exists("certificationStatus")) {
    function certificationStatus($program_id, $user_id)
    {
        $result = Result::with('program', 'module', 'user')->where('user_id', $user_id)->whereProgramId($program_id)->get();
        $program = Program::with('scoresettings')->find($program_id);
        $details = [];
        
        try {
            if($result->count() > 0){
                //code...
                $class = $email = $roleplay = $crm = $certification = 0;
    
                // Get the modules and calculate the total obtainable score
                $modules = Module::with('questions')
                    ->where('type', 'Class Test')
                    ->where('program_id', $program_id)
                    ->where('computation_status', 1)
                    ->get();
    
                if (empty($program->scoresettings) || $modules->count() < 1) {
                    return [
                        'program' => $program,
                        'status' => 'CERTIFIED'
                    ];
                }
    
                $obtainable = $modules->sum(fn($module) => $module->questions->count());
                
                foreach ($result as $t) {
                    if(!empty($t->marked_by)){
                        $details['certification_facilitator'] = $t->marked_by;
                    }

                    if (!empty($t->grader)) {
                        $details['certification_grader'] = $t->grader;
                    }

                    if (!empty($t->facilitator_comment)) {
                        $details['certification_facilitator_comment'] = $t->facilitator_comment;
                    }

                    if (!empty($t->grader_comment)) {
                        $details['certification_grader_comment'] = $t->grader_comment;
                    }
                    
                    // Accumulate test scores
                    $class += $t['class_test_score'];
                    $email += $t['email_test_score'];
                    $roleplay += $t['role_play_score'];
                    $crm += $t['crm_test_score'];
                    $certification += $t['certification_test_score'];
    
                    // Add program and score settings to each result
                    $t['program'] = $t->program->p_name;
                    $t['passmark'] = $program->scoresettings->passmark;
                    $t['ct_set_score'] = $program->scoresettings->class_test;
                    $t['name'] = $t->user->name;
                }
    
                // Calculate and round the class test score
                if (isset($t['ct_set_score'])) {
                    $details['class_test_score'] = round(($class * $t['ct_set_score']) / $obtainable, 0);
                }else{
                    $details['class_test_score'] = 0;
                }
                
                // Add other test scores to the details array
                $details['email_test_score'] = $email;
                $details['role_play_score'] = $roleplay;
                $details['crm_test_score'] = $crm;
                $details['certification_test_score'] = $certification;
            
                // Calculate the total score and add program details
                $details['total_score'] = $details['class_test_score'] + $email + $roleplay + $certification;
                $details['passmark'] = $t['passmark'] ?? 0;
                $details['program'] = $t['program'] ?? 0;
                $details['name'] = $t['name'];
                $details['staffID'] = $t->user->staffID;
    
                // Determine certification status
                $details['status'] = ($details['total_score'] >= $details['passmark']) ? 'CERTIFIED' : 'NOT CERTIFIED';
                
                $details['results'] = $result;
                $details['program'] = $program;

                // Extras for comparison with the new
                return $details;
            }else{
                return [
                    'program' => $program,
                ];
            }
        } catch (\Throwable $th) {
            dd($th->getMessage(),$th->getLine());
        }
    }
}

// if (!function_exists("certificationStatusNew")) {
//     function certificationStatusNew($training_result, $program, $user){
        
//         if($program instanceof Program){
//             $program = $program;
//         }else{
//             $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult')->with('scoresettings')->where('id', $program)->first();
//         }

//         if ($user instanceof User) {
//             $user = $user;
//         } else {
//             $user = User::where('id', $user)->first();
//         }
        
//         $training_result->certification_status = isset($training_result->total_score) && ($training_result->total_score >= $program->scoresettings->passmark) ? 'CERTIFIED' : 'NOT CERTIFIED';
//         $training_result->program = $program;
//         $training_result->scoresettings = $program->scoresettings;
//         $training_result->user = $user ?? null;

//         return $training_result;
//     }
// }
if (!function_exists("certificationStatusNew")) {
    function certificationStatusNew($training_result, $program, $user) {
        
        // Ensure $program is an object
        if (!($program instanceof Program)) {
            $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult')
                ->with('scoresettings')
                ->where('id', $program)
                ->first();
        }

        // Ensure $user is an object
        if (!($user instanceof User)) {
            $user = User::where('id', $user)->first();
        }

        // 1. Safe Check for $training_result (ensure it's an object)
        if (!is_object($training_result)) {
            return $training_result; 
        }

        // 2. Safe Check for passmark logic
        // Use ?-> to check if $program and scoresettings exist. Fallback to 0 passmark if missing.
        $passmark = $program?->scoresettings?->passmark ?? 0;
        
        $training_result->certification_status = (isset($training_result->total_score) && $training_result->total_score >= $passmark) 
            ? 'CERTIFIED' 
            : 'NOT CERTIFIED';

        // 3. Safe Assignments
        $training_result->program = $program;
        $training_result->scoresettings = $program?->scoresettings; // Safe if $program is null
        $training_result->user = $user;

        return $training_result;
    }
}

if (!function_exists("udateTrainingResult")) {
    function udateTrainingResult($program_id, $user_id, $data=[])
    {
        $transaction = Transaction::select('id', 'training_result', 'training_result_histories', 'user_id', 'program_id')
        ->where('program_id', $program_id)
        ->where('user_id', $user_id)
        ->lockForUpdate()
        ->first();
        
        $certificationStatus = certificationStatus($program_id, $user_id);
        
        $result = [
            "class_test_score" => $certificationStatus['class_test_score'] ?? ($transaction->training_result->class_test_score ?? 0),
            "class_test_resit_status" => $data['class_test_resit_status'] ?? ($transaction->training_result->class_test_resit_status ?? 0),
            "class_test_resit_expiry" => $data['class_test_resit_expiry'] ?? ($transaction->training_result->class_test_resit_expiry ?? NULL),
            "class_test_resit_enabled_by_id" => $data['class_test_resit_enabled_by_id'] ?? ($transaction->training_result->class_test_resit_enabled_by_id ?? NULL),

            "email_test_score" => $certificationStatus['email_test_score'] ?? ($transaction->training_result->email_test_score ?? 0),
            "email_test_resit_status" => $data['email_test_resit_status'] ?? ($transaction->training_result->email_test_resit_status ?? 0),
            "email_test_resit_expiry" => $data['email_test_resit_expiry'] ?? ($transaction->training_result->email_test_resit_expiry ?? NULL),
            "email_test_resit_enabled_by_id" => $data['email_test_resit_enabled_by_id'] ?? ($transaction->training_result->email_test_resit_enabled_by_id ?? NULL),

            "roleplay_test_score" => $certificationStatus['roleplay_test_score'] ?? ($transaction->training_result->roleplay_test_score ?? 0),
            "roleplay_test_resit_status" => $data['roleplay_test_resit_status'] ?? ($transaction->training_result->roleplay_test_resit_status ?? 0),
            "roleplay_test_resit_expiry" => $data['roleplay_test_resit_expiry'] ?? ($transaction->training_result->roleplay_test_resit_expiry ?? NULL),
            "roleplay_test_resit_enabled_by_id" => $data['roleplay_test_resit_enabled_by_id'] ?? ($transaction->training_result->roleplay_test_resit_enabled_by_id ?? NULL),

            "crm_test_score" => $certificationStatus['crm_test_score'] ?? ($transaction->training_result->crm_test_score ?? 0),
            "crm_test_resit_status" => $data['crm_test_resit_status'] ?? ($transaction->training_result->crm_test_resit_status ?? 0),
            "crm_test_resit_expiry" => $data['crm_test_resit_expiry'] ?? ($transaction->training_result->crm_test_resit_expiry ?? NULL),
            "crm_test_resit_enabled_by_id" => $data['crm_test_resit_enabled_by_id'] ?? ($transaction->training_result->crm_test_resit_enabled_by_id ?? NULL),

            "certification_test_score" => $certificationStatus['certification_test_score'] ?? ($transaction->training_result->certification_test_score ?? 0),
            "certification_test_resit_status" => $data['certification_test_resit_status'] ?? ($transaction->training_result->certification_test_resit_status ?? 0),
            "certification_test_resit_expiry" => $data['certification_test_resit_expiry'] ?? ($transaction->training_result->certification_test_resit_expiry ?? NULL),
            "certification_test_resit_enabled_by_id" => $data['certification_test_resit_enabled_by_id'] ?? ($transaction->training_result->certification_test_resit_enabled_by_id ?? NULL),
            
            "total_score" => $certificationStatus['total_score'] ?? 0,

            "certification_facilitator" => $certificationStatus['certification_facilitator'] ?? null,
            "certification_grader" => $certificationStatus['certification_grader'] ?? null,
            "certification_facilitator_comment" => $certificationStatus['certification_facilitator_comment'] ?? null,
            "certification_grader_comment" => $certificationStatus['certification_grader_comment'] ?? null,
        ];
        
        // dd($transaction->training_result->email_test_score, $transaction->training_result->roleplay_test_score, $transaction->training_result->crm_test_score, $transaction->training_result->certification_test_score);
        if(!empty($data)){
            $result["email_test_score"] = (int) ($data['email_test_score'] ?? ($transaction->training_result->email_test_score ?? 0));
            $result["roleplay_test_score"] = (int) ($data['roleplay_test_score'] ?? ($transaction->training_result->roleplay_test_score ?? 0));
            $result["crm_test_score"] = (int) ($data['crm_test_score'] ?? ($transaction->training_result->crm_test_score ?? 0));
            $result["certification_test_score"] = (int) ($data['certification_test_score'] ?? ($transaction->training_result->certification_test_score ?? 0));
            $result["total_score"] = (int) ($result["class_test_score"] + $result["email_test_score"] + $result["roleplay_test_score"] + $result["crm_test_score"]+ $result["certification_test_score"]);

            $result["certification_facilitator"] = ($data['certification_facilitator'] ?? ($transaction->training_result->certification_facilitator ?? null));
            $result["certification_grader"] = ($data['certification_grader'] ?? ($transaction->training_result->certification_grader ?? null));
            $result["certification_facilitator_comment"] = ($data['certification_facilitator_comment'] ?? ($transaction->training_result->certification_facilitator_comment ?? null));
            $result["certification_grader_comment"] = ($data['certification_grader_comment'] ?? ($transaction->training_result->certification_grader_comment ?? null));
            $result["last_updated_at"] = ($data['last_updated_at'] ?? ($transaction->training_result->last_updated_at ?? 'N/A'));            
        }
        
        // dd($result);

        // \Log::info([$result["total_score"],$result["class_test_score"], $result["email_test_score"], $result["roleplay_test_score"], $result["crm_test_score"] , $result["certification_test_score"]]);
        $transaction->update([
            'training_result' => $result,
        ]);
    
        return $transaction;
    }
}

// if (!function_exists("buildResultExport")) {
//     function buildResultExport($users, $data, $score_settings){
//     $filteredUsers = $users->map(function ($user) use ($data, $score_settings) {
//         $userArray = $user->toArray();
//         $filteredUser = array_intersect_key($userArray, array_flip($data));

//         $lastMock = $user->mocks?->last();

//         $filteredUser['Final Submission'] = $lastMock && $lastMock->created_at
//             ? $lastMock->created_at->format('d/m/Y')
//             : 'NOT SUBMITTED';

//         if (
//             isset($user->training_result?->total_cert_score) &&
//             $score_settings->certification > 0
//         ) {
//             $filteredUser['Certification Score'] = $user->training_result->total_cert_score;
//         }


//         if (isset($user->training_result->final_ct_score) && $score_settings->class_test > 0) {
//             $filteredUser['Class Test Score'] = $user->training_result->final_ct_score;
//         }

//         if (isset($user->training_result->total_role_play_score) && $score_settings->role_play > 0) {
//             $filteredUser['Role Play Score'] = $user->training_result->total_role_play_score; 
//         }

//         if (isset($user->training_result->total_email_test_score) && $score_settings->email > 0) {
//             $filteredUser['Email Test Score'] = $user->training_result->total_email_test_score;
//         }

//         if (isset($user->training_result->total_crm_test_score) && $score_settings->crm_test > 0) {
//             $filteredUser['CRM Test Score'] = $user->training_result->total_email_test_score;
//         }

//         $filteredUser['Passmark'] = $user->passmark;
//         $filteredUser['Total Score'] = $filteredUser['Total Score'] = (
//             ($user->final_ct_score ?? 0) +
//             ($user->total_role_play_score ?? 0) +
//             ($user->total_email_test_score ?? 0)
//         );

//         // if($user->staffID == '7470'){
//         //     dd($filteredUser, $user->final_ct_score);
//         // }

//         // Process metadata keys
//         if (isset($filteredUser['metadata']) && !empty($user->metadata)) {
//             foreach ($user->metadata as $key => $value) {
//                 if (!empty($value)) {
//                     $modifiedKey = ucwords(str_replace(['-', '_'], ' ', $key)); 
//                     $filteredUser[$modifiedKey] = $value;
//                 }
//             }
//             unset($filteredUser['metadata']);
//         }

//         // Format all keys in the filtered user
//         return collect($filteredUser)->mapWithKeys(function ($value, $key) {
//             $formattedKey = ucwords(str_replace(['-', '_'], ' ', $key)); 
//             return [$formattedKey => $value];
//         })->toArray();
//     })->toArray();
//         return  $filteredUsers;
//     }
// }

if (!function_exists("buildResultExport")) {
    function buildResultExport($users, $data, $score_settings)
    {
        $processedUsers = $users->map(function ($user) use ($data, $score_settings) {
            $userArray = $user->toArray();
            $filteredUser = array_intersect_key($userArray, array_flip($data ?? []));

            $lastMock = $user->mocks?->last();
            $filteredUser['Final Submission'] = $lastMock?->created_at
                ? $lastMock->created_at->format('d/m/Y')
                : 'NOT SUBMITTED';

            $result = $user->training_result;

            // Always define all test columns with default values
            $filteredUser['Certification Score'] = $score_settings->certification > 0
                ? ($result->certification_test_score ?? 0)
                : 0;

            $filteredUser['Class Test Score'] = $score_settings->class_test > 0
                ? ($result->class_test_score ?? 0)
                : 0;

            $filteredUser['Role Play Score'] = $score_settings->role_play > 0
                ? ($result->roleplay_test_score ?? 0)
                : 0;

            $filteredUser['Email Test Score'] = $score_settings->email > 0
                ? ($result->email_test_score ?? 0)
                : 0;

            $filteredUser['CRM Test Score'] = $score_settings->crm_test > 0
                ? ($result->crm_test_score ?? 0)
                : 0;

            $filteredUser['Passmark'] = $score_settings->passmark ?? '';

            // Total Score calculation
            $filteredUser['Total Score'] = (
                ($filteredUser['Certification Score'] ?? 0) +
                ($filteredUser['Class Test Score'] ?? 0) +
                ($filteredUser['Role Play Score'] ?? 0) +
                ($filteredUser['Email Test Score'] ?? 0)
            );

            // User meta fields (custom keys from $data excluding 'metadata')
            if (!empty($data)) {
                $cleanedData = array_values(array_diff($data, ['metadata']));

                foreach ($cleanedData as $value) {
                    if (!empty($value)) {
                        $modifiedKey = ucwords(str_replace(['-', '_'], ' ', $value));
                        $realValue = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value))));
                        $filteredUser[$modifiedKey] = $user->user->$realValue ?? null;
                    }
                }
            }

            // Final format: normalize keys
            return collect($filteredUser)->mapWithKeys(function ($value, $key) {
                $formattedKey = ucwords(str_replace(['-', '_'], ' ', $key));
                return [$formattedKey => $value];
            });
        });

        return $processedUsers->sortByDesc('Total Score')->values()->toArray();
    }
}



// if (!function_exists("generateCertificate")) {
//     function generateCertificate($request, $program_id=null, $location = null, $user = null, $certificate = null, $template=null)
//     {
//         if(!empty($program_id)) {
//             $program = Program::find($program_id);
//             $certificate_settings = $template->auto_certificate_settings ?? $program->auto_certificate_settings;
//         }else{
//             $certificate_settings = $request;
//         }
        
//         if (empty($user)) {
//             $user = Transaction::with('user')->whereHas('user')->inRandomOrder()->first();
//             $user = $user->user;
//         }

//         if (!empty($request['auto_certificate_template'])) {
//             $inputImagePath = $request['auto_certificate_template'];
//         } else {
//             $inputImagePath = base_path('uploads/' . $certificate_settings['auto_certificate_template']);
//         }

//         // Create a history for the previous certificate
//         $image = Image::make($inputImagePath);

//         if (!empty($request['auto_certificate_name_font_weight'])) {
//             $counter = count($request['auto_certificate_name_font_weight']);
//         } else {
//             $counter = count($certificate_settings['settings']);
//         }

//         if ($image->width() > 4000 || $image->height() > 4000) {
//             $image->resize(4000, null, function ($constraint) {
//                 $constraint->aspectRatio();
//                 $constraint->upsize();
//             });
//         }

//         $dateIssued = !empty($request['date_issued'])
//             ? Carbon::parse($request['date_issued'])->format('jS \d\a\y \o\f F, Y')
//             : now()->format('jS \d\a\y \o\f F, Y');

//         for ($i = 0; $i < $counter; $i++) {
//             $size = !empty($request['auto_certificate_name_font_size'][$i]) ? $request['auto_certificate_name_font_size'][$i] : $certificate_settings['settings'][$i]['auto_certificate_name_font_size'];
//             $color = !empty($request['auto_certificate_color'][$i]) ? $request['auto_certificate_color'][$i] : $certificate_settings['settings'][$i]['auto_certificate_color'];
//             $auto_certificate_top_offset = !empty($request['auto_certificate_top_offset'][$i]) ? $request['auto_certificate_top_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_top_offset'];
//             $auto_certificate_left_offset = !empty($request['auto_certificate_left_offset'][$i]) ? $request['auto_certificate_left_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_left_offset'];
//             $auto_certificate_font_weight = !empty($request['auto_certificate_name_font_weight'][$i]) ? $request['auto_certificate_name_font_weight'][$i] : ($certificate_settings['settings'][$i]['auto_certificate_name_font_weight'] ?? 10);
//             $text_type_face = !empty($request['text_type_face'][$i]) ? $request['text_type_face'][$i] : ($certificate_settings['settings'][$i]['text_type_face'] ?? 'Pesaro-Bold.ttf');

//             $text = 'Aboki Ogbeni Chuckwuma';
//             $text_type = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $certificate_settings['settings'][$i]['text_type'];

//             // Get text
//             if ($text_type == 'name') {
//                 $text = $user->name ?? $text;
//                 // Ensure each word starts with a capital letter
//                 $text = ucwords(strtolower($text));
//             };
            
//             if ($text_type == 'email') $text = $user->email;
//             if ($text_type == 'staffID') $text = $user->staffID ?? 'NO STAFF ID SET';

//             if ($text_type == 'certificate_number') {
//                 if (!empty($program_id)) {
//                     $certificate_number = !empty($certificate) ? $certificate->certificate_number : generateCertificateNumber($program, $user);
//                 }else{
//                     $certificate_number = rand(11111111,99999999);
//                 }

//                 $text = $certificate_number;
//             }

//             // \Log::info($certificate_settings['settings'], $certificate_settings['settings'][$i], $i);
//             if ($text_type == 'date_issued') {
//                 $text = $dateIssued;
//                 // $text = request()->route()->getName() == 'certificates.preview' ? Carbon::now()->format('jS \d\a\y \o\f F, Y') : $date_issued;
//             }
            
//             // End text
//             $image->text($text, $auto_certificate_left_offset, $auto_certificate_top_offset, function ($font) use ($size, $color, $auto_certificate_font_weight, $text_type_face) {
//                 $font->file(public_path('certificate_fonts/' . $text_type_face));
//                 $font->size($size);
//                 $font->color($color);
//                 // $font->weight($auto_certificate_font_weight);
//             });
//         }
        
//         $name = uniqid(9) . '.jpg';
//         // $outputImagePath = base_path('uploads/certificates/' . $name);
//         $outputImagePath = $location . '/' . $name;
//         $image->save($outputImagePath);

//         return [
//             'name' => $name,
//             'certificate_number' => $certificate_number ?? rand(111,999),
//             'outputImagePath' => $outputImagePath,
//             'date_issued' => $dateIssued
//         ];
//     }
// }
    
if (!function_exists("generateCertificate")) {
    function generateCertificate($request, $program_id=null, $location = null, $user = null, $certificate = null, $template=null)
    {
        $program = !empty($program_id)
            ? Program::with('certificateTemplate')->find($program_id)
            : null;

        if ($program?->certificateTemplate) {
            return generatePackageCertificate($request, $program, $location, $user, $certificate, $template);
        }

        return generateLegacyCertificate($request, $program_id, $location, $user, $certificate, $template);
    }
}

if (!function_exists("certificateRequestInput")) {
    function certificateRequestInput($request, string $key, mixed $default = null): mixed
    {
        if ($request instanceof \Illuminate\Http\Request) {
            return $request->input($key, $default);
        }

        if (is_array($request)) {
            return data_get($request, $key, $default);
        }

        if (is_object($request) && method_exists($request, 'input')) {
            return $request->input($key, $default);
        }

        return data_get($request, $key, $default);
    }
}

if (!function_exists("programAutoCertificateEnabled")) {
    function programAutoCertificateEnabled(?Program $program, ?array $settings = null): bool
    {
        $settings ??= is_array($program?->auto_certificate_settings) ? $program->auto_certificate_settings : [];
        $status = data_get($settings, 'auto_certificate_status');

        if ($status === null || $status === '') {
            return ! empty($program?->certificate_template_id)
                || ! empty(data_get($settings, 'settings'))
                || ! empty(data_get($settings, 'auto_certificate_template'));
        }

        return in_array($status, ['yes', 'published', 1, '1', true], true);
    }
}

if (!function_exists("certificateIssuedDate")) {
    function certificateIssuedDate($request, ?Program $program = null, ?array $settings = null): string
    {
        $settings ??= is_array($program?->auto_certificate_settings) ? $program->auto_certificate_settings : [];
        $rawDate = certificateRequestInput($request, 'date_issued');

        if (blank($rawDate)) {
            $rawDate = certificateRequestInput($request, 'preferred_date_of_issue');
        }

        if (blank($rawDate)) {
            $rawDate = data_get($settings, 'date_of_issue');
        }

        return ! blank($rawDate)
            ? \Carbon\Carbon::parse($rawDate)->format('jS \d\a\y \o\f F, Y')
            : now()->format('jS \d\a\y \o\f F, Y');
    }
}

if (!function_exists("generatePackageCertificate")) {
    function generatePackageCertificate($request, $program = null, $location = null, $user = null, $certificate = null, $template = null)
    {
        $packageTemplate = $program?->certificateTemplate;
        $settings = Settings::first();

        if (empty($user)) {
            $user = Transaction::with('user')->whereHas('user')->inRandomOrder()->first();
            $user = $user ? $user->user : (object)['name' => 'John Doe', 'email' => 'test@test.com'];
        }

        if (! $packageTemplate) {
            throw new \Exception('Certificate designer template not found for this program.');
        }

        if (! programAutoCertificateEnabled($program)) {
            throw new \Exception('Auto certificate generation is disabled for this program.');
        }

        $certificate_number = !empty($program)
            ? (!empty($certificate) ? $certificate->certificate_number : generateCertificateNumber($program, $user))
            : ("CERT-" . rand(111111, 999999));

        $dateIssued = certificateIssuedDate($request, $program);

        $payload = [
            'name' => $user->name ?? 'John Doe',
            'email' => $user->email ?? 'test@test.com',
            'staffID' => $user->staffID ?? 'NO STAFF ID SET',
            'certificate_number' => $certificate_number,
            'date_issued' => $dateIssued,
            'organisation_name' => $settings?->organisation_name ?? config('app.name'),
            'organization_name' => $settings?->organisation_name ?? config('app.name'),
            'organisation_address' => $settings?->ADDRESS_ON_RECEIPT ?? null,
            'organization_address' => $settings?->ADDRESS_ON_RECEIPT ?? null,
            'organisation_phone' => $settings?->phone ?? null,
            'organization_phone' => $settings?->phone ?? null,
            'organisation_email' => $settings?->OFFICIAL_EMAIL ?? null,
            'organization_email' => $settings?->OFFICIAL_EMAIL ?? null,
        ];

        $tempDir = storage_path('app/certificate-renders/' . uniqid('render_', true));
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $rendered = app(PackageCertificateManager::class)->render(
            template: $packageTemplate,
            data: $payload,
            outputDirectory: $tempDir,
            certificateNumber: $certificate_number,
        );

        if (empty($location)) {
            $location = base_path('uploads/certificates');
        }

        if (!is_dir($location)) {
            mkdir($location, 0777, true);
        }

        $finalPath = rtrim($location, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $rendered['name'];
        copy($rendered['output_path'], $finalPath);

        return [
            'name' => $rendered['name'],
            'certificate_number' => $certificate_number,
            'outputImagePath' => $finalPath,
            'date_issued' => $dateIssued,
        ];
    }
}

if (!function_exists("generateLegacyCertificate")) {
    function generateLegacyCertificate($request, $program_id=null, $location = null, $user = null, $certificate = null, $template=null)
    {
        // 1. Setup Program & Settings
        if (!empty($program_id)) {
            $program = Program::find($program_id);
            $certificate_settings = $template->auto_certificate_settings ?? $program->auto_certificate_settings;
        } else {
            $certificate_settings = $request;
            $program = null;
        }
        
        // 2. Initialize User (Ensures an 'id' exists for the number generator)
        if (empty($user)) {
            $user = (object)[
                'id' => 0, 
                'name' => 'John Doe', 
                'email' => 'test@test.com',
                'staffID' => 'STF-000'
            ];
        }

        // 3. ALWAYS generate the Number first (Guarantees QR and Text match)
        if (!empty($program)) {
            $certificate_number = !empty($certificate) ? $certificate->certificate_number : generateCertificateNumber($program, $user);
        } else {
            $certificate_number = "CERT-" . rand(111111, 999999);
        }

        if (! programAutoCertificateEnabled($program, is_array($certificate_settings) ? $certificate_settings : [])) {
            throw new \Exception('Auto certificate generation is disabled for this program.');
        }

        // 4. Image Path Logic
        if (!empty($request['auto_certificate_template']) && !is_string($request['auto_certificate_template'])) {
            $inputImagePath = $request['auto_certificate_template'];
        } elseif (!empty($request['template_path_override'])) {
            $inputImagePath = base_path('uploads/' . $request['template_path_override']);
        } else {
            $path = $certificate_settings['auto_certificate_template'] ?? null;
            $inputImagePath = base_path('uploads/' . $path);
        }

        if (!file_exists($inputImagePath)) {
            throw new \Exception("Template not found: " . $inputImagePath);
        }

        // 5. Initialize Image (GD Driver)
        $image = \Image::make($inputImagePath);
        
        // Resize for performance if necessary
        if ($image->width() > 4000) {
            $image->resize(4000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $dateIssued = certificateIssuedDate($request, $program, is_array($certificate_settings) ? $certificate_settings : []);

        // Determine loop count
        $counter = !empty($request['auto_certificate_name_font_size']) 
                   ? count($request['auto_certificate_name_font_size']) 
                   : (isset($certificate_settings['settings']) ? count($certificate_settings['settings']) : 0);

        // 6. Processing Loop
        for ($i = 0; $i < $counter; $i++) {
            $size = !empty($request['auto_certificate_name_font_size'][$i]) ? $request['auto_certificate_name_font_size'][$i] : $certificate_settings['settings'][$i]['auto_certificate_name_font_size'];
            $color = !empty($request['auto_certificate_color'][$i]) ? $request['auto_certificate_color'][$i] : $certificate_settings['settings'][$i]['auto_certificate_color'];
            $top = (int)(!empty($request['auto_certificate_top_offset'][$i]) ? $request['auto_certificate_top_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_top_offset']);
            $left = (int)(!empty($request['auto_certificate_left_offset'][$i]) ? $request['auto_certificate_left_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_left_offset']);
            $font_file = !empty($request['text_type_face'][$i]) ? $request['text_type_face'][$i] : ($certificate_settings['settings'][$i]['text_type_face'] ?? 'Pesaro-Bold.ttf');
            $text_type = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $certificate_settings['settings'][$i]['text_type'];

            // CASE A: QR CODE
            if ($text_type == 'qr_code') {
                $verifyUrl = url('/verify/certificate/' . $certificate_number);

                // SimpleSoftwareIO\QrCode handles PNG generation for GD
                $qrCodeData = \QrCode::format('png')
                    ->size($size)
                    ->margin(0)
                    ->backgroundColor(255, 255, 255, 0) // Transparent for GD
                    ->generate($verifyUrl);

                $qrImage = \Image::make($qrCodeData);
                $image->insert($qrImage, 'top-left', $left, $top);
                continue;
            }

            // CASE B: TEXT TYPES
            $text = '';
            switch ($text_type) {
                case 'name': $text = ucwords(strtolower($user->name)); break;
                case 'email': $text = $user->email; break;
                case 'staffID': $text = $user->staffID ?? 'N/A'; break;
                case 'certificate_number': $text = $certificate_number; break;
                case 'date_issued': $text = $dateIssued; break;
            }

            $image->text($text, $left, $top, function ($font) use ($size, $color, $font_file) {
                $font->file(public_path('certificate_fonts/' . $font_file));
                $font->size($size);
                $font->color($color);
                $font->align('left');
                $font->valign('top'); // Syncs coordinate logic with QR code's top-left insertion
            });
        }
        
        // 7. Save and Return
        if (empty($location)) {
            $location = base_path('uploads/certificates');
        }

        if (!is_dir($location)) {
            mkdir($location, 0777, true);
        }

        $name = uniqid() . '.jpg';
        $outputImagePath = $location . '/' . $name;
        $image->save($outputImagePath, 90); // 90% quality for GD JPG

        return [
            'name' => $name,
            'certificate_number' => $certificate_number,
            'outputImagePath' => $outputImagePath,
            'date_issued' => $dateIssued
        ];
    }
}

if (!function_exists("certificatePackageBackgroundPath")) {
    function certificatePackageBackgroundPath(array $request, array $certificate_settings): string
    {
        if (!empty($request['auto_certificate_template']) && !is_string($request['auto_certificate_template'])) {
            return $request['auto_certificate_template']->getRealPath();
        }

        if (!empty($request['template_path_override'])) {
            return base_path('uploads/' . $request['template_path_override']);
        }

        $path = $certificate_settings['auto_certificate_template'] ?? null;

        if (empty($path)) {
            throw new \Exception('Certificate template not found.');
        }

        return base_path('uploads/' . $path);
    }
}

if (!function_exists("certificatePackageSettingsFromLegacy")) {
    function certificatePackageSettingsFromLegacy(array $certificate_settings, ?string $backgroundPath = null): array
    {
        $legacySettings = $certificate_settings['settings'] ?? [];
        $elements = [];

        if (array_is_list($legacySettings)) {
            foreach ($legacySettings as $item) {
                $elements[] = certificatePackageElementFromLegacy($item);
            }
        } else {
            foreach ($legacySettings as $item) {
                if (is_array($item) && isset($item['text_type'])) {
                    $elements[] = certificatePackageElementFromLegacy($item);
                }
            }
        }

        $canvas = config('certificates.designer.canvas', ['width' => 1123, 'height' => 794, 'orientation' => 'landscape']);

        if ($backgroundPath && is_file($backgroundPath)) {
            $dimensions = @getimagesize($backgroundPath);
            if (is_array($dimensions) && ! empty($dimensions[0]) && ! empty($dimensions[1])) {
                $canvas['width'] = (int) $dimensions[0];
                $canvas['height'] = (int) $dimensions[1];
            }
        }

        return [
            'canvas' => $certificate_settings['canvas'] ?? $canvas,
            'elements' => $elements,
        ];
    }
}

if (!function_exists("certificatePackageElementFromLegacy")) {
    function certificatePackageElementFromLegacy(array $item): array
    {
        $textType = $item['text_type'] ?? 'custom_text';
        $fontFace = $item['text_type_face'] ?? 'Pesaro-Bold.ttf';
        $top = (int) ($item['auto_certificate_top_offset'] ?? $item['top'] ?? 0);
        $left = (int) ($item['auto_certificate_left_offset'] ?? $item['left'] ?? 0);
        $fontSize = (int) ($item['auto_certificate_name_font_size'] ?? $item['font_size'] ?? 36);

        return [
            'text_type' => $textType,
            'label' => $item['label'] ?? ucwords(str_replace('_', ' ', $textType)),
            'text_type_face' => $fontFace,
            'font' => $fontFace,
            'font_size' => $fontSize,
            'font_weight' => (int) ($item['auto_certificate_name_font_weight'] ?? $item['font_weight'] ?? 400),
            'color' => $item['auto_certificate_color'] ?? $item['color'] ?? '#000000',
            'auto_certificate_color' => $item['auto_certificate_color'] ?? $item['color'] ?? '#000000',
            'top' => $top,
            'left' => $left,
            'auto_certificate_top_offset' => $top,
            'auto_certificate_left_offset' => $left,
            'width' => (int) ($item['width'] ?? 0),
            'height' => (int) ($item['height'] ?? 0),
            'size' => (int) ($item['size'] ?? max($fontSize, 120)),
            'align' => $item['text_align'] ?? $item['align'] ?? 'left',
            'text_align' => $item['text_align'] ?? $item['align'] ?? 'left',
            'sample_text' => $item['sample_text'] ?? null,
            'custom_text' => $item['custom_text'] ?? $item['content'] ?? null,
            'visible' => true,
            'opacity' => 1,
            'rotation' => 0,
            'bold' => false,
            'italic' => false,
            'uppercase' => false,
            'line_height' => 1.2,
            'letter_spacing' => 0,
            'z_index' => 1,
            'locked' => false,
        ];
    }
}
    
if (!function_exists("certificateFontType")) {
    function certificateFontType()
    {
        return [
            'Times-New-Roman.ttf' => 'Times New Roman',
            'Times-New-Roman-Bold.ttf' => 'Times New Roman Bold',
            'Pesaro-Bold.ttf' => 'Pesaro-Bold',
            'Edwardian-Script-ITC.ttf' => 'Edwardian Script ITC',
        ];
    }
}

if (!function_exists("generateCertificateNumber")) {
    function generateCertificateNumber($program, $user = null) {
        // Fallback for program details if $program is null
        $programId = $program->id ?? rand(1, 99);
        $programAbbr = $program->p_abbr ?? 'CT';
        $programAbbr = str_replace('#', '', $programAbbr);

        // Generate the base random part
        $key = ($program->p_abbr ?? 'CT') . $programId;
        $randomNumber = generateRandomNumberBasedOnKey($key, 100, 999);
        
        $userId = isset($user->id) ? $user->id : rand(1000, 9999);
        
        $certificate_number = strtoupper($programAbbr) . '-' . $programId . '-' . $randomNumber . '-' . $userId;

        return $certificate_number;
    }
}

if (!function_exists("generateRandomNumberBasedOnKey")) {
    function generateRandomNumberBasedOnKey($key, $min = 1000, $max = 9999)
    {
        $hash = crc32($key);
        $randomNumber = $min + ($hash % ($max - $min + 1));
        return $randomNumber;
    }
}

if (!function_exists("generateRandomNumber")) {
    function generateRandomNumber($length){
        $charset = '';
        $chars = "123456789ABCDEFGHJKLMNPQRSTUVWXYZ";

        for ($i = 0; $i < $length; $i++) {
            $chars = str_shuffle($chars);
            $randIdx = rand(0, 32);
            $charset .= $chars[$randIdx];
        }

        return $charset;
    }
}

if (!function_exists("graderRoles")) {
    function graderRoles(){
        return [
            'Grader',
        ];
    }
}

if (!function_exists("currency")) {
    function currency()
    {
        return Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION');
    }
}


if (!function_exists("getAccounts")) {
    function getAccounts($program_id = null)
    {
        // Programs from WAACSP
        $waacsp_program_ids = [68];

        if (!empty($program_id) && in_array($program_id, $waacsp_program_ids)) {
            $accounts = [
                'Nigeria' => [
                    'bank' => 'GTB',
                    'number' => '0610151960',
                    'name' => 'West Africa Ass of Customer SP',
                    'status' => 1,
                    'country' => 'Nigeria',
                ],
                'Gambia' => [
                    'bank' => 'GTB (Gambia)',
                    'number' => '0610151960',
                    'name' => 'West Africa Ass of Customer SP',
                    'status' => 1,
                    'country' => 'Gambia',
                ],
                'Ghana' => [
                    'bank' => 'Mobile Money (MoMo)',
                    'number' => '0244627751',
                    'name' => 'Frank Asiedu',
                    'status' => 1,
                    'country' => 'Ghana',
                ],
            ];
        } else {
            $accounts = [
                'Nigeria' => [
                    'bank' => 'Access Bank',
                    'number' => '0106070151',
                    'name' => 'Employme E-learning',
                    'status' => 1,
                    'country' => 'Nigeria',
                ],
                'Ghana' => [
                    'bank' => 'Mobile Money (MoMo)',
                    'number' => '0500720400',
                    'name' => 'Frank Asiedu Trainings',
                    'status' => 1,
                    'country' => 'Ghana',
                ],
                'Gambia' => [
                    'bank' => 'GT Bank',
                    'number' => '0227187719',
                    'name' => 'Dimbars Academy',
                    'status' => 1,
                    'country' => 'Gambia',
                ],
                'Benin Rep & Togo' => [
                    'bank' => 'Momo',
                    'number' => '02290190007233',
                    'name' => 'Robert O.',
                    'status' => 1,
                    'country' => 'Benin Rep & Togo',
                ],
                'Cameroon' => [
                    'bank' => 'Momo',
                    'number' => '673524445',
                    'name' => 'Ndoiwong Comfort',
                    'status' => 1,
                    'country' => 'Cameroon',
                ],
            ];
        }

        return $accounts;
    }
}


if (!function_exists("getPackageAccess")) {
    function getPackageAccess()
    {
        $packages = Package::where('id', Session::get('company_package_id'))->get();
        dd($packages);
        return [
            'Teacher',
        ];
    }
}

if (!function_exists("allRoutes")){
    function allRoutes($type = null, $parent = null)
    {
        $menus = app('App\Http\Controllers\Controller')->adminMenus($type, $parent);
        $menus = app('App\Http\Controllers\Controller')->flattenedMenus($menus);
        
        return $menus;
    }
}

if (!function_exists("allAccess")) {
    function allAccess()
    {
        $menus = app('App\Http\Controllers\Controller')->adminTrainingPermissions('children');

        return $menus->sortBy('order')->pluck('route')->toArray();
    }
}

if (!function_exists("checkRoleHas")) {
    function checkRoleHas($roles_to_check, $user=null)
    {
        $user = $user ?? resolveAuthUser();

        if (empty($user)) {
            return false;
        }

        $user_roles = $user->role();

        return !empty(array_intersect($roles_to_check, $user_roles)) ? true : false;
    }
}

if (!function_exists("getUserByGuard")) {
    function getUserByGuard($email, $columns=null)
    {
        // Check the route prefix to determine guard type
        $isAdmin = request()->is('admin*');

        $query = $isAdmin ? Admin::query() : User::query();

        $query->where('email', $email)->orwhere('id',$email);
        
        if(!empty($columns)){
            $query->select($columns);
        }

        return $query->first();
    }
}

if (!function_exists("resolveAuthUser")) {
    function resolveAuthUser()
    {
        $currentRouteName = Route::currentRouteName();
        $routes = ['impersonate', 'topimpersonating'];
        $prefix = request()->prefix__ ?? request()->route()?->getPrefix();
        $isAdminRoute = request()->is('admin*') || str_starts_with((string) $prefix, '/admin') || $prefix === 'admin';
        
        if ($isAdminRoute || in_array($currentRouteName, $routes)) {
            $user = Auth::guard('admin')->user();
        } else {
            $user = Auth::user();
        }

        return $user;
    }
}

if (!function_exists("checkTrainingHasPermissions")) {
    function checkTrainingHasPermissions($training_id, $permissionsToCheck = null)
    {
        $result = [];
        $authUser = resolveAuthUser();
        if (empty($authUser)) {
            return $result;
        }

        $userPermissions = $authUser->trainingPermissions();
        
        $userTrainingPermissions = $userPermissions->where('program_id', $training_id)->first();
        $trainingPermissions = $userTrainingPermissions->training_permissions ?? [];
        
        // If specific permissions are provided, check them
        if (!empty($permissionsToCheck)) {
            foreach ($permissionsToCheck as $permission) {
                if (in_array($authUser->id, [1])) {
                    $result[$permission] = true;
                }else{
                    $result[$permission] = in_array($permission, $trainingPermissions);
                }
            }
        } else {
            // Otherwise, mark all permissions as true
            foreach ($trainingPermissions as $permission) {
                $result[$permission] = true;
            }
        }
        
        return $result;
    }
}

if (!function_exists("canUserAccessPermission")) {
    function canUserAccessPermission($routes, $user=null)
    {
        $user = $user ??  resolveAuthUser();
        if (empty($user)) {
            $result = [];

            foreach ($routes as $route) {
                $result[$route] = false;
            }

            return $result;
        }

        $allMenus = allRoutes('access'); 
        $userMenus = $user->permissions();
        $result = [];

        // Check for route-specific access
        foreach($routes as $route){
            if (in_array($route, $allMenus)) {
                $result[$route] = in_array($route, $userMenus) ? true : false;
            }else{
                $result[$route] = true;
            }

        }
        
        return $result;
    }
}

if (!function_exists("getAmountExtraCurrencies")) {
    function getAmountExtraCurrencies($training, $type = null, $amount = null, $earlybird='no')
    {
        $string = '';
        $array = [];
        $amountToUse = $amount ?? ($earlybird == 'yes' ? $training->e_amount : $training->p_amount);
        
        if (!empty($training->currencies) && is_array($training->currencies)) {
            $customAmounts = collect($training->currencies)->mapWithKeys(function ($c) {
                return [intval($c['id']) => $c['amount'] ?? null];
            })->all();

            $currencyIds = array_keys($customAmounts);

            $allCurrencies = Currency::where('status', 1)
                ->whereIn('id', $currencyIds)
                ->get();

            foreach ($allCurrencies as $cur) {
                $currencyId = $cur->id;
                
                if (isset($customAmounts[$currencyId]) && $customAmounts[$currencyId] !== null) {
                    $converted = $customAmounts[$currencyId] * $amountToUse;
                    $finalAmount = ($type === 'part') ? $converted / 2 : $converted;
                } else {
                    $converted = $cur->conversion_rate * $amountToUse;

                    // Now apply 'part' rule
                    $finalAmount = ($type === 'part') ? $converted / 2 : $converted;
                }

                $string .= ' <span style="color:black">|</span> <strong>'
                    . $cur->symbol . '</strong>'
                    . number_format($finalAmount, 0);

                $key = $cur->country_name ?: ($cur->code ?? $cur->id);

                $array[$key] = [
                    'symbol' => $cur->symbol,
                    'name' => $cur->name,
                    'amount' => number_format($finalAmount, 0)
                ];
            }
        }

        return [
            'string' => $string,
            'array' => $array
        ];
    }
}


if (!function_exists('getPriceRangeMultiCurrency')) {
    function getPriceRangeMultiCurrency($training, $type = null)
    {
        $subPrograms = $training->subPrograms ?? collect();

        if ($subPrograms->isEmpty()) {
            return [];
        }

        $mainFrom = $subPrograms->min('p_amount');
        $mainTo = $subPrograms->max('p_amount');

        $mainCurrency = $training->currencies[0] ?? null;

        $output = [];

        if ($mainCurrency && isset($mainCurrency->symbol)) {
            $output[] = 'From ' . $mainCurrency->symbol . number_format($mainFrom, 0)
                . ' to ' . $mainCurrency->symbol . number_format($mainTo, 0);
        } else {
            $output[] = 'From ' . number_format($mainFrom, 0) . ' to ' . number_format($mainTo, 0);
        }

        $extraRates = [];

        foreach ($subPrograms as $program) {
            $converted = getAmountExtraCurrenciesWithMainCurrency($program, $type);

            foreach ($converted['array'] as $key => $info) {
                $amountRaw = preg_replace('/[^\d]/', '', $info['amount']);
                $extraRates[$key]['from'] ??= (int)$amountRaw;
                $extraRates[$key]['to'] = max($extraRates[$key]['to'] ?? 0, (int)$amountRaw);
                $extraRates[$key]['symbol'] = $info['symbol'];
            }
        }

        foreach ($extraRates as $key => $data) {
            $output[] = 'From ' . $data['symbol'] . number_format($data['from'], 0)
                . ' to ' . $data['symbol'] . number_format($data['to'], 0);
        }
        
        return $output;
    }
}

if (!function_exists('getTransactionFromProgramIds')) {
    function getTransactionFromProgramIds(int $program_id, ?int $user_id = null)
    {
        $user_id = $user_id ?? resolveAuthUser()->id;
        $temp = TempTransaction::whereJsonContains('program_ids', $program_id)
            ->where('user_id', resolveAuthUser()->id)
            ->first() ?? collect([]);

        return $temp;
    }
}

if (!function_exists('paginationIndex')) {
    function paginationIndex($paginator, $loop)
    {
        if (!$paginator || !method_exists($paginator, 'firstItem')) {
            return $loop->iteration;
        }

        return $paginator->firstItem() + $loop->index;
    }
}

// function getPriceRangeAcrossPrograms($training, $type = null, $earlybird='no'): array
// {
//     $allPrograms = collect([$training])->merge($training->subPrograms);
//     $pluck = $earlybird == 'yes' ? 'e_amount' : 'p_amount';

//     // Main currency (₦) range
//     $nairaAmounts = $allPrograms->pluck($pluck)->map(function ($amount) use ($type) {
//         return $type === 'part' ? $amount / 2 : $amount;
//     });

//     $range = [
//         'main' => [
//             'symbol' => '₦',
//             'from' => number_format($nairaAmounts->min(), 0),
//             'to' => number_format($nairaAmounts->max(), 0),
//         ]
//     ];

//     // Track currency amounts grouped by currency ID
//     $currencyGroups = [];

//     foreach ($allPrograms as $prog) {
//         $baseAmount = $earlybird == 'yes' ? $prog->e_amount : $prog->p_amount;

//         foreach ($prog->currencies ?? [] as $currency) {
//             $currencyId = is_object($currency) ? $currency->id : $currency['id'];
//             $currencyAmount = is_object($currency) ? $currency->amount ?? null : $currency['amount'] ?? null;
//             $currencyAmount = $currencyAmount * $baseAmount;

//             // Fallback to conversion if no manual amount
//             if ($currencyAmount === null) {
//                 $model = Currency::find($currencyId);
//                 $currencyAmount = $model ? $model->conversion_rate * $baseAmount : null;
//             }

//             if ($currencyAmount !== null) {
//                 if ($type === 'part') {
//                     $currencyAmount /= 2;
//                 }
//                 $currencyGroups[$currencyId][] = $currencyAmount;
//             }
//         }
//     }

//     // Get all currencies used
//     $currencyModels = Currency::whereIn('id', array_keys($currencyGroups))->get();

//     foreach ($currencyGroups as $id => $amounts) {
//         $cur = $currencyModels->firstWhere('id', $id);
//         if ($cur) {
//             $key = $cur->country_name ?: ($cur->code ?? $cur->id);
//             $range[$key] = [
//                 'symbol' => $cur->symbol,
//                 'from' => number_format(min($amounts), 0),
//                 'to' => number_format(max($amounts), 0),
//             ];
//         }
//     }

//     return $range;
// }

// function getPriceRangeStringAcrossPrograms($training, $type = null, $earlybird = 'no'): string
// {
//     $ranges = getPriceRangeAcrossPrograms($training, $type, $earlybird);

//     $mainLine = '';
//     $extraParts = [];

//     foreach ($ranges as $key => $range) {
//         $part = "{$range['symbol']}" . $range['from'] . " – {$range['symbol']}" . $range['to'];

//         if ($key === 'main') {
//             $mainLine = $part;
//         } else {
//             $extraParts[] = $part;
//         }
//     }

//     $extraLine = implode(' | ', $extraParts);

//     return $mainLine . ($extraLine ? '<br> ' . $extraLine : '');
// }

function getPriceRangeAcrossPrograms($training, $type = null, $earlybird = 'no'): array
{
    $allPrograms = collect([$training])->merge($training->subPrograms);
    $pluck = $earlybird === 'yes' ? 'e_amount' : 'p_amount';

    // Main currency (₦) range
    $nairaAmounts = $allPrograms->pluck($pluck)->map(function ($amount) use ($type) {
        return $type === 'part' ? $amount / 2 : $amount;
    });

    $minNaira = $nairaAmounts->min();
    $maxNaira = $nairaAmounts->max();

    $range = [
        'main' => [
            'symbol' => '₦',
            'from'   => number_format($minNaira, 0),
            'to'     => number_format($maxNaira, 0),
            'single' => $minNaira == $maxNaira // flag for later
        ]
    ];

    // Track currency amounts grouped by currency ID
    $currencyGroups = [];

    foreach ($allPrograms as $prog) {
        $baseAmount = $earlybird === 'yes' ? $prog->e_amount : $prog->p_amount;

        foreach ($prog->currencies ?? [] as $currency) {
            $currencyId     = is_object($currency) ? $currency->id : $currency['id'];
            $currencyAmount = is_object($currency) ? ($currency->amount ?? null) : ($currency['amount'] ?? null);
            $currencyAmount = $currencyAmount !== null ? $currencyAmount * $baseAmount : null;

            // Fallback to conversion if no manual amount
            if ($currencyAmount === null) {
                $model = Currency::find($currencyId);
                $currencyAmount = $model ? $model->conversion_rate * $baseAmount : null;
            }

            if ($currencyAmount !== null) {
                if ($type === 'part') {
                    $currencyAmount /= 2;
                }
                $currencyGroups[$currencyId][] = $currencyAmount;
            }
        }
    }

    // Get all currencies used
    $currencyModels = Currency::whereIn('id', array_keys($currencyGroups))->get();

    foreach ($currencyGroups as $id => $amounts) {
        $cur = $currencyModels->firstWhere('id', $id);
        if ($cur) {
            $min = min($amounts);
            $max = max($amounts);
            $key = $cur->country_name ?: ($cur->code ?? $cur->id);
            $range[$key] = [
                'symbol' => $cur->symbol,
                'from'   => number_format($min, 0),
                'to'     => number_format($max, 0),
                'single' => $min == $max
            ];
        }
    }

    return $range;
}

function getPriceRangeStringAcrossPrograms($training, $type = null, $earlybird = 'no'): string
{
    $ranges = getPriceRangeAcrossPrograms($training, $type, $earlybird);

    $mainLine = '';
    $extraParts = [];

    foreach ($ranges as $key => $range) {
        if (!empty($range['single'])) {
            $part = "{$range['symbol']}{$range['from']}";
        } else {
            $part = "{$range['symbol']}{$range['from']} – {$range['symbol']}{$range['to']}";
        }

        if ($key === 'main') {
            $mainLine = $part;
        } else {
            $extraParts[] = $part;
        }
    }

    $extraLine = implode(' | ', $extraParts);

    return $mainLine . ($extraLine ? '<span style="color:black"> | </span>' . $extraLine : '');
}
