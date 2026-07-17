<?php

namespace App\Models;

use App\Models\User;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $guarded = [];
    protected $casts = [
        'follow_up_at' => 'date',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
