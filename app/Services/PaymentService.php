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

    public static function handleBalancePayment($existingTransaction, $existingTransactionBalance, $pop){
        $transaction = $existingTransaction;
        $isNew = false;

        // Check if there is a balance
        if ($existingTransactionBalance > 0) {
            $isBalancePayment = true;
            $expectedAmount = $existingTransactionBalance;
        } else {
            $isBalancePayment = false;
            $expectedAmount = $existingTransaction->balance;
        }

        $balance = $expectedAmount - $pop->amount;
        
        if ($pop->amount > $expectedAmount) {
            return [
                'status' => false,
                'message' => 'Cannot pay above ' . $expectedAmount
            ];
        }

        $type = $balance > 0 ? 'part' : 'full';

        $existingTransaction->update([
            'type' => $type,
            'amount' => $existingTransaction->amount + $pop->amount,
            'balance' => $balance,
        ]);

        PaymentThread::create([
            'program_id' => $existingTransaction->program_id,
            'user_id' => $existingTransaction->user_id,
            'payment_id' => $existingTransaction->id,
            'transaction_id' => PaymentService::getReference('PYTHRD'),
            't_type' => strtolower($existingTransaction->paymentMode->processor ?? 'TRANSFER'),
            'parent_transaction_id' => $existingTransaction->transid,
            'amount' => $pop->amount,
        ]);

        return [
            'status' => true,
            'message' => 'Balance Payment added succesfully',
        ];
    }
}
