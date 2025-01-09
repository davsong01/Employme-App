<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CompanyUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        if (!Auth::guard('company_user')->check()) {
            return redirect()->route('auth.login');
        }

        // If authenticated, allow the request to proceed
        return $next($request);
    }
}
