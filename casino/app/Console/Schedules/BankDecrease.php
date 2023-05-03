<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\FishBank;
use VanguardLTE\GameBank;
use VanguardLTE\Shop;
use VanguardLTE\Statistic;

class BankDecrease
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);


        $step = 10;
        $lower_limit = 15;

        $shops = Shop::get();
        if( $shops ){
            foreach ($shops AS $shop){

                $gamebank = GameBank::where('shop_id', $shop->id)->first();
                $fishbank = FishBank::where('shop_id', $shop->id)->first();
                if($gamebank && $fishbank){
                    foreach (['slots', 'little', 'table_bank', 'fish', 'bonus'] AS $bank){
                        $banker = $gamebank;
                        if( $bank == 'fish' ){
                            $banker = $fishbank;
                        }

                        $type_in = Statistic::select('statistics_add.*')
                            ->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id')
                            ->where('statistics_add.type_in', '!=', NULL)
                            ->where('statistics.shop_id', $shop->id)
                            ->sum('statistics_add.type_in');

                        $type_out = Statistic::select('statistics_add.*')
                            ->join('statistics_add', 'statistics_add.statistic_id', '=', 'statistics.id')
                            ->where('statistics_add.type_out', '!=', NULL)
                            ->where('statistics.shop_id', $shop->id)
                            ->sum('statistics_add.type_out');

                        //Info('------');
                        //Info('type in ' . $type_in);
                        //Info('type out ' . $type_out);
                        //Info('max ' . ($type_in - $type_out));

                        if( $type_in - $type_out <= 0 ){
                            continue;
                        }

                        $max = $type_in - $type_out;

                        //Info($bank . ' ' . $banker->$bank);

                        if( $banker->$bank > $lower_limit ){

                            if( ($banker->$bank - $lower_limit) > $step ){
                                $minus = $step;
                            } else{
                                $minus = $banker->$bank - $lower_limit;
                            }

                            if( $max < $minus ){
                                $minus = $max;
                            }

                            //Info($bank . ' $upper_limit ' . $upper_limit);
                            //Info($bank . ' $max ' . $max);
                            //Info($bank . ' minus ' . $minus);

                            if( $minus <= 0 ){
                                continue;
                            }

                            if( $minus != $step ){
                                continue;
                            }

                            $banker->decrement($bank, $minus);
                            if($bank == 'table_bank'){
                                $bank = 'table';
                            }
                            Statistic::create([
                                'title' => ucfirst($bank),
                                'user_id' => 1,
                                'type' => 'out',
                                'sum' => $minus,
                                'system' => 'bank',
                                'shop_id' => $shop->id
                            ]);
                        }
                    }
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('BankDecrease');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }


    }

}