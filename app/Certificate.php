<?php

namespace App;

use App\User;
use App\Module;

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
        $score_settings = ScoreSetting::whereProgramId($this->program_id)->first();

        $data['total'] = 0;
        $data['certification_test_score'] = $results->sum('certification_test_score') ?? 0;
        $data['role_play_score'] = $results->sum('role_play_score') ?? 0;
        $data['email_test_score'] = $results->sum('email_test_score') ?? 0;
        $program_ct_score_settings = $score_settings->class_test ?? null;
        $passmark = $score_settings->passmark ?? null;
        $class_test_score = 0;
        $obtainable  = 0;
        foreach($results as $result){
            if ($result->module->type == 'Class Test') {
                $u =  Module::where('type', 0)->where('program_id', $this->program_id)->get();
                $ob = [];

                foreach ($u as $t) {
                    array_push($ob, $t->questions->count());
                }

                $obtainable = array_sum($ob);
               
                if ($u->count() > 0) {
                    $class_test_score = $result->class_test_score + $class_test_score;
                }

                
            }  

            
        }
        // dd($obtainable);
        $final_ct_score  = $obtainable > 0 ? round(($class_test_score * $program_ct_score_settings) / $obtainable, 0) : 0;
         
        $data['class_test_score'] = $final_ct_score;
        $data['total'] = $results->sum('certification_test_score') +  $final_ct_score + $results->sum('role_play_score') + $results->sum('email_test_score');
       
        return $data;
      
    }
}
