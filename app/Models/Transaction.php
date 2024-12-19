<?php

namespace App\Models;

use App\Models\User;
use App\Models\Mocks;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Certificate;
use App\Models\ResultThread;
use App\Models\PaymentThread;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];
    protected $table = 'program_user';
    protected $casts = ['training_result' => 'object'];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
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

    public function certification_resits(){
        return $this->hasMany(ResultThread::class, 'program_id', 'program_id')->where('user_id', $this->user_id)->where('program_id', $this->user_id)->whereNotNull('certification_test_details');
    }
}
