<?php 
namespace VanguardLTE\Games\TropicalVacationKenoGV
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
                    $postData0 = json_decode(trim(file_get_contents('php://input')), true);
                    $postData = [];
                    $postData['command'] = $postData0['gameData'][0];
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                    $result_tmp = [];
                    $aid = '';
                    if( $postData['command'] == 'draw' ) 
                    {
                        $postData['command'] = 'bet';
                    }
                    if( $postData['command'] == 'bet' ) 
                    {
                        $lines = 1;
                        $betline = $postData0['gameData'][1]['bet'];
                        if( $lines <= 0 || $betline <= 0.0001 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetBalance() < ($lines * $betline) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
                            exit( $response );
                        }
                    }
                    $aid = (string)$postData['command'];
                    switch( $aid ) 
                    {
                        case 'setup':
                            $result_tmp[] = '40';
                            $result_tmp[] = '40/game';
                            break;
                        case 'open':
                            $gameBets = $slotSettings->Bet;
                            $denoms = [];
                            $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                            foreach( $gameBets as &$b ) 
                            {
                                $b = '' . ($b * 100) . '';
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                            $result_tmp[] = '42/game,["open",{"bet":{"denoms":[5,10,25,50,100],"bets":[1,2,3,4,5,6,7,8,9,10]},"paytable":[[null],[0],[0,0],[0,0,2,21],[0,0,1,6,40],[0,0,0,3,20,130],[0,0,0,1,5,90,250],[0,0,0,1,3,15,150,900],[0,0,0,0,2,10,75,300,1500],[0,0,0,0,1,6,25,175,800,2500],[0,0,0,0,1,3,10,50,600,1000,2500]],"rows":8,"columns":10,"picks":10,"rtp":0.9354,"feature":{"multipliers":{"3":[2,2,3,3,4,4,5,5,6,7],"4":[2,2,3,3,4,4,5,5,6,6],"5":[2,2,3,3,4,4,5,5,6,6],"6":[2,2,3,3,4,4,5,5,6,6],"7":[2,2,3,3,4,4,5,5,6,6],"8":[2,2,3,3,4,4,5,5,6,6],"9":[2,2,3,3,4,4,5,5,6,6],"10":[2,2,3,3,4,4,5,5,6,7]},"maxMult":[8,9,10,11,12],"maxNumPicks":3,"numBonusGames":6,"numLuckySymbols":3},"credits":' . $balanceInCents . '}]';
                            break;
                        case 'feature':
                            $actionFeature = $postData0['gameData'][1]['action'];
                            $FeatureStep = $slotSettings->GetGameData('TropicalVacationKenoGVFeatureStep');
                            $totalPicked = $slotSettings->GetGameData('TropicalVacationKenoGVtotalPicked');
                            $pickedObjectsIndexes = $slotSettings->GetGameData('TropicalVacationKenoGVpickedObjectsIndexes');
                            $pickedMultipliers = $slotSettings->GetGameData('TropicalVacationKenoGVpickedMultipliers');
                            if( $actionFeature == 'start' ) 
                            {
                                $FeatureMultipliers = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    8, 
                                    9, 
                                    10
                                ];
                                shuffle($FeatureMultipliers);
                                $result_tmp[0] = '42/game,["feature",{"win":0,"picks":4,"multipliers":[' . implode(',', $FeatureMultipliers) . '],"name":"tikibar","action":"start","_cost":0,"credits":' . $balanceInCents . '}]';
                            }
                            else if( $actionFeature == 'choosePick' ) 
                            {
                                $FeatureMultipliers = $slotSettings->GetGameData('TropicalVacationKenoGVFeatureMultipliers');
                                $pickIndex = $postData0['gameData'][1]['index'];
                                $pickMultplier = $FeatureMultipliers[$pickIndex];
                                $pickedObjectsIndexes[] = $pickIndex;
                                $totalPicked++;
                                $pickedMultipliers[] = $pickMultplier;
                                if( !is_array($pickedMultipliers) || !is_array($FeatureMultipliers) || !is_array($pickedObjectsIndexes) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"error","serverResponse":"invalid feature state"}';
                                    exit( $response );
                                }
                                $result_tmp[0] = '42/game,["feature",{"multiplier":' . $pickMultplier . ',"pickedMultipliers":[' . implode(',', $pickedMultipliers) . '],"pickIndexes":[' . implode(',', $pickedObjectsIndexes) . '],"pickedObjectsIndexes":[' . implode(',', $pickedObjectsIndexes) . '],"pickedObjectsMultipliers":[' . implode(',', $pickedMultipliers) . '],"index":' . $pickIndex . ',"totalPicked":' . $totalPicked . ',"picks":4,"name":"tikibar","action":"choosePick","_cost":0,"credits":' . $balanceInCents . '}]';
                            }
                            else if( $actionFeature == 'finishPick' ) 
                            {
                                $FeatureMultipliers = $slotSettings->GetGameData('TropicalVacationKenoGVFeatureMultipliers');
                                if( !is_array($FeatureMultipliers) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"error","serverResponse":"invalid feature state"}';
                                    exit( $response );
                                }
                                $result_tmp[0] = '42/game,["feature",{"multipliers":[' . implode(',', $slotSettings->GetGameData('TropicalVacationKenoGVFeatureMultipliers')) . '],"_close":true,"pickedMultipliers":[' . implode(',', $pickedMultipliers) . '],"pickIndexes":[' . implode(',', $pickedObjectsIndexes) . '],"pickedObjectsIndexes":[' . implode(',', $pickedObjectsIndexes) . '],"pickedObjectsMultipliers":[' . implode(',', $pickedMultipliers) . '],"name":"tikibar","action":"finishPick","_cost":0,"credits":' . $balanceInCents . '}]';
                            }
                            $slotSettings->SetGameData('TropicalVacationKenoGVFeatureMultipliers', $FeatureMultipliers);
                            $slotSettings->SetGameData('TropicalVacationKenoGVFeatureStep', $FeatureStep);
                            $slotSettings->SetGameData('TropicalVacationKenoGVtotalPicked', $totalPicked);
                            $slotSettings->SetGameData('TropicalVacationKenoGVpickedObjectsIndexes', $pickedObjectsIndexes);
                            $slotSettings->SetGameData('TropicalVacationKenoGVpickedMultipliers', $pickedMultipliers);
                            break;
                        case 'bet':
                            $lines = 1;
                            $betline = $postData0['gameData'][1]['bet'];
                            $allbet = $betline * $lines;
                            $postData['slotEvent'] = 'bet';
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                            }
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            $balls = [];
                            for( $b = 0; $b < 80; $b++ ) 
                            {
                                $balls[] = $b + 1;
                            }
                            $ballSelected = $postData0['gameData'][1]['selected'];
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                shuffle($balls);
                                $matchNumbers = [];
                                $drawnNumbers = [];
                                for( $a = 0; $a < 20; $a++ ) 
                                {
                                    $drawnNumbers[] = $balls[$a];
                                }
                                for( $b = 0; $b < count($ballSelected); $b++ ) 
                                {
                                    $curBall = $ballSelected[$b];
                                    if( in_array($curBall, $drawnNumbers) ) 
                                    {
                                        $matchNumbers[] = $curBall;
                                    }
                                }
                                $curPays = $slotSettings->Paytable[count($ballSelected)];
                                $totalWin = $betline * $curPays[count($matchNumbers)];
                                if( $totalWin <= $bank ) 
                                {
                                    break;
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $slotSettings->SetGameData('TropicalVacationKenoGVTotalWin', $totalWin);
                            $slotSettings->SetGameData('TropicalVacationKenoGVGambleStep', 5);
                            $hist = $slotSettings->GetGameData('TropicalVacationKenoGVCards');
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                            shuffle($balls);
                            $turtlesCnt = rand(0, 8);
                            $turtlesCurCnt = 0;
                            $turtlesArr = [];
                            $bonuses = '';
                            for( $i = 0; $i < $turtlesCnt; $i++ ) 
                            {
                                $turtlesArr[] = $balls[$i];
                                if( in_array($balls[$i], $matchNumbers) ) 
                                {
                                    $turtlesCurCnt++;
                                }
                            }
                            if( $turtlesCurCnt >= 3 ) 
                            {
                                $bonuses = ',"bonuses":["tikibar"]';
                            }
                            $bonusStr = '"turtles": [' . implode(',', $turtlesArr) . ']';
                            $result_tmp[] = '42/game,["draw",{"draw":[' . implode(',', $drawnNumbers) . '],"picks":[' . implode(',', $ballSelected) . ']' . $bonuses . ',"catches":[' . implode(',', $matchNumbers) . '],"win":' . $totalWin . ',"tikiObjectsSpots":{"spot":[]},"pickedObjectsIndexes":[],"pickedObjectsMultipliers":[],"multipliers":[],"indexes":[],"multHit":[],"multiplier":1,"numBonusGames":0,"bonusTotalWin":0,"bonus":{' . $bonusStr . '},"_close":true,"_cost":' . count($matchNumbers) . ',"credits":' . $balanceInCents . '}]';
                            break;
                    }
                    $response = implode('------:::', $result_tmp);
                    $slotSettings->SaveGameData();
                    echo ':::' . $response;
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
