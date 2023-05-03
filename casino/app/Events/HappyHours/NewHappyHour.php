<?php

namespace VanguardLTE\Events\HappyHours;

use VanguardLTE\HappyHour;

class NewHappyHour
{
    /**
     * @var HappyHour
     */
    protected $NewHappyHour;

    public function __construct(HappyHour $NewHappyHour)
    {
        $this->NewHappyHour = $NewHappyHour;
    }

    /**
     * @return HappyHour
     */
    public function getNewHappyHour()
    {
        return $this->NewHappyHour;
    }
}
