<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Settings;
use App\Template;
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

        $currency = $setting->DEFAULT_CURRENCY;

        if(empty($template) || $template == 0){
            $template = 'contai';
        }else{
            $template = Template::where('id', $template)->value('name');
            if(is_null($template)){
                $template = 'contai';
            }
        }

        if($request->get('facilitator')){
            $facilitator = User::where('license', $request->facilitator)->first();
            
            if(isset($facilitator) && !empty($facilitator)){
                Session::put('facilitator', $facilitator);
                Session::put('facilitator_id', $facilitator->id);
                Session::put('facilitator_name', $facilitator->name);
                Session::put('facilitator_license', $facilitator->license);
            }else{
                Session::remove('facilitator_id');
                Session::remove('facilitator');
                Session::remove('facilitator_name');
            }
        }
        
        $request['template'] = $template;
        $request['currency'] = $currency;
        
        return $next($request); 
        
    }
}

