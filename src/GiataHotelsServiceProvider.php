<?php

namespace GiataHotels;

use Illuminate\Support\ServiceProvider;

class GiataHotelsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/giata-api.php' => config_path('giata-api.php')]);
    }

    public function register()
    {
        $this->app->singleton(API::class, function () {
            return new API();
        });
    }
}
