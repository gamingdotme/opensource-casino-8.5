<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class SMSController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request)
        {
            $dataSet = $request->all();
            $sms = \VanguardLTE\SMS::where('message_id', $dataSet['messageId'])->first();
            if( $sms ) 
            {
                $sms->update(['status' => $dataSet['status']]);
            }
        }
    }

}
