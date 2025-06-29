<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'payments/*',
        'validate-coupon',
        'admin/certificates-modify',
        'get-mode-payment-types',
        'generate-certificate-preview/*',
        'admin/generate-certificate-preview/*',
        'admin/preview-regenerated-certificate-settings'
    ];
}
