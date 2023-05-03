<?php 
namespace VanguardLTE\Http\Requests\Auth\Social
{
    class SaveEmailRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return ['email' => 'required|email|unique:users,email'];
        }
    }

}
