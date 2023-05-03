<?php

namespace VanguardLTE\Events\Jackpot;

use VanguardLTE\JPG;

class NewJackpot
{
    /**
     * @var Returns
     */
    protected $NewJackpot;

    public function __construct(JPG $NewJackpot)
    {
        $this->NewJackpot = $NewJackpot;
    }

    /**
     * @Jackpot Jackpots
     */
    public function getNewJackpot()
    {
        return $this->NewJackpot;
    }
}
