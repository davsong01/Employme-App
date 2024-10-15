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


        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            $programs = DB::table('program_user')->where('user_id', Auth::user()->id)->whereProgram_id($request->p_id)->first();
            
            if (empty($programs)) {
                return abort(404);
            }

            $program = Program::find($request->p_id);
            
            // check if program is locked
            if($program->program_lock == 1){
                return redirect(route('home'));
            }

            if ($program->off_season && is_null(Auth::user()->facilitator_id) ) {
                // Select instructor mode disabled for now
                // $instructor = DB::table('program_user')->where('program_id', $request->p_id)->where('user_id', auth::user()->id)->first();
                // dd($programs);
                // $facilitator = !empty($instructor) ? User::find($instructor->facilitator_id) : null;

                // if (!$instructor || !$facilitator) {
                //     return back()->with('error', 'No Instructor found!');
                // }
                // $program = Program::find($request->p_id);
                // return view('dashboard.student.profiles.facilitators', compact('facilitator', 'program'));
                // return redirect('selectfacilitator/' . $program->id);
            }
            
            return $next($request);
        }

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            return $next($request);
        }
    }
}
