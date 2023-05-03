<?php 
namespace VanguardLTE\Games\AllWaysJokerAM
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
                    $floatBet = 100;
                    $response = '';
                    $lines = 1;
                    $linesFixed = 1;
                    $symCount = '9';
                    $symCountAll = 'a';
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                    $fixedLinesFormated0 = dechex($lines + 1);
                    if( strlen($fixedLinesFormated0) <= 1 ) 
                    {
                        $fixedLinesFormated0 = '0' . $fixedLinesFormated0;
                    }
                    $fixedLinesFormated = dechex($lines);
                    if( strlen($fixedLinesFormated) <= 1 ) 
                    {
                        $fixedLinesFormated = '0' . $fixedLinesFormated;
                    }
                    $fixedLinesFormatedStr = '';
                    for( $i = 1; $i <= $lines; $i++ ) 
                    {
                        $fixedLinesFormatedStr .= '10';
                    }
                    $gameData = [];
                    $tmpPar = explode(',', $postData['gameData']);
                    $gameData['slotEvent'] = $tmpPar[0];
                    if( $gameData['slotEvent'] == 'A/u251' || $gameData['slotEvent'] == 'A/u256' ) 
                    {
                        if( $gameData['slotEvent'] == 'A/u256' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') > 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetBalance() < $slotSettings->Bet[$tmpPar[2]] && $gameData['slotEvent'] == 'A/u251' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid balance"}';
                            exit( $response );
                        }
                        if( !isset($slotSettings->Bet[$tmpPar[2]]) || $tmpPar[1] <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid bet/lines"}';
                            exit( $response );
                        }
                    }
                    if( $gameData['slotEvent'] == 'A/u257' && $slotSettings->GetGameData($slotSettings->slotId . 'DoubleWin') <= 0 ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                        exit( $response );
                    }
                    if( $gameData['slotEvent'] == 'A/u256' ) 
                    {
                        $postData['spinType'] = 'free';
                        $gameData['slotEvent'] = 'A/u251';
                    }
                    else
                    {
                        $postData['spinType'] = 'regular';
                    }
                    switch( $gameData['slotEvent'] ) 
                    {
                        case 'A/u350':
                            $winall = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                            if( !is_numeric($winall) ) 
                            {
                                $winall = 0;
                            }
                            $balance = $slotSettings->GetBalance() - $winall;
                            $response = 'UPDATE#' . (sprintf('%01.2f', $balance) * $floatBet);
                            break;
                        case 'A/u25':
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', [
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00'
                            ]);
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                            $betsArr = $slotSettings->Bet;
                            $betString = '';
                            $minBets = '';
                            $maxBets = '';
                            for( $b = 0; $b < count($betsArr); $b++ ) 
                            {
                                $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                                $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                            }
                            $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                            $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1] * 1)) . dechex($betsArr[count($betsArr) - 1] * 1));
                            $betsLength = count($betsArr);
                            $betsLength = dechex($betsLength);
                            if( strlen($betsLength) <= 1 ) 
                            {
                                $betsLength = '0' . $betsLength;
                            }
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $slotState = '4';
                            $lastEvent = $slotSettings->GetHistory();
                            if( $lastEvent != 'NULL' ) 
                            {
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $reelSate = $slotSettings->HexFormat($reels->rp[0]) . $slotSettings->HexFormat($reels->rp[1]) . $slotSettings->HexFormat($reels->rp[2]) . $slotSettings->HexFormat($reels->rp[3]) . $slotSettings->HexFormat($reels->rp[4]);
                                $curBet = dechex($lastEvent->serverResponse->slotBet);
                                if( strlen($curBet) <= 1 ) 
                                {
                                    $curBet = '0' . $curBet;
                                }
                                $curLines = dechex($lastEvent->serverResponse->slotLines);
                                if( strlen($curLines) <= 1 ) 
                                {
                                    $curLines = '0' . $curLines;
                                }
                                $slotSettings->SetGameData('AllWaysJokerAMLines', $curLines);
                                $freeMpl = '11';
                                $slotSettings->SetGameData('AllWaysJokerAMBonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData('AllWaysJokerAMCurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData('AllWaysJokerAMTotalWin', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeBalance', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeStartWin', 0);
                                $tFree = dechex($slotSettings->GetGameData('AllWaysJokerAMFreeGames'));
                                $cFree = dechex($slotSettings->GetGameData('AllWaysJokerAMFreeGames') - $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame'));
                                $freeInfo = strlen($tFree) . $tFree . strlen($cFree) . $cFree;
                                $stateWin = $slotSettings->HexFormat($slotSettings->GetGameData('AllWaysJokerAMBonusWin') * $floatBet);
                                if( $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame') < $slotSettings->GetGameData('AllWaysJokerAMFreeGames') && $slotSettings->GetGameData('AllWaysJokerAMFreeGames') > 0 ) 
                                {
                                    $slotState = '6';
                                    if( $slotSettings->GetGameData('' . $slotSettings->slotId . 'CurrentFreeGame') == 0 ) 
                                    {
                                        $slotState = '5';
                                    }
                                }
                            }
                            else
                            {
                                $slotSettings->SetGameData('AllWaysJokerAMLines', $fixedLinesFormated);
                                $slotState = '4';
                                $reelSate = $slotSettings->GetRandomReels();
                                $curBet = '00';
                                $freeMpl = '11';
                                $freeInfo = '1010';
                                $stateWin = '10';
                            }
                            $response = '05' . $slotSettings->FormatReelStrips('') . '5' . $slotSettings->FormatReelStrips('Bonus') . '0' . $slotState . '0' . $reelSate . '10' . $balanceFormated . $stateWin . $curBet . $minBets . $maxBets . '0' . $symCount . $freeInfo . '101010101' . $symCount . '0' . $symCount . '0' . $symCount . '0' . $symCount . '1100' . $reelSate . '0000000000000000' . $betsLength . $betString . 'a10101010101010101010#00101010|0';
                            break;
                        case 'A/u250':
                            $fixedLinesFormated = $slotSettings->GetGameData('AllWaysJokerAMLines');
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $lastEvent = $slotSettings->GetHistory();
                            if( $lastEvent != 'NULL' ) 
                            {
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $reelSate = $slotSettings->HexFormat($reels->rp[0]) . $slotSettings->HexFormat($reels->rp[1]) . $slotSettings->HexFormat($reels->rp[2]) . $slotSettings->HexFormat($reels->rp[3]) . $slotSettings->HexFormat($reels->rp[4]);
                            }
                            else
                            {
                                $reelSate = $slotSettings->GetRandomReels();
                            }
                            $response = '100010' . $balanceFormated . '10' . $reelSate . '00' . '09' . '10101010101010101010100b101010101010101010101014311d0c18190208#101010';
                            break;
                        case 'A/u251':
                            if( $postData['spinType'] == 'regular' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('AllWaysJokerAMBonusWin', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeGames', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMTotalWin', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeBalance', 0);
                                $slotSettings->SetGameData('AllWaysJokerAMFreeStartWin', 0);
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('AllWaysJokerAMCurrentFreeGame', $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $lines = 1;
                            $betLine = $slotSettings->Bet[$tmpPar[2]];
                            $betCnt = $tmpPar[2];
                            $postData['bet'] = $betLine * $lines;
                            if( !isset($postData['slotEvent']) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"slotEvent","serverResponse":"invalid params "}';
                                exit( $response );
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['bet']);
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], 10);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $cWins = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $wild = ['none'];
                                $scatter = '9';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $tmpStringWin = '';
                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                {
                                    $csym = $slotSettings->SymbolGame[$j];
                                    $wsym = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $wildsym = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $cntsym = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $cWin = 0;
                                    $sMpl = 1;
                                    $offsetMpl = 1;
                                    $offsetMpl0 = 1;
                                    if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                    {
                                    }
                                    else
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            for( $s = 0; $s <= 2; $s++ ) 
                                            {
                                                if( $reels['reel' . $r][$s] == $csym || $reels['reel' . $r][$s] == $wild ) 
                                                {
                                                    $wsym[$r - 1] = 1;
                                                    $cntsym[$r - 1]++;
                                                }
                                                if( $reels['reel' . $r][$s] == $wild ) 
                                                {
                                                    $wildsym[$r - 1] = 1;
                                                }
                                            }
                                        }
                                        if( $postData['slotEvent'] == 'freespin' ) 
                                        {
                                            $sc = 0;
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                if( $cntsym[$r - 1] > 0 ) 
                                                {
                                                    $sMpl = $sMpl * $cntsym[$r - 1];
                                                    $sc++;
                                                }
                                            }
                                            $cWin = $slotSettings->Paytable['SYM_' . $csym][$sc] * $betLine * $sMpl;
                                        }
                                        else
                                        {
                                            if( $wsym[0] > 0 && $wsym[1] > 0 ) 
                                            {
                                                $sMpl = 1;
                                                $offsetMpl = 1;
                                                $offsetMpl0 = 1;
                                                for( $r = 1; $r <= 2; $r++ ) 
                                                {
                                                    if( $cntsym[$r - 1] > 0 ) 
                                                    {
                                                        $sMpl = $sMpl * $cntsym[$r - 1];
                                                    }
                                                }
                                                $cWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $sMpl;
                                            }
                                            if( $wsym[0] > 0 && $wsym[1] > 0 && $wsym[2] > 0 ) 
                                            {
                                                $sMpl = 1;
                                                $offsetMpl = 1;
                                                $offsetMpl0 = 1;
                                                for( $r = 1; $r <= 3; $r++ ) 
                                                {
                                                    if( $cntsym[$r - 1] > 0 ) 
                                                    {
                                                        $sMpl = $sMpl * $cntsym[$r - 1];
                                                    }
                                                }
                                                $tWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $sMpl;
                                                if( $cWin < $tWin ) 
                                                {
                                                    $cWin = $tWin;
                                                }
                                            }
                                            if( $wsym[0] > 0 && $wsym[1] > 0 && $wsym[2] > 0 && $wsym[3] > 0 ) 
                                            {
                                                $sMpl = 1;
                                                $offsetMpl = 1;
                                                $offsetMpl0 = 1;
                                                for( $r = 1; $r <= 4; $r++ ) 
                                                {
                                                    if( $cntsym[$r - 1] > 0 ) 
                                                    {
                                                        $sMpl = $sMpl * $cntsym[$r - 1];
                                                    }
                                                }
                                                $tWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $sMpl;
                                                if( $cWin < $tWin ) 
                                                {
                                                    $cWin = $tWin;
                                                }
                                            }
                                            if( $wsym[0] > 0 && $wsym[1] > 0 && $wsym[2] > 0 && $wsym[3] > 0 && $wsym[4] > 0 ) 
                                            {
                                                $sMpl = 1;
                                                $offsetMpl = 1;
                                                $offsetMpl0 = 1;
                                                for( $r = 1; $r <= 5; $r++ ) 
                                                {
                                                    if( $cntsym[$r - 1] > 0 ) 
                                                    {
                                                        $sMpl = $sMpl * $cntsym[$r - 1];
                                                    }
                                                }
                                                $tWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $sMpl;
                                                if( $cWin < $tWin ) 
                                                {
                                                    $cWin = $tWin;
                                                }
                                            }
                                        }
                                        $totalWin += $cWin;
                                        $cWins[$j] = $cWin;
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '{';
                                $scattersCount = 0;
                                $symDouble = 1;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                        if( $reels['reel' . $r][$p] == '10' ) 
                                        {
                                            $symDouble = $symDouble * 2;
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betLine * $lines * $bonusMpl;
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr .= '"scattersType":"win",';
                                }
                                else
                                {
                                    $scattersStr .= '"scattersType":"none",';
                                }
                                $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                                $totalWin += $scattersWin;
                                $totalWin = $totalWin * $symDouble;
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet'] * 10) ) 
                                    {
                                    }
                                    else if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                            }
                            if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('AllWaysJokerAMFreeGames') <= $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame') && $winType != 'bonus' && $slotSettings->GetGameData('AllWaysJokerAMBonusWin') + $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($slotSettings->GetGameData('AllWaysJokerAMBonusWin') + $totalWin);
                            }
                            else if( $postData['slotEvent'] != 'freespin' && $winType != 'bonus' && $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('AllWaysJokerAMBonusWin', $slotSettings->GetGameData('AllWaysJokerAMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('AllWaysJokerAMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('AllWaysJokerAMTotalWin', $totalWin);
                            }
                            $gameState = '03';
                            if( $scattersCount >= 3 ) 
                            {
                                $gameState = '05';
                                $bonusMpl = $slotSettings->slotFreeMpl;
                                $scattersWin = $scattersWin * $bonusMpl;
                                if( $slotSettings->GetGameData('AllWaysJokerAMFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('AllWaysJokerAMFreeGames', $slotSettings->GetGameData('AllWaysJokerAMFreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('AllWaysJokerAMFreeStartWin', $totalWin);
                                    $slotSettings->SetGameData('AllWaysJokerAMBonusWin', $totalWin);
                                    $slotSettings->SetGameData('AllWaysJokerAMFreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                }
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"symDouble":' . $symDouble . ',"slotLines":' . $lines . ',"slotBet":' . $betCnt . ',"totalFreeGames":' . $slotSettings->GetGameData('AllWaysJokerAMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('AllWaysJokerAMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('AllWaysJokerAMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response_log, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $playerId_ = $slotSettings->HexFormat(0);
                            $reelSate = $slotSettings->HexFormat($reels['rp'][0]) . $slotSettings->HexFormat($reels['rp'][1]) . $slotSettings->HexFormat($reels['rp'][2]) . $slotSettings->HexFormat($reels['rp'][3]) . $slotSettings->HexFormat($reels['rp'][4]);
                            $winLinesFormated = '';
                            for( $i = 0; $i < count($slotSettings->SymbolGame); $i++ ) 
                            {
                                $cWins[$i] = $cWins[$i] / $betLine / $bonusMpl * $symDouble;
                                $winLinesFormated .= $slotSettings->HexFormat(round(round($cWins[$i], 2)));
                            }
                            $winLinesFormated .= $slotSettings->HexFormat($scattersWin / $betLine / $lines / $bonusMpl * $symDouble);
                            $ln_h = dechex(count($slotSettings->SymbolGame));
                            if( strlen($ln_h) <= 1 ) 
                            {
                                $ln_h = '0' . $ln_h;
                            }
                            $bet_h = dechex($betCnt);
                            if( strlen($bet_h) <= 1 ) 
                            {
                                $bet_h = '0' . $bet_h;
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $tFree = dechex($slotSettings->GetGameData('AllWaysJokerAMFreeGames'));
                                $cFree = dechex($slotSettings->GetGameData('AllWaysJokerAMFreeGames') - $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame'));
                                $gameState = '06';
                                if( $slotSettings->GetGameData('AllWaysJokerAMFreeGames') <= $slotSettings->GetGameData('AllWaysJokerAMCurrentFreeGame') ) 
                                {
                                    $gameState = '0c';
                                }
                                if( $scattersCount >= 3 ) 
                                {
                                    $gameState = '0a';
                                }
                                $freeInfo = strlen($tFree) . $tFree . strlen($cFree) . $cFree;
                                $freeWinState = '10';
                                if( $totalWin > 0 ) 
                                {
                                    $freeWinState = '19';
                                }
                                $totalWin = $slotSettings->GetGameData('AllWaysJokerAMBonusWin');
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            else
                            {
                                $tFree = dechex($slotSettings->GetGameData('AllWaysJokerAMFreeGames'));
                                $freeWinState = '10';
                                $freeInfo = strlen($tFree) . $tFree . strlen($tFree) . $tFree;
                            }
                            $response = '1' . $gameState . '010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . $reelSate . $bet_h . $ln_h . $freeInfo . $freeWinState . $slotSettings->HexFormat($bonusMpl) . '1010' . $reelSate . '0' . $symCountAll . $winLinesFormated . implode('', $slotSettings->GetGameData($slotSettings->slotId . 'Cards')) . '#' . $scattersCount;
                            $response .= ('_' . json_encode($reels));
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleAnswer', $reelSate . $bet_h . $ln_h . $freeInfo . $freeWinState . $slotSettings->HexFormat($bonusMpl) . '1010' . $reelSate . '0' . $symCountAll . $winLinesFormated);
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleBalance', $balanceFormated);
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleWin', $totalWin);
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('' . $slotSettings->slotId . 'DoubleWin', $slotSettings->GetGameData('' . $slotSettings->slotId . 'BonusWin'));
                            }
                            else
                            {
                                $slotSettings->SetGameData('' . $slotSettings->slotId . 'DoubleWin', $totalWin);
                            }
                            $response_collect0 = $reelSate . $bet_h . $ln_h . '1010101010101010101010' . '0' . $symCount . $winLinesFormated . implode('', $slotSettings->GetGameData($slotSettings->slotId . 'Cards')) . '#101010';
                            $slotSettings->SetGameData('AllWaysJokerAMCollectP0', $response_collect0);
                            $gameState = '04';
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response_collect = '1' . $gameState . '010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . $reelSate . $bet_h . $ln_h . '1010101010101010101010' . '0' . $symCountAll . $winLinesFormated . implode('', $slotSettings->GetGameData($slotSettings->slotId . 'Cards')) . '#101010';
                            $slotSettings->SetGameData('AllWaysJokerAMCollect', $response_collect);
                            break;
                        case 'A/u254':
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $response = $slotSettings->GetGameData('AllWaysJokerAMCollect');
                            break;
                        case 'A/u257':
                            $doubleWin = rand(1, 2);
                            $winall = $slotSettings->GetGameData('AllWaysJokerAMDoubleWin');
                            $dbet = $winall;
                            $daction = $tmpPar[1];
                            if( $slotSettings->MaxWin < ($winall * $slotSettings->CurrentDenom) ) 
                            {
                                $doubleWin = 0;
                            }
                            if( $winall > 0 ) 
                            {
                                $slotSettings->SetBalance(-1 * $winall);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $winall);
                            }
                            $ucard = '';
                            $casbank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            if( $daction <= 2 ) 
                            {
                                if( $casbank < ($winall * 2) ) 
                                {
                                    $doubleWin = 0;
                                }
                            }
                            else if( $casbank < ($winall * 4) ) 
                            {
                                $doubleWin = 0;
                            }
                            $reds = [
                                0, 
                                1, 
                                4, 
                                5, 
                                8, 
                                9, 
                                12, 
                                13, 
                                16, 
                                17, 
                                20, 
                                21, 
                                24, 
                                25, 
                                28, 
                                29, 
                                32, 
                                33, 
                                36, 
                                37, 
                                40, 
                                41, 
                                44, 
                                45, 
                                48, 
                                49, 
                                52
                            ];
                            $blacks = [
                                2, 
                                3, 
                                6, 
                                7, 
                                10, 
                                11, 
                                14, 
                                15, 
                                18, 
                                19, 
                                22, 
                                23, 
                                26, 
                                27, 
                                30, 
                                31, 
                                34, 
                                35, 
                                38, 
                                39, 
                                42, 
                                43, 
                                46, 
                                47, 
                                50, 
                                51
                            ];
                            $suit3 = [
                                0, 
                                4, 
                                8, 
                                12, 
                                16, 
                                20, 
                                24, 
                                28, 
                                32, 
                                36, 
                                40, 
                                44, 
                                48, 
                                52
                            ];
                            $suit4 = [
                                1, 
                                5, 
                                9, 
                                13, 
                                17, 
                                21, 
                                25, 
                                29, 
                                33, 
                                37, 
                                41, 
                                45, 
                                49, 
                                53
                            ];
                            $suit5 = [
                                2, 
                                6, 
                                10, 
                                14, 
                                18, 
                                22, 
                                26, 
                                30, 
                                34, 
                                38, 
                                42, 
                                46, 
                                50
                            ];
                            $suit6 = [
                                3, 
                                7, 
                                11, 
                                15, 
                                19, 
                                23, 
                                27, 
                                31, 
                                35, 
                                39, 
                                43, 
                                47, 
                                51
                            ];
                            if( $daction <= 2 ) 
                            {
                                $winall = $winall * 2;
                            }
                            else
                            {
                                $winall = $winall * 4;
                            }
                            if( $doubleWin == 1 ) 
                            {
                                if( $daction == 1 ) 
                                {
                                    $ucard = $reds[rand(0, 26)];
                                }
                                if( $daction == 2 ) 
                                {
                                    $ucard = $blacks[rand(0, 25)];
                                }
                                if( $daction == 3 ) 
                                {
                                    $ucard = $suit3[rand(0, 12)];
                                }
                                if( $daction == 4 ) 
                                {
                                    $ucard = $suit4[rand(0, 12)];
                                }
                                if( $daction == 5 ) 
                                {
                                    $ucard = $suit5[rand(0, 12)];
                                }
                                if( $daction == 6 ) 
                                {
                                    $ucard = $suit6[rand(0, 12)];
                                }
                            }
                            else
                            {
                                if( $daction == 1 ) 
                                {
                                    $ucard = $blacks[rand(0, 25)];
                                }
                                if( $daction == 2 ) 
                                {
                                    $ucard = $reds[rand(0, 26)];
                                }
                                if( $daction == 3 ) 
                                {
                                    $rnds = [
                                        4, 
                                        5, 
                                        6
                                    ];
                                    $ucard = ${'suit' . $rnds[rand(0, 2)]}[rand(0, 12)];
                                }
                                if( $daction == 4 ) 
                                {
                                    $rnds = [
                                        3, 
                                        5, 
                                        6
                                    ];
                                    $ucard = ${'suit' . $rnds[rand(0, 2)]}[rand(0, 12)];
                                }
                                if( $daction == 5 ) 
                                {
                                    $rnds = [
                                        4, 
                                        3, 
                                        6
                                    ];
                                    $ucard = ${'suit' . $rnds[rand(0, 2)]}[rand(0, 12)];
                                }
                                if( $daction == 6 ) 
                                {
                                    $rnds = [
                                        4, 
                                        5, 
                                        3
                                    ];
                                    $ucard = ${'suit' . $rnds[rand(0, 2)]}[rand(0, 12)];
                                }
                                $winall = 0;
                            }
                            $winall = sprintf('%01.2f', $winall) * $floatBet;
                            $winall_h1 = str_replace('.', '', $winall . '');
                            $winall_h = dechex($winall_h1);
                            $ucard = dechex($ucard);
                            if( strlen($ucard) <= 1 ) 
                            {
                                $ucard = '0' . $ucard;
                            }
                            $doubleCards = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            array_pop($doubleCards);
                            array_unshift($doubleCards, $ucard);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $doubleCards);
                            $winall = $winall / 100;
                            if( $winall > 0 ) 
                            {
                                $slotSettings->SetBalance($winall);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $winall);
                            }
                            $response = '107010' . $slotSettings->GetGameData('AllWaysJokerAMDoubleBalance') . strlen($winall_h) . $winall_h . $slotSettings->GetGameData('AllWaysJokerAMDoubleAnswer') . implode('', $slotSettings->GetGameData($slotSettings->slotId . 'Cards'));
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleWin', $winall);
                            $slotSettings->SetGameData('AllWaysJokerAMTotalWin', $winall);
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response_collect = '104010' . $balanceFormated . strlen($winall_h) . $winall_h . $slotSettings->GetGameData('AllWaysJokerAMCollectP0');
                            $slotSettings->SetGameData('AllWaysJokerAMCollect', $response_collect);
                            $response_log = '{"responseEvent":"gambleResult","serverResponse":{"totalWin":' . $winall . '}}';
                            if( $winall <= 0 ) 
                            {
                                $winall = -1 * $dbet;
                            }
                            $slotSettings->SaveLogReport($response_log, $dbet, 1, $winall, 'slotGamble');
                            break;
                        case 'A/u258':
                            $winall = $slotSettings->GetGameData('AllWaysJokerAMDoubleWin');
                            if( $winall > 0.01 ) 
                            {
                                $winall22 = sprintf('%01.2f', $winall / 2);
                            }
                            else
                            {
                                $winall22 = 0;
                            }
                            $winall = $winall - $winall22;
                            $user_balance = $slotSettings->GetBalance() - $winall;
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleWin', $winall);
                            $slotSettings->SetGameData('AllWaysJokerAMTotalWin', $winall);
                            $user_balance = sprintf('%01.2f', $user_balance);
                            $str_balance = str_replace('.', '', $user_balance . '');
                            $hexBalance = dechex($str_balance - 0);
                            $rtnBalance = strlen($hexBalance) . $hexBalance;
                            $slotSettings->SetGameData('AllWaysJokerAMDoubleBalance', $rtnBalance);
                            $winall = sprintf('%01.2f', $winall) * $floatBet;
                            $winall_h1 = str_replace('.', '', $winall . '');
                            $winall_h = dechex($winall_h1);
                            $doubleCards = '26280b2714161d0c';
                            $response = '108010' . $slotSettings->GetGameData('AllWaysJokerAMDoubleBalance') . strlen($winall_h) . $winall_h . $slotSettings->GetGameData('AllWaysJokerAMDoubleAnswer') . implode('', $slotSettings->GetGameData($slotSettings->slotId . 'Cards'));
                            break;
                    }
                    $slotSettings->SaveGameData();
                    $slotSettings->SaveGameDataStatic();
                    echo $response;
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
