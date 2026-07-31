<?php
namespace App\Services;

use App\Models\Program;
use App\Models\Certificate;
use App\Models\Transaction;
use App\Models\CertificateStatusLog;

class CertificateService
{
    public function verify($certificate_number)
    {
        $certificate_number = $this->normalizeCertificateNumber($certificate_number);
        $details = $this->checkStatus($certificate_number);
        
        $this->logCertificateVerificationCheck($certificate_number, $details);

        return $details;
    }

    public function normalizeCertificateNumber($certificate_number): ?string
    {
        if ($certificate_number === null) {
            return null;
        }

        $certificate_number = trim((string) $certificate_number);
        $certificate_number = preg_replace('/[^\pL\pN\-]/u', '', $certificate_number);

        return $certificate_number !== '' ? strtoupper($certificate_number) : null;
    }

    public function logCertificateVerificationCheck($certificate_number, $details){
        $log = CertificateStatusLog::create([
            'ip' => request()->getClientIp(),
            'certificate_number' => $this->normalizeCertificateNumber($certificate_number),
            'response' => $details
        ]);
    }

    public function checkStatus($certificate_number){
        $details = [
            'status' => false,
            'message' => 'ERR01: Certificate not Found!',
            'status_code' => 401,
        ];


        if (!$certificate_number) {
            $details = [
                'status' => false,
                'message' => 'ERR02: Certificate number required',
                'status_code' => 412,
            ];

            return $details;
        }

        $certificate_number = $this->normalizeCertificateNumber($certificate_number);

        if (!$certificate_number) {
            return $details;
        }

        $certificate = Certificate::where('certificate_number', $certificate_number)->first();
        
        if (!$certificate) {
            return $details;
        }

        $transaction = Transaction::select('id', 'training_result', 'balance', 'user_id', 'program_id', 'currency_symbol')->where('program_id',  $certificate->program_id)->where('user_id', $certificate->user_id)->first();
        $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult', 'only_certified_should_see_certificate', 'allow_payment_restrictions_for_certificates')->with('scoresettings')->find($certificate->program_id);
        
        if (!$transaction || !$program) {
            return [
                'status' => false,
                'message' => 'ERR04: Associated Training not found!',
                'status_code' => 201,
                'error' => 'Invalid Transaction'
            ];
        }

        // Checks
        // allow_payment_restrictions_for_results
        if ($program->allow_payment_restrictions_for_certificates == 'yes') {
            $userBalance = (float) $transaction->balance;
            if ($userBalance > 0) {
                $details = [
                    'status' => false,
                    'message' => 'ERR03: Certificate is not available at the moment!',
                    'status_code' => 201,
                    'error' => 'Please Pay your balance of ' . $transaction->currency_symbol . number_format($userBalance) . ' in order to view/download your certificate'
                ];

                return $details;
            }
        }

        $certification_status = 'CERTIFIED';

        if ($program->only_certified_should_see_certificate == 'yes') {
            $details = certificationStatusNew($transaction->training_result, $program, resolveAuthUser());
            $certification_status = $details->certification_status ?? NULL;

            if (!$certification_status || $certification_status == 'NOT CERTIFIED') {
                $details = [
                    'status' => false,
                    'message' => 'ERR04: Certificate is not available at the moment!',
                    'status_code' => 201,
                    'error' => 'You must be certified before you can view certificate'
                ];

                return $details;
            }
        }
        
        $details = [
            'status' => true,
            'status_code' => 200,
            'certificate_number' => $certificate->certificate_number,
            'certification_status' => $certification_status == 'CERTIFIED' ? 'VERIFIED' : 'NOT VERIFIED',
            'training' => $certificate->program?->p_name ?? $program->p_name,
            'owner' => $certificate->user?->name ?? 'Unknown',
            'certified_on' => $certificate->created_at,
            'score_obtainable' => $details->scoresettings->passmark ?? 'N/A',
            'score_obtained' => $details->total_score ?? 'N/A',
            'image' => url('/download-certificate/'.$certificate->file),
        ];
        
        return $details;
    }
}
