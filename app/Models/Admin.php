<?php

namespace App\Models;

use App\Models\PaymentMode;
use App\Models\CompanyUserTraining;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = ['metadata' => 'array', 'menu_permissions' => 'array', 'roles' => 'array'];

    public function userTrainings()
    {
        if ($this->roles == 'Student') {
            return Program::isUserProgram()->with(['subPrograms'])->whereHas('transactions', function ($query) {
                $query->where('user_id', $this->id);
            });
        } else {
            return Program::isUserProgram()->with(['subPrograms'])->whereHas('trainings', function ($query) {
                $query->where('user_id', $this->id);
            });
        }
    }
    
    public function trainerStudents()
    {
        $programIds = $this->trainings()->pluck('program_id');
        $programIds = $this->userTrainings()->pluck('id')->toArray();
        
        if (empty($programIds)) {
            return collect(); 
        }

        $students = User::where('roles', 'Student')
        ->whereHas('transactions', function ($query) use ($programIds) {
            $query->whereIn('program_id', $programIds);
        });

        return $students;
    }

    public function trainings()
    {
        return $this->hasMany(FacilitatorTraining::class, 'user_id');
    }

    protected function scopeRole()
    {
        $roles = $this->roles;
        return $roles;
    }

    public function scopePermissions()
    {
        $a_menu = in_array($this->id, [1]) ? allRoutes() : ($this->menu_permissions ?? []);

        return $a_menu;
    }

    public function setImpersonating($id)
    {
        // Session::put('impersonate', $id);
        Session::put('impersonate_admin', $id);
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonate_o')) {
            // Session::forget('impersonate');
            Session::forget('impersonate_admin');
            Auth::guard('admin')->logout($this);
            Auth::guard('admin')->loginUsingId(session()->get('impersonate_o'));
        }
    }

    public function isImpersonating()
    {
        return Session::has('impersonate_admin');
    }


    public function payment_modes()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode');
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

}
