<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Currency;
use App\Models\CouponUser;
use App\Models\PaymentThread;
use App\Models\TempTransaction;
use App\Services\CouponService;
use Illuminate\Support\Facades\DB;


class PaymentService
{
    public static function getModeAmount(array $couponArray): ?float
    {
        $amount  = null;
        $program = json_decode($couponArray['program'] ?? '');
        $type    = $couponArray['type'] ?? null;
        $mode    = $couponArray['mode'] ?? null;

        if (isset($program->show_modes) && $program->show_modes === 'yes') {
            $modes = json_decode($program->modes ?? '');

            if (!empty($modes) && isset($modes->$mode)) {
                if ($type === 'full') {
                    $amount = (float) $modes->$mode;
                } elseif ($type === 'part') {
                    $amount = (float) $modes->$mode / 2;
                }
            }
        }

        return $amount;
    }

    public static function applyCoupon(array $couponArray): ?array
    {
        $code = $couponArray['code'] ?? null;
        if(!$code || in_array($couponArray['type'], ['part', 'earlybird'])){
            return null;
        }
        
        $couponValue = self::getCouponValue($couponArray);
        
        if (!is_array($couponValue) || empty($couponValue['status'])) {
            return null;
        }

        return self::getCouponUsage($couponArray);
    }

    public static function getCouponValue(array $couponArray): ?array{
        $code       = $couponArray['code'] ?? null;
        $programId  = $couponArray['program_id'] ?? null;
        $isPackage  = $couponArray['isPackage'] ?? false;
        $adminId    = $couponArray['admin_id'] ?? null;

        // Validate inputs
        if (!$code) {
            return null;
        }

        // If from admin, assume raw code and return as-is
        if (!empty($adminId)) {
            return [
                'status' => true,
                'amount' => 0,
                'id'     => null,
                'code'   => $code,
            ];
        }

        // Query based on package vs program
        $query = Coupon::where('code', $code);

        if ($isPackage) {
            $query->where('group_id', $programId);
        } else {
            $query->where('program_id', $programId);
        }

        $coupon = $query->first();

        if (!$coupon) {
            return null;
        }

        return [
            'status' => true,
            'amount' => $coupon->amount,
            'id'     => $coupon->id,
            'code'   => $coupon->code,
        ];
    }

    public static function getCouponUsage(array $couponArray): ?array
    {
        $code      = $couponArray['code'] ?? null;
        $email     = $couponArray['email'] ?? null;
        $amount    = $couponArray['amount'] ?? 0;
        $isPackage = $couponArray['isPackage'] ?? false;

        // Basic validations
        if (!$code || !$email || $amount <= 0) {
            return null;
        }

        // Get coupon from DB
        $coupon = !empty($couponArray['admin_id'])
            ? (object)[
                'id'     => null,
                'code'   => $code,
                'amount' => 0,
                'type'   => 'fixed'
            ]
            : Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->id) {
            return null;
        }

        // Check prior usage
        $existingUsage = CouponUser::where('coupon_id', $coupon->id)
            ->where('email', $email)
            ->first();

        if ($existingUsage && $existingUsage->status == 1) {
            return null;
        }

        // Create usage if not yet used
        if (!$existingUsage) {
            CouponUser::create([
                'email'      => $email,
                'coupon_id'  => $coupon->id,
                'status'     => 0,
                'program_id' => $couponArray['program_id'],
            ]);
        }

        // Calculate discount amount
        $discount = 0;
        
        if ($coupon->type === 'percentage') {
            $discount = round(($coupon->amount / 100) * $amount, 2);
        } elseif ($coupon->type === 'fixed') {
            $discount = min($coupon->amount, $amount); // prevent negative totals
        }

        $grandTotal = max(0, $amount - $discount);
        
        return [
            'status'      => true,
            'id'          => $coupon->id,
            'code'        => $coupon->code,
            'type'        => $coupon->type,
            'amount'      => $discount,
            'grand_total' => $grandTotal,
        ];
    }

    public static function getReference($prefix=null){
        date_default_timezone_set("Africa/Lagos");
        return ($prefix ? $prefix. '-' : '') . date('YmdHi') . '-' . rand(11111111, 99999999);
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

    public static function logTransaction($transactionArray)
    {
        try {
            $transaction = TempTransaction::create([
                'email' => $transactionArray['email'],
                'type' => $transactionArray['type'] ?? null,
                'payment_type' => $transactionArray['payment_type'] ?? ($transactionArray['type'] ?? null),
                'program_id' => $transactionArray['program_id'],
                'coupon_id' =>  $transactionArray['coupon_id'],
                'facilitator_id' => $transactionArray['facilitator'] ?? null,
                'amount' =>  $transactionArray['amount'],
                'discount' => $transactionArray['discount'] ?? null,
                'transid' =>  $transactionArray['transid'],
                'invoice_id' =>  $transactionArray['invoice_id'],
                'payment_mode' => $transactionArray['payment_mode'] ?? null,
                'preferred_timing' => $transactionArray['preferred_timing'] ?? null,
                'name' => $transactionArray['name'],
                'phone' => $transactionArray['phone'],
                'location' => $transactionArray['location'] ?? null,
                'training_mode' => $transactionArray['modes'] ?? null,
                'meta' => $transactionArray['meta'],
                'is_package' => $transactionArray['is_package'],
                'program_ids' => $transactionArray['program_ids']?? null,
                'status' => $transactionArray['status'],
                't_type' => $transactionArray['t_type'],
                'balance' => $transactionArray['balance'] ?? 0,

                'currency' => $transactionArray['currency'],
                'currency_symbol' => $transactionArray['currency_symbol'],
                'exchange_rate' => $transactionArray['exchange_rate'] ?? \Session::get('exchange_rate') ?? 1,
                'remarks' => $transactionArray['remarks'] ?? null,

                "coupon_code" => $transactionArray['coupon_code'] ?? null,
                "coupon_amount" =>  $transactionArray['coupon_amount'] ?? null,
                'expected_amount' => $transactionArray['expected_amount'] ?? null,
            ]);

            return  $transaction;
        } catch (\Throwable $th) {
            return $th->getMessage() . 'Line: ' . $th->getLine();
        }
    }

    public static function getConvertedCurrency($transaction){
        $string = '';
        $array = [];
        $amountToUse = $transaction->amount;
        $training = $transaction->is_package ? $transaction->group : $transaction->program;
        
        if (!empty($training->currencies) && is_array($training->currencies)) {
            $customAmounts = collect($training->currencies)->mapWithKeys(function ($c) {
                return [intval($c['id']) => $c['amount'] ?? null];
            })->all();

            $currencyIds = array_keys($customAmounts);

            $allCurrencies = Currency::where('status', 1)
                ->whereIn('id', $currencyIds)
                ->get();

            foreach ($allCurrencies as $cur) {
                $currencyId = $cur->id;

                if (isset($customAmounts[$currencyId]) && $customAmounts[$currencyId] !== null) {
                    $converted = $customAmounts[$currencyId] * $amountToUse;
                } else {
                    $converted = $cur->conversion_rate * $amountToUse;
                }

                $string .= ' <span style="color:black">|</span> <strong>'
                    . $cur->symbol . '</strong>'
                    . number_format($converted, 0);

                $key = $cur->country_name ?: ($cur->code ?? $cur->id);

                $array[$key] = [
                    'symbol' => $cur->symbol,
                    'name' => $cur->name,
                    'amount' => number_format($converted, 0)
                ];
            }
        }

        return [
            'string' => $string,
            'array' => $array
        ];
    }

    public static function confirmProgramAmount($transaction, $type)
    {
        if ($transaction->is_package) {
            $training = Group::where('id', $transaction->program_id)->first();
        } else {
            $training = Program::where('id', $transaction->program_id)->first();
        }
        
        return $training->$type;
    }

    public static function createUserAndAttachPrograms($transaction)
    {
        $existingUser = User::where('email', $transaction->email)->first();
        
        if ($existingUser) {
            $user = $existingUser;
        } else {
            $user = User::updateOrCreate(['email' => $transaction->email], [
                'name' => $transaction->name ?? 'N/A',
                'phone' => $transaction->phone ?? 'N/A',
                'password' => bcrypt('12345'),
                'roles' => 'Student',
            ]);
        }
        
        // Update user details
        $user->name = $transaction->name ?? 'N/A';
        $user->staffID = $transaction->meta['staffID'] ?? null;
        $user->metadata = $transaction->metadata ?? null;
        $user->phone = $transaction->phone;
        $user->save();

        $data = [
            'transid' => $transaction->transid,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $programIds = $transaction->is_package ? $transaction->program_ids : [$transaction->program_id];

        $dataToSync = [];
        
        foreach ($programIds as $programId) {
            $dataToSync[$programId] = $data;
        }
        
        $user->programs()->syncWithoutDetaching($dataToSync);
        
        $transaction->update([
            'user_id' => $user->id
        ]);
        
        return $user;
    }

    // public static function getExistingTransactionAndBalance($pop)
    // {
    //     $query = TempTransaction::query();

    //     if (isset($pop->user->id)) {
    //         $query->where('user_id', $pop->user->id);
    //     } else {
    //         $query->where('email', $pop->email);
    //     }
        
    //     if ($pop->is_package) {
    //         $query->where('program_id', $pop->group_id)
    //             ->where('is_package', 1);
    //     } else {
    //         $query->where('program_id', $pop->program_id)
    //             ->where('is_package', 0);
    //     }

    //     $existingTransaction = $query->first();
    //     dd($existingTransaction);
    //     return [
    //         'status' => true,
    //         'balance' => $existingTransaction->balance ?? 0,
    //         'transaction' => $existingTransaction,
    //         'message' => 'Existing Transaction',
    //     ];
    // }


    public static function handleBalancePayment($existingTransaction, $existingTransactionBalance, $data){
        $isNew = false;

        $amount = $data['amount'];
        $t_type = $data['t_type'];
        $selectedPaymentType = $data['payment_type'] ?? null;
        $amountToUse = $data['amount_to_use'] ?? null;

        // Check if there is a balance
        if ($selectedPaymentType === 'earlybird' && !is_null($amountToUse)) {
            $isBalancePayment = false;
            $expectedAmount = (float) $amountToUse;
        } elseif ($existingTransactionBalance > 0) {
            $isBalancePayment = true;
            $expectedAmount = $existingTransactionBalance;
        } else {
            $isBalancePayment = false;
            $expectedAmount = $existingTransaction->balance;
        }

        $balance = $expectedAmount - $amount;
        
        if ($amount > $expectedAmount) {
            return [
                'status' => false,
                'message' => 'Cannot pay above ' . $expectedAmount
            ];
        }

        $type = in_array($selectedPaymentType, ['full', 'part', 'earlybird'], true)
            ? $selectedPaymentType
            : ($balance > 0 ? 'part' : 'full');

        if ($selectedPaymentType === 'earlybird') {
            $type = 'earlybird';
        }
        
        $existingTransaction->update([
            't_type' => $t_type,
            'type' => $type,
            'payment_type' => $type,
            'amount' => $existingTransaction->amount + $amount,
            'balance' => $balance,
        ]);

        PaymentThread::create([
            'program_id' => $existingTransaction->program_id,
            'user_id' => $existingTransaction->user_id,
            'payment_id' => $existingTransaction->id,
            'transaction_id' => PaymentService::getReference('PYTHRD'),
            't_type' => strtolower($existingTransaction->paymentMode->processor ?? $t_type),
            'parent_transaction_id' => $existingTransaction->transid,
            'amount' => $amount,
        ]);

        return [
            'status' => true,
            'message' => 'Balance Payment added succesfully',
        ];
    }

    public static function completePayment($transaction){
        $balance = $transaction->balance;
        // Compare
        if ($transaction->type == 'full') {
            $payment_type = 'Full';
            $message = 'Full payment';
            $coupon_applied = $transaction->coupon ?? NULL;
            $paymentStatus =  1;
        } elseif ($transaction->type == 'part') {
            $payment_type = 'Part';
            $message = 'Part payment';
            $coupon_applied = $transaction->coupon ?? NULL;
            $paymentStatus =  0;
        } elseif ($transaction->type == 'earlybird') {
            $payment_type = 'Earlybird';
            $message = 'Earlybird payment';
            $paymentStatus =  1;
        } elseif ($transaction->type == 'balance') {
        }

        try {
            DB::beginTransaction();

            self::createUserAndAttachPrograms($transaction);
            $transaction = $transaction->fresh();

            PaymentThread::create([
                'program_id'   => $transaction->program_id,
                'admin_id'      => auth()->guard('admin')->user()->id ?? null,
                'user_id'      => $transaction->user_id,
                'payment_id'   => $transaction->id,
                'transaction_id' => self::getReference('PYTHRD'),
                't_type'       => strtolower($transaction->t_type),
                'parent_transaction_id' => $transaction->transid,
                'amount'       => $transaction->amount,
            ]);

            if (!empty($transaction->coupon_id)) {
                $couponTransaction = CouponService::getCouponTransactionFromTransaction($transaction);
                CouponService::completeCoupon($couponTransaction);
            }
            $transaction->update([
                'status' => 'complete',
            ]);

            DB::commit();

            return [
                'status' => true,
                'transaction' => $transaction,
                'paymentStatus' => $paymentStatus,
                'message' => $message,
                'payment_type' => $payment_type,
                'coupon_applied' => $coupon_applied ?? null,
                'balance' => $balance
            ];

        } catch (\Throwable $th) {
            DB::rollBack();
            $rand = rand(1111, 9999);
            logger()->info(['Payment Error: ' . $rand => $th->getMessage()]);

            return [
                'status' => false,
                'message' => 'An error occured, please contact Support with this error code: ' . $rand,
            ];
        }
    }

    public static function calculateCoupon($temp, $training, $mainProgram)
    {
        $couponData = [
            'coupon_amount' => 0,
            'total_due' => 0,
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
                'total_due' => min(round($coupon_amount, 2), $training->p_amount),
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
                    'total_due' => min(round($coupon_amount, 2), $training->p_amount),
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

    // public static function getExpectedAmountDetails($temp, $program, $couponData = null)
    // {
    //     $couponAmount = $couponData['total_due'] ?? 0;
    //     $programAmount = $program->p_amount;
    //     $trainingMode = $temp->training_mode;
    //     $type = $temp->type;

    //     // Apply mode-based pricing if applicable
    //     if (!empty($trainingMode) && ($program->show_modes ?? '') === 'yes' && !empty($program->modes)) {
    //         $modes = json_decode($program->modes, true);
    //         if (!empty($modes[$trainingMode])) {
    //             $programAmount = $modes[$trainingMode];
    //         }
    //     }

    //     $totalAmount = $programAmount;
    //     $balance = 0;
    //     $message = 'Full payment';
    //     $paymentStatus = 1;

    //     switch ($type) {
    //         case 'full':
    //             $totalAmount = ceil($programAmount - $couponAmount);
    //             break;

    //         case 'part':
    //             $totalAmount = ceil($programAmount / 2);
    //             $message = 'Part payment';
    //             $balance = $totalAmount - $temp->amount;
    //             break;

    //         case 'earlybird':
    //             $totalAmount = ceil($program->e_amount - $couponAmount);
    //             break;

    //         default:
    //             $type = 'full';
    //             break;
    //     }

    //     // Cap to full program amount if over-calculated
    //     $totalAmount = min($totalAmount, $programAmount);
    //     $balance = max(0, $balance);

    //     return [
    //         'amount_paid' => $totalAmount,
    //         'expected_amount' => $totalAmount,
    //         'type' => $type,
    //         'coupon_data' => $couponData,
    //         'message' => $message,
    //         'payment_status' => $paymentStatus,
    //         'balance' => $balance,
    //     ];
    // }

    // public static function applyProgramModeToAmount($program, $trainingMode=null, $amount_to_use=null)
    // {
    //     if(!empty($amount_to_use)){
    //         return [
    //             'status' => true,
    //             'total_due' => $amount_to_use
    //         ];
    //     }

    //     // Apply mode-based pricing if applicable
    //     if (!empty($trainingMode) && ($program->show_modes ?? '') === 'yes' && !empty($program->modes)) {
    //         $modes = $program->modes;
    //         if (!empty($modes[$trainingMode])) {
    //             $programAmount = $modes[$trainingMode];
    //         }
    //     }else{
    //         $programAmount = $program->early_bird_status ? $program->e_amount : $program->p_amount;
    //     }

    //     return [
    //         'status' => true,
    //         'total_due' => $programAmount,
    //         'training_mode' => $trainingMode
    //     ];
    // }
    public static function calculatePaymentBreakdownonhold($amountPaid, $type, $program, $trainingMode = null)
    {
        $programAmount = $program->p_amount;

        // Apply mode-based pricing if applicable
        if (!empty($trainingMode) && ($program->show_modes ?? '') === 'yes' && !empty($program->modes)) {
            $modes = $program->modes;
            if (!empty($modes[$trainingMode])) {
                $programAmount = $modes[$trainingMode];
            }
        }

        $expectedAmount = $programAmount;
        $message = 'Full payment';
        $type = strtolower($type);

        switch ($type) {
            case 'part':
                $expectedAmount = ceil($programAmount / 2);
                $message = 'Part payment';
                break;

            case 'earlybird':
                $expectedAmount = ceil($program->e_amount);
                $message = 'Early Bird payment';
                break;

            case 'full':
            default:
                $expectedAmount = ceil($programAmount);
                $type = 'full';
                break;
        }

        // Calculate balance properly
        $balance = max(0, $programAmount - $amountPaid);

        // Payment status: 1 = fully paid, 0 = not yet
        $paymentStatus = $balance > 0 ? 0 : 1;
        dd([
            'amount_paid'     => $amountPaid,
            'expected_amount' => $expectedAmount,
            'program_amount'  => $programAmount,
            'type'            => $type,
            'message'         => $message,
            'payment_status'  => $paymentStatus,
            'balance'         => $balance,
        ]);
        return [
            'amount_paid'     => $amountPaid,
            'expected_amount' => $expectedAmount,
            'program_amount'  => $programAmount,
            'type'            => $type,
            'message'         => $message,
            'payment_status'  => $paymentStatus,
            'balance'         => $balance,
        ];
    }

    // public static function calculatePaymentBreakdown($program, $type, $amountPaid, $trainingMode = null, $amount_to_use = null)
    // {
    //     $hasModes = ($program->show_modes ?? '') === 'yes' && !empty($program->modes);
    //     $modeAmount = ($hasModes && $trainingMode && isset($program->modes[$trainingMode]))
    //         ? (float) $program->modes[$trainingMode]
    //         : null;

    //     // Full (regular) price to use if no override
    //     $regularPrice = $modeAmount ?? (float) ($program->p_amount ?? 0);
    //     // Early-bird price (if applicable)
    //     $earlyBirdPrice = (float) ($program->e_amount ?? $regularPrice);

    //     // If an explicit override is provided, it becomes the “full” price baseline
    //     // $fullPrice = $amount_to_use !== null ? (float) $amount_to_use
    //         // : ($program->early_bird_status ? $earlyBirdPrice : $regularPrice);

    //     $type = strtolower((string) $type);

    //     // Determine how much is due *now* for this payment type
    //     switch ($type) {
    //         case 'earlybird':
    //             // Early-bird means paying the early-bird total now
    //             $dueNow = (float) ceil($amount_to_use !== null ? $amount_to_use : $earlyBirdPrice);
    //             $message = 'Early Bird payment';
    //             break;

    //         case 'part':
    //             // 50% installment (ceil so we don’t undercharge due to decimals)
    //             $dueNow = (float) ceil($regularPrice / 2);
    //             $message = 'Part payment (50%)';
    //             break;

    //         case 'full':
    //         default:
    //             $type = 'full';
    //             $dueNow = (float) ceil($regularPrice);
    //             $message = 'Full payment';
    //             break;
    //     }

    //     $remainingTotal = max(0.0, (float) ceil($dueNow) - (float) $amountPaid);

    //     $paymentStatus = $remainingTotal <= 0 ? 1 : 0;

    //     return [
    //         'status'           => true,
    //         'amount_paid'      => (float) $amountPaid,        // total paid so far (include current)
    //         'total_due'  => (float) ceil($dueNow),   // canonical full amount for this purchase
    //         'type'             => $type,
    //         'message'          => $message,
    //         'payment_status'   => $paymentStatus,             // 1 only if fully settled
    //         'balance'          => (float) $remainingTotal,    // remaining against FULL price
    //         'training_mode'    => $trainingMode,
    //     ];
    // }
    public static function calculatePaymentBreakdown($program, $type, $amountPaid, $trainingMode = null, $amount_to_use = null)
    {
        $hasModes = ($program->show_modes ?? '') === 'yes' && !empty($program->modes);
        $modeAmount = ($hasModes && $trainingMode && isset($program->modes[$trainingMode]))
            ? (float) $program->modes[$trainingMode]
            : null;

        // Full (regular) price to use if no override
        $regularPrice = $modeAmount ?? (float) ($program->p_amount ?? 0);
        // Early-bird price (if applicable)
        $earlyBirdPrice = (float) ($program->e_amount ?? $regularPrice);

        $type = strtolower((string) $type);

        // Determine total amount for this payment type
        switch ($type) {
            case 'earlybird':
                $totalDue = (float) ceil($amount_to_use !== null ? $amount_to_use : $earlyBirdPrice);
                $dueNow   = $totalDue; // early bird is pay all now
                $message  = 'Early Bird payment';
                break;

            case 'part':
                $totalDue = (float) ceil($regularPrice);      // full program cost
                $dueNow   = (float) ceil($regularPrice / 2);  // installment due now
                $message  = 'Part payment (50%)';
                break;

            case 'full':
            default:
                $type     = 'full';
                $totalDue = (float) ceil($regularPrice);
                $dueNow   = $totalDue;
                $message  = 'Full payment';
                break;
        }

        $remainingBalance = max(0.0, $totalDue - (float) $amountPaid);
        
        return [
            'status'           => true,
            'amount_paid'      => (float) $amountPaid,        // already paid
            'due_now'          => (float) $dueNow,            // what’s required at this step
            'total_due'        => (float) $totalDue,          // canonical program cost
            'balance'          => (float) $remainingBalance,  // how much remains unpaid
            'type'             => $type,
            'message'          => $message,
            'payment_status'   => $remainingBalance <= 0 ? 1 : 0,
            'training_mode'    => $trainingMode,
        ];
    }


    public static function adminAddNewParticipant($prepareData){

        $data = $prepareData['data'];
        $program = $prepareData['program'];
        $participant = $prepareData['participant'];
        $amount_to_use = $prepareData['amount_to_use'] ?? null;
        $isPackage = $prepareData['isPackage'];
        $send_email = $prepareData['send_email'];
        $remarks = $prepareData['remarks'] ?? null;
        $couponCheck = $prepareData['couponCheck'];
        $transaction_status = $prepareData['transaction_status'] ?? 'initiated';
        $payment_mode = $prepareData['payment_mode'];
        $trainingMode = $prepareData['trainingMode'] ?? null;
        $payment_type = $prepareData['payment_type'];
        $amountPaid = $prepareData['amountPaid'];

        $t_type = $prepareData['t_type'];
        $transid = $prepareData['transid'];
        $invoiceId = $prepareData['invoiceId'];
        
        try {
            DB::beginTransaction();

            $user = User::where('email', $participant['email'])->first();
            
            $newTrainings = $data['programIds'];

            $user_programs = $user
                ? DB::table('program_user')->where('user_id', $user->id)->pluck('program_id')->toArray()
                : [];
            
            $brandNewTrainings = array_diff($newTrainings, $user_programs);
            
            if (!empty($brandNewTrainings)) {
                if (!$program) {
                    return [
                        'status' => false,
                        'message' => 'Invalid Program',
                    ];
                };
                
                $calculateAmount = self::calculatePaymentBreakdown($program, $payment_type, $amountPaid, $trainingMode, $amount_to_use);
                $computedAmount = $calculateAmount['total_due'];
                
                if ($couponCheck) {
                    // Apply coupon to amount
                    $couponData = CouponService::getCouponData($payment_type, $couponCheck, $isPackage, $computedAmount, $program, $participant['email']);
                    
                    if ($couponData['status']) {
                        if($isPackage){
                            $couponData['group_id'] = $program->id;
                        }else{
                            $couponData['program_id'] = $program->id;
                        }

                        $couponData['email'] = $participant['email'];
                        $couponData['transactionId'] = $transid;
                        $couponData['isPackage'] = $isPackage;

                        $computedAmount = $couponData['total_due'] ?? $computedAmount;
                        $couponTransaction = CouponService::initiateCoupon($couponData);
                    }
                }

                $balance = $computedAmount - $amountPaid + ($couponData['discount'] ?? 0);

                $real_type = $payment_type === 'earlybird'
                    ? 'earlybird'
                    : ($balance > 0 ? 'part' : $payment_type);
                $transactionArray = [
                    'email'             => $user?->email ?? $participant['email'],
                    'type'              => $real_type,
                    'payment_type'      => $payment_type,
                    'program_id'        => $program->id,
                    'coupon_id'         => isset($couponData) && $couponData['status'] == 1 ? $couponData['coupon_id'] : null,
                    'facilitator_id'    => null,
                    'expected_amount' => $calculateAmount['total_due'] ?? null,
                    'amount'            => $amountPaid ?? $computedAmount,
                    "discount"          => $couponData['discount'] ?? 0,
                    'transid'           => $transid,
                    'invoice_id'        => $invoiceId,
                    'payment_mode'      => $payment_mode,
                    'preferred_timing'  => null,
                    'name'              => $user?->name ?? $participant['name'],
                    'phone'             => $user?->phone ?? $participant['phone'],
                    'location'          => null,
                    'training_mode'     => null,
                    'meta'              => null,
                    'is_package'        => $isPackage ?? 0,
                    'status'            => $transaction_status,
                    'balance'           => $balance,
                    't_type'            => $t_type,
                    'program_ids'       => $brandNewTrainings,
                    'currency'          => "NGN",
                    'currency_symbol'   => "₦",
                    "coupon_code"       => isset($couponData) && $couponData['status'] == 1 ? $couponData['code'] : null,
                    'remarks'           => $remarks,
                ];
                
                $transaction = self::logTransaction($transactionArray);
                
                self::createUserAndAttachPrograms($transaction);
                $transaction = $transaction->fresh();

                PaymentThread::create([
                    'program_id'   => $transaction->program_id,
                    'admin_id'      => auth()->guard('admin')->user()->id ?? null,
                    'user_id'      => $transaction->user_id,
                    'payment_id'   => $transaction->id,
                    'transaction_id' => self::getReference('PYTHRD'),
                    't_type'       => strtolower($transaction->t_type),
                    'parent_transaction_id' => $transaction->transid,
                    'amount'       => $transaction->amount,
                ]);

                if (!empty($transaction->coupon_id) && isset($couponTransaction->id)) {
                    CouponService::completeCoupon($couponTransaction);
                }
            }else{
                return [
                    'status' => false,
                    'message' => 'No new trainings or trainings already added'
                ];
            }
            
            if ($send_email == 'yes') {
                $data['balance'] = $balance;
                $data['programs'] = $transaction->allPrograms()->toArray();
                $data['payment_type'] = $transaction->type;

                $data['type'] = $real_type;
                $data['message'] = $balance > 0 ? 'Part payment' : 'Full payment';
                $data['paymentStatus'] = $balance > 0 ? 0 : 1;

                $data['currency'] = $transaction->currency;
                $data['currency_symbol'] = $transaction->currency_symbol;
                $data['exchange_rate'] = $transaction->exchange_rate;
                $data['type'] = 'initial';
                $data['t_type'] = $t_type;
                $data['amount'] = $transaction->amount;
                $data['email'] = $participant['email'];
                $data['programName'] = $program->p_name;
                $data['programAbbr'] = $program->p_abbr;
                $data['name'] = $transaction->name;
                $data['transaction'] = $transaction;
                $data['program'] = $program;

                $controller = new Controller();
                $controller->sendWelcomeMail($data);
            }
            
            DB::commit();

            return [
                'status' => true,
                'message' => 'Participant Added Successfully',
                'transaction' => $transaction,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage(). ' Line: '.$e->getLine(). ' File: '.$e->getFile());

            return [
                'status' => false,
                'message' => 'Error Occured'
            ];
        }
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

    public static function addParticipant($program, $data){
        $transid = 'AD-IMPORT' . rand(11111111, 9999999);
        $invoiceId = self::getInvoiceId();


        $transactionArray = [
            'email' => $data['email'],
            'type' => $data['type'],
            'program_id' => $program->id,
            'coupon_id' =>  null,
            'facilitator_id' => null,
            'amount' =>  $data['amount'],
            'transid' =>  $transid,
            'invoice_id' => $invoiceId,
            'payment_mode' => 0,
            'preferred_timing' => null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'location' => $data['location'] ?? null,
            'training_mode' => null,
            'meta' => $data['metadata'] ?? null,
            'is_package' => $data['is_package'],
            'status' => 'complete',
            'balance' => $data['balance'],
            't_type' => $data['t_type'],
            'program_ids' => $data['programIds'],
            'currency' => $data['currency'],
            'currency_symbol' => $data['currency_symbol'],
            'exchange_rate' => $data['exchange_rate'],
        ];

        $transaction = PaymentService::logTransaction($transactionArray);
        
        $data = self::createUserAndAttachPrograms($transaction);
    }

}
