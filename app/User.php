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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $casts = ['metadata' => 'array'];
    protected $guarded = [];
    protected $append = ['t_phone','account_balance'];

    use Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function startRedoStatus($pid){
        $this->redotest = $pid;
        return $this->save(); 
    }
    
    public function endRedoTest($result_id){
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

    public function getPhoneAttribute()
    {
        if ($this->attributes['t_phone'][0] != "0") {
            return "0" . $this->attributes['t_phone'];
        }
        return $this->attributes['t_phone'];
    }

    public function getAccountBalanceAttribute()
    {
        return app('App\Http\Controllers\WalletController')->getWalletBalance($this->id);
    }

    protected function scopeRole()
    {
        $role_id = explode(',',$this->role_id);
        return $role_id;
    }

    // protected function scopeRole($query)
    // {
    //     $role_ids = explode(',', $this->role_id);
    //     return $query->whereIn('role_id', $role_ids);
    // }

    public function scopePermissions(){
        $a_menu = $this->menu_permissions ?? '';
        $a_menu = explode(',', $a_menu);

        $a_menu = !empty($a_menu) ? $a_menu : [];
       
        return $a_menu; 
    }    
}
   

