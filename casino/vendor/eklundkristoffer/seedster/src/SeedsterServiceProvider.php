<?php

namespace Seedster;

use Seedster\SeedServiceProvider;
use Seedster\Commands\SeedCommand;
use Seedster\Handlers\SeedHandler;
use Illuminate\Support\ServiceProvider;

class SeedsterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ArtisanServiceProvider::class);

        $this->app->singleton('seed.handler', function ($app) {
            return new SeedHandler($app, collect());
        });

        $this->app->singleton('command.seed', function ($app) {
            return new SeedCommand($app['db']);
        });
    }
}