<?php

namespace App\Models;

use App\Models\Program;
use App\Models\CertificateProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificateRegenerationTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['auto_certificate_settings' => 'array'];

    // public function certificatePrograms()
    // {
    //     return $this->belongsToMany(Program::class, 'certificate_programs', 'certificate_regeneration_id', 'program_id');
    // }

    public function certificatePrograms()
    {
        return $this->hasOneThrough(
            Program::class,
            CertificateProgram::class,
            'certificate_regeneration_id',
            'id',
            'id',
            'program_id'
        );
    }
}
