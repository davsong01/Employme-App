<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\Program;
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
