<?php

namespace App\Http\Controllers;

use App\Errors\Services\SystemLogService;
use App\Mail\Email;
use App\Models\Certificate;
use App\Models\Group;
use App\Models\Program;
use App\Models\TempTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilityCronTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class UtilityTaskController extends Controller
{
    // public function runTool(){
    //     // $this->generateOldCertificateNumbers();
    //     $pending = UtilityCronTask::where('status', 'pending')->get();

    //     if ($pending->isEmpty()) {
    //         dd('no pending tasks');
    //     }

    //     foreach($pending as $pend){
    //         $request = new Request($pend->payload);

    //         // if($pend && $pend->key == 'certificate-generation'){
    //         //     $process = app('App\Http\Controllers\CertificateController')->generateCertificates($request, $pend->payload['program_id'], true);

    //         //     if($process && isset($process['status']) && $process['status'] == 'success'){
    //         //         $pend->status = 'completed';
    //         //         $pend->save();
    //         //     }else{
    //         //         // $pending->update(['response' => 'completed']);
    //         //     }
    //         // }
    //         if ($pend->key == 'certificate-generation') {
    //             $process = app('App\Http\Controllers\CertificateController')
    //                 ->generateCertificates($request, $pend->payload['program_id'], true);

    //             if ($process && isset($process['status'])) {

    //                 if ($process['status'] == 'success') {
    //                     // certificates still generated, keep task active
    //                     continue;
    //                 }

    //                 if ($process['status'] == 'failed') {
    //                     // tracker is exhausted → mark task completed
    //                     $pend->status = 'completed';
    //                     $pend->save();
    //                 }
    //             }
    //         }
    //     }

    //     dd('all done');

    // }
    public function runTool()
    {
        $lock = Cache::lock('utility-cron-tasks', 300);

        if (!$lock->get()) {
            return response()->json(['message' => 'Utility tasks are already running.'], 409);
        }

        try {
            return $this->processPendingTasks();
        } finally {
            $lock->release();
        }
    }

    private function processPendingTasks()
    {
        $pending = UtilityCronTask::where('status', 'pending')->limit(25)->get();

        if ($pending->isEmpty()) {
            return response()->json([
                'message' => 'No pending tasks',
                'successful_runs' => 0,
                'completed_tasks' => 0
            ]);
        }

        $successfulRuns = 0;
        $completedTasks = 0;

        foreach ($pending as $pend) {

            if ($pend->key !== 'certificate-generation') {
                continue;
            }

            $request = new Request($pend->payload);

            $process = app('App\Http\Controllers\CertificateController')
                ->generateCertificates($request, $pend->payload['program_id'], true);

            if (!$process || !isset($process['status'])) {
                continue;
            }

            // CERTIFICATES STILL PROCESSING
            if ($process['status'] === 'success') {
                $successfulRuns++;
                continue;
            }

            // TRACKER EXHAUSTED → MARK CRON COMPLETED
            if ($process['status'] === 'completed') {
                $pend->status = 'completed';
                $pend->save();
                $completedTasks++;
                continue;
            }

            // INTERNAL ERROR
            if ($process['status'] === 'failed') {
                // do NOT mark as completed, keep pending for retry
                continue;
            }
        }

        return response()->json([
            'message' => 'Cron execution finished',
            'successful_runs' => $successfulRuns,
            'completed_tasks' => $completedTasks,
        ]);
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

    public function renameCertificatesWithSpaceInFilename(){
        $certificates = Certificate::where('file', 'like', '% %')->get();
        $count = 0;

        foreach ($certificates as $certificate) {
            $oldFileName = $certificate->file;
            $extension = File::extension($oldFileName);
            
            if(empty($certificate->certificate_number)){
                $randomFileName = generateCertificateNumber($certificate->program, $certificate->user).'.'.$extension;
            }else{
                $randomFileName = $certificate-> certificate_number . '.' . $extension;
            }
            
            $oldFilePath = base_path('uploads/certificates/' . $oldFileName);
            $newFilePath = base_path('uploads/certificates/' . $randomFileName);
            
            if (File::exists($oldFilePath)) {
                File::move($oldFilePath, $newFilePath);

                $certificate->file = $randomFileName;
                $certificate->save();

                \Log::channel('certificate')->info([
                    'Action' => 'Certificate Renaming',
                    'Old name' => $oldFileName,
                    'New name' => $randomFileName,
                    'ID' => $certificate->id,
                ]);

                $count ++;
            }
        }

        dd($count . ' Files renamed');
    }

    public function moveParentProgramsToGroup(){
        $programIdsWithChildren = Program::whereHas('children')->pluck('id');

        $programIdsWithUsers = DB::table('program_user')
            ->whereIn('program_id', $programIdsWithChildren)
            ->distinct()
            ->pluck('program_id');

        $withUsersIds = $programIdsWithUsers;
        $withoutUsersIds = $programIdsWithChildren
            ->diff($programIdsWithUsers);


        if(!empty($withoutUsersIds)){
            Program::whereIn('id', $withoutUsersIds)->delete();
        }
        dd('Orphaned parents deleted');
        if(!empty($withUsersIds)){
            $toProcess = Program::with('users')->whereIn('id', $withUsersIds)->get();
            
            foreach($toProcess as $program){
                if($program->users->count() < 1){
                    $programs = $program->children->pluck('id');
    
                    $validated = [
                        'p_name'            => $program->p_name,
                        'p_abbr'            => $program->p_abbr,
                        'p_amount'          => $program->p_amount,
                        'e_amount'          => $program->e_amount,
                        'p_start'           => $program->p_start,
                        'p_end'             => $program->p_end,
                        'status'            => $program->status,
                        'early_bird_status' => $program->early_bird_status,
                        'currencies'        => $program->currencies,
                        'haspartpayment'    => $program->haspartpayment,
                        'image'             => $program->image,
                    ];
    
                    $group = Group::create($validated);
                    $group->programs()->attach($programs);
                    dd($program);
                    $program->delete();
                }
            }
        }

        return 'All done';
    }

    public function moveProgramUserToTempTransactions()
    {
        TempTransaction::truncate();

        DB::beginTransaction();

        // try {
        //     $programUsers = Transaction::with('user')->orderBy('created_at','DESC')->get();

        //     $toInsert = [];

        //     foreach ($programUsers as $record) {
        //         if (!$record->user) {
        //             continue;
        //         }

        //         $t_type = strtoupper($record->t_type) === 'PAYSTACK' ? 'Online' : 'Transfer';
        //         $payment_mode = strtoupper($record->t_type) === 'PAYSTACK' ? 1 : 0;

        //         $record->transid = $record->transid ?? 'BT-' . rand(11111111, 99999999);

        //         $toInsert[] = [
        //             'id'              => $record->id,
        //             'email'           => $record->user->email,
        //             'type'            => $record->balance > 0 ? 'part' : 'full',
        //             'program_id'      => $record->program_id,
        //             'coupon_id'       => $record->coupon_id,
        //             'coupon_amount'   => $record->coupon_amount,
        //             'coupon_code'   => $record->coupon_code,
        //             'facilitator_id'  => $record->facilitator,
        //             'amount'          => $record->amount ?? 0,
        //             'transid'         => $record->transid,
        //             'invoice_id'      => $record->invoice_id,
        //             'payment_mode'    => $record->payment_mode ?: $payment_mode,
        //             'preferred_timing' => $record->preferred_timing ?? null,
        //             'name'            => $record->name,
        //             'phone'           => $record->phone,
        //             'location'        => $record->location ?? null,
        //             'training_mode'   => $record->modes ?? null,
        //             'meta'            => $record->meta,
        //             'is_package'      => 0,
        //             'program_ids'     => json_encode([$record->program_id]),
        //             'balance'         => $record->balance ?? 0,
        //             'user_id'         => $record->user_id,
        //             'payload'         => $record->payload,
        //             'currency'        => $record->currency ?? 'NGN',
        //             'currency_symbol' => $record->currency_symbol ?? '₦',
        //             'payment_url'     => $record->payment_url,
        //             'status'          => 'complete',
        //             't_type'          => $t_type,
        //             'exchange_rate'   => $record->exchange_rate ?? '',
        //             'created_at'      => $record->created_at,
        //             'updated_at'      => $record->updated_at,
        //         ];
        //     }

        //     if (!empty($toInsert)) {
        //         foreach (array_chunk($toInsert, 1000) as $chunk) {
        //             TempTransaction::insert($chunk);
        //         }
        //     }
            
        //     DB::commit();
        //     return 'All done';
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return 'Error: ' . $th->getMessage();
        // }
    }

    public function fixTempTransactionsWithoutTransid(){
        DB::table('program_user')
            ->join('temp_transactions', 'program_user.invoice_id', '=', 'temp_transactions.invoice_id')
            ->whereNull('program_user.transid')
            ->update([
                'program_user.transid' => DB::raw('temp_transactions.transid')
            ]);
    }

    public function normalizeProgramIds()
    {
        TempTransaction::chunk(1000, function ($transactions) {
            foreach ($transactions as $transaction) {
                $ids = $transaction->program_ids;

                // If not a valid JSON array, skip
                if (!is_array($ids)) {
                    continue;
                }

                // Cast all to integers
                $normalized = array_map('intval', $ids);
                
                $transaction->program_ids = $normalized;
                $transaction->save();
            }
        });

        return "Normalization complete";
    }

    public function sendErrorNotification(){
        $recurring = app(SystemLogService::class)->getRecurringErrors(20);
        
        if ($recurring->isEmpty()) {
            return $recurring;
        }
        $subject = 'Employme Portal - Recurring System Errors Detected';
        $content = "<p>The following top recurring errors have been detected in the <strong>Employme Portal</strong>:</p>";

        if ($recurring->count()) {
            $content .= "<ol>"; // start ordered list
            foreach ($recurring as $error) {
                $content .= "<li>Error: <strong>{$error->message}</strong> - Occurrences: {$error->total}</li>";
            }
            $content .= "</ol>";
        } else {
            $content .= "<p>No recurring errors detected.</p>";
        }

        $content .= "<p>Please investigate these issues to ensure system stability.</p>";

        Mail::to('davedeloper@gmail.com')->send(new Email($content, 'Developer', $subject));       
        
        return 'Notification sent';
    }
}
