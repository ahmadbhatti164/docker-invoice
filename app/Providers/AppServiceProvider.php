<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        \Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return (bool) preg_match('/^[+][0-9]+$/', $value);
        }, 'Please enter the valid phone number. i.e +125xxxxx...');

        if (env('APP_ENV') != 'local') {
            URL::forceScheme(env('URL_SCHEME', 'https'));
        }
    }
}
