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
        $state = $request->session()->get('impersonation');
        if (is_array($state) && ($state['type'] ?? null) === 'participant' && !empty($state['target_id'])) {
            Auth::guard('web')->onceUsingId($state['target_id']);
        }
        return $next($request);
    }
}
