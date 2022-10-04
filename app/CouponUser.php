<?php

namespace App;

use App\User;
use App\Program;
use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        $user = User::whereEmail($this->email)->first();
        return $user;
    }

    public function couponusers()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');

    }
}
