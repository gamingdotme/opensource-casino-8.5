<?php

namespace VanguardLTE\Events\HappyHours;

use VanguardLTE\HappyHour;

class HappyHourEdited
{
    /**
     * @var HappyHour
     */
    protected $editedHappyHour;

    public function __construct(HappyHour $editedHappyHour)
    {
        $this->editedHappyHour = $editedHappyHour;
    }

    /**
     * @return HappyHour
     */
    public function getEditedHappyHour()
    {
        return $this->editedHappyHour;
    }
}
