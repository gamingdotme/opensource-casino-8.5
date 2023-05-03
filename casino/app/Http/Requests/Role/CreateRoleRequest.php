<?php 
namespace VanguardLTE\Http\Requests\Role
{
    class CreateRoleRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return ['slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:roles,slug'];
        }
    }

}
