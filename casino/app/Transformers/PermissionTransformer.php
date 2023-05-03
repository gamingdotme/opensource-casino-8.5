<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Permission;

class PermissionTransformer extends TransformerAbstract
{
    public function transform(Permission $permission)
    {
        return [
            'id' => (int) $permission->id,
            'name' => $permission->name,
            'display_name' => $permission->display_name,
            'description' => $permission->description,
            'removable' => (boolean) $permission->removable,
            'updated_at' => (string) $permission->updated_at,
            'created_at' => (string) $permission->created_at
        ];
    }
}
