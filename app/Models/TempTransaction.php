<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\PaymentMode;
use Illuminate\Database\Eloquent\Model;

class TempTransaction extends Model
{
    protected $guarded = [];
    protected $casts = ['program_ids' => 'array','meta'=>'array'];
    
    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function program(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'program_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode');
    }

    public function scopeAllPrograms()
    {
        $programs = Program::select('id', 'p_abbr', 'p_name')->whereIn('id', $this->program_ids)->get();

        return $programs;
    }
}
