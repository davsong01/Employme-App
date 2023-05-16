<?php

namespace App\Http\Middleware;

use Closure;
use session;
use App\Program;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgramCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        
       
        if(Auth::user()->role_id == 'Student'){
            $programs = DB::table('program_user')->where('user_id', Auth::user()->id)->whereProgram_id($request->p_id)->first();
            if(empty($programs)){
                return abort(404);
            }

            $program = Program::find($request->p_id);
            if($program->off_season && is_null(Auth::user()->facilitator_id)){
                return redirect('selectfacilitator/' . $program->id);
            }
            
            return $next($request);   
        }

       
        
        if(Auth::user()->role_id == 'Admin' || Auth::user()->role_id == 'Facilitator' || Auth::user()->role_id == 'Grader'){
            return $next($request); 
        }


              
        
    }
}

