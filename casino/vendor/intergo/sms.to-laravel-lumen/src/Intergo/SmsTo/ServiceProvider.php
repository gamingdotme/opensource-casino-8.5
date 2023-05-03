<?php 

namespace Intergo\SmsTo;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Intergo\SmsTo\Http\Client as SmsToClient;

class ServiceProvider extends BaseServiceProvider {
    
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
                config('smsto.client_id'),
                config('smsto.client_secret'),
                config('smsto.username'),
                config('smsto.password'),
                app(\Intergo\SmsTo\Token::class)->getAccessToken()
            );
        });
    }
}
