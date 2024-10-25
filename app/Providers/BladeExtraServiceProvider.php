<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BladeExtraServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Blade::if('hasrole', function($expression){
        //     if(Auth::user()){
        //     if(Auth::user()->hasAnyRole($expression)){
        //         return true;
        //     }
        // }
        // return false;
    // });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
