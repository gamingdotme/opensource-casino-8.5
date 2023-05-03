<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
// VanguardLTE\Role;
use jeremykenedy\LaravelRoles\Models\Role;

class RoleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['permissions'];

    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'updated_at' => (string) $role->updated_at,
            'created_at' => (string) $role->created_at
        ];
    }

    public function includePermissions(Role $role)
    {
        return $this->collection(
            $role->cachedPermissions(),
            new PermissionTransformer
        );
    }
}
