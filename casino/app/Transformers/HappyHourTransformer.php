<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\HappyHour;

class HappyHourTransformer extends TransformerAbstract
{
    public function transform(HappyHour $happyhour)
    {

        return [
            'id' => $happyhour->id,
            'multiplier' => $happyhour->multiplier,
            'wager' => $happyhour->wager,
            'time' => HappyHour::$values['time'][$happyhour->time],
            'status' => $happyhour->status,
            'shop_id' => $happyhour->shop_id,
        ];
    }
}
