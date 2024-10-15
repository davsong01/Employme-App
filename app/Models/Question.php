<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];
    
    public function module(){
        return $this->belongsTo(Module::Class);
    }
}
