<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Settings;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request['prefix__'] = request()->route()->getPrefix();
        
        $setting = Settings::value('site_access_settings');
        $webAccess = $setting->admin_access;
        
        if ($webAccess == 'disabled') {
            return abort(403);
        }

        if (!in_array(request()->route()->getName(), ['admin.login.post', 'admin.login']) && !auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
