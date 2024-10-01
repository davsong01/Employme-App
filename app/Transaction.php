<?php

namespace App;

use App\User;
use App\Mocks;
use App\Coupon;
use App\Program;
use App\Certificate;
use App\Models\PaymentThread;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'program_user';

    protected $guarded = [];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentthreads()
    {
        return $this->hasMany(PaymentThread::class, 'parent_transaction_id','transid');
    }

    public function paymentthreadsbyinvoice()
    {
        return $this->hasMany(PaymentThread::class, 'parent_transaction_id', 'invoice_id');
    }

    public function results(){
        return $this->hasMany(Result::class, 'user_id', 'user_id');
    }

    public function mocks()
    {
        return $this->hasMany(Mocks::class, 'user_id', 'user_id');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'user_id', 'user_id')
        ->whereColumn('program_id', 'program_id');
    }

}


