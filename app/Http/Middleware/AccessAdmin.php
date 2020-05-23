<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AccessAdmin
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
        
        //}
        // if(Auth::user()->hasAnyRole('admin')){
        //     return $next($request);
        // }
        // return redirect('home');
    //     if(Auth::user()->hasAnyRole(Auth::user()->role_id)){
    //        return $next($request);
    //     }
    //     return redirect('You do not have a role yet, call upon an admin');
    }
}
