<?php

namespace App\Models;

use App\Models\User;
use App\Models\Program;
use App\Models\CouponUser;
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

    public function coupon_users()
    {
        return $this->hasMany(CouponUser::class, 'coupon_id');
    }
}
