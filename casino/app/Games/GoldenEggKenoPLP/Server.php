<?php 
namespace VanguardLTE\Games\GoldenEggKenoPLP
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
                    $postData = $postData0['gameData'];
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                    $result_tmp = [];
                    $aid = '';
                    if( $postData['command'] == 'playGame' ) 
                    {
                        $postData['command'] = 'bet';
                    }
                    if( $postData['command'] == 'bet' ) 
                    {
                        $lines = 1;
                        $betline = $postData['params']['bet'] / 100;
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
                            $gameBets = $slotSettings->Bet;
                            $denoms = [];
                            $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                            foreach( $gameBets as &$b ) 
                            {
                                $b = '' . ($b * 100) . '';
                            }
                            $slotSettings->SetGameData('GoldenEggKenoPLPFreeSpin', 0);
                            $result_tmp[] = '{"credits": ' . $balanceInCents . ', "ruleList": [{"nrOfPickedNumbers": 1, "nrOfMatchingNumbers": 1, "multiplier": 3}, {"nrOfPickedNumbers": 2, "nrOfMatchingNumbers": 1, "multiplier": 1}, {"nrOfPickedNumbers": 2, "nrOfMatchingNumbers": 2, "multiplier": 8}, {"nrOfPickedNumbers": 3, "nrOfMatchingNumbers": 1, "multiplier": 1}, {"nrOfPickedNumbers": 3, "nrOfMatchingNumbers": 2, "multiplier": 2}, {"nrOfPickedNumbers": 3, "nrOfMatchingNumbers": 3, "multiplier": 13}, {"nrOfPickedNumbers": 4, "nrOfMatchingNumbers": 2, "multiplier": 1}, {"nrOfPickedNumbers": 4, "nrOfMatchingNumbers": 3, "multiplier": 5}, {"nrOfPickedNumbers": 4, "nrOfMatchingNumbers": 4, "multiplier": 140}, {"nrOfPickedNumbers": 5, "nrOfMatchingNumbers": 2, "multiplier": 1}, {"nrOfPickedNumbers": 5, "nrOfMatchingNumbers": 3, "multiplier": 2}, {"nrOfPickedNumbers": 5, "nrOfMatchingNumbers": 4, "multiplier": 10}, {"nrOfPickedNumbers": 5, "nrOfMatchingNumbers": 5, "multiplier": 450}, {"nrOfPickedNumbers": 6, "nrOfMatchingNumbers": 3, "multiplier": 1}, {"nrOfPickedNumbers": 6, "nrOfMatchingNumbers": 4, "multiplier": 10}, {"nrOfPickedNumbers": 6, "nrOfMatchingNumbers": 5, "multiplier": 100}, {"nrOfPickedNumbers": 6, "nrOfMatchingNumbers": 6, "multiplier": 1000}, {"nrOfPickedNumbers": 7, "nrOfMatchingNumbers": 3, "multiplier": 1}, {"nrOfPickedNumbers": 7, "nrOfMatchingNumbers": 4, "multiplier": 2}, {"nrOfPickedNumbers": 7, "nrOfMatchingNumbers": 5, "multiplier": 30}, {"nrOfPickedNumbers": 7, "nrOfMatchingNumbers": 6, "multiplier": 300}, {"nrOfPickedNumbers": 7, "nrOfMatchingNumbers": 7, "multiplier": 3000}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 3, "multiplier": 1}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 4, "multiplier": 2}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 5, "multiplier": 5}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 6, "multiplier": 75}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 7, "multiplier": 1000}, {"nrOfPickedNumbers": 8, "nrOfMatchingNumbers": 8, "multiplier": 5000}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 3, "multiplier": 1}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 4, "multiplier": 2}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 5, "multiplier": 5}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 6, "multiplier": 10}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 7, "multiplier": 100}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 8, "multiplier": 2000}, {"nrOfPickedNumbers": 9, "nrOfMatchingNumbers": 9, "multiplier": 5000}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 4, "multiplier": 1}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 5, "multiplier": 5}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 6, "multiplier": 10}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 7, "multiplier": 100}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 8, "multiplier": 500}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 9, "multiplier": 7500}, {"nrOfPickedNumbers": 10, "nrOfMatchingNumbers": 10, "multiplier": 10000}], "betLimits": [' . implode(',', $gameBets) . '], "serverVersion": "1.2.7", "action": "ApplicationConnected"}';
                            break;
                        case 'bet':
                            $lines = 1;
                            $betline = $postData['params']['bet'] / 100;
                            $allbet = $betline * $lines;
                            $postData['slotEvent'] = 'bet';
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                if( $slotSettings->GetGameData('GoldenEggKenoPLPFreeSpin') == 1 ) 
                                {
                                    $slotSettings->SetGameData('GoldenEggKenoPLPFreeSpin', 0);
                                }
                                else
                                {
                                    $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetGameData('GoldenEggKenoPLPFreeSpin', 0);
                                }
                            }
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            $balls = [];
                            for( $b = 0; $b < 80; $b++ ) 
                            {
                                $balls[] = $b + 1;
                            }
                            $ballSelected = $postData['params']['numbers'];
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
                            $isBonusWon = 'false';
                            if( in_array($drawnNumbers[19], $matchNumbers) ) 
                            {
                                $isBonusWon = 'true';
                                $slotSettings->SetGameData('GoldenEggKenoPLPFreeSpin', 1);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                            $winstring = '';
                            $slotSettings->SetGameData('GoldenEggKenoPLPTotalWin', $totalWin);
                            if( $slotSettings->GetGameData('GoldenEggKenoPLPFreeSpin') == 1 ) 
                            {
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, 'freespin');
                            }
                            else
                            {
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, 'bet');
                            }
                            $result_tmp[] = '{"win": ' . ($totalWin * 100) . ', "isBonusWon": ' . $isBonusWon . ', "actualLotteryWin": ' . ($totalWin * 100) . ', "bet": ' . ($betline * 100) . ', "gameSolved": false, "playable": false, "board": {"maxNumber": 80, "minNumber": 1, "playerNumbers": [' . implode(',', $ballSelected) . '], "pickedNumbers": [' . implode(',', $drawnNumbers) . '], "nrOfWinningNumbers": 1}, "previousRounds": [], "credits": ' . $balanceInCents . ', "action": "ApplicationPlayResult"}';
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
