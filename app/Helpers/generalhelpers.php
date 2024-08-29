<?php
    if (!function_exists("generateCertificate")) {
        function generateCertificate($inputImagePath, $text, $auto_certificate_left_offset, $auto_certificate_top_offset, $auto_certificate_font_weight,$size, $color, $location)
        {
            $image = Image::make($inputImagePath);

            // $size = $certificate_settings['auto_certificate_name_font_size'] ?? 150;
            // $color = $certificate_settings['auto_certificate_color'] ?? "#000000";
            // $auto_certificate_top_offset = $certificate_settings['auto_certificate_top_offset'] ?? 300;
            // $auto_certificate_left_offset = $certificate_settings['auto_certificate_left_offset'] ?? 150;

            if ($image->width() > 4000 || $image->height() > 4000) {
                $image->resize(4000, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $image->text($text, $auto_certificate_left_offset, $auto_certificate_top_offset, function ($font) use ($size, $color, $auto_certificate_font_weight) {
                $font->file(public_path('Pesaro-Bold.ttf'));
                $font->size($size);
                $font->color($color);
                // $font->weight($auto_certificate_font_weight);
            });
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
