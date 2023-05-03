<?php 
namespace VanguardLTE\Http\Controllers\Api\Auth\Password
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ResetController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
        }
        public function index(\VanguardLTE\Http\Requests\Auth\PasswordResetRequest $request)
        {
            $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
            $response = Password::reset($credentials, function($user, $password)
            {
                $this->resetPassword($user, $password);
            });
            switch( $response ) 
            {
                case Password::PASSWORD_RESET:
                    return $this->respondWithSuccess();
                default:
                    return $this->setStatusCode(400)->respondWithError(trans($response));
            }
        }
        protected function resetPassword($user, $password)
        {
            $user->password = $password;
            $user->save();
            event(new \VanguardLTE\Events\User\ResetedPasswordViaEmail($user));
        }
    }

}
