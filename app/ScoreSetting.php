<?php

namespace App;

use App\Program;
use App\Module;
use Illuminate\Database\Eloquent\Model;

class ScoreSetting extends Model
{
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo('App\Module', 'App\Program');
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

   
}
