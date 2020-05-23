<?php

namespace App\Providers;

use App\Module;
use App\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
            Schema::defaultStringLength(191);
            Schema::enableForeignKeyConstraints();

            //make data available in all channels
            // View::share('materials', Material::all());     
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
