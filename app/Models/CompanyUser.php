<?php

namespace App\Models;

use App\Models\CompanyUserTraining;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyUser extends Model
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
