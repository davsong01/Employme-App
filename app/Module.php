<?php

namespace App;

use App\Program;
use App\Question;
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

    public function program(){
        return $this->belongsTo(Program::Class);
    }
}
