<?php

namespace App\Services;



class CouponService
{
    public static function getCouponData($payment_type, $coupon, $isPackage, $amount, $program, $email): array
    {
        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid or inactive coupon code.',
                'discount' => 0,
                'final_price' => $program->price,
            ];
        }

        // Check if coupon applies to this program
        if($isPackage){
            $programCheck = $coupon->group_id == $program->id;
        }else{
            $programCheck = $coupon->program_id == $program->id;
        }

        dd($programCheck, $coupon, $program->id);
        if ($coupon->applies_to === 'programs') {
            $allowedPrograms = $coupon->programs()->pluck('id')->toArray(); // assuming many-to-many
            if (!in_array($program->id, $allowedPrograms)) {
                return [
                    'success' => false,
                    'message' => 'Coupon does not apply to this program.',
                    'discount' => 0,
                    'final_price' => $program->price,
                ];
            }
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($coupon->discount_value / 100) * $program->price;
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = min($coupon->discount_value, $program->price);
        }

        $finalPrice = max(0, $program->price - $discount);

        // Log usage (optional)
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $userId,
            'program_id' => $program->id,
            'discount'  => $discount,
        ]);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'discount' => $discount,
            'final_price' => $finalPrice,
        ];
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

    public static function initiateCouponTransaction($transactionArray)
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
                'exchange_rate' => $transactionArray['exchange_rate'] ?? \Session::get('exchange_rate') ?? 1,
                'remarks' => $transactionArray['remarks'] ?? null,
                
            ]);

            return  $transaction;
        } catch (\Throwable $th) {
            return $th->getMessage() . 'Line: ' . $th->getLine();
        }
    }

    // public static function calculateCoupon($temp, $training, $mainProgram)
    // {
    //     $couponData = [
    //         'coupon_amount' => 0,
    //         'computed_amount' => 0,
    //         'coupon_id' => null,
    //         'coupon_code' => null,
    //         'program_id' => $training->id,
    //         'original_program_id' => null,
    //         'created_by' => null,
    //         'coupon_scope' => null,
    //     ];

    //     if ($temp->type !== 'full') {
    //         return $couponData;
    //     }

    //     $coupon_amount = 0;

    //     // Check for training-specific coupon first (override behavior)
    //     $trainingCoupon = Coupon::where('program_id', $training->id)->first();

    //     if ($trainingCoupon) {
    //         if ($trainingCoupon->type === 'percentage') {
    //             $coupon_amount = ($trainingCoupon->amount / 100) * $training->p_amount;
    //         } elseif ($trainingCoupon->type === 'fixed') {
    //             $coupon_amount = $trainingCoupon->amount;
    //         }
    //         return [
    //             'coupon_amount' => round($coupon_amount, 2),
    //             'computed_amount' => min(round($coupon_amount, 2), $training->p_amount),
    //             'coupon_id' => $trainingCoupon->id,
    //             'coupon_code' => $trainingCoupon->code,
    //             'program_id' => $training->id,
    //             'original_program_id' => $trainingCoupon->program_id,
    //             'created_by' => $trainingCoupon->facilitator_id,
    //             'coupon_scope' => 'individual',
    //         ];
    //     }

    //     // If no training coupon, fallback to main program coupon
    //     if (isset($temp->coupon_id)) {
    //         $mainCoupon = Coupon::find($temp->coupon_id);

    //         if ($mainCoupon) {
    //             if ($mainCoupon->type === 'percentage') {
    //                 $coupon_amount = ($mainCoupon->amount / 100) * $training->p_amount;
    //             } elseif ($mainCoupon->type === 'fixed' && $mainProgram->p_amount > 0) {
    //                 $discountPercentage = ($mainCoupon->amount / $mainProgram->p_amount) * 100;
    //                 $coupon_amount = ($discountPercentage / 100) * $training->p_amount;
    //             }

    //             return [
    //                 'coupon_amount' => round($coupon_amount, 2),
    //                 'computed_amount' => min(round($coupon_amount, 2), $training->p_amount),
    //                 'coupon_id' => $mainCoupon->id,
    //                 'coupon_code' => $mainCoupon->code,
    //                 'program_id' => $training->id,
    //                 'original_program_id' => $mainCoupon->program_id,
    //                 'created_by' => $mainCoupon->facilitator_id,
    //                 'coupon_scope' => 'general',
    //             ];
    //         }
    //     }

    //     // Return default if no valid coupon applied
    //     return $couponData;
    // }


}
