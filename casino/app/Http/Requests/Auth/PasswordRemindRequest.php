<?php 
namespace VanguardLTE\Http\Requests\Auth
{
    class PasswordRemindRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return ['email' => 'required|email|exists:users,email'];
        }
    }

}
