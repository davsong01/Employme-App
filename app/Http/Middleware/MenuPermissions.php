<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class MenuPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {   
        // dd(Auth::user()->role_id);
        if(Auth::user()->role_id != 'Admin' && Auth::user()->role_id != 'Student' ){
            $i_menus = [];
            $all_menus = app('App\Http\Controllers\Controller')->adminMenus();

            if (auth()->user()) {
                $i_menus  = Auth::user()->menu_permissions ?? [];
                if ($i_menus) {
                    $i_menus  = explode(',', $i_menus);
                }
            }

            $allowed = [];

            foreach ($all_menus as $menu) {
                if (in_array($menu['id'], $i_menus)) {
                    $allowed[] = $menu['route'];
                }
            }

            // Check if user has access to page
            $name = Route::currentRouteName();
            if (in_array($name, array_column($all_menus, 'route'))) {
                if (in_array($name, $allowed)) {
                    return $next($request);
                } else {
                    return back()->with('error', 'Unauthorised');
                }
            } else {
                return $next($request);
            }
        }
        
        return $next($request);

    }
}