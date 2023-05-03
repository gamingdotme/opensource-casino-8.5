<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use VanguardLTE\Lib\SMS_sender;
use VanguardLTE\Shop;
use VanguardLTE\SMSBonus;
use VanguardLTE\SMSBonusItem;
use VanguardLTE\User;

class SMSBonuses
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);


        $sms_bonuses = [];

        $users = User::where('role_id', 1)->get();
        $shops = Shop::get();



        if(count($shops)){
            foreach ($shops AS $shop){
                $sms_bonuses[$shop->id] = SMSBonus::where(['shop_id' => $shop->id])->orderBy('days', 'ASC')->get();
            }
        }

        if(count($users)){
            foreach ($users AS $user){

                if( !($user->shop && $user->shop->sms_bonuses_active) ){
                    continue;
                }

                if( $user->phone != '' && $user->phone_verified ){
                    if( isset($sms_bonuses[$user->shop_id]) && count($sms_bonuses[$user->shop_id])){
                        $diffInDays =  Carbon::now()->diffInDays(Carbon::parse($user->last_bid));
                        foreach ($sms_bonuses[$user->shop_id] AS $sms_bonus){
                            if($sms_bonus->days == $diffInDays){
                                $exist = SMSBonusItem::where(['user_id' => $user->id, 'days' => $sms_bonus->days, 'status' => 0])->orderBy('id', 'DESC')->first();
                                if(!$exist){
                                    SMSBonusItem::where(['user_id' => $user->id, 'status' => 0])->orderBy('id', 'DESC')->delete();
                                    SMSBonusItem::create([
                                        'user_id' => $user->id,
                                        'days' => $sms_bonus->days,
                                        'sms_bonus_id' => $sms_bonus->id,
                                        'status' => 0,
                                        'bonus' => $sms_bonus->bonus,
                                        'shop_id' => $sms_bonus->shop_id
                                    ]);
                                    $text = 'New SMS Bonus ' . $sms_bonus->bonus;
                                    SMS_sender::send($user->phone, $text, $user->id);
                                }
                            }
                        }
                    }
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('SMSBonuses');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}
