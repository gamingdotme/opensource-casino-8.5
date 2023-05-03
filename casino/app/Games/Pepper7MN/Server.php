<?php 
namespace VanguardLTE\Games\Pepper7MN
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
                        $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$postData['action'];
                        switch( $aid ) 
                        {
                            case 'Init1':
                            case 'Init2':
                            case 'Act61':
                            case 'Ping':
                            case 'Act58':
                            case 'getBalance':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . ',"Denom":' . ($slotSettings->CurrentDenom * 100) . '}';
                                break;
                            case 'Act41':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . '}';
                                break;
                            case 'Act18':
                                if( isset($postData['reqDat']) ) 
                                {
                                    $aid = 'Act19';
                                    $lines = 25;
                                    $betline = $postData['reqDat']['bet'];
                                    $allbet = $betline * $lines;
                                    $postData['slotEvent'] = 'bet';
                                    if( $postData['reqDat']['freegamesMode'] == 'true' ) 
                                    {
                                        $postData['slotEvent'] = 'freespin';
                                    }
                                    if( $postData['slotEvent'] == 'bet' && $allbet < 1 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Bet "}';
                                        exit( $response );
                                    }
                                    if( $postData['slotEvent'] == 'bet' && $slotSettings->GetBalance() < $allbet ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Balance"}';
                                        exit( $response );
                                    }
                                    if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Bonus State"}';
                                        exit( $response );
                                    }
                                    if( $postData['slotEvent'] != 'freespin' ) 
                                    {
                                        if( !isset($postData['slotEvent']) ) 
                                        {
                                            $postData['slotEvent'] = 'bet';
                                        }
                                        $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                        $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                        $jackState = $slotSettings->UpdateJackpots($allbet);
                                        if( is_array($jackState) ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                        }
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                        $bonusMpl = 1;
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                        $bonusMpl = 1;
                                    }
                                    $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                    $winType = $winTypeTmp[0];
                                    $spinWinLimit = $winTypeTmp[1];
                                    $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                    for( $i = 0; $i <= 2000; $i++ ) 
                                    {
                                        $totalWin = 0;
                                        $lineWins = [];
                                        $cWins = [];
                                        $cWinsCount = [];
                                        $cWinsMpl = [];
                                        $wild = ['9'];
                                        $scatter = '9';
                                        $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                        $k = 0;
                                        $tmpStringWin = '';
                                        for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                        {
                                            $csym = (string)$slotSettings->SymbolGame[$j];
                                            if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                            {
                                            }
                                            else
                                            {
                                                $isSeq = true;
                                                $wlines = [];
                                                $reels_t = [];
                                                $reels_t['reel1'] = [
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $reels_t['reel2'] = [
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $reels_t['reel3'] = [
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $reels_t['reel4'] = [
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $reels_t['reel5'] = [
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $reelWayMpl = [
                                                    0, 
                                                    0, 
                                                    0, 
                                                    0, 
                                                    0, 
                                                    0
                                                ];
                                                $wildMpl = 1;
                                                for( $rl = 1; $rl <= 5; $rl++ ) 
                                                {
                                                    $isSeq = false;
                                                    for( $rs = 0; $rs <= 2; $rs++ ) 
                                                    {
                                                        if( $reels['reel' . $rl][$rs] == $csym || in_array($reels['reel' . $rl][$rs], $wild) ) 
                                                        {
                                                            $reels_t['reel' . $rl][$rs] = -1;
                                                            $reelWayMpl[$rl]++;
                                                            $isSeq = true;
                                                            if( in_array($reels['reel' . $rl][$rs], $wild) ) 
                                                            {
                                                                $wildMpl = 1;
                                                            }
                                                        }
                                                    }
                                                    if( !$isSeq ) 
                                                    {
                                                        break;
                                                    }
                                                }
                                                $cWin = 0;
                                                $wwMpl = 1;
                                                if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 ) 
                                                {
                                                    $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $wildMpl;
                                                    if( $cWin > 0 ) 
                                                    {
                                                        $cWinsCount[$k] = '[' . $csym . ',' . $cWin . ',' . $slotSettings->GetSymPositions($reels_t) . ',' . $wildMpl . ']';
                                                    }
                                                    $wwMpl = $reelWayMpl[1] * $reelWayMpl[2];
                                                }
                                                if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 ) 
                                                {
                                                    $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $wildMpl;
                                                    if( $cWin > 0 ) 
                                                    {
                                                        $cWinsCount[$k] = '[' . $csym . ',' . $cWin . ',' . $slotSettings->GetSymPositions($reels_t) . ',' . $wildMpl . ']';
                                                    }
                                                    $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3];
                                                }
                                                if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 && $reelWayMpl[4] > 0 ) 
                                                {
                                                    $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4] * $wildMpl;
                                                    if( $cWin > 0 ) 
                                                    {
                                                        $cWinsCount[$k] = '[' . $csym . ',' . $cWin . ',' . $slotSettings->GetSymPositions($reels_t) . ',' . $wildMpl . ']';
                                                    }
                                                    $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4];
                                                }
                                                if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 && $reelWayMpl[4] > 0 && $reelWayMpl[5] > 0 ) 
                                                {
                                                    $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4] * $reelWayMpl[5] * $wildMpl;
                                                    if( $cWin > 0 ) 
                                                    {
                                                        $cWinsCount[$k] = '[' . $csym . ',' . $cWin . ',' . $slotSettings->GetSymPositions($reels_t) . ',' . $wildMpl . ']';
                                                    }
                                                    $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4] * $reelWayMpl[5];
                                                }
                                                if( $cWin > 0 ) 
                                                {
                                                    if( $wwMpl == 0 ) 
                                                    {
                                                        $wwMpl = 1;
                                                    }
                                                    $cWinsMpl[$k] = $bonusMpl;
                                                    $cWins[$k] = $cWin / $bonusMpl;
                                                    $totalWin += $cWin;
                                                    $k++;
                                                }
                                            }
                                        }
                                        $scattersWin = 0;
                                        $scattersStr = '';
                                        $scattersCount = 0;
                                        $scattersCount2 = 0;
                                        $scPos = [];
                                        $scRPos = [
                                            0, 
                                            1, 
                                            2, 
                                            4, 
                                            8, 
                                            16
                                        ];
                                        $reels_ts = [];
                                        $reels_ts['reel1'] = [
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel2'] = [
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel3'] = [
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel4'] = [
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel5'] = [
                                            0, 
                                            0, 
                                            0
                                        ];
                                        for( $rl = 1; $rl <= 5; $rl++ ) 
                                        {
                                            for( $rs = 0; $rs <= 2; $rs++ ) 
                                            {
                                                if( $reels['reel' . $rl][$rs] == $scatter ) 
                                                {
                                                    $reels_ts['reel' . $rl][$rs] = -1;
                                                    $scattersCount++;
                                                }
                                            }
                                        }
                                        for( $rl = 1; $rl <= 5; $rl++ ) 
                                        {
                                            if( $reels['reel' . $rl][0] == '6' || $reels['reel' . $rl][1] == '6' || $reels['reel' . $rl][2] == '6' ) 
                                            {
                                                $scattersCount2++;
                                            }
                                            else
                                            {
                                                break;
                                            }
                                        }
                                        $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                        $totalWin += $scattersWin;
                                        if( $i > 1000 ) 
                                        {
                                            $winType = 'none';
                                        }
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                        {
                                        }
                                        else
                                        {
                                            $minWin = $slotSettings->GetRandomPay();
                                            if( $i > 700 ) 
                                            {
                                                $minWin = 0;
                                            }
                                            if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $allbet) ) 
                                            {
                                            }
                                            else if( ($scattersCount >= 3 || $scattersCount2 >= 4) && $winType != 'bonus' ) 
                                            {
                                            }
                                            else if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
                                            {
                                                $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                                if( $cBank < $spinWinLimit ) 
                                                {
                                                    $spinWinLimit = $cBank;
                                                }
                                                else
                                                {
                                                    break;
                                                }
                                            }
                                            else if( $totalWin > 0 && $totalWin <= $spinWinLimit && $winType == 'win' ) 
                                            {
                                                $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                                if( $cBank < $spinWinLimit ) 
                                                {
                                                    $spinWinLimit = $cBank;
                                                }
                                                else
                                                {
                                                    break;
                                                }
                                            }
                                            else if( $totalWin == 0 && $winType == 'none' ) 
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                    }
                                    $reportWin = $totalWin;
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                    }
                                    $fs = 0;
                                    $swu = 0;
                                    $swm = 0;
                                    if( $scattersCount == 2 && $scattersWin > 0 ) 
                                    {
                                        $swm = $slotSettings->GetSymPositions($reels_ts);
                                    }
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $swm = $slotSettings->GetSymPositions($reels_ts);
                                        if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                        }
                                        else
                                        {
                                            $balanceInCents0 = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $balanceInCents0);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                        }
                                        $fs = $slotSettings->slotFreeCount[$scattersCount];
                                        $swu = 1;
                                    }
                                    $winString = implode(',', $lineWins);
                                    $jsSpin = '' . json_encode($reels) . '';
                                    $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"FreeBalance":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance') . ',"scattersWin":' . $scattersWin . ',"swm":' . $swm . ',"fsnew":' . $fs . ',"fscount":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"slotLines":' . $lines . '' . $lines . ',"spinWins":[' . implode(',', $cWinsCount) . '],"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"Jackpots":[],"reelsSymbols":' . $jsSpin . '}}';
                                    $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                    $winstring = '';
                                    $postData['payload'] = $response;
                                }
                                else
                                {
                                    $lastEvent = $slotSettings->GetHistory();
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    if( $lastEvent != 'NULL' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->FreeBalance);
                                        $reels = $lastEvent->serverResponse->reelsSymbols;
                                        $lines = $lastEvent->serverResponse->slotLines;
                                        $bet = $lastEvent->serverResponse->slotBet * 100;
                                        $postData['payload'] = $lastEvent;
                                    }
                                    $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                }
                                $result_tmp[0] = '{"action":"' . $aid . '","serverResponse":' . json_encode($postData) . ',"nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . ',"Denom":' . ($slotSettings->CurrentDenom * 100) . '}';
                                break;
                        }
                        $response = $result_tmp[0];
                        $slotSettings->SaveGameData();
                        $slotSettings->SaveGameDataStatic();
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
