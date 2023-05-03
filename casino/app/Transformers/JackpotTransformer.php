<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\JPG;

class JackpotTransformer extends TransformerAbstract
{
    public function transform(JPG $jackpot)
    {		
		
        return [
            'id' => $jackpot->id,
            'name' => $jackpot->name,
            'balance' => $jackpot->balance            
        ];
    }
}
