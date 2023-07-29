<?php

namespace App;

use App\User;
use App\Program;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function show_certificate()
    {
        $check = Transaction::where(['user_id' =>$this->user_id, 'program_id'=>$this->program_id])->first();
        $access = 'Disabled';
        
        if($check){
            $access = $check->show_certificate == 1 ? 'Enabled' : 'Disabled';
        }
      
        return $access;
    }
}
