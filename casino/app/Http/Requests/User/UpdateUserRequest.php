<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateUserRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $user = $this->user();
            return [
                'username' => 'regex:/^[A-Za-z0-9_.]+$/|nullable|unique:users,username,' . $user->id, 
                'email' => 'nullable|unique:users,email,' . $user->id, 
                'password' => 'min:6|confirmed', 
                'status' => \Illuminate\Validation\Rule::in(array_keys(\VanguardLTE\Support\Enum\UserStatus::lists()))
            ];
        }
    }

}
