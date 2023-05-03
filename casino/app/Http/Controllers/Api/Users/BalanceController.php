<?php 
namespace VanguardLTE\Http\Controllers\Api\Users
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class BalanceController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function balance(\Illuminate\Http\Request $request, \VanguardLTE\User $user, $type = 'add')
        {
            if( auth()->user()->hasRole('admin') && $user->hasRole(['agent']) || auth()->user()->hasRole('agent') && $user->hasRole(['distributor']) || auth()->user()->hasRole('cashier') && $user->hasRole('user') ) 
            {
                if( !in_array($type, [
                    'add', 
                    'out'
                ]) ) 
                {
                    $type = 'add';
                }
                $request->summ = floatval($request->summ);
                if( $request->all && $request->all == '1' ) 
                {
                    $request->summ = $user->balance;
                }
                if( $type == 'add' ) 
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $request->summ));
                }
                else
                {
                    event(new \VanguardLTE\Events\User\MoneyOut($user, $request->summ));
                }
                $result = $user->addBalance($type, $request->summ);
                $result = json_decode($result, true);
                if( $result['status'] == 'error' ) 
                {
                    return $this->errorWrongArgs($result['message']);
                }
                return $this->respondWithSuccess();
            }
            else
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
        }
    }

}
