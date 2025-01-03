<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
        dd('hhh', checkRoleHas(['Admin', 'Facilitator', 'Grader']), request()->route()->uri());
            
            return redirect('/admin/login');
        }
        
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
           
            return back();
        }
        return $next($request);
    }
}
