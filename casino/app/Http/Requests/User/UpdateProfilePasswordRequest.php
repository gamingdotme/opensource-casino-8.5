<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateProfilePasswordRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return [
                'old_password' => 'required', 
                'password' => 'required|min:8|confirmed|different:old_password', 
                'password_confirmation' => 'required|min:8'
            ];
        }
    }

}
