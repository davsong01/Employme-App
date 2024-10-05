<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Certificate;
use Illuminate\Http\Request;
use App\Models\UtilityCronTask;

class UtilityTaskController extends Controller
{
    public function runTool(){
        // $this->generateOldCertificateNumbers();
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

    public function generateOldCertificateNumbers(){
        $certificates = Certificate::whereNull('certificate_number')->whereNotIn('program_id', [83,84])->get();
        
        if($certificates->count() < 1){
            return;
        }

        foreach($certificates as $certificate){
            $program = Program::find($certificate->program_id);
            $user = User::find($certificate->user_id);

            $certificate_number = generateCertificateNumber($program, $user);

            $certificate->certificate_number = $certificate_number;
            $certificate->save();
        }

        return;
    }
}
