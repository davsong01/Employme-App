<?php

namespace App\Models;

use DavidOghi\CertificateGeneration\Models\CertificateTemplate as BaseCertificateTemplate;

class CertificateTemplate extends BaseCertificateTemplate
{
    protected $casts = [
        'settings' => 'array',
        'supported_modules' => 'array',
        'status' => 'boolean',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class, 'certificate_template_id');
    }
}
