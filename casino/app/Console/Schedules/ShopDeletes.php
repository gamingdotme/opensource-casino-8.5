<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Api;
use VanguardLTE\Category;
use VanguardLTE\FishBank;
use VanguardLTE\Game;
use VanguardLTE\GameBank;
use VanguardLTE\HappyHour;
use VanguardLTE\Invite;
use VanguardLTE\JPG;
use VanguardLTE\Message;
use VanguardLTE\OpenShift;
use VanguardLTE\PaymentSetting;
use VanguardLTE\Pincode;
use VanguardLTE\Progress;
use VanguardLTE\Reward;
use VanguardLTE\Security;
use VanguardLTE\ShopCategory;
use VanguardLTE\ShopCountry;
use VanguardLTE\ShopDevice;
use VanguardLTE\ShopOS;
use VanguardLTE\ShopUser;
use VanguardLTE\SMSBonus;
use VanguardLTE\SMSBonusItem;
use VanguardLTE\SMSMailing;
use VanguardLTE\SMSMailingMessage;
use VanguardLTE\StatGame;
use VanguardLTE\Statistic;
use VanguardLTE\StatisticAdd;
use VanguardLTE\Task;
use VanguardLTE\Ticket;
use VanguardLTE\TicketAnswer;
use VanguardLTE\Tournament;
use VanguardLTE\User;
use VanguardLTE\UserActivity;
use VanguardLTE\WelcomeBonus;
use VanguardLTE\WheelFortune;

class ShopDeletes
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $tasks = Task::where(['finished' => 0, 'category' => 'shop', 'action' => 'delete' ])->take(5)->get();
        if( count($tasks)){
            foreach ($tasks AS $task){
                $task->update(['finished' => 1]);

                $shopId = $task->item_id;

                $rel_users = User::whereHas('rel_shops', function ($query) use($shopId)  {
                    $query->where('shop_id', $shopId);
                })->get();

                $toDelete = User::whereIn('role_id', [1,2,3])->where('shop_id', $shopId)->get();
                if($toDelete){
                    foreach($toDelete AS $userDelete){
                        $userDelete->detachAllRoles();
                        $userDelete->delete();
                    }
                }

                $games = Game::where('shop_id', $shopId)->get();

                foreach($games AS $game){
                    //Game::where('id', $game->id)->delete();
                    Game::destroy($game->id);
                    //GameCategory::where('game_id', $game->id)->delete();
                    //GameLog::where('game_id', $game->id)->delete();
                }

                $tournaments = Tournament::where('shop_id', $shopId)->get();
                if($tournaments){
                    foreach($tournaments AS $tournament){
                        $tournament->delete();
                    }
                }

                StatGame::where('shop_id', $shopId)->delete();
                Category::where('shop_id', $shopId)->delete();
                OpenShift::where('shop_id', $shopId)->delete();
                ShopUser::where('shop_id', $shopId)->delete();
                Statistic::where('shop_id', $shopId)->delete();
                StatisticAdd::where('shop_id', $shopId)->delete();
                PaymentSetting::where('shop_id', $shopId)->delete();
                Api::where('shop_id', $shopId)->delete();
                ShopCategory::where('shop_id', $shopId)->delete();
                JPG::where('shop_id', $shopId)->delete();
                Pincode::where('shop_id', $shopId)->delete();
                HappyHour::where('shop_id', $shopId)->delete();
                GameBank::where('shop_id', $shopId)->delete();
                FishBank::where('shop_id', $shopId)->delete();
                Invite::where('shop_id', $shopId)->delete();
                WheelFortune::where('shop_id', $shopId)->delete();
                ShopCountry::where('shop_id', $shopId)->delete();
                ShopOS::where('shop_id', $shopId)->delete();
                ShopDevice::where('shop_id', $shopId)->delete();
                Progress::where('shop_id', $shopId)->delete();
                WelcomeBonus::where('shop_id', $shopId)->delete();
                SMSBonus::where('shop_id', $shopId)->delete();
                SMSBonusItem::where('shop_id', $shopId)->delete();
                Reward::where('shop_id', $shopId)->delete();
                Message::where('shop_id', $shopId)->delete();
                Ticket::where('shop_id', $shopId)->delete();
                TicketAnswer::where('shop_id', $shopId)->delete();
                Security::where('shop_id', $shopId)->delete();
                SMSMailing::where('shop_id', $shopId)->delete();
                SMSMailingMessage::where('shop_id', $shopId)->delete();
                UserActivity::where('shop_id', $shopId)->delete();

                if($rel_users){
                    foreach($rel_users AS $user){
                        //if($user = $user->user){
                        if( $user->hasRole(['agent', 'distributor']) ){
                            $shops = $user->shops(true);
                            if( count($shops) ){
                                if(!is_array($shops)){
                                    $shops = $shops->toArray();
                                }
                                $user->update(['shop_id' => array_shift($shops)]);
                            } else{
                                $user->update(['shop_id' => 0]);
                            }
                        }
                        //}
                    }
                }

                User::doesntHave('rel_shops')->where('shop_id', '!=', 0)->whereIn('role_id', [4,5])->update(['shop_id' => 0]);


                $admin = User::where('role_id', 6)->first();
                if($admin->shop_id == $shopId){
                    $admin->update(['shop_id' => 0]);
                }

            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('ShopDeletes');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}
