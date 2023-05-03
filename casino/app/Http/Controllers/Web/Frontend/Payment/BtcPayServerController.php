<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend\Payment
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class BtcPayServerController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request)
        {
            $dataSet = $request->all();
            if( !(isset($dataSet['orderId']) && isset($dataSet['id'])) ) 
            {
                Info('Wrong input array');
                return false;
            }
            $payment_id = $dataSet['orderId'];
            $payment = \VanguardLTE\Payment::find($payment_id);
            if( !$payment ) 
            {
                Info('Payment ID #' . $payment_id . ' / Payment not exist');
                return false;
            }
            $api_token = \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'api_token', $payment->shop_id);
            $server = \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'server', $payment->shop_id);
            $storeId = \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'store_id', $payment->shop_id);
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json', 
                'Authorization' => 'token ' . $api_token
            ])->get($server . '/api/v1/stores/' . $storeId . '/invoices/' . $dataSet['id']);
            if( !(isset($response['status']) && isset($response['amount']) && isset($response['id'])) ) 
            {
                Info('Wrong response array');
                return false;
            }
            if( !in_array(mb_strtolower($response['status']), [
                'confirmed', 
                'complete', 
                'settled'
            ]) ) 
            {
                Info('Payment ID #' . $payment_id . ' / Status ' . $response['status']);
                return false;
            }
            if( $response['amount'] < $payment->give ) 
            {
                Info('Payment ID #' . $payment->id . ' / Sum is wrong');
                return false;
            }
            if( $payment->status ) 
            {
                Info('Payment ID #' . $payment->id . ' /paid');
                return false;
            }
            $user = $payment->user;
            if( $user = $payment->user ) 
            {
                if( $payment->credit ) 
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $payment->credit->credit));
                    $result = $user->addBalance('add', $payment->credit->credit, $user->referral, true, 'btcpayserver');
                }
                else
                {
                    event(new \VanguardLTE\Events\User\MoneyIn($user, $payment->sum));
                    $result = $user->addBalance('add', $payment->sum, $user->referral, true, 'btcpayserver');
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
        public function redirect(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.game.list')->withSuccess(trans('app.payment_wait'));
        }
    }

}
