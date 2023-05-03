<?php 
namespace VanguardLTE\Http\Requests\Role
{
    class UpdateRoleRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $role = $this->route('role');
            return ['slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:roles,slug,' . $role->id];
        }
    }

}
