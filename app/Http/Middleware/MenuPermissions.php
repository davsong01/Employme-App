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
    // public function handle(Request $request, Closure $next)
    // {
    //     $currentRouteName = Route::currentRouteName();

    //     // Exclude students from this check for now
    //     if (session()->get('impersonate')) {
    //         $user = User::where('id', session()->get('impersonate'))->first();
    //     } else {
    //         $user = Auth::user();
    //     }

    //     $roles = $user->role();

    //     if(in_array('Student', $roles)){
    //         return $next($request);
    //     }

    //     // Exclude some users from this middleware
    //     if (in_array($user->id, [1])) {
    //         return $next($request);
    //     }

    //     if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
    //         $all_menus = allRoutes();
    //         $all_permissions = allAccess();
    //         $user_menus = auth()->check() ? $user->menu_permissions ?? [] : [];

    //         // Check for program access
    //         if(!empty($request->p_id)){
    //             if (checkPermissionHas($request->p_id, [$currentRouteName])) {
    //                     return $next($request);
    //             } else {
    //                 return redirect(route('home'))->with('danger', 'Unauthorized access.');
    //             }
    //         }

    //         // check for route/menu access
    //         if (in_array($currentRouteName, $all_menus)) {
    //             if (in_array($currentRouteName, $user_menus)) {
    //                 return $next($request);
    //             } else {
    //                 return redirect(route('home'))->with('danger', 'Unauthorized access.');
    //             }
    //         }
    //     }

    //     return $next($request);

    // }
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

        // Allow specific users to bypass this middleware
        $excludedUserIds = [1]; // Add more user IDs as needed
        if (in_array($user->id, $excludedUserIds)) {
            return $next($request);
        }

        // Check if the user has specific roles
        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            $allMenus = allRoutes(); // All possible menu routes
            $allPermissions = allAccess(); // All possible access permissions
            $userMenus = $user->menu_permissions ?? []; 
            
            // Check for program-specific access
            if (!empty($request->p_id)) {
                if (in_array($currentRouteName, $allPermissions)) {
                    if (!URL::hasValidSignature($request)) {
                        return redirect()->route('home')->with('danger', 'Invalid or expired link.');
                    }

                    if (checkPermissionHas($request->p_id, [$currentRouteName])[$currentRouteName]) {
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
