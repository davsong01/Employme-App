<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Module;
use App\Models\Result;
use App\Models\Program;
use App\Models\Settings;
use App\Models\Transaction;
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

    if (!function_exists("certificationStatusNew")) {
        function certificationStatusNew($training_result, $program, $user){

            if($program instanceof Program){
                $program = $program;
            }else{
                $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult')->with('scoresettings')->where('id', $program)->first();
            }

            if ($user instanceof User) {
                $user = $user;
            } else {
                $user = User::where('id', $user)->first();
            }
            
            $training_result->certification_status = $training_result->total_score >= $program->scoresettings->passmark ? 'CERTIFIED' : 'NOT CERTIFIED';
            $training_result->program = $program;
            $training_result->scoresettings = $program->scoresettings;
            $training_result->user = $user ?? null;

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

    if (!function_exists("buildResultExport")) {
        function buildResultExport($users, $data, $score_settings){
        $filteredUsers = $users->map(function ($user) use ($data, $score_settings) {
            $userArray = $user->toArray();
            $filteredUser = array_intersect_key($userArray, array_flip($data));

            $filteredUser['Final Submission'] = !empty($user->mocks->last()->created_at)
            ? $user->mocks->last()->created_at->format('d/m/Y')
            : 'NOT SUBMITTED';

            if (isset($user->total_cert_score) && $score_settings->certification > 0) {
                $filteredUser['Certification Score'] = $user->total_cert_score;
            }

            if (isset($user->final_ct_score) && $score_settings->class_test > 0) {
                $filteredUser['Class Test Score'] = $user->final_ct_score;
            }

            if (isset($user->total_role_play_score) && $score_settings->role_play > 0) {
                $filteredUser['Role Play Score'] = $user->total_role_play_score; 
            }

            if (isset($user->total_email_test_score) && $score_settings->email > 0) {
                $filteredUser['Email Test Score'] = $user->total_email_test_score;
            }

            if (isset($user->total_crm_test_score) && $score_settings->crm_test > 0) {
                $filteredUser['CRM Test Score'] = $user->total_email_test_score;
            }

            $filteredUser['Passmark'] = $user->passmark;
            $filteredUser['Total Score'] = $filteredUser['Total Score'] = (
                ($user->final_ct_score ?? 0) +
                ($user->total_role_play_score ?? 0) +
                ($user->total_email_test_score ?? 0)
            );

            // if($user->staffID == '7470'){
            //     dd($filteredUser, $user->final_ct_score);
            // }

            // Process metadata keys
            if (isset($filteredUser['metadata']) && !empty($user->metadata)) {
                foreach ($user->metadata as $key => $value) {
                    if (!empty($value)) {
                        $modifiedKey = ucwords(str_replace(['-', '_'], ' ', $key)); 
                        $filteredUser[$modifiedKey] = $value;
                    }
                }
                unset($filteredUser['metadata']);
            }

            // Format all keys in the filtered user
            return collect($filteredUser)->mapWithKeys(function ($value, $key) {
                $formattedKey = ucwords(str_replace(['-', '_'], ' ', $key)); 
                return [$formattedKey => $value];
            })->toArray();
        })->toArray();
            return  $filteredUsers;
        }

        if (!function_exists("generateCertificate")) {
            function generateCertificate($request, $program_id, $location, $user=null, $certificate=null)
            {
                $program = Program::find($program_id);
                
                if(empty($user)){
                    $user = Transaction::with('user')->whereHas('user')->inRandomOrder()->first();
                    $user = $user->user;
                }
                
                $certificate_settings = $program->auto_certificate_settings;
            
                if (!empty($request['auto_certificate_template'])) {
                    $inputImagePath = $request['auto_certificate_template'];
                } else {
                    $inputImagePath = base_path('uploads/' . $certificate_settings['auto_certificate_template']);
                }
                
                // Create a history for the previous certificate

                $image = Image::make($inputImagePath);
                
                if(!empty($request['auto_certificate_name_font_weight'])){
                    $counter = count($request['auto_certificate_name_font_weight']);
                }else{
                    $counter = count($certificate_settings['settings']);
                }

                if ($image->width() > 4000 || $image->height() > 4000) {
                    $image->resize(4000, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                for ($i = 0; $i < $counter; $i++) {
                    $size = !empty($request['auto_certificate_name_font_size'][$i]) ? $request['auto_certificate_name_font_size'][$i] : $certificate_settings['settings'][$i]['auto_certificate_name_font_size'];
                    $color = !empty($request['auto_certificate_color'][$i]) ? $request['auto_certificate_color'][$i] : $certificate_settings['settings'][$i]['auto_certificate_color'];
                    $auto_certificate_top_offset = !empty($request['auto_certificate_top_offset'][$i]) ? $request['auto_certificate_top_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_top_offset'];
                    $auto_certificate_left_offset = !empty($request['auto_certificate_left_offset'][$i]) ? $request['auto_certificate_left_offset'][$i] : $certificate_settings['settings'][$i]['auto_certificate_left_offset'];
                    $auto_certificate_font_weight = !empty($request['auto_certificate_name_font_weight'][$i]) ? $request['auto_certificate_name_font_weight'][$i] : ($certificate_settings['settings'][$i]['auto_certificate_name_font_weight'] ?? 10);
                    $text_type_face = !empty($request['text_type_face'][$i]) ? $request['text_type_face'][$i] : ($certificate_settings['settings'][$i]['text_type_face'] ?? 'Pesaro-Bold.ttf');
                    
                    $text = 'Aboki Ogbeni Chuckwuma';
                    
                    $text_type = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $certificate_settings['settings'][$i]['text_type'];
                    // Get text
                    if($text_type == 'name') $text = $user->name ?? $text;
                    if($text_type == 'email') $text = $user->email;
                    if($text_type == 'staffID') $text = $user->staffID ?? 'NO STAFF ID SET';
                    
                    
                    if($text_type == 'certificate_number'){
                        $certificate_number = !empty($certificate) ? $certificate->certificate_number : generateCertificateNumber($program, $user);
                        $text = $certificate_number;
                    }
                    if ($text_type == 'date_issued'){
                        $date_issued = !empty($request['date_issued'])
                        ? Carbon::parse($request['date_issued'])->format('jS \d\a\y \o\f F, Y')
                        : '';
                        $text = request()->route()->getName() == 'certificates.preview' ? Carbon::now()->format('jS \d\a\y \o\f F, Y') : $date_issued;
                    }
                    
                    // End text
                    $image->text($text, $auto_certificate_left_offset, $auto_certificate_top_offset, function ($font) use ($size, $color, $auto_certificate_font_weight, $text_type_face) {
                        $font->file(public_path('certificate_fonts/'. $text_type_face));
                        $font->size($size);
                        $font->color($color);
                        // $font->weight($auto_certificate_font_weight);
                    });
                }

                $name = uniqid(9) . '.jpg';
                // $outputImagePath = base_path('uploads/certificates/' . $name);
                $outputImagePath = $location .'/'. $name;
                $image->save($outputImagePath);
                
                return [
                    'name' => $name,
                    'certificate_number' => $certificate_number
                ];
            }
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
        function generateCertificateNumber($program, $user){
            $randomNumber = generateRandomNumberBasedOnKey($program->programAbbr. $program->id, 100, 999);
            $programId = $program->id;
            $programAbbr = $program->p_abbr ?? 'CT';
            $programAbbr = $program->p_abbr ?? 'CT';
            $programAbbr = str_replace('#', '', $programAbbr);
            
            $certificate_number = strtoupper($programAbbr) . '-' . $programId . '-' . $randomNumber . '-' . $user->id;

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
            // TAKE CARE OF PROGRAMS FROM WAACSP
            $waacsp_program_ids = [68];
            if(!empty($program_id) && in_array($program_id, $waacsp_program_ids)){
                $accounts = [
                    [
                        'bank' =>  'GTB',
                        'number' => '0610151960',
                        'name' => ' West Africa Ass of Customer SP',
                        'status' => 1,
                        'country' => 'Nigeria'
                    ],
                    [
                        'bank' =>  'GTB (Gambia)',
                        'number' => '0610151960',
                        'name' => ' West Africa Ass of Customer SP',
                        'status' => 1,
                        'country' => 'Gambia'
                    ],
                    [
                        'bank' =>  'Mobile Money (MoMo)',
                        'number' => '0557963331',
                        'name' => 'Frank Asiedu',
                        'status' => 1,
                        'country' => 'Ghana'
                    ],
                ];
            }else{
                $accounts = [
                    [
                        'bank' =>  'Access Bank',
                        'number' => '0106070151',
                        'name' => 'Employme E-learning',
                        'status' => 1,
                        'country' => 'Nigeria'
                    ],
                    // [
                    //     'bank' =>  'GTB',
                    //     'number' => '0434442453',
                    //     'name' => 'EmployMe E-Learning',
                    //     'status' => 1,
                    //     'country' => 'Nigeria'
                    // ],

                    [
                        'bank' =>  'Mobile Money (MoMo)',
                        'number' => '0557963331',
                        'name' => '3y publicity limited (Frank Asiedu)',
                        'status' => 1,
                        'country' => 'Ghana'
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
            
            if (request()->prefix__ == '/admin' || in_array($currentRouteName, $routes)) {
                $user = $user ?? Auth::guard('admin')->user();
            } else {
                $user = $user ?? Auth::user();
            }

            return $user;
        }
    }

    if (!function_exists("checkTrainingHasPermissions")) {
        function checkTrainingHasPermissions($training_id, $permissionsToCheck = null)
        {
            $result = [];
            $userPermissions = resolveAuthUser()->trainingPermissions();
           
            $userTrainingPermissions = $userPermissions->where('program_id', $training_id)->first();
            $trainingPermissions = $userTrainingPermissions->training_permissions ?? [];
            
            // If specific permissions are provided, check them
            if (!empty($permissionsToCheck)) {
                foreach ($permissionsToCheck as $permission) {
                    if (in_array(resolveAuthUser()->id, [1])) {
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
