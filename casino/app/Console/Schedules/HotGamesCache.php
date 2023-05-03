<?php


namespace VanguardLTE\Console\Schedules;


use Illuminate\Support\Facades\Cache;
use VanguardLTE\Game;
use VanguardLTE\Shop;
use VanguardLTE\StatGame;

class HotGamesCache
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);

        $shops = Shop::get();

        if( count($shops) ){
            foreach ($shops AS  $shop){
                $shop_id = $shop->id;
                foreach([1, 0] AS $is_mobile){

                    $hot_games = [];

                    $hot_games_stat = StatGame::where('shop_id', $shop_id)->groupBy('game')->take(100)->pluck('game');
                    if($hot_games_stat){
                        $hot_games = Game::where(['view' => 1, 'shop_id' => $shop_id])->whereIn('name', $hot_games_stat);
                        if( $is_mobile ){
                            $hot_games = $hot_games->whereIn('device', [0,2]);
                        }else{
                            $hot_games = $hot_games->whereIn('device', [1,2]);
                        }
                        $hot_games = $hot_games->take(20)->pluck('id');
                        if($hot_games && count($hot_games)){
                            $hot_games = $hot_games->toArray();
                        }
                    }

                    Cache::put('hot_games:'.$shop_id.':'.$is_mobile, $hot_games, 60*60*3);
                }


                // new games

                foreach([1, 0] AS $is_mobile){
                    $random_20_games = [];
                    $last_30_games = Game::where(['view' => 1, 'shop_id' => $shop_id]);
                    if( $is_mobile ){
                        $last_30_games = $last_30_games->whereIn('device', [0,2]);
                    }else{
                        $last_30_games = $last_30_games->whereIn('device', [1,2]);
                    }
                    $last_30_games = $last_30_games->orderBy('id', 'DESC')
                        ->take(30)
                        ->get();
                    if( $last_30_games ){
                        $random_20_games = $last_30_games->random(20)->pluck('id');
                        if($random_20_games && count($random_20_games)){
                            $random_20_games = $random_20_games->toArray();
                        }
                    }
                    Cache::put('new_games:'.$shop_id.':'.$is_mobile, $random_20_games, 60*60*3);
                }







            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('HotGamesCache');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}
