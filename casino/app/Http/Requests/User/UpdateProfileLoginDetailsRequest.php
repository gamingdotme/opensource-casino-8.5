<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UpdateProfileLoginDetailsRequest extends UpdateLoginDetailsRequest
    {
        protected function getUserForUpdate()
        {
            return Auth::user();
        }
    }

}
