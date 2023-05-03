<?php 
namespace VanguardLTE\Games\StacksOGoldISB
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
                            $result_tmp[0] = '{ "rootdata": { "uid": "161f4c20-31e0-43a3-952f-ded93e15a5c5", "data": { "version": { "versionServer": "2.5.0.0", "versionGMAPI": "8.1.16GS:2.5.1FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "025554270158876657011709763" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "coins": "1", "betPerSpin": "20", "coinValue": "1", "lines": "34", "money": "' . $balanceInCents . '", "currentState": "beginGame", "lastGame": { "endGame": { "money": "' . $balanceInCents . '", "bet": "20", "betMoney": "20", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "wilds": { }, "totalWinnings": "0", "totalWinningsMoney": "0" } } } } } }';
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
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[2] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[4] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[5] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[6] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[7] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[8] = [
                                1, 
                                2, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[9] = [
                                1, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[10] = [
                                2, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[11] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[12] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[13] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[14] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                2, 
                                2, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[16] = [
                                2, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[17] = [
                                2, 
                                3, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[18] = [
                                2, 
                                3, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[19] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[20] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[21] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[22] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[23] = [
                                2, 
                                3, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[24] = [
                                3, 
                                3, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[25] = [
                                3, 
                                3, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[26] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[27] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[28] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[29] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[30] = [
                                3, 
                                3, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[31] = [
                                3, 
                                4, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[32] = [
                                3, 
                                4, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[33] = [
                                3, 
                                4, 
                                3, 
                                4, 
                                3
                            ];
                            $lines = 20;
                            $betline = trim($gDat[1]) / 100;
                            $betlineRaw = trim($gDat[1]);
                            $allbet = $betline * 20;
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
                                $wild = [
                                    '9', 
                                    '10'
                                ];
                                $scatter = '8';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $wildsCounter = $slotSettings->GetGameData($slotSettings->slotId . 'wildsCounter');
                                $wildStr = '';
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $wildsCounter0 = 0;
                                    $wildsArr = [
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ]
                                    ];
                                    $wildStr0 = [];
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $p == 3 && ($r == 1 || $r == 3 || $r == 5) ) 
                                            {
                                            }
                                            if( $reels['reel' . $r][$p] == '10' ) 
                                            {
                                                $wildsArr[$p][$r - 1] = 1;
                                                $wildsCounter++;
                                                $wildsCounter0++;
                                            }
                                        }
                                    }
                                    if( $wildsCounter0 > 0 ) 
                                    {
                                        $wildStr0[] = '{ "type": "2", "mask": { "line": [ "' . implode('-', $wildsArr[0]) . '", "' . implode('-', $wildsArr[1]) . '", "' . implode('-', $wildsArr[2]) . '", "' . implode('-', $wildsArr[3]) . '" ] } }]';
                                    }
                                }
                                for( $k = 0; $k < 34; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = (string)$slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $s = [];
                                            $sp = [];
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            $sp[0] = $linesId[$k][0];
                                            $sp[1] = $linesId[$k][1];
                                            $sp[2] = $linesId[$k][2];
                                            $sp[3] = $linesId[$k][3];
                                            $sp[4] = $linesId[$k][4];
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '000", "symbolId": "22", "multiplier": "' . $mpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '00", "symbolId": "22", "multiplier": "' . $mpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '0", "symbolId": "22", "multiplier": "' . $mpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '' . $sp[4] . '", "symbolId": "22", "multiplier": "' . $mpl . '" }';
                                                }
                                            }
                                        }
                                    }
                                    if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                    {
                                        array_push($lineWins, $tmpStringWin);
                                        $totalWin += $cWins[$k];
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scPos = [];
                                for( $p = 0; $p <= 3; $p++ ) 
                                {
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $p == 3 && ($r == 1 || $r == 3 || $r == 5) ) 
                                        {
                                        }
                                        else if( $reels['reel' . $r][$p] == $scatter ) 
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
                                    $scattersStr = ', "scatters": { "value": "0", "valueMoney": "0", "multiplier": "1", "hl": "' . implode('', $scPos) . '" }, "freeGamesWin": { "total": "' . $sgwin . '", "multiplier": "1", "hl": "' . implode('', $scPos) . '" }';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount);
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'wildsCounter', $wildsCounter);
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $curReels .= (',"' . $reels['reel1'][3] . '-' . $reels['reel2'][3] . '-' . $reels['reel3'][3] . '-' . $reels['reel4'][3] . '-' . $reels['reel5'][3] . '"');
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
                                $result_tmp[0] = '{ "rootdata": { "uid": "918e5cd7-e166-4503-9a76-ed3c828ff00f", "data": { "endGame": { "money": "' . $balanceInCentsEnd . '", "bet": "20", "betMoney": "20", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { "line": [ ' . $winString . ' ] }, "wilds": {' . $wildStr . '}, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "wildsCounter": "0" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '" }, "balance": { "cashBalance": "99794", "freeBalance": "0" }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "60a3f519-577f-453b-8d72-9fe5a976c204", "data": { "id": { "roundId": "025554270158876703251592054" }, "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "betMoney": "20", "symbols": { "line": [ ' . $curReels . '  ] }, "lines": {  "line": [' . $winString . ']}' . $scattersStr . ', "wilds": { }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '" }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
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
