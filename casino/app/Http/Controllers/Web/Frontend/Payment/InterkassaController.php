<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend\Payment
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class InterkassaController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request)
        {
            $dataSet = $request->all();
            Info($dataSet);
            if( !isset($dataSet['ik_co_id']) ) 
            {
                Info('Payment ID #' . $dataSet['ik_pm_no'] . ' / interkassa id error');
                return false;
            }
            $payment = \VanguardLTE\Payment::find($dataSet['ik_pm_no']);
            if( !$payment ) 
            {
                Info('Payment ID #' . $dataSet['ik_pm_no'] . ' / Payment not exist');
                return false;
            }
            $shop_id = 0;
            if( !$payment->credit ) 
            {
                $shop_id = $payment->shop_id;
            }
            Info($shop_id);
            $id = \VanguardLTE\Lib\Setting::get_value('interkassa', 'shop_id', $shop_id);
            $key = \VanguardLTE\Lib\Setting::get_value('interkassa', 'token', $shop_id);
            Info($key);
            Info($id);
            if( !(isset($dataSet['ik_co_id']) && $dataSet['ik_co_id'] == $id) ) 
            {
                Info('Payment ID #' . $dataSet['ik_pm_no'] . ' / interkassa id error');
                return false;
            }
            Info(json_encode($dataSet));
            unset($dataSet['ik_sign']);
            ksort($dataSet, SORT_STRING);
            array_push($dataSet, $key);
            $signString = implode(':', $dataSet);
            $sign = base64_encode(md5($signString, true));
            if( !(isset($request->ik_sign) && $request->ik_sign == $sign) ) 
            {
                Info('Payment ID #' . $dataSet['ik_pm_no'] . ' / Sign error');
                return false;
            }
            if( !(isset($dataSet['ik_inv_st']) && $dataSet['ik_inv_st'] == 'success') ) 
            {
                Info('Payment ID #' . $dataSet['ik_pm_no'] . ' / Status ' . $dataSet['ik_inv_st']);
                return false;
            }
            if( !(isset($dataSet['ik_am']) && $dataSet['ik_am'] == $payment->sum) ) 
            {
                Info('Payment ID #' . $payment->id . ' / Sum is wrong');
                return false;
            }
            $user = $payment->user;
            if( $user ) 
            {
                if( $payment->credit ) 
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $payment->credit->credit));
                    $result = $user->addBalance('add', $payment->credit->credit, $user->referral, true, 'interkassa');
                }
                else
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $payment->sum));
                    $result = $user->addBalance('add', $payment->sum, $user->referral, true, 'interkassa');
                }
                $result = json_decode($result, true);
                if( $result['status'] == 'error' ) 
                {
                    Info($result['message']);
                    return false;
                }
                $payment->update(['status' => 1]);
            }
        }
        public function success(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.game.list')->withSuccess(trans('app.payment_success'));
        }
        public function fail(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.game.list')->withErrors(trans('app.payment_fail'));
        }
        public function wait(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.game.list')->withSuccess(trans('app.payment_wait'));
        }
    }

}
