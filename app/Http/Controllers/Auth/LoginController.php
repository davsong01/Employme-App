<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Program;
use App\Models\Transaction;
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

                // Update last_login after successful login
                $user->update(['last_login' => now()]);

                return true;
            }
        }

        $credentials = $this->credentials($request);
        $attemptLogin = Auth::attempt($credentials, $request->filled('remember'));

        // If login is successful, update last_login
        if ($attemptLogin) {
            $user = Auth::user();
            $user->update(['last_login' => now()]);
        }

        return $attemptLogin;
    }

}