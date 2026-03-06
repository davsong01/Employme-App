<?php

namespace App\Models\AI;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRequest extends Model
{
    protected $guard = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function material() {
        return $this->belongsTo(Material::class);
    }
}
