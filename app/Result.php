<?php

namespace App;
use App\Program;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'user_id';

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function module(){
        return $this->belongsTo(Module::class);
    }
}
