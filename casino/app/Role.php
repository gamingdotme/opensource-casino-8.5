<?php 
namespace VanguardLTE
{
    class Role extends \jeremykenedy\LaravelRoles\Models\Role
    {
        public function hasOnePermission($permission)
        {
            return $this->permissions()->where(['permission_id' => $permission])->first();
        }
    }

}
