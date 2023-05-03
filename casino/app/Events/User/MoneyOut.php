<?php

namespace VanguardLTE\Events\User;

use VanguardLTE\User;

class MoneyOut
{
    /**
     * @var User
     */
    protected $user;
    protected $sum;

    public function __construct(User $user, $sum)
    {
        $this->user = $user;
        $this->sum = $sum;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getSum()
    {
        return $this->sum;
    }
}
