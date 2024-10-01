<?php

namespace App\Http\Controllers;

use App\Models\UtilityCronTask;
use Illuminate\Http\Request;

class UtilityTaskController extends Controller
{
    public function runTool(){
        $pending = UtilityCronTask::get();
        $rand = rand();

        if($pending->count() < 1){
            dd('no pending tasks');
        }

        foreach($pending as $pend){
            $request = new Request($pend->payload);
            
            if($pend && $pend->key == 'certificate-generation'){
                $process = app('App\Http\Controllers\CertificateController')->generateCertificates($request, $pend->payload['program_id'], true);
    
                if($process && isset($process['status']) && $process['status'] == 'success'){
                    $pend->status = 'completed';
                    $pend->save();
                }else{
                    // $pending->update(['response' => 'completed']);
                }
            }

        }

        dd('all done');

    }
}
