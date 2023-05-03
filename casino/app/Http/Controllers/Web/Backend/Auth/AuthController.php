<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend\Auth
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class AuthController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $users = null;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users)
        {
            $this->middleware('guest', [
                'except' => ['getLogout']
            ]);
            $this->middleware('auth', [
                'only' => ['getLogout']
            ]);
            $this->middleware('registration', [
                'only' => [
                    'getRegister', 
                    'postRegister'
                ]
            ]);
            $this->users = $users;
        }
        public function getLogin()
        {
            $directories = [];
            foreach( glob(resource_path() . '/lang/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            return view('backend.auth.login', compact('directories'));
        }
        public function postLogin(\VanguardLTE\Http\Requests\Auth\LoginRequest $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $throttles = settings('throttle_enabled');
            $to = ($request->has('to') ? '?to=' . $request->get('to') : '');
            if( $throttles && $this->hasTooManyLoginAttempts($request) ) 
            {
                return $this->sendLockoutResponse($request);
            }
            $credentials = $request->getCredentials();
            if( !\Auth::validate($credentials) ) 
            {
                if( $throttles ) 
                {
                    $this->incrementLoginAttempts($request);
                }
                return redirect()->to('backend/login' . $to)->withErrors(trans('auth.failed'));
            }
            $user = \Auth::getProvider()->retrieveByCredentials($credentials);
            if( $request->lang ) 
            {
                $user->update(['language' => $request->lang]);
            }
            if( !$user->hasRole('admin') && settings('siteisclosed') ) 
            {
                \Auth::logout();
                return redirect()->route('backend.auth.login')->withErrors(trans('app.site_is_turned_off'));
            }
            $data = \VanguardLTE\Lib\GeoData::get_data();
            if( $data['country'] != '' && !$user->country ) 
            {
                $user->update(['country' => $data['country']]);
            }
            if( $data['city'] != '' && !$user->town ) 
            {
                $user->update(['town' => $data['city']]);
            }
            if( $data['country'] == '' ) 
            {
                return redirect()->route('backend.auth.login')->withErrors(trans('app.unknown_country'));
            }
            if( \VanguardLTE\Lib\Filter::country_filtered($user, $data['country']) ) 
            {
                return redirect()->route('backend.auth.login')->withErrors(trans('app.blocked_phone_zone'));
            }
            if( $user->isBlocked() ) 
            {
                return redirect()->to('backend/login' . $to)->withErrors(trans('app.your_shop_is_blocked'));
            }
            if( $user->hasRole('user') && $user->shop && $user->shop->pending ) 
            {
                return redirect()->to('backend/login' . $to)->withErrors(__('app.shop_is_creating'));
            }
            if( $user->isBanned() ) 
            {
                return redirect()->to('backend/login' . $to)->withErrors(trans('app.your_account_is_banned'));
            }
            if( settings('reset_authentication') && $user->hasRole('user') && count($sessionRepository->getUserSessions(\Auth::id())) ) 
            {
                foreach( $sessionRepository->getUserSessions($user->id) as $session ) 
                {
                    $sessionRepository->invalidateSession($session->id);
                }
            }
            \Auth::login($user, true);
            return $this->handleUserWasAuthenticated($request, $throttles, $user);
        }
        protected function handleUserWasAuthenticated(\Illuminate\Http\Request $request, $throttles, $user)
        {
            if( $throttles ) 
            {
                $this->clearLoginAttempts($request);
            }
            event(new \VanguardLTE\Events\User\LoggedIn());
            if( $request->has('to') ) 
            {
                return redirect()->to($request->get('to'));
            }
            if( !\Auth::user()->hasPermission('dashboard') ) 
            {
                return redirect()->route('backend.user.list');
            }
            return redirect()->route('backend.dashboard');
        }
        public function getLogout()
        {
            event(new \VanguardLTE\Events\User\LoggedOut());
            session()->forget('beforeUser');
            \Auth::logout();
            \Google2FA::logout();
            return redirect('/backend/login');
        }
        public function loginUsername()
        {
            return 'username';
        }
        protected function hasTooManyLoginAttempts(\Illuminate\Http\Request $request)
        {
            return app('Illuminate\Cache\RateLimiter')->tooManyAttempts($request->input($this->loginUsername()) . $request->ip(), $this->maxLoginAttempts());
        }
        protected function incrementLoginAttempts(\Illuminate\Http\Request $request)
        {
            app('Illuminate\Cache\RateLimiter')->hit($request->input($this->loginUsername()) . $request->ip(), $this->lockoutTime() / 60);
        }
        protected function retriesLeft(\Illuminate\Http\Request $request)
        {
            $attempts = app('Illuminate\Cache\RateLimiter')->attempts($request->input($this->loginUsername()) . $request->ip());
            return $this->maxLoginAttempts() - $attempts + 1;
        }
        protected function sendLockoutResponse(\Illuminate\Http\Request $request)
        {
            $seconds = app('Illuminate\Cache\RateLimiter')->availableIn($request->input($this->loginUsername()) . $request->ip());
            return redirect('/backend/login')->withInput($request->only($this->loginUsername(), 'remember'))->withErrors([$this->loginUsername() => $this->getLockoutErrorMessage($seconds)]);
        }
        protected function getLockoutErrorMessage($seconds)
        {
            return trans('auth.throttle', ['seconds' => $seconds]);
        }
        protected function clearLoginAttempts(\Illuminate\Http\Request $request)
        {
            app('Illuminate\Cache\RateLimiter')->clear($request->input($this->loginUsername()) . $request->ip());
        }
        protected function maxLoginAttempts()
        {
            return settings('throttle_attempts', 5);
        }
        protected function lockoutTime()
        {
            $lockout = (int)settings('throttle_lockout_time');
            if( $lockout <= 1 ) 
            {
                $lockout = 1;
            }
            return 60 * $lockout;
        }
        public function getRegister()
        {
            return view('backend.auth.register');
        }
        public function postRegister(\VanguardLTE\Http\Requests\Auth\RegisterRequest $request, \VanguardLTE\Repositories\Role\RoleRepository $roles)
        {
            $user = $this->users->create(array_merge($request->only('username', 'password'), ['status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE]));
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            return redirect('/backend/login')->with('success', trans('app.account_created_login'));
        }
    }

}
