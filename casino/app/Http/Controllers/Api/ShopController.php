<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ShopController extends ApiController
    {
        private $max_shops = 1000;
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( auth()->user()->hasRole(['cashier']) ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
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
                $shops = $shops->where('shops.name', $request->name);
            }
            if( $request->credit_from != '' ) 
            {
                $shops = $shops->where('shops.credit', '>=', $request->credit_from);
            }
            if( $request->credit_to != '' ) 
            {
                $shops = $shops->where('shops.credit', '<=', $request->credit_to);
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
                $shops = $shops->where('shops.status', $request->status);
            }
            if( $request->categories ) 
            {
                $shops = $shops->join('shop_categories', 'shop_categories.shop_id', '=', 'shops.id');
                $shops = $shops->whereIn('shop_categories.category_id', $request->categories);
            }
            if( $request->users != '' ) 
            {
                $shops = $shops->join('shops_user', 'shops_user.shop_id', '=', 'shops.id');
                $shops = $shops->where('shops_user.user_id', $request->users);
            }
            $shops = $shops->paginate(20);
            return $this->respondWithPagination($shops, new \VanguardLTE\Transformers\ShopTransformer());
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( $this->max_shops <= \VanguardLTE\Shop::count() ) 
            {
                return $this->errorWrongArgs(trans('max_shops', ['max' => config('limits.max_shops')]));
            }
            if( !auth()->user()->hasRole('distributor') ) 
            {
                return $this->errorWrongArgs(trans('app.only_for_distributors'));
            }
            $validatedData = $request->validate([
                'name' => 'required|unique:shops|max:255', 
                'currency' => 'present|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'percent' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['percent']), 
                'orderby' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['orderby']), 
                'country' => 'required', 
                'os' => 'required', 
                'device' => 'required', 
                'access' => 'required'
            ]);
            $data = $request->only([
                'name', 
                'percent', 
                'frontend', 
                'currency', 
                'is_blocked', 
                'orderby', 
                'user_id', 
                'pending', 
                'access', 
                'max_win'
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
            $shop = \VanguardLTE\Shop::create($data + [
                'user_id' => auth()->user()->id, 
                'is_blocked' => 0
            ]);
            $user = \VanguardLTE\User::find(auth()->user()->id);
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
            $user->update(['shop_id' => $shop->id]);
            \VanguardLTE\Task::create([
                'category' => 'shop', 
                'action' => 'create', 
                'item_id' => $shop->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            return $this->setStatusCode(201)->respondWithItem($shop, new \VanguardLTE\Transformers\ShopTransformer());
        }
        public function view(\Illuminate\Http\Request $request, $shop_id)
        {
            if( auth()->user()->hasRole([
                'cashier', 
                'user'
            ]) ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
            if( ($shopIds = auth()->user()->shops_array(true)) && count($shopIds) && in_array($shop_id, $shopIds) ) 
            {
                $shop = \VanguardLTE\Shop::find($shop_id);
                if( $shop ) 
                {
                    return $this->respondWithItem($shop, new \VanguardLTE\Transformers\ShopTransformer());
                }
            }
            return $this->errorNotFound();
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository, \VanguardLTE\Shop $shop)
        {
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            if( auth()->user()->hasRole([
                'cashier', 
                'user'
            ]) ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
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
                    return $this->errorNotFound();
                }
            }
            $data = $request->only([
                'name', 
                'frontend', 
                'currency', 
                'percent', 
                'max_win', 
                'orderby', 
                'is_blocked', 
                'country', 
                'os', 
                'device', 
                'access'
            ]);
            $validatedData = $request->validate([
                'name' => 'required|unique:shops,name,' . $shop->id, 
                'currency' => 'present|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'percent' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['percent']), 
                'orderby' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['orderby']), 
                'country' => 'required', 
                'os' => 'required', 
                'device' => 'required', 
                'access' => 'required'
            ]);
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
                $users = \VanguardLTE\User::where('shop_id', $shop->id)->whereIn('role_id', [
                    1, 
                    2, 
                    3
                ])->get();
                if( $users ) 
                {
                    foreach( $users as $userElem ) 
                    {
                        DB::table('sessions')->where('user_id', $userElem->id)->delete();
                        $userElem->update(['remember_token' => null]);
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
                    if( !($activity && $data['access'] && (!isset($data['country']) || isset($data['country']) && in_array($activity->country, $data['country'])) && (!isset($data['os']) || isset($data['os']) && in_array($activity->os, $data['os'])) && (!isset($data['device']) || isset($data['device']) && in_array($activity->device, $data['device']))) ) 
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
            return $this->setStatusCode(201)->respondWithItem($shop, new \VanguardLTE\Transformers\ShopTransformer());
        }
        public function admin(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                return $this->errorWrongArgs('Only for admin');
            }
            $sleep = 0;
            $shop = $request->only([
                'name', 
                'percent', 
                'frontend', 
                'orderby', 
                'max_win', 
                'currency', 
                'categories', 
                'balance', 
                'country', 
                'os', 
                'device', 
                'access'
            ]);
            $agent = $request->input('agent');
            $distributor = $request->input('distributor');
            $manager = $request->input('manager');
            $cashier = $request->input('cashier');
            $users = $request->input('users');
            if( $this->max_shops <= \VanguardLTE\Shop::count() ) 
            {
                return $this->errorWrongArgs(trans('max_shops', ['max' => config('limits.max_shops')]));
            }
            $request->validate([
                'name' => 'required|unique:shops|max:255', 
                'currency' => 'present|in:' . implode(',', \VanguardLTE\Shop::$values['currency']), 
                'percent' => 'required|in:' . implode(',', \VanguardLTE\Shop::$values['percent']), 
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
                    'username' => 'required|regex:/^[A-Za-z0-9_]+$/|unique:users,username', 
                    'password' => 'required|min:6'
                ]);
                if( $validator->fails() ) 
                {
                    return $this->respondWithArray($validator->errors()->toArray());
                }
            }
            $validator = \Illuminate\Support\Facades\Validator::make($users, [
                'count' => 'required', 
                'balance' => 'required'
            ]);
            if( $validator->fails() ) 
            {
                return $this->respondWithArray($validator->errors()->toArray());
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
                return $this->errorWrongArgs('Error balance < 0');
            }
            if( $usersBalance > 0 && ($shopBalance <= 0 || $shopBalance < $usersBalance) ) 
            {
                return $this->errorWrongArgs('Error balance: Users > Shop');
            }
            if( $shopBalance > 0 && ($distributorBalance <= 0 || $distributorBalance < $shopBalance) ) 
            {
                return $this->errorWrongArgs('Error balance: Manager+shop > Distributor');
            }
            if( $distributorBalance > 0 && ($agentBalance <= 0 || $agentBalance < $distributorBalance) ) 
            {
                return $this->errorWrongArgs('Error balance: Distributor > Agent');
            }
            $roles = \jeremykenedy\LaravelRoles\Models\Role::get();
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
            $shop = \VanguardLTE\Shop::create($shop + [
                'user_id' => $distributor->id, 
                'is_blocked' => 0
            ]);
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
                sleep(1);
            }
            if( $distributorBalance > 0 ) 
            {
                $distributor->addBalance('add', $distributorBalance, $agent);
                sleep(1);
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
                sleep(1);
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
                    sleep(1);
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
            return $this->respondWithItem($shop, new \VanguardLTE\Transformers\ShopTransformer());
        }
        public function currency(\Illuminate\Http\Request $request)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            return $this->respondWithArray(['currency' => $shop->currency]);
        }
        public function balance(\Illuminate\Http\Request $request, \VanguardLTE\Shop $shop, $type = 'add')
        {
            if( !in_array($type, [
                'add', 
                'out'
            ]) ) 
            {
                $type = 'add';
            }
            $user = \VanguardLTE\User::find(auth()->user()->id);
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            if( !$user ) 
            {
                return $this->errorWrongArgs(trans('app.wrong_user'));
            }
            if( !auth()->user()->hasRole('distributor') ) 
            {
                return $this->errorWrongArgs('Wrong user. Only for distributors');
            }
            if( !$request->summ ) 
            {
                return $this->errorWrongArgs(trans('app.wrong_sum'));
            }
            if( $type == 'add' && $user->balance < $request->summ ) 
            {
                return $this->errorWrongArgs('Not enough money in the user balance "' . $user->username . '". Only ' . $user->balance);
            }
            if( $type == 'out' && $shop->balance < $request->summ ) 
            {
                return $this->errorWrongArgs('Not enough money in the shop "' . $shop->name . '". Only ' . $shop->balance);
            }
            $sum = ($type == 'out' ? -1 * $request->summ : $request->summ);
            \VanguardLTE\Statistic::create([
                'user_id' => auth()->user()->id, 
                'shop_id' => $user->shop_id, 
                'type' => $type, 
                'sum' => abs($sum), 
                'system' => 'shop'
            ]);
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => auth()->user()->shop_id, 
                'end_date' => null
            ])->first();
            if( $open_shift ) 
            {
                if( $type == 'out' ) 
                {
                    $open_shift->increment('balance_out', abs($sum));
                }
                else
                {
                    $open_shift->increment('balance_in', abs($sum));
                }
            }
            else if( $type == 'out' ) 
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
            return $this->respondWithSuccess();
        }
        public function shop_block(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            if( !auth()->user()->hasPermission('shops.block') ) 
            {
                return $this->errorForbidden('Permission denied.');
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                return $this->errorForbidden('Shop is not exist');
            }
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
            $shop->update(['is_blocked' => 1]);
            return $this->respondWithSuccess();
        }
        public function shop_unblock(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasPermission('shops.unblock') ) 
            {
                return $this->errorForbidden('Permission denied.');
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                return $this->errorForbidden('Shop is not exist');
            }
            $shop->update(['is_blocked' => 0]);
            return $this->respondWithSuccess();
        }
        public function destroy($shop)
        {
            if( !auth()->user()->hasRole([
                'admin', 
                'agent'
            ]) ) 
            {
                return $this->errorForbidden(__('app.no_permission'));
            }
            if( !auth()->user()->hasRole('admin') ) 
            {
                $usersWithBalance = \VanguardLTE\User::where('shop_id', $shop)->where('role_id', 1)->where('balance', '>', 0)->count();
                if( $usersWithBalance ) 
                {
                    return $this->errorWrongArgs(trans('app.users_with_balance', ['count' => $usersWithBalance]));
                }
                $gamesWithBalance = \VanguardLTE\GameBank::where('shop_id', $shop)->where(function($query)
                {
                    return $query->where('slots', '>', 0)->orWhere('little', '>', 0)->orWhere('table_bank', '>', 0)->orWhere('bonus', '>', 0);
                })->count();
                if( $gamesWithBalance ) 
                {
                    return $this->errorWrongArgs(trans('app.games_with_gamebank', ['count' => $gamesWithBalance]));
                }
                $gamesWithBalance = \VanguardLTE\FishBank::where('shop_id', $shop)->цhere('fish', '>', 0)->count();
                if( $gamesWithBalance ) 
                {
                    return $this->errorWrongArgs(trans('app.games_with_gamebank', ['count' => $gamesWithBalance]));
                }
                $jackpotsWithBalance = \VanguardLTE\JPG::where('shop_id', $shop)->where('balance', '>', 0)->count();
                if( $jackpotsWithBalance ) 
                {
                    return $this->errorWrongArgs(trans('app.jackpots_with_balance', ['count' => $jackpotsWithBalance]));
                }
                $pincodesWithBalance = \VanguardLTE\Pincode::where('shop_id', $shop)->where('nominal', '>', 0)->count();
                if( $pincodesWithBalance ) 
                {
                    return $this->errorWrongArgs(trans('app.pincodes_with_nominal', ['count' => $pincodesWithBalance]));
                }
                $shopInfo = \VanguardLTE\Shop::find($shop);
                if( $shopInfo && $shopInfo->balance > 0 ) 
                {
                    return $this->errorWrongArgs(trans('app.shop_balance'));
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
                            return $this->errorWrongArgs(trans('app.distributors_with_balance', ['count' => count($distributorsWithBalance)]));
                        }
                    }
                }
            }
            \VanguardLTE\Shop::where('id', $shop)->delete();
            \VanguardLTE\ShopUser::where('shop_id', $shop)->delete();
            \VanguardLTE\Statistic::where('shop_id', $shop)->delete();
            \VanguardLTE\StatisticAdd::where('shop_id', $shop)->delete();
            \VanguardLTE\ShopCountry::where('shop_id', $shop->id)->delete();
            \VanguardLTE\ShopOS::where('shop_id', $shop->id)->delete();
            \VanguardLTE\ShopDevice::where('shop_id', $shop->id)->delete();
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
            return $this->respondWithSuccess();
        }
    }

}
