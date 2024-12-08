<?php

namespace App\Models;

use Auth;
use App\Models\Mocks;
use App\Models\Result;
use App\Models\Program;
use App\Models\Complain;
use App\Models\Material;
use App\Models\Certificate;
use App\Models\PaymentMode;
use App\Models\Transaction;
use App\Models\FacilitatorTraining;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $casts = ['metadata' => 'array', 'menu_permissions' => 'array'];
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

    public function trainings()
    {
        return $this->hasMany(FacilitatorTraining::class, 'user_id');
    }

    public function scopeTrainingPermissions($query, $training_id = null)
    {
        $trainings = $this->trainings()->get();

        if (!empty($training_id)) {
            $trainingPermissions = $trainings->where('program_id', $training_id)->pluck('training_permissions')->first();
            return $trainingPermissions;
        }

        return $trainings;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
    
    public function userTrainings()
    {
        if($this->role_id == 'Student'){
            return Program::isUserProgram()->with(['subPrograms'])->whereHas('transactions', function ($query) {
                $query->where('user_id', $this->id);
            })->get();
        }else{
            return Program::isUserProgram()->with(['subPrograms'])->whereHas('trainings', function ($query) {
                $query->where('user_id', $this->id);
            })->get();
        }
    }

    public function trainerStudents()
    {
        // Fetch program IDs linked to this facilitator
        $programIds = $this->trainings()->pluck('program_id');
        
        // Ensure program IDs are not empty
        if ($programIds->isEmpty()) {
            return collect(); // Return an empty collection if no programs are found
        }

        // Fetch students linked to these programs via transactions
        $students = User::where('role_id', 'Student')
        ->whereHas('transactions', function ($query) use ($programIds) {
            $query->whereIn('program_id', $programIds);
        })->get();

        return $students;
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

    public function scopePermissions(){
        // $a_menu = in_array($this->id, [1]) ? array_merge(allRoutes(), allAccess()) : ($this->menu_permissions ?? []);
        $a_menu = in_array($this->id, [1]) ? allRoutes() : ($this->menu_permissions ?? []);            

        return $a_menu; 
    }
 
}

