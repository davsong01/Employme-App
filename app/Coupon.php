<?php

namespace App;

use App\User;
use App\Program;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code','amount','facilitator_id', 'program_id'];

    public function facilitator(){
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
