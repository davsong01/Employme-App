<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mocks extends Model
{
    protected $guarded = [];

    public function module(){
        return $this->belongsTo(Module::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }
}
