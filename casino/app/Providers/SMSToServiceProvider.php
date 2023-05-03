<?php

namespace VanguardLTE\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Intergo\SmsTo\Http\Client as SmsToClient;

class SMSToServiceProvider extends BaseServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/smsto.php' => config_path('smsto.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../views' => resource_path('views/vendor/smsto'),
        ], 'views');

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/smsto.php', 'smsto'
        );

        $this->loadViewsFrom(__DIR__.'/../../views', 'smsto');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        // If PHP SDK is ready
        $this->app->bind('laravel-smsto', function() {
            return new SmsToClient(
                settings('smsto_client_id'),
                settings('smsto_client_secret'),
                config('smsto.username'),
                config('smsto.password'),
                $this->getAccessToken()
            );
        });
    }


    public function getAccessToken()
    {
        $accessToken = null;

        // Check if we have accessToken saved already
        if ( ! file_exists(storage_path('smsto-accessToken'))) {
            $client = new SmsToClient(
                settings('smsto_client_id'),
                settings('smsto_client_secret'),
                config('smsto.username'),
                config('smsto.password')
            );
            $response = $client->getAccessToken();

            if ($response) {
                if (isset($response['access_token'])) {
                    $date = Carbon::now()->addSeconds($response['expires_in']);
                    file_put_contents(storage_path('smsto-accessTokenExpiredOn'), $date->toDateTimeString());
                    file_put_contents(storage_path('smsto-accessToken'), $response['access_token']);
                    $accessToken = $response['access_token'];
                }
            }
        } else {
            $dateExpired = file_get_contents(storage_path('smsto-accessTokenExpiredOn'));
            if ($dateExpired > Carbon::now()->toDateTimeString()) {
                $accessToken = file_get_contents(storage_path('smsto-accessToken'));
            }
        }

        return $accessToken;
    }
}
