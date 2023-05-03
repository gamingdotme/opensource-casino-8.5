<?php 
namespace VanguardLTE\Games\MagicTreeNG
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
                            if( $slotSettings->GetBalance() < ($postData['data']['coin'] * $postData['data']['bet'] * 40) && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= 0 ) 
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
                            case 'PickBonusItemRequest':
                                $index = $postData['data']['index'];
                                $BonusState = $slotSettings->GetGameData($slotSettings->slotId . 'BonusState');
                                if( $BonusState != 1 ) 
                                {
                                    exit();
                                }
                                $itemsAll = [
                                    '{"type":"BonusItem","index":"1","value":"' . rand(5, 12) . '","picked":"true"}', 
                                    '{"type":"BonusItem","index":"2","value":"' . rand(5, 12) . '","picked":"true"}', 
                                    '{"type":"BonusItem","index":"3","value":"' . rand(5, 12) . '","picked":"true"}'
                                ];
                                $pickCount = rand(5, 12);
                                $curItem = '{"type":"BonusItem","index":"' . $index . '","value":"' . $pickCount . '","picked":"true"}';
                                $itemsAll[$index - 1] = $curItem;
                                $result_tmp[] = '{"action":"PickBonusItemResponse","result":"true","sesId":"10000585678","data":{"items":[' . implode(',', $itemsAll) . '],"state":"CollectionBonus","lastPick":"true","canGamble":"false","params":{"PickBonus":"0"},"bonusItem":' . $curItem . '}}';
                                $jArr = [
                                    0, 
                                    0, 
                                    0, 
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
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $slotSettings->SetGameData($slotSettings->slotId . 'jArr', $jArr);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusState', 2);
                                $slotSettings->SetGameData($slotSettings->slotId . 'pickCount', $pickCount);
                                break;
                            case 'CollectionBonusItemRequest':
                                $item = $postData['data']['index'];
                                $Items = $slotSettings->GetGameData($slotSettings->slotId . 'Items');
                                $BonusState = $slotSettings->GetGameData($slotSettings->slotId . 'BonusState');
                                $pickCount = $slotSettings->GetGameData($slotSettings->slotId . 'pickCount');
                                $SelectedItems = $slotSettings->GetGameData($slotSettings->slotId . 'SelectedItems');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $reserve = $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved');
                                $jArr = $slotSettings->GetGameData($slotSettings->slotId . 'jArr');
                                if( $BonusState != 2 ) 
                                {
                                    exit();
                                }
                                $bank = $slotSettings->GetBank('bonus');
                                $picksRemain = 1;
                                $winAmount = 0;
                                $lastPick = 'false';
                                $state = 'CollectionBonus';
                                $gameParameters = '';
                                $pays = [];
                                $pays[0] = [
                                    0, 
                                    0, 
                                    5, 
                                    10, 
                                    75, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $pays[1] = [
                                    0, 
                                    0, 
                                    4, 
                                    7, 
                                    15, 
                                    150, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $pays[2] = [
                                    0, 
                                    0, 
                                    3, 
                                    5, 
                                    10, 
                                    75, 
                                    500, 
                                    0, 
                                    0
                                ];
                                $pays[3] = [
                                    0, 
                                    0, 
                                    2, 
                                    4, 
                                    7, 
                                    15, 
                                    750, 
                                    1000, 
                                    0
                                ];
                                $pays[4] = [
                                    0, 
                                    0, 
                                    1, 
                                    3, 
                                    5, 
                                    10, 
                                    250, 
                                    2500, 
                                    5000
                                ];
                                shuffle($jArr);
                                $currentSelect = 0;
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    if( $jArr[0] == -1 ) 
                                    {
                                        shuffle($jArr);
                                    }
                                    else
                                    {
                                        $Prizes = $slotSettings->GetGameData($slotSettings->slotId . 'Prizes');
                                        $Prizes[$jArr[0]][0]++;
                                        $Prizes[$jArr[0]][1] = $pays[$jArr[0]][$Prizes[$jArr[0]][0]] * $allbet;
                                        $allwin = $Prizes[0][1] + $Prizes[1][1] + $Prizes[2][1] + $Prizes[3][1] + $Prizes[4][1];
                                        if( $allwin <= ($bank + $reserve) ) 
                                        {
                                            $currentSelect = $jArr[0];
                                            $jArr[0] = -1;
                                            if( $pickCount == 1 ) 
                                            {
                                                $winAmount = $allwin;
                                            }
                                            break;
                                        }
                                        shuffle($jArr);
                                    }
                                }
                                $pickCount--;
                                $curItem = '{"type":"BonusItem","index":"' . $item . '","collectionSymbolId":"' . ($currentSelect + 15) . '","value":"0","picked":"true"}';
                                $curItem0 = '{"type":"BonusItem","index":"' . $item . '","value":"' . ($currentSelect + 15) . '","picked":"true"}';
                                $Items[] = $curItem0;
                                $SelectedItems[] = $item;
                                if( $winAmount > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                                    $slotSettings->SetBank('bonus', -1 * $winAmount + $reserve);
                                    $slotSettings->SetBalance($winAmount);
                                    $slotSettings->SaveLogReport('NULL', $allbet, 1, $winAmount, 'BG');
                                    $BonusState = 0;
                                    $state = 'Ready';
                                    $lastPick = 'true';
                                    $picksRemain = 0;
                                    $gameParameters = '"gameParameters":{"initialSymbols":[["11","9","7","6","11"],["9","8","11","9","3"],["8","3","4","5","9"]]},';
                                    for( $i = 1; $i <= 30; $i++ ) 
                                    {
                                        if( !in_array($i, $SelectedItems) ) 
                                        {
                                            for( $j = 1; $j <= 30; $j++ ) 
                                            {
                                                if( $jArr[$j] != -1 ) 
                                                {
                                                    $Items[] = '{"type":"BonusItem","index":"' . $i . '","value":"' . ($jArr[$j] + 15) . '","picked":"false"}';
                                                    $jArr[$j] = -1;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                $result_tmp[] = '{"action":"CollectionBonusItemResponse","result":"true","sesId":"10000585678","data":{' . $gameParameters . '"items":[' . implode(',', $Items) . '],"state":"' . $state . '","lastPick":"' . $lastPick . '","bonusItem":' . $curItem . ',"collections":[{"type":"Money","symbolId":"15","value":"' . $Prizes[0][1] . '","count":"' . $Prizes[0][0] . '"},{"type":"Money","symbolId":"16","value":"' . $Prizes[1][1] . '","count":"' . $Prizes[1][0] . '"},{"type":"Money","symbolId":"17","value":"' . $Prizes[2][1] . '","count":"' . $Prizes[2][0] . '"},{"type":"Money","symbolId":"18","value":"' . $Prizes[3][1] . '","count":"' . $Prizes[3][0] . '"},{"type":"Money","symbolId":"19","value":"' . $Prizes[4][1] . '","count":"' . $Prizes[4][0] . '"}]}}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'jArr', $jArr);
                                $slotSettings->SetGameData($slotSettings->slotId . 'pickCount', $pickCount);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Items', $Items);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusState', $BonusState);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Prizes', $Prizes);
                                $slotSettings->SetGameData($slotSettings->slotId . 'SelectedItems', $SelectedItems);
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
                                $result_tmp[0] = '{"action":"AuthResponse","result":"true","sesId":"10000342794","data":{"snivy":"proxy v6.10.48 (API v4.23)","supportedFeatures":["Offers","Jackpots","InstantJackpots","SweepStakes"],"sessionId":"10000342794","defaultLines":["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29"],"bets":["1","2","3","4","5","10","15","20","30","40","50","100","200"],"betMultiplier":"1.0000000","defaultBet":"1","defaultCoinValue":"0.01","coinValues":["0.01"],"gameParameters":{"availableLines":[["1","1","1","1","1"],["0","0","0","0","0"],["2","2","2","2","2"],["0","1","2","1","0"],["2","1","0","1","2"],["1","0","0","0","1"],["1","2","2","2","1"],["0","0","1","2","2"],["2","2","1","0","0"],["1","0","1","0","1"],["1","2","1","2","1"],["0","1","1","1","2"],["2","1","1","1","0"],["1","1","0","1","2"],["1","1","2","1","0"],["0","1","0","1","0"],["2","1","2","1","2"],["0","0","2","0","0"],["2","2","0","2","2"],["1","0","2","0","1"],["1","2","0","2","1"],["0","2","0","2","0"],["2","0","2","0","2"],["0","2","2","2","0"],["2","0","0","0","2"],["0","2","1","2","0"],["2","0","1","0","2"],["1","1","2","1","1"],["1","1","0","1","1"],["0","2","0","1","1"]],"rtp":"0.00","payouts":[{"payout":"30","symbols":["0","0","0"],"type":"basic"},{"payout":"300","symbols":["0","0","0","0"],"type":"basic"},{"payout":"20000","symbols":["0","0","0","0","0"],"type":"basic"},{"payout":"30","symbols":["1","1","1"],"type":"basic"},{"payout":"300","symbols":["1","1","1","1"],"type":"basic"},{"payout":"600","symbols":["1","1","1","1","1"],"type":"basic"},{"payout":"25","symbols":["2","2","2"],"type":"basic"},{"payout":"100","symbols":["2","2","2","2"],"type":"basic"},{"payout":"300","symbols":["2","2","2","2","2"],"type":"basic"},{"payout":"20","symbols":["3","3","3"],"type":"basic"},{"payout":"50","symbols":["3","3","3","3"],"type":"basic"},{"payout":"200","symbols":["3","3","3","3","3"],"type":"basic"},{"payout":"15","symbols":["4","4","4"],"type":"basic"},{"payout":"30","symbols":["4","4","4","4"],"type":"basic"},{"payout":"150","symbols":["4","4","4","4","4"],"type":"basic"},{"payout":"12","symbols":["5","5","5"],"type":"basic"},{"payout":"25","symbols":["5","5","5","5"],"type":"basic"},{"payout":"75","symbols":["5","5","5","5","5"],"type":"basic"},{"payout":"10","symbols":["6","6","6"],"type":"basic"},{"payout":"20","symbols":["6","6","6","6"],"type":"basic"},{"payout":"50","symbols":["6","6","6","6","6"],"type":"basic"},{"payout":"7","symbols":["7","7","7"],"type":"basic"},{"payout":"15","symbols":["7","7","7","7"],"type":"basic"},{"payout":"45","symbols":["7","7","7","7","7"],"type":"basic"},{"payout":"5","symbols":["8","8","8"],"type":"basic"},{"payout":"10","symbols":["8","8","8","8"],"type":"basic"},{"payout":"30","symbols":["8","8","8","8","8"],"type":"basic"},{"payout":"5","symbols":["9","9","9"],"type":"basic"},{"payout":"10","symbols":["9","9","9","9"],"type":"basic"},{"payout":"20","symbols":["9","9","9","9","9"],"type":"basic"},{"payout":"3","symbols":["10","10","10"],"type":"basic"},{"payout":"6","symbols":["10","10","10","10"],"type":"basic"},{"payout":"15","symbols":["10","10","10","10","10"],"type":"basic"},{"payout":"5","symbols":["15","15"],"type":"basic"},{"payout":"10","symbols":["15","15","15"],"type":"basic"},{"payout":"75","symbols":["15","15","15","15"],"type":"basic"},{"payout":"4","symbols":["16","16"],"type":"basic"},{"payout":"7","symbols":["16","16","16"],"type":"basic"},{"payout":"15","symbols":["16","16","16","16"],"type":"basic"},{"payout":"150","symbols":["16","16","16","16","16"],"type":"basic"},{"payout":"3","symbols":["17","17"],"type":"basic"},{"payout":"5","symbols":["17","17","17"],"type":"basic"},{"payout":"10","symbols":["17","17","17","17"],"type":"basic"},{"payout":"75","symbols":["17","17","17","17","17"],"type":"basic"},{"payout":"500","symbols":["17","17","17","17","17","17"],"type":"basic"},{"payout":"2","symbols":["18","18"],"type":"basic"},{"payout":"4","symbols":["18","18","18"],"type":"basic"},{"payout":"7","symbols":["18","18","18","18"],"type":"basic"},{"payout":"15","symbols":["18","18","18","18","18"],"type":"basic"},{"payout":"1","symbols":["19","19"],"type":"basic"},{"payout":"3","symbols":["19","19","19"],"type":"basic"},{"payout":"5","symbols":["19","19","19","19"],"type":"basic"},{"payout":"10","symbols":["19","19","19","19","19"],"type":"basic"},{"payout":"250","symbols":["19","19","19","19","19","19"],"type":"basic"},{"payout":"2500","symbols":["19","19","19","19","19","19","19"],"type":"basic"},{"payout":"5000","symbols":["19","19","19","19","19","19","19","19"],"type":"basic"},{"payout":"30","symbols":["20","20","20"],"type":"basic"},{"payout":"300","symbols":["20","20","20","20"],"type":"basic"},{"payout":"600","symbols":["20","20","20","20","20"],"type":"basic"}],"initialSymbols":[["9","10","11","9","10"],["2","2","6","5","2"],["10","7","9","0","7"]]},"jackpotsEnabled":"true","gameModes":"[]"}}';
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
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
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
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[20] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[21] = [
                                    1, 
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[22] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[23] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[24] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $linesId[25] = [
                                    1, 
                                    3, 
                                    2, 
                                    3, 
                                    1
                                ];
                                $linesId[26] = [
                                    3, 
                                    1, 
                                    2, 
                                    1, 
                                    3
                                ];
                                $linesId[27] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[28] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[29] = [
                                    1, 
                                    3, 
                                    1, 
                                    2, 
                                    2
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
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
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
                                        '0', 
                                        '1'
                                    ];
                                    $scatter = '13';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsTmp = $reels;
                                    $advancedReels = '';
                                    $advancedReelsSym = rand(8, 15);
                                    $isAdvancedReels = false;
                                    $isTreeWild = rand(1, 10);
                                    if( $isTreeWild == 1 && $winType != 'bonus' ) 
                                    {
                                        $isAdvancedReels = true;
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            for( $p = 0; $p <= 2; $p++ ) 
                                            {
                                                if( rand(1, 5) == 1 ) 
                                                {
                                                    $reels['reel' . $r][$p] = 1;
                                                }
                                            }
                                        }
                                    }
                                    if( $isTreeWild == 1 && $winType != 'bonus' ) 
                                    {
                                        $syma = '["' . $reels['reel1'][0] . '","' . $reels['reel2'][0] . '","' . $reels['reel3'][0] . '","' . $reels['reel4'][0] . '","' . $reels['reel5'][0] . '"],["' . $reels['reel1'][1] . '","' . $reels['reel2'][1] . '","' . $reels['reel3'][1] . '","' . $reels['reel4'][1] . '","' . $reels['reel5'][1] . '"],["' . $reels['reel1'][2] . '","' . $reels['reel2'][2] . '","' . $reels['reel3'][2] . '","' . $reels['reel4'][2] . '","' . $reels['reel5'][2] . '"]';
                                        $advancedReels = ',"spinResultStage2":{"type":"SpinResult","rows":[' . $syma . ']}';
                                    }
                                    for( $k = 0; $k < 30; $k++ ) 
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
                                    $scattersWin = 0;
                                    $scattersWinB = 0;
                                    $scattersPos = [];
                                    $scattersPos2 = [];
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scattersCount2 = 0;
                                    $bSym = $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol');
                                    $bSymCnt = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scattersPos[] = '["' . ($r - 1) . '","' . $p . '"]';
                                                $isScat = true;
                                            }
                                            if( $reels['reel' . $r][$p] == '11' ) 
                                            {
                                                $scattersCount2++;
                                                $scattersPos2[] = '["' . ($r - 1) . '","' . $p . '"]';
                                                $isScat = true;
                                            }
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betLine * $lines * $bonusMpl;
                                    $gameState = 'Ready';
                                    if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                    {
                                        $gameState = 'FreeSpins';
                                        $scw = '{"type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"10"},"amount":"' . $slotSettings->FormatFloat($scattersWin) . '","wonSymbols":[' . implode(',', $scattersPos) . ']}';
                                        array_push($lineWins, $scw);
                                    }
                                    if( $scattersCount2 >= 2 && $slotSettings->slotBonus ) 
                                    {
                                        $gameState = 'PickBonus';
                                        $scw = '{"wonSymbols":[' . implode(',', $scattersPos2) . '],"amount":"0.00","type":"Bonus","bonusName":"FreeSpins","params":{"freeSpins":"1"}}';
                                        array_push($lineWins, $scw);
                                    }
                                    if( $scattersCount >= 3 && $scattersCount2 >= 2 ) 
                                    {
                                    }
                                    else
                                    {
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
                                                if( $scattersCount2 >= 2 && $spinWinLimit < ($allbet * 40 + $totalWin) ) 
                                                {
                                                }
                                                else if( ($scattersCount >= 3 || $scattersCount2 >= 3) && $winType != 'bonus' ) 
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
                                if( $scattersCount2 >= 2 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'SelectedItems', []);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Prizes', [
                                        [
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0
                                        ]
                                    ]);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Items', []);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusState', 1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', $allbet * 40);
                                    $slotSettings->SetBank('bonus', -1 * ($allbet * 40));
                                }
                                $reels = $reelsTmp;
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                if( $totalWin > 0 || $winType == 'bonus' ) 
                                {
                                    $winString0 = implode(',', $lineWins);
                                    $winString = ',"slotWin":{"lineWinAmounts":[' . $winString0 . '],"totalWin":"' . $slotSettings->FormatFloat($totalWin) . '","canGamble":"false"}';
                                }
                                else
                                {
                                    $winString = '';
                                }
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusSymbol":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = '["' . $reelsTmp['reel1'][0] . '","' . $reelsTmp['reel2'][0] . '","' . $reelsTmp['reel3'][0] . '","' . $reelsTmp['reel4'][0] . '","' . $reelsTmp['reel5'][0] . '"],["' . $reelsTmp['reel1'][1] . '","' . $reelsTmp['reel2'][1] . '","' . $reelsTmp['reel3'][1] . '","' . $reelsTmp['reel4'][1] . '","' . $reelsTmp['reel5'][1] . '"],["' . $reelsTmp['reel1'][2] . '","' . $reelsTmp['reel2'][2] . '","' . $reelsTmp['reel3'][2] . '","' . $reelsTmp['reel4'][2] . '","' . $reelsTmp['reel5'][2] . '"]';
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
                                    $result_tmp[] = '{"action":"FreeSpinResponse","result":"true","sesId":"10000228087","data":{' . $gameParameters . '"state":"' . $gameState . '"' . $winString . ',"spinResult":{"type":"SpinResult","rows":[' . $symb . ']}' . $advancedReels . ' ,"totalBonusWin":"' . $slotSettings->FormatFloat($bonusWin0) . '","freeSpinRemain":"' . $freeSpinRemain . '","freeSpinsTotal":"' . $freeSpinsTotal . '"}}';
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'initialSymbols', $symb);
                                    $result_tmp[] = '{"action":"SpinResponse","result":"true","sesId":"10000217909","data":{"state":"' . $gameState . '"' . $winString . ',"spinResult":{"type":"SpinResult","rows":[' . $symb . ']} ' . $advancedReels . ' }}';
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
