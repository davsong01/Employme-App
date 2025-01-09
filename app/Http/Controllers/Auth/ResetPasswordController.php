<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function resetPassword(Request $request)
    {
        if(empty($request->token)){
            return (redirect(route('login')));
        }

        $user = DB::table('password_reset_tokens')->where('token', $request->token)->first();
        
        if (Auth::check()) {
            Auth::logout();
        }
        
        if (!$user) {
            return (redirect(route('login')));
        }

        return view('auth.passwords.reset')->with('token', $request->token);
    }

    public function processResetPassword(Request $request)
    {

        $this->validate($request, [
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'Please provide a password.',
            'password.min' => 'Your password must be at least 8 characters long.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.same' => 'Password confirmation does not match.',
        ]);

        $token = DB::table('password_reset_tokens')->where('token', $request->token)->where('token',
            $request->token
        )->first();
        
        $newPassword = Hash::make($request->password);
        $user = getUserByGuard($token->email);

        if (!$token || !$user) {
            return (redirect(route('login')));
        }
        
        $user->update([
            'password' => $newPassword,
            'last_login' => now()
        ]);

        $token = DB::table('password_reset_tokens')->where('email', $user->email)->where('token', $request->token)->delete();

        Auth::login($user);
        return redirect()->intended('dashboard')->with('message', 'Password Reset Succesfully');
    }
}
