<?php
/**
 * Created by PhpStorm.
 * User: Omen
 * Date: 07.09.2019
 * Time: 17:15
 */

namespace VanguardLTE\Lib;

use Illuminate\Support\Facades\Http;
use VanguardLTE\Shop;

class Interkassa {

    public $shop_id;

public function __construct($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    public static function get_systems($user_id, $shop_id, $payment_id=0, $amount=5){

        $shop = Shop::find($shop_id);

        $response = Http::withHeaders([
            'Content-Type' => 'json'
        ])->get('https://sci.interkassa.com/', [
            'ik_co_id' => Setting::get_value('interkassa', 'shop_id', $shop_id),
            'ik_pm_no' => $payment_id,
            'ik_am' => $amount,
            'ik_cur' => $shop ? $shop->currency : settings('default_currency'),
            'ik_desc' => 'Account replenishment for a client #'. $user_id,
            'ik_act' => 'payways',
            'ik_int' => 'json'
        ]);

        $result = $response->json();

        if( isset($result['resultCode'], $result['resultData']['paywaySet']) && $result['resultCode'] == 0){
            return [
                'success' => true,
                'systems' => $result['resultData']['paywaySet']
            ];
        }

        if( isset($result['resultCode'], $result['resultMsg']) && $result['resultCode'] > 0){
            return [
                'fail' => true,
                'systems' => $result['resultMsg']
            ];
        }

        return [
            'fail' => true,
            'systems' => __('app.something_went_wrong')
        ];

    }

    public static function get_form($user_id, $shop_id, $payment_id, $amount, $method, $data=[]){

        $shop = Shop::find($shop_id);

        $post = [
            'ik_co_id' => Setting::get_value('interkassa', 'shop_id', $shop_id),
            'ik_pm_no' => $payment_id,
            'ik_am' => $amount,
            'ik_cur' => $shop ? $shop->currency : settings('default_currency'),
            'ik_desc' => 'Account replenishment for a client #'. $user_id,
            'ik_act' => 'process',
            'ik_int' => 'json',
            'ik_pw_via' => Interkassa::str_replace_once($method),
            //'ik_pnd_u' => route('payment.interkassa.wait'),
            //'ik_suc_u' => route('payment.interkassa.success'),
            //'ik_fal_u' => route('payment.interkassa.fail'),
        ];

        if( isset($data['ik_suc_u']) ){
            $post['ik_suc_u'] = $data['ik_suc_u'];
        } else{
            $post['ik_suc_u'] = route('payment.interkassa.success');
        }

        if( isset($data['ik_pnd_u']) ){
            $post['ik_pnd_u'] = $data['ik_pnd_u'];
        } else{
            $post['ik_pnd_u'] = route('payment.interkassa.wait');
        }

        if( isset($data['ik_fal_u']) ){
            $post['ik_fal_u'] = $data['ik_fal_u'];
        } else{
            $post['ik_fal_u'] = route('payment.interkassa.fail');
        }

        $response = Http::withHeaders([
            'Content-Type' => 'json'
        ])->get('https://sci.interkassa.com/', $post);

        $result = $response->json();

        if( isset($result['resultCode'], $result['resultData']['internalForm']) && $result['resultCode'] == 0){
            return [
                'success' => true,
                'form' => $result['resultData']['internalForm']
            ];
        }

        if( isset($result['resultCode'], $result['resultData']['paymentForm']) && $result['resultCode'] == 0){
            return [
                'success' => true,
                'form' => $result['resultData']['paymentForm']
            ];
        }

        if( isset($result['resultCode'], $result['resultMsg']) && $result['resultCode'] > 0){
            return [
                'fail' => true,
                'text' => $result['resultMsg']
            ];
        }

        return [
            'fail' => true,
            'systems' => __('app.something_went_wrong')
        ];

    }

    public static function str_replace_once($text)
    {
        $pos = strpos($text, 'interkassa_');
        return $pos!==false ? substr_replace($text, '', $pos, strlen('interkassa_')) : $text;
    }

}