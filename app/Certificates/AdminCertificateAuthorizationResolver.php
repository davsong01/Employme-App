<?php

namespace App\Certificates;

use App\Models\Admin;
use DavidOghi\CertificateGeneration\Contracts\CertificateAuthorizationResolver;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminCertificateAuthorizationResolver implements CertificateAuthorizationResolver
{
    public function canManage(?Authenticatable $actor = null): bool
    {
        if (! $actor instanceof Admin) {
            return false;
        }

        if ((int) $actor->getAuthIdentifier() === 1) {
            return true;
        }

        if (($actor->status ?? 'active') !== 'active') {
            return false;
        }

        $roles = array_values(array_filter(array_map('strval', (array) ($actor->roles ?? []))));

        return count(array_intersect($roles, ['Admin', 'Facilitator', 'Grader'])) > 0;
    }
}
