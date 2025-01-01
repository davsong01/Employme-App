<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function index(Request $request, $id)
    {

        if (!checkRoleHas(['Admin','Facilitator','Grader'])) {
            return redirect('/dashboard');
        }

        $user = User::find($id);
        Auth::login($user);
        resolveAuthUser()->setImpersonating($user->id);
        
        return redirect(route('home'));
    }

    public function stopImpersonate()
    {
        if(Auth::check()){
            Auth::logout(resolveAuthUser());
            resolveAuthUser()->stopImpersonating();
        }else{
            return redirect(route('login'));
        }

        return redirect(route('users.index'))->with('message', 'Welcome back!');   

    }

    public function stopImpersonateFacilitator()
    {
        if (Auth::check()) {
            resolveAuthUser()->stopImpersonating();
        }else{
            return redirect(route('login'));
        }
        
        return redirect(route('teachers.index'))->with('message', 'Welcome back!');   

    }
    
}