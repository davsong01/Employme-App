<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ImpersonateAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('impersonate_admin')) {
            Auth::guard('admin')->onceUsingId(session()->get('impersonate_admin'));
        }
        
        return $next($request);
    }
}
