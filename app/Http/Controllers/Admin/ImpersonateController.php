<?php

namespace App\Http\Controllers\Admin;
use App\User;
use App\Module;
use App\Program;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;

class ImpersonateController extends Controller
{
    public function index(Request $request, $id)
    {
        if(Auth::user()->role_id <> 'Admin')
        {
            return redirect('/dashboard');
        }

        $user = User::find($id);

        Auth::user()->setImpersonating($user->id);

        // Guard against administrator impersonate
        if($user->role_id <> 'Admin')
        {
            return redirect(route('home'));
        }
        else
        {
            return back()->with('error', 'Impersonate disabled for this user');
        }

    }

    public function stopImpersonate()
    {
        
        Auth::user()->stopImpersonating();

        return redirect(route('users.index'))->with('message', 'Welcome back!');   

    }

    public function stopImpersonateFacilitator()
    {
        
        Auth::user()->stopImpersonating();

        return redirect(route('teachers.index'))->with('message', 'Welcome back!');   

    }
    
}