<?php

namespace VanguardLTE\Extension;

use Illuminate\Support\ServiceProvider;
use VanguardLTE\Extension\CustomDatabaseSessionHandler;
use Session;

class CustomSessionServiceProvider extends ServiceProvider {

    public function register()
    {
        $connection = $this->app['config']['session.connection'];
        $table = $this->app['config']['session.table'];
        $lifetime = $this->app['config']['session.lifetime'];

        $this->app['session']->extend('database', function($app) use ($connection, $table, $lifetime){
            return new CustomDatabaseSessionHandler(
                $this->app['db']->connection($connection),
                $table,
                $lifetime
            );
        });
    }

}