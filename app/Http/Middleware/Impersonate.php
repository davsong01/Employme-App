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
        if(Auth::user()->role_id == 'Admin'){
            if($request->session()->has('impersonate'))
            {
                Auth::onceUsingId($request->session()->get('impersonate'));
            }

        }
        return $next($request);         
        
    }
}

// if(Auth::user()->role_id == 'Student'){
//                 $programs = DB::table('program_user')->where('user_id', Auth::user()->id)->where('program_id', $program->id)->get();
//                 if(!$programs){
//                     return back();
//                 }
//             }