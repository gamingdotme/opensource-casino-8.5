<?php

namespace VanguardLTE\Services\Auth\Api;

use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class JWTServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the bindings for the main JWT class.
     *
     * @return void
     */
    protected function registerJWT()
    {
        $this->app->singleton('tymon.jwt', function ($app) {
            return new JWT(
                $app['tymon.jwt.manager'],
                $app['tymon.jwt.parser']
            );
        });
    }

    /**
     * Register the bindings for the main JWTAuth class.
     *
     * @return void
     */
    protected function registerJWTAuth()
    {
        $this->app->singleton('tymon.jwt.auth', function ($app) {
            return new JWTAuth(
                $app['tymon.jwt.manager'],
                $app['tymon.jwt.provider.auth'],
                $app['tymon.jwt.parser']
            );
        });
    }
}
