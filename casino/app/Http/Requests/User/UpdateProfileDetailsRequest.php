<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateProfileDetailsRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return ['password' => 'nullable|confirmed|min:6'];
        }
    }

}
