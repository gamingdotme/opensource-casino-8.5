<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class OpenShiftController extends ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function start_shift(\Illuminate\Http\Request $request)
        {
            $user = auth()->user();
            $open_shift = '';
            if( $user->hasRole('user') ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
            if( !$user->hasRole('cashier') ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
            $users = \VanguardLTE\User::where([
                'shop_id' => $user->shop_id, 
                'role_id' => 1
            ])->where('balance', '>', 0)->count();
            if( $users ) 
            {
                return $this->errorWrongArgs(trans('app.users_with_balance', ['count' => $users]));
            }
            $count = \VanguardLTE\OpenShift::where([
                'shop_id' => $user->shop_id, 
                'end_date' => null
            ])->first();
            if( $count ) 
            {
                $summ = \VanguardLTE\User::where([
                    'shop_id' => $user->shop_id, 
                    'role_id' => 1
                ])->sum('balance');
                $count->update([
                    'end_date' => \Carbon\Carbon::now(), 
                    'last_banks' => $count->banks(), 
                    'users' => $summ
                ]);
            }
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            if( $count ) 
            {
                $open_shift = \VanguardLTE\OpenShift::create([
                    'start_date' => \Carbon\Carbon::now(), 
                    'balance' => ($count->balance + $count->balance_in) - $count->balance_out, 
                    'old_banks' => $count->banks(), 
                    'user_id' => $user->id, 
                    'shop_id' => $user->shop_id
                ]);
            }
            else
            {
                $open_shift = \VanguardLTE\OpenShift::create([
                    'start_date' => \Carbon\Carbon::now(), 
                    'balance' => 0, 
                    'user_id' => $user->id, 
                    'shop_id' => $user->shop_id
                ]);
            }
            $temps = \VanguardLTE\OpenShiftTemp::where('shop_id', $user->shop_id)->get();
            if( count($temps) ) 
            {
                foreach( $temps as $temp ) 
                {
                    if( $temp->type == 'inc' ) 
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
            return $this->respondWithSuccess();
        }
        public function info(\Illuminate\Http\Request $request)
        {
            if( !$request->shop_id ) 
            {
                return $this->errorNotFound();
            }
            if( !in_array($request->shop_id, auth()->user()->availableShops()) ) 
            {
                return $this->errorNotFound();
            }
            $shift = \VanguardLTE\OpenShift::where([
                'shop_id' => $request->shop_id, 
                'end_date' => null
            ])->first();
            if( $shift ) 
            {
                return $this->respondWithArray([
                    'id' => $shift->id, 
                    'start_date' => $shift->start_date
                ]);
            }
            return $this->errorWrongArgs(trans('app.shift_not_opened'));
        }
    }

}
