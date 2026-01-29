<?php

namespace App\Models;

use App\Models\Program;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;

class ScoreSetting extends Model
{
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'App\Program');
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

   
}
