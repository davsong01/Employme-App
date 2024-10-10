<?php

namespace App\Models;

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

}
