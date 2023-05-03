<?php 
namespace VanguardLTE\Games\RacetrackRichesISB
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentLap', $lastEvent->serverResponse->currentLap);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPosition', $lastEvent->serverResponse->currentPosition);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemy', $lastEvent->serverResponse->currentPositionEnemy);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionRaw', $lastEvent->serverResponse->currentPositionRaw);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemyRaw', $lastEvent->serverResponse->currentPositionEnemyRaw);
                                $slotSettings->SetGameData($slotSettings->slotId . 'trajectory', explode('-', $lastEvent->serverResponse->trajectory));
                                $slotSettings->SetGameData($slotSettings->slotId . 'RemovedSym', $lastEvent->serverResponse->RemovedSym);
                                $slotSettings->SetGameData($slotSettings->slotId . 'freeMultiplier', $lastEvent->serverResponse->freeMultiplier);
                                $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $freeMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'freeMultiplier');
                                $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeSpinsStr = '"freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "' . $freeMultiplier . '", "totalMultiplier": "' . $freeMultiplier . '" }, "gameSpecific": { "prizesLap1": "C:1,W:5,M:5,R:1,C:2,M:10,W:10,C:1,M:5,C:2,X:2,W:5,C:1,M:10,C:2,R:2,W:10,S:1", "prizesLap2": "C:2,W:10,M:10,R:3,C:5,M:20,W:20,C:2,M:10,C:5,X:3,W:10,C:2,M:20,C:5,R:4,W:20,S:1", "prizesLap3": "C:5,W:20,M:20,R:5,C:10,M:50,W:50,C:5,M:20,C:10,X:5,W:20,C:5,M:50,C:10,R:6,W:50,B:1", "currentLap":' . $lastEvent->serverResponse->currentLap . ', "currentPosition": "' . $lastEvent->serverResponse->currentPosition . '" , "underlay": "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0", "currentFreespin": "' . $fsCur . '", "trajectory": "' . $lastEvent->serverResponse->trajectory . '"  }, ';
                            }
                            else
                            {
                                $freeSpinsStr = '';
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "5640f7f3-4693-4059-841a-dc3afd3f1925", "data": { "version": { "versionServer": "2.2.0.1-1", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520020301588680413177316196800814753252699" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "1", "coinValue": "' . $gameBets[0] . '", "lines": "20", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 20) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": { }, "bonusRequest": { } } } } } } }';
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
                                2, 
                                1, 
                                2
                            ];
                            $linesId[6] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[7] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[8] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[9] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[10] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[13] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[14] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[15] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[17] = [
                                2, 
                                3, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[18] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[19] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                2
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'freeMultiplier', 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentLap', 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPosition', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemy', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionRaw', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemyRaw', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'trajectory', []);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RemovedSym', []);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + 1);
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
                                $wild = ['10'];
                                $scatter = '11';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $randomMod = rand(1, 20);
                                if( $winType == 'bonus' || $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $randomMod = 0;
                                }
                                $miniBonusStr = '';
                                if( $randomMod == 1 ) 
                                {
                                    $rWildsLimit = rand(3, 8);
                                    $mystSym = 10;
                                    $mystSymArr = [
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
                                    shuffle($mystSymArr);
                                    for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                    {
                                        $msc = $mystSymArr[$p];
                                        $reels['reel' . $msc[0]][$msc[1]] = -1;
                                    }
                                    $scPos = [
                                        [], 
                                        [], 
                                        []
                                    ];
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
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
                                    $miniBonusStr = ', "gameSpecific": { "featureType": "wilds", "underlay": "' . implode(',', $scPos[0]) . ',' . implode(',', $scPos[1]) . ',' . implode(',', $scPos[2]) . '" } ';
                                }
                                if( $randomMod == 2 ) 
                                {
                                    $rWildsLimit = rand(6, 12);
                                    $mystSym = rand(1, 9);
                                    $mystSymArr = [
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
                                    shuffle($mystSymArr);
                                    for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                    {
                                        $msc = $mystSymArr[$p];
                                        $reels['reel' . $msc[0]][$msc[1]] = -1;
                                    }
                                    $scPos = [
                                        [], 
                                        [], 
                                        []
                                    ];
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
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
                                    $miniBonusStr = ', "gameSpecific": { "featureType": "respin", "underlay": "' . implode(',', $scPos[0]) . ',' . implode(',', $scPos[1]) . ',' . implode(',', $scPos[2]) . '" } ';
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $currentLap = $slotSettings->GetGameData($slotSettings->slotId . 'currentLap');
                                    $currentPosition = $slotSettings->GetGameData($slotSettings->slotId . 'currentPosition');
                                    $currentPositionEnemy = $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionEnemy');
                                    $currentPositionRaw = $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionRaw');
                                    $currentPositionEnemyRaw = $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionEnemyRaw');
                                    $CurrentFreeGame = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $trajectory = $slotSettings->GetGameData($slotSettings->slotId . 'trajectory');
                                    $freeMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'freeMultiplier');
                                    $RemovedSym = $slotSettings->GetGameData($slotSettings->slotId . 'RemovedSym');
                                    $prizesLap = [];
                                    $prizesLap[0] = [];
                                    $prizesLap[1] = [
                                        '', 
                                        'C:1', 
                                        'W:5', 
                                        'M:5', 
                                        'R:1', 
                                        'C:2', 
                                        'M:10', 
                                        'W:10', 
                                        'C:1', 
                                        'M:5', 
                                        'C:2', 
                                        'X:2', 
                                        'W:5', 
                                        'C:1', 
                                        'M:10', 
                                        'C:2', 
                                        'R:2', 
                                        'W:10', 
                                        'S:1'
                                    ];
                                    $prizesLap[2] = [
                                        '', 
                                        'C:2', 
                                        'W:10', 
                                        'M:10', 
                                        'R:3', 
                                        'C:5', 
                                        'M:20', 
                                        'W:20', 
                                        'C:2', 
                                        'M:10', 
                                        'C:5', 
                                        'X:3', 
                                        'W:10', 
                                        'C:2', 
                                        'M:20', 
                                        'C:5', 
                                        'R:4', 
                                        'W:20', 
                                        'S:1'
                                    ];
                                    $prizesLap[3] = [
                                        '', 
                                        'C:5', 
                                        'W:20', 
                                        'M:20', 
                                        'R:5', 
                                        'C:10', 
                                        'M:50', 
                                        'W:50', 
                                        'C:5', 
                                        'M:20', 
                                        'C:10', 
                                        'X:5', 
                                        'W:20', 
                                        'C:5', 
                                        'M:50', 
                                        'C:10', 
                                        'R:6', 
                                        'W:50', 
                                        'B:1'
                                    ];
                                    $prizesLap[4] = [
                                        '', 
                                        'C:5', 
                                        'W:20', 
                                        'M:20', 
                                        'R:5', 
                                        'C:10', 
                                        'M:50', 
                                        'W:50', 
                                        'C:5', 
                                        'M:20', 
                                        'C:10', 
                                        'X:5', 
                                        'W:20', 
                                        'C:5', 
                                        'M:50', 
                                        'C:10', 
                                        'R:6', 
                                        'W:50', 
                                        'B:1'
                                    ];
                                    $pDice = rand(1, 6);
                                    $eDice = rand(1, 6);
                                    $currentPosition += $pDice;
                                    $currentPositionEnemy += $eDice;
                                    $currentPositionRaw += $pDice;
                                    $currentPositionEnemyRaw += $eDice;
                                    if( $currentPosition > 18 ) 
                                    {
                                        $currentPosition = $currentPosition - 18;
                                        $currentLap++;
                                    }
                                    if( $currentPositionEnemy > 18 ) 
                                    {
                                        $currentPositionEnemy = $currentPositionEnemy - 18;
                                    }
                                    $curPrize = explode(':', $prizesLap[$currentLap][$currentPosition]);
                                    $trajectory[] = $pDice . ':' . $currentPositionRaw . ':' . $eDice . ':' . $currentPositionEnemyRaw;
                                    $rWildsLimit = 0;
                                    $mystSym = 0;
                                    $symToChangeArr = [];
                                    for( $r = 1; $r <= 9; $r++ ) 
                                    {
                                        if( !in_array($r, $RemovedSym) ) 
                                        {
                                            $symToChangeArr[] = $r;
                                        }
                                    }
                                    shuffle($symToChangeArr);
                                    if( $curPrize[0] == 'W' ) 
                                    {
                                        $mystSym = 10;
                                        $rWildsLimit = (int)$curPrize[1];
                                    }
                                    if( $curPrize[0] == 'C' ) 
                                    {
                                        $totalWin = (int)$curPrize[1] * $allbet;
                                    }
                                    if( $curPrize[0] == 'M' ) 
                                    {
                                        $mystSym = $symToChangeArr[0];
                                        $rWildsLimit = (int)$curPrize[1];
                                    }
                                    if( $curPrize[0] == 'R' ) 
                                    {
                                        $RemovedSym[] = (int)$curPrize[1];
                                    }
                                    if( $curPrize[0] == 'X' ) 
                                    {
                                        $freeMultiplier = (int)$curPrize[1];
                                    }
                                    $mystSymArr = [
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
                                    shuffle($mystSymArr);
                                    if( $rWildsLimit >= 10 ) 
                                    {
                                        $rWildsLimit = rand(5, 10);
                                    }
                                    for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                    {
                                        $msc = $mystSymArr[$p];
                                        $reels['reel' . $msc[0]][$msc[1]] = -1;
                                    }
                                    $scPos = [
                                        [], 
                                        [], 
                                        []
                                    ];
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( in_array($reels['reel' . $r][$p], $RemovedSym) ) 
                                            {
                                                $reels['reel' . $r][$p] = array_shift($symToChangeArr);
                                            }
                                            if( $reels['reel' . $r][$p] == -1 && $mystSym > 0 ) 
                                            {
                                                $reels['reel' . $r][$p] = $mystSym;
                                                if( $curPrize[0] == 'M' ) 
                                                {
                                                    $scPos[$p][] = '12';
                                                }
                                                else if( $curPrize[0] == 'W' ) 
                                                {
                                                    $scPos[$p][] = '12';
                                                }
                                                else
                                                {
                                                    $scPos[$p][] = $mystSym;
                                                }
                                            }
                                            else
                                            {
                                                $scPos[$p][] = '0';
                                            }
                                        }
                                    }
                                    $underlay = '';
                                    if( $curPrize[0] == 'M' || $curPrize[0] == 'W' ) 
                                    {
                                        $underlay = ', "underlay": "' . implode(',', $scPos[0]) . ',' . implode(',', $scPos[1]) . ',' . implode(',', $scPos[2]) . '"';
                                    }
                                    $bonusDataStr = ', "currentLap": "' . $currentLap . '", "currentPosition": "' . $currentPosition . '" ' . $underlay . ', "currentFreespin": "' . $CurrentFreeGame . '", "trajectory": "' . implode('-', $trajectory) . '" ';
                                    $bonusMpl = $freeMultiplier;
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
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '000", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
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
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '00", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
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
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '0", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
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
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '' . $sp[4] . '", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'freeMultiplier', 1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'currentLap', 1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'currentPosition', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemy', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionRaw', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemyRaw', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'trajectory', []);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'RemovedSym', []);
                                    $scattersStr = ', "scatters": { "value": "0", "valueMoney": "0", "multiplier": "1", "hl": "' . implode('', $scPos) . '" }, "freeGamesWin": { "total": "' . $sgwin . '", "multiplier": "1", "hl": "' . implode('', $scPos) . '" }, "gameSpecific": { "prizesLap1": "C:1,W:5,M:5,R:1,C:2,M:10,W:10,C:1,M:5,C:2,X:2,W:5,C:1,M:10,C:2,R:2,W:10,S:1", "prizesLap2": "C:2,W:10,M:10,R:3,C:5,M:20,W:20,C:2,M:10,C:5,X:3,W:10,C:2,M:20,C:5,R:4,W:20,S:1", "prizesLap3": "C:5,W:20,M:20,R:5,C:10,M:50,W:50,C:5,M:20,C:10,X:5,W:20,C:5,M:50,C:10,R:6,W:50,B:1", "currentLap": "1" }';
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 2);
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentLap', $currentLap);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPosition', $currentPosition);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemy', $currentPositionEnemy);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionRaw', $currentPositionRaw);
                                $slotSettings->SetGameData($slotSettings->slotId . 'currentPositionEnemyRaw', $currentPositionEnemyRaw);
                                $slotSettings->SetGameData($slotSettings->slotId . 'freeMultiplier', $freeMultiplier);
                                $slotSettings->SetGameData($slotSettings->slotId . 'trajectory', $trajectory);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RemovedSym', $RemovedSym);
                            }
                            if( $postData['slotEvent'] == 'freespin' && ($currentPositionRaw <= $currentPositionEnemyRaw || $currentLap > 3) ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame'));
                            }
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"RemovedSym":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'RemovedSym')) . '],"trajectory":"' . implode('-', $slotSettings->GetGameData($slotSettings->slotId . 'trajectory')) . '","freeMultiplier":' . $slotSettings->GetGameData($slotSettings->slotId . 'freeMultiplier') . ',"currentPositionEnemyRaw":' . $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionEnemy') . ',"currentPosition":' . $slotSettings->GetGameData($slotSettings->slotId . 'currentPosition') . ',"currentPositionEnemy":' . $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionEnemyRaw') . ',"currentPositionRaw":' . $slotSettings->GetGameData($slotSettings->slotId . 'currentPositionRaw') . ',"currentLap":' . $slotSettings->GetGameData($slotSettings->slotId . 'currentLap') . ',"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "' . $bonusMpl . '", "totalMultiplier": "' . $bonusMpl . '" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "' . $bonusMpl . '", "bonusRequest": "0", "gameSpecific": { "prizesLap1": "C:1,W:5,M:5,R:1,C:2,M:10,W:10,C:1,M:5,C:2,X:2,W:5,C:1,M:10,C:2,R:2,W:10,S:1", "prizesLap2": "C:2,W:10,M:10,R:3,C:5,M:20,W:20,C:2,M:10,C:5,X:3,W:10,C:2,M:20,C:5,R:4,W:20,S:1", "prizesLap3": "C:5,W:20,M:20,R:5,C:10,M:50,W:50,C:5,M:20,C:10,X:5,W:20,C:5,M:50,C:10,R:6,W:50,B:1"' . $bonusDataStr . ' }  }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "e9e8d380-bc70-46a9-aad3-3a3e66d81528", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587903974365541166421222354308299" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "1" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { "line": [' . $winString . '] }' . $scattersStr . ', "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "' . $bonusMpl . '", "bonusRequest": "0" ' . $miniBonusStr . ' }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
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
