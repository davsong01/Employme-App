<?php

use App\Program;
use App\Transaction;

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
                
                if($text_type == 'certificate_number'){
                    $programId = $program->id;
                    $programAbbr = $program->p_abbr ?? '#';

                    $randomNumber = rand(100, 999);

                    $text = strtoupper($programAbbr) . '-' . $programId . '-' . $randomNumber. '-'.$user->id;
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
            return $name;
        }
    }


    if (!function_exists("graderRoles")) {
        function graderRoles(){
            return [
                'Grader',
            ];
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
