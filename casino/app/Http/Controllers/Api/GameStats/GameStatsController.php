<?php 
namespace VanguardLTE\Http\Controllers\Api\GameStats
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class GameStatsController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function pay(\Illuminate\Http\Request $request)
        {
            $users = auth()->user()->hierarchyUsers();
            $shops = auth()->user()->availableShops(true);
            $statistics = \VanguardLTE\Statistic::select([
                'id', 
                'title', 
                'user_id', 
                'payeer_id', 
                'system', 
                'type', 
                'sum', 
                'sum2', 
                'shop_id', 
                'created_at'
            ])->orderBy('id', 'DESC');
            if( auth()->user()->shop_id > 0 ) 
            {
                $statistics = $statistics->whereIn('shop_id', $shops);
                $statistics = $statistics->whereIn('user_id', $users);
            }
            if( !auth()->user()->hasRole('admin') ) 
            {
                $statistics = $statistics->whereNotIn('system', [
                    'jpg', 
                    'bank'
                ]);
            }
            if( $request->user != '' ) 
            {
                $username = $request->user;
                $statistics = $statistics->where(function($query) use ($username)
                {
                    $query->whereHas('user', function($query2) use ($username)
                    {
                        $query2->where('username', $username);
                    })->orWhereHas('payeer', function($query2) use ($username)
                    {
                        $query2->where('username', $username);
                    });
                });
            }
            if( $request->system != '' ) 
            {
                $statistics = $statistics->where('system', $request->system);
            }
            if( $request->role != '' ) 
            {
                $role_id = $request->role;
                $statistics = $statistics->where(function($query) use ($role_id)
                {
                    $query->whereHas('user', function($query2) use ($role_id)
                    {
                        $query2->where('role_id', $role_id);
                    })->orWhereHas('payeer', function($query2) use ($role_id)
                    {
                        $query2->where('role_id', $role_id);
                    });
                });
            }
            if( $request->credit_in_from != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('credit_in', '>=', $request->credit_in_from);
                });
            }
            if( $request->credit_in_to != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('credit_in', '<=', $request->credit_in_to);
                });
            }
            if( $request->credit_out_from != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('credit_out', '>=', $request->credit_out_from);
                });
            }
            if( $request->credit_out_to != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('credit_out', '<=', $request->credit_out_to);
                });
            }
            if( $request->money_in_from != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('money_in', '>=', $request->money_in_from);
                });
            }
            if( $request->money_in_to != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('money_in', '<=', $request->money_in_to);
                });
            }
            if( $request->money_out_from != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('money_out', '>=', $request->money_out_from);
                });
            }
            if( $request->money_out_to != '' ) 
            {
                $statistics = $statistics->whereHas('add', function($query) use ($request)
                {
                    $query->where('money_out', '<=', $request->money_out_to);
                });
            }
            if( $request->dates != '' ) 
            {
                $dates = explode(' - ', $request->dates);
                $statistics = $statistics->where('created_at', '>=', $dates[0]);
                $statistics = $statistics->where('created_at', '<=', $dates[1]);
            }
            if( $request->shifts != '' ) 
            {
                $shift = \VanguardLTE\OpenShift::find($request->shifts);
                if( $shift ) 
                {
                    $statistics = $statistics->where('created_at', '>=', $shift->start_date);
                    if( $shift->end_date ) 
                    {
                        $statistics = $statistics->where('created_at', '<=', $shift->end_date);
                    }
                }
            }
            $statistics = $statistics->paginate(100);
            return $this->respondWithPagination($statistics, new \VanguardLTE\Transformers\StatisticTransformer());
        }
        public function game(\Illuminate\Http\Request $request)
        {
            $stats = \VanguardLTE\StatGame::select('*')->orderBy('date_time', 'DESC');
            if( auth()->user()->shop_id > 0 ) 
            {
                $stats = $stats->whereIn('stat_game.shop_id', auth()->user()->availableShops());
            }
            if( !auth()->user()->hasRole('admin') ) 
            {
                $users = auth()->user()->hierarchyUsers(auth()->user()->shop_id);
                $stats = $stats->whereIn('stat_game.user_id', $users);
            }
            if( $request->game != '' ) 
            {
                $stats = $stats->where('stat_game.game', 'like', '%' . $request->game . '%');
            }
            if( $request->balance_from != '' ) 
            {
                $stats = $stats->where('stat_game.balance', '>=', $request->balance_from);
            }
            if( $request->balance_to != '' ) 
            {
                $stats = $stats->where('stat_game.balance', '<=', $request->balance_to);
            }
            if( $request->bet_from != '' ) 
            {
                $stats = $stats->where('stat_game.bet', '>=', $request->bet_from);
            }
            if( $request->bet_to != '' ) 
            {
                $stats = $stats->where('stat_game.bet', '<=', $request->bet_to);
            }
            if( $request->win_from != '' ) 
            {
                $stats = $stats->where('stat_game.win', '>=', $request->win_from);
            }
            if( $request->win_to != '' ) 
            {
                $stats = $stats->where('stat_game.win', '<=', $request->win_to);
            }
            $stats = $stats->paginate(100);
            return $this->respondWithPagination($stats, new \VanguardLTE\Transformers\GameStatTransformer());
        }
        public function shift(\Illuminate\Http\Request $request)
        {
            if( auth()->user()->hasRole([
                'cashier', 
                'user'
            ]) ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
            $stats = \VanguardLTE\OpenShift::select('open_shift.*')->whereIn('open_shift.shop_id', auth()->user()->availableShops())->orderBy('open_shift.start_date', 'DESC');
            if( $request->id != '' ) 
            {
                $stats = $stats->where('id', $request->id);
            }
            if( $request->username != '' ) 
            {
                $stats = $stats->whereHas('user', function($query) use ($request)
                {
                    $query->where('username', 'like', '%' . $request->username . '%');
                });
            }
            $stats = $stats->paginate(100);
            return $this->respondWithPagination($stats, new \VanguardLTE\Transformers\OpenShiftTransformer());
        }
    }

}
