<?php 
namespace VanguardLTE\Http\Controllers\Api\Transactions
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class TransactionsController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
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
    }

}
