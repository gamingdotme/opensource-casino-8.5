<?php
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once (base_path() . '/app/ShopCore.php');
    include_once (base_path() . '/app/ShopGame.php');
    class DashboardController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $users = null;
        private $activities = null;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users, \VanguardLTE\Repositories\Activity\ActivityRepository $activities)
        {
            $this->middleware(['auth', '2fa']);
            $this->middleware('permission:access.admin.panel');
            $this->users = $users;
            $this->activities = $activities;
        }
        public function index()
        {

            if (\Auth::user()->hasRole('cashier'))
            {
                return redirect()->route('netpos');
            }
            if (!auth()->user()->hasPermission('dashboard'))
            {
                return redirect()->route('backend.user.list');
            }
            $ids = auth()->user()->hierarchyUsers();
            $availableShops = auth()->user()->availableShops();
            $stats = ['total' => $this->users->count($ids) , 'new' => $this->users->newUsersCount($ids) , 'banned' => $this->users->countByStatus(\VanguardLTE\Support\Enum\UserStatus::BANNED, $ids) , 'games' => \VanguardLTE\Game::where(['shop_id' => auth()->user()->shop_id, 'view' => 1])->count() ];
            $shops = \VanguardLTE\Shop::orderBy('id', 'desc')->whereIn('id', $availableShops)->take(5)->get();
            $open_shift = \VanguardLTE\OpenShift::select('open_shift.*')->whereIn('open_shift.shop_id', $availableShops)->orderBy('open_shift.start_date', 'DESC')->take(5)->get();
            $statistics = \VanguardLTE\Statistic::whereHas('add', function ($query)
            {
                $query->where('money_in', '!=', null)->orWhere('money_out', '!=', null);
            });
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('shop_id', $availableShops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $statistics = $statistics->whereIn('user_id', $ids);
            }
            $statistics = $statistics->orderBy('id', 'DESC')->take(5)->get();
            $gamestat = \VanguardLTE\StatGame::whereIn('user_id', $ids)->orderBy('date_time', 'DESC')->take(5)->get();
            return view('backend.dashboard.admin', compact('stats', 'statistics', 'gamestat', 'shops', 'open_shift'));
        }
        public function shopIndex(\Illuminate\Http\Request $request)
        {

            if (!\Auth::user()->hasRole('cashier'))
            {
                return redirect()->route('backend.user.list');

            }
            $users = auth()->user()->hierarchyUsers();
            $availableShops = auth()->user()->availableShops();
            //    $statistics = \VanguardLTE\Transaction::select('transactions.*')->orderBy('transactions.created_at', 'DESC')->take(25);
            //    $statistics = $statistics->whereIn('transactions.user_id', $users);
            $statistics = \VanguardLTE\Statistic::whereHas('add', function ($query)
            {
                $query->where('money_in', '!=', null)->orWhere('money_out', '!=', null);
            });
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('shop_id', $availableShops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $statistics = $statistics->whereIn('user_id', $users);
            }
            $statistics = $statistics->orderBy('id', 'DESC')->take(5)->get();

            $ids = auth()->user()->hierarchyUsers();
            $availableShops = auth()->user()->availableShops();
            $usersPerMonth = $this->users->countOfNewUsersPerMonth(\Carbon\Carbon::now()->subYear()->startOfMonth() , \Carbon\Carbon::now()->endOfMonth() , $ids);
            $stats = ['total' => $this->users->count($ids) , 'new' => $this->users->newUsersCount($ids) , 'banned' => $this->users->countByStatus(\VanguardLTE\Support\Enum\UserStatus::BANNED, $ids) ,

            ];
            $latestRegistrations = $this->users->latest(5, $ids);
            $user = \Auth::user();
            $shops = \VanguardLTE\Shop::orderBy('id', 'desc')->whereIn('id', $availableShops)->take(5)->get();
            $summ = \VanguardLTE\User::where(['shop_id' => \Auth::user()->shop_id, 'role_id' => 1])->sum('balance');
            $statuses = ['' => trans('app.all') ] + \VanguardLTE\Support\Enum\UserStatus::lists();
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('level', '<', \Illuminate\Support\Facades\Auth::user()->level())->pluck('name', 'id');
            $roles->prepend(trans('app.all') , '0');
            $users = \VanguardLTE\User::orderBy('created_at', 'DESC');
            if (!auth()->user()->shop_id)
            {
                if (auth()->user()->hasRole('admin'))
                {
                    $users = $users->whereIn('role_id', [4, 5]);
                }
                if (auth()->user()->hasRole('agent'))
                {
                    $distributors = auth()->user()->availableUsersByRole('distributor');
                    if ($distributors)
                    {
                        $users = $users->whereIn('id', $distributors);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
                if (auth()->user()->hasRole('distributor'))
                {
                    $managers = auth()->user()->availableUsersByRole('manager');
                    if ($managers)
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
                $users = $users->whereIn('id', auth()->user()->availableUsers())->whereHas('rel_shops', function ($query)
                {
                    $query->where('shop_id', auth()->user()->shop_id);
                });
            }
            $users = $users->where('id', '!=', \Illuminate\Support\Facades\Auth::user()->id);
            if ($request->search != '')
            {
                $users = $users->where('username', 'like', '%' . $request->search . '%');
            }
            if ($request->status != '')
            {
                $users = $users->where('status', $request->status);
            }
            if ($request->role)
            {
                $users = $users->where('role_id', $request->role);
            }
            $users = $users->paginate(20);

            return view('backend.newbackend.shop.index', compact('stats', 'latestRegistrations', 'users', 'usersPerMonth', 'user', 'statistics', 'shops', 'summ'));
        }
        public function start_shift(\Illuminate\Http\Request $request)
        {
            if (auth()->user()->hasRole('cashier'))
            {
                $shop_id = auth()->user()->shop_id;
                $user_id = auth()->user()->id;
                $open_shift = '';
                $count = \VanguardLTE\OpenShift::where(['shop_id' => $shop_id, 'end_date' => null])->first();
                if ($count)
                {
                    $summ = \VanguardLTE\User::where(['shop_id' => $shop_id, 'role_id' => 1])->sum('balance');
                    $count->update(['end_date' => \Carbon\Carbon::now() , 'last_banks' => $count->banks() , 'jpg' => \VanguardLTE\JPG::where('shop_id', $shop_id)->sum('balance') , 'users' => $summ]);
                }
                $shop = \VanguardLTE\Shop::find($shop_id);
                if (!$shop)
                {
                    abort(404);
                }
                if ($count)
                {
                    $open_shift = \VanguardLTE\OpenShift::create(['start_date' => \Carbon\Carbon::now() , 'balance' => ($count->balance + $count->balance_in) - $count->balance_out, 'old_banks' => $count->banks() , 'user_id' => $user_id, 'shop_id' => $shop_id]);
                }
                else
                {
                    $open_shift = \VanguardLTE\OpenShift::create(['start_date' => \Carbon\Carbon::now() , 'balance' => 0, 'user_id' => $user_id, 'shop_id' => $shop_id]);
                }
                $temps = \VanguardLTE\OpenShiftTemp::where('shop_id', $shop_id)->get();
                if (count($temps))
                {
                    foreach ($temps as $temp)
                    {
                        if ($temp->type == 'inc')
                        {
                            $open_shift->increment($temp->field, $temp->value);
                        }
                        else
                        {
                            $open_shift->decrement($temp->field, $temp->value);
                        }
                        $temp->delete();
                    }
                }
                return redirect()->route('backend.shift_stat')->withSuccess(trans('app.open_shift_started'));
            }
            else
            {
                abort(403);
            }
        }
        public function start_shift_print(\Illuminate\Http\Request $request)
        {
            $date = (isset($request->date) ? $request->date : \Carbon\Carbon::now()->format(config('app.date_time_format')));
            $open_shift = \VanguardLTE\OpenShift::where(['shop_id' => auth()->user()->shop_id, 'end_date' => null])->first();
            return view('backend.dashboard.shift_print', compact('open_shift', 'date'));
        }
        public function transactions(\Illuminate\Http\Request $request)
        {
            $users = auth()->user()->hierarchyUsers();
            $shops = auth()->user()->availableShops(true);
            $statistics = \VanguardLTE\Statistic::lockForUpdate()->select(['id', 'title', 'user_id', 'payeer_id', 'system', 'type', 'sum', 'sum2', 'shop_id', 'created_at'])->orderBy('id', 'DESC');
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $statistics = $statistics->whereIn('user_id', $users);
            }
            if (!auth()->user()->hasRole('admin'))
            {
                $statistics = $statistics->whereNotIn('system', ['jpg', 'bank']);
            }
            $systems = ['' => '---', 'pincode' => 'PIN', 'refund' => 'Refund', 'happyhour' => 'HH', 'handpay' => 'HP', 'invite' => 'IF', 'progress' => 'PB', 'tournament' => 'TB', 'daily_entry' => 'DE', 'interkassa' => 'IK', 'coinbase' => 'CB', 'btcpayserver' => 'BP', 'welcome_bonus' => 'WB', 'sms_bonus' => 'SB', 'wheelfortune' => 'WH'];
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('id', '<=', auth()->user()->role_id)->pluck('name', 'id')->toArray();
            if (auth()->user()->hasRole('admin'))
            {
                $systems = $systems + ['bank' => 'Bank', 'jpg' => 'JPG'];
            }
            if ($request->user != '')
            {
                $username = $request->user;
                $statistics = $statistics->where(function ($query) use ($username)
                {
                    $query->whereHas('user', function ($query2) use ($username)
                    {
                        $query2->where('username', $username);
                    })->orWhereHas('payeer', function ($query2) use ($username)
                    {
                        $query2->where('username', $username);
                    });
                });
            }
            if ($request->system != '')
            {
                $statistics = $statistics->where('system', $request->system);
            }
            if ($request->role != '')
            {
                $role_id = $request->role;
                $statistics = $statistics->where(function ($query) use ($role_id)
                {
                    $query->whereHas('user', function ($query2) use ($role_id)
                    {
                        $query2->where('role_id', $role_id);
                    })->orWhereHas('payeer', function ($query2) use ($role_id)
                    {
                        $query2->where('role_id', $role_id);
                    });
                });
            }
            if ($request->credit_in_from != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('credit_in', '>=', $request->credit_in_from);
                });
            }
            if ($request->credit_in_to != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('credit_in', '<=', $request->credit_in_to);
                });
            }
            if ($request->credit_out_from != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('credit_out', '>=', $request->credit_out_from);
                });
            }
            if ($request->credit_out_to != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('credit_out', '<=', $request->credit_out_to);
                });
            }
            if ($request->money_in_from != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('money_in', '>=', $request->money_in_from);
                });
            }
            if ($request->money_in_to != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('money_in', '<=', $request->money_in_to);
                });
            }
            if ($request->money_out_from != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('money_out', '>=', $request->money_out_from);
                });
            }
            if ($request->money_out_to != '')
            {
                $statistics = $statistics->whereHas('add', function ($query) use ($request)
                {
                    $query->where('money_out', '<=', $request->money_out_to);
                });
            }
            if ($request->dates != '')
            {
                $dates = explode(' - ', $request->dates);
                $statistics = $statistics->where('created_at', '>=', $dates[0]);
                $statistics = $statistics->where('created_at', '<=', $dates[1]);
            }
            if ($request->shifts != '')
            {
                $shift = \VanguardLTE\OpenShift::find($request->shifts);
                if ($shift)
                {
                    $statistics = $statistics->where('created_at', '>=', $shift->start_date);
                    if ($shift->end_date)
                    {
                        $statistics = $statistics->where('created_at', '<=', $shift->end_date);
                    }
                }
            }
            if (!(isset($request->type_in_out) && $request->type_in_out == '1'))
            {
                $statistics = $statistics->whereNotIn('system', ['jpg', 'bank']);
            }
            $count = $statistics->count();
            $page = ($request->page ? : 1);
            $perPage = 50;
            $offset = $page * $perPage - $perPage;
            $statistics = $statistics->offset($offset)->take($perPage)->pluck('id');
            $transactions = \VanguardLTE\Statistic::whereIn('id', $statistics)->orderBy('id', 'DESC')->get();
            $stats = ['total_agent' => 0, 'total_distributor' => 0, 'total_credit' => 0, 'total_money' => 0, 'pay_out' => 0, 'money_in' => 0, 'money_out' => 0];
            $statistics = \VanguardLTE\Statistic::select('statistics_add.*')->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
            }
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $statistics = $statistics->whereIn('statistics.user_id', $users);
            }
            $statistics = $statistics->where(function ($query)
            {
                $query->where('statistics_add.agent_in', '!=', null)->orWhere('statistics_add.agent_out', '!=', null);
            });
            $stats['total_agent'] = $statistics->sum('statistics_add.agent_in') - $statistics->sum('statistics_add.agent_out');
            $total_distributor = \VanguardLTE\Statistic::select('statistics_add.*')->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $total_distributor = $total_distributor->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $total_distributor = $total_distributor->whereIn('statistics.user_id', $users);
            }
            $total_distributor = $total_distributor->where(function ($query)
            {
                $query->where('statistics_add.distributor_in', '!=', null)->orWhere('statistics_add.distributor_out', '!=', null);
            });
            $stats['total_distributor'] = $total_distributor->sum('statistics_add.distributor_in') - $total_distributor->sum('statistics_add.distributor_out');
            $total_credit = \VanguardLTE\Statistic::select('statistics_add.*')->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $total_credit = $total_credit->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $total_credit = $total_credit->whereIn('statistics.user_id', $users);
            }
            $total_credit = $total_credit->where(function ($query)
            {
                $query->where('statistics_add.credit_in', '!=', null)->orWhere('statistics_add.credit_out', '!=', null);
            });
            $stats['total_credit'] = $total_credit->sum('statistics_add.credit_in') - $total_credit->sum('statistics_add.credit_out');
            $total_money = \VanguardLTE\Statistic::select('statistics_add.*')->whereNotIn('statistics.system', ['invite', 'progress', 'tournament', 'daily_entry', 'happyhour', 'refund', 'welcome_bonus', 'sms_bonus', 'wheelfortune'])->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $total_money = $total_money->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $total_money = $total_money->whereIn('statistics.user_id', $users);
            }
            $total_money = $total_money->where(function ($query)
            {
                $query->where('statistics_add.money_in', '!=', null)->orWhere('statistics_add.money_out', '!=', null);
            });
            $stats['total_money'] = $total_money->sum('statistics_add.money_in') - $total_money->sum('statistics_add.money_out');
            $happyhour_money = \VanguardLTE\Statistic::select('statistics.hh_multiplier', 'statistics_add.*')->whereIn('statistics.system', ['happyhour'])->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $happyhour_money = $happyhour_money->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $happyhour_money = $happyhour_money->whereIn('statistics.user_id', $users);
            }
            $happyhour_money = $happyhour_money->where(function ($query)
            {
                $query->where('statistics_add.money_in', '!=', null)->orWhere('statistics_add.money_out', '!=', null);
            });
            $happyhour_money = $happyhour_money->get();
            foreach ($happyhour_money as $item)
            {
                $stats['total_money'] += ($item->money_in / $item->hh_multiplier);
            }
            $money_in = \VanguardLTE\Statistic::select('statistics_add.*')->whereNotIn('statistics.system', ['invite', 'progress', 'tournament', 'daily_entry', 'happyhour', 'refund', 'welcome_bonus', 'sms_bonus', 'wheelfortune'])->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $money_in = $money_in->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $money_in = $money_in->whereIn('statistics.user_id', $users);
            }
            $money_in = $money_in->where('statistics_add.money_in', '!=', null);
            $stats['money_in'] = $money_in->sum('statistics_add.money_in');
            $money_out = \VanguardLTE\Statistic::select('statistics_add.*')->whereNotIn('statistics.system', ['invite', 'progress', 'tournament', 'daily_entry', 'happyhour', 'refund'])->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id');
            if (auth()->user()->shop_id > 0)
            {
                $money_out = $money_out->whereIn('statistics.shop_id', $shops);
            }
            if (!(auth()->user()->hasRole('admin') && auth()->user()->shop_id == 0))
            {
                $money_out = $money_out->whereIn('statistics.user_id', $users);
            }
            $money_out = $money_out->where('statistics_add.money_out', '!=', null);
            $stats['money_out'] = $money_out->sum('statistics_add.money_out');
            $stats['pay_out'] = ($stats['money_in'] > 0 ? $stats['money_out'] / $stats['money_in'] * 100 : 0);
            return view('backend.stat.transactions', compact('transactions', 'stats', 'systems', 'roles', 'count', 'page', 'perPage', 'offset'));
        }
        public function game_stat(\Illuminate\Http\Request $request)
        {
            $users = auth()->user()->hierarchyUsers();
            $statistics = \VanguardLTE\StatGame::select('stat_game.id')->orderBy('stat_game.id', 'DESC');
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('stat_game.shop_id', auth()->user()->availableShops());
            }
            if (!auth()->user()->hasRole('admin'))
            {
                $statistics = $statistics->whereIn('stat_game.user_id', $users);
            }
            if ($request->game != '')
            {
                $statistics = $statistics->where('stat_game.game', 'like', '%' . $request->game . '%');
            }
            if ($request->balance_from != '')
            {
                $statistics = $statistics->where('stat_game.balance', '>=', $request->balance_from);
            }
            if ($request->balance_to != '')
            {
                $statistics = $statistics->where('stat_game.balance', '<=', $request->balance_to);
            }
            if ($request->bet_from != '')
            {
                $statistics = $statistics->where('stat_game.bet', '>=', $request->bet_from);
            }
            if ($request->bet_to != '')
            {
                $statistics = $statistics->where('stat_game.bet', '<=', $request->bet_to);
            }
            if ($request->win_from != '')
            {
                $statistics = $statistics->where('stat_game.win', '>=', $request->win_from);
            }
            if ($request->win_to != '')
            {
                $statistics = $statistics->where('stat_game.win', '<=', $request->win_to);
            }
            if ($request->user != '')
            {
                $statistics = $statistics->join('users', 'users.id', '=', 'stat_game.user_id');
                $statistics = $statistics->where('users.username', 'like', '%' . $request->user . '%');
            }
            if ($request->dates != '')
            {
                $dates = explode(' - ', $request->dates);
                $statistics = $statistics->where('stat_game.date_time', '>=', $dates[0]);
                $statistics = $statistics->where('stat_game.date_time', '<=', $dates[1]);
            }
            if ($request->shifts != '')
            {
                $shift = \VanguardLTE\OpenShift::find($request->shifts);
                if ($shift)
                {
                    $statistics = $statistics->where('stat_game.date_time', '>=', $shift->start_date);
                    if ($shift->end_date)
                    {
                        $statistics = $statistics->where('stat_game.date_time', '<=', $shift->end_date);
                    }
                }
            }
            $count = $statistics->count();
            $page = ($request->page ? : 1);
            $perPage = 50;
            $offset = $page * $perPage - $perPage;
            $start = microtime(true);
            $statistics = $statistics->offset($offset)->take($perPage)->pluck('id');
            $time_elapsed_secs = microtime(true) - $start;
            $game_stat = \VanguardLTE\StatGame::whereIn('id', $statistics)->orderBy('id', 'DESC')->get();
            return view('backend.stat.game_stat', compact('game_stat', 'count', 'page', 'perPage'));
        }
        public function securities(\Illuminate\Http\Request $request)
        {
            if (!auth()->user()->hasRole('admin'))
            {
                abort(403);
            }
            $securities = \VanguardLTE\Security::where('securities.view', 1);
            if ($request->type != '')
            {
                $securities = $securities->where('securities.type', 'LIKE', $request->type . '%');
            }
            if ($request->dates != '')
            {
                $dates = explode(' - ', $request->dates);
                $securities = $securities->where('securities.created_at', '>=', $dates[0]);
                $securities = $securities->where('securities.created_at', '<=', $dates[1]);
            }
            $securities = $securities->orderBy('securities.created_at', 'DESC')->paginate(25)->withQueryString();
            return view('backend.dashboard.security', compact('securities'));
        }
        public function securities_delete(\Illuminate\Http\Request $request, \VanguardLTE\Security $item)
        {
            if ($item)
            {
                $item->update(['view' => 0]);
            }
            return redirect()->back()->withSuccess(__('app.security_deleted'));
        }
        public function securities_block(\Illuminate\Http\Request $request, \VanguardLTE\Security $item)
        {
            if ($item)
            {
                if ($item->type == 'user' && $item->user)
                {
                    $item->user->update(['status' => \VanguardLTE\Support\Enum\UserStatus::BANNED, 'remember_token' => null]);
                    \DB::table('sessions')->where('user_id', $item->user->id)->delete();
                    $item->update(['view' => 0]);
                    event(new \VanguardLTE\Events\User\Banned($item->user));
                    return redirect()->back()->withSuccess(__('app.user_blocked'));
                }
                if ($item->type == 'game' && $item->game)
                {
                    $item->game->update(['view' => 0]);
                    $item->update(['view' => 0]);
                    return redirect()->back()->withSuccess(__('app.game_blocked'));
                }
            }
            return redirect()->back()->withSuccess(__('app.security_not_found'));
        }
        public function search(\Illuminate\Http\Request $request)
        {
            if (!$request->q)
            {
                return redirect()->back()->withErrors(['Empty query']);
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $query = $request->q;
            $hierarchyUsers = auth()->user()->hierarchyUsers();
            $availableShops = auth()->user()->availableShops(true);
            $happyhour = false;
            if ($shop && $shop->happyhours_active)
            {
                $happyhour = \VanguardLTE\HappyHour::where(['shop_id' => auth()->user()->shop_id, 'time' => date('G') ])->first();
            }
            $users = \VanguardLTE\User::orderBy('created_at', 'DESC');
            if (!auth()->user()->shop_id)
            {
                if (auth()->user()->hasRole('admin'))
                {
                    $users = $users->whereIn('role_id', [4, 5]);
                }
                if (auth()->user()->hasRole('agent'))
                {
                    $distributors = auth()->user()->availableUsersByRole('distributor');
                    if ($distributors)
                    {
                        $users = $users->whereIn('id', $distributors);
                    }
                    else
                    {
                        $users = $users->where('id', 0);
                    }
                }
                if (auth()->user()->hasRole('distributor'))
                {
                    $managers = auth()->user()->availableUsersByRole('manager');
                    if ($managers)
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
                $users = $users->whereIn('id', auth()->user()->availableUsers())->whereHas('rel_shops', function ($query)
                {
                    $query->where('shop_id', auth()->user()->shop_id);
                });
            }
            $users = $users->where('id', '!=', auth()->user()->id)->where(function ($q) use ($query)
            {
                $q->where('email', 'like', '%' . $query . '%')->orWhere('username', 'like', '%' . $query . '%');
            })->orderBy('created_at', 'DESC')->take(25)->get();
            $statistics = \VanguardLTE\Statistic::select(['id', 'title', 'user_id', 'payeer_id', 'system', 'type', 'sum', 'sum2', 'shop_id', 'created_at'])->orderBy('id', 'DESC');
            if (auth()->user()->shop_id > 0)
            {
                $statistics = $statistics->whereIn('shop_id', $availableShops);
                $statistics = $statistics->whereIn('user_id', $hierarchyUsers);
            }
            if (!auth()->user()->hasRole('admin'))
            {
                $statistics = $statistics->whereNotIn('system', ['jpg', 'bank']);
            }
            $statistics = $statistics->where(function ($q) use ($query)
            {
                $q->whereHas('user', function ($q2) use ($query)
                {
                    $q2->where('username', 'like', '%' . $query . '%')->orWhere('email', 'like', '%' . $query . '%');
                })->orWhereHas('payeer', function ($q2) use ($query)
                {
                    $q2->where('username', 'like', '%' . $query . '%')->orWhere('email', 'like', '%' . $query . '%');
                });
            });
            $pay_stats = $statistics->take(25)->get();
            $game_stats = \VanguardLTE\StatGame::select('stat_game.*')->whereIn('stat_game.shop_id', $availableShops)->orderBy('stat_game.date_time', 'DESC')->whereIn('stat_game.user_id', $hierarchyUsers)->where(function ($q) use ($query)
            {
                $q->where('stat_game.game', 'like', '%' . $query . '%')->orWhereHas('user', function ($q2) use ($query)
                {
                    $q2->where('username', 'like', '%' . $query . '%')->orWhere('email', 'like', '%' . $query . '%');
                });
            })->take(25)->get();
            return view('backend.dashboard.search', compact('query', 'happyhour', 'users', 'pay_stats', 'game_stats'));
        }
        public function shift_stat(\Illuminate\Http\Request $request)
        {
            $users = auth()->user()->hierarchyUsers();
            $summ = \VanguardLTE\User::where(['shop_id' => auth()->user()->shop_id, 'role_id' => 1])->sum('balance');
            $statistics = \VanguardLTE\OpenShift::select('open_shift.*')->whereIn('open_shift.shop_id', auth()->user()->availableShops())->orderBy('open_shift.start_date', 'DESC');
            if ($request->shifts != '')
            {
                $statistics = $statistics->where('open_shift.id', $request->shifts);
            }
            if ($request->user != '')
            {
                $statistics = $statistics->join('users', 'users.id', '=', 'open_shift.user_id');
                $statistics = $statistics->where('users.username', 'like', '%' . $request->user . '%');
            }
            if ($request->dates != '')
            {
                $dates = explode(' - ', $request->dates);
                $statistics = $statistics->where('open_shift.start_date', '>=', $dates[0]);
                $statistics = $statistics->where('open_shift.start_date', '<=', $dates[1]);
            }
            $count = $statistics->count();
            $page = ($request->page ? : 1);
            $perPage = 50;
            $offset = $page * $perPage - $perPage;
            $statistics = $statistics->offset($offset)->take($perPage)->pluck('id');
            $open_shift = \VanguardLTE\OpenShift::whereIn('id', $statistics)->orderBy('id', 'DESC')->get();
            return view('backend.stat.shift_stat', compact('open_shift', 'summ', 'count', 'page', 'perPage', 'offset'));
        }
        public function banks(\Illuminate\Http\Request $request)
        {
            $savedSortOrder = ($request->session()->exists('banks_sort_order') ? $request->session()->get('banks_sort_order') : '');
            if ((count($request->all()) || isset($request->sort_order)) && $request->session()->get('banks_sort_order') != $request->sort_order)
            {
                if (isset($request->sort_order))
                {
                    $savedSortOrder = $request->sort_order;
                    $request->session()->put('banks_sort_order', $request->sort_order);
                }
                else
                {
                    $savedSortOrder = '';
                    $request->session()->forget('banks_sort_order');
                }
            }
            $savedSortFiled = ($request->session()->exists('banks_sort_field') ? $request->session()->get('banks_sort_field') : '');
            if ((count($request->all()) || isset($request->sort_field)) && $request->session()->get('banks_sort_field') != $request->sort_field)
            {
                if (isset($request->sort_field))
                {
                    $savedSortFiled = $request->sort_field;
                    $request->session()->put('banks_sort_field', $request->sort_field);
                }
                else
                {
                    $savedSortFiled = '';
                    $request->session()->forget('banks_sort_field');
                }
            }
            $banks = \VanguardLTE\GameBank::where('game_bank.shop_id', '>', 0)->join('fish_bank', 'game_bank.shop_id', '=', 'fish_bank.shop_id');
            if ($request->search != '')
            {
                $search = $request->search;
                $banks = $banks->where(function ($q) use ($search)
                {
                    $q->whereHas('shop', function ($query) use ($search)
                    {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                });
            }
            if ($request->percent != '')
            {
                $percent = $request->percent;
                $banks = $banks->where(function ($q) use ($percent)
                {
                    $q->whereHas('shop', function ($query) use ($percent)
                    {
                        $query->where('percent', $percent);
                    });
                });
            }
            if ($request->slots_from != '')
            {
                $banks = $banks->where('game_bank.slots', '>=', $request->slots_from);
            }
            if ($request->slots_to != '')
            {
                $banks = $banks->where('game_bank.slots', '<=', $request->slots_to);
            }
            if ($request->little_from != '')
            {
                $banks = $banks->where('game_bank.little', '>=', $request->little_from);
            }
            if ($request->little_to != '')
            {
                $banks = $banks->where('game_bank.little', '<=', $request->little_to);
            }
            if ($request->table_from != '')
            {
                $banks = $banks->where('game_bank.table_bank', '>=', $request->table_from);
            }
            if ($request->table_to != '')
            {
                $banks = $banks->where('game_bank.table_bank', '<=', $request->table_to);
            }
            if ($request->fish_from != '')
            {
                $banks = $banks->where('fish_bank.fish', '>=', $request->fish_from);
            }
            if ($request->fish_to != '')
            {
                $banks = $banks->where('fish_bank.fish', '<=', $request->fish_from);
            }
            if ($request->bonus_from != '')
            {
                $banks = $banks->where('game_bank.bonus', '>=', $request->bonus_from);
            }
            if ($request->bonus_to != '')
            {
                $banks = $banks->where('game_bank.bonus', '<=', $request->bonus_to);
            }
            if ($savedSortFiled && $savedSortOrder && in_array($savedSortOrder, ['asc', 'desc']))
            {
                if (in_array($savedSortFiled, ['slots', 'little', 'table_bank', 'fish', 'bonus']))
                {
                    $sort = 'game_bank.' . $savedSortFiled;
                    if ($savedSortFiled == 'fish')
                    {
                        $sort = 'fish_bank.fish';
                    }
                    $banks = $banks->orderBy($sort, $savedSortOrder);
                }
                if (in_array($savedSortFiled, ['percent']))
                {
                    $shops = \VanguardLTE\Shop::where('id', '>', 0)->orderBy('percent', $savedSortOrder)->pluck('id');
                    if ($shops)
                    {
                        $banks = $banks->orderByRaw('FIELD(w_game_bank.shop_id, ' . implode(',', $shops->toArray()) . ')');
                    }
                }
                if (in_array($savedSortFiled, ['rtp']))
                {
                    $gamebanks = \VanguardLTE\GameBank::get();
                    if ($gamebanks)
                    {
                        foreach ($gamebanks as $item)
                        {
                            $item->update(['temp_rtp' => $item->get_rtp() ]);
                        }
                    }
                    $banks = $banks->orderBy('game_bank.temp_rtp', $savedSortOrder);
                }
                if (in_array($savedSortFiled, ['total']))
                {
                    $banks = $banks->orderByRaw('(w_game_bank.slots+w_game_bank.little+w_game_bank.table_bank+w_game_bank.bonus+w_fish_bank.fish) ' . $savedSortOrder);
                }
            }
            $banks = $banks->groupBy('game_bank.shop_id')->get();
            if ($request->rtp_from != '')
            {
                $temp = [];
                foreach ($banks as $bank)
                {
                    if ($request->rtp_from <= $bank->get_rtp())
                    {
                        $temp[] = $bank;
                    }
                }
                $banks = $temp;
            }
            if ($request->rtp_to != '')
            {
                $temp = [];
                foreach ($banks as $bank)
                {
                    if ($bank->get_rtp() <= $request->rtp_to)
                    {
                        $temp[] = $bank;
                    }
                }
                $banks = $temp;
            }
            return view('backend.dashboard.banks', compact('banks', 'savedSortOrder', 'savedSortFiled'));
        }
        public function banks_update(\Illuminate\Http\Request $request)
        {
            if (!$request->checkbox || !count($request->checkbox))
            {
                return redirect()->back()->withErrors([trans('app.shops_not_selected') ]);
            }
            if (!auth()->user()->hasRole('admin'))
            {
                abort(403);
            }
            $ids = [];
            foreach ($request->checkbox as $id => $val)
            {
                $ids[] = $id;
            }
            $banks = \VanguardLTE\GameBank::where('shop_id', '!=', 0)->whereIn('id', $ids)->get();
            return view('backend.dashboard.banks_change', compact('ids', 'banks'));
        }
        public function do_banks_update(\Illuminate\Http\Request $request)
        {
            if (!$request->ids)
            {
                return redirect()->route('backend.banks')->withErrors([trans('app.shops_not_selected') ]);
            }
            if (!auth()->user()->hasRole('admin'))
            {
                abort(403);
            }
            $banks = [];
            foreach (['slots', 'little', 'table_bank', 'fish', 'bonus'] as $bank)
            {
                if (isset($request->$bank) && $request->$bank != '')
                {
                    $banks[] = $bank;
                }
            }
            if (!count($banks))
            {
                return redirect()->back()->withErrors([trans('app.banks_not_selected') ]);
            }
            $ids = explode(',', $request->ids);
            foreach ($ids as $id)
            {
                $gamebank = \VanguardLTE\GameBank::where('shop_id', $id)->first();
                $fishbank = \VanguardLTE\FishBank::where('shop_id', $id)->first();
                if (!$gamebank)
                {
                    return redirect()->back()->withErrors([__('app.gamebank_entry_not_exist', ['id' => $id]) ]);
                }
                if (!$fishbank)
                {
                    return redirect()->back()->withErrors([__('app.fishbank_entry_not_exist', ['id' => $id]) ]);
                }
                foreach ($banks as $bank)
                {
                    $balance = 0;
                    $model = $gamebank;
                    if ($bank == 'fish')
                    {
                        $model = $fishbank;
                    }
                    if ($request->$bank == 0)
                    {
                        $balance = - 1 * $model->$bank;
                    }
                    if ($request->$bank > 0)
                    {
                        $balance = $request->$bank - $model->$bank;
                    }
                    if ($balance == 0)
                    {
                        continue;
                    }
                    $balance = intval($balance);
                    $old = $model->$bank;
                    $type = ($bank == 'table_bank' ? 'table' : $bank);
                    if ($model->shop_id > 0)
                    {
                        \VanguardLTE\Statistic::create(['title' => ucfirst($type) , 'user_id' => 1, 'type' => ($balance > 0 ? 'add' : 'out') , 'system' => 'bank', 'sum' => $balance, 'old' => $old, 'shop_id' => $model->shop_id]);
                    }
                    $model->increment($bank, $balance);
                }
            }
            return redirect()->back()->withSuccess(trans('app.gamebank_added'));
        }
        public function invites(\Illuminate\Http\Request $request)
        {
            if (auth()->user()->shop && auth()->user()->shop->pending)
            {
                return redirect()->back()->withErrors([__('app.shop_is_creating') . '. ' . __('app.invites_will_be_added_in_few_minutes') ]);
            }
            $invite = \VanguardLTE\Invite::where(['shop_id' => auth()->user()->shop_id])->first();
            if (!$invite)
            {
                abort(404);
            }
            return view('backend.dashboard.invite', compact('invite'));
        }
        public function invite_status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if ($shop && auth()->user()->hasPermission('invite.manage'))
            {
                if ($status == 'disable')
                {
                    $shop->update(['invite_active' => 0]);
                }
                else
                {
                    $shop->update(['invite_active' => 1]);
                }
            }
            return redirect()->route('backend.invites')->withSuccess(trans('app.invite_updated'));
        }
        public function invite_update(\Illuminate\Http\Request $request)
        {
            $request->validate(['wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\Invite::$values['wager'])) ], $request->all());
            $invite = \VanguardLTE\Invite::where('shop_id', auth()->user()->shop_id)->first();
            if (!$invite)
            {
                abort(404);
            }
            $data = $request->only(['message', 'sum', 'sum_ref', 'min_amount', 'waiting_time', 'wager', 'type', 'status']);
            $invite->update($data);
            return redirect()->back()->withSuccess(trans('app.invite_updated'));
        }
        public function wheelfortune(\Illuminate\Http\Request $request)
        {
            if (auth()->user()->shop && auth()->user()->shop->pending)
            {
                return redirect()->back()->withErrors([__('app.shop_is_creating') . '. ' . __('app.invites_will_be_added_in_few_minutes') ]);
            }
            $wheelfortune = \VanguardLTE\WheelFortune::where('shop_id', auth()->user()->shop_id)->first();
            if (!$wheelfortune)
            {
                abort(404);
            }
            return view('backend.dashboard.wheelfortune', compact('wheelfortune'));
        }
        public function wheelfortune_status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if ($shop && auth()->user()->hasPermission('wheelfortune.manage'))
            {
                if ($status == 'disable')
                {
                    $shop->update(['wheelfortune_active' => 0]);
                }
                else
                {
                    $shop->update(['wheelfortune_active' => 1]);
                }
            }
            return redirect()->route('backend.wheelfortune')->withSuccess(trans('app.wheelfortune_updated'));
        }
        public function wheelfortune_update(\Illuminate\Http\Request $request)
        {
            $request->validate(['wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\WheelFortune::$values['wager'])) ], $request->all());
            $wheelfortune = \VanguardLTE\WheelFortune::where('shop_id', auth()->user()->shop_id)->first();
            if (!$wheelfortune)
            {
                abort(404);
            }
            $data = $request->only(['wh1_1', 'wh1_2', 'wh1_3', 'wh1_4', 'wh1_5', 'wh1_6', 'wh1_7', 'wh2_1', 'wh2_2', 'wh2_3', 'wh2_4', 'wh2_5', 'wh2_6', 'wh2_7', 'wh2_8', 'wh3_1', 'wh3_2', 'wh3_3', 'wh3_4', 'wh3_5', 'wh3_6', 'wh3_7', 'wh3_8', 'wh3_9', 'wh3_10', 'wh3_11', 'wh3_12', 'wh3_13', 'wh3_14', 'wh3_15', 'wh3_16', 'status', 'wager']);
            $wheelfortune->update($data);
            return redirect()->back()->withSuccess(trans('app.wheelfortune_updated'));
        }
        public function generator(\Illuminate\Http\Request $request)
        {
            abort(403);
            if (!auth()->user()->shop_id)
            {
                return redirect()->route('backend.dashboard');
            }
            $view = ['1' => 'Active', '0' => 'Disabled'];
            $device = ['Mobile', 'Desktop', 'Mobile + Desktop'];
            $shops = auth()->user()->shops();
            $games = \VanguardLTE\Game::where('shop_id', auth()->user()->shop_id)->get()->pluck('name');
            $jackpots = \VanguardLTE\JPG::where('shop_id', auth()->user()->shop_id)->get()->pluck('name', 'id');
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            $api = [];
            if (count($shops))
            {
                $rarr = array_keys($shops->toArray());
                $apis = \VanguardLTE\Api::where('shop_id', $rarr[0])->get();
                foreach ($apis as $key)
                {
                    $api[$key->keygen] = $key->keygen . ' / ' . $key->ip;
                }
            }
            $text = '';
            if ($request->isMethod('post') && $request->shop_id)
            {
                $text .= ('$apiServer="' . $request->root() . '";' . "\n");
                $text .= ('$apiKey="key=' . $request->key . '";' . "\n");
                $text .= ('$apiShop="shop_id=' . $request->shop_id . '";' . "\n");
                $text .= '$apiGames="';
                $apiGames = '';
                if ($request->categories_ids)
                {
                    $apiGames .= ('&category=' . implode('|', (array)$request->categories_ids));
                }
                if ($request->view)
                {
                    $apiGames .= ('&view=' . $request->view);
                }
                if ($request->device)
                {
                    $apiGames .= ('&device=' . implode('|', (array)$request->device));
                }
                if ($request->game_ids)
                {
                    $apiGames .= ('&name=' . implode('|', (array)$request->game_ids));
                }
                $apiGames = trim($apiGames, '&');
                $text .= $apiGames;
                $text .= '";';
                $text .= "\n";
                $text .= '$apiJP="';
                if ($request->jackpot_ids)
                {
                    $text .= ('id=' . implode('|', (array)$request->jackpot_ids));
                }
                $text .= '";';
                $text .= "\n";
            }
            return view('backend.settings.generator', compact('shops', 'jackpots', 'games', 'categories', 'text', 'view', 'device', 'api'));
        }

        public function withdraw(\Illuminate\Http\Request $request)
        {
            $withdraws = \VanguardLTE\Withdraw::whereIn('user_id', auth()->user()->availableUsers())->orderBy('created_at', 'desc')->get();

            return view('backend.withdraw.list', compact('withdraws'));
        }

    }

}

