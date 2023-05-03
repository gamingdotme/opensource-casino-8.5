<?php

namespace VanguardLTE\Providers;

use VanguardLTE\Events\User\Banned;
use VanguardLTE\Events\User\LoggedIn;
use VanguardLTE\Events\User\Registered;
use VanguardLTE\Listeners\Users\InvalidateSessionsAndTokens;
use VanguardLTE\Listeners\Login\UpdateLastLoginTimestamp;
use VanguardLTE\Listeners\Registration\SendConfirmationEmail;
use VanguardLTE\Listeners\PermissionEventsSubscriber;
use VanguardLTE\Listeners\RoleEventsSubscriber;
use VanguardLTE\Listeners\UserEventsSubscriber;
use VanguardLTE\Listeners\ShopEventsSubscriber;
use VanguardLTE\Listeners\JackpotEventsSubscriber;
use VanguardLTE\Listeners\HappyHourEventsSubscriber;
use VanguardLTE\Listeners\GameEventsSubscriber;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendConfirmationEmail::class,
        ],
        LoggedIn::class => [
            UpdateLastLoginTimestamp::class
        ],
        Banned::class => [
            InvalidateSessionsAndTokens::class
        ],

    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventsSubscriber::class,
        RoleEventsSubscriber::class,
        PermissionEventsSubscriber::class,
        ShopEventsSubscriber::class,
        JackpotEventsSubscriber::class,
        GameEventsSubscriber::class,
        HappyHourEventsSubscriber::class
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
