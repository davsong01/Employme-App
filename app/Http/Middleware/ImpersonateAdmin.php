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
        $state = $request->session()->get('impersonation');
        if (is_array($state) && ($state['type'] ?? null) === 'staff' && !empty($state['target_id'])) {
            Auth::guard('admin')->onceUsingId($state['target_id']);
        }
        
        return $next($request);
    }
}
