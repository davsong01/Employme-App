<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateGenerationHistory extends Model
{
    use HasFactory;
    protected $table = 'certificate_regeneration_history';
    protected $guarded = [];
}
