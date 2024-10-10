<?php

namespace App\Models;

use App\Models\CompanyUserTraining;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CompanyUser extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = ['permissions' => 'array'];

    public function trainings()
    {
        return $this->hasMany(CompanyUserTraining::class);
    }
}
