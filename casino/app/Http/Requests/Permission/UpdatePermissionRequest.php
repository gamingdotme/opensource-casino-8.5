<?php 
namespace VanguardLTE\Http\Requests\Permission
{
    class UpdatePermissionRequest extends BasePermissionRequest
    {
        public function rules()
        {
            $permission = $this->route('permission');
            return [
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:permissions,slug,' . $permission->id, 
                'name' => 'required'
            ];
        }
    }

}
