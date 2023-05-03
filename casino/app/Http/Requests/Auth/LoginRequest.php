<?php 
namespace VanguardLTE\Http\Requests\Auth
{
    class LoginRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return [
                'username' => 'required', 
                'password' => 'required'
            ];
        }
        public function getCredentials()
        {
            $username = $this->get('username');
            return $this->only('username', 'password');
        }
    }

}
