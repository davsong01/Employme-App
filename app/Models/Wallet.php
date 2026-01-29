<?php

namespace App\Models;

use App\Models\User;
use App\Models\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentMode::class, 'provider');
    }


}
