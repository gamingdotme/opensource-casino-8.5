<?php

namespace VanguardLTE\Support\Authorization;

use VanguardLTE\Role;

trait AuthorizationUserTrait
{
    /**
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Check if user has specified role.
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role->name === $role;
    }

    /**
     * Check if user can perform some action.
     * @param $permission
     * @param bool $allRequired
     * @return bool
     */
    public function hasPermission($permission, $allRequired = true)
    {
        $permission = (array) $permission;

        return $allRequired
            ? $this->hasAllPermissions($permission)
            : $this->hasAtLeastOnePermission($permission);
    }

    /**
     * Check if user has all provided permissions
     * (translates to AND logic between permissions).
     *
     * @param array $permissions
     * @return bool
     */
    private function hasAllPermissions(array $permissions)
    {
        $availablePermissions = $this->role->cachedPermissions()->pluck('name')->toArray();

        foreach ($permissions as $perm) {
            if (! in_array($perm, $availablePermissions, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has at least one of provided permissions
     * (translates to OR logic between permissions).
     *
     * @param array $permissions
     * @return bool
     */
    private function hasAtLeastOnePermission(array $permissions)
    {
        $availablePermissions = $this->role->cachedPermissions()->pluck('name')->toArray();

        foreach ($permissions as $perm) {
            if (in_array($perm, $availablePermissions, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set user's role.
     * @param Role $role
     * @return mixed
     */
    public function setRole($role)
    {
        return $this->forceFill([
            'role_id' => $role instanceof Role ? $role->id : $role
        ])->save();
    }
}
