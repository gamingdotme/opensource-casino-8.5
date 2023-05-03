<?php 
namespace VanguardLTE\Http\Requests\Role
{
    class RemoveRoleRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function authorize()
        {
            return $this->route('role')->removable;
        }
        public function rules()
        {
            return [];
        }
    }

}
