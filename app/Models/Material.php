<?php

namespace App\Models;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
