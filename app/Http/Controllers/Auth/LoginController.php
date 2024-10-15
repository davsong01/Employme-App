<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Program;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $login = request()->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'staffID';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    protected function attemptLogin(Request $request)
    {
        $username = $request->input('login');
        
        $user = User::where('email', $username)->orWhere('staffID', $username)->first();
        
        if ($user) {
            $programIds = Program::where(['login_without_password' => 1, 'program_lock' => 0])->pluck('id')->toArray();
            
            $hasProgram = Transaction::where('user_id', $user->id)
                ->whereIn('program_id', $programIds)
                ->exists();
            
            if ($hasProgram) {
                Auth::login($user);
                return true;
            }
        }

        $credentials = $this->credentials($request);
        return Auth::attempt($credentials, $request->filled('remember'));
    }
}
