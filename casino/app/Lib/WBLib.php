<?php

namespace VanguardLTE\Lib;

use VanguardLTE\Message;
use VanguardLTE\Statistic;
use VanguardLTE\User;
use VanguardLTE\WelcomeBonus;

class WBLib {

    public static function action($user_id=false){

        if(!$user_id){
            $user_id = auth()->user()->id;
        }

        $user = User::find($user_id);

        if(!$user->hasRole('user')){
            return 0;
        }

        if( !($user->shop && $user->shop->welcome_bonuses_active )){
            return 0;
        }

        $count = WelcomeBonus::where(['shop_id' =>  $user->shop_id])->count();

        if(!$count){
            return 0;
        }

        $statistics = Statistic::where('user_id', $user_id)
            ->whereIn('system', WelcomeBonus::$values['systems'])
            ->orderBy('id', 'ASC')
            ->take($count)
            ->get();

        if( $statistics ){
            foreach ($statistics AS $index=>$statistic){
                $welcomeBonus = WelcomeBonus::where(['shop_id' => $user->shop_id, 'pay' => $index+1])->first();
                $getBonus = Statistic::where(['user_id' => $user_id, 'system' => 'welcome_bonus', 'title' => 'WB ' . ($index+1)])->first();
                if( !$getBonus && $welcomeBonus ){
                    if( $statistic->sum >= $welcomeBonus->sum ){
                        $payeer = User::where('id', $user->parent_id)->first();
                        $data = $user->addBalance('add', $welcomeBonus->bonus, $payeer, false, 'welcome_bonus', false, $welcomeBonus);
                        Message::create(['user_id' => $user->id, 'type' => 'welcome_bonus', 'value' => $welcomeBonus->bonus, 'shop_id' => $user->shop_id]);
                    }
                }
            }
        }

        return 0;

    }


}
