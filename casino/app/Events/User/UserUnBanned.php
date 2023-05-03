<?php

namespace VanguardLTE\Events\User;

use VanguardLTE\User;

class UserUnBanned
{
    /**
     * @var User
     */
    protected $bannedUser;

    public function __construct(User $bannedUser)
    {
        $this->bannedUser = $bannedUser;
    }

    /**
     * @return User
     */
    public function getBannedUser()
    {
        return $this->bannedUser;
    }
}
