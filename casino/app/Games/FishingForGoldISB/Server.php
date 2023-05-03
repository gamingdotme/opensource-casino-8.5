<?php 
namespace VanguardLTE\Games\FishingForGoldISB
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'bonusChancePercent' . ($slotSettings->Bet[$i] * 100), 0);
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $lastEvent = $slotSettings->GetHistory();
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'freeGamesSym', []);
                            if( $lastEvent != 'NULL' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $slotSettings->SetGameData($slotSettings->slotId . 'freeGamesSym', $lastEvent->serverResponse->freeGamesSym);
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $lines = $lastEvent->serverResponse->slotLines;
                                $bet = $lastEvent->serverResponse->slotBet * 100;
                            }
                            else
                            {
                                $lines = 10;
                                $bet = $slotSettings->Bet[0] * 100;
                            }
                            $gameSpecific = ', "gameSpecific": { "bonusChanceEnabled": "0", "bonusChancePercent1": "0" }';
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeSpinsStr = '"freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "1", "totalMultiplier": "1" },';
                                $gameSpecific = ',"gameSpecific": ' . json_encode($lastEvent->serverResponse->gameSpecific);
                            }
                            else
                            {
                                $freeSpinsStr = '';
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "538165de-c155-4c6f-bd29-a880668c56ac", "data": { "version": { "versionServer": "2.2.0.1-0", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "255953101589481352875641709137320751236297" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "1", "coinValue": "1", "lines": "25", "currentState": "beginGame", "lastGame": { "endGame": {  ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 25) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": "1", "bonusRequest": { }' . $gameSpecific . ' } } } } } }';
                            break;
                        case '192837':
                            $result_tmp[0] = '{ "rootdata": { "uid": "419427a2-d300-41d9-8d67-fe4eec61be5f", "data": "1" } }';
                            break;
                        case '8':
                            $result_tmp[0] = '{ "rootdata": { "uid": "e44866bf-d795-4f3c-bf30-6f495a6797f2", "data": { "bonusChoice": { "bonusId": "1", "choicesOrder": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0", "bonusGain": { }, "bonusGainMoney": { }, "choicesWinnings": "1-2-3-4-5-6-7-8-12-22-32-42-52-62-72-82-13-23-33-43-53-63-73-83-15-25-35-45-55-65-75-85-11-11-11-11-11-11-11-11-11-11-11-11", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesMult": "1-1-1-1-1-1-1-1-2-2-2-2-2-2-2-2-3-3-3-3-3-3-3-3-5-5-5-5-5-5-5-5-1-1-1-1-1-1-1-1-1-1-1-1", "totalMultiplier": "1", "choicesFreeGames": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-1-1-1-1-1-1-1-1-1-1-1-1" }, "gameSpecific": { "bonusChanceEnabled": "0", "bonusChancePercent1": "0" } } } }';
                            break;
                        case '9':
                            $gDat = explode("\n", $postData['cmd']);
                            $select = trim($gDat[1]);
                            $fishingItems = [
                                1, 
                                2, 
                                3, 
                                4, 
                                5, 
                                6, 
                                7, 
                                8, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11, 
                                11
                            ];
                            $freeGamesCnt = 0;
                            $freeGamesSym = [];
                            $buckets = [];
                            for( $b = 0; $b < 4; $b++ ) 
                            {
                                shuffle($fishingItems);
                                $randomFg = rand(8, 15);
                                $buckets[$b] = [];
                                for( $i = 0; $i < $randomFg; $i++ ) 
                                {
                                    if( $select - 1 == $b ) 
                                    {
                                        if( $fishingItems[$i] == 11 ) 
                                        {
                                            $freeGamesCnt++;
                                        }
                                        else
                                        {
                                            $freeGamesSym[] = $fishingItems[$i];
                                        }
                                    }
                                    $buckets[$b][] = $fishingItems[$i];
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $freeGamesCnt);
                            $slotSettings->SetGameData($slotSettings->slotId . 'freeGamesSym', $freeGamesSym);
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $result_tmp[0] = '{ "rootdata": { "uid": "e44866bf-d795-4f3c-bf30-6f495a6797f2", "data": { "bonusWin": { "money": "' . $balanceInCents . '", "bonusRequest": "0", "bonusId": "1", "bonusesLeft": "0", "totalFreeGames": "' . $freeGamesCnt . '", "multiplier": "1", "totalMultiplier": "1", "choicesFreeGames": "0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-1-1-1-1-1-1-1-1-1-1-1-1", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesOrder": "1-0-0-2-0-3-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-4-5-6-7-8-9-10-11-12-13-14-0", "choicesWinnings": "1-2-3-4-5-6-7-8-12-22-32-42-52-62-72-82-13-23-33-43-53-63-73-83-15-25-35-45-55-65-75-85-11-11-11-11-11-11-11-11-11-11-11-11", "choicesMult": "1-1-1-1-1-1-1-1-2-2-2-2-2-2-2-2-3-3-3-3-3-3-3-3-5-5-5-5-5-5-5-5-1-1-1-1-1-1-1-1-1-1-1-1", "totalWinnings": "0", "totalWinningsMoney": "0", "freeGamesWin": { "total": "' . $freeGamesCnt . '", "multiplier": "1" } }, "gameSpecific": { "selectedCharacterId": "' . $select . '", "bucket1": "' . implode('-', $buckets[0]) . '", "bucket2": "' . implode('-', $buckets[1]) . '", "bucket3": "' . implode('-', $buckets[2]) . '", "bucket4": "' . implode('-', $buckets[3]) . '", "bonusSymbols": "' . implode('-', $freeGamesSym) . '", "retriggerSymbolCount": "0", "bonusChanceEnabled": "0", "bonusChancePercent1": "0" }, "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0" } } } }';
                            break;
                        case '5347':
                            $result_tmp[0] = '{ "rootdata": { "uid": "26d1001c-15ff-4d21-af9e-45dde0260165", "data": { "success": "" } } }';
                            break;
                        case '2':
                            $gDat = explode("\n", $postData['cmd']);
                            $bch = trim($gDat[2]);
                            if( $bch == 108 ) 
                            {
                                $betlineRaw = $slotSettings->GetGameData($slotSettings->slotId . 'betlineRaw');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank('', $bankSum, 'bet');
                                $response = '{"responseEvent":"gambleResult","responseType":"spin","serverResponse":{"slotLines":25,"slotBet":1,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":0,"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 25, 0, 'BCH');
                                $balanceInCentsStart = round($slotSettings->GetBalance() * 100);
                                $winTypeTmp = $slotSettings->GetSpinSettings('bet', $allbet, 25);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $curReels = $slotSettings->GetGameData($slotSettings->slotId . 'curReels');
                                if( $winType == 'bonus' ) 
                                {
                                    $bonusT = 1;
                                }
                                else
                                {
                                    $bonusT = 0;
                                }
                                echo '{ "rootdata": { "uid": "378b4dcb-8dd5-4721-b53a-b31fa6f54fd6", "data": { "balance": { "cashBalance": "' . $balanceInCentsStart . '", "freeBalance": "0" }, "id": { "roundId": "255953101589483732916657191931375981555583" }, "coinValues": { "coinValueList": "1,2,5,10,20,30,50,100", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [ ' . $curReels . '] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "totalMultiplier": "1", "bonusRequest": "' . $bonusT . '", "bonuses": { "bonus": { "id": "1", "hl": "011100000000000" } }, "gameSpecific": { "bonusChanceEnabled": "0", "bonusChancePercent1": "0", "bonusChanceWon": "' . $bonusT . '" } }, "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCentsStart . '" } } } }';
                            }
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
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[22] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[23] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[24] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                2
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
                                $wild = ['10'];
                                $scatter = '11';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $wild = $slotSettings->GetGameData($slotSettings->slotId . 'freeGamesSym');
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
                                $miniBonusStr = ' "gameSpecific": { "bonusChanceEnabled": "1", "bonusChancePercent1": "3" } }';
                                $miniBonusStr0 = '';
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
                            $bonusChancePercent1 = $slotSettings->GetGameData($slotSettings->slotId . 'bonusChancePercent' . $betlineRaw . '');
                            $bonusChanceEnabled = 0;
                            if( $scattersCount == 2 ) 
                            {
                                $bonusChancePercent1++;
                            }
                            if( $bonusChancePercent1 > 0 ) 
                            {
                                $bonusChanceEnabled = 1;
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'bonusChancePercent' . $betlineRaw . '', $bonusChancePercent1);
                            $slotSettings->SetGameData($slotSettings->slotId . 'betlineRaw' . $betlineRaw . '', $betlineRaw);
                            $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ', "gameSpecific": { "bonusSymbols": "' . implode('-', $slotSettings->GetGameData($slotSettings->slotId . 'freeGamesSym')) . '", "retriggerSymbolCount": "0","bonusChanceEnabled": "' . $bonusChanceEnabled . '", "bonusChancePercent' . $betlineRaw . '": "' . $bonusChancePercent1 . '" } ,"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"freeGamesSym":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'freeGamesSym')) . '],"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $slotSettings->SetGameData($slotSettings->slotId . 'curReels', $curReels);
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
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": "0", "gameSpecific": { "bonusSymbols": "' . implode('-', $slotSettings->GetGameData($slotSettings->slotId . 'freeGamesSym')) . '", "retriggerSymbolCount": "0","bonusChanceEnabled": "' . $bonusChanceEnabled . '", "bonusChancePercent' . $betlineRaw . '": "' . $bonusChancePercent1 . '" } }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "e9e8d380-bc70-46a9-aad3-3a3e66d81528", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587903974365541166421222354308299" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "1" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "25", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": { "line": [' . $winString . '] }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "' . $bonusMpl . '", "bonusRequest": "0" , "gameSpecific": { "bonusChanceEnabled": "' . $bonusChanceEnabled . '", "bonusChancePercent' . $betlineRaw . '": "' . $bonusChancePercent1 . '" }' . $scattersStr . ' }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
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
