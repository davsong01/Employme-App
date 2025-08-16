<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\Currency;
use App\Models\CouponUser;
use App\Models\PaymentThread;
use App\Models\TempTransaction;


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

    public static function initiateTransaction($transactionArray)
    {
        try {
            $transaction = TempTransaction::create([
                'email' => $transactionArray['email'],
                'type' => $transactionArray['type'] ?? null,
                'program_id' => $transactionArray['program_id'],
                'coupon_id' =>  $transactionArray['coupon_id'],
                'facilitator_id' => $transactionArray['facilitator'] ?? null,
                'amount' =>  $transactionArray['amount'],
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
        
        foreach ($programIds as $programId) {
            $alreadyHasProgram = $user->programs()->where('program_id', $programId)->exists();

            if (!$alreadyHasProgram) {
                $data['program_id'] = $programId;

                $user->programs()->attach($programId, $data);
            }
        }
        
        $transaction->update([
            'user_id' => $user->id
        ]);

        return;
    }

    public static function getExistingTransactionAndBalance($pop)
    {
        // check if there is a user, if yes, use the user_id, else use the email and program or group as the case may and get the transaction balance if it exist
        $isPackage = $pop->is_package;

        if (isset($pop->user->id)) {
            $existingTransaction = TempTransaction::where('user_id', $pop->user->id);
            if (!$isPackage) {
                $existingTransaction = $existingTransaction->where('program_id', $pop->program_id)->where('is_package', 0);
            } else {
                $existingTransaction = $existingTransaction->where('program_id', $pop->group_id)->where('is_package', 1);
            }
            $existingTransaction = $existingTransaction->first();

            if ($existingTransaction) {
                return [
                    'balance' => $existingTransaction->balance ?? 0,
                    'transaction' => $existingTransaction,
                ];
            }
        } else {
            $existingTransaction = TempTransaction::where('email', $pop->email);
            if (!$isPackage) {
                $existingTransaction = $existingTransaction->where('program_id', $pop->program_id)->where('is_package', 0);
            } else {
                $existingTransaction = $existingTransaction->where('program_id', $pop->group_id)->where('is_package', 1);
            }

            $existingTransaction = $existingTransaction->first();

            if ($existingTransaction) {
                return [
                    'balance' => $existingTransaction->balance ?? 0,
                    'transaction' => $existingTransaction,
                ];
            }
        }

        return [
            'balance' => 0,
            'transaction' => null,
        ];
    }

    public static function handleBalancePayment($existingTransaction, $existingTransactionBalance, $data){
        $isNew = false;

        $amount = $data['amount'];
        $t_type = $data['t_type'];

        // Check if there is a balance
        if ($existingTransactionBalance > 0) {
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

        $type = $balance > 0 ? 'part' : 'full';
        
        $existingTransaction->update([
            't_type' => $t_type,
            'type' => $type,
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


}
