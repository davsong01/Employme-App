<?php

use App\Setting;

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => Setting::select('PAYSTACK_PUBLIC_KEY')->first()->value('PAYSTACK_PUBLIC_KEY'),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => Setting::select('PAYSTACK_SECRET_KEY')->first()->value('PAYSTACK_SECRET_KEY'),

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => Setting::select('PAYSTACK_PAYMENT_URL')->first()->value('PAYSTACK_PAYMENT_URL'),

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => Setting::select('MERCHANT_EMAIL')->first()->value('MERCHANT_EMAIL'),

];
