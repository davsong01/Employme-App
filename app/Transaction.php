<?php

namespace App;

use App\User;
use App\Program;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'program_user';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }
}

