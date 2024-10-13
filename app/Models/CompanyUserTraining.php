<?php

namespace App\Models;

use App\User;
use App\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyUserTraining extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function program(){
        return $this->hasOne(Program::class, 'id','program_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

}
