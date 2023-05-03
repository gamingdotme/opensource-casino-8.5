<?php 
namespace VanguardLTE\Games\LastBlastKenoGV
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
                            $result_tmp[] = '42/game,["open",{"bet":{"denoms":[5,10,25,50,100],"bets":[1,2,3,4,5,6,7,8,9,10]},"paytable":[[null],[null],[null],[null,0,2,28],[null,0,1,5,67],[null,0,0,2,21,240],[null,0,0,1,5,70,510],[null,0,0,1,2,15,160,1000],[null,0,0,0,2,7,48,630,1250],[null,0,0,0,1,4,22,170,855,1250],[null,0,0,0,1,2,7,50,560,1100,1250]],"rows":8,"columns":10,"picks":10,"rtp":0.9165,"credits":' . $balanceInCents . '}]';
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
                                $powerhit = 'false';
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
                                if( in_array($drawnNumbers[19], $matchNumbers) ) 
                                {
                                    $powerhit = 'true';
                                    $totalWin = $totalWin * 4;
                                }
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
                            $slotSettings->SetGameData('LastBlastKenoGVTotalWin', $totalWin);
                            $slotSettings->SetGameData('LastBlastKenoGVGambleStep', 5);
                            $hist = $slotSettings->GetGameData('LastBlastKenoGVCards');
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                            $bonusStr = 'turtles: [57, 61, 11, 1]';
                            $result_tmp[] = '42/game,["draw",{"draw":[' . implode(',', $drawnNumbers) . '],"picks":[' . implode(',', $ballSelected) . '],"catches":[' . implode(',', $matchNumbers) . '],"win":' . $totalWin . ',"guaranteedWin":true,"luckySymbol":0,"numBonusGames":5,"multiplier":6,"numPicks":6,"totalWon":' . $totalWin . ',"bonus":{},"powerhit":' . $powerhit . ',"_win":' . $totalWin . ',"_close":true,"_cost":' . count($matchNumbers) . ',"credits":' . $balanceInCents . '}]';
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
