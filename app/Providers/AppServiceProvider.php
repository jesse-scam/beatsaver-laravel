<?php

namespace App\Providers;

use DB;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production') {
            Url::forceScheme('https');
        }


        if (env('APP_ENV') !== 'production') {
            DB::connection()->enableQueryLog();
        }
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
