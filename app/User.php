<?php

namespace App;

use Auth;
use App\Result;
use App\Program;
use App\Complain;
use App\Certificate;
use Illuminate\Support\Facades\Session;
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

        public function certificate(){
            return $this->hasOne(Certificate::class);
        }

        public function certificates(){
            return $this->hasMany(Certificate::class);
        }

        public function programs(){
            return $this->belongsToMany(Program::class);
        } 

        public function setImpersonating($id)
        {
            Session::put('impersonate', $id);
        }
    
        public function stopImpersonating()
        {
            Session::forget('impersonate');
        }
    
        public function isImpersonating()
        {
            return Session::has('impersonate');
        }   
        
        
       
}
   

