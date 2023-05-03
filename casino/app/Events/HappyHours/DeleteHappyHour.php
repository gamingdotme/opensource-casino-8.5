<?php

namespace VanguardLTE\Events\HappyHours;

use VanguardLTE\HappyHour;

class DeleteHappyHour
{
    /**
     * @var HappyHour
     */
    protected $DeleteHappyHour;

    public function __construct(HappyHour $DeleteHappyHour)
    {
        $this->DeleteHappyHour = $DeleteHappyHour;
    }

    /**
     * @return HappyHour
     */
    public function getDeleteHappyHour()
    {
        return $this->DeleteHappyHour;
    }
}
