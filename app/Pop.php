<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    protected $guarded = [];

     public function scopeOrdered($query)
    {
        return $query->ORDERBY('date', 'DESC');
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'email','email');
    }
}
