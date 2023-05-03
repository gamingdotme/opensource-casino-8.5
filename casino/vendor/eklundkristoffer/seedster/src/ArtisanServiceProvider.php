<?php

namespace Seedster;

use Seedster\Commands\SeedCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerSeedCommand()
    {
        $this->app->singleton('command.seed', function ($app) {
            return new SeedCommand($app['db']);
        });
    }
}