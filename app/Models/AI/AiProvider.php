<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    protected $guard = [];

    protected $casts = [
        'config' => 'array',
        'active' => 'boolean'
    ];
}
