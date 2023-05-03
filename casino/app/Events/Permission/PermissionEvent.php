<?php

namespace VanguardLTE\Events\Permission;

use VanguardLTE\Permission;

abstract class PermissionEvent
{
    /**
     * @var Permission
     */
    protected $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}