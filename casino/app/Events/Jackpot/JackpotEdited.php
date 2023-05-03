<?php

namespace VanguardLTE\Events\Jackpot;

use VanguardLTE\JPG;

class JackpotEdited
{
    /**
     * @var Jackpots
     */
    protected $editedJackpot;

    public function __construct(JPG $editedJackpot)
    {
        $this->editedJackpot = $editedJackpot;
    }

    /**
     * @Jackpot Jackpots
     */
    public function getEditedJackpot()
    {
        return $this->editedJackpot;
    }

}
