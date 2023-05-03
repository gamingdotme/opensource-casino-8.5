<?php 
namespace VanguardLTE\Http\Requests\Permission
{
    class CreatePermissionRequest extends BasePermissionRequest
    {
        public function rules()
        {
            return [
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:permissions,slug', 
                'name' => 'required'
            ];
        }
    }

}
