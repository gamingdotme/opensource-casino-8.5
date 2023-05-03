<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ProfileController extends \VanguardLTE\Http\Controllers\Controller
    {
        protected $theUser = null;
        private $users = null;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users)
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('session.database', [
                'only' => [
                    'sessions', 
                    'invalidateSession'
                ]
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->users = $users;
            $this->middleware(function($request, $next)
            {
                $this->theUser = \Auth::user();
                return $next($request);
            });
        }
        public function index(\VanguardLTE\Repositories\Role\RoleRepository $rolesRepo)
        {
            $user = $this->theUser;
            $edit = true;
            $roles = $rolesRepo->lists();
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            $shops = \Auth::User()->shops();
            $allShops = \VanguardLTE\Shop::get();
            $free_shops = [];
            foreach( $allShops as $shop ) 
            {
                if( !$shop->distributors_count() ) 
                {
                    $free_shops[$shop->id] = $shop->name;
                }
            }
            return view('backend.user.profile', compact('user', 'edit', 'roles', 'statuses', 'shops', 'free_shops'));
        }
        public function updateDetails(\VanguardLTE\Http\Requests\User\UpdateProfileDetailsRequest $request)
        {
            $this->users->update($this->theUser->id, $request->except('role_id', 'status', 'shops'));
            event(new \VanguardLTE\Events\User\UpdatedProfileDetails());
            return redirect()->back()->withSuccess(trans('app.profile_updated_successfully'));
        }
        public function updateAvatar(\Illuminate\Http\Request $request, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager)
        {
            $this->validate($request, ['avatar' => 'image']);
            $name = $avatarManager->uploadAndCropAvatar($this->theUser, $request->file('avatar'), $request->get('points'));
            if( $name ) 
            {
                return $this->handleAvatarUpdate($name);
            }
            return redirect()->route('backend.profile')->withErrors(trans('app.avatar_not_changed'));
        }
        private function handleAvatarUpdate($avatar)
        {
            $this->users->update($this->theUser->id, ['avatar' => $avatar]);
            event(new \VanguardLTE\Events\User\ChangedAvatar());
            return redirect()->route('backend.profile')->withSuccess(trans('app.avatar_changed'));
        }
        public function updateAvatarExternal(\Illuminate\Http\Request $request, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager)
        {
            $avatarManager->deleteAvatarIfUploaded($this->theUser);
            return $this->handleAvatarUpdate($request->get('url'));
        }
        public function updateLoginDetails(\VanguardLTE\Http\Requests\User\UpdateProfileLoginDetailsRequest $request)
        {
            $data = $request->except('role', 'status');
            if( trim($data['password']) == '' ) 
            {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            $this->users->update($this->theUser->id, $data);
            return redirect()->route('backend.profile')->withSuccess(trans('app.login_updated'));
        }
        public function activity(\VanguardLTE\Repositories\Activity\ActivityRepository $activitiesRepo, \Illuminate\Http\Request $request)
        {
            $user = $this->theUser;
            $activities = $activitiesRepo->paginateActivitiesForUser($user->id, $perPage = 20, $request->get('search'));
            return view('backend.activity.index', compact('activities', 'user'));
        }
        public function sessions(\VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $profile = true;
            $user = $this->theUser;
            $sessions = $sessionRepository->getUserSessions($user->id);
            return view('backend.user.sessions', compact('sessions', 'user', 'profile'));
        }
        public function invalidateSession($session, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $sessionRepository->invalidateSession($session->id);
            return redirect()->route('backend.profile.sessions')->withSuccess(trans('app.session_invalidated'));
        }
        public function setshop(\VanguardLTE\Http\Requests\User\UpdateProfileDetailsRequest $request)
        {
            $shops = auth()->user()->shops_array(true);
            if( !auth()->user()->hasRole([
                'admin', 
                'agent'
            ]) && count($shops) && !in_array($request->shop_id, $shops) ) 
            {
                abort(404);
            }
            $this->users->update($this->theUser->id, $request->only('shop_id'));
            if( $request->to ) 
            {
                return redirect()->to($request->to);
            }
            return redirect()->back()->withSuccess(trans('app.shop_changed_successfully'));
        }
        public function balance()
        {
            $return = [
                'balance' => 0, 
                'currency' => ''
            ];
            if( auth()->user()->hasRole([
                'cashier', 
                'manager'
            ]) ) 
            {
                $shop = \VanguardLTE\Shop::find(auth()->user()->present()->shop_id);
                $return['balance'] = ($shop ? number_format($shop->balance, 2, '.', '') : 0);
            }
            else
            {
                $return['balance'] = number_format(auth()->user()->present()->balance, 2, '.', '');
            }
            if( auth()->user()->present()->shop ) 
            {
                $return['currency'] = auth()->user()->present()->shop->currency;
            }
            return $return;
        }
        public function security()
        {
        }
    }

}
