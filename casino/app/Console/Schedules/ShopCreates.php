<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Category;
use VanguardLTE\FishBank;
use VanguardLTE\Game;
use VanguardLTE\GameBank;
use VanguardLTE\GameCategory;
use VanguardLTE\Invite;
use VanguardLTE\JPG;
use VanguardLTE\Shop;
use VanguardLTE\ShopCategory;
use VanguardLTE\Statistic;
use VanguardLTE\Task;
use VanguardLTE\WheelFortune;

class ShopCreates
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);

        $per_page = 1000;

        $task = Task::where(['finished' => 0, 'category' => 'shop', 'action' => 'create' ])->first();
        if( $task ) {

            $task->update(['finished' => 1]);

            $shop = Shop::find($task->item_id);

            if($shop){

                $shopCategories = ShopCategory::where('shop_id', $shop->id)->get();

                if(count($shopCategories)){
                    $shopCategories = $shopCategories->pluck('category_id')->toArray();
                }


                // JPG
                $jackpots = JPG::where('shop_id', 0)->get();
                if( count($jackpots)){
                    foreach($jackpots AS $jackpot){
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

                // JPG



                $bank = GameBank::where('shop_id', 0)->first();
                if($bank){
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

                $bank = FishBank::where('shop_id', 0)->first();
                if($bank){
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

                // Games
                $game_ids = [];
                if( count($shopCategories) ){
                    $categories = Category::whereIn('parent', $shopCategories)->where('shop_id', 0 )->pluck('id')->toArray();
                    $categories = array_merge($categories, $shopCategories);
                    $game_ids = GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();
                }

                if( count($game_ids) ){

                    $pages = ceil(count($game_ids) / $per_page);
                    for($i=0; $i<$pages; $i++) {
                        $game_ids_temp = array_slice($game_ids, ($i * $per_page), (($i + 1) * $per_page));
                        if (count($game_ids_temp)) {
                            $games = Game::where('shop_id', 0)->whereIn('id', $game_ids_temp)->get();
                            if( count($games) ){
                                foreach($games AS $game){
                                    $newGame = $game->replicate();
                                    $newGame->original_id = $game->id;
                                    $newGame->shop_id = $shop->id;
                                    $newGame->save();

                                }
                            }
                        }
                    }

                    //$games = Game::where('shop_id', 0)->whereIn('id', $game_ids)->get();
                } else {
                    $games = Game::where('shop_id', 0)->get();
                }



                $shop->update(['pending' => 0]);

            }

        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('ShopCreates');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }
}