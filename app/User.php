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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   
  
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
        public function program(){
            return $this->belongsTo(Program::class);
        }  
        public function result(){
            return $this->belongsTo(Result::class);
        }
        public function complains(){
            return $this->hasMany(Complain::class);
        }
        // public function scopepaymentStatus($query)
        // {
        //     return $query->where('paymentStatus', '=', 0);
        // }
}
   

