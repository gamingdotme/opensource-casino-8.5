<?php

namespace VanguardLTE\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
use VanguardLTE\WelcomeBonus;
use VanguardLTE\WheelFortune;


class StartSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){


        $shops = Shop::get();

        if( count($shops) ){
            foreach($shops AS $shop){

                $per_page = 1000;
                //Category::where('shop_id', $shop->id)->delete();

                // Clear Game Categories
                $games = Game::where('shop_id', $shop->id)->get();
                if( count($games) ){
                    foreach($games AS $game){
                        GameCategory::where('game_id', $game->id)->delete();
                        //GameBank::where('game_id', $game->id)->delete();
                    }
                }

                $shopCategories = ShopCategory::where('shop_id', $shop->id)->pluck('category_id')->toArray();

                // Games
                $game_ids = [];
                if( count($shopCategories) ){
                    $categories = Category::whereIn('parent', $shopCategories)->pluck('id')->toArray();
                    $categories = array_merge($categories, $shopCategories);
                    $game_ids = GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();
                }


                if(count($game_ids)){
                    $pages = ceil(count($game_ids) / $per_page);
                    for($i=0; $i<$pages; $i++){
                        $game_ids_temp = array_slice($game_ids, ($i*$per_page), (($i+1)*$per_page));
                        if(count($game_ids_temp)){
                            $games = Game::where('shop_id', 0)->whereIn('id', $game_ids_temp)->groupBy('original_id')->get();
                            if( count($games) ){

                                foreach($games AS $game){
                                    $newGame = $game->replicate();
                                    $newGame->original_id = $game->id;
                                    $newGame->shop_id = $shop->id;
                                    $oldGame = Game::where(['original_id' => $game->original_id, 'shop_id' => $shop->id])->orderBy('id', 'ASC')->first();
                                    if($oldGame){
                                        $newGame->gamebank = $oldGame->gamebank;
                                        $newGame->bids = $oldGame->bids;
                                        $newGame->stat_in = $oldGame->stat_in;
                                        $newGame->stat_out = $oldGame->stat_out;
                                        $newGame->view = $oldGame->view;

                                        $oldGames = Game::where(['original_id' => $game->original_id, 'shop_id' => $shop->id])->get();
                                        if($oldGames){
                                            foreach($oldGames AS $oldGameItem){
                                                $oldGameItem->delete();
                                            }
                                        }

                                    }
                                    $newGame->save();
                                }

                            }
                        }


                    }

                }

                Progress::where('shop_id', $shop->id)->delete();
                Invite::where('shop_id', $shop->id)->delete();
                WheelFortune::where('shop_id', $shop->id)->delete();
                WelcomeBonus::where('shop_id', $shop->id)->delete();
                SMSBonus::where('shop_id', $shop->id)->delete();

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
                $progress = Progress::where('shop_id', 0)->get();
                if( count($progress)){
                    foreach($progress AS $item){
                        $newProgress = $item->replicate();
                        $newProgress->shop_id = $shop->id;
                        $newProgress->save();
                    }
                }


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
}
