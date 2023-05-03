<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ShopsController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $max_shops = 200;
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:shops.manage');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $shops = \VanguardLTE\Shop::select('shops.*', 'shops.id AS shop_id');
            if( $shopIds = auth()->user()->shops(true) ) 
            {
                $shops = $shops->whereIn('shops.id', $shopIds);
            }
            else
            {
                $shops = $shops->where('shops.id', 0);
            }
            if( $request->name != '' ) 
            {
                $shops = $shops->where('shops.name', 'LIKE', '%' . $request->name . '%');
            }
            if( $request->credit_from != '' ) 
            {
                $shops = $shops->where('shops.balance', '>=', $request->credit_from);
            }
            if( $request->credit_to != '' ) 
            {
                $shops = $shops->where('shops.balance', '<=', $request->credit_to);
            }
            if( $request->frontend != '' ) 
            {
                $shops = $shops->where('shops.frontend', $request->frontend);
            }
            if( $request->percent_from != '' ) 
            {
                $shops = $shops->where('shops.percent', '>=', $request->percent_from);
            }
            if( $request->percent_to != '' ) 
            {
                $shops = $shops->where('shops.percent', '<=', $request->percent_to);
            }
            if( $request->order != '' ) 
            {
                $shops = $shops->where('shops.orderby', $request->order);
            }
            if( $request->currency != '' ) 
            {
                $shops = $shops->where('shops.currency', $request->currency);
            }
            if( $request->status != '' ) 
            {
                $shops = $shops->where('shops.is_blocked', !$request->status);
            }
            if( $request->categories ) 
            {
                $shops = $shops->join('shop_categories', 'shop_categories.shop_id', '=', 'shops.id');
                $shops = $shops->whereIn('shop_categories.category_id', $request->categories);
            }
            if( $request->users != '' ) 
            {
                $request->users = str_replace('_', '\_', $request->users);
                $shops = $shops->join('shops_user', 'shops_user.shop_id', '=', 'shops.id');
                $tempUsers = \VanguardLTE\User::whereIn('id', auth()->user()->availableUsers())->where('username', 'LIKE', '%' . $request->users . '%')->get();
                if( $tempUsers ) 
                {
                    $shops = $shops->whereIn('shops_user.user_id', $tempUsers->pluck('id'));
                }
                else
                {
                    $shops = $shops->where('shops_user.user_id', 0);
                }
            }
            $shops = $shops->groupBy('shops.id')->paginate(15)->withQueryString();
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            $directories = [];
            foreach( glob(public_path() . '/frontend/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            $stats = [
                'shops' => $shops->count(), 
                'agents' => 1, 
                'distributors' => 0, 
                'managers' => 0, 
                'cashiers' => 0, 
                'users' => 0, 
                'credit' => $shops->sum('balance')
            ];
            $countAgents = [];
            $countDistributors = [];
            if( $shops ) 
            {
                foreach( $shops as $shop ) 
                {
                    if( $shop->users ) 
                    {
                        foreach( $shop->users as $user ) 
                        {
                            if( $user = $user->user ) 
                            {
                                if( $user->hasRole('agent') ) 
                                {
                                    $countAgents[$user->username] = 1;
                                }
                                if( $user->hasRole('distributor') ) 
                                {
                                    $countDistributors[$user->username] = 1;
                                }
                                if( $user->hasRole('manager') ) 
                                {
                                    $stats['managers']++;
                                }
                                if( $user->hasRole('cashier') ) 
                                {
                                    $stats['cashiers']++;
                                }
                                if( $user->hasRole('user') ) 
                                {
                                    $stats['users']++;
                                }
                            }
                        }
                    }
                }
            }
            if( auth()->user()->hasRole('admin') ) 
            {
                $stats['agents'] = \VanguardLTE\User::where('role_id', 5)->count();
                $stats['distributors'] = \VanguardLTE\User::where('role_id', 4)->count();
            }
            if( auth()->user()->hasRole('agent') ) 
            {
                $stats['distributors'] = \VanguardLTE\User::where([
                    'role_id' => 4, 
                    'parent_id' => auth()->user()->id
                ])->count();
            }
            if( auth()->user()->hasRole('distributor') ) 
            {
                $stats['distributors'] = 1;
            }
            if( auth()->user()->hasRole('manager') ) 
            {
                $stats['distributors'] = 1;
            }
            if( count($request->all()) ) 
            {
                $stats['agents'] = count($countAgents);
                $stats['distributors'] = count($countDistributors);
            }
            $agents = \VanguardLTE\User::where('role_id', 5)->pluck('username', 'id')->toArray();
            $distributors = [];
            if( auth()->user()->hasRole(['admin']) ) 
            {
                $distributors = \VanguardLTE\User::where('role_id', 4)->pluck('username', 'id')->toArray();
            }
            else if( auth()->user()->hasRole(['agent']) ) 
            {
                $distributors = \VanguardLTE\User::where([
                    'role_id' => 4, 
                    'parent_id' => auth()->user()->id
                ])->pluck('username', 'id')->toArray();
            }
            return view('backend.shops.list', compact('shops', 'categories', 'stats', 'agents', 'distributors', 'directories'));
        }
        public function create()
        {
            $directories = [];
            foreach( glob(public_path() . '/frontend/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            $shop = new \VanguardLTE\Shop();
            $availibleUsers = [];
            if( auth()->user()->hasRole('admin') ) 
            {
                $me = \VanguardLTE\User::where('id', auth()->user()->id)->get();
                $availibleUsers = \VanguardLTE\User::whereIn('role_id', [
                    4, 
                    5
                ])->has('rel_shops')->get();
                $availibleUsers = $me->merge($availibleUsers);
            }
            if( auth()->user()->hasRole('agent') ) 
            {
                $me = \VanguardLTE\User::where('id', auth()->user()->id)->get();
                $distributors = \VanguardLTE\User::where([
                    'parent_id' => auth()->user()->id, 
                    'role_id' => 4
                ])->has('rel_shops')->get();
                $availibleUsers = $me->merge($distributors);
            }
            if( auth()->user()->hasRole('distributor') ) 
            {
                $availibleUsers = \VanguardLTE\User::where('id', auth()->user()->id)->has('rel_shops')->get();
            }
            $blocks = [];
            if( auth()->user()->hasPermission('shops.unblock') ) 
            {
                $blocks[0] = __('app.unblock');
            }
            if( auth()->user()->hasPermission('shops.block') ) 
            {
                $blocks[1] = __('app.block');
            }
            return view('backend.shops.add', compact('directories', 'categories', 'shop', 'availibleUsers', 'blocks'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( $this->max_shops <= \VanguardLTE\Shop::count() ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('max_shops', ['max' => config('limits.max_shops')])]);
            }
            $validatedData = $request->validate([
                'name' => 'required|unique:shops|max:255', 
                'currency' => 'present|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'orderby' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['orderby'])
            ]);
            $data = $request->only([
                'name', 
                'percent', 
                'frontend', 
                'currency', 
                'is_blocked', 
                'orderby', 
                'access', 
                'max_win', 
                'shop_limit', 
                'rules_terms_and_conditions', 
                'rules_privacy_policy', 
                'rules_general_bonus_policy', 
                'rules_why_bitcoin', 
                'rules_responsible_gaming'
            ]);
            $temp = $request->only([
                'country', 
                'os', 
                'device'
            ]);
            if( count($temp) ) 
            {
                foreach( $temp as $key => $item ) 
                {
                    $data[$key] = implode(',', $item);
                }
            }
            $shop = \VanguardLTE\Shop::create($data + ['user_id' => auth()->user()->id]);
            $user = \VanguardLTE\User::find(auth()->user()->id);
            $progress = \VanguardLTE\Progress::where('shop_id', 0)->get();
            if( count($progress) ) 
            {
                foreach( $progress as $item ) 
                {
                    $newProgress = $item->replicate();
                    $newProgress->shop_id = $shop->id;
                    $newProgress->save();
                }
            }
            $welcomebonuses = \VanguardLTE\WelcomeBonus::where('shop_id', 0)->get();
            if( count($welcomebonuses) ) 
            {
                foreach( $welcomebonuses as $item ) 
                {
                    $newWelcomeBonus = $item->replicate();
                    $newWelcomeBonus->shop_id = $shop->id;
                    $newWelcomeBonus->save();
                }
            }
            $smsbonuses = \VanguardLTE\SMSBonus::where('shop_id', 0)->get();
            if( count($smsbonuses) ) 
            {
                foreach( $smsbonuses as $item ) 
                {
                    $newSMSBonus = $item->replicate();
                    $newSMSBonus->shop_id = $shop->id;
                    $newSMSBonus->save();
                }
            }
            if( $request->country ) 
            {
                foreach( $request->country as $country ) 
                {
                    \VanguardLTE\ShopCountry::create([
                        'shop_id' => $shop->id, 
                        'country' => $country
                    ]);
                }
            }
            if( $request->os ) 
            {
                foreach( $request->os as $os ) 
                {
                    \VanguardLTE\ShopOS::create([
                        'shop_id' => $shop->id, 
                        'os' => $os
                    ]);
                }
            }
            if( $request->device ) 
            {
                foreach( $request->device as $device ) 
                {
                    \VanguardLTE\ShopDevice::create([
                        'shop_id' => $shop->id, 
                        'device' => $device
                    ]);
                }
            }
            if( isset($request->categories) && count($request->categories) ) 
            {
                foreach( $request->categories as $category ) 
                {
                    \VanguardLTE\ShopCategory::create([
                        'shop_id' => $shop->id, 
                        'category_id' => $category
                    ]);
                }
            }
            \VanguardLTE\ShopUser::create([
                'shop_id' => $shop->id, 
                'user_id' => auth()->user()->id
            ]);
            if( auth()->user()->hasRole('distributor') ) 
            {
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => auth()->user()->parent_id
                ]);
            }
            $user->update(['shop_id' => $shop->id]);
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'create', 
                'item_id' => $shop->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_created'));
        }
        public function fast_shop()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.only_for_distributors')]);
            }
            $sleep = 0;
            $rand = rand(111111111, 999999999);
            $shop = [
                'name' => 'DEMO_' . $rand, 
                'percent' => 90, 
                'frontend' => 'Default', 
                'orderby' => 'AZ', 
                'currency' => 'USD', 
                'access' => 1
            ];
            $agent = [
                'username' => 'A_' . $rand, 
                'password' => 'A_' . $rand, 
                'status' => 'Active'
            ];
            $distributor = [
                'username' => 'D_' . $rand, 
                'balance' => 0, 
                'password' => 'D_' . $rand, 
                'status' => 'Active'
            ];
            $manager = [
                'username' => 'M_' . $rand, 
                'password' => 'M_' . $rand, 
                'status' => 'Active'
            ];
            $cashier = [
                'username' => 'C_' . $rand, 
                'password' => 'C_' . $rand, 
                'status' => 'Active'
            ];
            $users = ['count' => 10];
            $distributorBalance = 5000;
            $agentBalance = 10000;
            $shopBalance = 5000;
            $userBalance = 100;
            $roles = \jeremykenedy\LaravelRoles\Models\Role::get();
            $sleep++;
            $agent = \VanguardLTE\User::create($agent + [
                'parent_id' => auth()->user()->id, 
                'role_id' => 5, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $agent->attachRole($roles->find(5));
            $sleep++;
            $distributor = \VanguardLTE\User::create($distributor + [
                'parent_id' => $agent->id, 
                'role_id' => 4, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $distributor->attachRole($roles->find(4));
            $sleep++;
            $manager = \VanguardLTE\User::create($manager + [
                'parent_id' => $distributor->id, 
                'role_id' => 3, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $manager->attachRole($roles->find(3));
            $sleep++;
            $cashier = \VanguardLTE\User::create($cashier + [
                'parent_id' => $manager->id, 
                'role_id' => 2, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $cashier->attachRole($roles->find(2));
            $shop = \VanguardLTE\Shop::create($shop + ['user_id' => $distributor->id]);
            $progress = \VanguardLTE\Progress::where('shop_id', 0)->get();
            if( count($progress) ) 
            {
                foreach( $progress as $item ) 
                {
                    $newProgress = $item->replicate();
                    $newProgress->shop_id = $shop->id;
                    $newProgress->save();
                }
            }
            $welcomebonuses = \VanguardLTE\WelcomeBonus::where('shop_id', 0)->get();
            if( count($welcomebonuses) ) 
            {
                foreach( $welcomebonuses as $item ) 
                {
                    $newWelcomeBonus = $item->replicate();
                    $newWelcomeBonus->shop_id = $shop->id;
                    $newWelcomeBonus->save();
                }
            }
            $smsbonuses = \VanguardLTE\SMSBonus::where('shop_id', 0)->get();
            if( count($smsbonuses) ) 
            {
                foreach( $smsbonuses as $item ) 
                {
                    $newSMSBonus = $item->replicate();
                    $newSMSBonus->shop_id = $shop->id;
                    $newSMSBonus->save();
                }
            }
            $open_shift = \VanguardLTE\OpenShift::create([
                'start_date' => \Carbon\Carbon::now(), 
                'balance' => 0, 
                'user_id' => $cashier->id, 
                'shop_id' => $shop->id
            ]);
            if( $agentBalance > 0 ) 
            {
                $agent->addBalance('add', $agentBalance);
            }
            if( $distributorBalance > 0 ) 
            {
                $distributor->addBalance('add', $distributorBalance, $agent);
            }
            if( $shopBalance > 0 ) 
            {
                $open_shift->increment('balance_in', $shopBalance);
                $distributor->decrement('balance', $shopBalance);
                $shop->increment('balance', $shopBalance);
                \VanguardLTE\Statistic::create([
                    'user_id' => $distributor->id, 
                    'shop_id' => $shop->id, 
                    'sum' => $shopBalance, 
                    'type' => 'add', 
                    'system' => 'shop'
                ]);
            }
            foreach( [
                $agent, 
                $distributor, 
                $manager, 
                $cashier
            ] as $user ) 
            {
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $user->id
                ]);
                $user->update(['shop_id' => $shop->id]);
            }
            $role = \jeremykenedy\LaravelRoles\Models\Role::find(1);
            for( $i = 0; $i < $users['count']; $i++ ) 
            {
                $sleep++;
                $number = rand(111111111, 999999999);
                $data = [
                    'username' => $number, 
                    'password' => $number, 
                    'role_id' => $role->id, 
                    'status' => 'Active', 
                    'shop_id' => $shop->id, 
                    'parent_id' => $cashier->id, 
                    'created_at' => time() + $sleep
                ];
                $newUser = \VanguardLTE\User::create($data);
                $newUser->attachRole($role);
                if( $userBalance > 0 ) 
                {
                    $newUser->addBalance('add', $userBalance, $cashier);
                }
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $newUser->id
                ]);
                $newUser->update(['shop_id' => $shop->id]);
            }
            foreach( [0] as $category ) 
            {
                \VanguardLTE\ShopCategory::create([
                    'shop_id' => $shop->id, 
                    'category_id' => $category
                ]);
            }
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'create', 
                'item_id' => $shop->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_created'));
        }
        public function admin_create()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.only_for_distributors')]);
            }
            $directories = [];
            foreach( glob(public_path() . '/frontend/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            $categories = \VanguardLTE\Category::where([
                'parent' => 0, 
                'shop_id' => 0
            ])->get();
            $shop = new \VanguardLTE\Shop();
            $availibleUsers = [];
            if( auth()->user()->hasRole('admin') ) 
            {
                $me = \VanguardLTE\User::where('id', auth()->user()->id)->get();
                $availibleUsers = \VanguardLTE\User::whereIn('role_id', [
                    4, 
                    5
                ])->has('rel_shops')->get();
                $availibleUsers = $me->merge($availibleUsers);
            }
            $blocks = [];
            if( auth()->user()->hasPermission('shops.unblock') ) 
            {
                $blocks[0] = __('app.unblock');
            }
            if( auth()->user()->hasPermission('shops.block') ) 
            {
                $blocks[1] = __('app.block');
            }
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            return view('backend.shops.admin', compact('directories', 'categories', 'shop', 'availibleUsers', 'blocks', 'statuses'));
        }
        public function admin_store(\Illuminate\Http\Request $request)
        {
            $sleep = 0;
            $shop = $request->only([
                'name', 
                'percent', 
                'frontend', 
                'orderby', 
                'currency', 
                'max_win', 
                'categories', 
                'balance', 
                'country', 
                'city', 
                'os', 
                'device', 
                'access', 
                'shop_limit', 
                'rules_terms_and_conditions', 
                'rules_privacy_policy', 
                'rules_general_bonus_policy', 
                'rules_why_bitcoin', 
                'rules_responsible_gaming'
            ]);
            $agent = $request->input('agent');
            $distributor = $request->input('distributor');
            $manager = $request->input('manager');
            $cashier = $request->input('cashier');
            $users = $request->input('users');
            if( $this->max_shops <= \VanguardLTE\Shop::count() ) 
            {
                return redirect()->back()->with('blockError', 'SHOP')->withErrors([trans('max_shops', ['max' => config('limits.max_shops')])])->withInput();
            }
            $request->validate([
                'name' => 'required|unique:shops|max:255', 
                'currency' => 'present|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'orderby' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['orderby'])
            ]);
            foreach( [
                'agent', 
                'distributor', 
                'manager', 
                'cashier'
            ] as $role_name ) 
            {
                $validator = \Illuminate\Support\Facades\Validator::make($request->input($role_name), [
                    'username' => 'required|regex:/^[A-Za-z0-9]+$/|unique:users,username', 
                    'password' => 'required|min:6'
                ]);
                if( $validator->fails() ) 
                {
                    return redirect()->back()->withErrors($validator)->with('blockError', $role_name)->withInput();
                }
            }
            $validator = \Illuminate\Support\Facades\Validator::make($users, [
                'count' => 'required', 
                'balance' => 'required'
            ]);
            if( $validator->fails() ) 
            {
                return redirect()->back()->withErrors($validator)->with('blockError', 'Users')->withInput();
            }
            $usersBalance = floatval($users['balance'] * $users['count']);
            $distributorBalance = floatval($distributor['balance']);
            $agentBalance = floatval($agent['balance']);
            $shopBalance = floatval($shop['balance']);
            $shop['balance'] = 0;
            $manager['balance'] = $shop['balance'];
            $distributor['balance'] = $manager['balance'];
            $agent['balance'] = $distributor['balance'];
            if( $usersBalance < 0 || $distributorBalance < 0 || $agentBalance < 0 || $shopBalance < 0 ) 
            {
                return redirect()->back()->withErrors(['Error balance < 0'])->withInput();
            }
            if( $usersBalance > 0 && ($shopBalance <= 0 || $shopBalance < $usersBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Users > Shop'])->withInput();
            }
            if( $shopBalance > 0 && ($distributorBalance <= 0 || $distributorBalance < $shopBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Manager+shop > Distributor'])->withInput();
            }
            if( $distributorBalance > 0 && ($agentBalance <= 0 || $agentBalance < $distributorBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Distributor > Agent'])->withInput();
            }
            $roles = \jeremykenedy\LaravelRoles\Models\Role::get();
            $sleep++;
            $agent = \VanguardLTE\User::create($agent + [
                'parent_id' => auth()->user()->id, 
                'role_id' => 5, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $agent->attachRole($roles->find(5));
            $sleep++;
            $distributor = \VanguardLTE\User::create($distributor + [
                'parent_id' => $agent->id, 
                'role_id' => 4, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $distributor->attachRole($roles->find(4));
            $sleep++;
            $manager = \VanguardLTE\User::create($manager + [
                'parent_id' => $distributor->id, 
                'role_id' => 3, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $manager->attachRole($roles->find(3));
            $sleep++;
            $cashier = \VanguardLTE\User::create($cashier + [
                'parent_id' => $manager->id, 
                'role_id' => 2, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $cashier->attachRole($roles->find(2));
            $temp = $request->only([
                'country', 
                'os', 
                'device'
            ]);
            if( count($temp) ) 
            {
                foreach( $temp as $key => $item ) 
                {
                    $shop[$key] = implode(',', $item);
                }
            }
            $shop = \VanguardLTE\Shop::create($shop + ['user_id' => $distributor->id]);
            if( $request->country ) 
            {
                foreach( $request->country as $country ) 
                {
                    \VanguardLTE\ShopCountry::create([
                        'shop_id' => $shop->id, 
                        'country' => $country
                    ]);
                }
            }
            if( $request->os ) 
            {
                foreach( $request->os as $os ) 
                {
                    \VanguardLTE\ShopOS::create([
                        'shop_id' => $shop->id, 
                        'os' => $os
                    ]);
                }
            }
            if( $request->device ) 
            {
                foreach( $request->device as $device ) 
                {
                    \VanguardLTE\ShopDevice::create([
                        'shop_id' => $shop->id, 
                        'device' => $device
                    ]);
                }
            }
            $progress = \VanguardLTE\Progress::where('shop_id', 0)->get();
            if( count($progress) ) 
            {
                foreach( $progress as $item ) 
                {
                    $newProgress = $item->replicate();
                    $newProgress->shop_id = $shop->id;
                    $newProgress->save();
                }
            }
            $welcomebonuses = \VanguardLTE\WelcomeBonus::where('shop_id', 0)->get();
            if( count($welcomebonuses) ) 
            {
                foreach( $welcomebonuses as $item ) 
                {
                    $newWelcomeBonus = $item->replicate();
                    $newWelcomeBonus->shop_id = $shop->id;
                    $newWelcomeBonus->save();
                }
            }
            $smsbonuses = \VanguardLTE\SMSBonus::where('shop_id', 0)->get();
            if( count($smsbonuses) ) 
            {
                foreach( $smsbonuses as $item ) 
                {
                    $newSMSBonus = $item->replicate();
                    $newSMSBonus->shop_id = $shop->id;
                    $newSMSBonus->save();
                }
            }
            $open_shift = \VanguardLTE\OpenShift::create([
                'start_date' => \Carbon\Carbon::now(), 
                'balance' => 0, 
                'user_id' => $cashier->id, 
                'shop_id' => $shop->id
            ]);
            if( $agentBalance > 0 ) 
            {
                $agent->addBalance('add', $agentBalance);
            }
            if( $distributorBalance > 0 ) 
            {
                $distributor->addBalance('add', $distributorBalance, $agent);
            }
            if( $shopBalance > 0 ) 
            {
                $open_shift->increment('balance_in', $shopBalance);
                $distributor->decrement('balance', $shopBalance);
                $shop->increment('balance', $shopBalance);
                \VanguardLTE\Statistic::create([
                    'user_id' => $distributor->id, 
                    'shop_id' => $shop->id, 
                    'sum' => $shopBalance, 
                    'type' => 'add', 
                    'system' => 'shop'
                ]);
            }
            foreach( [
                $agent, 
                $distributor, 
                $manager, 
                $cashier
            ] as $user ) 
            {
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $user->id
                ]);
                $user->update(['shop_id' => $shop->id]);
            }
            $role = \jeremykenedy\LaravelRoles\Models\Role::find(1);
            for( $i = 0; $i < $users['count']; $i++ ) 
            {
                $sleep++;
                $number = rand(111111111, 999999999);
                $data = [
                    'username' => $number, 
                    'password' => $number, 
                    'role_id' => $role->id, 
                    'status' => 'Active', 
                    'shop_id' => $shop->id, 
                    'parent_id' => $cashier->id, 
                    'created_at' => time() + $sleep
                ];
                $newUser = \VanguardLTE\User::create($data);
                $newUser->attachRole($role);
                if( $users['balance'] > 0 ) 
                {
                    $newUser->addBalance('add', $users['balance'], $cashier);
                }
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $newUser->id
                ]);
                $newUser->update(['shop_id' => $shop->id]);
            }
            if( $request->input('categories') && count($request->input('categories')) ) 
            {
                foreach( $request->input('categories') as $category ) 
                {
                    \VanguardLTE\ShopCategory::create([
                        'shop_id' => $shop->id, 
                        'category_id' => $category
                    ]);
                }
            }
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'create', 
                'item_id' => $shop->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_created'));
        }
        public function edit($shop)
        {
            $shop = \VanguardLTE\Shop::where('id', $shop)->first();
            if( !$shop ) 
            {
                abort(404);
            }
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            if( auth()->user()->hasRole([
                'agent', 
                'distributor', 
                'manager', 
                'cashier'
            ]) ) 
            {
                $ids = \VanguardLTE\ShopUser::where('user_id', auth()->user()->id)->pluck('shop_id')->toArray();
                if( !(count($ids) && in_array($shop->id, $ids)) ) 
                {
                    abort(404);
                }
            }
            $directories = [];
            foreach( glob(public_path() . '/frontend/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            $cats = \VanguardLTE\ShopCategory::where('shop_id', $shop->id)->pluck('category_id')->toArray();
            $blocks = [];
            if( auth()->user()->hasPermission('shops.unblock') ) 
            {
                $blocks[0] = __('app.unblock');
            }
            if( auth()->user()->hasPermission('shops.block') ) 
            {
                $blocks[1] = __('app.block');
            }
            $activity = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                'system' => 'shop', 
                'item_id' => $shop->id
            ])->take(2)->get();
            return view('backend.shops.edit', compact('shop', 'directories', 'categories', 'cats', 'blocks', 'activity'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository, \VanguardLTE\Shop $shop)
        {
            $user = \VanguardLTE\User::find(auth()->id());
            if( auth()->user()->hasRole([
                'agent', 
                'distributor', 
                'manager', 
                'cashier'
            ]) ) 
            {
                $ids = \VanguardLTE\ShopUser::where('user_id', auth()->user()->id)->pluck('shop_id')->toArray();
                if( !(count($ids) && in_array($shop->id, $ids)) ) 
                {
                    abort(404);
                }
            }
            $fields = [
                'is_blocked', 
                'currency'
            ];
            if( $user->hasRole('admin') ) 
            {
                $fields[] = 'shop_limit';
            }
            if( $user->hasPermission('shops.title') ) 
            {
                $fields[] = 'name';
            }
            if( $user->hasPermission('shops.percent') ) 
            {
                $fields[] = 'percent';
            }
            if( $user->hasPermission('shops.frontend') ) 
            {
                $fields[] = 'frontend';
            }
            if( $user->hasPermission('shops.currency') ) 
            {
                $fields[] = 'currency';
            }
            if( $user->hasPermission('shops.order') ) 
            {
                $fields[] = 'orderby';
            }
            if( $user->hasPermission('shops.access') ) 
            {
                $fields[] = 'access';
            }
            if( $user->hasPermission('shops.max_win') ) 
            {
                $fields[] = 'max_win';
            }
            if( $user->hasPermission('shops.privacy_policy') ) 
            {
                $fields[] = 'rules_privacy_policy';
            }
            if( $user->hasPermission('shops.why_bitcoin') ) 
            {
                $fields[] = 'rules_why_bitcoin';
            }
            if( $user->hasPermission('shops.terms_and_conditions') ) 
            {
                $fields[] = 'rules_terms_and_conditions';
            }
            if( $user->hasPermission('shops.general_bonus_policy') ) 
            {
                $fields[] = 'rules_general_bonus_policy';
            }
            if( $user->hasPermission('shops.responsible_gaming') ) 
            {
                $fields[] = 'rules_responsible_gaming';
            }
            $data = $request->only($fields);
            $validatedData = $request->validate([
                'name' => 'sometimes|required|unique:shops,name,' . $shop->id, 
                'currency' => 'sometimes|required|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'orderby' => 'sometimes|required|in:' . implode(',', \VanguardLTE\Shop::$values['orderby'])
            ]);
            $temp = $request->only([
                'country', 
                'os', 
                'device'
            ]);
            if( count($temp) ) 
            {
                foreach( $temp as $key => $item ) 
                {
                    $data[$key] = implode(',', $item);
                }
            }
            $shop->update($data);
            \VanguardLTE\ShopCountry::where('shop_id', $shop->id)->delete();
            \VanguardLTE\ShopOS::where('shop_id', $shop->id)->delete();
            \VanguardLTE\ShopDevice::where('shop_id', $shop->id)->delete();
            if( $request->country ) 
            {
                foreach( $request->country as $country ) 
                {
                    \VanguardLTE\ShopCountry::create([
                        'shop_id' => $shop->id, 
                        'country' => $country
                    ]);
                }
            }
            if( $request->os ) 
            {
                foreach( $request->os as $os ) 
                {
                    \VanguardLTE\ShopOS::create([
                        'shop_id' => $shop->id, 
                        'os' => $os
                    ]);
                }
            }
            if( $request->device ) 
            {
                foreach( $request->device as $device ) 
                {
                    \VanguardLTE\ShopDevice::create([
                        'shop_id' => $shop->id, 
                        'device' => $device
                    ]);
                }
            }
            if( isset($request->categories) && count($request->categories) ) 
            {
                \VanguardLTE\ShopCategory::where('shop_id', $shop->id)->delete();
                foreach( $request->categories as $category ) 
                {
                    \VanguardLTE\ShopCategory::create([
                        'shop_id' => $shop->id, 
                        'category_id' => $category
                    ]);
                }
            }
            if( $request->is_blocked ) 
            {
                $users = \VanguardLTE\User::where('shop_id', $shop->id)->whereIn('role_id', [1])->get();
                if( $users ) 
                {
                    foreach( $users as $user ) 
                    {
                        $sessions = $sessionRepository->getUserSessions($user->id);
                        if( count($sessions) ) 
                        {
                            foreach( $sessions as $session ) 
                            {
                                $sessionRepository->invalidateSession($session->id);
                            }
                        }
                    }
                }
            }
            $data = $request->only([
                'access', 
                'country', 
                'os', 
                'device'
            ]);
            $users = \VanguardLTE\User::where([
                'shop_id' => $shop->id, 
                'role_id' => 1
            ])->get();
            if( $users ) 
            {
                foreach( $users as $user ) 
                {
                    $activity = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                        'type' => 'user', 
                        'user_id' => $user->id
                    ])->orderBy('id', 'DESC')->first();
                    if( $activity ) 
                    {
                        foreach( [
                            'countries' => 'country', 
                            'oss' => 'os', 
                            'devices' => 'device'
                        ] as $index => $item ) 
                        {
                            if( !count($shop->$index) ) 
                            {
                                continue;
                            }
                            if( !($shop->access && $shop->$index->filter(function($value, $key) use ($activity, $item)
                            {
                                return $value->$item == $activity->$item || strpos($activity->$item, $value->$item) !== false;
                            })->count() || !$shop->access && !$shop->$index->filter(function($value, $key) use ($activity, $item)
                            {
                                return $value->$item == $activity->$item || strpos($activity->$item, $value->$item) !== false;
                            })->count()) ) 
                            {
                                $sessions = $sessionRepository->getUserSessions($user->id);
                                if( count($sessions) ) 
                                {
                                    foreach( $sessions as $session ) 
                                    {
                                        $sessionRepository->invalidateSession($session->id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_updated'));
        }
        public function get_demo()
        {
            if( !auth()->user()->phone_verified ) 
            {
                return redirect()->route('backend.user.edit', ['user' => auth()->user()->id])->withErrors([__('app.phone_is_not_verified')]);
            }
            if( auth()->user()->free_demo ) 
            {
                return redirect()->back()->withErrors([__('app.only_1_demo')]);
            }
            $sleep = 0;
            $rand = rand(111111111, 999999999);
            $data = [
                'shop' => [
                    'name' => 'DEMO_' . $rand, 
                    'percent' => 90, 
                    'frontend' => 'Default', 
                    'orderby' => 'AZ', 
                    'currency' => 'USD', 
                    'categories' => [0], 
                    'balance' => 100, 
                    'max_win' => 100, 
                    'shop_limit' => 200
                ], 
                'agent' => ['balance' => 150], 
                'distributor' => [
                    'username' => 'D_' . $rand, 
                    'balance' => 100, 
                    'password' => 'D_' . $rand, 
                    'status' => 'Active'
                ], 
                'manager' => [
                    'username' => 'M_' . $rand, 
                    'password' => 'M_' . $rand, 
                    'status' => 'Active'
                ], 
                'cashier' => [
                    'username' => 'C_' . $rand, 
                    'password' => 'C_' . $rand, 
                    'status' => 'Active'
                ], 
                'users' => [
                    'count' => 10, 
                    'balance' => 10
                ]
            ];
            $usersBalance = floatval($data['users']['balance'] * $data['users']['count']);
            $distributorBalance = floatval($data['distributor']['balance']);
            $agentBalance = floatval($data['agent']['balance']);
            $shopBalance = floatval($data['shop']['balance']);
            $data['shop']['balance'] = 0;
            $manager['balance'] = $data['shop']['balance'];
            $data['distributor']['balance'] = $manager['balance'];
            $data['agent']['balance'] = $data['distributor']['balance'];
            if( $usersBalance < 0 || $distributorBalance < 0 || $agentBalance < 0 || $shopBalance < 0 ) 
            {
                return redirect()->back()->withErrors(['Error balance < 0'])->withInput();
            }
            if( $usersBalance > 0 && ($shopBalance <= 0 || $shopBalance < $usersBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Users > Shop'])->withInput();
            }
            if( $shopBalance > 0 && ($distributorBalance <= 0 || $distributorBalance < $shopBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Manager+shop > Distributor'])->withInput();
            }
            if( $distributorBalance > 0 && ($agentBalance <= 0 || $agentBalance < $distributorBalance) ) 
            {
                return redirect()->back()->withErrors(['Error balance: Distributor > Agent'])->withInput();
            }
            $roles = \jeremykenedy\LaravelRoles\Models\Role::get();
            $sleep++;
            $agent = \VanguardLTE\User::find(auth()->user()->id);
            $distributor = \VanguardLTE\User::create($data['distributor'] + [
                'parent_id' => $agent->id, 
                'role_id' => 4, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $distributor->attachRole($roles->find(4));
            $sleep++;
            $manager = \VanguardLTE\User::create($data['manager'] + [
                'parent_id' => $distributor->id, 
                'role_id' => 3, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $manager->attachRole($roles->find(3));
            $sleep++;
            $cashier = \VanguardLTE\User::create($data['cashier'] + [
                'parent_id' => $manager->id, 
                'role_id' => 2, 
                'created_at' => time() + $sleep, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE
            ]);
            $cashier->attachRole($roles->find(2));
            $shop = \VanguardLTE\Shop::create($data['shop'] + ['user_id' => $distributor->id]);
            $progress = \VanguardLTE\Progress::where('shop_id', 0)->get();
            if( count($progress) ) 
            {
                foreach( $progress as $item ) 
                {
                    $newProgress = $item->replicate();
                    $newProgress->shop_id = $shop->id;
                    $newProgress->save();
                }
            }
            $welcomebonuses = \VanguardLTE\WelcomeBonus::where('shop_id', 0)->get();
            if( count($welcomebonuses) ) 
            {
                foreach( $welcomebonuses as $item ) 
                {
                    $newWelcomeBonus = $item->replicate();
                    $newWelcomeBonus->shop_id = $shop->id;
                    $newWelcomeBonus->save();
                }
            }
            $smsbonuses = \VanguardLTE\SMSBonus::where('shop_id', 0)->get();
            if( count($smsbonuses) ) 
            {
                foreach( $smsbonuses as $item ) 
                {
                    $newSMSBonus = $item->replicate();
                    $newSMSBonus->shop_id = $shop->id;
                    $newSMSBonus->save();
                }
            }
            $open_shift = \VanguardLTE\OpenShift::create([
                'start_date' => \Carbon\Carbon::now(), 
                'balance' => 0, 
                'user_id' => $cashier->id, 
                'shop_id' => $shop->id
            ]);
            if( $agentBalance > 0 ) 
            {
                $payeer = \VanguardLTE\User::find(1);
                $agent->addBalance('add', $agentBalance, $payeer);
            }
            if( $distributorBalance > 0 ) 
            {
                $distributor->addBalance('add', $distributorBalance, $agent);
            }
            if( $shopBalance > 0 ) 
            {
                $open_shift->increment('balance_in', $shopBalance);
                $distributor->decrement('balance', $shopBalance);
                $shop->increment('balance', $shopBalance);
                \VanguardLTE\Statistic::create([
                    'user_id' => $distributor->id, 
                    'shop_id' => $shop->id, 
                    'sum' => $shopBalance, 
                    'type' => 'add', 
                    'system' => 'shop'
                ]);
            }
            foreach( [
                $agent, 
                $distributor, 
                $manager, 
                $cashier
            ] as $user ) 
            {
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $user->id
                ]);
                $user->update(['shop_id' => $shop->id]);
            }
            $role = \jeremykenedy\LaravelRoles\Models\Role::find(1);
            for( $i = 0; $i < $data['users']['count']; $i++ ) 
            {
                $sleep++;
                $number = rand(111111111, 999999999);
                $params = [
                    'username' => $number, 
                    'password' => $number, 
                    'role_id' => $role->id, 
                    'status' => 'Active', 
                    'shop_id' => $shop->id, 
                    'parent_id' => $cashier->id, 
                    'created_at' => time() + $sleep
                ];
                $newUser = \VanguardLTE\User::create($params);
                $newUser->attachRole($role);
                if( $data['users']['balance'] > 0 ) 
                {
                    $newUser->addBalance('add', $data['users']['balance'], $cashier);
                }
                \VanguardLTE\ShopUser::create([
                    'shop_id' => $shop->id, 
                    'user_id' => $newUser->id
                ]);
                $newUser->update(['shop_id' => $shop->id]);
            }
            foreach( $data['shop']['categories'] as $category ) 
            {
                \VanguardLTE\ShopCategory::create([
                    'shop_id' => $shop->id, 
                    'category_id' => $category
                ]);
            }
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'create', 
                'item_id' => $shop->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            $agent->update(['free_demo' => 1]);
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_created'));
        }
        public function delete($shop)
        {
            $usersWithBalance = \VanguardLTE\User::where('shop_id', $shop)->where('role_id', 1)->where('balance', '>', 0)->count();
            if( $usersWithBalance ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.users_with_balance', ['count' => $usersWithBalance])]);
            }
            $gamesWithBalance = \VanguardLTE\GameBank::where('shop_id', $shop)->where(function($query)
            {
                return $query->where('slots', '>', 0)->orWhere('little', '>', 0)->orWhere('table_bank', '>', 0)->orWhere('bonus', '>', 0);
            })->count();
            if( $gamesWithBalance ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.games_with_gamebank', ['count' => $gamesWithBalance])]);
            }
            $gamesWithBalance = \VanguardLTE\FishBank::where('shop_id', $shop)->where('fish', '>', 0)->count();
            if( $gamesWithBalance ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.games_with_gamebank', ['count' => $gamesWithBalance])]);
            }
            $jackpotsWithBalance = \VanguardLTE\JPG::where('shop_id', $shop)->where('balance', '>', 0)->count();
            if( $jackpotsWithBalance ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.jackpots_with_balance', ['count' => $jackpotsWithBalance])]);
            }
            $pincodesWithBalance = \VanguardLTE\Pincode::where('shop_id', $shop)->where('nominal', '>', 0)->count();
            if( $pincodesWithBalance ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.pincodes_with_nominal', ['count' => $pincodesWithBalance])]);
            }
            $shopInfo = \VanguardLTE\Shop::find($shop);
            if( $shopInfo && $shopInfo->balance > 0 ) 
            {
                return redirect()->route('backend.shop.list')->withErrors([trans('app.shop_balance')]);
            }
            $distributors = \VanguardLTE\User::where('role_id', 4)->whereHas('rel_shops', function($query) use ($shop)
            {
                $query->where('shop_id', $shop);
            })->pluck('id')->toArray();
            if( count($distributors) ) 
            {
                $distributorsWithBalance = \VanguardLTE\User::whereIn('id', $distributors)->where('balance', '>', 0)->get();
                foreach( $distributorsWithBalance as $distributor ) 
                {
                    if( count($distributor->shops()) == 1 && $distributor->shop_id == $shopInfo->id ) 
                    {
                        return redirect()->route('backend.shop.list')->withErrors([trans('app.distributors_with_balance', ['count' => count($distributorsWithBalance)])]);
                    }
                }
            }
            $item = \VanguardLTE\Shop::find($shop);
            $item->delete();
            \VanguardLTE\Shop::where('id', $shop)->delete();
            \VanguardLTE\ShopUser::where('shop_id', $shop)->delete();
            \VanguardLTE\Statistic::where('shop_id', $shop)->delete();
            \VanguardLTE\StatisticAdd::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopCountry::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopOS::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopDevice::where('shop_id', $shop)->delete();
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'delete', 
                'item_id' => $shop, 
                'shop_id' => auth()->user()->shop_id
            ]);
            $usersToDelete = \VanguardLTE\User::whereIn('role_id', [
                1, 
                2, 
                3
            ])->where('shop_id', $shop)->get();
            if( $usersToDelete ) 
            {
                foreach( $usersToDelete as $userDelete ) 
                {
                    $userDelete->delete();
                }
            }
            \VanguardLTE\User::doesntHave('rel_shops')->where('shop_id', '!=', 0)->whereIn('role_id', [
                4, 
                5
            ])->update(['shop_id' => 0]);
            $admin = \VanguardLTE\User::where('role_id', 6)->first();
            if( $admin->shop_id == $shop ) 
            {
                $admin->update(['shop_id' => 0]);
            }
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_deleted'));
        }
        public function hard_delete($shop)
        {
            $item = \VanguardLTE\Shop::find($shop);
            $item->delete();
            \VanguardLTE\Shop::where('id', $shop)->delete();
            \VanguardLTE\ShopUser::where('shop_id', $shop)->delete();
            \VanguardLTE\Statistic::where('shop_id', $shop)->delete();
            \VanguardLTE\StatisticAdd::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopCountry::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopOS::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopDevice::where('shop_id', $shop)->delete();
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'delete', 
                'item_id' => $shop, 
                'shop_id' => auth()->user()->shop_id
            ]);
            $usersToDelete = \VanguardLTE\User::whereIn('role_id', [
                1, 
                2, 
                3
            ])->where('shop_id', $shop)->get();
            if( $usersToDelete ) 
            {
                foreach( $usersToDelete as $userDelete ) 
                {
                    $userDelete->delete();
                }
            }
            \VanguardLTE\User::doesntHave('rel_shops')->where('shop_id', '!=', 0)->whereIn('role_id', [
                4, 
                5
            ])->update(['shop_id' => 0]);
            $admin = \VanguardLTE\User::where('role_id', 6)->first();
            if( $admin->shop_id == $shop ) 
            {
                $admin->update(['shop_id' => 0]);
            }
            return redirect()->route('backend.shop.list')->withSuccess(trans('app.shop_deleted'));
        }
        public function balance(\Illuminate\Http\Request $request)
        {
            $data = $request->all();
            if( !array_get($data, 'type') ) 
            {
                $data['type'] = 'add';
            }
            $shop = \VanguardLTE\Shop::find($request->shop_id);
            $user = \VanguardLTE\User::find(auth()->user()->id);
            if( $request->all && $request->all == '1' ) 
            {
                $request->summ = $shop->balance;
            }
            $summ = floatval($request->summ);
            if( !$shop ) 
            {
                abort(404);
            }
            if( !$user ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_user')]);
            }
            if( !auth()->user()->hasRole([
                'distributor', 
                'manager'
            ]) ) 
            {
                return redirect()->back()->withErrors([trans('app.only_for_distributors')]);
            }
            if( !$summ || $summ == 0 || $summ < 0 ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_sum')]);
            }
            if( $data['type'] == 'add' && $user->balance < $summ ) 
            {
                return redirect()->back()->withErrors([trans('app.not_enough_money_in_the_user_balance', [
                    'name' => $user->username, 
                    'balance' => $user->balance
                ])]);
            }
            if( $data['type'] == 'out' && $shop->balance < $summ ) 
            {
                return redirect()->back()->withErrors([trans('app.not_enough_money_in_the_shop', [
                    'name' => $shop->name, 
                    'balance' => $shop->balance
                ])]);
            }
            $sum = ($request->type == 'out' ? -1 * $request->summ : $request->summ);
            \VanguardLTE\Statistic::create([
                'user_id' => auth()->user()->id, 
                'shop_id' => $shop->id, 
                'type' => $request->type, 
                'sum' => abs($sum), 
                'system' => 'shop'
            ]);
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => $shop->id, 
                'end_date' => null
            ])->first();
            if( $open_shift ) 
            {
                if( $request->type == 'out' ) 
                {
                    $open_shift->increment('balance_out', abs($sum));
                }
                else
                {
                    $open_shift->increment('balance_in', abs($sum));
                }
            }
            else if( $request->type == 'out' ) 
            {
                \VanguardLTE\OpenShiftTemp::create([
                    'field' => 'balance_out', 
                    'value' => abs($sum), 
                    'shop_id' => $shop->id
                ]);
            }
            else
            {
                \VanguardLTE\OpenShiftTemp::create([
                    'field' => 'balance_in', 
                    'value' => abs($sum), 
                    'shop_id' => $shop->id
                ]);
            }
            $user->update([
                'balance' => $user->balance - $sum, 
                'count_balance' => $user->count_balance - $sum
            ]);
            $shop->update(['balance' => $shop->balance + $sum]);
            return redirect()->back()->withSuccess(trans('app.balance_updated'));
        }
        public function action(\VanguardLTE\Shop $shop, $action)
        {
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => $shop->id, 
                'end_date' => null
            ])->first();
            if( $action && in_array($action, [
                'jpg_out', 
                'games_out', 
                'return_out'
            ]) ) 
            {
                switch( $action ) 
                {
                    case 'jpg_out':
                        $jackpots = \VanguardLTE\JPG::where('shop_id', $shop->id)->get();
                        foreach( $jackpots as $jackpot ) 
                        {
                            $sum = $jackpot->balance;
                            if( $sum <= 0 ) 
                            {
                                continue;
                            }
                            $jackpot->decrement('balance', abs($sum));
                            $shop->increment('balance', abs($sum));
                            if( $open_shift ) 
                            {
                                $open_shift->increment('balance_in', abs($sum));
                            }
                            else
                            {
                                \VanguardLTE\OpenShiftTemp::create([
                                    'field' => 'balance_in', 
                                    'value' => abs($sum), 
                                    'shop_id' => $shop->id
                                ]);
                            }
                            if( $shop->id > 0 ) 
                            {
                                \VanguardLTE\Statistic::create([
                                    'title' => $jackpot->name, 
                                    'user_id' => auth()->user()->id, 
                                    'system' => 'jpg', 
                                    'type' => 'out', 
                                    'sum' => abs($sum), 
                                    'old' => $sum, 
                                    'shop_id' => $shop->id
                                ]);
                            }
                        }
                        return redirect()->back()->withSuccess(trans('app.balance_updated'));
                        break;
                    case 'games_out':
                        $arr = ['gamebank'];
                        if( $action == 'jpg_out' ) 
                        {
                            $arr = [
                                'jp_1', 
                                'jp_2', 
                                'jp_3', 
                                'jp_4', 
                                'jp_5', 
                                'jp_6', 
                                'jp_7', 
                                'jp_8', 
                                'jp_9', 
                                'jp_10'
                            ];
                        }
                        $games = \VanguardLTE\Game::where('shop_id', $shop->id)->get();
                        foreach( $games as $game ) 
                        {
                            foreach( $arr as $element ) 
                            {
                                $sum = $game->$element;
                                if( $sum <= 0 ) 
                                {
                                    continue;
                                }
                                $name = $game->name;
                                if( $element != 'gamebank' ) 
                                {
                                    $name .= (' JP ' . str_replace('jp_', '', $element));
                                }
                                $shop->increment('balance', $sum);
                                if( $open_shift ) 
                                {
                                    $open_shift->increment('balance_in', abs($sum));
                                }
                                else
                                {
                                    \VanguardLTE\OpenShiftTemp::create([
                                        'field' => 'balance_in', 
                                        'value' => abs($sum), 
                                        'shop_id' => $shop->id
                                    ]);
                                }
                                if( $action == 'jpg_out' ) 
                                {
                                    $game->update([$element => 0]);
                                }
                                else
                                {
                                    $game->update([$element => 0]);
                                }
                                if( $shop->id > 0 ) 
                                {
                                    \VanguardLTE\Statistic::create([
                                        'title' => $name, 
                                        'user_id' => auth()->user()->id, 
                                        'system' => 'jpg', 
                                        'type' => 'out', 
                                        'sum' => $sum, 
                                        'old' => $sum, 
                                        'shop_id' => $shop->id
                                    ]);
                                }
                            }
                        }
                        return redirect()->back()->withSuccess(trans('app.balance_updated'));
                        break;
                    case 'return_out':
                        \VanguardLTE\User::where('shop_id', $shop->id)->update(['refunds' => 0]);
                        return redirect()->back()->withSuccess(trans('app.balance_updated'));
                        break;
                }
            }
        }

    }

}
