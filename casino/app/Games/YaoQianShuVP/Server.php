<?php 
namespace VanguardLTE\Games\YaoQianShuVP
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
                        $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                        if( isset($_GET['command']) ) 
                        {
                            $aid0 = explode('init/?func=', $_GET['command'])[1];
                            $aid = explode('&', $aid0)[0];
                        }
                        else
                        {
                            $aid = $postData['type'];
                        }
                        $curTime = floor(microtime(true) * 1000);
                        switch( $aid ) 
                        {
                            case 'fishRoomTypeInfo':
                                $result_tmp[0] = "{\r\n    \"Code\": 20000,\r\n    \"Message\": \"登录成功\",\r\n    \"Data\": {\r\n        \"roomTypeInfo\": {\r\n            \"money\": " . $balanceInCents . ",\r\n            \"limit\": [\r\n                {\r\n                    \"roomtype\": 0,\r\n                    \"limitBalance\": 1000\r\n                },\r\n                {\r\n                    \"roomtype\": 1,\r\n                    \"limitBalance\": 10000\r\n                },\r\n                {\r\n                    \"roomtype\": 2,\r\n                    \"limitBalance\": 50000\r\n                }\r\n            ]\r\n        },\r\n        \"fishRoomMod\": 1\r\n    }\r\n}";
                                break;
                            case 'changelocking':
                                $result_tmp[] = '{"message":{"pos":5,"fishid":' . $postData['message']['fishid'] . '},"succ":true,"errinfo":"ok","type":"changelock"}';
                                break;
                            case 'servids':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 1);
                                $result_tmp[0] = "{\r\n    \"Code\": 20000,\r\n    \"Message\": \"登录成功\",\r\n    \"Data\": {\r\n        \"res\": [\r\n            {\r\n                \"gameid\": 1008,\r\n                \"servid\": 1\r\n            }\r\n        ]\r\n    }\r\n}";
                                break;
                            case 'changerate':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentBet = $postData['message']['rewardrate'];
                                if( $CurrentBet < 0 ) 
                                {
                                    $CurrentBet = 1;
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $CurrentBet);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance() * 100);
                                $result_tmp[0] = '{"type":"changerate","message":{"rewardrate":6}}';
                                $result_tmp[1] = '{"message":{"type":3,"player":{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":0}},"succ":true,"errinfo":"ok","type":"broadplayer"}';
                                break;
                            case 'heart':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '{"type":"heart","message":{"time":' . $curTime . '}}';
                                break;
                            case 'login':
                                $bets = [];
                                foreach( $slotSettings->Bet as $b ) 
                                {
                                    $bets[] = '' . ($b * 100) . '';
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 1);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = '{"message":{"issucc":1,"uid":7401},"succ":true,"errinfo":"ok","type":"login"}';
                                break;
                            case 'changbackstage':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[0] = '{"message":{"type":3,"player":{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":0}},"succ":true,"errinfo":"ok","type":"broadplayer"}';
                                $result_tmp[1] = '{"message":{"sceneid":' . rand(1, 3) . ',"btime":' . ($curTime + 20000) . ',"etime":' . $curTime . '},"succ":true,"errinfo":"ok","type":"changescene"}';
                                break;
                            case 'fire':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[0] = '{"message":{"type":3,"player":{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":0}},"succ":true,"errinfo":"ok","type":"broadplayer"}';
                                break;
                            case 'hit':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet / 100;
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
                                $bulletid = $postData['message']['fblist'][0]['bulletid'];
                                $fishids = $postData['message']['fblist'][0]['fishids'];
                                $fishpids = $postData['message']['fblist'][0]['fishpids'];
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet / 100;
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                $payRate = 0;
                                for( $i = 0; $i < count($fishpids); $i++ ) 
                                {
                                    $fishType = $fishpids[$i];
                                    if( $fishType == 25 ) 
                                    {
                                        $isBombId = $fishids[$i];
                                        $isBomb = 1;
                                        $isBombWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                        $totalWinsArr[] = '{"uid":' . $isBombId . ',"score":0,"rate":0,"ext":0}';
                                    }
                                }
                                $i = 0;
                                while( $i < count($fishpids) ) 
                                {
                                    $fishType = $fishpids[$i];
                                    if( count($slotSettings->Paytable['Fish_' . $fishType]) > 1 ) 
                                    {
                                        $payRate = rand($slotSettings->Paytable['Fish_' . $fishType][0], $slotSettings->Paytable['Fish_' . $fishType][1]);
                                    }
                                    else
                                    {
                                        $payRate = $slotSettings->Paytable['Fish_' . $fishType][0];
                                    }
                                    $isWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                    if( $isBomb && $isBombWin == 1 ) 
                                    {
                                        if( $isBombId == $fishids[$i] ) 
                                        {
                                        }
                                        else
                                        {
                                            $isWin = 1;
                                        }
                                        $i++;
                                    }
                                    if( $isWin == 1 && $totalWin + ($payRate * $allbet) <= $bank ) 
                                    {
                                        $totalWin += ($payRate * $allbet);
                                        $totalWinsArr[] = '{"uid":' . $fishids[$i] . ',"score":' . round($payRate * $allbet * 100) . ',"rate":' . $payRate . ',"ext":0}';
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $result_tmp[] = '{"message":{"bulletid":"' . $bulletid . '","pos":5,"fishes":[' . implode(',', $totalWinsArr) . '],"rate":1},"succ":true,"errinfo":"ok","type":"hitsprites"}';
                                }
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[] = '{"payRate":' . $payRate . ',"isBomb":' . $isBomb . ',"message":{"money":0},"succ":true,"errinfo":"ok","type":"userinfo"}';
                                $result_tmp[] = '{"message":{"type":3,"player":{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":0}},"succ":true,"errinfo":"ok","type":"broadplayer"}';
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                            case 'quickenterroom':
                                if( $postData['message']['roomtype'] == 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 1);
                                    $min = 1;
                                    $max = 200;
                                }
                                else if( $postData['message']['roomtype'] == 1 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 10);
                                    $min = 10;
                                    $max = 2000;
                                }
                                else if( $postData['message']['roomtype'] == 2 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 50);
                                    $min = 50;
                                    $max = 20000;
                                }
                                $fishes = [];
                                $cfishes = rand(10, 20);
                                for( $i = 0; $i < $cfishes; $i++ ) 
                                {
                                    $fishes[] = '{"id":' . rand(1, 9999999) . ',"classid":5,"fishid":' . rand(1, 12) . ',"born_time":' . $curTime . ',"routeid":' . rand(1, 110) . ',"dead_time":' . ($curTime + 60000) . ',"offsettype":0,"offsetx":0,"offsety":0,"offsetr":0,"rate":2,"ext":0}';
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[0] = '{"message":{"result":1,"roompos":3,"scenestate":5,"sceneid":1,"scene_etime":' . $curTime . ',"scene_btime":' . ($curTime + 80000) . ',"players":[{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":false}],"sprites":[],"bullets":[],"min":' . $min . ',"max":' . $max . ',"coinrate":1000,"bombs":[]},"succ":true,"errinfo":"ok","type":"enterroom"}';
                                break;
                        }
                        $slotSettings->SaveGameData();
                        echo implode('---', $result_tmp);
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
