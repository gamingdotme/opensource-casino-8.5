<?php

namespace VanguardLTE\Listeners\Users;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use VanguardLTE\Events\User\Banned;
use VanguardLTE\Events\User\LoggedIn;
use VanguardLTE\Repositories\Session\SessionRepository;
use VanguardLTE\Repositories\User\UserRepository;
use VanguardLTE\Services\Auth\Api\Token;

class InvalidateSessionsAndTokens
{
    /**
     * @var SessionRepository
     */
    private $sessions;

    public function __construct(SessionRepository $sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * Handle the event.
     *
     * @param Banned $event
     * @return void
     */
    public function handle(Banned $event)
    {
        $user = $event->getBannedUser();

        $this->sessions->invalidateAllSessionsForUser($user->id);

        Token::where('user_id', $user->id)->delete();
    }
}
