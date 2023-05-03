<?php

namespace VanguardLTE\Events\User;

use VanguardLTE\User;

class GeoChanged
{
    /**
     * @var User
     */
    protected $createdUser;

    public function __construct(User $createdUser)
    {
        $this->createdUser = $createdUser;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->createdUser;
    }
}
