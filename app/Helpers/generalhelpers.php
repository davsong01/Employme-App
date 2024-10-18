<?php

use App\Models\Module;
use App\Models\Result;
use App\Models\Program;
use App\Models\Settings;
use App\Models\Transaction;

if (!function_exists("certificationStatus")) {
    function certificationStatus($program_id, $user_id)
    {
        $result = Result::with('program', 'module', 'user')->where('user_id', $user_id)->whereProgramId($program_id)->get();
        $program = Program::find($program_id);

        $details = [];
        $class = $email = $roleplay = $crm = $certification = 0;

        // Get the modules and calculate the total obtainable score
        $modules = Module::with('questions')
            ->where('type', 'Class Test')
            ->where('program_id', $program_id)
            ->where('status', 1)
            ->get();

        $obtainable = $modules->sum(fn($module) => $module->questions->count());
        
        foreach ($result as $t) {
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
        }

        // Add other test scores to the details array
        $details['email_test_score'] = $email;
        $details['role_play_score'] = $roleplay;
        $details['crm_test_score'] = $crm;
        $details['certification_test_score'] = $certification;

        // Calculate the total score and add program details
        $details['total_score'] = $details['class_test_score'] + $email + $roleplay + $certification;
        $details['passmark'] = $t['passmark'];
        $details['program'] = $t['program'];
        $details['name'] = $t['name'];
        $details['staffID'] = $t->user->staffID;

        // Determine certification status
        $details['status'] = ($details['total_score'] >= $details['passmark']) ? 'CERTIFIED' : 'NOT CERTIFIED';
        $details['results'] = $result;
        $details['program'] = $program;
        
        return $details;
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
            function generateCertificate($request, $program_id, $location, $user=null)
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
                    $text = 'Aboki Ogbeni Chuckwuma';
                    
                    $text_type = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $certificate_settings['settings'][$i]['text_type'];
                    // Get text
                    if($text_type == 'name') $text = $user->name ?? $text;
                    if($text_type == 'email') $text = $user->email;
                    if($text_type == 'staffID') $text = $user->staffID ?? 'NO STAFF ID SET';

                    $certificate_number = generateCertificateNumber($program, $user);
                    
                    if($text_type == 'certificate_number'){
                        $text = $certificate_number;
                    }

                    // End text
                    $image->text($text, $auto_certificate_left_offset, $auto_certificate_top_offset, function ($font) use ($size, $color, $auto_certificate_font_weight) {
                        $font->file(public_path('Pesaro-Bold.ttf'));
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

    

    if (!function_exists("adminRoles")) {
        function adminRoles(){
            return [
                'Admin',
            ];
        }
    }

    if (!function_exists("facilitatorRoles")) {
        function facilitatorRoles(){
            return [
                'Facilitator',
            ];
        }
    }

    if (!function_exists("studentRoles")) {
        function studentRoles(){
            return [
                'Student',
            ];
        }
    }

    if (!function_exists("teacherRoles")) {
        function teacherRoles(){
            return [
                'Teacher',
            ];
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






    // if (!function_exists("getOptions")) {
    //     function getOptions($options)
    //     {
    //         $opts = preg_split("/\r\n|\n|\r/", $options);
    //         $p_opts = [];
    //         foreach ($opts as $opt) {
    //             $spt_opt = explode('||', $opt);
    //             $p_opts[trim($spt_opt[0]) . ':=:' . trim($spt_opt[1])] = trim($spt_opt[1]);
    //         }
    //         return array_filter($p_opts);
    //     }
    // }
