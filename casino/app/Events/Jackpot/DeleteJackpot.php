<?php

namespace VanguardLTE\Events\Jackpot;

use VanguardLTE\JPG;

class DeleteJackpot
{
    /**
     * @var Returns
     */
    protected $DeleteJackpot;

    public function __construct(JPG $DeleteJackpot)
    {
        $this->DeleteJackpot = $DeleteJackpot;
    }

    /**
     * @Jackpot Jackpots
     */
    public function getDeleteJackpot()
    {
        return $this->DeleteJackpot;
    }
}
