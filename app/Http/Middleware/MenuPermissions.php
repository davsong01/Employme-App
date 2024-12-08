<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
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
        // Exclude students from this check for now
        if (session()->get('impersonate')) {
            $user = User::where('id', session()->get('impersonate'))->first();
        } else {
            $user = Auth::user();
        }

        $roles = $user->role();
        
        if(in_array('Student', $roles)){
            return $next($request);
        }

        // Exclude some users from this middleware
        if (in_array($user->id, [1])) {
            return $next($request);
        }

        if (empty(array_intersect(adminRoles(), $roles)) || empty(array_intersect(facilitatorRoles(), $roles))) {
            $a_menus = allRoutes();
            $a_permissions = allAccess();
            $all_menus = array_merge($a_menus, $a_permissions);
            
            $user_menus = auth()->check() ? $user->menu_permissions ?? [] : [];
            
            $currentRouteName = Route::currentRouteName();
            
            if (in_array($currentRouteName, $all_menus)){
                if(in_array($currentRouteName, $user_menus)){
                    return $next($request);
                }else{
                    return redirect(route('home'))->with('danger', 'Unauthorized access.');
                }
            }
        }
        
        return $next($request);

    }
}
