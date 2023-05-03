<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\User;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['role', 'country'];

    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
			'balance' => $user->balance,
            'total_in' => $user->total_in,
            'total_out' => $user->total_out,
            'refunds' => $user->refunds,
            'role_id' => (int) $user->role_id,
            'status' => $user->status,
            'language' => $user->language,
            'currency' => $user->shop ? $user->shop->currency : '',
            'last_login' => (string) $user->last_login,
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at
        ];
    }

    public function includeRole(User $user)
    {
        if (! auth('api')->user()->hasPermission('roles.manage')) {
            return null;
        }

		
        return $this->item($user->role, new RoleTransformer);
    }

    public function includeCountry(User $user)
    {
        return $this->item($user->country, new CountryTransformer);
    }
}
