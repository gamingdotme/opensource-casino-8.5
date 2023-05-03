<?php 
namespace VanguardLTE\Http\Requests\Auth\Social
{
    class ApiAuthenticateRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return [
                'network' => [
                    'required', 
                    \Illuminate\Validation\Rule::in(config('auth.social.providers'))
                ], 
                'social_token' => 'required'
            ];
        }
    }

}
