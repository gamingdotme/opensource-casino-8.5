<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\JPG;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\Game;

class GameTransformer extends TransformerAbstract
{
    public function transform(Game $game)
    {

        return [
            'id' => $game->id,
            'name' => $game->name,
            'title' => $game->title,
            'category' => $game->categories->pluck('category_id')->toArray(),
            'device' => $game->device,
            'denomination' => $game->denomination,
			'view' => $game->view,
            'label' => $game->label,
            'jpg' => $game->jackpot ? $game->jackpot->get(['name', 'balance']) : ''
        ];
    }
}
