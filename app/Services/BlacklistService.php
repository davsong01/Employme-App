<?php
namespace App\Services;

use App\Models\Blacklist;


class BlacklistService
{
    public static function check($model): bool
    {
        $checks = [];

        if (!empty($model->email)) {
            $checks[] = ['email', $model->email];
        }
        if (!empty($model->phone)) {
            $checks[] = ['phone', $model->phone];
        }
        if (request()->ip()) {
            $checks[] = ['ip', request()->ip()];
        }
        
        foreach ($checks as [$type, $value]) {
            if (Blacklist::isBlacklisted($type, $value)) {
                return true;
            }
        }

        return false;
    }

    public static function checkByValues(array $data): bool
    {
        foreach ($data as $type => $value) {
            if (Blacklist::isBlacklisted($type, $value)) {
                return true;
            }
        }
        return false;
    }
}
