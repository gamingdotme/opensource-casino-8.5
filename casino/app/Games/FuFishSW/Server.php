<?php 
namespace VanguardLTE\Games\FuFishSW
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
        {
            function get_($request, $game)
            {
                \DB::transaction(function() use ($request, $game)
                {
                    try
                    {
                        if( isset($_GET['step']) ) 
                        {
                            sleep(2);
                            if( $_GET['step'] == 1 ) 
                            {
                                echo '2:40';
                                return null;
                            }
                            else if( $_GET['step'] == 0 ) 
                            {
                                echo '96:0{"sid":"Frz92Su8ddussxizACPJ","upgrades":["websocket"],"pingInterval":25000,"pingTimeout":2000}';
                                return null;
                            }
                            echo 'ok';
                            return null;
                        }
                        $userId = \Auth::id();
                        if( $userId == null ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid login"}';
                            exit( $response );
                        }
                        $slotSettings = new SlotSettings($game, $userId);
                        if( !$slotSettings->is_active() ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"Game is disabled"}';
                            exit( $response );
                        }
                        $postData = json_decode(trim(file_get_contents('php://input')), true);
                        if( isset($postData['command']) && $postData['command'] == 'CheckAuth' ) 
                        {
                            $response = '{"responseEvent":"CheckAuth","startTimeSystem":' . (time() * 1000) . ',"userId":' . $userId . ',"shop_id":' . $slotSettings->shop_id . ',"username":"' . $slotSettings->username . '"}';
                            exit( $response );
                        }
                        $result_tmp = [];
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                        $result_tmp[0] = '42["init",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MDIzNzQzNzkwOnN3X29yOm1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MDI4NjI3LCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.xq2WFe3GCy7VZugNB_FK_DFcUhwwbaYKDN_RYbydQnuMX4t1WTJ3W8WVL5HAt8B27XHAVOVibzPun2fEJTOPVA","balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"init","gameId":"sw_or","version":"2.1.0","name":"Ocean Ruler","settings":{"coins":[1,2,3,4,5,6,7,8,9,10,20,30,40,50,60,70,80,90,100,200,300,400,500,600,700,800,900,1000],"stakeDef":' . $slotSettings->Denominations[0] . ',"coinsRate":' . $slotSettings->Denominations[0] . ',"defaultCoin":1,"currencyMultiplier":100},"state":{"mode":"normal","features":[]},"playerCode":"' . $slotSettings->username . '"},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}]';
                        $aid = '';
                        $aid = (string)$postData[0];
                        switch( $aid ) 
                        {
                            case 'init':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $slotSettings->CurrentDenom = $slotSettings->Denominations[0];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '42["init",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MTMzODk5MzQxOnN3X2Z1ZmlzaF9pbnR3Om1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MTMzOTIwLCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.7U9pc0abgmefSMTWYhhmWREN8bB0QsasvKhq5jzUldsBSHmgiY48qbzl7oGLVDpTDGwwX7eyJIng9rVsfM7uoQ","balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"init","player_id":"' . $slotSettings->username . '","player_code":"' . $slotSettings->username . '","player_name":"' . $slotSettings->username . '","coins_rate":' . $slotSettings->Denominations[0] . ',"settings":{"coinsRate":' . $slotSettings->Denominations[0] . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100}},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}]';
                                break;
                            case 'balance':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '42["balance",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MTIyMzE5NDcwOnN3X2Z1ZmlzaF9pbnR3Om1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MTIyNDg3LCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.mNF0rwOpuY3pE6rV55zbBc1q03yM3QtawnKQUmVKWlXwGpIm74lTx5oazOSaZtoEtbnBXoFbwkHvk8MgnxMZCJg1b7qslsE8lTLB5zKSwu41ouGqrnzC_kQpVXw","balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"balance"}}]';
                                break;
                            case 'play':
                                $gameCommand = $postData[1]['request'];
                                if( $gameCommand == 'room-exit' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"room-exit","timestamp":' . time() . ',"roundEnded":true},"requestId":' . $requestId . ',"roundEnded":true}]';
                                }
                                if( $gameCommand == 'room-enter' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $roomType = $postData[1]['type'];
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"room-enter","type":"' . $roomType . '","room_id":"ID-' . $roomType . '","timestamp":' . time() . ',"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                }
                                if( $gameCommand == 'list-rooms' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"list-rooms","rooms":[{"type":"1","bet_list":[1,2,3,4,5,6,7,8,9],"active":true,"default_bet":1},{"type":"2","bet_list":[10,20,30,40,50,60,70,80,90],"active":true,"default_bet":10},{"type":"3","bet_list":[100,200,300,400,500,600,700,800,900,1000],"active":true,"default_bet":100}]},"requestId":' . $requestId . ',"roundEnded":true}]';
                                }
                                if( $gameCommand == 'startBonus' ) 
                                {
                                    if( isset($postData[1]['select']) ) 
                                    {
                                        $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        $totalWin = 0;
                                        $requestId = $postData[1]['requestId'];
                                        $bulletId = $postData[1]['bulletId'];
                                        $allbet = $postData[1]['bet'];
                                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                        $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"startBonus","state":{"mode":"bonus","features":[],"bonus":{"bet":' . $allbet . ',"rounds":[]}},"totalWin":0,"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    }
                                    else
                                    {
                                        $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        $totalWin = 0;
                                        $requestId = $postData[1]['requestId'];
                                        $bulletId = $postData[1]['bulletId'];
                                        $allbet = $postData[1]['bet'];
                                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                        $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"startBonus","state":{"mode":"bonus","features":[],"bonus":{"bet":' . $allbet . ',"rounds":[]}},"totalWin":0,"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    }
                                }
                                if( $gameCommand == 'fire' || $gameCommand == 'shot' ) 
                                {
                                    $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    $totalWin = 0;
                                    $requestId = $postData[1]['requestId'];
                                    $bulletId = $postData[1]['bullet_id'];
                                    $allbet = $postData[1]['bet_amount'];
                                    $is_bomb = $postData[1]['is_bomb'];
                                    $bomb_id = $postData[1]['bomb_id'];
                                    if( !$is_bomb ) 
                                    {
                                        $items = $postData[1]['hit_items'];
                                        $shotState = 'normal';
                                        $items_ = [];
                                    }
                                    else
                                    {
                                        $items = [];
                                        $items_ = $postData[1]['hit_items'];
                                    }
                                    if( $allbet <= $slotSettings->GetBalance() ) 
                                    {
                                        $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                        $slotSettings->UpdateJackpots($allbet);
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                        $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                    }
                                    else
                                    {
                                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                        exit( $response );
                                    }
                                    $features = '';
                                    $kills = [];
                                    for( $i = 0; $i < count($items); $i++ ) 
                                    {
                                        $curItem = $items[$i];
                                        $fishType = $curItem[0];
                                        $fishId = $curItem[1];
                                        if( !isset($slotSettings->FishDamage['Fish_' . $fishType]) ) 
                                        {
                                            $slotSettings->FishDamage['Fish_' . $fishType] = [10000];
                                        }
                                        if( !isset($slotSettings->Paytable['Fish_' . $fishType]) ) 
                                        {
                                            $slotSettings->Paytable['Fish_' . $fishType] = [0];
                                        }
                                        $damage = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                        $pay = $slotSettings->Paytable['Fish_' . $fishType];
                                        if( is_numeric($pay[0]) ) 
                                        {
                                            shuffle($pay);
                                            $win = $pay[0] * $allbet;
                                        }
                                        else
                                        {
                                            $win = 0;
                                        }
                                        if( $totalWin + $win <= $bank && $damage == 1 ) 
                                        {
                                            $kills[] = '' . $fishId . '';
                                        }
                                        else
                                        {
                                            $win = 0;
                                        }
                                        $totalWin += $win;
                                    }
                                    $allDamages = 0;
                                    for( $i = 0; $i < count($items_); $i++ ) 
                                    {
                                        $curItem = $items_[$i];
                                        $fishType = $curItem[0];
                                        $fishId = $curItem[1];
                                        if( !isset($slotSettings->FishDamage['Fish_' . $fishType]) ) 
                                        {
                                            $slotSettings->FishDamage['Fish_' . $fishType] = [10000];
                                        }
                                        if( !isset($slotSettings->Paytable['Fish_' . $fishType]) ) 
                                        {
                                            $slotSettings->Paytable['Fish_' . $fishType] = [0];
                                        }
                                        $damage = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                        $pay = $slotSettings->Paytable['Fish_' . $fishType];
                                        if( $damage == 1 && $fishId == $bomb_id ) 
                                        {
                                            $allDamages = 1;
                                        }
                                        $damage = $allDamages;
                                        if( is_numeric($pay[0]) ) 
                                        {
                                            shuffle($pay);
                                            $win = $pay[0] * $allbet;
                                        }
                                        else
                                        {
                                            $win = 0;
                                        }
                                        if( $totalWin + $win <= $bank && $damage == 1 ) 
                                        {
                                            $kills[] = '' . $fishId . '';
                                        }
                                        else
                                        {
                                            $win = 0;
                                        }
                                        $totalWin += $win;
                                    }
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank('', -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                    }
                                    if( $gameCommand == 'fire' ) 
                                    {
                                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                        if( $is_bomb ) 
                                        {
                                            $is_bomb = 'true';
                                        }
                                        else
                                        {
                                            $is_bomb = 'false';
                                        }
                                        $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"fire","requestId":' . $requestId . ',"bullet_id":' . $bulletId . ',"bet_amount":' . $allbet . ',"coins_amount":' . $totalWin . ',"is_bomb":' . $is_bomb . ',"rewards":[' . implode(',', $kills) . '],"timestamp":' . time() . ',"is_season":false,"coins_rate":' . $slotSettings->CurrentDenom . '},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    }
                                    $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                    $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                }
                                break;
                        }
                        $slotSettings->SaveGameData();
                        echo $result_tmp[0];
                    }
                    catch( \Exception $e ) 
                    {
                        if( isset($slotSettings) ) 
                        {
                            $slotSettings->InternalErrorSilent($e);
                        }
                        else
                        {
                            $strLog = '';
                            $strLog .= "\n";
                            $strLog .= ('{"responseEvent":"error","responseType":"' . $e . '","serverResponse":"InternalError","request":' . json_encode($_REQUEST) . ',"requestRaw":' . file_get_contents('php://input') . '}');
                            $strLog .= "\n";
                            $strLog .= ' ############################################### ';
                            $strLog .= "\n";
                            $slg = '';
                            if( file_exists(storage_path('logs/') . 'GameInternal.log') ) 
                            {
                                $slg = file_get_contents(storage_path('logs/') . 'GameInternal.log');
                            }
                            file_put_contents(storage_path('logs/') . 'GameInternal.log', $slg . $strLog);
                        }
                    }
                }, 5);
            }
            get_($request, $game);
        }
    }

}
