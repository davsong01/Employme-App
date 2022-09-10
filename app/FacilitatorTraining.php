<?php

namespace App;

use App\User;
use App\Mocks;
use App\Program;
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
}
