<?php

namespace App\Services;

use App\Models\CouponUser;

class CouponService
{   
    public static function getCouponData($payment_type, $coupon, $isPackage, $amount, $program, $email): array
    {
        if (!$coupon) {
            return [
                'status' => false,
                'message' => 'Invalid or inactive coupon code.',
                'discount' => 0,
                'total_due' => $amount,
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
            ];
        }
        
        if(in_array($payment_type, ['part','earlybird'])){
            return [
                'status' => false,
                'message' => 'Coupon not valid for part payment or earlybird',
                'discount' => 0,
                'total_due' => $amount,
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
            ];
        }

        // Check if coupon applies to this program
        if($isPackage){
            $programCheck = $coupon->group_id == $program->id;
        }else{
            $programCheck = $coupon->program_id == $program->id;
        }

        if(!$programCheck){
            return [
                'status' => false,
                'message' => 'Coupon does not apply to this program.',
                'discount' => 0,
                'total_due' => $amount,
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
            ];
        }

        // check if coupon already applies to user
        $checkApplication = CouponUser::where('email', $email)->where('coupon_id', $coupon->id)->where('status', 1)->count();
        if($checkApplication > 0){
            return [
                'status' => false,
                'message' => 'Coupon already applied.',
                'discount' => 0,
                'total_due' => $amount,
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
            ];
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = ($coupon->amount / 100) * $amount;
        } elseif ($coupon->type === 'fixed') {
            $discount = min($coupon->amount, $amount);
        }

        $finalPrice = max(0, $amount - $discount);

        return [
            'status' => true,
            'message' => 'Coupon valid.',
            'discount' => $discount,
            'total_due' => $finalPrice,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
        ];
    }

    public static function initiateCoupon($data){
        $couponTransaction = null;
        $couponArray = [
            'email'      => $data['email'],
            'coupon_id'  => $data['coupon_id'],
            'status'     => 0,
            'transactionId'     => $data['transactionId'],
        ];

        if($data['isPackage'] == 1){
            $couponArray['group_id'] = $data['group_id'];
        }

        if ($data['isPackage'] == 0) {
            $couponArray['program_id'] = $data['program_id'];
        }
        
        try {
            $couponTransaction = CouponUser::create($couponArray);
        } catch (\Throwable $th) {
        }

        return $couponTransaction;
    }

    public static function getCouponTransactionFromTransaction($transaction){
        return CouponUser::where('transactionId', $transaction->transid)->first();
    }

    public static function completeCoupon($couponTransaction){
        $couponTransaction->update(['status' => 1]);
    }
}
