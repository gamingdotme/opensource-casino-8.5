<?php 
namespace VanguardLTE\Games\FishingGodSG
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
                            $serialNo = $postData['gameData']['serialNo'];
                        }
                        $curTime = floor(microtime(true) * 1000);
                        switch( $aid ) 
                        {
                            case 'F-SF01':
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
                                $result_tmp[0] = '701.{"serialNo":"' . $serialNo . '","code":0,"time":"2020-08-04 21:16:54","backHome":true,"di":{"bal":' . $balanceInCents . ',"rmId":"L1","dkId":2,"ciId":1,"st":1,"atId":1242865,"skId":2,"scId":0,"isSc":false,"mbs":15,"pis":[],"tms":[],"lscet":"2020-08-04 21:14:16"}}';
                                break;
                            case 'room.fire':
                                $result_tmp[] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.fire","code":200,"data":{"pay":true,"isFree":false,"machineCannonInfo":null,"shellUUID":"f4df0a11-3ed6-4685-9f95-877c0224d47f"}}';
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
                                $bulletid = [$postData['gameData']['blId']];
                                $fishids = [$postData['gameData']['fhId']];
                                $fishpids = [$postData['gameData']['kd']];
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
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
                                        if( $fishType == 101 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'MassAttack', 1);
                                        }
                                        $totalWinsArr[] = '{"id":' . $fishids[$i] . ',"kd":' . $postData['gameData']['kd'] . ',"win":' . ($payRate * $allbet) . ',"od":' . $payRate . '}';
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","code":0,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . ',"fhes":[' . implode(',', $totalWinsArr) . ']}';
                                }
                                else
                                {
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","code":0,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . '}';
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
                                $bulletid = [$postData['gameData']['blId']];
                                $fishids = $postData['gameData']['fhIds'];
                                $fishpids = [];
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                $payRate = 0;
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
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","code":0,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . ',"fhes":[' . implode(',', $totalWinsArr) . ']}';
                                }
                                else
                                {
                                    $result_tmp[] = '703.{"serialNo":"' . $serialNo . '","code":0,"win":' . $totalWin . ',"bal":' . $balanceInCents . ',"scBet":' . $allbet . ',"blId":"' . $postData['gameData']['blId'] . '","fhId":' . $postData['gameData']['fhId'] . '}';
                                }
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
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
