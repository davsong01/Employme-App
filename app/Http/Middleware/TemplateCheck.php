<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Settings;
use App\Models\Template;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TemplateCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $setting = Settings::first();
        $template =  $setting->frontend_template;

        $currency = $setting->CURR_ABBREVIATION;
        $currency_symbol = $setting->DEFAULT_CURRENCY;

        if (empty($template) || $template == 0) {
            $template = 'contai';
        } else {
            $template = Template::where('id', $template)->value('name');
            if (is_null($template)) {
                $template = 'contai';
            }
        }
        
        if ($request->get('facilitator')) {
           
            $facilitator = User::with('payment_modes')->where('license', $request->facilitator)->first();

            if (isset($facilitator) && !empty($facilitator)) {
                Session::put('facilitator', $facilitator);
                Session::put('facilitator_id', $facilitator->id);
                
                Session::put('facilitator_name', $facilitator->name);
                Session::put('facilitator_license', $facilitator->license);

                if (isset($facilitator->payment_modes) && !empty($facilitator->payment_modes)) {
                    $currency_symbol = $facilitator->payment_modes->currency_symbol;
                    Session::put('currency_symbol', $currency_symbol);
                    Session::put('currency', $facilitator->payment_modes->currency);
                    Session::put('exchange_rate', $facilitator->payment_modes->exchange_rate);
                }
            }
        }

        if(!Session::get('currency_symbol')){
            Session::put('currency_symbol', $currency_symbol);
            Session::put('currency', $currency);
            Session::put('exchange_rate', 1);
        }

        $request['template'] = $template;

        return $next($request);
    }
}
