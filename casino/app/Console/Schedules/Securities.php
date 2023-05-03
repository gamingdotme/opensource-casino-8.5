<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use VanguardLTE\Events\User\Banned;
use VanguardLTE\Game;
use VanguardLTE\GameBank;
use VanguardLTE\Lib\SMS_sender;
use VanguardLTE\Security;
use VanguardLTE\Session;
use VanguardLTE\Shop;
use VanguardLTE\StatGame;
use VanguardLTE\StatisticAdd;
use VanguardLTE\Support\Enum\UserStatus;
use VanguardLTE\User;

class Securities
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);

        $five_minutes = Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s');

        $limit = 1000;

        $gameCount = Game::where('shop_id', '!=', 0)->count();
        for($i=0; $i<ceil($gameCount / $limit); $i++){
            $games = Game::where('shop_id', '!=', 0)->skip($i*$limit)->take($limit)->get();
            if($games){
                foreach ($games AS $game){
                    $stat_total = $game->stat_in - $game->stat_out;
                    if( settings('game_in_out_x') && $stat_total < settings('game_in_out_x') ){
                        $exist = Security::where(['type' => 'game', 'item_id' => $game->id])->first();
                        if(!$exist){
                            $rtp = $game->stat_in > 0 ? ($game->stat_out / $game->stat_in) * 100 : 0;
                            $exist = Security::create([
                                'type' => 'game',
                                'item_id' => $game->id,
                                'pay_in' => $game->stat_in,
                                'pay_out' => $game->stat_out,
                                'pay_total' => $stat_total,
                                'rtp' => $rtp,
                                'count' => $game->bids,
                                'shop_id' => $game->shop_id,
                                'category' => 'game_in_out_x'
                            ]);
                            if( settings('game_in_out_x_block') ){
                                $exist->update(['block' => 1]);
                                Game::where('name', $game->name)->update(['view' => 0]);
                            }
                            if( settings('game_in_out_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.game_in_out_x_notify', ['game' => $game->name, 'shop' => $game->shop ? $game->shop->name : 'NoShop', 'in_out' => number_format($stat_total, 2, '.', '')]));
                            }
                        }

                    }
                }
            }
        }


        // Shops
        $shopsCount = Shop::where('id', '>', 0)->count();
        for($i=0; $i<ceil($shopsCount / $limit); $i++){
            $shops = Shop::where('id', '>', 0)->skip($i*$limit)->take($limit)->get();
            if($shops){
                foreach ($shops AS $shop){
                    if( settings('shop_balance_x') && $shop->balance >= settings('shop_balance_x') ){
                        $exist = Security::where(['type' => 'shop', 'item_id' => $shop->id])->first();
                        if(!$exist){
                            $exist = Security::create([
                                'type' => 'shop',
                                'item_id' => $shop->id,
                                'balance' => $shop->balance,
                                'shop_id' => $shop->id,
                                'category' => 'shop_balance_x'
                            ]);
                            if( settings('shop_balance_x_block') ){
                                $exist->update(['block' => 1]);
                                Shop::where('id', $shop->id)->update(['is_blocked' => 1]);
                                $users = User::where('shop_id', $shop->id)->whereIn('role_id', [1,2,3])->get();
                                if($users){
                                    foreach($users AS $user){
                                        Session::where('user_id', $user->id)->delete();
                                        $user->update(['remember_token' => null]);
                                    }
                                }
                            }
                            if( settings('shop_balance_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.shop_balance_x_notify', ['shop' => $shop->name, 'balance' => number_format($shop->balance, 2, '.', '')]));
                            }
                        }
                    }
                    //if( settings('shop_balance_bigger_x_block') || settings('shop_balance_bigger_x_sms')){
                        $shop_in = StatisticAdd::where('shop_id',  $shop->id)->sum('credit_in');
                        $shop_out = StatisticAdd::where('shop_id', $shop->id)->sum('credit_out');
                        if( $shop->balance > ($shop_in - $shop_out) ){
                            $exist = Security::where(['type' => 'shop_balance_bigger', 'item_id' => $shop->id])->first();
                            if(!$exist){
                                $exist = Security::create([
                                    'type' => 'shop_balance_bigger',
                                    'item_id' => $shop->id,
                                    'pay_in' => $shop_in,
                                    'pay_out' => $shop_out,
                                    'pay_total' => $shop_in - $shop_out,
                                    'balance' => $shop->balance,
                                    'shop_id' => $shop->id,
                                    'category' => 'shop_balance_bigger_x'
                                ]);
                                if(settings('shop_balance_bigger_x_block')){
                                    $exist->update(['block' => 1]);
                                    Shop::where('id', $shop->id)->update(['is_blocked' => 1]);
                                    $users = User::where('shop_id', $shop->id)->whereIn('role_id', [1,2,3])->get();
                                    if($users){
                                        foreach($users AS $user){
                                            Session::where('user_id', $user->id)->delete();
                                            $user->update(['remember_token' => null]);
                                        }
                                    }
                                }
                                if(settings('shop_balance_bigger_x_sms')){
                                    $exist->update(['sms' => 1]);
                                    SMS_sender::notify(__('app.shop_balance_bigger_x_notify',
                                        [
                                            'shop' => $shop->name,
                                            'balance' => number_format($shop->balance, 2, '.', ''),
                                            'in' => number_format($shop_in, 2, '.', ''),
                                            'out' => number_format($shop_out, 2, '.', ''),
                                        ]
                                    ));
                                }
                            }
                        }
                    //}
                }
            }
        }

        // GameBanks
        $gameBanksCount = Shop::where('id', '>', 0)->count();
        for($i=0; $i<ceil($gameBanksCount / $limit); $i++){
            $gamebanks = GameBank::where('id', '>', 0)->skip($i*$limit)->take($limit)->get();
            if($gamebanks){
                foreach ($gamebanks AS $gamebank){
                    foreach (['slots', 'little', 'table_bank', 'bonus'] AS $bank){
                        if( settings('bank_balance_x') && $gamebank->$bank >= settings('bank_balance_x') ){
                            $exist = Security::where(['type' => 'shop', 'item_id' => $gamebank->id])->first();
                            if(!$exist){
                                $exist = Security::create([
                                    'type' => 'shop',
                                    'item_id' => $gamebank->id,
                                    'bank' => $bank,
                                    'balance' => $gamebank->$bank,
                                    'shop_id' => $gamebank->shop_id,
                                    'category' => 'bank_balance_x'
                                ]);
                                if( settings('bank_balance_x_block') ){
                                    $exist->update(['block' => 1]);
                                    Shop::where('id', $gamebank->shop_id)->update(['is_blocked' => 1]);
                                    $users = User::where('shop_id', $gamebank->shop_id)->whereIn('role_id', [1,2,3])->get();
                                    if($users){
                                        foreach($users AS $user){
                                            Session::where('user_id', $user->id)->delete();
                                            $user->update(['remember_token' => null]);
                                        }
                                    }
                                }
                                if( settings('bank_balance_x_sms') ){
                                    $exist->update(['sms' => 1]);
                                    SMS_sender::notify(__('app.bank_balance_x_notify', ['bank' => $bank, 'shop' => $gamebank->shop ? $gamebank->shop->name : 'NoShop', 'balance' => number_format($gamebank->$bank, 2, '.', '')]));
                                }
                            }
                        }
                    }
                }
            }
        }

        // FishBanks
        $fishBanksCount = Shop::where('id', '>', 0)->count();
        for($i=0; $i<ceil($fishBanksCount / $limit); $i++){
            $fishbanks = GameBank::where('id', '>', 0)->skip($i*$limit)->take($limit)->get();
            if($fishbanks){
                foreach ($fishbanks AS $fishbank){
                    foreach (['fish'] AS $bank){
                        if( settings('bank_balance_x') && $fishbank->$bank >= settings('bank_balance_x') ){
                            $exist = Security::where(['type' => 'shop', 'item_id' => $fishbank->id])->first();
                            if(!$exist){
                                $exist = Security::create([
                                    'type' => 'shop',
                                    'item_id' => $fishbank->id,
                                    'bank' => $bank,
                                    'balance' => $fishbank->$bank,
                                    'shop_id' => $fishbank->shop_id,
                                    'category' => 'bank_balance_x'
                                ]);
                                if( settings('bank_balance_x_block') ){
                                    $exist->update(['block' => 1]);
                                    Shop::where('id', $fishbank->shop_id)->update(['is_blocked' => 1]);
                                    $users = User::where('shop_id', $fishbank->shop_id)->whereIn('role_id', [1,2,3])->get();
                                    if($users){
                                        foreach($users AS $user){
                                            Session::where('user_id', $user->id)->delete();
                                            $user->update(['remember_token' => null]);
                                        }
                                    }
                                }
                                if( settings('bank_balance_x_sms') ){
                                    $exist->update(['sms' => 1]);
                                    SMS_sender::notify(__('app.bank_balance_x_notify', ['bank' => $bank, 'shop' => $fishbank->shop ? $fishbank->shop->name : 'NoShop', 'balance' => number_format($fishbank->$bank, 2, '.', '')]));
                                }
                            }

                        }
                    }
                }
            }
        }

        // Agents
        $agentsCount = User::where('role_id', 5)->count();
        for($i=0; $i<ceil($agentsCount / $limit); $i++){
            $agents = User::where('role_id', 5)->skip($i*$limit)->take($limit)->get();
            if($agents){
                foreach ($agents AS $agent){
                    if( settings('agent_balance_x') && $agent->balance >= settings('agent_balance_x') ){
                        $exist = Security::where(['type' => 'user_balance', 'item_id' => $agent->id])->first();
                        if(!$exist){
                            $exist = Security::create([
                                'type' => 'user_balance',
                                'item_id' => $agent->id,
                                'pay_in' => $agent->total_in,
                                'pay_out' => $agent->total_out,
                                'pay_total' => $agent->total_in - $agent->total_out,
                                'balance' => $agent->balance,
                                'shop_id' => $agent->shop_id,
                                'category' => 'agent_balance_x'
                            ]);
                            if( settings('agent_balance_x_block') ){
                                $exist->update(['block' => 1]);

                                /*
                                $users = User::whereIn('id', [$agent->id] + $agent->hierarchyUsers())->get();
                                if($users){
                                    foreach($users AS $userElem){
                                        \DB::table('sessions')
                                            ->where('user_id', $userElem->id)
                                            ->delete();
                                        $userElem->update(['remember_token' => null, 'is_blocked' => 1]);
                                    }
                                }
                                */

                                $agent->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                Session::where('user_id', $agent->id)->delete();
                                event(new Banned($agent));
                            }
                            if( settings('agent_balance_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.agent_balance_x_notify', ['agent' => $agent->username, 'balance' => number_format($agent->balance, 2, '.', '')]));
                            }
                        }

                    }


                    //if( settings('agent_balance_bigger_x_block') || settings('agent_balance_bigger_x_sms')){
                        $agent_in = StatisticAdd::where('user_id', $agent->id)->sum('agent_in');
                        $agent_out = StatisticAdd::where('user_id', $agent->id)->sum('agent_out');
                        if( $agent->balance > ($agent_in - $agent_out) ){
                            $exist = Security::where(['type' => 'user_balance_bigger', 'item_id' => $agent->id])->first();
                            if(!$exist) {
                                $exist = Security::create([
                                    'type' => 'user_balance_bigger',
                                    'item_id' => $agent->id,
                                    'pay_in' => $agent->total_in,
                                    'pay_out' => $agent->total_out,
                                    'pay_total' => $agent->total_in - $agent->total_out,
                                    'balance' => $agent->balance,
                                    'shop_id' => $agent->shop_id,
                                    'category' => 'agent_balance_bigger_x'
                                ]);
                                if(settings('agent_balance_bigger_x_block')){
                                    $exist->update(['block' => 1]);
                                    $agent->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                    Session::where('user_id', $agent->id)->delete();
                                    event(new Banned($agent));

                                    /*
                                    $users = User::whereIn('id', [$agent->id] + $agent->hierarchyUsers())->get();
                                    if($users){
                                        foreach($users AS $userElem){
                                            \DB::table('sessions')
                                                ->where('user_id', $userElem->id)
                                                ->delete();
                                            $userElem->update(['remember_token' => null, 'is_blocked' => 1]);
                                        }
                                    }
                                    */

                                }
                                if(settings('agent_balance_bigger_x_sms')){
                                    $exist->update(['sms' => 1]);
                                    SMS_sender::notify(__('app.agent_balance_bigger_x_notify',
                                        [
                                            'agent' => $agent->username,
                                            'balance' => number_format($agent->balance, 2, '.', ''),
                                            'in' => number_format($agent_in, 2, '.', ''),
                                            'out' => number_format($agent_out, 2, '.', ''),
                                        ]
                                    ));
                                }

                            }

                        }
                    //}

                }
            }
        }


        // Distributors
        $distributorsCount = User::where('role_id', 4)->count();
        for($i=0; $i<ceil($distributorsCount / $limit); $i++){
            $distributors = User::where('role_id', 4)->skip($i*$limit)->take($limit)->get();
            if($distributors){
                foreach ($distributors AS $distributor){
                    if( settings('distributor_balance_x') && $distributor->balance >= settings('distributor_balance_x') ){
                        $exist = Security::where(['type' => 'user_balance', 'item_id' => $distributor->id])->first();
                        if(!$exist){
                            $exist = Security::create([
                                'type' => 'user_balance',
                                'item_id' => $distributor->id,
                                'pay_in' => $distributor->total_in,
                                'pay_out' => $distributor->total_out,
                                'pay_total' => $distributor->total_in - $distributor->total_out,
                                'balance' => $distributor->balance,
                                'shop_id' => $distributor->shop_id,
                                'category' => 'distributor_balance_x'
                            ]);
                            if( settings('distributor_balance_x_block') ){
                                $exist->update(['block' => 1]);

                                /*
                                $users = User::whereIn('id', [$distributor->id] + $distributor->hierarchyUsers())->get();
                                if($users){
                                    foreach($users AS $userElem){
                                        \DB::table('sessions')
                                            ->where('user_id', $userElem->id)
                                            ->delete();
                                        $userElem->update(['remember_token' => null, 'is_blocked' => 1]);
                                    }
                                }
                                */

                                $distributor->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                Session::where('user_id', $distributor->id)->delete();
                                event(new Banned($distributor));
                            }
                            if( settings('distributor_balance_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.distributor_balance_x_notify', ['distributor' => $distributor->username, 'balance' => number_format($distributor->balance, 2, '.', '')]));
                            }
                        }
                    }
                    //if( settings('distributor_balance_bigger_x_block') || settings('distributor_balance_bigger_x_sms')){
                        $distributor_in = StatisticAdd::where('user_id', $distributor->id)->sum('distributor_in');
                        $distributor_out = StatisticAdd::where('user_id', $distributor->id)->sum('distributor_out');
                        if( $distributor->balance > ($distributor_in - $distributor_out) ){

                            $exist = Security::where(['type' => 'user_balance_bigger', 'item_id' => $distributor->id])->first();
                            if(!$exist) {
                                $exist = Security::create([
                                    'type' => 'user_balance_bigger',
                                    'item_id' => $distributor->id,
                                    'pay_in' => $distributor->total_in,
                                    'pay_out' => $distributor->total_out,
                                    'pay_total' => $distributor->total_in - $distributor->total_out,
                                    'balance' => $distributor->balance,
                                    'shop_id' => $distributor->shop_id,
                                    'category' => 'distributor_balance_bigger_x'
                                ]);
                                if(settings('distributor_balance_bigger_x_block')){
                                    $exist->update(['block' => 1]);

                                    /*
                                    $users = User::whereIn('id', [$distributor->id] + $distributor->hierarchyUsers())->get();
                                    if($users){
                                        foreach($users AS $userElem){
                                            \DB::table('sessions')
                                                ->where('user_id', $userElem->id)
                                                ->delete();
                                            $userElem->update(['remember_token' => null, 'is_blocked' => 1]);
                                        }
                                    }
                                    */


                                    $distributor->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                    Session::where('user_id', $distributor->id)->delete();
                                    event(new Banned($distributor));
                                }
                                if(settings('distributor_balance_bigger_x_sms')){
                                    $exist->update(['sms' => 1]);
                                    SMS_sender::notify(__('app.distributor_balance_bigger_x_notify',
                                        [
                                            'distributor' => $distributor->username,
                                            'balance' => number_format($distributor->balance, 2, '.', ''),
                                            'in' => number_format($distributor_in, 2, '.', ''),
                                            'out' => number_format($distributor_out, 2, '.', ''),
                                        ]
                                    ));
                                }
                            }


                        }
                    //}
                }
            }
        }

        // Users
        $usersCount = User::where('role_id', 1)->count();
        for($i=0; $i<ceil($usersCount / $limit); $i++){
            $users = User::where('role_id', 1)->skip($i*$limit)->take($limit)->get();
            if($users){
                foreach ($users AS $user){
                    if( settings('user_balance_x') && $user->balance >= settings('user_balance_x') ){
                        $exist = Security::where(['type' => 'user_balance', 'item_id' => $user->id])->first();
                        if(!$exist){
                            $exist = Security::create([
                                'type' => 'user_balance',
                                'item_id' => $user->id,
                                'pay_in' => $user->total_in,
                                'pay_out' => $user->total_out,
                                'pay_total' => $user->total_in - $user->total_out,
                                'balance' => $user->balance,
                                'shop_id' => $user->shop_id,
                                'category' => 'user_balance_x'
                            ]);
                            if( settings('user_balance_x_block') ){
                                $exist->update(['block' => 1]);
                                $user->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                Session::where('user_id', $user->id)->delete();

                                event(new Banned($user));
                            }
                            if( settings('user_balance_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.user_balance_x_notify', ['user' => $user->username, 'balance' => number_format($user->balance, 2, '.', '')]));
                            }
                        }

                    }

                    // In - Out

                    $stat_total = $user->total_in - $user->total_out;
                    if( settings('user_in_out_x') && $stat_total <= settings('user_in_out_x') ){
                        $exist = Security::where(['type' => 'user_in_out', 'item_id' => $user->id])->first();
                        if(!$exist){
                            $rtp = $user->total_in > 0 ? ($user->total_out / $user->total_in) * 100 : 0;
                            $exist = Security::create([
                                'type' => 'user_in_out',
                                'item_id' => $user->id,
                                'pay_in' => $user->total_in,
                                'pay_out' => $user->total_out,
                                'balance' => $user->balance,
                                'pay_total' => $stat_total,
                                'rtp' => $rtp,
                                'shop_id' => $user->shop_id,
                                'category' => 'user_in_out_x'
                            ]);
                            if( settings('user_in_out_x_block') ){
                                $exist->update(['block' => 1]);
                                $user->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                Session::where('user_id', $user->id)->delete();

                                event(new Banned($user));
                            }
                            if( settings('user_in_out_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.user_in_out_x_notify', ['user' => $user->username, 'shop' => $user->shop ? $user->shop->name : 'NoShop', 'in_out' => number_format($stat_total, 2, '.', '')]));
                            }
                        }

                    }

                }
            }
        }


        // User Wins

        $statGameCount = StatGame::where('date_time', '>', $five_minutes)->count();
        for($i=0; $i<ceil($statGameCount / $limit); $i++){
            $statGames = StatGame::where('date_time', '>', $five_minutes)->skip($i*$limit)->take($limit)->get();
            if($statGames){
                foreach ($statGames AS $statGame){

                    $win = floatval(str_replace(' ', '', $statGame->win));

                    if( settings('user_win_x') && settings('user_win_x') <= $win){

                        $exist = Security::where(['type' => 'user_win', 'item_id' => $statGame->user->id])->first();
                        if(!$exist){
                            $exist = Security::create([
                                'type' => 'user_win',
                                'item_id' => $statGame->user->id,
                                'pay_in' => $statGame->user->total_in,
                                'pay_out' => $statGame->user->total_out,
                                'pay_total' => $statGame->user->total_in - $statGame->user->total_out,
                                'balance' => $statGame->user->balance,
                                'shop_id' => $statGame->user->shop_id,
                                'win' => $win,
                                'category' => 'user_win_x'
                            ]);
                            if( settings('user_win_x_block') ){
                                $exist->update(['block' => 1]);
                                $statGame->user->update(['status' => UserStatus::BANNED, 'remember_token' => null]);
                                Session::where('user_id', $statGame->user->id)->delete();
                                event(new Banned($statGame->user));
                            }
                            if( settings('user_win_x_sms') ){
                                $exist->update(['sms' => 1]);
                                SMS_sender::notify(__('app.user_win_x_notify', [
                                    'user' => $statGame->user->username,
                                    'shop' => $statGame->user->shop->name,
                                    'game' => $statGame->name,
                                    'win' => $statGame->win
                                ]));
                            }
                        }
                    }

                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('Securities');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }
}
