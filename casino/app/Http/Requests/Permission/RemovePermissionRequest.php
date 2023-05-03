<?php 
namespace VanguardLTE\Http\Requests\Permission
{
    class RemovePermissionRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function authorize()
        {
            return $this->route('permission')->removable;
        }
        public function rules()
        {
            return [];
        }
    }

}
