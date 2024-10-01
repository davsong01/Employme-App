<?php

namespace App\Http\Controllers;

use App\Models\UtilityCronTask;
use Illuminate\Http\Request;

class UtilityTaskController extends Controller
{
    public function runTool(){
        $pending = UtilityCronTask::where('status', 'pending')->first();
        $rand = rand();

        if(!$pending){
            dd('no pending tasks');
        }
        $request = new Request($pending->payload);
        
        $pending->update(['status' => 'picked'. $rand]);
    
        if($pending && $pending->key == 'certificate-generation'){
            $process = app('App\Http\Controllers\CertificateController')->generateCertificates($request, $pending->payload['pick'], true);

            if($process && isset($process['status']) && $process['status'] == 'success'){
                $pending->update(['status' => 'completed']);
            }else{
                // $pending->update(['response' => 'completed']);
            }
        }

        dd('all done');

    }
}
