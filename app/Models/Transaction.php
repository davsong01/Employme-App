<?php

namespace App\Models;

use App\Models\User;
use App\Models\Mocks;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Certificate;
use App\Models\ResultThread;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
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

    public function certification_resits($program_id=null, $user_id=null){
        $histories = null;
        if($program_id && $user_id){
            $histories = ResultThread::where('program_id', $program_id)->where('user_id', $user_id)->whereNull('class_test_details')->orderBy('id','desc')->get();
        }
        return $histories;
    }

    public function classtests_resits($program_id = null, $user_id = null)
    {
        $histories = null;
        if ($program_id && $user_id) {
            $histories = ResultThread::where('program_id', $program_id)->where('user_id', $user_id)->whereNotNull('class_test_details')->orderBy('id', 'desc')->get();
        }
        
        return $histories;
    }

    public function paymentLog(){
        return $this->belongsTo(TempTransaction::class, 'transid','transid');
    }
}
