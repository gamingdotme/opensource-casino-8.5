<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Pincode;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\JPG;

class PincodeTransformer extends TransformerAbstract
{
    public function transform(Pincode $pincode)
    {

        return [
            'id' => $pincode->id,
            'code' => $pincode->code,
            'nominal' => $pincode->nominal,
            'user_id' => $pincode->user_id,
            'status' => $pincode->status,
            'shop_id' => $pincode->shop_id,
        ];
    }
}
