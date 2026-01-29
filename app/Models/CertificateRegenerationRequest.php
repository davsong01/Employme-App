<?php

namespace App\Models;

use App\Models\User;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificateRegenerationRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = ['meta' => 'array'];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}