<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class UsersController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $users = null;
        private $max_users = 10000000;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users)
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->users = $users;
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $statuses = ['' => trans('app.all')] + \VanguardLTE\Support\Enum\UserStatus::lists();
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('level', '<', auth()->user()->level())->pluck('name', 'id');
            $roles->prepend(trans('app.all'), '0');
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $users = \VanguardLTE\User::orderBy('created_at', 'DESC');
            if( !auth()->user()->shop_id ) 
            {
                if( auth()->user()->hasRole('admin') ) 
                {
                    $users = $users->whereIn('role_id', [
                        4, 
                        5
                    ]);
                }
                if( auth()->user()->hasRole('agent') ) 
                {
                    $distributors = auth()->user()->availableUsersByRole('distributor');
                    if( $distributors ) 
                    {
                        $users = $users->whereIn('id', $distributors);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
                if( auth()->user()->hasRole('distributor') ) 
                {
                    $managers = auth()->user()->availableUsersByRole('manager');
                    if( $managers ) 
                    {
                        $users = $users->whereIn('id', $managers);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
            }
            else
            {
                if( auth()->user()->hasRole('admin') ) 
                {
                    $users = $users->whereIn('id', auth()->user()->availableUsers());
                }
                else
                {
                    $users = $users->whereIn('id', auth()->user()->availableUsers())->whereHas('rel_shops', function($query)
                    {
                        $query->where('shop_id', auth()->user()->shop_id);
                    });
                }
            }
            $users = $users->where('id', '!=', auth()->user()->id);
            if( $request->search != '' ) 
            {
                $request->search = str_replace('_', '\_', $request->search);
                $users = $users->where('username', 'like', '%' . $request->search . '%');
            }
            if( $request->status != '' ) 
            {
                $users = $users->where('status', $request->status);
            }
            if( $request->role ) 
            {
                $users = $users->where('role_id', $request->role);
            }
            if( $request->active ) 
            {
                if( $request->active == 1 ) 
                {
                    $users = $users->whereHas('sessions');
                }
                else
                {
                    $users = $users->whereDoesntHave('sessions');
                }
            }
            if( count($users->pluck('id')) ) 
            {
                $activeUsers = \VanguardLTE\User::whereIn('id', $users->pluck('id'))->whereHas('sessions')->pluck('id');
            }
            else
            {
                $activeUsers = \VanguardLTE\User::where('id', 0)->whereHas('sessions')->pluck('id');
            }
            $users = $users->paginate(20)->withQueryString();
            $happyhour = false;
            if( $shop && $shop->happyhours_active ) 
            {
                $happyhour = \VanguardLTE\HappyHour::where([
                    'shop_id' => auth()->user()->shop_id, 
                    'time' => date('G')
                ])->first();
            }
            return view('backend.user.list', compact('users', 'statuses', 'roles', 'happyhour', 'activeUsers'));
        }
        public function get_balance()
        {
            $users = \VanguardLTE\User::orderBy('created_at', 'DESC');
            if( !auth()->user()->shop_id ) 
            {
                if( auth()->user()->hasRole('admin') ) 
                {
                    $users = $users->whereIn('role_id', [
                        4, 
                        5
                    ]);
                }
                if( auth()->user()->hasRole('agent') ) 
                {
                    $distributors = auth()->user()->availableUsersByRole('distributor');
                    if( $distributors ) 
                    {
                        $users = $users->whereIn('id', $distributors);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
                if( auth()->user()->hasRole('distributor') ) 
                {
                    $managers = auth()->user()->availableUsersByRole('manager');
                    if( $managers ) 
                    {
                        $users = $users->whereIn('id', $managers);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
            }
            else
            {
                $users = $users->whereIn('id', auth()->user()->availableUsers())->whereHas('rel_shops', function($query)
                {
                    $query->where('shop_id', auth()->user()->shop_id);
                });
            }
            $users = $users->where('id', '!=', auth()->user()->id)->get();
            $data = [];
            foreach( $users as $user ) 
            {
                $data[$user->id] = ['balance' => number_format(floatval($user->balance), 2, '.', ''),
                    'shop_limit' => $user->shop_limit];
            }
            return json_encode($data);
        }
        public function tree()
        {
            if( \Auth::user()->hasRole('cashier') ) {
                return redirect()->route('netpos');

            }
            $users = \VanguardLTE\User::where('id', auth()->user()->id)->get();
            if (auth()->user()->hasRole('admin')) {
                $users = \VanguardLTE\User::where('role_id', 5)->get();
            }
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('id', auth()->user()->role_id - 1)->first();
            return view('backend.user.tree', compact('users', 'role'));

        }

    
        public function view(\VanguardLTE\User $user, \VanguardLTE\Repositories\Activity\ActivityRepository $activities)
        {
            $userActivities = $activities->getLatestActivitiesForUser($user->id, 10);
            if( auth()->user()->role_id < $user->role_id ) 
            {
                return redirect()->route('backend.user.list');
            }
            return view('backend.user.view', compact('user', 'userActivities'));
        }
        public function create()
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $happyhour = false;
            if( $shop && $shop->happyhours_active ) 
            {
                $happyhour = \VanguardLTE\HappyHour::where([
                    'shop_id' => auth()->user()->shop_id, 
                    'time' => date('G')
                ])->first();
            }
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('level', '<', auth()->user()->level())->pluck('name', 'id');
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            $shops = auth()->user()->shops();
            $availibleUsers = [];
            if( auth()->user()->hasRole('admin') ) 
            {
                $availibleUsers = \VanguardLTE\User::get();
            }
            if( auth()->user()->hasRole('agent') ) 
            {
                $me = \VanguardLTE\User::where('id', auth()->user()->id)->get();
                $distributors = \VanguardLTE\User::where([
                    'parent_id' => auth()->user()->id, 
                    'role_id' => 4
                ])->get();
                if( $shopsIds = auth()->user()->shops(true) ) 
                {
                    $users = \VanguardLTE\ShopUser::whereIn('shop_id', $shopsIds)->pluck('user_id');
                    if( $users ) 
                    {
                        $availibleUsers = \VanguardLTE\User::whereIn('id', $users)->whereIn('role_id', [
                            2, 
                            3
                        ])->get();
                    }
                }
                $me = $me->merge($distributors);
                $availibleUsers = $me->merge($availibleUsers);
            }
            if( auth()->user()->hasRole([
                'distributor', 
                'manager', 
                'cashier'
            ]) ) 
            {
                $me = \VanguardLTE\User::where('id', auth()->user()->id)->get();
                if( $shopsIds = auth()->user()->shops(true) ) 
                {
                    $users = \VanguardLTE\ShopUser::whereIn('shop_id', $shopsIds)->pluck('user_id');
                    if( $users ) 
                    {
                        $availibleUsers = \VanguardLTE\User::whereIn('id', $users)->whereIn('role_id', [
                            2, 
                            3
                        ])->get();
                    }
                }
                $availibleUsers = $me->merge($availibleUsers);
            }
            return view('backend.user.add', compact('roles', 'statuses', 'shops', 'availibleUsers', 'happyhour'));
        }
        public function store(\VanguardLTE\Http\Requests\User\CreateUserRequest $request)
        {
            $count = \VanguardLTE\User::where([
                'shop_id' => auth()->user()->shop_id, 
                'role_id' => 1
            ])->count();
            if( $request->role_id <= 3 && !$request->shop_id ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.choose_shop')]);
            }
            $data = $request->only([
                'email', 
                'username', 
                'language', 
                'status', 
                'shop_id', 
                'is_blocked', 
                'password', 
                'password_confirmation'
            ]) + ['status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE];
            if( isset($data['email']) && ($return = \VanguardLTE\Lib\Filter::domain_filtered($data['email'])) ) 
            {
                return redirect()->back()->withErrors([__('app.blocked_domain_zone', ['zone' => $return['domain']])]);
            }
            if( trim($data['username']) == '' ) 
            {
                $data['username'] = null;
            }
            if( $this->max_users <= $count && $data['role_id'] == 1 ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.max_users', ['max' => $this->max_users])]);
            }
            if( !$request->parent_id ) 
            {
                $data['parent_id'] = auth()->user()->id;
            }
            if( $request->balance && $request->balance > 0 ) 
            {
                $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
                $sum = floatval($request->balance);
                if( $shop->balance < $sum ) 
                {
                    return redirect()->back()->withErrors([trans('app.not_enough_money_in_the_shop', [
                        'name' => $shop->name, 
                        'balance' => $shop->balance
                    ])]);
                }
                $open_shift = \VanguardLTE\OpenShift::where([
                    'shop_id' => auth()->user()->shop_id, 
                    'user_id' => auth()->user()->id, 
                    'end_date' => null
                ])->first();
                if( !$open_shift ) 
                {
                    return redirect()->back()->withErrors([trans('app.shift_not_opened')]);
                }
            }
            $role_id = (isset($data['role_id']) && $data['role_id'] < auth()->user()->role_id ? $data['role_id'] : auth()->user()->role_id - 1);
            $data['role_id'] = $role_id;
            $role = \jeremykenedy\LaravelRoles\Models\Role::find($role_id);
            if( auth()->user()->hasRole('distributor') && $role->slug == 'manager' && \VanguardLTE\User::where([
                'role_id' => $role->id, 
                'shop_id' => $request->shop_id
            ])->count() ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.only_1', ['type' => $role->slug])]);
            }
            $user = $this->users->create($data + ['status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE]);
            $user->detachAllRoles();
            $user->attachRole($role);
            if( $request->shop_id && $request->shop_id > 0 && !empty($request->shop_id) ) 
            {
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $request->shop_id, 
                    'user_id' => $user->id
                ]);
            }
            if( $request->balance && $request->balance > 0 ) 
            {
                $user->addBalance('add', $request->balance);
                if( auth()->user()->hasRole('cashier') ) 
                {
                    auth()->user()->hierarchyUsers(false, true);
                }
            }
            if( !$user->shop_id && $user->hasRole([
                'manager', 
                'cashier', 
                'user'
            ]) ) 
            {
                $shops = $user->shops(true);
                if( count($shops) ) 
                {
                    $shop_id = $shops->first();
                    $user->update(['shop_id' => $shop_id]);
                }
            }
            return redirect()->route('backend.user.list')->withSuccess(trans('app.user_created'));
        }
        public function massadd(\Illuminate\Http\Request $request)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $count = \VanguardLTE\User::where([
                'shop_id' => auth()->user()->shop_id, 
                'role_id' => 1
            ])->count();
            if( isset($request->count) && is_numeric($request->count) && isset($request->balance) && is_numeric($request->balance) ) 
            {
                
                if( $this->max_users < ($count + $request->count) ) 
                {
                    return redirect()->route('backend.user.list')->withErrors([trans('max_users', ['max' => $this->max_users])]);
                }
                if( $request->balance > 0 ) 
                {
                    if( $shop->balance < ($request->count * $request->balance) ) 
                    {
                        return redirect()->back()->withErrors([trans('app.not_enough_money_in_the_shop', [
                            'name' => $shop->name, 
                            'balance' => $shop->balance
                        ])]);
                    }
                    $open_shift = \VanguardLTE\OpenShift::where([
                        'shop_id' => auth()->user()->shop_id, 
                        'user_id' => auth()->user()->id, 
                        'end_date' => null
                    ])->first();
                    if( !$open_shift ) 
                    {
                        return redirect()->back()->withErrors([trans('app.shift_not_opened')]);
                    }
                }
                if( auth()->user()->hasRole('cashier') ) 
                {
                    $role = \jeremykenedy\LaravelRoles\Models\Role::find(1);
                    for( $i = 0; $i < $request->count; $i++ ) 
                    {

                        $number = rand(111111111, 999999999);
                        $data = [
                            'username' => $number, 
                            'password' => $number, 
                            'role_id' => $role->id, 
                            'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE, 
                            'parent_id' => auth()->user()->id, 
                            'shop_id' => auth()->user()->shop_id
                        ];
                        
                        $newUser = $this->users->create($data);
                        $newUser->attachRole($role);
                        \VanguardLTE\ShopUser::create([
                            'shop_id' => auth()->user()->shop_id, 
                            'user_id' => $newUser->id
                        ]);
                      
                        if( $request->balance > 0 ) 
                        {
                            $newUser->addBalance('add', $request->balance);
                        }
                    }
                    auth()->user()->hierarchyUsers(false, true);
                }
            }
            return redirect()->route('backend.user.list')->withSuccess(trans('app.user_created'));
        }
        public function edit(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Activity\ActivityRepository $activitiesRepo, \VanguardLTE\User $user)
        {
            $edit = true;
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('level', '<=', auth()->user()->level())->pluck('name', 'id');
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            $shops = $user->shops();
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $userActivities = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                'user_id' => $user->id, 
                'type' => 'user'
            ])->orderBy('created_at', 'DESC')->paginate(30)->withQueryString();
            $users = auth()->user()->availableUsers();
            if( count($users) && !in_array($user->id, $users) ) 
            {
                abort(404);
            }
            if( auth()->user()->role_id < $user->role_id ) 
            {
                return redirect()->route('backend.user.list');
            }
            $hasActivities = $this->hasActivities($user);
            $langs = [];
            foreach( glob(resource_path() . '/lang/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $langs[$dirname] = $dirname;
            }
            if( $user->sms_token != '' ) 
            {
                $now = \Carbon\Carbon::now();
                $times = $now->diffInSeconds(\Carbon\Carbon::parse($user->sms_token_date), false);
                if( $times <= 0 ) 
                {
                    $user->update([
                        'phone' => '', 
                        'phone_verified' => 0, 
                        'sms_token' => ''
                    ]);
                }
            }
            $google2fa = app('pragmarx.google2fa');
            $QR_Image = '';
            $secret = $user->google2fa_secret;
            if( $user->google2fa_enable ) 
            {
                $secret = $google2fa->generateSecretKey();
                $QR_Image = $google2fa->getQRCodeInline(config('app.name'), $user->email, $secret);
            }
            $happyhour = false;
            if( $shop && $shop->happyhours_active ) 
            {
                $happyhour = \VanguardLTE\HappyHour::where([
                    'shop_id' => auth()->user()->shop_id, 
                    'time' => date('G')
                ])->first();
            }
            return view('backend.user.edit', compact('edit', 'user', 'roles', 'statuses', 'shops', 'userActivities', 'hasActivities', 'langs', 'QR_Image', 'secret', 'happyhour'));
        }
        public function send_phone_code()
        {
            $code = rand(11111, 99999);
            $sender = \VanguardLTE\Lib\SMS_sender::send(auth()->user()->phone, 'Verification code: ' . $code, auth()->user()->id);
            if( isset($sender['error']) ) 
            {
                if( isset($sender['text']) ) 
                {
                    return redirect()->back()->withErrors($sender['text']);
                }
                return redirect()->back()->withErrors('Error sending message');
            }
            if( !isset($sender['success']) ) 
            {
                return redirect()->back()->withErrors(__('app.something_went_wrong'));
            }
            if( !$sender['success'] ) 
            {
                return redirect()->back()->withErrors($sender['message']);
            }
            \VanguardLTE\SMS::create([
                'user_id' => auth()->user()->id, 
                'message' => $code, 
                'message_id' => $sender['message_id'], 
                'shop_id' => auth()->user()->shop_id, 
                'type' => 'verification', 
                'status' => 'Sent'
            ]);
            auth()->user()->update([
                'sms_token' => $code, 
                'sms_token_date' => \Carbon\Carbon::now()->addMinutes(settings('smsto_time'))
            ]);
            return redirect()->back()->withSuccess('Code sent');
        }
        public function updateDetails(\VanguardLTE\User $user, \VanguardLTE\Http\Requests\User\UpdateDetailsRequest $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $users = auth()->user()->availableUsers();
            $google2fa = app('pragmarx.google2fa');
            if( count($users) && !in_array($user->id, $users) ) 
            {
                abort(404);
            }
            if( auth()->user()->role_id < $user->role_id ) 
            {
                return redirect()->route('backend.user.list');
            }
            $data = $request->only([
                'email', 
                'username', 
                'language', 
                'shop_id', 
                'status', 
                'is_blocked', 
                'password', 
                'password_confirmation', 
                'google2fa_enable'
            ]);
            if( isset($request->secret_key) && isset($request->google_2fa_code) && $request->google_2fa_code != '' ) 
            {
                $code = $request->google_2fa_code;
                $key = $user->google2fa_secret;
                if( $user->google2fa_secret == null ) 
                {
                    $key = $request->secret_key;
                }
                $verify = $google2fa->verifyGoogle2FA($key, $code);
                if( $verify ) 
                {
                    if( $user->google2fa_enable ) 
                    {
                        $user->update(['google2fa_secret' => $key]);
                    }
                    else
                    {
                        $user->update([
                            'google2fa_secret' => null, 
                            'google2fa_enable' => 0
                        ]);
                    }
                    $google2fa->logout();
                }
                else
                {
                    return redirect()->route('backend.user.edit', $user->id)->withInput(['google_tab' => true])->withErrors(['Code is wrong']);
                }
            }
            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'username' => 'required|unique:users,username,' . $user->id, 
                'email' => 'nullable|unique:users,email,' . $user->id, 
                'phone' => 'nullable|unique:users,phone,' . $user->id
            ]);
            if( $validator->fails() ) 
            {
                return redirect()->route('backend.user.edit', $user->id)->withErrors($validator)->withInput();
            }
            $count = \VanguardLTE\User::where([
                'shop_id' => auth()->user()->shop_id, 
                'role_id' => 1
            ])->count();
            if( empty($data['password']) || empty($data['password_confirmation']) ) 
            {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            if( !(auth()->user()->hasRole('admin') && $user->hasRole([
                'agent', 
                'distributor'
            ])) ) 
            {
                unset($data['is_blocked']);
            }
            else if( isset($data['is_blocked']) ) 
            {
                $users = \VanguardLTE\User::whereIn('id', [$user->id] + $user->hierarchyUsers())->get();
                if( $users ) 
                {
                    foreach( $users as $userElem ) 
                    {
                        \DB::table('sessions')->where('user_id', $userElem->id)->delete();
                        $userElem->update([
                            'remember_token' => null, 
                            'is_blocked' => $data['is_blocked']
                        ]);
                    }
                }
                $myShops = \VanguardLTE\Shop::whereIn('id', $user->availableShops())->get();
                if( $myShops ) 
                {
                    foreach( $myShops as $myShop ) 
                    {
                        $myShop->update(['is_blocked' => $data['is_blocked']]);
                    }
                }
            }
            if( $request->status != $user->status ) 
            {
                if( $request->status == \VanguardLTE\Support\Enum\UserStatus::ACTIVE && $user->status == \VanguardLTE\Support\Enum\UserStatus::BANNED ) 
                {
                    event(new \VanguardLTE\Events\User\UserUnBanned($user));
                }
                if( $request->status == \VanguardLTE\Support\Enum\UserStatus::ACTIVE && $user->status == \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED ) 
                {
                    event(new \VanguardLTE\Events\User\UserConfirmed($user));
                }
                if( $request->status == \VanguardLTE\Support\Enum\UserStatus::BANNED ) 
                {
                    event(new \VanguardLTE\Events\User\Banned($user));
                }
            }
            if( isset($data['email']) && !$user->hasRole('admin') && ($return = \VanguardLTE\Lib\Filter::domain_filtered($data['email'])) ) 
            {
                return redirect()->route('backend.user.edit', $user->id)->withErrors([__('app.blocked_domain_zone', ['zone' => $return['domain']])]);
            }
            if( isset($request->phone) && $request->phone ) 
            {
                $phone = preg_replace('/[^0-9]/', '', $request->phone);
                $code = null;
                if( $phone != '' && !$user->phone ) 
                {
                    $code = rand(1111, 9999);
                    $data['phone'] = $phone;
                }
                if( $user->phone && $user->phone != $phone && !$user->phone_verified ) 
                {
                    $code = rand(1111, 9999);
                    $data['phone'] = $phone;
                }
                if( $user->phone_verified && auth()->user()->hasRole('admin') && $user->phone != $phone ) 
                {
                    $code = rand(1111, 9999);
                    $data['phone'] = $phone;
                    $data['phone_verified'] = 0;
                }
                if( $code ) 
                {
                    $sender = \VanguardLTE\Lib\SMS_sender::send($phone, 'Verification code: ' . $code, $user->id);
                    $this->users->update($user->id, [
                        'sms_token' => $code, 
                        'sms_token_date' => \Carbon\Carbon::now()->addMinutes(settings('smsto_time'))
                    ]);
                    if( isset($sender['message_id']) ) 
                    {
                        \VanguardLTE\SMS::create([
                            'user_id' => $user->id, 
                            'message' => $code, 
                            'message_id' => $sender['message_id'], 
                            'shop_id' => $user->shop_id, 
                            'type' => 'verification', 
                            'status' => 'Sent'
                        ]);
                    }
                }
            }
            else
            {
                $data['phone'] = '';
                $data['phone_verified'] = 0;
                $data['sms_token'] = null;
            }
            $this->users->update($user->id, $data);
            if( $user->hasRole([
                'distributor', 
                'cashier', 
                'user'
            ]) && $request->shops && count($request->shops) ) 
            {
                foreach( $request->shops as $shop ) 
                {
                    \VanguardLTE\ShopUser::create([
                        'shop_id' => $shop, 
                        'user_id' => $user->id
                    ]);
                }
            }
            if( $request->sms_token ) 
            {
                if( $request->sms_token == $user->sms_token ) 
                {
                    $now = \Carbon\Carbon::now();
                    $seconds = $now->diffInSeconds(\Carbon\Carbon::parse($user->sms_token_date), false);
                    if( $seconds <= 0 ) 
                    {
                        return redirect()->route('backend.user.edit', $user->id)->withErrors(trans('app.time_is_up'));
                    }
                    $user->update([
                        'sms_token' => null, 
                        'phone_verified' => 1
                    ]);
                    return redirect()->route('backend.user.edit', $user->id)->withSuccess(trans('app.phone_verified'));
                }
                else
                {
                    return redirect()->route('backend.user.edit', $user->id)->withErrors(trans('app.phone_verification_code_is_wrong'));
                }
            }
            event(new \VanguardLTE\Events\User\UpdatedByAdmin($user));
            if( $this->userIsBanned($user, $request) ) 
            {
                event(new \VanguardLTE\Events\User\Banned($user));
            }
            return redirect()->route('backend.user.edit', $user->id)->withSuccess(trans('app.user_updated'));
        }
        public function updateBalance(\Illuminate\Http\Request $request)
        {
            $data = $request->all();
            if( !array_get($data, 'type') ) 
            {
                $data['type'] = 'add';
            }
            if( auth()->user()->hasRole('admin') && auth()->user()->google2fa_secret != null && auth()->user()->google2fa_enable ) 
            {
                if( !$request->google_2fa_code ) 
                {
                    return redirect()->back()->withErrors([__('app.wrong_code') . ' ' . __('app.google_2fa')]);
                }
                $google2fa = app('pragmarx.google2fa');
                $verify = $google2fa->verifyGoogle2FA(auth()->user()->google2fa_secret, $request->google_2fa_code);
                if( !$verify ) 
                {
                    return redirect()->back()->withErrors([__('app.wrong_code') . ' ' . __('app.google_2fa')]);
                }
            }
            $user = \VanguardLTE\User::find($request->user_id);
            if( !$user ) 
            {
                return redirect()->back()->withErrors([__('app.wrong_user')]);
            }
            $request->summ = floatval($request->summ);
            if( $request->all && $request->all == '1' ) 
            {
                $request->summ = $user->balance;
            }
            $result = $user->addBalance($data['type'], $request->summ);
            $result = json_decode($result, true);
            if( $data['type'] == 'add' ) 
            {
                event(new \VanguardLTE\Events\User\MoneyIn($user, $request->summ));
            }
            else
            {
                event(new \VanguardLTE\Events\User\MoneyOut($user, $request->summ));
            }
            if( $result['status'] == 'error' ) 
            {
                return redirect()->back()->withErrors([$result['message']]);
            }
            return redirect()->back()->withSuccess($result['message']);
        }

        public function updateLimit(\Illuminate\Http\Request $request)
        {
           
            $data = $request->all();
            if( !array_get($data, 'type') ) 
            {
                $data['type'] = 'add';
            }
            if( auth()->user()->hasRole('admin') && auth()->user()->google2fa_secret != null && auth()->user()->google2fa_enable ) 
            {
                dd('i am an admin with 2fa');
                if( !$request->google_2fa_code ) 
                {
                    return redirect()->back()->withErrors([__('app.wrong_code') . ' ' . __('app.google_2fa')]);
                }
                $google2fa = app('pragmarx.google2fa');
                $verify = $google2fa->verifyGoogle2FA(auth()->user()->google2fa_secret, $request->google_2fa_code);
                if( !$verify ) 
                {
                    return redirect()->back()->withErrors([__('app.wrong_code') . ' ' . __('app.google_2fa')]);
                }
            }
            $user = \VanguardLTE\User::find($request->user_id);

            if( !$user ) 
            {
                return redirect()->back()->withErrors([__('app.wrong_user')]);
            }
            $request->summ = floatval($request->summ);
            if( $request->all && $request->all == '1' ) 
            {
                $request->summ = $user->balance;
            }
            $result = $user->addLimit($data['type'], $request->summ);
            $result = json_decode($result, true);
            if( $result['status'] == 'error' ) 
            {
                return redirect()->back()->withErrors([$result['message']]);
            }
            return redirect()->back()->withSuccess($result['message']);
        }

        public function statistics(\VanguardLTE\User $user, \Illuminate\Http\Request $request)
        {
            $statistics = \VanguardLTE\Statistic::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(20)->withQueryString();
            return view('backend.stat.pay_stat', compact('user', 'statistics'));
        }
        private function userIsBanned(\VanguardLTE\User $user, \Illuminate\Http\Request $request)
        {
            return $user->status != $request->status && $request->status == \VanguardLTE\Support\Enum\UserStatus::BANNED;
        }
        public function specauth(\Illuminate\Http\Request $request, \VanguardLTE\User $user)
        {
            if( !$user ) 
            {
                return redirect()->route('backend.auth.login')->withErrors([trans('app.wrong_user')]);
            }
            if( $user->auth_token == $request->token && auth()->user()->hasRole('admin') && !$user->hasRole('admin') ) 
            {
                if( auth()->user()->shop && auth()->user()->shop->pending ) 
                {
                    return redirect()->route('backend.dashboard')->withErrors(__('app.shop_is_creating'));
                }
                session(['beforeUser' => auth()->user()->id]);
                \Illuminate\Support\Facades\Auth::loginUsingId($user->id);
                if( !$user->hasRole('user') ) 
                {
                    if( !auth()->user()->hasPermission('dashboard') ) 
                    {
                        return redirect()->route('backend.user.list');
                    }
                    return redirect()->route('backend.dashboard');
                }
                return redirect()->intended();
            }
            return redirect()->route('backend.auth.login')->withErrors([trans('app.wrong_user')]);
        }
        public function back_login(\Illuminate\Http\Request $request)
        {
            if( $request->session()->exists('beforeUser') ) 
            {
                \Illuminate\Support\Facades\Auth::loginUsingId(session('beforeUser'));
                $request->session()->forget('beforeUser');
                return redirect()->route('backend.dashboard');
            }
            return redirect()->route('backend.dashboard')->withErrors([trans('app.wrong_user')]);
        }
        public function updateAvatar(\VanguardLTE\User $user, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager, \Illuminate\Http\Request $request)
        {
            $this->validate($request, ['avatar' => 'image']);
            $name = $avatarManager->uploadAndCropAvatar($user, $request->file('avatar'), $request->get('points'));
            if( $name ) 
            {
                $this->users->update($user->id, ['avatar' => $name]);
                event(new \VanguardLTE\Events\User\UpdatedByAdmin($user));
                return redirect()->route('backend.user.edit', $user->id)->withSuccess(trans('app.avatar_changed'));
            }
            return redirect()->route('backend.user.edit', $user->id)->withErrors(trans('app.avatar_not_changed'));
        }
        public function updateAvatarExternal(\VanguardLTE\User $user, \Illuminate\Http\Request $request, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager)
        {
            $avatarManager->deleteAvatarIfUploaded($user);
            $this->users->update($user->id, ['avatar' => $request->get('url')]);
            event(new \VanguardLTE\Events\User\UpdatedByAdmin($user));
            return redirect()->route('backend.user.edit', $user->id)->withSuccess(trans('app.avatar_changed'));
        }
        public function updateLoginDetails(\VanguardLTE\User $user, \VanguardLTE\Http\Requests\User\UpdateLoginDetailsRequest $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $data = $request->all();
            if( trim($data['password']) == '' ) 
            {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            if( !(auth()->user()->hasRole('admin') && $user->hasRole([
                'agent', 
                'distributor'
            ])) ) 
            {
                unset($data['is_blocked']);
            }
            else
            {
                $users = \VanguardLTE\User::whereIn('id', [$user->id] + $user->hierarchyUsers())->get();
                if( $users ) 
                {
                    foreach( $users as $userElem ) 
                    {
                        \DB::table('sessions')->where('user_id', $userElem->id)->delete();
                        $userElem->update([
                            'remember_token' => null, 
                            'is_blocked' => 1
                        ]);
                    }
                }
            }
            $this->users->update($user->id, $data);
            event(new \VanguardLTE\Events\User\UpdatedByAdmin($user));
            return redirect()->route('backend.user.edit', $user->id)->withSuccess(trans('app.login_updated'));
        }
        public function delete(\VanguardLTE\User $user)
        {
            if( $user->id == auth()->user()->id ) 
            {
                return redirect()->route('backend.user.list')->withErrors(trans('app.you_cannot_delete_yourself'));
            }
            if( $user->balance > 0 ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.balance_not_zero')]);
            }
            if( (auth()->user()->hasRole('admin') && $user->hasRole('agent') || auth()->user()->hasRole('agent') && $user->hasRole('distributor') || auth()->user()->hasRole('distributor') && $user->hasRole('manager')) && ($count = \VanguardLTE\User::where('parent_id', $user->id)->count()) ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.has_users', ['name' => $user->username])]);
            }
            if( (auth()->user()->hasRole('admin') && $user->hasRole('agent') || auth()->user()->hasRole('agent') && $user->hasRole('distributor') || auth()->user()->hasRole('distributor') && $user->hasRole('manager') || auth()->user()->hasRole('manager') && $user->hasRole('cashier')) && $this->hasActivities($user) ) 
            {
                return redirect()->route('backend.user.list')->withErrors([trans('app.has_stats', ['name' => $user->username])]);
            }
            $user->detachAllRoles();
            \VanguardLTE\Statistic::where('user_id', $user->id)->delete();
            \VanguardLTE\StatisticAdd::where('user_id', $user->id)->delete();
            \VanguardLTE\ShopUser::where('user_id', $user->id)->delete();
            \VanguardLTE\StatGame::where('user_id', $user->id)->delete();
            \VanguardLTE\GameLog::where('user_id', $user->id)->delete();
            \VanguardLTE\UserActivity::where('user_id', $user->id)->delete();
            \VanguardLTE\Session::where('user_id', $user->id)->delete();
            \VanguardLTE\Info::where('user_id', $user->id)->delete();
            $user->delete();
            return redirect()->route('backend.user.list')->withSuccess(trans('app.user_deleted'));
        }
        public function hard_delete(\VanguardLTE\User $user)
        {
            if( $user->id == auth()->user()->id ) 
            {
                return redirect()->route('backend.user.list')->withErrors(trans('app.you_cannot_delete_yourself'));
            }
            if( !(auth()->user()->hasRole('admin') && $user->hasRole([
                'agent', 
                'distributor'
            ])) ) 
            {
                abort(403);
            }
            if( $user->hasRole('agent') ) 
            {
                $distributors = \VanguardLTE\User::where([
                    'parent_id' => $user->id, 
                    'role_id' => 4
                ])->get();
            }
            if( $user->hasRole('distributor') ) 
            {
                $distributors = \VanguardLTE\User::where(['id' => $user->id])->get();
            }
            if( $distributors ) 
            {
                foreach( $distributors as $distributor ) 
                {
                    if( $distributor->rel_shops ) 
                    {
                        foreach( $distributor->rel_shops as $shop ) 
                        {
                            $shop->shop->delete();
                            \VanguardLTE\Task::create([
                                'category' => 'shop', 
                                'action' => 'delete', 
                                'item_id' => $shop->shop_id, 
                                'user_id' => auth()->user()->id, 
                                'shop_id' => auth()->user()->shop_id
                            ]);
                            $usersToDelete = \VanguardLTE\User::whereIn('role_id', [
                                1, 
                                2, 
                                3
                            ])->where('shop_id', $shop->shop_id)->get();
                            if( $usersToDelete ) 
                            {
                                foreach( $usersToDelete as $userDelete ) 
                                {
                                    $userDelete->delete();
                                }
                            }
                        }
                    }
                    $distributor->delete();
                }
            }
            if( $user->hasRole('agent') ) 
            {
                $user->delete();
            }
            if( auth()->user()->hasRole('admin') ) 
            {
                $admin = \VanguardLTE\User::find(auth()->user()->id);
                $admin->update(['shop_id' => 0]);
                \VanguardLTE\Jobs\UpdateTreeCache::dispatch($admin->hierarchyUsers());
            }
            return redirect()->route('backend.user.list')->withSuccess(trans('app.user_deleted'));
        }
        public function hasActivities($user)
        {
            if( $user->hasRole([
                'distributor', 
                'manager', 
                'cashier'
            ]) ) 
            {
                $stats = \VanguardLTE\Statistic::where('user_id', $user->id)->count();
                if( $stats ) 
                {
                    return true;
                }
                $stats = \VanguardLTE\StatGame::where('user_id', $user->id)->count();
                if( $stats ) 
                {
                    return true;
                }
                $open_shifts = \VanguardLTE\OpenShift::where('user_id', $user->id)->count();
                if( $open_shifts ) 
                {
                    return true;
                }
            }
            return false;
        }
        public function sessions(\VanguardLTE\User $user, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $adminView = true;
            $sessions = $sessionRepository->getUserSessions($user->id);
            return view('backend.user.sessions', compact('sessions', 'user', 'adminView'));
        }
        public function invalidateSession(\VanguardLTE\User $user, $session, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $sessionRepository->invalidateSession($session->id);
            return redirect()->route('backend.user.sessions', $user->id)->withSuccess(trans('app.session_invalidated'));
        }
        public function action($action)
        {
            if( !auth()->user()->hasRole('cashier') ) 
            {
                abort(403);
            }
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => auth()->user()->shop_id, 
                'user_id' => auth()->user()->id, 
                'end_date' => null
            ])->first();
            if( !$open_shift ) 
            {
                return redirect()->back()->withErrors([trans('app.shift_not_opened')]);
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( $action && in_array($action, ['users_out']) ) 
            {
                switch( $action ) 
                {
                    case 'users_out':
                        $users = \VanguardLTE\User::where('shop_id', $shop->id)->get();
                        foreach( $users as $user ) 
                        {
                            $sum = $user->balance;
                            if( $sum <= 0 ) 
                            {
                                continue;
                            }
                            $user->addBalance('out', $sum, $user->referral);
                        }
                        return redirect()->back()->withSuccess(trans('app.balance_updated'));
                        break;
                }
            }
        }
    }

}
