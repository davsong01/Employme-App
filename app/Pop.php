<?php

namespace App;

use App\TempTransaction;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    protected $guarded = [];

     public function scopeOrdered($query)
    {
        return $query->ORDERBY('date', 'DESC');
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'email','email');
    }

    public function temp(){
        return $this->belongsTo(TempTransaction::class, 'temp_transaction_id');
    }
}
