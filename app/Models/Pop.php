<?php

namespace App\Models;

use App\Models\Group;
use App\Models\TempTransaction;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    protected $guarded = [];

    public function scopeOrdered($query)
    {
        return $query->ORDERBY('date', 'DESC');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getRelatedAttribute()
    {
        return !$this->is_package ? $this->program : $this->group;
    }

    public function user(){
        return $this->belongsTo(User::class, 'email','email');
    }

    public function temp(){
        return $this->belongsTo(TempTransaction::class, 'temp_transaction_id');
    }

    
}
