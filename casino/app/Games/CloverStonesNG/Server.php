<?php 
namespace VanguardLTE\Games\CloverStonesNG
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
                        $result_tmp = [];
                        if( isset($postData['gameData']) ) 
                        {
                            $postData = $postData['gameData'];
                            $reqId = $postData['cmd'];
                            if( !isset($postData['cmd']) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"incorrect action"}';
                                exit( $response );
                            }
                        }
                        else
                        {
                            $reqId = $postData['action'];
                        }
                        if( $reqId == 'SpinRequest' ) 
                        {
                            if( $postData['data']['coin'] <= 0 || $postData['data']['bet'] <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['data']['coin'] * $postData['data']['bet'] * 10) && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                        }
                        switch( $reqId ) 
                        {
                            case 'InitRequest':
                                $result_tmp[0] = '{"action":"InitResponce","result":true,"sesId":"a40e5dc15a83a70f288e421fbcfc6de8","data":{"id":16183084}}';
                                exit( $result_tmp[0] );
                                break;
                            case 'EventsRequest':
                                $result_tmp[0] = '{"action":"EventsResponce","result":true,"sesId":"a40e5dc15a83a70f288e421fbcfc6de8","data":[]}';
                                exit( $result_tmp[0] );
                                break;
                            case 'APIVersionRequest':
                                $result_tmp[] = '{"action":"APIVersionResponse","result":true,"sesId":false,"data":{"router":"v3.12","transportConfig":{"reconnectTimeout":5000}}}';
                                break;
                            case 'PickBonusItemRequest':
                                $bonusSymbol = $postData['data']['index'];
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', $bonusSymbol);
                                $result_tmp[] = '{"action":"PickBonusItemResponse","result":"true","sesId":"10000217909","data":{"state":"PickBonus","params":{"picksRemain":"0","expandingSymbols":["' . $bonusSymbol . '"]}}}';
                                break;
                            case 'CheckBrokenGameRequest':
                                $result_tmp[] = '{"action":"CheckBrokenGameResponse","result":"true","sesId":"false","data":{"haveBrokenGame":"false"}}';
                                break;
                            case 'AuthRequest':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', $lastEvent->serverResponse->BonusSymbol);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                    $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                    $rp2 = '[' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0] . ']';
                                    $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1] . ']');
                                    $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2] . ']');
                                    $bet = $lastEvent->serverResponse->slotBet * 100 * 20;
                                }
                                else
                                {
                                    $rp1 = implode(',', [
                                        rand(0, count($slotSettings->reelStrip1) - 3), 
                                        rand(0, count($slotSettings->reelStrip2) - 3), 
                                        rand(0, count($slotSettings->reelStrip3) - 3)
                                    ]);
                                    $rp_1 = rand(0, count($slotSettings->reelStrip1) - 3);
                                    $rp_2 = rand(0, count($slotSettings->reelStrip2) - 3);
                                    $rp_3 = rand(0, count($slotSettings->reelStrip3) - 3);
                                    $rp_4 = rand(0, count($slotSettings->reelStrip4) - 3);
                                    $rp_5 = rand(0, count($slotSettings->reelStrip5) - 3);
                                    $rr1 = $slotSettings->reelStrip1[$rp_1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3];
                                    $rr4 = $slotSettings->reelStrip4[$rp_4];
                                    $rr5 = $slotSettings->reelStrip5[$rp_5];
                                    $rp2 = '[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']';
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                    $rr3 = $slotSettings->reelStrip4[$rp_4 + 1];
                                    $rr3 = $slotSettings->reelStrip5[$rp_5 + 1];
                                    $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                    $rr3 = $slotSettings->reelStrip4[$rp_4 + 2];
                                    $rr3 = $slotSettings->reelStrip5[$rp_5 + 2];
                                    $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                    $bet = $slotSettings->Bet[0] * 100 * 20;
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') == $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $fBonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                    $fTotal = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $fCurrent = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $fRemain = $fTotal - $fCurrent;
                                    $restoreString = ',"restoredGameCode":"340","lastResponse":{"spinResult":{"type":"SpinResult","rows":[' . $rp2 . ']},"freeSpinsTotal":"' . $fTotal . '","freeSpinRemain":"' . $fRemain . '","totalBonusWin":"' . $fBonusWin . '","state":"FreeSpins","expandingSymbols":["1"]}';
                                }
                                $result_tmp[0] = '{"action":"AuthResponse","result":"true","sesId":"10000569942","data":{"snivy":"proxy v6.10.48 (API v4.23)","supportedFeatures":["Offers","Jackpots","InstantJackpots","SweepStakes"],"sessionId":"10000569942","defaultLines":["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19"],"bets":["1","2","3","4","5","10","15","20","30","40","50","100","200","400"],"betMultiplier":"1.0000000","defaultBet":"1","defaultCoinValue":"0.01","coinValues":["0.01"],"gameParameters":{"availableLines":[["1","1","1","1","1"],["2","2","2","2","2"],["0","0","0","0","0"],["3","3","3","3","3"],["1","2","3","2","1"],["2","1","0","1","2"],["0","0","1","2","3"],["3","3","2","1","0"],["1","0","0","0","1"],["2","3","3","3","2"],["0","1","2","3","3"],["3","2","1","0","0"],["1","0","1","2","1"],["2","3","2","1","2"],["0","1","0","1","0"],["3","2","3","2","3"],["1","2","1","0","1"],["2","1","2","3","2"],["0","1","1","1","0"],["3","2","2","2","3"]],"rtp":"0.00","payouts":[{"payout":"16","symbols":["1","1","1"],"type":"basic"},{"payout":"32","symbols":["1","1","1","1"],"type":"basic"},{"payout":"80","symbols":["1","1","1","1","1"],"type":"basic"},{"payout":"16","symbols":["2","2","2"],"type":"basic"},{"payout":"24","symbols":["2","2","2","2"],"type":"basic"},{"payout":"48","symbols":["2","2","2","2","2"],"type":"basic"},{"payout":"16","symbols":["3","3","3"],"type":"basic"},{"payout":"24","symbols":["3","3","3","3"],"type":"basic"},{"payout":"48","symbols":["3","3","3","3","3"],"type":"basic"},{"payout":"8","symbols":["4","4","4"],"type":"basic"},{"payout":"16","symbols":["4","4","4","4"],"type":"basic"},{"payout":"32","symbols":["4","4","4","4","4"],"type":"basic"},{"payout":"8","symbols":["5","5","5"],"type":"basic"},{"payout":"16","symbols":["5","5","5","5"],"type":"basic"},{"payout":"32","symbols":["5","5","5","5","5"],"type":"basic"},{"payout":"4","symbols":["6","6","6"],"type":"basic"},{"payout":"8","symbols":["6","6","6","6"],"type":"basic"},{"payout":"16","symbols":["6","6","6","6","6"],"type":"basic"},{"payout":"4","symbols":["7","7","7"],"type":"basic"},{"payout":"8","symbols":["7","7","7","7"],"type":"basic"},{"payout":"16","symbols":["7","7","7","7","7"],"type":"basic"},{"payout":"4","symbols":["8","8","8"],"type":"basic"},{"payout":"8","symbols":["8","8","8","8"],"type":"basic"},{"payout":"16","symbols":["8","8","8","8","8"],"type":"basic"},{"payout":"4","symbols":["9","9","9"],"type":"basic"},{"payout":"8","symbols":["9","9","9","9"],"type":"basic"},{"payout":"16","symbols":["9","9","9","9","9"],"type":"basic"},{"payout":"4","symbols":["10","10","10"],"type":"basic"},{"payout":"8","symbols":["10","10","10","10"],"type":"basic"},{"payout":"16","symbols":["10","10","10","10","10"],"type":"basic"},{"payout":"4","symbols":["11","11","11"],"type":"basic"},{"payout":"8","symbols":["11","11","11","11"],"type":"basic"},{"payout":"16","symbols":["11","11","11","11","11"],"type":"basic"},{"payout":"4","symbols":["12","12","12"],"type":"basic"},{"payout":"8","symbols":["12","12","12","12"],"type":"basic"},{"payout":"16","symbols":["12","12","12","12","12"],"type":"basic"},{"payout":"4","symbols":["13","13","13"],"type":"basic"},{"payout":"8","symbols":["13","13","13","13"],"type":"basic"},{"payout":"16","symbols":["13","13","13","13","13"],"type":"basic"},{"payout":"4","symbols":["14","14","14"],"type":"basic"},{"payout":"8","symbols":["14","14","14","14"],"type":"basic"},{"payout":"16","symbols":["14","14","14","14","14"],"type":"basic"},{"payout":"4","symbols":["15","15","15"],"type":"basic"},{"payout":"8","symbols":["15","15","15","15"],"type":"basic"},{"payout":"16","symbols":["15","15","15","15","15"],"type":"basic"},{"payout":"1","symbols":["100"],"type":"basic"},{"payout":"3","symbols":["101"],"type":"basic"},{"payout":"2","symbols":["102"],"type":"basic"},{"payout":"10","symbols":["103"],"type":"basic"},{"payout":"1","symbols":["105"],"type":"basic"},{"payout":"5","symbols":["106"],"type":"basic"},{"payout":"2","symbols":["107"],"type":"basic"},{"payout":"30","symbols":["108"],"type":"basic"}],"initialSymbols":[["8","8","10","9","7"],["10","10","7","6","8"],["9","9","9","7","10"],["6","6","8","8","9"]]},"jackpotsEnabled":"true","gameModes":"[]"}}';
                                break;
                            case 'BalanceRequest':
                                $result_tmp[] = '{"action":"BalanceResponse","result":"true","sesId":"10000214325","data":{"entries":"0.00","totalAmount":"' . $slotSettings->GetBalance() . '","currency":"' . $slotSettings->slotCurrency . '"}}';
                                break;
                            case 'FreeSpinRequest':
                            case 'SpinRequest':
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $linesId = [];
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[1] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[2] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[3] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[4] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[5] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[6] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[7] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[9] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[11] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[13] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[14] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[15] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[16] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    3, 
                                    2, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[18] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[19] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $lines = 20;
                                $betLine = $postData['data']['coin'] * $postData['data']['bet'];
                                $allbet = $betLine * $lines;
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                if( $reqId == 'FreeSpinRequest' ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
                                $balance = sprintf('%01.2f', $slotSettings->GetBalance());
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $betLine, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        []
                                    ];
                                    $stageWins = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
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
                                    $isBonusStarted = false;
                                    $wild = ['0'];
                                    $scatter = '';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsTmp = $reels;
                                    $reelsOffset = $reels;
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $bonusMpl = 2;
                                    }
                                    $resultStages = '';
                                    for( $stg = 1; $stg <= 10; $stg++ ) 
                                    {
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
                                            0
                                        ];
                                        if( $postData['slotEvent'] == 'freespin' ) 
                                        {
                                            $bonusMpl++;
                                            if( $bonusMpl == 4 ) 
                                            {
                                                $bonusMpl = 5;
                                            }
                                            if( $bonusMpl == 6 ) 
                                            {
                                                $bonusMpl = 10;
                                            }
                                            if( $bonusMpl == 11 ) 
                                            {
                                                $bonusMpl = 15;
                                            }
                                        }
                                        if( $stg > 1 ) 
                                        {
                                            if( $stageWins[$stg - 1] > 0 ) 
                                            {
                                                $reels = $slotSettings->OffsetReels($reelsOffset);
                                                $reelsOffset = $reels;
                                            }
                                            else
                                            {
                                                break;
                                            }
                                        }
                                        for( $k = 0; $k < 20; $k++ ) 
                                        {
                                            $tmpStringWin = '';
                                            for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                            {
                                                $csym = $slotSettings->SymbolGame[$j];
                                                if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                                {
                                                }
                                                else
                                                {
                                                    $s = [];
                                                    $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                    $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                    $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                    $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                                    $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                                    $p0 = $linesId[$k][0] - 1;
                                                    $p1 = $linesId[$k][1] - 1;
                                                    $p2 = $linesId[$k][2] - 1;
                                                    $p3 = $linesId[$k][3] - 1;
                                                    $p4 = $linesId[$k][4] - 1;
                                                    if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                                    {
                                                        $mpl = 1;
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = '{"type":"LineWinAmount","selectedLine":"' . $k . '","amount":"' . $tmpWin . '","wonSymbols":[["0","' . $p0 . '"]]}';
                                                            $reelsOffset['reel1'][$p0] = -1;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                        {
                                                            $mpl = 0;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = '{"type":"LineWinAmount","selectedLine":"' . $k . '","amount":"' . $tmpWin . '","wonSymbols":[["0","' . $p0 . '"],["1","' . $p1 . '"]]}';
                                                            $reelsOffset['reel1'][$p0] = -1;
                                                            $reelsOffset['reel2'][$p1] = -1;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                        {
                                                            $mpl = 0;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = '{"type":"LineWinAmount","selectedLine":"' . $k . '","amount":"' . $tmpWin . '","wonSymbols":[["0","' . $p0 . '"],["1","' . $p1 . '"],["2","' . $p2 . '"]]}';
                                                            $reelsOffset['reel1'][$p0] = -1;
                                                            $reelsOffset['reel2'][$p1] = -1;
                                                            $reelsOffset['reel3'][$p2] = -1;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                        {
                                                            $mpl = 0;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = '{"type":"LineWinAmount","selectedLine":"' . $k . '","amount":"' . $tmpWin . '","wonSymbols":[["0","' . $p0 . '"],["1","' . $p1 . '"],["2","' . $p2 . '"],["3","' . $p3 . '"]]}';
                                                            $reelsOffset['reel1'][$p0] = -1;
                                                            $reelsOffset['reel2'][$p1] = -1;
                                                            $reelsOffset['reel3'][$p2] = -1;
                                                            $reelsOffset['reel4'][$p3] = -1;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                        {
                                                            $mpl = 0;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = '{"type":"LineWinAmount","selectedLine":"' . $k . '","amount":"' . $tmpWin . '","wonSymbols":[["0","' . $p0 . '"],["1","' . $p1 . '"],["2","' . $p2 . '"],["3","' . $p3 . '"],["4","' . $p4 . '"]]}';
                                                            $reelsOffset['reel1'][$p0] = -1;
                                                            $reelsOffset['reel2'][$p1] = -1;
                                                            $reelsOffset['reel3'][$p2] = -1;
                                                            $reelsOffset['reel4'][$p3] = -1;
                                                            $reelsOffset['reel5'][$p4] = -1;
                                                        }
                                                    }
                                                }
                                            }
                                            if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                            {
                                                array_push($lineWins[$stg], $tmpStringWin);
                                                $totalWin += $cWins[$k];
                                                $stageWins[$stg] += $cWins[$k];
                                            }
                                        }
                                        $scattersWin = 0;
                                        $scattersWinB = 0;
                                        $scattersPos = [];
                                        $scattersStr = '';
                                        $scattersCount = 0;
                                        $bSym = $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol');
                                        $bSymCnt = 0;
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            $isScat = false;
                                            for( $p = 0; $p <= 2; $p++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == $scatter ) 
                                                {
                                                    $reelsOffset['reel' . $r][$p] = -1;
                                                    $scattersPos[] = '["' . ($r - 1) . '","' . $p . '"]';
                                                    $isScat = true;
                                                }
                                            }
                                        }
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            $isScat = false;
                                            for( $p = 0; $p <= 2; $p++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == $bSym ) 
                                                {
                                                    $bSymCnt++;
                                                    break;
                                                }
                                            }
                                        }
                                        $gameState = 'Ready';
                                        if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                        {
                                            $scw = '{"wonSymbols":[' . implode(',', $scattersPos) . '],"amount":"' . $slotSettings->FormatFloat($scattersWin) . '","type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"15"}}';
                                            array_push($lineWins[$stg], $scw);
                                            $isBonusStarted = true;
                                        }
                                        $totalWin += $scattersWin;
                                        $stageWins[$stg] += $scattersWin;
                                        if( $stg > 1 ) 
                                        {
                                            $symb = '["1","1","1","1","1"],["' . $reels['reel1'][0] . '","' . $reels['reel2'][0] . '","' . $reels['reel3'][0] . '","' . $reels['reel4'][0] . '","' . $reels['reel5'][0] . '"],["' . $reels['reel1'][1] . '","' . $reels['reel2'][1] . '","' . $reels['reel3'][1] . '","' . $reels['reel4'][1] . '","' . $reels['reel5'][1] . '"],["' . $reels['reel1'][2] . '","' . $reels['reel2'][2] . '","' . $reels['reel3'][2] . '","' . $reels['reel4'][2] . '","' . $reels['reel5'][2] . '"],["' . $reels['reel1'][3] . '","' . $reels['reel2'][3] . '","' . $reels['reel3'][3] . '","' . $reels['reel4'][3] . '","' . $reels['reel5'][3] . '"]';
                                            $resultStages .= ('"spinResultStage' . $stg . '":{"type":"SpinResult","rows":[' . $symb . ']},');
                                        }
                                    }
                                    if( $i > 300 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 700 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                    {
                                    }
                                    else if( $stg < 7 && $winType == 'bonus' ) 
                                    {
                                    }
                                    else if( $stg >= 7 && $winType != 'bonus' ) 
                                    {
                                    }
                                    else if( $totalWin <= $spinWinLimit && ($winType == 'bonus' || $isBonusStarted) ) 
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
                                $flag = 0;
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $flag = 6;
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
                                if( $scattersCount >= 3 || $isBonusStarted ) 
                                {
                                    $gameState = 'FreeSpins';
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
                                }
                                $reels = $reelsTmp;
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                if( $stg == 7 ) 
                                {
                                    $gameState = 'FreeSpins';
                                    $scw = '{"wonSymbols":"","type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"5"}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + 5);
                                    array_push($lineWins[$stg - 1], $scw);
                                }
                                if( $stg == 8 ) 
                                {
                                    $gameState = 'FreeSpins';
                                    $scw = '{"wonSymbols":"","type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"7"}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + 7);
                                    array_push($lineWins[$stg - 1], $scw);
                                }
                                if( $stg >= 9 ) 
                                {
                                    $gameState = 'FreeSpins';
                                    $scw = '{"wonSymbols":"","type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"10"}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + 10);
                                    array_push($lineWins[$stg - 1], $scw);
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $winString0 = implode(',', $lineWins[1]);
                                    $winString = ',"slotWin":{"lineWinAmounts":[' . $winString0 . '],"totalWin":"' . $slotSettings->FormatFloat($totalWin) . '"';
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $bonusMpl = 2;
                                    }
                                    for( $sw = 2; $sw <= 15; $sw++ ) 
                                    {
                                        if( count($lineWins[$sw]) > 0 ) 
                                        {
                                            $winString .= (',"lineWinAmountsStage' . $sw . '":[' . implode(',', $lineWins[$sw]) . ',{"type":"Bonus","bonusName":"Multiplier","wonSymbols":"","params":{"value":"' . $bonusMpl . '"}}]');
                                            if( $postData['slotEvent'] == 'freespin' ) 
                                            {
                                                $bonusMpl++;
                                                if( $bonusMpl == 4 ) 
                                                {
                                                    $bonusMpl = 5;
                                                }
                                                if( $bonusMpl == 6 ) 
                                                {
                                                    $bonusMpl = 10;
                                                }
                                                if( $bonusMpl == 11 ) 
                                                {
                                                    $bonusMpl = 15;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $winString .= (',"lineWinAmountsStage' . $sw . '":[{"type":"Bonus","bonusName":"Multiplier","wonSymbols":"","params":{"value":"' . $bonusMpl . '"}}]');
                                            break;
                                        }
                                    }
                                    $winString .= ',"canGamble":"false"}';
                                }
                                else
                                {
                                    $winString = '';
                                }
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusSymbol":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = '["1","1","1","1","1"],["' . $reelsTmp['reel1'][0] . '","' . $reelsTmp['reel2'][0] . '","' . $reelsTmp['reel3'][0] . '","' . $reelsTmp['reel4'][0] . '","' . $reelsTmp['reel5'][0] . '"],["' . $reelsTmp['reel1'][1] . '","' . $reelsTmp['reel2'][1] . '","' . $reelsTmp['reel3'][1] . '","' . $reelsTmp['reel4'][1] . '","' . $reelsTmp['reel5'][1] . '"],["' . $reelsTmp['reel1'][2] . '","' . $reelsTmp['reel2'][2] . '","' . $reelsTmp['reel3'][2] . '","' . $reelsTmp['reel4'][2] . '","' . $reelsTmp['reel5'][2] . '"],["' . $reelsTmp['reel1'][3] . '","' . $reelsTmp['reel2'][3] . '","' . $reelsTmp['reel3'][3] . '","' . $reelsTmp['reel4'][3] . '","' . $reelsTmp['reel5'][3] . '"]';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $bonusWin0 = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                    $freeSpinRemain = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $freeSpinsTotal = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $gameState = 'FreeSpins';
                                    $gameParameters = '';
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $gameState = 'Ready';
                                        $gameParameters = '"gameParameters":{"initialSymbols":[' . $slotSettings->GetGameData($slotSettings->slotId . 'initialSymbols') . ']},';
                                    }
                                    $result_tmp[] = '{"action":"FreeSpinResponse","result":"true","sesId":"10000228087","data":{' . $gameParameters . '"state":"' . $gameState . '"' . $winString . ',' . $resultStages . '"spinResult":{"type":"SpinResult","rows":[' . $symb . ']},"totalBonusWin":"' . $slotSettings->FormatFloat($bonusWin0) . '","freeSpinRemain":"' . $freeSpinRemain . '","freeSpinsTotal":"' . $freeSpinsTotal . '"}}';
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'initialSymbols', $symb);
                                    $result_tmp[] = '{"action":"SpinResponse","result":"true","sesId":"10000217909","data":{"state":"' . $gameState . '"' . $winString . ',' . $resultStages . '"spinResult":{"type":"SpinResult","rows":[' . $symb . ']}}}';
                                }
                                break;
                        }
                        $response = implode('------', $result_tmp);
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
