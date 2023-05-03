<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateLoginDetailsRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $user = $this->getUserForUpdate();
            return [
                'username' => 'regex:/^[A-Za-z0-9]+$/|nullable|unique:users,username,' . $user->id, 
                'password' => 'nullable|min:6|confirmed'
            ];
        }
        protected function getUserForUpdate()
        {
            return $this->route('user');
        }
    }

}
