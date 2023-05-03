<?php

namespace VanguardLTE\Listeners;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use VanguardLTE\Activity;
use VanguardLTE\Events\Settings\Updated as SettingsUpdated;
use VanguardLTE\Events\User\Banned;
use VanguardLTE\Events\User\ChangedAvatar;
use VanguardLTE\Events\User\Created;
use VanguardLTE\Events\User\Deleted;
use VanguardLTE\Events\User\GeoChanged;
use VanguardLTE\Events\User\LoggedIn;
use VanguardLTE\Events\User\LoggedOut;
use VanguardLTE\Events\User\MoneyIn;
use VanguardLTE\Events\User\MoneyOut;
use VanguardLTE\Events\User\Registered;
use VanguardLTE\Events\User\UpdatedByAdmin;
use VanguardLTE\Events\User\UpdatedProfileDetails;
use VanguardLTE\Events\User\UserConfirmed;
use VanguardLTE\Events\User\UserEventContract;
use VanguardLTE\Events\User\UserUnBanned;
use VanguardLTE\Helpers\UserSystemInfoHelper;
use VanguardLTE\Services\Logging\UserActivity\Logger;

class UserEventsSubscriber
{
    /**
     * @var UserActivityLogger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onLogin(LoggedIn $event)
    {
        $this->logger->log(trans('log.logged_in'), NULL, 'user', 'info');
    }

    public function onLogout(LoggedOut $event)
    {
        $this->logger->log(trans('log.logged_out'), NULL, 'user', 'info');
    }

    public function onRegister(Registered $event)
    {
        //$this->logger->setUser($event->getRegisteredUser());
        //$this->logger->log(trans('log.created_account'), NULL, 'user', 'info');
    }

    public function onAvatarChange(ChangedAvatar $event)
    {
        //$this->logger->log(trans('log.updated_avatar'), NULL, 'user', 'info');
    }

    public function onProfileDetailsUpdate(UpdatedProfileDetails $event)
    {
        //$this->logger->log(trans('log.updated_profile'), NULL, 'user', 'info');
    }

    public function onDelete(Deleted $event)
    {
        $message = trans(
            'log.deleted_user',
            ['name' => $event->getDeletedUser()->present()->username]
        );

        //$this->logger->log($message, NULL, 'user', 'info');
    }

    public function onBan(Banned $event)
    {
        $message = trans(
            'log.banned_user',
            ['name' => $event->getBannedUser()->present()->username]
        );

        //$this->logger->log($message, NULL, 'user', 'info');
    }

    public function onUpdateByAdmin(UpdatedByAdmin $event)
    {
        $message = trans(
            'log.updated_profile_details_for',
            ['name' => $event->getUpdatedUser()->present()->username]
        );

        //$this->logger->log($message, NULL, 'user', 'info');
    }

    public function onCreate(Created $event)
    {
        $message = trans(
            'log.created_account_for',
            ['name' => $event->getCreatedUser()->present()->username]
        );

        //$this->logger->log($message, NULL, 'user', 'info');
    }

    public function onSettingsUpdate(SettingsUpdated $event)
    {
        //$this->logger->log(trans('log.updated_settings'), NULL, 'user', 'info');
    }


    public function onMoneyIn(MoneyIn $event)
    {
        $shop = $event->getUser()->shop;
        $text = trans('app.balance_updated') . ' / ' . $event->getUser()->username. '  +' . $event->getSum() . ' ';
        if($shop){
            $text .= $shop->currency;
        }

        //$this->logger->setUser($event->getUser());
        //$this->logger->log($text, NULL, 'user', 'info');
    }

    public function onMoneyOut(MoneyOut $event)
    {
        $shop = $event->getUser()->shop;
        $text = trans('app.balance_updated') . ' / ' . $event->getUser()->username. '  -' . $event->getSum() . ' ';
        if($shop){
            $text .= $shop->currency;
        }
        //$this->logger->log($text, NULL, 'user', 'info');
    }

    public function onUserUnBanned(UserUnBanned $event)
    {
        //$this->logger->log(trans('app.account_is_unbanned'), NULL, 'user', 'info');
    }

    public function onUserConfirmed(UserConfirmed $event)
    {
        //$this->logger->setUser($event->getUser());
        //$this->logger->log(trans('app.account_is_confirmed'), NULL, 'user', 'info');
    }

    public function onGeoChanged(GeoChanged $event)
    {
        $user = $event->getUser();
        $text = 'GEO / ' . $user->username;
        $this->logger->log($text, NULL, 'user', 'geo');

    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $class = 'VanguardLTE\Listeners\UserEventsSubscriber';

        $events->listen(LoggedIn::class, "{$class}@onLogin");
        $events->listen(LoggedOut::class, "{$class}@onLogout");
        $events->listen(Registered::class, "{$class}@onRegister");
        $events->listen(Created::class, "{$class}@onCreate");
        $events->listen(ChangedAvatar::class, "{$class}@onAvatarChange");
        $events->listen(UpdatedProfileDetails::class, "{$class}@onProfileDetailsUpdate");
        $events->listen(UpdatedByAdmin::class, "{$class}@onUpdateByAdmin");
        $events->listen(Deleted::class, "{$class}@onDelete");
        $events->listen(Banned::class, "{$class}@onBan");
        $events->listen(SettingsUpdated::class, "{$class}@onSettingsUpdate");


        $events->listen(MoneyIn::class, "{$class}@onMoneyIn");
        $events->listen(MoneyOut::class, "{$class}@onMoneyOut");
        $events->listen(UserUnBanned::class, "{$class}@onUserUnBanned");
        $events->listen(UserConfirmed::class, "{$class}@onUserConfirmed");
        $events->listen(GeoChanged::class, "{$class}@onGeoChanged");
    }
}
