<?php 
namespace VanguardLTE\Games\SheriffOfNotinghamISB
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
                            $result_tmp[0] = '{ "rootdata": { "uid": "24f86986-1081-49df-b75a-4d4830bb80b4", "data": { "version": { "versionServer": "2.2.0.1-3", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "USD", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520003501587064156642840845543695174381020" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "1", "coinValue": "1", "lines": "25", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "25", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": "1", "bonusRequest": { } } } } } } }';
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
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[6] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[7] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[8] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[9] = [
                                2, 
                                3, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[13] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[14] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[15] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[16] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[17] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $linesId[20] = [
                                3, 
                                1, 
                                1, 
                                1, 
                                3
                            ];
                            $linesId[21] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[22] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[23] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[24] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $lines = 25;
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
                                $scatter = '11';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $syncReels = [
                                        1, 
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
                                        3, 
                                        3, 
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
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5
                                    ];
                                    $syncReelArr = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $syncReelCur = rand(1, 5);
                                    $syncReelsCnt = rand(2, 5);
                                    shuffle($syncReels);
                                    $syncReelArr[$syncReelCur - 1] = 1;
                                    for( $r = 1; $r <= $syncReelsCnt; $r++ ) 
                                    {
                                        $syncReelArr[$syncReels[$r] - 1] = 1;
                                        $reels['reel' . $syncReels[$r]] = $reels['reel' . $syncReelCur];
                                    }
                                }
                                $miniBonuses = [
                                    'STACKED_WILDS', 
                                    'STACKED_WILDS', 
                                    'WIN_SPIN', 
                                    'WIN_SPIN', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    '', 
                                    ''
                                ];
                                shuffle($miniBonuses);
                                $miniBonusStr = '"gameSpecific": { "currentCashPrizes": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "currentCashPrizesMoney": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0" }';
                                $miniBonusStr0 = '';
                                if( $miniBonuses[0] == 'WIN_SPIN' && $postData['slotEvent'] != 'freespin' && $winType !== 'bonus' ) 
                                {
                                    $dummyModifierSymbolsMask = [];
                                    $winsPosArr = [
                                        [
                                            1, 
                                            0
                                        ], 
                                        [
                                            1, 
                                            1
                                        ], 
                                        [
                                            1, 
                                            2
                                        ], 
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
                                        ]
                                    ];
                                    $rsym = rand(1, 8);
                                    $rsymCnt = rand(4, 12);
                                    shuffle($winsPosArr);
                                    for( $jl = 0; $jl < $rsymCnt; $jl++ ) 
                                    {
                                        $cw = $winsPosArr[$jl];
                                        $reels['reel' . $cw[0]][$cw[1]] = $rsym;
                                    }
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $rsym ) 
                                            {
                                                $dummyModifierSymbolsMask[] = $rsym;
                                            }
                                            else
                                            {
                                                $dummyModifierSymbolsMask[] = '0';
                                            }
                                        }
                                    }
                                    $miniBonusStr = '"gameSpecific": { "currentCashPrizes": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "currentCashPrizesMoney": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "dummyModifierActive": "1", "dummyModifierName": "WIN_SPIN", "dummyModifierSymbolsMask": "' . implode('-', $dummyModifierSymbolsMask) . '" }';
                                }
                                if( $miniBonuses[0] == 'STACKED_WILDS' && $postData['slotEvent'] != 'freespin' && $winType !== 'bonus' ) 
                                {
                                    $dummyModifierSymbolsMask = [];
                                    $rowWilds = [
                                        1, 
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
                                        3, 
                                        3, 
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
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5
                                    ];
                                    shuffle($rowWilds);
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $reels['reel' . $rowWilds[$r]][0] = '9';
                                        $reels['reel' . $rowWilds[$r]][1] = '9';
                                        $reels['reel' . $rowWilds[$r]][2] = '9';
                                    }
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '9' ) 
                                            {
                                                $dummyModifierSymbolsMask[] = '9';
                                            }
                                            else
                                            {
                                                $dummyModifierSymbolsMask[] = '0';
                                            }
                                        }
                                    }
                                    $miniBonusStr = '"gameSpecific": { "currentCashPrizes": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "currentCashPrizesMoney": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "dummyModifierActive": "1", "dummyModifierName": "STACKED_WILDS", "dummyModifierSymbolsMask": "' . implode('-', $dummyModifierSymbolsMask) . '" }';
                                }
                                for( $k = 0; $k < $lines; $k++ ) 
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
                                $scattersCount2 = 0;
                                $scPos = [];
                                $scPos2 = [];
                                $respinsNewSymbols = [];
                                $respinsOriginalSymbols = [];
                                $currentCashPrizesMoney = [];
                                $currentCashPrizes = [];
                                $respinsTotalWinningsMoney = 0;
                                $respinsTotalWinnings = 0;
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
                                        $respinsOriginalSymbols[] = $reels['reel' . $r][$p];
                                        if( $reels['reel' . $r][$p] >= 14 ) 
                                        {
                                            $scattersCount2++;
                                            $respinsNewSymbols[] = $reels['reel' . $r][$p];
                                            $currentCashPrizesMoney[] = $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]] * $allbet * 100;
                                            $currentCashPrizes[] = $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]];
                                            $respinsTotalWinningsMoney += ($slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]] * $allbet);
                                            $respinsTotalWinnings += $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]];
                                        }
                                        else
                                        {
                                            $respinsNewSymbols[] = 0;
                                            $currentCashPrizesMoney[] = 0;
                                            $currentCashPrizes[] = 0;
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
                                if( $scattersCount2 >= 5 ) 
                                {
                                    $totalWin += $respinsTotalWinningsMoney;
                                    $miniBonusStr = ' "gameSpecific": { "currentCashPrizes": "' . implode('-', $currentCashPrizes) . '", "currentCashPrizesMoney": "' . implode('-', $currentCashPrizesMoney) . '", "respinsActive": "1", "respinsLeft": "4", "respinsUsed": "0", "respinsLastWin": "' . $respinsTotalWinnings . '", "respinsLastWinMoney": "' . ($respinsTotalWinningsMoney * 100) . '", "respinsTotalWinnings": "' . $respinsTotalWinnings . '", "respinsTotalWinningsMoney": "' . ($respinsTotalWinningsMoney * 100) . '", "respinsOriginalSymbols": "' . implode('-', $respinsOriginalSymbols) . '", "respinsOriginalCashPrizes": "' . implode('-', $currentCashPrizes) . '", "respinsOriginalCashPrizesMoney": "' . implode('-', $currentCashPrizesMoney) . '", "respinsNewSymbols": "' . implode('-', $respinsNewSymbols) . '" }';
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
                                    else if( ($scattersCount >= 3 || $scattersCount2 >= 5) && $winType != 'bonus' ) 
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
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
                            if( $miniBonuses[0] == 'STACKED_WILDS' && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $miniBonusStr0 = ', "pendingWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" }';
                            }
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            if( $scattersCount2 >= 5 ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'respinsTotalWinningsMoney', $respinsTotalWinningsMoney);
                                $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinsTotal', 3);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinsCurrent', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'respinsCurrentSymbols', $respinsNewSymbols);
                                $slotSettings->SetGameData($slotSettings->slotId . 'respinsNewSymbols', $respinsNewSymbols);
                                $slotSettings->SetGameData($slotSettings->slotId . 'respinsOriginalSymbols', $respinsOriginalSymbols);
                                $slotSettings->SetGameData($slotSettings->slotId . 'curReels', $curReels);
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, 'FG2');
                            }
                            else
                            {
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": { }, "insync": { "sync": { "from": "0-0-0-0-0", "to": "' . implode('-', $syncReelArr) . '", "mask": "' . implode('-', $syncReelArr) . '" } } }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "e9e8d380-bc70-46a9-aad3-3a3e66d81528", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587903974365541166421222354308299" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "1" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { "line": [' . $winString . '] }' . $scattersStr . ', "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "' . $bonusMpl . '", "bonusRequest": { }, ' . $miniBonusStr . ' }' . $miniBonusStr0 . ', "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCentsEnd . '" } } } }';
                                $slotSettings->SetGameData($slotSettings->slotId . 'LastResponse', $result_tmp[0]);
                            }
                            break;
                        case '104':
                            $respinsTotalWinningsMoneyCur = $slotSettings->GetGameData($slotSettings->slotId . 'respinsTotalWinningsMoney');
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                            $RespinsTotal = $slotSettings->GetGameData($slotSettings->slotId . 'RespinsTotal');
                            $RespinsCurrent = $slotSettings->GetGameData($slotSettings->slotId . 'RespinsCurrent');
                            $respinsCurrentSymbols = $slotSettings->GetGameData($slotSettings->slotId . 'respinsCurrentSymbols');
                            $respinsNewSymbols_ = $slotSettings->GetGameData($slotSettings->slotId . 'respinsNewSymbols');
                            $respinsOriginalSymbols = $slotSettings->GetGameData($slotSettings->slotId . 'respinsOriginalSymbols');
                            $curReels_ = $slotSettings->GetGameData($slotSettings->slotId . 'curReels');
                            $LastResponse = json_decode($slotSettings->GetGameData($slotSettings->slotId . 'LastResponse'), true);
                            if( $RespinsTotal <= 0 || $RespinsTotal < $RespinsCurrent ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid respin state"}';
                                exit( $response );
                            }
                            $bank = $slotSettings->GetBank('bonus');
                            for( $lp = 1; $lp <= 2000; $lp++ ) 
                            {
                                $totalWin = 0;
                                $rstripSyms = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    14, 
                                    14, 
                                    15, 
                                    15, 
                                    16, 
                                    17, 
                                    18, 
                                    19, 
                                    20
                                ];
                                $reels = [];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    shuffle($rstripSyms);
                                    $reels['reel' . $r][0] = $rstripSyms[0];
                                    $reels['reel' . $r][1] = $rstripSyms[1];
                                    $reels['reel' . $r][2] = $rstripSyms[2];
                                }
                                $rc = 0;
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $cs = $respinsNewSymbols_[$rc];
                                        if( $cs >= 14 ) 
                                        {
                                            $reels['reel' . $r][$p] = $cs;
                                        }
                                        $rc++;
                                    }
                                }
                                $respinsNewSymbols = [];
                                $currentCashPrizesMoney = [];
                                $currentCashPrizes = [];
                                $respinsTotalWinningsMoney = 0;
                                $respinsTotalWinnings = 0;
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] >= 14 ) 
                                        {
                                            $respinsNewSymbols[] = $reels['reel' . $r][$p];
                                            $currentCashPrizesMoney[] = $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]] * $allbet * 100;
                                            $currentCashPrizes[] = $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]];
                                            $respinsTotalWinningsMoney += ($slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]] * $allbet);
                                            $respinsTotalWinnings += $slotSettings->WantedPaytable['SYM_' . $reels['reel' . $r][$p]];
                                        }
                                        else
                                        {
                                            $respinsNewSymbols[] = 0;
                                            $currentCashPrizesMoney[] = 0;
                                            $currentCashPrizes[] = 0;
                                        }
                                    }
                                }
                                $totalWin = $respinsTotalWinningsMoney - $respinsTotalWinningsMoneyCur;
                                if( $totalWin <= $bank ) 
                                {
                                    break;
                                }
                                if( $lp > 1900 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"internal game error|respin| "}';
                                    exit( $response );
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank('bonus', -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            if( $RespinsTotal == $RespinsCurrent ) 
                            {
                                $totalWinningsMoney = $respinsTotalWinningsMoney;
                                $respinsActive = 0;
                            }
                            else
                            {
                                $respinsActive = 1;
                                $totalWinningsMoney = 0;
                            }
                            $rsp = $RespinsTotal - $RespinsCurrent;
                            if( $rsp < 0 ) 
                            {
                                $rsp = 0;
                            }
                            $miniBonusStr = ' "gameSpecific": { "currentCashPrizes": "' . implode('-', $currentCashPrizes) . '", "currentCashPrizesMoney": "' . implode('-', $currentCashPrizesMoney) . '", "respinsActive": "' . $respinsActive . '", "respinsLeft": "' . $rsp . '", "respinsUsed": "4", "respinsLastWin": "0", "respinsLastWinMoney": "0", "respinsTotalWinnings": "' . $respinsTotalWinnings . '", "respinsTotalWinningsMoney": "' . ($respinsTotalWinningsMoney * 100) . '", "respinsOriginalSymbols": "' . implode('-', $respinsOriginalSymbols) . '", "respinsOriginalCashPrizes": "' . implode('-', $currentCashPrizes) . '", "respinsOriginalCashPrizesMoney": "' . implode('-', $currentCashPrizesMoney) . '", "respinsNewSymbols": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0" }';
                            $RespinsCurrent += 1;
                            $slotSettings->SetGameData($slotSettings->slotId . 'respinsTotalWinningsMoney', $respinsTotalWinningsMoney);
                            $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'RespinsTotal', $RespinsTotal);
                            $slotSettings->SetGameData($slotSettings->slotId . 'RespinsCurrent', $RespinsCurrent);
                            $slotSettings->SetGameData($slotSettings->slotId . 'respinsCurrentSymbols', $respinsNewSymbols);
                            $slotSettings->SetGameData($slotSettings->slotId . 'respinsNewSymbols', $respinsNewSymbols);
                            $slotSettings->SetGameData($slotSettings->slotId . 'respinsOriginalSymbols', $respinsOriginalSymbols);
                            $LastResponse['rootdata']['data']['endGame']['gameSpecific'] = $miniBonusStr;
                            $balanceInCentsEnd = round($slotSettings->GetBalance() * 100);
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "be0231dc-3c66-47e4-804b-e5c83c6ac617", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501589206645224829188902808664887969" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsEnd . '", "bet": "' . ($allbet * 100) . '", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "totalMultiplier": "1", "bonusRequest": { }, ' . $miniBonusStr . '}, "pendingWin": { "totalWinnings": "' . ($totalWinningsMoney * 100) . '", "totalWinningsMoney": "' . ($totalWinningsMoney * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            $response = '{"responseEvent":"spin","responseType":"respin","serverResponse":{"slotLines":25,"slotBet":' . ($allbet / 25) . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'RespinsTotal') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'RespinsCurrent') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'respinsTotalWinningsMoney') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[],"lastResponse":' . $result_tmp[0] . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, 25, $totalWin, 'FG2');
                            if( $RespinsTotal <= $RespinsCurrent ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "be0231dc-3c66-47e4-804b-e5c83c6ac617", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501589206645224829188902808664887969" }, "coinValues": {  "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1"  }, "endGame": { "money": "' . $balanceInCentsEnd . '", "bet": "' . ($allbet * 100) . '", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { }, "totalWinnings": "' . ($totalWinningsMoney * 100) . '", "totalWinningsMoney": "' . ($totalWinningsMoney * 100) . '", "totalMultiplier": "1", "bonusRequest": { }, ' . $miniBonusStr . ' }, "doubleWin": { "totalWinnings": "' . ($totalWinningsMoney * 100) . '", "totalWinningsMoney": "' . ($totalWinningsMoney * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
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
