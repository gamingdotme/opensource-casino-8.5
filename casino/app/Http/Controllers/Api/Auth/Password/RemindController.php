<?php 
namespace VanguardLTE\Http\Controllers\Api\Auth\Password
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RemindController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
        }
        public function index(\VanguardLTE\Http\Requests\Auth\PasswordRemindRequest $request, \VanguardLTE\Repositories\User\UserRepository $users)
        {
            $user = $users->findByEmail($request->email);
            $token = Password::getRepository()->create($user);
            $user->notify(new \VanguardLTE\Notifications\ResetPassword($token));
            event(new \VanguardLTE\Events\User\RequestedPasswordResetEmail($user));
            return $this->respondWithSuccess();
        }
    }

}
