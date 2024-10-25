<?php

namespace App\Models;

use App\Models\User;
use App\Models\Mocks;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;

class FacilitatorTraining extends Model
{
    protected $guarded = [];
    
    public function trainings()
    {
        return $this->HasMany(Program::class);
    }
    public function users(){
        return $this->belongsTo(User::class);
    }

    public function programName(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'program_id');
    }
}
