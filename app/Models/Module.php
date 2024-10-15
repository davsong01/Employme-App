<?php

namespace App\Models;

use App\Models\Mocks;
use App\Models\Program;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $guarded = [];

    public function getTypeAttribute($attribute){
        return [
            0 => 'Class Test',
            1 => 'Certification Test',
        ][$attribute];

    }

    public function questions(){
        return $this->hasMany(Question::Class);
    }

    public function results(){
        return $this->hasMany(Result::Class);
    }

    public function mocks(){
        return $this->hasMany(Mocks::Class);
    }

    public function program(){
        return $this->belongsTo(Program::Class);
    }

    public function scopeClassTests($query, $program){
           
        return $query->where('type', 0)->where('program_id', $program);
       
    }

}
