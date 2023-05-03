<?php 

namespace Intergo\SmsTo;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Carbon\Carbon;
use Storage;
use Intergo\SmsTo\Http\Client as SmsToClient;

class LumenServiceProvider extends BaseServiceProvider {
    
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


        $this->mergeConfigFrom(
            __DIR__ . '/../../config/smsto.php', 'smsto'
        );

        // May be we need this if lumen
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Intergo\SmsTo\PublishCommand::class
            ]);
        }

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
