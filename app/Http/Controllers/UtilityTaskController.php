<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Certificate;
use App\Models\Transaction;
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

    public function resolveTrainingResult(){
        $transactions = Transaction::whereHas('program', function ($query) {
            $query->whereHas('scoresettings') // Program has scoresettings
            ->whereHas('modules', function ($moduleQuery) {
                $moduleQuery->where('computation_status', 1); // Modules with computation_status = 1
            });
        })
        ->whereHas('user', function ($query) {
            $query->whereHas('results');
        })
        ->whereNull('training_result')
        
        ->get();
        
        $count = 0;
        foreach($transactions as $transaction){
            $count ++;
            // Calculate transaction scores and certification status
            $certificationStatus = certificationStatus($transaction->program_id, $transaction->user_id);
            $transaction->training_result = [
                "class_test_score" => $certificationStatus['class_test_score'] ?? 0,
                "email_test_score" => $certificationStatus['email_test_score'] ?? 0,
                "roleplay_test_score" => $certificationStatus['role_play_score'] ?? 0,
                "crm_test_score" => $certificationStatus['crm_test_score'] ?? 0,
                "certification_test_score" => $certificationStatus['certification_test_score'] ?? 0,
                "total_score" => $certificationStatus['total_score'] ?? 0,
                
                "certification_facilitator" => $certificationStatus['certification_facilitator'] ?? null,
                "certification_grader" => $certificationStatus['certification_grader'] ?? null,
                "certification_facilitator_comment" => $certificationStatus['certification_facilitator_comment'] ?? null,
                "certification_grader_comment" => $certificationStatus['certification_grader_comment'] ?? null,

                "class_test_resit_status" => 0,
                "class_test_resit_expiry" => NULL,

                "email_test_resit_status" => 0,
                "email_test_resit_expiry" => NULL,

                "roleplay_test_resit_status" => 0,
                "roleplay_test_resit_expiry" => NULL,

                "certification_test_resit_status" => $transaction->user->redotest != 0 ? 1 : 0,
                "certification_test_resit_expiry" => NULL,

                "crm_test_resit_status" => 0,
                "certification_test_resit_expiry" => NULL,
            ];
            
            if(!empty($certificationStatus['certification_facilitator'])){
                \Log::info($transaction->id);
            }

            $transaction->save();
        }
        
        dd($count . ' Transactions Updated');
    }
}
