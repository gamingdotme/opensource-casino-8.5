<?php 
namespace VanguardLTE\Games\GhostsNGoldISB
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
                            $result_tmp[0] = '{ "rootdata": { "uid": "5640f7f3-4693-4059-841a-dc3afd3f1925", "data": { "version": { "versionServer": "2.2.0.1-1", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520020301588680413177316196800814753252699" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "30", "coinValue": "' . $gameBets[0] . '", "lines": "20", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 20) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": { }, "bonusRequest": { } } } } } } }';
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
                            $lines = 30;
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
                                $randomWild = rand(1, 50);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $randomWild = rand(1, 5);
                                }
                                $miniBonusStr = '';
                                if( $randomWild == 1 && $winType != 'bonus' ) 
                                {
                                    $rWildsLimit = rand(3, 8);
                                    $mystSym = 11;
                                    $mystSymArr = [
                                        [
                                            2, 
                                            0
                                        ], 
                                        [
                                            2, 
                                            1
                                        ], 
                                        [
                                            2, 
                                            2
                                        ], 
                                        [
                                            2, 
                                            3
                                        ], 
                                        [
                                            2, 
                                            4
                                        ], 
                                        [
                                            2, 
                                            5
                                        ], 
                                        [
                                            3, 
                                            0
                                        ], 
                                        [
                                            3, 
                                            1
                                        ], 
                                        [
                                            3, 
                                            2
                                        ], 
                                        [
                                            3, 
                                            3
                                        ], 
                                        [
                                            3, 
                                            4
                                        ], 
                                        [
                                            3, 
                                            5
                                        ], 
                                        [
                                            4, 
                                            0
                                        ], 
                                        [
                                            4, 
                                            1
                                        ], 
                                        [
                                            4, 
                                            2
                                        ], 
                                        [
                                            4, 
                                            3
                                        ], 
                                        [
                                            4, 
                                            4
                                        ], 
                                        [
                                            4, 
                                            5
                                        ], 
                                        [
                                            5, 
                                            0
                                        ], 
                                        [
                                            5, 
                                            1
                                        ], 
                                        [
                                            5, 
                                            2
                                        ], 
                                        [
                                            5, 
                                            3
                                        ], 
                                        [
                                            5, 
                                            4
                                        ], 
                                        [
                                            5, 
                                            5
                                        ]
                                    ];
                                    shuffle($mystSymArr);
                                    for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                    {
                                        $msc = $mystSymArr[$p];
                                        $reels['reel' . $msc[0]][$msc[1]] = -1;
                                    }
                                    $scPos = [
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        []
                                    ];
                                    for( $p = 0; $p <= 5; $p++ ) 
                                    {
                                        for( $r = 2; $r <= 5; $r++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == -1 ) 
                                            {
                                                $reels['reel' . $r][$p] = $mystSym;
                                                $scPos[$p][] = '1';
                                            }
                                            else
                                            {
                                                $scPos[$p][] = '0';
                                            }
                                        }
                                    }
                                    $hlw = [
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
                                    $hlwc = 0;
                                    for( $p = 0; $p <= 5; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $mystSym ) 
                                            {
                                                $hlw[$hlwc] = 1;
                                            }
                                            $hlwc++;
                                        }
                                    }
                                    $miniBonusStr = ', "wilds": { "wild": { "type": "2", "id": "11", "pos": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] }, "mask": { "line": [ "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[0]) . '" ] } } }, "gameSpecific": { "dragonDropWilds": "' . implode('', $hlw) . '" }';
                                }
                                $tmpStringWin = '';
                                $k = 0;
                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                {
                                    $csym = (string)$slotSettings->SymbolGame[$j];
                                    if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                    {
                                    }
                                    else
                                    {
                                        $symCounts = [
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
                                        $lastReel = 0;
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            $isEmpty = true;
                                            for( $p = 0; $p <= 5; $p++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == $csym || $reels['reel' . $r][$p] == $wild ) 
                                                {
                                                    $isEmpty = false;
                                                    $symCounts[$r]++;
                                                    $lastReel = $r;
                                                }
                                            }
                                            if( $isEmpty ) 
                                            {
                                                break;
                                            }
                                        }
                                        $hl = [
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
                                        $hlc = 0;
                                        for( $p = 0; $p <= 5; $p++ ) 
                                        {
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                if( ($reels['reel' . $r][$p] == $csym || $reels['reel' . $r][$p] == $wild) && $r <= $lastReel ) 
                                                {
                                                    $hl[$hlc] = 1;
                                                }
                                                $hlc++;
                                            }
                                        }
                                        if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 ) 
                                        {
                                            $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3];
                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $symWeight * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{ "ways": "' . $symWeight . '", "initialValue": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "initialValueMoney": "' . ($cWins[$k] * 100) . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                                            }
                                        }
                                        if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 && $symCounts[4] > 0 ) 
                                        {
                                            $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3] * $symCounts[4];
                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $symWeight * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{ "ways": "' . $symWeight . '", "initialValue": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "initialValueMoney": "' . ($cWins[$k] * 100) . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                                            }
                                        }
                                        if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 && $symCounts[4] > 0 && $symCounts[5] > 0 ) 
                                        {
                                            $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3] * $symCounts[4] * $symCounts[5];
                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $symWeight * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{ "ways": "' . $symWeight . '", "initialValue": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "initialValueMoney": "' . ($cWins[$k] * 100) . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                                            }
                                        }
                                        if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                        {
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$k];
                                        }
                                        $k++;
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scPos = [];
                                for( $p = 0; $p <= 5; $p++ ) 
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
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $curReels .= (',"-1-' . $reels['reel2'][3] . '-' . $reels['reel3'][3] . '-' . $reels['reel4'][3] . '-' . $reels['reel5'][3] . '"');
                            $curReels .= (',"-1-' . $reels['reel2'][4] . '-' . $reels['reel3'][4] . '-' . $reels['reel4'][4] . '-' . $reels['reel5'][4] . '"');
                            $curReels .= (',"-1-' . $reels['reel2'][5] . '-' . $reels['reel3'][5] . '-' . $reels['reel4'][5] . '-' . $reels['reel5'][5] . '"');
                            $balanceInCentsEnd = round($slotSettings->GetBalance() * 100);
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": { }, "wilds": { "wild": { "type": "2", "id": "11", "pos": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] }, "mask": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] } } }' . $miniBonusStr . ' }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "901cc9a9-47fd-4e39-a483-04132c59ae8c", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "255551901588710457781853013233514248334670" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "30", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": {"line": [ ' . $winString . ' ]  }' . $scattersStr . ', "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": { }, "bonusRequest": { }, "wilds": { "wild": { "type": "2", "id": "11", "pos": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] }, "mask": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] } } }' . $miniBonusStr . ' }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
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
