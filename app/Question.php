<?php

namespace App;

use App\Module;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];
    
    public function module(){
        return $this->belongsTo(Module::Class);
    }
}
