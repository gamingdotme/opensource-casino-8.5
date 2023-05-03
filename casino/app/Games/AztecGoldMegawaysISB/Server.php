<?php 
namespace VanguardLTE\Games\AztecGoldMegawaysISB
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
                                $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeSpinsStr = '"freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "1", "totalMultiplier": "1" },';
                            }
                            else
                            {
                                $freeSpinsStr = '';
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "5640f7f3-4693-4059-841a-dc3afd3f1925", "data": { "version": { "versionServer": "2.2.0.1-1", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520020301588680413177316196800814753252699" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "20", "coinValue": "' . $gameBets[0] . '", "lines": "20", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 20) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": { }, "bonusRequest": { } } } } } } }';
                            break;
                        case '192837':
                            $result_tmp[0] = '{ "rootdata": { "uid": "419427a2-d300-41d9-8d67-fe4eec61be5f", "data": "1" } }';
                            break;
                        case '5347':
                            $result_tmp[0] = '{ "rootdata": { "uid": "26d1001c-15ff-4d21-af9e-45dde0260165", "data": { "success": "" } } }';
                            break;
                        case '105':
                            $responseStruct = json_decode($slotSettings->GetGameData($slotSettings->slotId . 'LastResponse'), true);
                            $responseStruct['rootdata']['data']['endGame']['gameSpecific']['dummyModifierActive'] = '0';
                            $result_tmp[0] = json_encode($responseStruct);
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
                                2, 
                                2, 
                                2, 
                                1
                            ];
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
                                $wild = '11';
                                $scatter = '12';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $ways = $reels['ways'];
                                $waysCount = 0;
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
                                    $curReels .= ('"' . $newReels['reel1'][0] . '-' . $newReels['reel2'][0] . '-' . $newReels['reel3'][0] . '-' . $newReels['reel4'][0] . '-' . $newReels['reel5'][0] . '-' . $newReels['reel6'][0] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][1] . '-' . $newReels['reel2'][1] . '-' . $newReels['reel3'][1] . '-' . $newReels['reel4'][1] . '-' . $newReels['reel5'][1] . '-' . $newReels['reel6'][1] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][2] . '-' . $newReels['reel2'][2] . '-' . $newReels['reel3'][2] . '-' . $newReels['reel4'][2] . '-' . $newReels['reel5'][2] . '-' . $newReels['reel6'][2] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][3] . '-' . $newReels['reel2'][3] . '-' . $newReels['reel3'][3] . '-' . $newReels['reel4'][3] . '-' . $newReels['reel5'][3] . '-' . $newReels['reel6'][3] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][4] . '-' . $newReels['reel2'][4] . '-' . $newReels['reel3'][4] . '-' . $newReels['reel4'][4] . '-' . $newReels['reel5'][4] . '-' . $newReels['reel6'][4] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][5] . '-' . $newReels['reel2'][5] . '-' . $newReels['reel3'][5] . '-' . $newReels['reel4'][5] . '-' . $newReels['reel5'][5] . '-' . $newReels['reel6'][5] . '"');
                                    $curReels .= (',"' . $newReels['reel1'][6] . '-' . $newReels['reel2'][6] . '-' . $newReels['reel3'][6] . '-' . $newReels['reel4'][6] . '-' . $newReels['reel5'][6] . '-' . $newReels['reel6'][6] . '"');
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
                                $allScattersWin = 0;
                                $allScattersWinTempl = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    8, 
                                    8, 
                                    8, 
                                    8, 
                                    10, 
                                    10, 
                                    10, 
                                    15, 
                                    15, 
                                    15, 
                                    20, 
                                    20, 
                                    20, 
                                    25, 
                                    25, 
                                    25, 
                                    30, 
                                    100, 
                                    1000
                                ];
                                $allScattersWinArr = [];
                                $scatterValues = [];
                                $bonusGameNewScatters = [];
                                $bonusGameValuesCoins = [];
                                $bonusGameValuesMoney = [];
                                $bonusGameInitialSymbols = [];
                                $bonusGameInitialScatterValues = [];
                                $bonusOnlyGain = 0;
                                $bonusGameGain = 0;
                                $bonusGameGainMoney = 0;
                                shuffle($allScattersWinTempl);
                                for( $p = 0; $p <= 6; $p++ ) 
                                {
                                    for( $r = 1; $r <= 6; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '1';
                                            $curScWin = array_shift($allScattersWinTempl);
                                            $allScattersWinArr[] = $curScWin;
                                            $bonusGameValuesCoins[] = $curScWin * $allbet * 100;
                                            $bonusGameValuesMoney[] = ($curScWin * $allbet * 100) / $betlineRaw;
                                            $allScattersWin += ($curScWin * $allbet);
                                        }
                                        else
                                        {
                                            $bonusGameValuesCoins[] = 0;
                                            $bonusGameValuesMoney[] = 0;
                                            $allScattersWinArr[] = 0;
                                            $scPos[] = '0';
                                        }
                                        if( $reels['reel' . $r][$p] > 0 ) 
                                        {
                                            $bonusGameInitialSymbols[] = $reels['reel' . $r][$p];
                                        }
                                        else
                                        {
                                            $bonusGameInitialSymbols[] = 0;
                                        }
                                    }
                                }
                                $sgwin = 0;
                                if( $scattersCount >= 5 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameInitialSymbols', $bonusGameInitialSymbols);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'allScattersWinArr', $allScattersWinArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameInitialScatterValues', $allScattersWinArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'allScattersWin', $allScattersWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'betlineRaw', $betlineRaw);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameSpinsLeft', 3);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameSpinsUsed', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'reels', $reels);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'ways', $ways);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameValuesCoins', $bonusGameValuesCoins);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameValuesMoney', $bonusGameValuesMoney);
                                    $scattersStr = ', "gameSpecific": { "scatterValues": "' . implode('-', $allScattersWinArr) . '", "bonusGameInProgress": "1", "bonusGameSpinsLeft": "3", "bonusGameSpinsUsed": "0", "bonusOnlyGain": "' . (($allScattersWin * 100) / $betlineRaw) . '", "bonusGameGain": "' . (($allScattersWin * 100) / $betlineRaw) . '", "bonusGameGainMoney": "' . ($allScattersWin * 100) . '", "bonusGameNewScatters": "' . implode('', $scPos) . '", "bonusGameMultipliers": "1-1-1-1-1-1", "bonusGameValuesCoins": "' . implode('-', $bonusGameValuesCoins) . '", "bonusGameValuesMoney": "' . implode('-', $bonusGameValuesMoney) . '", "bonusGameInitialSymbols": "' . implode('-', $bonusGameInitialSymbols) . '", "bonusGameInitialScatterValues": "' . implode('-', $allScattersWinArr) . '", "isBonusAddModifier": "1", "megaways": "' . $ways . '" }';
                                }
                                else
                                {
                                    $allScattersWin = 0;
                                }
                                if( $scattersCount >= 5 && $winType == 'bonus' && $totalWin > 0 ) 
                                {
                                }
                                else
                                {
                                    $totalWin += ($scattersWin + $allScattersWin);
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
                                        else if( $scattersCount >= 5 && $winType != 'bonus' ) 
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
                            if( $scattersCount >= 5 ) 
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
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '-' . $reels['reel6'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '-' . $reels['reel6'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '-' . $reels['reel6'][2] . '"');
                            $curReels .= (',"' . $reels['reel1'][3] . '-' . $reels['reel2'][3] . '-' . $reels['reel3'][3] . '-' . $reels['reel4'][3] . '-' . $reels['reel5'][3] . '-' . $reels['reel6'][3] . '"');
                            $curReels .= (',"' . $reels['reel1'][4] . '-' . $reels['reel2'][4] . '-' . $reels['reel3'][4] . '-' . $reels['reel4'][4] . '-' . $reels['reel5'][4] . '-' . $reels['reel6'][4] . '"');
                            $curReels .= (',"' . $reels['reel1'][5] . '-' . $reels['reel2'][5] . '-' . $reels['reel3'][5] . '-' . $reels['reel4'][5] . '-' . $reels['reel5'][5] . '-' . $reels['reel6'][5] . '"');
                            $curReels .= (',"' . $reels['reel1'][6] . '-' . $reels['reel2'][6] . '-' . $reels['reel3'][6] . '-' . $reels['reel4'][6] . '-' . $reels['reel5'][6] . '-' . $reels['reel6'][6] . '"');
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
                            $balanceInCentsEnd = round(($slotSettings->GetBalance() - $allScattersWin) * 100);
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            $totalWin -= $allScattersWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": { }, "insync": { "sync": { "from": "0-0-0-0-0", "to": "' . implode('-', $syncReelArr) . '", "mask": "' . implode('-', $syncReelArr) . '" } } }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "e505a70b-d71b-4cb6-a47f-79d607387412", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "255899901589710939772397155069424252367048" }, "coinValues": { "coinValueList": "1,2,5,10,20,30,50,100", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": { }, "bonusRequest": { }, "gameSpecific": { "scatterValues": "' . implode('-', $allScattersWinArr) . '", "megaways": "' . $ways . '" }' . $scattersStr . ' }' . $spinsStr . ' , "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            break;
                        case '104':
                            $bank = $slotSettings->GetBank('bonus');
                            for( $l = 0; $l < 2000; $l++ ) 
                            {
                                $bonusGameInitialSymbols = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameInitialSymbols');
                                $allScattersWinArr = $slotSettings->GetGameData($slotSettings->slotId . 'allScattersWinArr');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $bonusGameSpinsLeft = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameSpinsLeft');
                                $bonusGameSpinsUsed = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameSpinsUsed');
                                $oldWin = $slotSettings->GetGameData($slotSettings->slotId . 'allScattersWin');
                                $reels = $slotSettings->GetGameData($slotSettings->slotId . 'reels');
                                $ways = $slotSettings->GetGameData($slotSettings->slotId . 'ways');
                                $betlineRaw = $slotSettings->GetGameData($slotSettings->slotId . 'betlineRaw');
                                $bonusGameInitialScatterValues = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameInitialScatterValues');
                                $bonusGameValuesCoins = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameValuesCoins');
                                $bonusGameValuesMoney = $slotSettings->GetGameData($slotSettings->slotId . 'bonusGameValuesMoney');
                                $reelStrips = [];
                                $reelStrips[0] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelStrips[1] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelStrips[2] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelStrips[3] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelStrips[4] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelStrips[5] = [
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    12, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13, 
                                    13
                                ];
                                $reelsNew = $reels;
                                for( $r = 1; $r <= 6; $r++ ) 
                                {
                                    shuffle($reelStrips[$r - 1]);
                                    for( $p = 0; $p <= 6; $p++ ) 
                                    {
                                        if( $reelsNew['reel' . $r][$p] > 0 && $reelsNew['reel' . $r][$p] != 12 ) 
                                        {
                                            $reelsNew['reel' . $r][$p] = $reelStrips[$r - 1][$p];
                                        }
                                    }
                                }
                                $allScattersWinTempl = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    8, 
                                    8, 
                                    8, 
                                    8, 
                                    10, 
                                    10, 
                                    10, 
                                    15, 
                                    15, 
                                    15, 
                                    20, 
                                    20, 
                                    20, 
                                    25, 
                                    25, 
                                    25, 
                                    30, 
                                    100, 
                                    1000
                                ];
                                $scPos = [];
                                $bonusGameNewScatters = [];
                                $scCnt = 0;
                                $allScattersWin = 0;
                                shuffle($allScattersWinTempl);
                                for( $p = 0; $p <= 6; $p++ ) 
                                {
                                    for( $r = 1; $r <= 6; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] != 12 && $reelsNew['reel' . $r][$p] == 12 ) 
                                        {
                                            $curScWin = array_shift($allScattersWinTempl);
                                            $allScattersWinArr[$scCnt] = $curScWin;
                                            $bonusGameValuesCoins[$scCnt] = ($curScWin * $allbet * 100) / $betlineRaw;
                                            $bonusGameValuesMoney[$scCnt] = $curScWin * $allbet * 100;
                                            $allScattersWin += ($curScWin * $allbet);
                                            $bonusGameNewScatters[] = 1;
                                        }
                                        else
                                        {
                                            $bonusGameNewScatters[] = 0;
                                        }
                                        if( $reelsNew['reel' . $r][$p] == 12 ) 
                                        {
                                            $scPos[$scCnt] = '1';
                                        }
                                        else
                                        {
                                            $scPos[$scCnt] = '0';
                                        }
                                        $scCnt++;
                                    }
                                }
                                if( $l > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"respin","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $allScattersWin <= $bank ) 
                                {
                                    break;
                                }
                            }
                            if( $allScattersWin > 0 ) 
                            {
                                $slotSettings->SetBank('bonus', -1 * $allScattersWin);
                                $slotSettings->SetBalance($allScattersWin);
                            }
                            $bonusGameSpinsLeft--;
                            $bonusGameSpinsUsed++;
                            $curReels = '';
                            $curReels .= ('"' . $reelsNew['reel1'][0] . '-' . $reelsNew['reel2'][0] . '-' . $reelsNew['reel3'][0] . '-' . $reelsNew['reel4'][0] . '-' . $reelsNew['reel5'][0] . '-' . $reelsNew['reel6'][0] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][1] . '-' . $reelsNew['reel2'][1] . '-' . $reelsNew['reel3'][1] . '-' . $reelsNew['reel4'][1] . '-' . $reelsNew['reel5'][1] . '-' . $reelsNew['reel6'][1] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][2] . '-' . $reelsNew['reel2'][2] . '-' . $reelsNew['reel3'][2] . '-' . $reelsNew['reel4'][2] . '-' . $reelsNew['reel5'][2] . '-' . $reelsNew['reel6'][2] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][3] . '-' . $reelsNew['reel2'][3] . '-' . $reelsNew['reel3'][3] . '-' . $reelsNew['reel4'][3] . '-' . $reelsNew['reel5'][3] . '-' . $reelsNew['reel6'][3] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][4] . '-' . $reelsNew['reel2'][4] . '-' . $reelsNew['reel3'][4] . '-' . $reelsNew['reel4'][4] . '-' . $reelsNew['reel5'][4] . '-' . $reelsNew['reel6'][4] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][5] . '-' . $reelsNew['reel2'][5] . '-' . $reelsNew['reel3'][5] . '-' . $reelsNew['reel4'][5] . '-' . $reelsNew['reel5'][5] . '-' . $reelsNew['reel6'][5] . '"');
                            $curReels .= (',"' . $reelsNew['reel1'][6] . '-' . $reelsNew['reel2'][6] . '-' . $reelsNew['reel3'][6] . '-' . $reelsNew['reel4'][6] . '-' . $reelsNew['reel5'][6] . '-' . $reelsNew['reel6'][6] . '"');
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $balanceInCentsStart = round(($slotSettings->GetBalance() - ($allScattersWin + $oldWin)) * 100);
                            $result_tmp[0] = '{ "rootdata": { "uid": "5f45095e-1ab3-4dcb-a66c-8771be2326d5", "data": { "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0" }, "id": { "roundId": "255899901589747526494184306528790444093525" }, "coinValues": { "coinValueList": "1,2,5,10,20,30,50,100", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "' . ($betlineRaw * 20) . '", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "totalMultiplier": { }, "bonusRequest": { }, "gameSpecific": { "scatterValues": "' . implode('-', $allScattersWinArr) . '", "bonusGameInProgress": "1", "bonusGameSpinsLeft": "' . $bonusGameSpinsLeft . '", "bonusGameSpinsUsed": "' . $bonusGameSpinsUsed . '", "bonusOnlyGain": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "bonusGameGain": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "bonusGameGainMoney": "' . (($allScattersWin + $oldWin) * 100) . '", "bonusGameNewScatters": "' . implode('-', $bonusGameNewScatters) . '", "bonusGameMultipliers": "1-1-1-1-1-1", "bonusGameValuesCoins": "' . implode('-', $bonusGameValuesCoins) . '", "bonusGameValuesMoney": "' . implode('-', $bonusGameValuesMoney) . '", "bonusGameInitialSymbols": "' . implode('-', $bonusGameInitialSymbols) . '", "bonusGameInitialScatterValues": "' . implode('-', $bonusGameInitialScatterValues) . '", "megaways": "' . $ways . '" } }, "pendingWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" } } } }';
                            $response = '{"responseEvent":"spin","responseType":"respin","serverResponse":{"slotLines":20,"slotBet":' . ($allbet / 20) . ',"totalFreeGames":' . $bonusGameSpinsLeft . ',"currentFreeGames":' . ($allScattersWin + $oldWin) . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . ($allScattersWin + $oldWin) . ',"totalWin":' . $allScattersWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[],"lastResponse":' . $result_tmp[0] . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, 20, $allScattersWin, 'FG2');
                            if( $bonusGameSpinsLeft <= 0 ) 
                            {
                                $balanceInCents = round($slotSettings->GetBalance() * 100);
                                $balanceInCentsStart = round(($slotSettings->GetBalance() - ($allScattersWin + $oldWin)) * 100);
                                $gameBets = [];
                                for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                                {
                                    $gameBets[] = $slotSettings->Bet[$i] * 100;
                                }
                                $result_tmp[0] = '{ "rootdata": { "uid": "5f45095e-1ab3-4dcb-a66c-8771be2326d5", "data": { "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0" }, "id": { "roundId": "255899901589747526494184306528790444093525" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "1" }, "endGame": { "money": "' . $balanceInCents . '", "bet": "' . ($betlineRaw * 20) . '", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }, "totalWinnings": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . (($allScattersWin + $oldWin) * 100) . '", "totalMultiplier": { }, "bonusRequest": { }, "gameSpecific": { "scatterValues": "' . implode('-', $allScattersWinArr) . '", "bonusGameInProgress": "0", "bonusGameSpinsLeft": "' . $bonusGameSpinsLeft . '", "bonusGameSpinsUsed": "' . $bonusGameSpinsUsed . '", "bonusOnlyGain": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "bonusGameGain": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "bonusGameGainMoney": "' . (($allScattersWin + $oldWin) * 100) . '", "bonusGameNewScatters": "' . implode('-', $bonusGameNewScatters) . '", "bonusGameMultipliers": "1-1-1-1-1-1", "bonusGameValuesCoins": "' . implode('-', $bonusGameValuesCoins) . '", "bonusGameValuesMoney": "' . implode('-', $bonusGameValuesMoney) . '", "bonusGameInitialSymbols": "' . implode('-', $bonusGameInitialSymbols) . '", "bonusGameInitialScatterValues": "' . implode('-', $bonusGameInitialScatterValues) . '", "megaways": "' . $ways . '" } }, "doubleWin": { "totalWinnings": "' . ((($allScattersWin + $oldWin) * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . (($allScattersWin + $oldWin) * 100) . '", "money": "' . $balanceInCents . '" } } } }';
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameInitialSymbols', $bonusGameInitialSymbols);
                            $slotSettings->SetGameData($slotSettings->slotId . 'allScattersWinArr', $allScattersWinArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameSpinsLeft', $bonusGameSpinsLeft);
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameSpinsUsed', $bonusGameSpinsUsed);
                            $slotSettings->SetGameData($slotSettings->slotId . 'allScattersWin', $allScattersWin + $oldWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'reels', $reelsNew);
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameValuesCoins', $bonusGameValuesCoins);
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusGameValuesMoney', $bonusGameValuesMoney);
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
