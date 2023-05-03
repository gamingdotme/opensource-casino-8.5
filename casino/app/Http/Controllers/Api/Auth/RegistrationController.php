<?php 
namespace VanguardLTE\Http\Controllers\Api\Auth
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RegistrationController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        private $users = null;
        private $roles = null;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users, \VanguardLTE\Repositories\Role\RoleRepository $roles)
        {
            $this->middleware('registration');
            $this->users = $users;
            $this->roles = $roles;
        }
        public function index(\VanguardLTE\Http\Requests\Auth\RegisterRequest $request)
        {
            if( !$request->cashier_id ) 
            {
                return $this->errorWrongArgs('No Cashier ID');
            }
            $cashier = \VanguardLTE\User::where([
                'id' => $request->cashier_id, 
                'role_id' => 2
            ])->first();
            if( !$cashier ) 
            {
                return $this->errorWrongArgs('Wrong Cashier ID');
            }
            if( isset($request->email) && ($return = \VanguardLTE\Lib\Filter::domain_filtered($request->email)) ) 
            {
                return $this->errorWrongArgs(__('app.blocked_domain_zone', ['zone' => $return['domain']]));
            }
            $status = (settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE);
            $user = $this->users->create(array_merge($request->only('email', 'username', 'password'), [
                'status' => $status, 
                'role_id' => 1, 
                'parent_id' => $cashier->id, 
                'shop_id' => $cashier->shop_id
            ]));
            \VanguardLTE\ShopUser::create([
                'user_id' => $user->id, 
                'shop_id' => $cashier->shop_id
            ]);
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            return $this->setStatusCode(201)->respondWithArray(['requires_email_confirmation' => settings('use_email')]);
        }
        public function verifyEmail($token)
        {
            if( !settings('use_email') ) 
            {
                return $this->errorNotFound();
            }
            if( $user = $this->users->findByConfirmationToken($token) ) 
            {
                $this->users->update($user->id, [
                    'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE, 
                    'confirmation_token' => null
                ]);
                return $this->respondWithSuccess();
            }
            return $this->setStatusCode(400)->respondWithError('Invalid confirmation token.');
        }
    }

}
