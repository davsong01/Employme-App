<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $guarded = [];

    public function templateName(){
        return $this->belongsTo(Template::class, 'frontend_template');
    }
}
