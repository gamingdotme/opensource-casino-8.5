<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend\Auth
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class PasswordController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware('guest');
        }
        public function getBasicTheme()
        {
            $frontend = settings('frontend');
            if( \Auth::check() ) 
            {
                $shop = \Shop::find(\Auth::user()->shop_id);
                if( $shop ) 
                {
                    $frontend = $shop->frontend;
                }
            }
            return $frontend;
        }
        public function forgotPassword()
        {
            $frontend = $this->getBasicTheme();
            return view('frontend.' . $frontend . '.auth.password.remind');
        }
        public function sendPasswordReminder(\VanguardLTE\Http\Requests\Auth\PasswordRemindRequest $request, \VanguardLTE\Repositories\User\UserRepository $users)
        {
            $user = $users->findByEmail($request->email);
            $token = \Password::getRepository()->create($user);
            $user->notify(new \VanguardLTE\Notifications\ResetPassword($token));
            event(new \VanguardLTE\Events\User\RequestedPasswordResetEmail($user));
            if( $request->has('is_ajax') ) 
            {
				return response()->json([trans('app.password_reset_email_sent')],200);
            }
            return redirect()->to('password/remind')->with('success', trans('app.password_reset_email_sent'));
        }
        public function getReset($token = null)
        {
            if( is_null($token) ) 
            {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
            $frontend = $this->getBasicTheme();
            $category1 = null;$cat1 = null;$categories = null;$currentSliderNum = -1;$title = null;$body = null;$keywords = null;$description = null;$jpgs = null;$shop = null;$devices = null;$tournament = null;$is_game_page = null;$jpgSum = null;$gamestat = null;$depositlist = null;
            return view('frontend.' . $frontend . '.auth.password.reset', compact('category1', 'cat1', 'categories', 'currentSliderNum', 'title', 'body', 'keywords', 'description', 'jpgs', 'shop', 'devices', 'tournament', 'is_game_page', 'jpgSum', 'gamestat', 'depositlist'))->with('token', $token);
        }
        public function postReset(\VanguardLTE\Http\Requests\Auth\PasswordResetRequest $request, \VanguardLTE\Repositories\User\UserRepository $users)
        {
            $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
            $response = \Password::reset($credentials, function($user, $password)
            {
                $this->resetPassword($user, $password);
            });
            switch( $response ) 
            {
                case \Password::PASSWORD_RESET:
                    $user = $users->findByEmail($request->email);
                    \Auth::login($user);
                    return redirect('');
                default:
                    return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response)]);
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
