<?php

namespace App;

use Auth;
use App\Result;
use App\Program;
use App\Complain;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];
    use Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];
        public function program(){
            return $this->belongsTo(Program::class);
        }  
        public function results(){
            return $this->hasMany(Result::class);
        }
        public function complains(){
            return $this->hasMany(Complain::class);
        }
       
}
   

