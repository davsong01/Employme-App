<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Module;
use App\Models\Program;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;

class ImpersonateController extends Controller
{
    public function index(Request $request, $id)
    {

        if (!checkRoleHas(['Admin','Facilitator','Grader'])) {
            return redirect('/dashboard');
        }

        $user = User::find($id);

        Auth::user()->setImpersonating($user->id);
        
        // Guard against administrator impersonate
        return redirect(route('home'));
        // if($user->id <> $id)
        // {
        // }
        // else
        // {
        //     return back()->with('error', 'Impersonate disabled for this user');
        // }

    }

    public function stopImpersonate()
    {
        if(Auth::check()){
            Auth::user()->stopImpersonating();
        }else{
            return redirect(route('login'));
        }

        return redirect(route('users.index'))->with('message', 'Welcome back!');   

    }

    public function stopImpersonateFacilitator()
    {
        if (Auth::check()) {
            Auth::user()->stopImpersonating();
        }else{
            return redirect(route('login'));
        }
        
        return redirect(route('teachers.index'))->with('message', 'Welcome back!');   

    }
    
}