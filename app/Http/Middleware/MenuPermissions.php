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
        $currentRouteName = Route::currentRouteName();
        
        // Get the authenticated or impersonated user
        $user = session()->get('impersonate')
            ? User::find(session()->get('impersonate'))
            : Auth::user();

        if (!$user) {
            return redirect(route('login'))->with('danger', 'Please log in to continue.');
        }

        $roles = $user->role();

        // Allow students to bypass this middleware
        if (in_array('Student', $roles)) {
            return $next($request);
        }

        $excludedUserIds = [1]; // Add more user IDs as needed
        if (in_array($user->id, $excludedUserIds)) {
            return $next($request);
        }

        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            $allMenus = allRoutes();
            $allPermissions = allAccess();
            $userMenus = $user->menu_permissions ?? []; 
            
            // Check for program-specific access
            if (!empty($request->p_id)) {
                if (in_array($currentRouteName, $allPermissions)) {
                    if (!URL::hasValidSignature($request)) {
                        return redirect()->route('home')->with('danger', 'Invalid or expired link.');
                    }

                    if (checkTrainingHasPermissions($request->p_id, [$currentRouteName])[$currentRouteName]) {
                        return $next($request);
                    }
                    return redirect(route('home'))->with('danger', 'Unauthorized access to program.');
                }
            }

            // Check for route-specific access
            if (in_array($currentRouteName, $allMenus)) {
                if (in_array($currentRouteName, $userMenus)) {
                    return $next($request);
                }
                return redirect(route('home'))->with('danger', 'Unauthorized access to menu.');
            }
        }

        // Allow the request to proceed if no conditions block it
        return $next($request);
    }

}
