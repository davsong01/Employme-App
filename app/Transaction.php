<?php

namespace App;

use App\User;
use App\Coupon;
use App\Program;
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

}


