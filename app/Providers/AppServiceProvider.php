<?php

namespace App\Providers;
use Illuminate\Support\Carbon;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    config(['app.timezone' => 'America/Bogota']);
    date_default_timezone_set(config('app.timezone'));
    Carbon::setLocale('es');
        

    }
}
