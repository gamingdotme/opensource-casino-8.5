<?php 
namespace VanguardLTE\Games\FishingWarSG
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                        if( isset($_GET['command']) ) 
                        {
                            $aid = $_GET['command'];
                        }
                        else
                        {
                            $aid = $postData['gameData']['gameCode'];
                            if( $postData['gameData']['t'] == '701' ) 
                            {
                                $aid = 'EnterRoom';
                            }
                            if( $postData['gameData']['t'] == '703' ) 
                            {
                                $aid = 'Hit';
                            }
                            if( $postData['gameData']['t'] == '704' ) 
                            {
                                $aid = 'Hit2';
                            }
                            if( $postData['gameData']['t'] == '708' ) 
                            {
                                $aid = 'ExitRoom';
                            }
                            if( $postData['gameData']['t'] == '709' ) 
                            {
                                $aid = 'Balance';
                            }
                            $serialNo = $postData['gameData']['serialNo'];
                        }
                        $curTime = floor(microtime(true) * 1000);
                        switch( $aid ) 
                        {
                            case 'F-SF02':
                                if( $serialNo == '99999.' ) 
                                {
                                    $result_tmp[] = '99999.{"serialNo":"99999.","code":0,"sessionId":"c7w5i0rqo0ojimg1rewdmxhhnlxto13m","token":"f799cce70b4ead8df9fe31a2728d08c2","acct":{"acctId":"' . $slotSettings->username . '","acctName":"' . $slotSettings->username . '","merchant":"TEST","currency":"PTS","balance":' . $balanceInCents . ',"oneWallet":false},"lobbyUrl":"","reTime":30}';
                                }
                                else if( $serialNo == '0' ) 
                                {
                                    $result_tmp[] = '1.{"serialNo":"0","code":0,"sessionId":"c7w5i0rqo0ojimg1rewdmxhhnlxto13m","token":"f799cce70b4ead8df9fe31a2728d08c2","acct":{"acctId":"' . $slotSettings->username . '","acctName":"' . $slotSettings->username . '","merchant":"TEST","currency":"PTS","balance":' . $balanceInCents . ',"oneWallet":false},"lobbyUrl":"","reTime":30}';
                                }
                                else if( $serialNo == '2' ) 
                                {
                                    $result_tmp[] = '6.{"serialNo":"2","code":0,"time":"2020-08-04 09:20:08","timeMillis":1596504008529,"timeZone":"+0800"}';
                                }
                                else if( $serialNo == '3' ) 
                                {
                                    $result_tmp[] = '700.{"serialNo":"3","code":0,"gameCode":"F-SF02","ris":[{"id":"L1","dm":[[0.01],[0.05,0.10],[0.20,0.30,0.40],[0.50,0.60],[0.70,0.80],[0.90,1.00]],"ddm":0.01},{"id":"L2","dm":[[0.10,0.20,0.30,0.40,0.50],[0.60,0.70,0.80,0.90,1.00],[2],[3],[4],[5]],"ddm":0.10},{"id":"L3","dm":[[1.00,2.00],[3.00,4.00],[5.00,6.00],[7.00,8.00],[9],[10]],"ddm":1.00}]}';
                                }
                                else
                                {
                                    $result_tmp[] = '6.{"serialNo":"' . $serialNo . '","code":0,"time":"2020-08-04 09:20:13","timeMillis":' . $curTime . ',"timeZone":"+0800"}';
                                }
                                break;
                            case 'EnterRoom':
                                $KilledFish = [];
                                $slotSettings->SetGameData($slotSettings->slotId . 'KilledFish', $KilledFish);
                                $result_tmp[0] = '701.{"serialNo":"' . $serialNo . '","code":0,"time":"2020-08-04 09:44:16","backHome":true,"di":{"bal":' . $balanceInCents . ',"rmId":"L1","dkId":2,"ciId":1,"st":1,"atId":1241064,"skId":2,"scId":1,"isSc":false,"mbs":15,"pis":[],"tms":[],"kis":[{"kd":0,"sp":200},{"kd":1,"sp":150},{"kd":2,"sp":180},{"kd":3,"sp":100},{"kd":4,"sp":100},{"kd":5,"sp":100},{"kd":6,"sp":70},{"kd":7,"sp":90},{"kd":8,"sp":80},{"kd":9,"sp":100},{"kd":10,"sp":70},{"kd":11,"sp":150},{"kd":12,"sp":100},{"kd":13,"sp":100},{"kd":14,"sp":100},{"kd":15,"sp":70},{"kd":16,"sp":70},{"kd":17,"sp":70},{"kd":18,"sp":70},{"kd":19,"sp":70},{"kd":20,"sp":70},{"kd":21,"sp":70},{"kd":100,"sp":70},{"kd":101,"sp":70},{"kd":102,"sp":70,"se":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]},{"kd":103,"sp":180},{"kd":104,"sp":60},{"kd":105,"sp":60},{"kd":106,"sp":70},{"kd":107,"sp":70}],"lscet":"2020-08-04 09:41:33"}}';
                                break;
                            case 'Balance':
                                $result_tmp[] = '709.{"serialNo":"' . $serialNo . '","code":0,"pis":[]}';
                                break;
                            case 'ExitRoom':
                                $result_tmp[] = '708.{"serialNo":"' . $serialNo . '","code":0,"ai":{"acctId":"' . $slotSettings->username . '","acctName":"' . $slotSettings->username . '","merchant":"TEST","currency":"PTS","balance":' . $balanceInCents . '}}';
                                break;
                            case 'Hit':
                                $CurrentBet = $postData['gameData']['bet'];
                                $allbet = $CurrentBet;
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $CurrentBet);
                                $bulletid = [$postData['gameData']['blId']];
                                $fishids = [$postData['gameData']['fhId']];
                                $fishpids = [$postData['gameData']['kd']];
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'fhId', $postData['gameData']['fhId']);
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $totalWin0 = 0;
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                for( $i = 0; $i < count($fishpids); $i++ ) 
                                {
                                    $fishType = $fishpids[$i];
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
                                    if( $fishType == 106 ) 
                                    {
                                        $wheelPays = [
                                            30, 
                                            40, 
                                            50, 
                                            60, 
                                            70, 
                                            80, 
                                            90, 
                                            100, 
                                            200, 
                                            300
                                        ];
                                        shuffle($wheelPays);
                                        $payRate = $wheelPays[0];
                                    }
                                    $isWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                    $KilledFish = $slotSettings->GetGameData($slotSettings->slotId . 'KilledFish');
                                    if( in_array($fishids[$i], $KilledFish) ) 
                                    {
                                        $isWin = 0;
                                    }
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
                                    $isFreeze = false;
                                    if( $isWin == 1 && $totalWin + ($payRate * $allbet) <= $bank ) 
                                    {
                                        if( $fishType == 101 ) 
                                        {
                                            $totalWin0 += ($payRate * $allbet);
                                        }
                                        else
                                        {
                                            $totalWin += ($payRate * $allbet);
                                        }
                                        if( $fishType == 101 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'MassAttack', 1);
                                        }
                                        if( $fishType == 102 ) 
                                        {
                                            $isFreeze = true;
                                        }
                                        $totalWinsArr[] = '{"id":' . $fishids[$i] . ',"kd":' . $postData['gameData']['kd'] . ',"win":' . ($payRate * $allbet) . ',"od":' . $payRate . '}';
                                    }
                                }
                                if( $totalWin + $totalWin0 > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $KilledFish[] = $postData['gameData']['fhId'];
                                    $slotSettings->SetGameData($slotSettings->slotId . 'KilledFish', $KilledFish);
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","ty":1,"code":0,"win":' . ($totalWin + $totalWin0) . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . ',"fhes":[' . implode(',', $totalWinsArr) . ']}';
                                    if( $isFreeze ) 
                                    {
                                        $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                        $O7gBMXEqvgq3woLZuVvMFOSYRjehwrX68 = [];
                                        foreach( $postData['gameData']['fishFreezes'] as $vl ) 
                                        {
                                            $O7gBMXEqvgq3woLZuVvMFOSYRjehwrX68[] = $vl[0];
                                        }
                                        $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                        $result_tmp[] = '-120.{"kd":102,"fhId":' . $postData['gameData']['fhId'] . ',"ciId":1,"O7gBMXEqvgq3woLZuVvMFOSYRjehwrX68":' . json_encode($O7gBMXEqvgq3woLZuVvMFOSYRjehwrX68) . '}';
                                        $result_tmp[] = '-111.{"ciId":1,"bal":' . $balanceInCents . ',"bet":' . $allbet . ',"win":' . $totalWin . ',"fhes":[' . implode(',', $totalWinsArr) . '],"fr":false,"et":"2020-09-07 22:37:12"}';
                                    }
                                }
                                else
                                {
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","code":0,"ty":1,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . '}';
                                }
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                            case 'Hit2':
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'MassAttack') != 1 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid command"}';
                                    exit( $response );
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'MassAttack', 0);
                                $fishids = $postData['gameData']['fhIds'];
                                $fishpids = [];
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet;
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                for( $i = 0; $i < count($fishids); $i++ ) 
                                {
                                    $tpay = explode('000', $fishids[$i]);
                                    $fishpids[$i] = $tpay[1];
                                }
                                for( $i = 0; $i < count($fishpids); $i++ ) 
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
                                    $isWin = rand(1, 2);
                                    if( $isWin == 1 && $totalWin + ($payRate * $allbet) <= $bank ) 
                                    {
                                        $totalWin += ($payRate * $allbet);
                                        $totalWinsArr[] = '{"id":' . $fishids[$i] . ',"kd":' . $fishType . ',"win":' . ($payRate * $allbet) . ',"od":' . $payRate . '}';
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[] = '704.{"serialNo":"' . $serialNo . '","code":0,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"fhes":[' . implode(',', $totalWinsArr) . '],"st":9,"fhId":' . $postData['gameData']['rfId'] . ',"kd":' . $postData['gameData']['kd'] . '}';
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                        }
                        $slotSettings->SaveGameData();
                        echo '------' . implode('------', $result_tmp);
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
