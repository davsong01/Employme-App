<?php
namespace App\Services;

use PDF;
use App\Models\User;
use App\Models\Coupon;
use App\Mail\Welcomemail;
use App\Models\CouponUser;
use App\Models\PaymentMode;
use App\Models\Transaction;
use RecursiveArrayIterator;
use App\Models\PaymentThread;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class TransactionService
{
    public static function createOrUpdateParticipant($data){
        if (Auth::check() && empty($data['new'])) {
            $user = resolveAuthUser();
        } else {
            // Check if user exists previously
            $existingUser = User::where(['email' => $data['email']])->first();

            if ($existingUser) {
                $user = $existingUser;
            } else {
                $user = User::updateOrCreate(['email' => $data['email']], [
                    'name' => $data['name'] ?? 'N/A',
                    'password' => $data['password'],
                    'roles' => $data['roles'],
                ]);
            }
        }

        // Update 
        $user->name = $data['name'] ?? 'N/A';
        $user->staffID = $data['staffID'] ?? null;
        $user->metadata = $data['metadata'] ?? null;

        $user->phone = $data['phone'];
        $user->save();
        
        return $user;
    }

    public static function getTransactionDetails($temp, $training, $mainProgram){
        $couponData = self::calculateCoupon($temp, $training, $mainProgram);
        
        $amountDetails = self::getExpectedAmountDetails($temp, $training, $couponData);
        $earnings = self::getEarnings(($temp->amount), $couponData['computed_amount'], $couponData['created_by'], $training, $temp->facilitator_id ?? NULL); // handle this to do once for this program

        self::updateCoupon($couponData, $temp);

        return [
            'amountDetails' => $amountDetails,
            'earnings' => $earnings,
            'couponData' => $couponData,
        ];        
    }

    public static function calculateCoupon($temp, $training, $mainProgram)
    {
        $couponData = [
            'coupon_amount' => 0,
            'computed_amount' => 0,
            'coupon_id' => null,
            'coupon_code' => null,
            'program_id' => $training->id,
            'original_program_id' => null,
            'created_by' => null,
            'coupon_scope' => null,
        ];

        if ($temp->type !== 'full') {
            return $couponData;
        }

        $coupon_amount = 0;

        // Check for training-specific coupon first (override behavior)
        $trainingCoupon = Coupon::where('program_id', $training->id)->first();

        if ($trainingCoupon) {
            if ($trainingCoupon->type === 'percentage') {
                $coupon_amount = ($trainingCoupon->amount / 100) * $training->p_amount;
            } elseif ($trainingCoupon->type === 'fixed') {
                $coupon_amount = $trainingCoupon->amount;
            }
            return [
                'coupon_amount' => round($coupon_amount, 2),
                'computed_amount' => min(round($coupon_amount, 2), $training->p_amount),
                'coupon_id' => $trainingCoupon->id,
                'coupon_code' => $trainingCoupon->code,
                'program_id' => $training->id,
                'original_program_id' => $trainingCoupon->program_id,
                'created_by' => $trainingCoupon->facilitator_id,
                'coupon_scope' => 'individual',
            ];
        }

        // If no training coupon, fallback to main program coupon
        if (isset($temp->coupon_id)) {
            $mainCoupon = Coupon::find($temp->coupon_id);

            if ($mainCoupon) {
                if ($mainCoupon->type === 'percentage') {
                    $coupon_amount = ($mainCoupon->amount / 100) * $training->p_amount;
                } elseif ($mainCoupon->type === 'fixed' && $mainProgram->p_amount > 0) {
                    $discountPercentage = ($mainCoupon->amount / $mainProgram->p_amount) * 100;
                    $coupon_amount = ($discountPercentage / 100) * $training->p_amount;
                }

                return [
                    'coupon_amount' => round($coupon_amount, 2),
                    'computed_amount' => min(round($coupon_amount, 2), $training->p_amount),
                    'coupon_id' => $mainCoupon->id,
                    'coupon_code' => $mainCoupon->code,
                    'program_id' => $training->id,
                    'original_program_id' => $mainCoupon->program_id,
                    'created_by' => $mainCoupon->facilitator_id,
                    'coupon_scope' => 'general',
                ];
            }
        }

        // Return default if no valid coupon applied
        return $couponData;
    }


    public static function prepareTrainingDetails($training, $temp, $transactionDetails)
    {
        $payment_mode = PaymentMode::find($temp->payment_mode);
        $processor = $payment_mode->processor ?? null;

        $data['programFee'] = $training->p_amount;
        $data['programName'] = $training->p_name;
        $data['programAbbr'] = $training->p_abbr;
        $data['bookingForm'] = $training->booking_form;
        $data['program_id'] = $training->id;
        $data['participant_name'] = $temp->name;
        $data['participant_email'] = $temp->email;
        $data['participant_phone'] = $temp->phone;

        $data['t_type'] = strtoupper($processor) ?? 'bank_transfer';
        
        // Create Facilitator details
        if (isset($temp->facilitator_id)) {
            $data['facilitator_id'] = $temp->facilitator_id;
            $data['facilitator_name'] = User::where('id', $temp->facilitator_id)->value('name');
        }

        if (isset($temp->location)) {
            $data['location'] = $temp->location;
        } else $data['location'] = ' ';

        $data['paymentModeDetails'] = [
            'id' => $payment_mode->id ?? 0,
            'type' => $payment_mode->type ?? 'bank_transfer',
            'processor' => $payment_mode->processor ?? 'bank_transfer',
            'currency' => $payment_mode->currency ?? '&#x20A6;',
            'currency_symbol' => $payment_mode->currency_symbol ?? '&#x20A6;',
            'exchange_rate' => $payment_mode->exchange_rate ?? 1,
        ];
        
        $data['roles'] = "Student";
        $data['transid'] = $temp->transid;
        $data['training_mode'] = $temp->training_mode;
        $data['preferred_timing'] = $temp->preferred_timing;

        $llData = array_merge($data, $transactionDetails);

        return $llData;
    }

    public static function updateCoupon($couponData, $temp)
    {
        $ids = [$couponData['original_program_id'], $couponData['program_id']];
        
        if(!empty($ids) && !empty($couponData['coupon_id'])){
            CouponUser::where('coupon_id', $couponData['coupon_id'])->where('email', $temp->email)->whereIn('program_id', $ids)->update([
                'status' => 1
            ]);

        }

        return;
    }

    
    public static function getExpectedAmountDetails($temp, $program, $couponData = null)
    {
        $couponAmount = $couponData['computed_amount'] ?? 0;
        $programAmount = $program->p_amount;
        $trainingMode = $temp->training_mode;
        $type = $temp->type;

        // Apply mode-based pricing if applicable
        if (!empty($trainingMode) && ($program->show_modes ?? '') === 'yes' && !empty($program->modes)) {
            $modes = json_decode($program->modes, true);
            if (!empty($modes[$trainingMode])) {
                $programAmount = $modes[$trainingMode];
            }
        }

        $totalAmount = $programAmount;
        $balance = 0;
        $message = 'Full payment';
        $paymentStatus = 1;

        switch ($type) {
            case 'full':
                $totalAmount = ceil($programAmount - $couponAmount);
                break;

            case 'part':
                $totalAmount = ceil($programAmount / 2);
                $message = 'Part payment';
                $balance = $totalAmount - $temp->amount;
                break;

            case 'earlybird':
                $totalAmount = ceil($program->e_amount - $couponAmount);
                break;

            default:
                $type = 'full';
                break;
        }

        // Cap to full program amount if over-calculated
        $totalAmount = min($totalAmount, $programAmount);
        $balance = max(0, $balance);

        return [
            'amount_paid' => $totalAmount,
            'expected_amount' => $totalAmount,
            'type' => $type,
            'coupon_data' => $couponData,
            'message' => $message,
            'payment_status' => $paymentStatus,
            'balance' => $balance,
        ];
    }


    public static function getEarnings($amount, $coupon, $createdBy, $program, $programFacilitator = NULL)
    {
        // Admin created coupon
        if (is_null($coupon)) {
            return [
                'facilitator' => $data['facilitator_percent'] ?? 0,
                'admin' => $data['admin_percent'] ?? 0,
                'tech' => $data['tech_percent'] ?? 0,
                'faculty' =>  $data['faculty_percent'] ?? 0,
                'other' => $data['other_percent'] ?? 0,
            ];
        }

        if ($coupon > 0) {
            $coupon = $coupon;
        } else {
            $coupon = 0;
        }

        if ($createdBy == 0) {
            $toShare = $amount - $coupon;
        } else {
            $toShare = $amount;
        }

        $data['tech_percent'] = ($toShare * $program->tech_percent) / 100;
        $data['faculty_percent'] = ($toShare * $program->faculty_percent) / 100;
        $data['admin_percent'] = ($toShare * $program->admin_percent) / 100;
        $data['other_percent'] = ($toShare * $program->other_percent) / 100;

        if (isset($programFacilitator)) {
            if ($createdBy == $programFacilitator) {
                $data['facilitator_percent'] = (($toShare * $program->facilitator_percent) / 100) - $coupon;
            } else {
                $data['facilitator_percent'] = (($toShare * $program->facilitator_percent) / 100);
            }
        } else {
            $data['facilitator_percent'] = 0;
        }

        return [
            'facilitator' => $data['facilitator_percent'] ?? 0,
            'admin' => $data['admin_percent'] ?? 0,
            'tech' => $data['tech_percent'] ?? 0,
            'faculty' =>  $data['faculty_percent'] ?? 0,
            'other' => $data['other_percent'] ?? 0,
        ];
    }


    public static function assignTrainingToUser($allData, $user){
        $data['admin_earning'] = $allData['earnings']['admin'] ?? NULL;
        $data['facilitator_earning'] = $allData['earnings']['facilitator'] ?? NULL;
        $data['tech_earning'] = $allData['earnings']['tech'] ?? NULL;
        $data['faculty_earning'] = $allData['earnings']['faculty'] ?? NULL;
        $data['other_earning'] = $allData['earnings']['other'] ?? NULL;

        $allData['invoice_d'] = self::getInvoiceId($user->id);
        
        //If program id is not in array of user program, attach program
        $userPrograms = Transaction::where('user_id', $user->id)->where('program_id', $allData['program_id'])->count();
    
        // dd([
        //     'created_at' =>  now(),
        //     'amount' => $allData['amountDetails']['amount_paid'],
        //     't_type' => $allData['t_type'] ?? null,
        //     't_location' => $allData['location'] ?? null,
        //     'training_mode' => $allData['training_mode'] ?? null,
        //     'transid' => $allData['transid'],
        //     'paymenttype' => $allData['amountDetails']['type'] ?? null,
        //     'paymentStatus' => $allData['amountDetails']['payment_status'],
        //     'balance' => $allData['amountDetails']['balance'],
        //     'invoice_id' => $allData['invoice_id'],

        //     'facilitator_id' => $allData['facilitator_id'] ?? NULL,

        //     'coupon_amount' => $allData['couponData']['computed_amount'] ?? NULL,
        //     'coupon_id' => $allData['couponData']['coupon_id']  ?? NULL,
        //     'coupon_code' => $allData['couponData']['coupon_code'] ?? NULL,
        //     'admin_earning' => $allData['earnings']['admin'] ?? NULL,
        //     'facilitator_earning' => $allData['earnings']['facilitator'] ?? 0,
        //     'tech_earning' => $allData['earnings']['tech'] ?? 0,
        //     'faculty_earning' => $allData['earnings']['faculty'] ?? 0,
        //     'other_earning' => $allData['earnings']['other'] ?? 0,
        //     'currency' =>  \Session::get('currency'),
        //     'payload' => $allData['payload'] ?? null,
        //     'payment_mode' => $allData['paymentModeDetails']['id'],
        //     'preferred_timing' => $allData['preferred_timing'] ?? null,
        // ]);
        if ($userPrograms < 1) {
            // Attach program
            $user->programs()->attach($allData['program_id'], [
                'created_at' =>  now(),
                'amount' => $allData['amountDetails']['amount_paid'],
                't_type' => $allData['t_type'] ?? null,
                't_location' => $allData['location'] ?? null,
                'training_mode' => $allData['training_mode'] ?? null,
                'transid' => $allData['transid'],
                'paymenttype' => $allData['amountDetails']['type'] ?? null,
                'paymentStatus' => $allData['amountDetails']['payment_status'],
                'balance' => $allData['amountDetails']['balance'],
                'invoice_id' => $allData['invoice_id'],

                'facilitator_id' => $allData['facilitator_id'] ?? NULL,

                'coupon_amount' => $allData['couponData']['computed_amount'] ?? NULL,
                'coupon_id' => $allData['couponData']['coupon_id']  ?? NULL,
                'coupon_code' => $allData['couponData']['coupon_code'] ?? NULL,
                'admin_earning' => $allData['earnings']['admin'] ?? NULL,
                'facilitator_earning' => $allData['earnings']['facilitator'] ?? 0,
                'tech_earning' => $allData['earnings']['tech'] ?? 0,
                'faculty_earning' => $allData['earnings']['faculty'] ?? 0,
                'other_earning' => $allData['earnings']['other'] ?? 0,
                'currency' =>  \Session::get('currency'),
                'payload' => $allData['payload'] ?? null,
                'payment_mode' => $allData['paymentModeDetails']['id'] ?? null,
                'currency_symbol' => $allData['paymentModeDetails']['currency_symbol'] ?? null,
                'preferred_timing' => $allData['preferred_timing'] ?? null,
            ]);
        }
        
        PaymentThread::create([
            'program_id' => $allData['program_id'],
            'user_id' => $user->id,
            'payment_id' => $allData['payment_id'],
            'transaction_id' => self::getReference('PYTHRD'),
            't_type' => $allData['t_type'],
            'parent_transaction_id' => $allData['transid'],
            'amount' => $allData['amountDetails']['amount_paid'],
        ]);
        
        return $allData;
    }

    public static function getReference($prefix){
        date_default_timezone_set("Africa/Lagos");
        return $prefix . '-' . date('YmdHi') . '-' . rand(11111111, 99999999);
    }

    public static function getInvoiceId($id = null)
    {
        date_default_timezone_set("Africa/Lagos");
        if (isset($id) && !empty($id)) {
            $invoice_id = date("YmdHi") . '-' . $id . '-' . rand(10000, 99999);
        } else {
            $invoice_id = date("YmdHi") . '-' . rand(10000, 99999);
        }
        return $invoice_id;
    }


    public static function sendWelcomeMail($data, $pdf = null)
    {
        set_time_limit(360);
        
        return view('emails.receipt', compact('data'));
        $provider = app('App\Http\Controllers\Controller')->emailProvider();

        if ($provider == 'default') {
            if (isset($data['invoice_id'])) {
                $pdf = PDF::loadView('emails.printreceipt', compact('data'));
            } else $pdf = null;

            try {
                if (env('ENT') == 'local') {
                    \Log::info(['email' => $data]);
                } else {
                    $data['subject'] = app('App\Http\Controllers\Controller')->emailContent($data)['subject'];
                    $data['content'] = app('App\Http\Controllers\Controller')->emailContent($data)['content'];

                    Mail::to($data['email'])->send(new Welcomemail($data, $pdf));
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
                // Get error here
                return false;
            }
        } else {
            if (isset($data['invoice_id'])) {
                $pdf = PDF::loadView('emails.printreceipt', compact('data'));
                if (env('ENT') == 'local') {
                    $file = 'receipts/' . $data['invoice_id'] . ".pdf";
                    $filepath = public_path() . '/' . $file;
                } else {
                    $file = base_path() . '/receipts/' . $data['invoice_id'] . ".pdf";
                    $filepath = $file;
                }
                $filename = $data['invoice_id'] . ".pdf";

                file_put_contents($file, $pdf->output());
                $data['type'] = 'initial';
                $data['attachments'] = [
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'file' => $file,
                ];
            }

            if (isset($data['type']) && $data['type'] == 'pop') {
                // $data['attachments'] = $data['pop'];
                $data['attachments'] = [
                    'filename' => $data['realfilename'],
                    'filepath' => $data['pop'],
                    'file' => 'uploads/pop/' . $data['realfilename'],
                ];
            }

            $this->sendEmailWithElastic($data);
        }

        return;
    }
}
