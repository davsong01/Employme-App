<?php

namespace App;
use App\Program;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class)->orderBy('p_name');
    }
}
