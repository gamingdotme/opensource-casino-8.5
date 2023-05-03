<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\StatGame;

class GameStatTransformer extends TransformerAbstract
{
    public function transform(StatGame $stat)
    {		
		
        return [
            'id' => $stat->id,
            'game' => $stat->game,
            'date_time' => $stat->date_time,
			'user_id' => $stat->user_id,
			'balance' => $stat->balance,
			'bet' => $stat->bet,
			'win' => $stat->win,
            'in_game' => $stat->in_game,
            'in_jpg' => $stat->in_jpg,
            'in_profit' => $stat->in_profit,
            'denomination' => $stat->denomination,
            'shop_id' => $stat->shop_id,
        ];
    }
}
