<?php

namespace App\Models;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }
}
