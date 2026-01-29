<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CertificateRegenerationTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificateProgram extends Model
{
    use HasFactory;

    public function regenerationTemplates()
    {
        return $this->belongsToMany(CertificateRegenerationTemplate::class, 'certificate_program_template');
    }
}
