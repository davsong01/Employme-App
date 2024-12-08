<?php

namespace App\Models;

use App\Models\User;
use App\Models\Mocks;
use App\Models\Program;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class FacilitatorTraining extends Model
{
    protected $guarded = [];
    protected $casts = ['training_permissions' => 'array'];
    
    public function trainings()
    {
        return $this->HasMany(Program::class)->where('program_lock', 0);
    }

    public function programName(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'program_id');
    }

    // public function users()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function training()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function transactions()
    {
        return Transaction::whereIn('program_id', $this->trainings()->pluck('program_id'))->get();
    }

}
