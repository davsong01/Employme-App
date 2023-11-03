<?php
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
