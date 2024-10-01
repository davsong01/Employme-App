<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityCronTask extends Model
{
    use HasFactory;
    protected $casts = ['payload' => 'array'];
    protected $guarded = [];
}
