<?php 
namespace VanguardLTE\Http\Requests\Role
{
    class UpdateRolePermissionsRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $permissions = \VanguardLTE\Permission::pluck('id')->toArray();
            return [
                'permissions' => 'required|array', 
                'permissions.*' => \Illuminate\Validation\Rule::in($permissions)
            ];
        }
        public function messages()
        {
            return ['permissions.*' => 'Provided permission does not exist.'];
        }
    }

}
