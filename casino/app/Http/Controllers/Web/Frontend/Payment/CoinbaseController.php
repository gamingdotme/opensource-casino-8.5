<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend\Payment
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class CoinbaseController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request)
        {
            $dataSet = $request->all();
            $payment_id = $dataSet['event']['data']['metadata']['payment_id'];
            Info($payment_id . ' ' . $dataSet['event']['type']);
            if( !(isset($dataSet['event']['type']) && $dataSet['event']['type'] == 'charge:confirmed') ) 
            {
                Info('Payment ID #' . $payment_id . ' / Status ' . $dataSet['event']['type']);
                return false;
            }
            $payment = \VanguardLTE\Payment::find($payment_id);
            if( !$payment ) 
            {
                Info('Payment ID #' . $payment_id . ' / Payment not exist');
                return false;
            }
            $paymentSum = $dataSet['event']['data']['payments'][0]['value']['local'];
            if( !(isset($payment_id) && $paymentSum['amount'] == $payment->sum) ) 
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
                    $result = $user->addBalance('add', $payment->credit->credit, $user->referral, true, 'coinbase');
                }
                else
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $payment->sum));
                    $result = $user->addBalance('add', $payment->sum, $user->referral, true, 'coinbase');
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
            return redirect()->route('frontend.game.list')->withSuccess(trans('app.payment_fail'));
        }
        public function wait(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.game.list')->withSuccess(trans('app.payment_wait'));
        }
    }

}
