<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function submitForgetPasswordForm(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|exists:users,email'
        ]);

        $check = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        $user = getUserByGuard($request->email, ['id','name','email']);
        
        if ($check) {
            $tk = $check->token;
        } else {
            $tk = Str::random(60);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $tk,
                'created_at' => Carbon::now()
            ]); 
        }

        $details['subject'] = "You have requested a Password Recovery Link";
        $body = '<p>Hello! ' . $user->name . '</p>';
        $reset_link =  url('/') . "/reset-password?token=" . $tk;
        $body .= '<p style="line-height: 2.0;">Please click the button below to reset your password <br><br><a target="_blank" href="' . $reset_link . '">RESET PASSWORD</a><br/><br/>If you did not request for Password Recovery, kindly notify support Immediately.<b><hr/><br>Warm Regards. (' . config('app.name') . ')<br/></p>';

        $details['email'] = $user->email;
        $details['content'] = $body;
        $details['type'] = 'bulk';

        $this->sendGenericEmail($details);
        dd('donme');
        return back()->with('status', "We have sent a password reset email to: {$request->email}");
    }
}
