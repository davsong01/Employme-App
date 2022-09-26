<?php

namespace App;

use Auth;
use App\Mocks;
use App\Result;
use App\Program;
use App\Complain;
use App\Material;
use App\Certificate;
use App\PaymentMode;
use App\FacilitatorTraining;
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

       
        public function startRedoStatus($pid){
            $this->redotest = $pid;
            return $this->save(); 
        }
        
        public function endRedoTest(){
            $this->redotest = 0;
            return $this->save();
        }

        public function getRedoStatus(){
            return $this->redotest;
        }


        public function getName(){
            return $this->name;
        }

        public function program(){
            return $this->belongsTo(Program::class);
        }  
        public function results(){
            return $this->hasMany(Result::class);
        }
        public function mocks(){
            return $this->hasMany(Mocks::class);
        }
        public function complains(){
            return $this->hasMany(Complain::class);
        }

        public function certificates(){
            return $this->hasMany(Certificate::class);
        }

        public function programs(){
            return $this->belongsToMany(Program::class);
        } 

        //Facilitator's relationship
        public function trainings()
        {
            return $this->hasMany(FacilitatorTraining::class);
        }

        // public function students()
        // {
        //     return $this->hasMany(User::class, 'facilitator_id');
        // }
            
        // public function facilitator()
        // {
        //     return $this->belongsTo(User::class, 'facilitator_id');
        // }

        public function payment_modes(){
            return $this->belongsTo(PaymentMode::class, 'payment_mode');
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
   

