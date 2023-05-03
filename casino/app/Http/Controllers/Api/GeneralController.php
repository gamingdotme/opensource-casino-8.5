<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class GeneralController extends ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
        }
        public function paysystems(\Illuminate\Http\Request $request)
        {
            $systems = [
                'coinbase' => settings('payment_coinbase') && \VanguardLTE\Lib\Setting::is_available('coinbase', auth()->user()->shop_id), 
                'btcpayserver' => settings('payment_btcpayserver') && \VanguardLTE\Lib\Setting::is_available('btcpayserver', auth()->user()->shop_id), 
                'pin' => settings('payment_pin')
            ];
            $interkassa = \VanguardLTE\Lib\Interkassa::get_systems(auth()->user()->id, auth()->user()->shop_id);
            if( settings('payment_interkassa') && \VanguardLTE\Lib\Setting::is_available('interkassa', auth()->user()->shop_id) && isset($interkassa['success']) && count($interkassa['systems']) ) 
            {
                foreach( $interkassa['systems'] as $system ) 
                {
                    $systems['interkassa_' . $system['als']] = 1;
                }
            }
            return [
                'success' => true, 
                'systems' => $systems
            ];
        }
    }

}
