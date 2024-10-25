<?php

namespace App\Http\Middleware;

use Closure;
use session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            if ($request->session()->has('impersonate')) {
                Auth::onceUsingId($request->session()->get('impersonate'));
            }
        }
        return $next($request);
    }
}
