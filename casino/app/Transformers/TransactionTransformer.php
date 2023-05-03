<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\Transaction;

class TransactionTransformer extends TransformerAbstract
{
    public function transform(Transaction $transaction)
    {		
		
        return [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'payeer_id' => $transaction->payeer_id, 
			'system' => $transaction->system,
			'type' => $transaction->type,
			'summ' => $transaction->summ,
            'value' => $transaction->value,
			'status' => $transaction->status,
            'shop_id' => $transaction->shop_id,
			'created_at' => $transaction->created_at,			
        ];
    }
}
