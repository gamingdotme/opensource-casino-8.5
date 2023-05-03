<?php 
namespace VanguardLTE\Games\FlyJetSW
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
                        $aid = '';
                        $gameBets = $slotSettings->Bet;
                        $denoms = [];
                        foreach( $slotSettings->Denominations as $b ) 
                        {
                            $denoms[] = '' . ($b * 100) . '';
                        }
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                        $result_tmp[0] = '42["init",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MTMzODk5MzQxOnN3X2Z1ZmlzaF9pbnR3Om1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MTMzOTIwLCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.7U9pc0abgmefSMTWYhhmWREN8bB0QsasvKhq5jzUldsBSHmgiY48qbzl7oGLVDpTDGwwX7eyJIng9rVsfM7uoQ","balance":{"currency":" ","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"init","player_id":"' . $slotSettings->username . '","player_code":"' . $slotSettings->username . '","player_name":"' . $slotSettings->username . '","coins_rate":' . $slotSettings->Denominations[0] . ',"settings":{"coinsRate":' . $slotSettings->Denominations[0] . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100}},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}]';
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
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '42["init",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MTMzODk5MzQxOnN3X2Z1ZmlzaF9pbnR3Om1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MTMzOTIwLCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.7U9pc0abgmefSMTWYhhmWREN8bB0QsasvKhq5jzUldsBSHmgiY48qbzl7oGLVDpTDGwwX7eyJIng9rVsfM7uoQ","balance":{"currency":" ","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"init","player_id":"' . $slotSettings->username . '","player_code":"' . $slotSettings->username . '","player_name":"' . $slotSettings->username . '","coins_rate":' . $slotSettings->Denominations[0] . ',"settings":{"coinsRate":' . $slotSettings->Denominations[0] . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100}},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}]';
                                break;
                            case 'balance':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '42["balance",{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTg5MTIyMzE5NDcwOnN3X2Z1ZmlzaF9pbnR3Om1vYmlsZSIsImdhbWVNb2RlIjoiZnVuIiwiaWF0IjoxNTg5MTIyNDg3LCJpc3MiOiJza3l3aW5kZ3JvdXAifQ.mNF0rwOpuY3pE6rV55zbBc1q03yM3QtawnKQUmVKWlXwGpIm74lTx5oazOSaZtoEtbnBXoFbwkHvk8MgnxMZCJg1b7qslsE8lTLB5zKSwu41ouGqrnzC_kQpVXw","balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"balance"}}]';
                                break;
                            case 'play':
                                $gameCommand = $postData[1]['request'];
                                if( $gameCommand == 'exitRoom' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"exitRoom","totalBet":0,"totalWin":0,"roundEnded":true},"requestId":' . $requestId . ',"roundEnded":true}]';
                                }
                                if( $gameCommand == 'room-exit' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"room-exit","timestamp":' . time() . ',"roundEnded":true},"requestId":' . $requestId . ',"roundEnded":true}]';
                                }
                                if( $gameCommand == 'enterRoom' ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $requestId = $postData[1]['requestId'];
                                    $bet = $postData[1]['bet'];
                                    if( !$slotSettings->HasGameData($slotSettings->slotId . 'Progress') ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Progress', [
                                            [
                                                0, 
                                                1
                                            ], 
                                            [
                                                0, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                6
                                            ]
                                        ]);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'World', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Mission', 0);
                                    }
                                    $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                    $World = $slotSettings->GetGameData($slotSettings->slotId . 'World');
                                    $Mission = $slotSettings->GetGameData($slotSettings->slotId . 'Mission');
                                    if( $Progress[0][1] < $Progress[0][0] ) 
                                    {
                                        $Progress[0][0] = $Progress[0][1];
                                    }
                                    if( $Progress[1][1] < $Progress[1][0] ) 
                                    {
                                        $Progress[1][0] = $Progress[1][1];
                                    }
                                    if( $Progress[2][1] < $Progress[2][0] ) 
                                    {
                                        $Progress[2][0] = $Progress[2][1];
                                    }
                                    if( $Progress[3][1] < $Progress[3][0] ) 
                                    {
                                        $Progress[3][0] = $Progress[3][1];
                                    }
                                    if( $Progress[4][1] < $Progress[4][0] ) 
                                    {
                                        $Progress[4][0] = $Progress[4][1];
                                    }
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"enterRoom","totalBet":' . $bet . ',"state":{"world":' . $World . ',"mode":"game","mission":' . $Mission . ',"bet":' . $bet . ',"progress":' . json_encode($Progress) . ',"features":[]},"totalWin":0,"roundEnded":true},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Progress', $Progress);
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
                                if( $gameCommand == 'finishBoss' ) 
                                {
                                    $requestId = $postData[1]['requestId'];
                                    $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                    $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                    $World = $slotSettings->GetGameData($slotSettings->slotId . 'World');
                                    $Mission = $slotSettings->GetGameData($slotSettings->slotId . 'Mission');
                                    $Mission += 3;
                                    $World += 1;
                                    $Progress[0][0] = 0;
                                    $Progress[0][1] += 2;
                                    $Progress[1][0] = 0;
                                    $Progress[1][1] += 3;
                                    $Progress[2][0] = 0;
                                    $Progress[2][1] += 4;
                                    $Progress[3][0] = 0;
                                    $Progress[3][1] += 5;
                                    $Progress[4][0] = 0;
                                    $Progress[4][1] += 6;
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"finishBoss","state":{"world":' . $World . ',"mode":"game","mission":' . $Mission . ',"bet":' . $allbet . ',"progress":' . json_encode($Progress) . ',"features":[]},"totalWin":0,"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Progress', $Progress);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'World', $World);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Mission', $Mission);
                                }
                                if( $gameCommand == 'startBoss' ) 
                                {
                                    $requestId = $postData[1]['requestId'];
                                    $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                    $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                    $World = $slotSettings->GetGameData($slotSettings->slotId . 'World');
                                    $Mission = $slotSettings->GetGameData($slotSettings->slotId . 'Mission');
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $progressWin = 1;
                                    if( $World == 0 ) 
                                    {
                                        $BossWin = rand(50, 300) * $allbet;
                                    }
                                    if( $World == 1 ) 
                                    {
                                        $BossWin = rand(50, 350) * $allbet;
                                    }
                                    if( $World == 2 ) 
                                    {
                                        $BossWin = rand(50, 500) * $allbet;
                                    }
                                    if( $World == 3 ) 
                                    {
                                        $BossWin = rand(50, 750) * $allbet;
                                    }
                                    if( $World >= 4 ) 
                                    {
                                        $BossWin = rand(50, 1000) * $allbet;
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BossWin', $BossWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'progressWin', $progressWin);
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"request":"startBoss","state":{"world":' . $World . ',"mode":"boss","mission":' . $Mission . ',"bet":' . $allbet . ',"progress":' . json_encode($Progress) . ',"features":[],"bossInfo":{"shootId":0,"win":0,"progressWin":' . $progressWin . ',"maxWin":' . $BossWin . '}},"totalWin":0,"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                }
                                if( $gameCommand == 'attackBoss' ) 
                                {
                                    $requestId = $postData[1]['requestId'];
                                    $bulletId = $postData[1]['bulletId'];
                                    $allbet = $postData[1]['bet'];
                                    $item = $postData[1]['item'];
                                    $killed = [];
                                    $bank = $slotSettings->GetBank();
                                    $totalWin = 0;
                                    $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                    if( $allbet <= $slotSettings->GetBalance() && $allbet > 0 ) 
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
                                    $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                    $World = $slotSettings->GetGameData($slotSettings->slotId . 'World');
                                    $Mission = $slotSettings->GetGameData($slotSettings->slotId . 'Mission');
                                    $BossWin = $slotSettings->GetGameData($slotSettings->slotId . 'BossWin');
                                    $progressWin = $slotSettings->GetGameData($slotSettings->slotId . 'progressWin');
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $airType = $item['target']['type'];
                                    $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                    if( $winChance == 1 ) 
                                    {
                                        $progressWin = $progressWin * 100;
                                        $progressWin -= 20;
                                        $progressWin = $progressWin / 100;
                                        $BossWin -= (rand(0, 5) * $allbet);
                                        if( $progressWin <= 0 ) 
                                        {
                                            if( $BossWin <= $bank ) 
                                            {
                                                $totalWin = $BossWin;
                                                $killed[] = '{"uid":' . $item['target']['uid'] . ',"type":' . $airType . ',"payout":' . $BossWin . '}';
                                            }
                                            else
                                            {
                                                $BossWin = $slotSettings->GetGameData($slotSettings->slotId . 'BossWin');
                                                $progressWin = $slotSettings->GetGameData($slotSettings->slotId . 'progressWin');
                                            }
                                        }
                                    }
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank('', -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BossWin', $BossWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'progressWin', $progressWin);
                                    if( $progressWin <= 0 ) 
                                    {
                                        $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"bulletId":' . $bulletId . ',"killed":[' . implode(',', $killed) . '],"request":"attackBoss","totalBet":' . $allbet . ',"state":{"world":' . $World . ',"mode":"boss","mission":' . $Mission . ',"bet":' . $allbet . ',"progress":' . json_encode($Progress) . ',"features":[],"bossInfo":{"shootId":0,"win":' . $BossWin . '}},"totalWin":' . $BossWin . ',"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    }
                                    else
                                    {
                                        $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"bulletId":' . $bulletId . ',"killed":[' . implode(',', $killed) . '],"request":"attackBoss","totalBet":' . $allbet . ',"state":{"world":' . $World . ',"mode":"boss","mission":' . $Mission . ',"bet":' . $allbet . ',"progress":' . json_encode($Progress) . ',"features":[],"bossInfo":{"shootId":0,"win":0,"progressWin":' . $progressWin . ',"maxWin":' . $BossWin . '}},"totalWin":0,"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    }
                                    $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                    $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                }
                                if( $gameCommand == 'fire' || $gameCommand == 'shot' ) 
                                {
                                    $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    $totalWin = 0;
                                    $requestId = $postData[1]['requestId'];
                                    $bulletId = $postData[1]['bulletId'];
                                    $allbet = $postData[1]['bet'];
                                    $item = $postData[1]['item'];
                                    $killed = [];
                                    $bank = $slotSettings->GetBank();
                                    $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                    if( $allbet <= $slotSettings->GetBalance() && $allbet > 0 ) 
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
                                    $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                    $World = $slotSettings->GetGameData($slotSettings->slotId . 'World');
                                    $Mission = $slotSettings->GetGameData($slotSettings->slotId . 'Mission');
                                    if( $item['mode'] == 'singleShot' ) 
                                    {
                                        $airType = $item['target']['type'];
                                        $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                        $mpl = 1;
                                        if( isset($item['target']['m']) ) 
                                        {
                                            $mpl = 2;
                                        }
                                        $tmpWin = $slotSettings->Damage['Air_' . $airType] * $allbet * $mpl;
                                        if( $tmpWin <= $bank && $winChance == 1 ) 
                                        {
                                            if( isset($item['target']['m']) ) 
                                            {
                                                $killed[] = '{"uid":' . $item['target']['uid'] . ',"type":' . $airType . ',"payout":' . $tmpWin . ',"m":true}';
                                            }
                                            else
                                            {
                                                $killed[] = '{"uid":' . $item['target']['uid'] . ',"type":' . $airType . ',"payout":' . $tmpWin . '}';
                                            }
                                            $totalWin += $tmpWin;
                                            $Progress[$airType][0]++;
                                        }
                                    }
                                    if( $item['mode'] == 'chainReactorShot' ) 
                                    {
                                        $airType = $item['target']['type'];
                                        $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                        $tmpWin = $slotSettings->Damage['Air_' . $airType] * $allbet;
                                        if( $winChance == 1 ) 
                                        {
                                            $killed[] = '{"uid":' . $item['target']['uid'] . ',"type":' . $airType . ',"payout":' . $tmpWin . '}';
                                            $totalWin += $tmpWin;
                                            $Progress[$airType][0]++;
                                            for( $i = 0; $i < count($item['hitItems']); $i++ ) 
                                            {
                                                $airType = $item['hitItems'][$i]['type'];
                                                $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                                $tmpWin = $slotSettings->Damage['Air_' . $airType] * $allbet;
                                                $killed[] = '{"uid":' . $item['hitItems'][$i]['uid'] . ',"type":' . $airType . ',"payout":' . $tmpWin . '}';
                                                $totalWin += $tmpWin;
                                                $Progress[$airType][0]++;
                                            }
                                            if( $bank < $totalWin ) 
                                            {
                                                $killed = [];
                                                $totalWin = 0;
                                                $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                            }
                                        }
                                    }
                                    if( $item['mode'] == 'bombShot' ) 
                                    {
                                        $airType = $item['target']['type'];
                                        $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                        $tmpWin = $slotSettings->Damage['Air_' . $airType] * $allbet;
                                        if( $winChance == 1 ) 
                                        {
                                            $killed[] = '{"uid":' . $item['target']['uid'] . ',"type":' . $airType . ',"payout":0}';
                                            $totalWin += $tmpWin;
                                            for( $i = 0; $i < count($item['hitItems']); $i++ ) 
                                            {
                                                $airType = $item['hitItems'][$i]['type'];
                                                $winChance = rand(1, $slotSettings->Damage['Air_' . $airType]);
                                                $tmpWin = $slotSettings->Damage['Air_' . $airType] * $allbet;
                                                $killed[] = '{"uid":' . $item['hitItems'][$i]['uid'] . ',"type":' . $airType . ',"payout":' . $tmpWin . '}';
                                                $totalWin += $tmpWin;
                                                $Progress[$airType][0]++;
                                            }
                                            if( $bank < $totalWin ) 
                                            {
                                                $killed = [];
                                                $totalWin = 0;
                                                $Progress = $slotSettings->GetGameData($slotSettings->slotId . 'Progress');
                                            }
                                        }
                                    }
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank('', -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                    }
                                    $features = '';
                                    if( $Progress[0][1] <= $Progress[0][0] && $Progress[1][1] <= $Progress[1][0] && $Progress[2][1] <= $Progress[2][0] && $Progress[3][1] <= $Progress[3][0] && $Progress[4][1] <= $Progress[4][0] ) 
                                    {
                                        $features = '{"type":"boss"}';
                                    }
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                    $result_tmp[0] = '42["play",{"balance":{"currency":"","amount":' . $balanceInCents . ',"real":{"amount":' . $balanceInCents . '},"bonus":{"amount":0}},"result":{"bulletId":' . $bulletId . ',"killed":[' . implode(',', $killed) . '],"request":"shot","totalBet":' . $allbet . ',"state":{"world":' . $World . ',"mode":"game","mission":' . $Mission . ',"bet":' . $allbet . ',"progress":' . json_encode($Progress) . ',"features":[' . $features . ']},"totalWin":' . $totalWin . ',"roundEnded":false},"requestId":' . $requestId . ',"roundEnded":false}]';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Progress', $Progress);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'World', $World);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Mission', $Mission);
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
