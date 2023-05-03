<?php 
namespace VanguardLTE\Games\TheGoldenCityISB
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
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
                    $postData = $_POST;
                    $result_tmp = [];
                    $aid = '';
                    if( !isset($postData['cmd']) ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid request"}';
                        exit( $response );
                    }
                    $postData['command'] = explode("\n", $postData['cmd']);
                    $postData['command'] = trim($postData['command'][0]);
                    $aid = (string)$postData['command'];
                    switch( $aid ) 
                    {
                        case '1':
                            $result_tmp[0] = '{ "rootdata": { "uid": "undefined" , "data": { "logout": "1" } } }';
                            break;
                        case '5814':
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $slotSettings->SetGameData($slotSettings->slotId . 'totalBetMultiplier', $lastEvent->serverResponse->totalBetMultiplier);
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $lines = $lastEvent->serverResponse->slotLines;
                                $bet = $lastEvent->serverResponse->slotBet * 100;
                            }
                            else
                            {
                                $lines = 10;
                                $bet = $slotSettings->Bet[0] * 100;
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $totalBetMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'totalBetMultiplier');
                                $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeSpinsStr = ' "gameSpecific": { "totalBetMultiplier": "3" }, "freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "1", "totalMultiplier": "1" },';
                            }
                            else
                            {
                                $totalBetMultiplier = 0;
                                $freeSpinsStr = '';
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "5640f7f3-4693-4059-841a-dc3afd3f1925", "data": { "version": { "versionServer": "2.2.0.1-1", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520020301588680413177316196800814753252699" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "1", "coinValue": "' . $gameBets[0] . '", "lines": "20", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 20) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": { }, "bonusRequest": { }, "gameSpecific": { "totalBetMultiplier": "' . $totalBetMultiplier . '" } } } } } } }';
                            break;
                        case '192837':
                            $result_tmp[0] = '{ "rootdata": { "uid": "419427a2-d300-41d9-8d67-fe4eec61be5f", "data": "1" } }';
                            break;
                        case '5347':
                            $result_tmp[0] = '{ "rootdata": { "uid": "26d1001c-15ff-4d21-af9e-45dde0260165", "data": { "success": "" } } }';
                            break;
                        case '8':
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurStep', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurPos', 2);
                            $slotSettings->SetGameData($slotSettings->slotId . 'totalBetMultiplier', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'collectedCashWinUnlocks', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentCollectValue', 0);
                            $result_tmp[0] = '{ "rootdata": { "uid": "3fb14ec3-da73-4f7e-8455-04f49b0c2b9a", "data": { "bonusChoice": { "bonusId": "1", "choicesOrder": "0", "bonusGain": "0", "bonusGainMoney": "0", "choicesWinnings": "0", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesMult": "1", "totalMultiplier": "1", "choicesFreeGames": "0" } } } }';
                            break;
                        case '9':
                            $boardWins = [
                                'myst', 
                                'm2', 
                                'start', 
                                's3', 
                                'k', 
                                'myst', 
                                's2', 
                                'k', 
                                's1', 
                                'm3', 
                                'myst', 
                                's2', 
                                'm2', 
                                's1', 
                                'start', 
                                'myst', 
                                's3', 
                                'k', 
                                'm3', 
                                's2'
                            ];
                            $FreeGames = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            $CurStep = $slotSettings->GetGameData($slotSettings->slotId . 'CurStep');
                            $CurPos = $slotSettings->GetGameData($slotSettings->slotId . 'CurPos');
                            $totalBetMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'totalBetMultiplier');
                            $collectedCashWinUnlocks = $slotSettings->GetGameData($slotSettings->slotId . 'collectedCashWinUnlocks');
                            $bonusRequest = 1;
                            $dice = rand(1, 4);
                            $currentCollectType = '';
                            $currentCollectValue = 0;
                            $randomStr = '';
                            $NextPos = $CurPos + $dice;
                            if( $NextPos > 19 ) 
                            {
                                $NextPos -= 20;
                            }
                            $curWin = $boardWins[$NextPos];
                            if( $curWin == 's1' ) 
                            {
                                $FreeGames += 1;
                                $currentCollectValue = 1;
                                $currentCollectType = 'freespins';
                            }
                            if( $curWin == 's2' ) 
                            {
                                $FreeGames += 2;
                                $currentCollectValue = 2;
                                $currentCollectType = 'freespins';
                            }
                            if( $curWin == 's3' ) 
                            {
                                $FreeGames += 3;
                                $currentCollectValue = 3;
                                $currentCollectType = 'freespins';
                            }
                            if( $curWin == 'myst' ) 
                            {
                                $FreeGames += 1;
                                $randomStr = ',"randomWinType": "freespins", "randomWinValue": "1"';
                            }
                            if( $curWin == 'k' ) 
                            {
                                $collectedCashWinUnlocks++;
                                $currentCollectValue = 1;
                                $currentCollectType = 'cash_win_unlock';
                            }
                            if( $curWin == 'm1' ) 
                            {
                                $totalBetMultiplier += 1;
                                $currentCollectValue = 1;
                                $currentCollectType = 'total_bet_multiplier';
                            }
                            if( $curWin == 'm2' ) 
                            {
                                $totalBetMultiplier += 2;
                                $currentCollectValue = 2;
                                $currentCollectType = 'total_bet_multiplier';
                            }
                            if( $curWin == 'm3' ) 
                            {
                                $totalBetMultiplier += 3;
                                $currentCollectValue = 3;
                                $currentCollectType = 'total_bet_multiplier';
                            }
                            $endStr = '';
                            if( $CurStep > 4 || $curWin == 'start' ) 
                            {
                                $bonusRequest = 0;
                                $endStr = ', "choicesOrder": "1", "freeGamesWin": { "total": "' . $FreeGames . '", "multiplier": "1" }';
                                $currentCollectType = 'start_freespins';
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $result_tmp[0] = '{ "rootdata": { "uid": "3fb14ec3-da73-4f7e-8455-04f49b0c2b9a", "data": { "bonusWin": { "money": "' . $balanceInCents . '", "bonusRequest": "' . $bonusRequest . '", "bonusId": "1", "bonusesLeft": "' . $bonusRequest . '", "totalFreeGames": "0", "multiplier": "1", "totalMultiplier": "1", "choicesFreeGames": "0", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesOrder": "0", "choicesWinnings": "0", "choicesMult": "1", "totalWinnings": "0", "totalWinningsMoney": "0" ' . $endStr . ' }, "gameSpecific": { "move": "' . $CurStep . '", "advance": "' . $dice . '", "lastPosition": "' . $CurPos . '", "currentPosition": "' . $NextPos . '", "currentCollectType": "' . $currentCollectType . '", "currentCollectValue": "' . $currentCollectValue . '" ' . $randomStr . ', "collectedTotalBetMultiplier": "' . $totalBetMultiplier . '", "collectedFreespins": "' . $FreeGames . '", "collectedCashWinUnlocks": "' . $collectedCashWinUnlocks . '" }, "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0" } } } }';
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $FreeGames);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurStep', $CurStep);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurPos', $NextPos);
                            $slotSettings->SetGameData($slotSettings->slotId . 'totalBetMultiplier', $totalBetMultiplier);
                            $slotSettings->SetGameData($slotSettings->slotId . 'collectedCashWinUnlocks', $collectedCashWinUnlocks);
                            break;
                        case '2':
                            $gDat = explode("\n", $postData['cmd']);
                            $linesId = [];
                            $linesId[0] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[2] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[4] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[5] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[6] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[7] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[13] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[17] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $totalBetMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'totalBetMultiplier');
                            $lines = 20;
                            $betline = trim($gDat[1]) / 100;
                            $betlineRaw = trim($gDat[1]);
                            $allbet = $betline * $lines;
                            $postData['slotEvent'] = 'bet';
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                            }
                            if( $slotSettings->GetBalance() < $allbet && $postData['slotEvent'] == 'bet' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance "}';
                                exit( $response );
                            }
                            if( $allbet <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet "}';
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
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
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $winType == 'bonus' && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $winType = 'none';
                            }
                            $balanceInCentsStart = round($slotSettings->GetBalance() * 100);
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
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
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
                                $wild = ['9'];
                                $scatter = '10';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    if( $reels['reel' . $r][2] == 9 ) 
                                    {
                                        $reels['reel' . $r][0] = '9';
                                        $reels['reel' . $r][1] = '9';
                                        $reels['reel' . $r][2] = '9';
                                    }
                                }
                                $spins = [];
                                for( $rs = 0; $rs <= 50; $rs++ ) 
                                {
                                    if( $rs == 0 ) 
                                    {
                                        $newReels = $reels;
                                        $curSpin = $slotSettings->GetReelsWin($reels, 20, $betline, $betlineRaw, $linesId, $cWins, $rs);
                                    }
                                    else
                                    {
                                        $newReels = $slotSettings->OffsetReels($curSpin['reelsOffset']);
                                        $curSpin = $slotSettings->GetReelsWin($newReels, 20, $betline, $betlineRaw, $linesId, $cWins, $rs);
                                    }
                                    if( $curSpin['totalWin'] <= 0 ) 
                                    {
                                        $reels = $slotSettings->OffsetReels($curSpin['reelsOffset']);
                                        break;
                                    }
                                    $curReels = '';
                                    $curReels .= ('"' . $newReels['reel1'][0] . '-' . $newReels['reel2'][0] . '-' . $newReels['reel3'][0] . '-' . $newReels['reel4'][0] . '-' . $newReels['reel5'][0] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][1] . '-' . $newReels['reel2'][1] . '-' . $newReels['reel3'][1] . '-' . $newReels['reel4'][1] . '-' . $newReels['reel5'][1] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][2] . '-' . $newReels['reel2'][2] . '-' . $newReels['reel3'][2] . '-' . $newReels['reel4'][2] . '-' . $newReels['reel5'][2] . '"');
                                    $spins[] = '{ "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { "line":[ ' . implode(',', $curSpin['lineWins']) . ' ]}, "combo": "' . $rs . '", "winnings": "' . (($curSpin['totalWin'] * 100) / $betlineRaw) . '", "winningsMoney": "' . ($curSpin['totalWin'] * 100) . '", "money": "' . $balanceInCentsStart . '" }';
                                    $totalWin += $curSpin['totalWin'];
                                }
                                $spinsStr = '';
                                if( $rs > 0 ) 
                                {
                                    $spinsStr = ',"spins": { "spin": [' . implode(',', $spins) . '] }';
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scPos = [];
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '1';
                                        }
                                        else
                                        {
                                            $scPos[] = '0';
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet;
                                $sgwin = 0;
                                if( $scattersCount >= 3 ) 
                                {
                                    $sgwin = $slotSettings->slotFreeCount;
                                    $scattersStr = ', "bonusRequest": "1", "bonuses": { "bonus": { "id": "1", "hl": "' . implode('', $scPos) . '" } }';
                                }
                                if( $scattersCount >= 2 && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $sgwin = $slotSettings->slotFreeCount;
                                    $scattersStr = ', "scatters": { "value": "' . (($totalBetMultiplier * 100 * $scattersCount) / $betlineRaw) . '", "valueMoney": "' . ($totalBetMultiplier * $scattersCount * 100) . '", "multiplier": "' . $totalBetMultiplier . '", "hl": "' . implode('', $scPos) . '" }';
                                }
                                $totalWin += $scattersWin;
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
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
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"totalBetMultiplier":' . $totalBetMultiplier . ',"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $isJack = 'false';
                            if( $totalWin > 0 ) 
                            {
                                $state = 'gamble';
                            }
                            else
                            {
                                $state = 'idle';
                            }
                            if( !isset($sgwin) ) 
                            {
                                $fs = 0;
                            }
                            else if( $sgwin > 0 ) 
                            {
                                $fs = $sgwin;
                            }
                            else
                            {
                                $fs = 0;
                            }
                            $balanceInCentsEnd = round($slotSettings->GetBalance() * 100);
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "3fb14ec3-da73-4f7e-8455-04f49b0c2b9a", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "255841201589646528829739405937720334470878" }, "coinValues": { "coinValueList": "1,2,5,10,20,30,50,100", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }' . $scattersStr . ', "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": "0", "gameSpecific": { "totalBetMultiplier": "0" } }' . $spinsStr . ' , "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "acf3f2cd-4e9a-4d9b-8073-5e71a535ec79", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "255841201589630792275407805802349536997145" }, "coinValues": { "coinValueList": "1,2,5,10,20,30,50,100", "coinValueDefault": "0", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [' . $curReels . ' ] }, "lines": { }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": "0" ' . $scattersStr . ' }' . $spinsStr . ' , "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                                $slotSettings->SetGameData($slotSettings->slotId . 'LastResponse', $result_tmp[0]);
                            }
                            break;
                    }
                    $response = $result_tmp[0];
                    $slotSettings->SaveGameData();
                    $slotSettings->SaveGameDataStatic();
                    echo $response;
                }
                catch( \Exception $e ) 
                {
                    $slotSettings->InternalErrorSilent($e);
                }
            }, 5);
        }
    }

}
