<?php

if (!function_exists('getProgramModuleAvailability')) {
    function getProgramModuleAvailability($program, $module): bool
    {
        return !empty($program->ai_settings['status'])
            && !empty($program->ai_settings['use_cases'])
            && in_array($module, $program->ai_settings['use_cases'], true);
    }
}
