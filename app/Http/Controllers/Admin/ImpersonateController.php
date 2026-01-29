<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

    public function indexStaff(Request $request, $id)
    {
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return redirect('/dashboard');
        }
        
        Session::put('impersonate_o', Auth::guard('admin')->user()->id);

        $user = Admin::find($id);
        Auth::guard('admin')->login($user);
        $user->setImpersonating($user->id);
        
        return redirect(route('admin.home'));
    }

    public function stopImpersonate()
    {
        $user = resolveAuthUser();
        
        if(Auth::check()){
            Auth::logout($user);
            $user->stopImpersonating();
        }else{
            return redirect(route('login'));
        }

        return redirect(route('users.index'))->with('message', 'Welcome back!');   

    }

    public function stopImpersonateFacilitator()
    {
        if (Auth::guard('admin')->check()) {
            resolveAuthUser()->stopImpersonating();
        }else{
            return redirect(route('admin.login'));
        }
        
        return redirect(route('teachers.index'))->with('message', 'Welcome back!');   

    }
    
}