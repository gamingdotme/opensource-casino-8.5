<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Category;
use VanguardLTE\FishBank;
use VanguardLTE\Game;
use VanguardLTE\GameBank;
use VanguardLTE\GameCategory;
use VanguardLTE\Invite;
use VanguardLTE\JPG;
use VanguardLTE\Progress;
use VanguardLTE\Shop;
use VanguardLTE\ShopCategory;
use VanguardLTE\SMSBonus;
use VanguardLTE\Statistic;
use VanguardLTE\Task;
use VanguardLTE\WelcomeBonus;
use VanguardLTE\WheelFortune;

class Synchronization
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);


        $task = Task::where(['finished' => 0, 'category' => 'settings', 'action' => 'sync' ])->first();
        if($task){
            $task->delete();

            $shops = Shop::get();

            if( count($shops) ){
                foreach($shops AS $shop){

                    $shopCategories = ShopCategory::where('shop_id', $shop->id)->pluck('category_id')->toArray();

                    // Games
                    $game_ids = [];
                    if( count($shopCategories) ){
                        $categories = Category::whereIn('parent', $shopCategories)->pluck('id')->toArray();
                        $categories = array_merge($categories, $shopCategories);
                        $game_ids = GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();
                    }

                    $allCurrentGameIds = [];

                    // Get Shop 0 games
                    $gameInShop0 = [];
                    $gamesShop0 = Game::where(['shop_id' => 0])->whereIn('id', $game_ids)->get();
                    if( $gamesShop0 && count($gamesShop0)){
                        foreach ($gamesShop0 as $item) {
                            $gameInShop0[$item->id] = $item;
                            $allCurrentGameIds[] = $item->id;
                        }
                    }

                    // Get Shop 0 games
                    $gameInShopCur = [];
                    $gamesShopCur = Game::where(['shop_id' => $shop->id])->get();
                    if( $gamesShopCur && count($gamesShopCur)){
                        foreach ($gamesShopCur as $item) {
                            $gameInShopCur[$item->original_id] = $item;

                        }
                    }



                    foreach($gameInShop0 AS $game_id=>$gameShop0){

                        // if game exist in cur shop
                        if( isset($gameInShopCur[$game_id]) ){

                            foreach ($gameInShopCur[$game_id]->getFillable() AS $field){
                                if( !in_array($field, ['gamebank', 'bids', 'stat_in', 'stat_out', 'view', 'shop_id']) ){
                                    $gameInShopCur[$game_id]->$field = $gameShop0->$field;
                                }
                            }

                            $gameInShopCur[$game_id]->save();


                        } else{
                            // copy new game from shop 0
                            $newGame = $gameShop0->replicate();
                            $newGame->original_id = $game_id;
                            $newGame->shop_id = $shop->id;
                            $newGame->save();
                        }

                    }


                    // Delete Not Existing Games
                    foreach($gameInShopCur AS $game_id=>$gameShopCur){
                        if(!in_array($gameShopCur->original_id, $allCurrentGameIds)){
                            $gameShopCur->delete();
                        }
                    }


                    Progress::where('shop_id', $shop->id)->delete();
                    WelcomeBonus::where('shop_id', $shop->id)->delete();
                    SMSBonus::where('shop_id', $shop->id)->delete();
                    Invite::where('shop_id', $shop->id)->delete();
                    WheelFortune::where('shop_id', $shop->id)->delete();

                    // JPG
                    $jackpots = JPG::where('shop_id', 0)->get();
                    if( count($jackpots)){
                        foreach($jackpots AS $jackpot){
                            $exist = JPG::where(['shop_id' => $shop->id, 'name' => $jackpot->name])->first();
                            if(!$exist){
                                $newJackpot = $jackpot->replicate();
                                $newJackpot->shop_id = $shop->id;
                                $newJackpot->save();
                                if($newJackpot->balance > 0 ){
                                    Statistic::create(['title' => $newJackpot->name,
                                        'user_id' => 1,
                                        'type' => 'add',
                                        'system' => 'jpg',
                                        'sum' => $newJackpot->balance,
                                        'shop_id' => $shop->id]);
                                }
                            }
                        }
                    }

                    $bank = GameBank::where('shop_id', 0)->first();
                    if($bank){
                        $exist = GameBank::where(['shop_id' => $shop->id])->first();
                        if(!$exist){
                            $newBank = $bank->replicate();
                            $newBank->shop_id = $shop->id;

                            if( settings('default_slots') > 0 ){
                                $newBank->slots = settings('default_slots');
                                Statistic::create(['title' => 'Slots', 'user_id' => 1, 'type' => 'add',
                                    'sum' => $newBank->slots, 'system' => 'bank',
                                    'old' => 0,
                                    'shop_id' => $shop->id ]);
                            }
                            if( settings('default_little') > 0 ){
                                $newBank->little = settings('default_little');
                                Statistic::create(['title' => 'Little', 'user_id' => 1, 'type' => 'add',
                                    'sum' => $newBank->little, 'system' => 'bank',
                                    'old' => 0,
                                    'shop_id' => $shop->id ]);
                            }
                            if( settings('default_table') > 0 ){
                                $newBank->table_bank = settings('default_table');
                                Statistic::create(['title' => 'Table', 'user_id' => 1, 'type' => 'add',
                                    'sum' => $newBank->table_bank, 'system' => 'bank',
                                    'old' => 0,
                                    'shop_id' => $shop->id ]);
                            }
                            if( settings('default_bonus') > 0 ){
                                $newBank->bonus = settings('default_bonus');
                                Statistic::create(['title' => 'Bonus', 'user_id' => 1, 'type' => 'add',
                                    'sum' => $newBank->bonus, 'system' => 'bank',
                                    'old' => 0,
                                    'shop_id' => $shop->id ]);
                            }
                            $newBank->save();
                        }
                    }


                    $bank = FishBank::where('shop_id', 0)->first();
                    if($bank){
                        $exist = FishBank::where(['shop_id' => $shop->id])->first();
                        if(!$exist){
                            $newBank = $bank->replicate();
                            $newBank->shop_id = $shop->id;
                            if( settings('default_fish') > 0 ){
                                $newBank->fish = settings('default_fish');
                                Statistic::create(['title' => 'Fish', 'user_id' => 1, 'type' => 'add',
                                    'sum' => $newBank->fish, 'system' => 'bank',
                                    'old' => 0,
                                    'shop_id' => $shop->id ]);
                            }
                            $newBank->save();
                        }
                    }

                    // JPG
                    $welcomebonuses = WelcomeBonus::where('shop_id', 0)->get();
                    if( count($welcomebonuses)){
                        foreach($welcomebonuses AS $item){
                            $newWelcomeBonus = $item->replicate();
                            $newWelcomeBonus->shop_id = $shop->id;
                            $newWelcomeBonus->save();
                        }
                    }

                    $smsbonuses = SMSBonus::where('shop_id', 0)->get();
                    if( count($smsbonuses)){
                        foreach($smsbonuses AS $item){
                            $newSMSBonus = $item->replicate();
                            $newSMSBonus->shop_id = $shop->id;
                            $newSMSBonus->save();
                        }
                    }

                    $progress = Progress::where('shop_id', 0)->get();
                    if( count($progress)){
                        foreach($progress AS $item){
                            $newProgress = $item->replicate();
                            $newProgress->shop_id = $shop->id;
                            $newProgress->save();
                        }
                    }

                    // invite
                    $invite = Invite::where('shop_id', 0)->first();
                    if($invite){
                        $newInvite = $invite->replicate();
                        $newInvite->shop_id = $shop->id;
                        $newInvite->save();
                    }

                    $wheelfortune = WheelFortune::where('shop_id', 0)->first();
                    if($wheelfortune){
                        $newWheelfortune = $wheelfortune->replicate();
                        $newWheelfortune->shop_id = $shop->id;
                        $newWheelfortune->save();
                    }


                }
            }
        }


        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('Synchronization');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }


    }

}