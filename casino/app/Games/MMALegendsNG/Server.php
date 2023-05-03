<?php 
namespace VanguardLTE\Games\MMALegendsNG
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
                                $result_tmp[] = '{"action":"APIVersionResponse","result":true,"sesId":false,"data":{"router":"v3.12","transportConfig":{"reconnectTimeout":500000000000}}}';
                                break;
                            case 'BetSlipPlayRequest':
                                $betId = $postData['data']['betId'];
                                $bet = $postData['data']['bet'];
                                if( $slotSettings->GetBalance() < $bet || $bet <= 0 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance or bet"}';
                                    exit( $response );
                                }
                                $slotSettings->SetBalance(-1 * $bet, 'bet');
                                $bankSum = $bet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank('bet', $bankSum, 'bet');
                                $slotSettings->UpdateJackpots($bet);
                                $BetSlipData = $slotSettings->GetGameData($slotSettings->slotId . 'BetSlipData');
                                if( !isset($BetSlipData['data']['payouts'][$betId]) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet"}';
                                    exit( $response );
                                }
                                $BetSlipData['data']['payouts'][$betId]['finished'] = 'false';
                                $BetSlipData['data']['payouts'][$betId]['bet'] = $bet;
                                $BetSlipData['data']['payouts'][$betId]['win'] = 0;
                                $BetSlipData['data']['payouts'][$betId]['factor'] = $BetSlipData['data']['payouts'][$betId]['factor'] * $bet;
                                $slotSettings->SetGameData($slotSettings->slotId . 'BetSlipCurrent', $BetSlipData['data']['payouts'][$betId]);
                                $slotSettings->SaveLogReport('NULL', $bet, 1, 0, 'BS');
                                $result_tmp[] = '{"action":"BetSlipPlayResponse","result":"true","sesId":"10000615303","data":""}';
                                break;
                            case 'CheckBrokenGameRequest':
                                $result_tmp[] = '{"action":"CheckBrokenGameResponse","result":"true","sesId":"false","data":{"haveBrokenGame":"false"}}';
                                break;
                            case 'BetSlipGetPayoutsRequest':
                                $rs = '{"action":"BetSlipGetPayoutsResponse","result":"true","sesId":"10000580268","data":{"payouts":[{"id":"0","symbol":"1","factor":"7.7999999999999998","toCollect":"78","spins":"27","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"1","symbol":"1","factor":"19.699999999999999","toCollect":"106","spins":"35","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"2","symbol":"1","factor":"1000","toCollect":"200","spins":"60","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"3","symbol":"2","factor":"6.2999999999999998","toCollect":"34","spins":"20","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"4","symbol":"2","factor":"17.699999999999999","toCollect":"56","spins":"32","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"5","symbol":"2","factor":"420","toCollect":"77","spins":"40","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"6","symbol":"3","factor":"5.4000000000000004","toCollect":"39","spins":"15","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"7","symbol":"3","factor":"12","toCollect":"66","spins":"25","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"8","symbol":"3","factor":"116","toCollect":"100","spins":"36","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"9","symbol":"4","factor":"4.0999999999999996","toCollect":"14","spins":"13","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"10","symbol":"4","factor":"10","toCollect":"25","spins":"22","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"11","symbol":"4","factor":"56","toCollect":"37","spins":"30","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"12","symbol":"5","factor":"2.8999999999999999","toCollect":"9","spins":"7","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"13","symbol":"5","factor":"8.4499999999999993","toCollect":"21","spins":"15","bets":["1","2.5","5","7.5","10","15","25","50"]},{"id":"14","symbol":"5","factor":"34.5","toCollect":"34","spins":"23","bets":["1","2.5","5","7.5","10","15","25","50"]}]}}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'BetSlipData', json_decode($rs, true));
                                $result_tmp[] = $rs;
                                break;
                            case 'AuthRequest':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BetSlipCurrent', 0);
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
                                    $restoreString = ',"restoredGameCode":"278","lastResponse":{"spinResult":{"type":"SpinResult","rows":[' . $rp2 . ']},"freeSpinsTotal":"' . $fTotal . '","freeSpinRemain":"' . $fRemain . '","totalBonusWin":"' . $fBonusWin . '","state":"FreeSpins","expandingSymbols":["1"]}';
                                }
                                $result_tmp[0] = '{"action":"AuthResponse","result":"true","sesId":"10000580268","data":{"snivy":"proxy DEV-v10.15.73 (API v4.16)","bets":["1","2","3","4","5","10","15","20","30","40","50","100","200","300"],"coinValues":["0.01"],"betMultiplier":"0.6000000","defaultCoinValue":"0.01","defaultBet":"1","jackpotsEnabled":"false","defaultLines":["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49"],"supportedFeatures":["Offers","Jackpots","PaidJackpots","InstantJackpots","SweepStakes"],"sessionId":"10000580268","gameParameters":{"availableLines":[["0","0","0","0","0"],["0","0","1","0","0"],["0","1","1","1","0"],["0","1","2","1","0"],["0","1","0","1","0"],["0","0","0","1","0"],["0","1","0","0","0"],["0","0","1","1","0"],["0","1","1","0","0"],["0","0","2","0","0"],["0","0","2","1","0"],["0","1","2","0","0"],["1","1","1","1","1"],["1","1","2","1","1"],["1","1","0","1","1"],["1","2","2","2","1"],["1","0","0","0","1"],["1","2","1","2","1"],["1","0","1","0","1"],["1","1","1","2","1"],["1","1","1","0","1"],["1","2","1","1","1"],["1","0","1","1","1"],["1","2","0","2","1"],["1","0","2","0","1"],["2","2","2","2","2"],["2","2","3","2","2"],["2","2","1","2","2"],["2","3","3","3","2"],["2","1","1","1","2"],["2","3","2","3","2"],["2","1","2","1","2"],["2","2","2","3","2"],["2","2","2","1","2"],["2","3","2","2","2"],["2","1","2","2","2"],["2","3","1","3","2"],["2","1","3","1","2"],["3","3","3","3","3"],["3","3","2","3","3"],["3","2","2","2","3"],["3","2","1","2","3"],["3","2","3","2","3"],["3","3","3","2","3"],["3","2","3","3","3"],["3","3","2","2","3"],["3","2","2","3","3"],["3","3","1","3","3"],["3","3","1","2","3"],["3","2","1","3","3"]],"rtp":"96.56","initialSymbols":[["5","5","0","1","2"],["10","8","10","8","0"],["2","2","8","9","9"],["11","6","3","0","2"]],"payouts":[{"payout":"2","symbols":["1","1"],"type":"basic"},{"payout":"10","symbols":["1","1","1"],"type":"basic"},{"payout":"25","symbols":["1","1","1","1"],"type":"basic"},{"payout":"50","symbols":["1","1","1","1","1"],"type":"basic"},{"payout":"2","symbols":["2","2"],"type":"basic"},{"payout":"10","symbols":["2","2","2"],"type":"basic"},{"payout":"25","symbols":["2","2","2","2"],"type":"basic"},{"payout":"40","symbols":["2","2","2","2","2"],"type":"basic"},{"payout":"5","symbols":["3","3","3"],"type":"basic"},{"payout":"15","symbols":["3","3","3","3"],"type":"basic"},{"payout":"30","symbols":["3","3","3","3","3"],"type":"basic"},{"payout":"5","symbols":["4","4","4"],"type":"basic"},{"payout":"15","symbols":["4","4","4","4"],"type":"basic"},{"payout":"30","symbols":["4","4","4","4","4"],"type":"basic"},{"payout":"5","symbols":["5","5","5"],"type":"basic"},{"payout":"15","symbols":["5","5","5","5"],"type":"basic"},{"payout":"30","symbols":["5","5","5","5","5"],"type":"basic"},{"payout":"5","symbols":["6","6","6"],"type":"basic"},{"payout":"10","symbols":["6","6","6","6"],"type":"basic"},{"payout":"25","symbols":["6","6","6","6","6"],"type":"basic"},{"payout":"5","symbols":["7","7","7"],"type":"basic"},{"payout":"10","symbols":["7","7","7","7"],"type":"basic"},{"payout":"25","symbols":["7","7","7","7","7"],"type":"basic"},{"payout":"4","symbols":["8","8","8"],"type":"basic"},{"payout":"10","symbols":["8","8","8","8"],"type":"basic"},{"payout":"25","symbols":["8","8","8","8","8"],"type":"basic"},{"payout":"4","symbols":["9","9","9"],"type":"basic"},{"payout":"10","symbols":["9","9","9","9"],"type":"basic"},{"payout":"25","symbols":["9","9","9","9","9"],"type":"basic"},{"payout":"4","symbols":["10","10","10"],"type":"basic"},{"payout":"10","symbols":["10","10","10","10"],"type":"basic"},{"payout":"25","symbols":["10","10","10","10","10"],"type":"basic"},{"payout":"4","symbols":["11","11","11"],"type":"basic"},{"payout":"10","symbols":["11","11","11","11"],"type":"basic"},{"payout":"25","symbols":["11","11","11","11","11"],"type":"basic"},{"payout":"2","symbols":["12","12","12"],"type":"scatter"},{"payout":"1","symbols":["14","14"],"type":"basic"},{"payout":"5","symbols":["14","14","14"],"type":"basic"},{"payout":"15","symbols":["14","14","14","14"],"type":"basic"},{"payout":"25","symbols":["14","14","14","14","14"],"type":"basic"},{"payout":"1","symbols":["15","15"],"type":"basic"},{"payout":"5","symbols":["15","15","15"],"type":"basic"},{"payout":"15","symbols":["15","15","15","15"],"type":"basic"},{"payout":"20","symbols":["15","15","15","15","15"],"type":"basic"},{"payout":"2","symbols":["16","16","16"],"type":"basic"},{"payout":"10","symbols":["16","16","16","16"],"type":"basic"},{"payout":"15","symbols":["16","16","16","16","16"],"type":"basic"},{"payout":"2","symbols":["17","17","17"],"type":"basic"},{"payout":"10","symbols":["17","17","17","17"],"type":"basic"},{"payout":"15","symbols":["17","17","17","17","17"],"type":"basic"},{"payout":"2","symbols":["18","18","18"],"type":"basic"},{"payout":"10","symbols":["18","18","18","18"],"type":"basic"},{"payout":"15","symbols":["18","18","18","18","18"],"type":"basic"},{"payout":"2","symbols":["19","19","19"],"type":"basic"},{"payout":"10","symbols":["19","19","19","19"],"type":"basic"},{"payout":"15","symbols":["19","19","19","19","19"],"type":"basic"},{"payout":"1","symbols":["20","20","20"],"type":"basic"},{"payout":"4","symbols":["20","20","20","20"],"type":"basic"},{"payout":"5","symbols":["20","20","20","20","20"],"type":"basic"},{"payout":"1","symbols":["21","21","21"],"type":"basic"},{"payout":"4","symbols":["21","21","21","21"],"type":"basic"},{"payout":"5","symbols":["21","21","21","21","21"],"type":"basic"},{"payout":"1","symbols":["22","22","22"],"type":"basic"},{"payout":"4","symbols":["22","22","22","22"],"type":"basic"},{"payout":"5","symbols":["22","22","22","22","22"],"type":"basic"},{"payout":"1","symbols":["23","23","23"],"type":"basic"},{"payout":"4","symbols":["23","23","23","23"],"type":"basic"},{"payout":"5","symbols":["23","23","23","23","23"],"type":"basic"},{"payout":"1","symbols":["24","24","24"],"type":"basic"},{"payout":"4","symbols":["24","24","24","24"],"type":"basic"},{"payout":"5","symbols":["24","24","24","24","24"],"type":"basic"},{"payout":"2","symbols":["25","25","25"],"type":"scatter"}]},"gameModes":""}}';
                                break;
                            case 'BalanceRequest':
                                $result_tmp[] = '{"action":"BalanceResponse","result":"true","sesId":"10000214325","data":{"entries":"0.00","totalAmount":"' . $slotSettings->GetBalance() . '","currency":"' . $slotSettings->slotCurrency . '"}}';
                                $BetSlipCurrent = $slotSettings->GetGameData($slotSettings->slotId . 'BetSlipCurrent');
                                if( isset($BetSlipCurrent['spins']) && ($BetSlipCurrent['spins'] >= 0 || $BetSlipCurrent['finished'] == 'true') ) 
                                {
                                    if( $BetSlipCurrent['finished'] == 'true' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BetSlipCurrent', 0);
                                    }
                                    $win = $BetSlipCurrent['win'];
                                    $result_tmp[] = ':::{"action":"BetSlipUpdateResponse","result":"true","sesId":"10000615303","data":{"bets":[{"payoutId":"' . $BetSlipCurrent['id'] . '","bet":"' . $BetSlipCurrent['bet'] . '","pays":"' . $BetSlipCurrent['factor'] . '","toCollect":"' . $BetSlipCurrent['toCollect'] . '","actionsLeft":"' . $BetSlipCurrent['spins'] . '","win":"' . $win . '","finished":"' . $BetSlipCurrent['finished'] . '"}]}}';
                                }
                                break;
                            case 'FreeSpinRequest':
                            case 'SpinRequest':
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
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
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[2] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[3] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
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
                                    1, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[6] = [
                                    1, 
                                    2, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[7] = [
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    1, 
                                    2, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[9] = [
                                    1, 
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[10] = [
                                    1, 
                                    1, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[11] = [
                                    1, 
                                    2, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[15] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[16] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[18] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[21] = [
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[22] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[23] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[25] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[26] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
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
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[29] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[30] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[31] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[32] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[33] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[34] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[35] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[36] = [
                                    3, 
                                    4, 
                                    2, 
                                    4, 
                                    3
                                ];
                                $linesId[37] = [
                                    3, 
                                    2, 
                                    4, 
                                    2, 
                                    3
                                ];
                                $linesId[38] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[39] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[40] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[41] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[42] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[43] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[44] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[45] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[46] = [
                                    4, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[47] = [
                                    4, 
                                    4, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $linesId[48] = [
                                    4, 
                                    4, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[49] = [
                                    4, 
                                    3, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $lines = 30;
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'WildsArr', []);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'GameState', 'Spin');
                                    $slotSettings->Paytable['SYM_0'] = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $slotSettings->Paytable['SYM_1'] = [
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        25, 
                                        40
                                    ];
                                    $slotSettings->Paytable['SYM_2'] = [
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        25, 
                                        40
                                    ];
                                    $slotSettings->Paytable['SYM_3'] = [
                                        0, 
                                        0, 
                                        0, 
                                        5, 
                                        15, 
                                        30
                                    ];
                                    $slotSettings->Paytable['SYM_4'] = [
                                        0, 
                                        0, 
                                        0, 
                                        5, 
                                        15, 
                                        30
                                    ];
                                    $slotSettings->Paytable['SYM_5'] = [
                                        0, 
                                        0, 
                                        0, 
                                        5, 
                                        15, 
                                        30
                                    ];
                                    $slotSettings->Paytable['SYM_6'] = [
                                        0, 
                                        0, 
                                        0, 
                                        5, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_7'] = [
                                        0, 
                                        0, 
                                        0, 
                                        5, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_8'] = [
                                        0, 
                                        0, 
                                        0, 
                                        4, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_9'] = [
                                        0, 
                                        0, 
                                        0, 
                                        4, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_10'] = [
                                        0, 
                                        0, 
                                        0, 
                                        4, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_11'] = [
                                        0, 
                                        0, 
                                        0, 
                                        4, 
                                        10, 
                                        25
                                    ];
                                    $slotSettings->Paytable['SYM_12'] = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                }
                                else
                                {
                                    $slotSettings->Paytable['SYM_0'] = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $slotSettings->Paytable['SYM_1'] = [
                                        0, 
                                        0, 
                                        1, 
                                        5, 
                                        15, 
                                        20
                                    ];
                                    $slotSettings->Paytable['SYM_2'] = [
                                        0, 
                                        0, 
                                        1, 
                                        5, 
                                        15, 
                                        20
                                    ];
                                    $slotSettings->Paytable['SYM_3'] = [
                                        0, 
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        15
                                    ];
                                    $slotSettings->Paytable['SYM_4'] = [
                                        0, 
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        15
                                    ];
                                    $slotSettings->Paytable['SYM_5'] = [
                                        0, 
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        15
                                    ];
                                    $slotSettings->Paytable['SYM_6'] = [
                                        0, 
                                        0, 
                                        0, 
                                        2, 
                                        10, 
                                        15
                                    ];
                                    $slotSettings->Paytable['SYM_7'] = [
                                        0, 
                                        0, 
                                        0, 
                                        1, 
                                        4, 
                                        5
                                    ];
                                    $slotSettings->Paytable['SYM_8'] = [
                                        0, 
                                        0, 
                                        0, 
                                        1, 
                                        4, 
                                        5
                                    ];
                                    $slotSettings->Paytable['SYM_9'] = [
                                        0, 
                                        0, 
                                        0, 
                                        1, 
                                        4, 
                                        5
                                    ];
                                    $slotSettings->Paytable['SYM_10'] = [
                                        0, 
                                        0, 
                                        0, 
                                        1, 
                                        4, 
                                        5
                                    ];
                                    $slotSettings->Paytable['SYM_11'] = [
                                        0, 
                                        0, 
                                        0, 
                                        1, 
                                        4, 
                                        5
                                    ];
                                    $slotSettings->Paytable['SYM_12'] = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
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
                                        0
                                    ];
                                    $wild = ['0'];
                                    $scatter = '12';
                                    $scatter2 = '0';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsTmp = $reels;
                                    for( $k = 0; $k < 50; $k++ ) 
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
                                    $BetSlipCurrent = $slotSettings->GetGameData($slotSettings->slotId . 'BetSlipCurrent');
                                    if( isset($BetSlipCurrent['spins']) && $BetSlipCurrent['spins'] >= 0 ) 
                                    {
                                        $bsSym = $BetSlipCurrent['symbol'];
                                        if( $winType == 'bonus' ) 
                                        {
                                            $winType = 'win';
                                        }
                                    }
                                    else
                                    {
                                        $bsSym = -1;
                                    }
                                    $scattersWin = 0;
                                    $scattersWinB = 0;
                                    $scattersPos2 = [];
                                    $scattersPos = [];
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scattersCount2 = 0;
                                    $bSym = $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol');
                                    $bSymCnt = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scattersPos[] = '["' . ($r - 1) . '","' . $p . '"]';
                                                $isScat = true;
                                            }
                                            if( $reels['reel' . $r][$p] == '0' ) 
                                            {
                                                $scattersCount2++;
                                                $scattersPos2[] = '["' . ($r - 1) . '","' . $p . '"]';
                                            }
                                            if( $reels['reel' . $r][$p] == $bsSym ) 
                                            {
                                                $BetSlipCurrent['toCollect']--;
                                            }
                                        }
                                    }
                                    if( isset($BetSlipCurrent['spins']) && $BetSlipCurrent['spins'] > 0 ) 
                                    {
                                        $BetSlipCurrent['spins']--;
                                        if( $BetSlipCurrent['toCollect'] <= 0 ) 
                                        {
                                            $BetSlipCurrent['toCollect'] = 0;
                                            $BetSlipCurrent['spins'] = 0;
                                            $BetSlipCurrent['finished'] = 'true';
                                            $BetSlipCurrent['win'] = $BetSlipCurrent['factor'];
                                            $scattersWinB = $BetSlipCurrent['factor'];
                                        }
                                        else if( $BetSlipCurrent['spins'] <= 0 ) 
                                        {
                                            $BetSlipCurrent['spins'] = 0;
                                            $BetSlipCurrent['finished'] = 'true';
                                            $BetSlipCurrent['win'] = 0;
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betLine * $bonusMpl;
                                    $gameState = 'Ready';
                                    if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                    {
                                        $gameState = 'FreeSpins';
                                        $slotSettings->SetGameData($slotSettings->slotId . 'GameState', 'FreeSpin');
                                        $scw = '{"type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"' . $slotSettings->slotFreeCount . '"},"amount":"' . $slotSettings->FormatFloat($scattersWin) . '","wonSymbols":[' . implode(',', $scattersPos) . ']}';
                                        array_push($lineWins, $scw);
                                    }
                                    if( $scattersCount2 >= 4 && $slotSettings->slotBonus ) 
                                    {
                                        $gameState = 'ReSpins';
                                        $slotSettings->SetGameData($slotSettings->slotId . 'GameState', 'Respin');
                                        $scw = '{"type":"Bonus","bonusName":"ReSpins","params":{"reSpins":"1"},"amount":"0.00","wonSymbols":[' . implode(',', $scattersPos2) . ']}';
                                        array_push($lineWins, $scw);
                                    }
                                    $totalWin += ($scattersWin + $scattersWinB);
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
                                        else
                                        {
                                            if( $i > 1500 ) 
                                            {
                                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"Bad Reel Strip"}';
                                                exit( $response );
                                            }
                                            if( $scattersCount2 >= 4 && $scattersCount >= 3 ) 
                                            {
                                            }
                                            else if( $scattersCount2 >= 4 && $winType != 'bonus' ) 
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
                                }
                                $reels = $reelsTmp;
                                $slotSettings->SetGameData($slotSettings->slotId . 'WildsCount', $scattersCount2);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                if( $scattersWinB > 0 ) 
                                {
                                    $totalWin -= $scattersWinB;
                                }
                                if( count($lineWins) > 0 ) 
                                {
                                    $winString = ',"slotWin":{"totalWin":"' . $totalWin . '","lineWinAmounts":[' . implode(',', $lineWins) . '],"canGamble":"false"}';
                                }
                                else
                                {
                                    $winString = ',"slotWin":{"totalWin":"' . $totalWin . '","lineWinAmounts":[],"canGamble":"false"}';
                                }
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusSymbol":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = '["' . $reels['reel1'][0] . '","' . $reels['reel2'][0] . '","' . $reels['reel3'][0] . '","' . $reels['reel4'][0] . '","' . $reels['reel5'][0] . '"],["' . $reels['reel1'][1] . '","' . $reels['reel2'][1] . '","' . $reels['reel3'][1] . '","' . $reels['reel4'][1] . '","' . $reels['reel5'][1] . '"],["' . $reels['reel1'][2] . '","' . $reels['reel2'][2] . '","' . $reels['reel3'][2] . '","' . $reels['reel4'][2] . '","' . $reels['reel5'][2] . '"],["' . $reels['reel1'][3] . '","' . $reels['reel2'][3] . '","' . $reels['reel3'][3] . '","' . $reels['reel4'][3] . '","' . $reels['reel5'][3] . '"]';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BetSlipCurrent', $BetSlipCurrent);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Reels', $reels);
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
                                    $result_tmp[] = '{"action":"FreeSpinResponse","result":"true","sesId":"10000228087","data":{' . $gameParameters . '"state":"' . $gameState . '"' . $winString . ',"spinResult":{"type":"SpinResult","rows":[' . $symb . ']},"totalBonusWin":"' . $slotSettings->FormatFloat($bonusWin0) . '","freeSpinRemain":"' . $freeSpinRemain . '","freeSpinsTotal":"' . $freeSpinsTotal . '"}}';
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'initialSymbols', $symb);
                                    $result_tmp[] = '{"action":"SpinResponse","result":"true","sesId":"10000373695","data":{"spinResult":{"type":"SpinResult","rows":[' . $symb . ']}' . $winString . ',"state":"' . $gameState . '"}}';
                                }
                                break;
                            case 'RespinRequest':
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'GameState') != 'Respin' ) 
                                {
                                    exit();
                                }
                                $postData['slotEvent'] = 'freespin';
                                $bonusMpl = 1;
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
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[2] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[3] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
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
                                    1, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[6] = [
                                    1, 
                                    2, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[7] = [
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    1, 
                                    2, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[9] = [
                                    1, 
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[10] = [
                                    1, 
                                    1, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[11] = [
                                    1, 
                                    2, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[15] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[16] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[18] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[21] = [
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[22] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[23] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[25] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[26] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
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
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[29] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[30] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[31] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[32] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[33] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[34] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[35] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[36] = [
                                    3, 
                                    4, 
                                    2, 
                                    4, 
                                    3
                                ];
                                $linesId[37] = [
                                    3, 
                                    2, 
                                    4, 
                                    2, 
                                    3
                                ];
                                $linesId[38] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[39] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[40] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[41] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[42] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[43] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[44] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[45] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[46] = [
                                    4, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[47] = [
                                    4, 
                                    4, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $linesId[48] = [
                                    4, 
                                    4, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[49] = [
                                    4, 
                                    3, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $lines = 30;
                                $betLine = $postData['data']['coin'] * $postData['data']['bet'];
                                $allbet = $betLine * $lines;
                                $slotSettings->Paytable['SYM_0'] = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $slotSettings->Paytable['SYM_1'] = [
                                    0, 
                                    0, 
                                    1, 
                                    5, 
                                    15, 
                                    20
                                ];
                                $slotSettings->Paytable['SYM_2'] = [
                                    0, 
                                    0, 
                                    1, 
                                    5, 
                                    15, 
                                    20
                                ];
                                $slotSettings->Paytable['SYM_3'] = [
                                    0, 
                                    0, 
                                    0, 
                                    2, 
                                    10, 
                                    15
                                ];
                                $slotSettings->Paytable['SYM_4'] = [
                                    0, 
                                    0, 
                                    0, 
                                    2, 
                                    10, 
                                    15
                                ];
                                $slotSettings->Paytable['SYM_5'] = [
                                    0, 
                                    0, 
                                    0, 
                                    2, 
                                    10, 
                                    15
                                ];
                                $slotSettings->Paytable['SYM_6'] = [
                                    0, 
                                    0, 
                                    0, 
                                    2, 
                                    10, 
                                    15
                                ];
                                $slotSettings->Paytable['SYM_7'] = [
                                    0, 
                                    0, 
                                    0, 
                                    1, 
                                    4, 
                                    5
                                ];
                                $slotSettings->Paytable['SYM_8'] = [
                                    0, 
                                    0, 
                                    0, 
                                    1, 
                                    4, 
                                    5
                                ];
                                $slotSettings->Paytable['SYM_9'] = [
                                    0, 
                                    0, 
                                    0, 
                                    1, 
                                    4, 
                                    5
                                ];
                                $slotSettings->Paytable['SYM_10'] = [
                                    0, 
                                    0, 
                                    0, 
                                    1, 
                                    4, 
                                    5
                                ];
                                $slotSettings->Paytable['SYM_11'] = [
                                    0, 
                                    0, 
                                    0, 
                                    1, 
                                    4, 
                                    5
                                ];
                                $slotSettings->Paytable['SYM_12'] = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $bonusMpl = $slotSettings->slotFreeMpl;
                                $balance = sprintf('%01.2f', $slotSettings->GetBalance());
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $betLine, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
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
                                        0
                                    ];
                                    $wild = ['0'];
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsTmp = $reels;
                                    $respinEnd = false;
                                    $WildsCountOld = $slotSettings->GetGameData($slotSettings->slotId . 'WildsCount');
                                    $WildsArr = $slotSettings->GetGameData($slotSettings->slotId . 'WildsArr');
                                    $wildReels = $slotSettings->GetGameData($slotSettings->slotId . 'Reels');
                                    $lineCount = 0;
                                    $wildsCount = 0;
                                    for( $r = 2; $r <= 5; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $wildReels['reel' . $r][$p] == '0' ) 
                                            {
                                                $reels['reel' . $r][$p] = '0';
                                            }
                                        }
                                    }
                                    $scattersPos = [];
                                    $wildsCount = 0;
                                    for( $r = 2; $r <= 5; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '0' && $wildReels['reel' . $r][$p] != '0' ) 
                                            {
                                                $scattersPos[] = '["' . ($r - 1) . '","' . $p . '"]';
                                            }
                                            if( $reels['reel' . $r][$p] == '0' ) 
                                            {
                                                $wildsCount++;
                                            }
                                        }
                                    }
                                    $WildsArr[] = implode(',', $scattersPos);
                                    if( $wildsCount <= $WildsCountOld ) 
                                    {
                                        $respinEnd = true;
                                        $lineCount = 50;
                                    }
                                    for( $k = 0; $k < $lineCount; $k++ ) 
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
                                    $gameState = 'ReSpins';
                                    if( $respinEnd ) 
                                    {
                                        $gameState = 'Ready';
                                        $slotSettings->SetGameData($slotSettings->slotId . 'GameState', 'Main');
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    if( $totalWin <= $cBank ) 
                                    {
                                        break;
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $reportWin = $totalWin;
                                $lineWins[] = '{"type":"Bonus","bonusName":"ReSpins","params":{"reSpins":"1"},"amount":"0.00","wonSymbols":[' . implode(',', $scattersPos) . ']}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'Reels', $reels);
                                $slotSettings->SetGameData($slotSettings->slotId . 'WildsArr', $WildsArr);
                                $slotSettings->SetGameData($slotSettings->slotId . 'WildsCount', $wildsCount);
                                $reels = $reelsTmp;
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                if( count($lineWins) > 0 ) 
                                {
                                    $winString = ',"slotWin":{"totalWin":"' . $totalWin . '","lineWinAmounts":[' . implode(',', $lineWins) . '],"canGamble":"false"}';
                                }
                                else
                                {
                                    $winString = ',"slotWin":{"totalWin":"' . $totalWin . '","lineWinAmounts":[],"canGamble":"false"}';
                                }
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusSymbol":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = '["' . $reels['reel1'][0] . '","' . $reels['reel2'][0] . '","' . $reels['reel3'][0] . '","' . $reels['reel4'][0] . '","' . $reels['reel5'][0] . '"],["' . $reels['reel1'][1] . '","' . $reels['reel2'][1] . '","' . $reels['reel3'][1] . '","' . $reels['reel4'][1] . '","' . $reels['reel5'][1] . '"],["' . $reels['reel1'][2] . '","' . $reels['reel2'][2] . '","' . $reels['reel3'][2] . '","' . $reels['reel4'][2] . '","' . $reels['reel5'][2] . '"],["' . $reels['reel1'][3] . '","' . $reels['reel2'][3] . '","' . $reels['reel3'][3] . '","' . $reels['reel4'][3] . '","' . $reels['reel5'][3] . '"]';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $result_tmp[0] = '{"action":"RespinResponse","result":"true","sesId":"10000298751","data":{"state":"' . $gameState . '"' . $winString . ',"spinResult":{"type":"SpinResult","rows":[' . $symb . ']},"reSpinRemain":"0","reSpinTotal":"0","totalBonusWin":"0.00"}}';
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
