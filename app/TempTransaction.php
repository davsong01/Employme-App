<?php

namespace App;

use App\Coupon;
use App\Program;
use Illuminate\Database\Eloquent\Model;

class TempTransaction extends Model
{
    protected $guarded = [];

    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

     public function program(){
        return $this->belongsTo(Program::class, 'program_id');
    }
}
