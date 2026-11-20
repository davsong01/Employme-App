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

        $user = User::findOrFail($id);
        $originalAdmin = Auth::guard('admin')->user();
        abort_unless($originalAdmin, 403);
        Session::put('impersonation', ['type' => 'participant', 'original_admin_id' => $originalAdmin->id, 'target_id' => $user->id]);
        Auth::guard('web')->login($user);
        
        return redirect(route('home'));
    }

    public function indexStaff(Request $request, $id)
    {
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return redirect('/dashboard');
        }
        
        $originalAdmin = Auth::guard('admin')->user();
        abort_unless($originalAdmin, 403);
        $user = Admin::findOrFail($id);
        Session::put('impersonation', ['type' => 'staff', 'original_admin_id' => $originalAdmin->id, 'target_id' => $user->id]);
        Auth::guard('admin')->onceUsingId($user->id);
        
        return redirect(route('admin.home'));
    }

    public function stopImpersonate()
    {
        $state = Session::get('impersonation');
        if (!is_array($state) || ($state['type'] ?? null) !== 'participant' || !Auth::guard('admin')->check()) {
            return redirect(route('login'));
        }
        Auth::guard('web')->logout();
        Session::forget(['impersonation', 'impersonate', 'impersonate_admin', 'impersonate_o']);
        return redirect(route('users.index'))->with('message', 'Welcome back!');   

    }

    public function stopImpersonateFacilitator()
    {
        $state = Session::get('impersonation');
        if (!is_array($state) || ($state['type'] ?? null) !== 'staff' || !Auth::guard('admin')->check()) {
            return redirect(route('admin.login'));
        }
        Session::forget(['impersonation', 'impersonate', 'impersonate_admin', 'impersonate_o']);
        return redirect(route('teachers.index'))->with('message', 'Welcome back!');   

    }
    
}
