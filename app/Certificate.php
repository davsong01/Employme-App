<?php

namespace App;

use App\User;
use App\Result;

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

    public function scores(){
        $results = Result::where('user_id', $this->user_id)->where('program_id', $this->program_id)->get();
        
        $data['total'] = 0;
        $data['certification_test_score'] = $results->sum('certification_test_score') ?? 0;
        $data['class_test_score'] = $results->sum('class_test_score') ?? 0;
        $data['role_play_score'] = $results->sum('role_play_score') ?? 0;
        $data['email_test_score'] = $results->sum('email_test_score') ?? 0;

        $data['total'] = $results->sum('certification_test_score') + $results->sum('class_test_score') + $results->sum('role_play_score') + $results->sum('email_test_score');
       
        return $data;
      
    }
}
