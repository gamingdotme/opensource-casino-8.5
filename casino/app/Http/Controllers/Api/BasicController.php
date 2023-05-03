<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class BasicController extends ApiController
    {
        public function __construct()
        {
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( !(isset($request->key) && $request->key == config('demo.key')) ) 
            {
                return $this->setStatusCode(401)->respondWithArray(['fail' => true]);
            }
            $user = \VanguardLTE\User::where('role_id', 6)->first();
            if( $user ) 
            {
                \VanguardLTE\QuickShop::create(['data' => json_encode($request->all())]);
                return $this->setStatusCode(200)->respondWithArray(['success' => true]);
            }
        }
        public function agent(\Illuminate\Http\Request $request)
        {
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('slug', 'agent')->first();
            $token = str_random(60);
            $data = $request->only([
                'username', 
                'email', 
                'password', 
                'password_confirmation'
            ]);
            $request->validate([
                'email' => 'required|unique:users', 
                'username' => 'required|unique:users', 
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation', 
                'password_confirmation' => 'min:6'
            ]);
            $data['role_id'] = $role->id;
            $data['parent_id'] = 1;
            $data['status'] = 'Unconfirmed';
            $data['is_demo_agent'] = 1;
            $data['confirmation_token'] = $token;
            if( isset($data['email']) && ($return = \VanguardLTE\Lib\Filter::domain_filtered($data['email'])) ) 
            {
                return [
                    'blocked_domain_zone' => [__('app.blocked_domain_zone', ['zone' => $return['domain']])]
                ];
            }
            $user = \VanguardLTE\User::create($data);
            $user->attachRole($role);
            $user->notify(new \VanguardLTE\Notifications\EmailConfirmation($token));
            return ['success' => true];
        }
    }

}
