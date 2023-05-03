<?php 
namespace VanguardLTE\Http\Requests\Permission
{
    class BasePermissionRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function messages()
        {
            return ['name.unique' => trans('app.permission_already_exists')];
        }
    }

}
