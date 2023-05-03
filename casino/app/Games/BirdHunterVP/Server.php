<?php 
namespace VanguardLTE\Games\BirdHunterVP
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
                                $result_tmp[1] = '{"message":{"sceneid":' . rand(1, 3) . ',"state":0,"servtime":' . $curTime . '},"succ":true,"errinfo":"ok","type":"changescene"}';
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
                                $result_tmp[0] = '{"message":{"result":1,"roompos":3,"scenestate":5,"sceneid":1,"scene_etime":' . $curTime . ',"scene_btime":' . ($curTime + 80000) . ',"players":[{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":false}],"sprites":[' . implode(',', $fishes) . '],"bullets":[],"min":' . $min . ',"max":' . $max . ',"coinrate":1000,"bombs":[],"feathers":[{"k":10089,"v":1},{"k":9914,"v":1},{"k":8331,"v":4},{"k":8975,"v":1},{"k":9336,"v":4},{"k":2512,"v":2},{"k":6707,"v":2},{"k":8609,"v":2},{"k":9614,"v":1},{"k":8819,"v":2},{"k":9870,"v":3},{"k":3208,"v":3},{"k":9772,"v":1},{"k":6603,"v":1},{"k":9634,"v":4},{"k":9573,"v":1},{"k":8704,"v":4},{"k":103,"v":4},{"k":8976,"v":4},{"k":7420,"v":1},{"k":9429,"v":4},{"k":5639,"v":1},{"k":6498,"v":4},{"k":6105,"v":4},{"k":9361,"v":1},{"k":5142,"v":2},{"k":9138,"v":4},{"k":9635,"v":4},{"k":9157,"v":4},{"k":7852,"v":1},{"k":10019,"v":1},{"k":2076,"v":1},{"k":8599,"v":1},{"k":8790,"v":4},{"k":10047,"v":3},{"k":9469,"v":4},{"k":6501,"v":1},{"k":7528,"v":3},{"k":5090,"v":4},{"k":7818,"v":1},{"k":5843,"v":1},{"k":8566,"v":4},{"k":7041,"v":1},{"k":9143,"v":1},{"k":2805,"v":3},{"k":6155,"v":4},{"k":10151,"v":2},{"k":9166,"v":4},{"k":2057,"v":4},{"k":8342,"v":3},{"k":7383,"v":4},{"k":10087,"v":4},{"k":9322,"v":1},{"k":10339,"v":2},{"k":7689,"v":1},{"k":10457,"v":1},{"k":2126,"v":1},{"k":9229,"v":4},{"k":9451,"v":1},{"k":7200,"v":3},{"k":8633,"v":4},{"k":8446,"v":2},{"k":8700,"v":1},{"k":7587,"v":2},{"k":9023,"v":4},{"k":2443,"v":3},{"k":9010,"v":3},{"k":10141,"v":4},{"k":7483,"v":4},{"k":2679,"v":4},{"k":8631,"v":4},{"k":8027,"v":1},{"k":4488,"v":1},{"k":9896,"v":1},{"k":9840,"v":4},{"k":9252,"v":1},{"k":144,"v":2},{"k":983,"v":2},{"k":8341,"v":3},{"k":7980,"v":1},{"k":4329,"v":1},{"k":9289,"v":1},{"k":8629,"v":4},{"k":9534,"v":4},{"k":8823,"v":2},{"k":9681,"v":1},{"k":8932,"v":1},{"k":8205,"v":4},{"k":6679,"v":2},{"k":8960,"v":2},{"k":5492,"v":2},{"k":10070,"v":1},{"k":8974,"v":4},{"k":95,"v":1},{"k":9583,"v":2},{"k":2819,"v":4},{"k":9691,"v":3},{"k":8029,"v":4},{"k":9261,"v":4},{"k":8721,"v":4},{"k":1187,"v":4},{"k":10296,"v":2},{"k":1778,"v":1},{"k":4379,"v":2},{"k":10181,"v":4},{"k":8523,"v":3},{"k":6718,"v":4},{"k":2761,"v":4},{"k":8841,"v":2},{"k":8664,"v":4},{"k":3710,"v":2},{"k":9326,"v":2},{"k":276,"v":4},{"k":8457,"v":1},{"k":99,"v":4},{"k":8521,"v":4},{"k":8640,"v":4},{"k":9456,"v":2},{"k":7485,"v":4},{"k":8522,"v":1},{"k":9406,"v":1},{"k":8333,"v":4},{"k":8327,"v":1},{"k":3701,"v":4},{"k":84,"v":4},{"k":8724,"v":4},{"k":9004,"v":4},{"k":8086,"v":3},{"k":4987,"v":4},{"k":6774,"v":1},{"k":5908,"v":1},{"k":9879,"v":1},{"k":7474,"v":1},{"k":4849,"v":4},{"k":9936,"v":4},{"k":88,"v":4},{"k":3086,"v":2},{"k":9575,"v":3},{"k":6875,"v":3},{"k":9053,"v":4},{"k":6022,"v":4},{"k":154,"v":4},{"k":8598,"v":4},{"k":6457,"v":4},{"k":8295,"v":1},{"k":10142,"v":4},{"k":10137,"v":1},{"k":8972,"v":1},{"k":8626,"v":1},{"k":7757,"v":3},{"k":9829,"v":1},{"k":5809,"v":4},{"k":9524,"v":2},{"k":8644,"v":4},{"k":8752,"v":2},{"k":5865,"v":2},{"k":2830,"v":1},{"k":7397,"v":3},{"k":98,"v":4},{"k":10356,"v":4},{"k":8891,"v":4},{"k":7669,"v":3},{"k":8769,"v":1},{"k":9274,"v":4},{"k":6484,"v":4},{"k":9486,"v":1},{"k":1358,"v":2},{"k":8652,"v":2},{"k":3402,"v":2},{"k":7991,"v":1},{"k":9832,"v":4},{"k":6061,"v":1},{"k":4356,"v":2},{"k":7745,"v":4},{"k":9752,"v":4},{"k":9392,"v":1},{"k":8324,"v":4},{"k":8973,"v":1},{"k":9767,"v":2},{"k":8965,"v":1},{"k":8329,"v":4},{"k":2496,"v":4},{"k":7225,"v":4},{"k":9142,"v":2},{"k":9174,"v":4},{"k":8751,"v":1},{"k":6761,"v":4},{"k":1186,"v":4},{"k":9237,"v":4},{"k":8699,"v":4},{"k":7760,"v":4},{"k":6461,"v":4},{"k":9807,"v":1},{"k":10332,"v":1},{"k":8313,"v":1},{"k":9798,"v":4},{"k":6049,"v":3},{"k":7911,"v":3},{"k":8321,"v":4},{"k":9250,"v":2},{"k":7445,"v":4},{"k":9408,"v":4},{"k":9032,"v":4},{"k":8992,"v":4},{"k":8178,"v":4},{"k":7529,"v":4},{"k":9925,"v":1},{"k":8428,"v":2},{"k":8087,"v":1},{"k":2788,"v":2},{"k":1999,"v":1},{"k":9632,"v":2},{"k":4473,"v":4},{"k":7509,"v":4},{"k":9714,"v":1},{"k":9173,"v":4},{"k":8722,"v":4},{"k":7515,"v":4},{"k":5432,"v":4},{"k":8390,"v":4},{"k":3220,"v":2},{"k":8723,"v":4},{"k":8984,"v":1},{"k":7612,"v":1},{"k":157,"v":4},{"k":9049,"v":1},{"k":8536,"v":3},{"k":8803,"v":1},{"k":8837,"v":1},{"k":9033,"v":1},{"k":7653,"v":4},{"k":10386,"v":1},{"k":4493,"v":1},{"k":8949,"v":4},{"k":9933,"v":3},{"k":6161,"v":2},{"k":9151,"v":2},{"k":7197,"v":1},{"k":3794,"v":1},{"k":9608,"v":4},{"k":6406,"v":4},{"k":6480,"v":1},{"k":8304,"v":1},{"k":9066,"v":1},{"k":10309,"v":2},{"k":7787,"v":1},{"k":6824,"v":4},{"k":85,"v":1},{"k":8411,"v":4},{"k":4375,"v":4},{"k":5230,"v":1},{"k":8167,"v":4},{"k":9766,"v":2},{"k":7417,"v":1},{"k":9636,"v":4},{"k":8938,"v":1},{"k":9260,"v":1},{"k":7436,"v":4},{"k":2376,"v":2},{"k":6333,"v":4},{"k":9461,"v":1},{"k":9924,"v":3},{"k":8636,"v":4}]},"succ":true,"errinfo":"ok","type":"enterroom"}';
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
