<?php 
namespace VanguardLTE\Games\SunWukongPTM
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        if( isset($postData['umid']) ) 
                        {
                            $umid = $postData['umid'];
                            if( isset($postData['ID']) ) 
                            {
                                $umid = $postData['ID'];
                            }
                        }
                        else
                        {
                            if( isset($postData['ID']) ) 
                            {
                                $result_tmp[] = '3:::{"ID":18}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['ID']) && $postData['ID'] == '46108' ) 
                        {
                            if( $postData['pick'] == '2' ) 
                            {
                                $fCount = 20;
                                $bonusMpl = 2;
                            }
                            if( $postData['pick'] == '1' ) 
                            {
                                $fCount = 10;
                                $bonusMpl = 4;
                            }
                            if( $postData['pick'] == '0' ) 
                            {
                                $fCount = 8;
                                $bonusMpl = 5;
                            }
                            $lines = $slotSettings->GetGameData('SunWukongPTMLines');
                            $betLine = $slotSettings->GetGameData('SunWukongPTMBet') / $lines;
                            $allWin = 0;
                            $spins = [];
                            $responseLog = [];
                            for( $s = 1; $s <= $fCount; $s++ ) 
                            {
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [];
                                    $wild = ['0'];
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips('', 'freespin');
                                    $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $slotSettings->GetGameData('SunWukongPTMBet'));
                                    $winType = $winTypeTmp[0];
                                    $spinWinLimit = $winTypeTmp[1];
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
                                    $spinInfo = $slotSettings->GetSpinWin($reels, $lines, $betLine, $linesId, $wild, $scatter, $bonusMpl);
                                    $totalWin = $spinInfo['totalWin'];
                                    $lineWins = $spinInfo['lineWins'];
                                    $scattersWin = $spinInfo['scattersWin'];
                                    $scattersCount = $spinInfo['scattersCount'];
                                    $scattersStr = $spinInfo['scattersStr'];
                                    if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $i > 1000 ) 
                                        {
                                            $winType = 'none';
                                        }
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        $minWin = $slotSettings->GetRandomPay();
                                        if( $i > 700 ) 
                                        {
                                            $minWin = 0;
                                        }
                                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $i > 1500 ) 
                                            {
                                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                                exit( $response );
                                            }
                                            if( $scattersCount >= 2 && $winType != 'bonus' ) 
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
                                    $allWin += $totalWin;
                                }
                                if( $scattersCount == 2 ) 
                                {
                                    $fCount += 10;
                                }
                                if( $scattersCount == 3 ) 
                                {
                                    $fCount += 20;
                                }
                                if( $scattersCount == 4 ) 
                                {
                                    $fCount += 50;
                                }
                                if( $scattersCount == 5 ) 
                                {
                                    $fCount += 100;
                                }
                                $slotSettings->SetGameData('SunWukongPTMBonusWin', $slotSettings->GetGameData('SunWukongPTMBonusWin') + $totalWin);
                                $spins[] = '{"drawId":' . ($s + 1) . ',"winLines":{"currentWin":' . $totalWin . ',"currentSpins":' . $fCount . ',"display":"Q,SA,M,K,M;PE,M,J,A,N;A,PE,K,Q,A","reelset":0,"runningTotal":' . ($slotSettings->GetGameData('SunWukongPTMFreeStartWin') + $slotSettings->GetGameData('SunWukongPTMBonusWin')) . ',"spins":' . $fCount . ',"stops":"' . implode(',', $reels['rp']) . '"},"replayInfo":{"foItems":"' . implode(',', $reels['rp']) . '"}}';
                                $jsSpin = '' . json_encode($reels) . '';
                                $responseLog[] = '{"responseEvent":"spin","responseType":"freespin","serverResponse":{"bonusMpl":' . $bonusMpl . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $fCount . ',"currentFreeGames":' . $s . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('SunWukongPTMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('SunWukongPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":"","Jackpots":"","reelsSymbols":' . $jsSpin . '}}';
                            }
                            $slotSettings->SetGameData('SunWukongPTMFreeSpins', $spins);
                            $slotSettings->SetGameData('SunWukongPTMFreeLogs', $responseLog);
                            $slotSettings->SetGameData('SunWukongPTMFreeGames', $fCount);
                            $slotSettings->SetGameData('SunWukongPTMCurrentFreeGame', 0);
                            $fSpinList = implode(',', $spins);
                            $result_tmp[] = '3:::{"data":{"gameId":2543168908,"drawStates":[{"drawId":1,"feature":{"name":"bonus","details":{"bonusPayout":' . $slotSettings->GetGameData('SunWukongPTMFreeStartWin') . ',"initialSpins":' . $fCount . ',"multiplier":' . $bonusMpl . ',"pick":' . $postData['pick'] . ',"spins":' . $fCount . ',"totalPayout":' . $allWin . '}}},' . $fSpinList . ']},"ID":47342,"umid":1951}';
                        }
                        if( isset($postData['ID']) && $postData['ID'] == '46120' && $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 && $postData['savedStates'][0]['attr21'] > 0 ) 
                        {
                            $fsNum = $slotSettings->GetGameData('SunWukongPTMCurrentFreeGame');
                            $spins_ = $slotSettings->GetGameData('SunWukongPTMFreeSpins');
                            $logs_ = $slotSettings->GetGameData('SunWukongPTMFreeLogs');
                            $spins = json_decode(trim($spins_[$fsNum]), true);
                            $logs = json_decode(trim($logs_[$fsNum]), true);
                            $totalWin = $logs['serverResponse']['totalWin'];
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $logs['serverResponse']['freeSeq'] = $spins_;
                            $logs['serverResponse']['freeLogSeq'] = $logs_;
                            $slotSettings->SaveLogReport(json_encode($logs), $logs['serverResponse']['slotLines'], $logs['serverResponse']['slotBet'], $totalWin, 'freespin');
                            $result_tmp[] = '3:::{"data":{"gameId":2543168908},"ID":46121,"umid":45}';
                            $slotSettings->SetGameData('SunWukongPTMCurrentFreeGame', $slotSettings->GetGameData('SunWukongPTMCurrentFreeGame') + 1);
                        }
                        if( isset($postData['ID']) && ($postData['ID'] == '46123' || $postData['ID'] == '46302') ) 
                        {
                            $result_tmp = [];
                            $postData['spinType'] = 'regular';
                            if( $postData['ID'] == '46302' ) 
                            {
                                $postData['spinType'] = 'free';
                            }
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('SunWukongPTMBonusWin', 0);
                                $slotSettings->SetGameData('SunWukongPTMFreeGames', 0);
                                $slotSettings->SetGameData('SunWukongPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('SunWukongPTMTotalWin', 0);
                                $slotSettings->SetGameData('SunWukongPTMFreeBalance', 0);
                                $slotSettings->SetGameData('SunWukongPTMFreeStartWin', 0);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('SunWukongPTMCurrentFreeGame', $slotSettings->GetGameData('SunWukongPTMCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
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
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $postData['bet'] = $slotSettings->GetGameData('SunWukongPTMBet');
                                $lines = $slotSettings->GetGameData('SunWukongPTMLines');
                            }
                            else
                            {
                                $postData['bet'] = $postData['stake'];
                                $lines_ = explode('L', $postData['numLines']);
                                $lines = (int)$lines_[1];
                                $slotSettings->SetGameData('SunWukongPTMBet', $postData['bet']);
                                $slotSettings->SetGameData('SunWukongPTMLines', $lines);
                            }
                            $betLine = $postData['bet'] / $lines;
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < ($lines * $betLine) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                                    exit( $response );
                                }
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['bet']);
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $wild = ['0'];
                                $scatter = '12';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $spinInfo = $slotSettings->GetSpinWin($reels, $lines, $betLine, $linesId, $wild, $scatter, $bonusMpl);
                                $totalWin = $spinInfo['totalWin'];
                                $lineWins = $spinInfo['lineWins'];
                                $scattersWin = $spinInfo['scattersWin'];
                                $scattersCount = $spinInfo['scattersCount'];
                                $scattersStr = $spinInfo['scattersStr'];
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                                $slotSettings->SetGameData('SunWukongPTMBonusWin', $slotSettings->GetGameData('SunWukongPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('SunWukongPTMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('SunWukongPTMTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('SunWukongPTMFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('SunWukongPTMFreeGames', $slotSettings->GetGameData('SunWukongPTMFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('SunWukongPTMFreeStartWin', $totalWin);
                                    $slotSettings->SetGameData('SunWukongPTMBonusWin', 0);
                                    $slotSettings->SetGameData('SunWukongPTMFreeGames', $slotSettings->slotFreeCount);
                                }
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"placeBetsResponse":{"gameId":2543270502,"newBalance":2.147483647E9},"loadResultsResponse":{"gameId":2543270502,"drawStates":[{"drawId":0,"state":"settling","wCapMaxWin":1000000.0,"bet":{"pick":"L' . $lines . '","seq":0,"stake":0.01,"type":"line","won":"false"},"winLines":{"currentSpins":0,"display":"J,T,W,N,T;M,H,J,S,N;PE,PE,Q,W,SA","reelset":0,"runningTotal":0.0,"spins":0,"stops":"' . implode(',', $reels['rp']) . '"},"replayInfo":{"foItems":"' . implode(',', $reels['rp']) . '"}}]}},"ID":47343,"umid":32}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $result_tmp[] = '3:::{"data":{"settleBetsResponse":{"gameId":2543270502,"newBalance":2.147483647E9},"closeGameResponse":{},"openGameResponse":{"gameId":2543271807,"drawStates":[{"drawId":0}],"savedState":{"attr21":"-1","seq":0},"persistedProperties":{"attr21":"-1"}},"ID":47345,"umid":39}}';
                            $result_tmp[] = '3:::{"data":{"gameId":2543271807},"ID":46121,"umid":40}';
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('SunWukongPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('SunWukongPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('SunWukongPTMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('SunWukongPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                        }
                        switch( $umid ) 
                        {
                            case '31031':
                                $result_tmp[] = '3:::{"data":{"urlList":[{"urlType":"mobile_login","url":"https://login.loc/register","priority":1},{"urlType":"mobile_support","url":"https://ww2.loc/support","priority":1},{"urlType":"playerprofile","url":"","priority":1},{"urlType":"playerprofile","url":"","priority":10},{"urlType":"gambling_commission","url":"","priority":1},{"urlType":"cashier","url":"","priority":1},{"urlType":"cashier","url":"","priority":1}]},"ID":100}';
                                break;
                            case '10001':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40083,"umid":3}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":4}';
                                $result_tmp[] = '3:::{"data":{"commandId":13218,"params":["0","null"]},"ID":50001,"umid":5}';
                                $result_tmp[] = '3:::{"token":{"secretKey":"","currency":"USD","balance":0,"loginTime":""},"ID":10002,"umid":7}';
                                break;
                            case '40294':
                                $result_tmp[] = '3:::{"nicknameInfo":{"nickname":""},"ID":10022,"umid":8}';
                                $result_tmp[] = '3:::{"data":{"commandId":10713,"params":["0","ba","bj","ct","gc","grel","hb","po","ro","sc","tr"]},"ID":50001,"umid":9}';
                                $result_tmp[] = '3:::{"data":{"commandId":11666,"params":["0","0","0"]},"ID":50001,"umid":11}';
                                $result_tmp[] = '3:::{"data":{"commandId":13981,"params":["0","1"]},"ID":50001,"umid":12}';
                                $result_tmp[] = '3:::{"data":{"commandId":14080,"params":["0","0"]},"ID":50001,"umid":14}';
                                $result_tmp[] = '3:::{"data":{"keyValueCount":5,"elementsPerKey":1,"params":["10","1","11","500","12","1","13","0","14","0"]},"ID":40716,"umid":15}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":16}';
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":' . $balanceInCents . '},"ID":10006,"umid":17}';
                                $result_tmp[] = '3:::{"data":{},"ID":40292,"umid":18}';
                                break;
                            case '10010':
                                $result_tmp[] = '3:::{"data":{"urls":{"casino-cashier-myaccount":[],"regulation_pt_self_exclusion":[],"link_legal_aams":[],"regulation_pt_player_protection":[],"mobile_cashier":[],"mobile_bank":[],"mobile_bonus_terms":[],"mobile_help":[],"link_responsible":[],"cashier":[{"url":"","priority":1},{"url":"","priority":1}],"gambling_commission":[{"url":"","priority":1},{"url":"","priority":1}],"desktop_help":[],"chat_token":[],"mobile_login_error":[],"mobile_error":[],"mobile_login":[{"url":"","priority":1}],"playerprofile":[{"url":"","priority":1},{"url":"","priority":10}],"link_legal_half":[],"ngmdesktop_quick_deposit":[],"external_login_form":[],"mobile_main_promotions":[],"mobile_lobby":[],"mobile_promotion":[],{"url":"","priority":1},{"url":"","priority":10}],"mobile_withdraw":[],"mobile_funds_trans":[],"mobile_quick_deposit":[],"mobile_history":[],"mobile_deposit_limit":[],"minigames_help":[],"link_legal_18":[],"mobile_responsible":[],"mobile_share":[],"mobile_lobby_error":[],"mobile_mobile_comp_points":[],"mobile_support":[{"url":"","priority":1}],"mobile_chat":[],"mobile_logout":[],"mobile_deposit":[],"invite_friend":[]}},"ID":10011,"umid":19}';
                                $result_tmp[] = '3:::{"data":{"brokenGames":[],"windowId":"SuJLru"},"ID":40037,"umid":20}';
                                break;
                            case '46090':
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                    $fsReel = $slotSettings->GetGameData('SunWukongPTMFreeSpins');
                                    $fsNum = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $result_tmp[] = '3:::{"data":{"gameId":2543168908,"drawStates":[{"drawId":0,"state":"settling","wCapMaxWin":1000000.0,"bet":{"payout":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin')) . ',"pick":"L' . $slotSettings->GetGameData($slotSettings->slotId . 'Lines') . '","seq":0,"stake":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * $slotSettings->GetGameData($slotSettings->slotId . 'Lines')) . ',"type":"line","won":"true"},"winLines":{"currentSpins":0,"display":"S,S,M,S,N;Q,PE,T,H,A;PE,J,N,A,H","reelset":0,"runningTotal":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"spins":0,"stops":"16,53,26,61,31","scatter":{"length":3,"offsets":"0,1,3","payout":0.05,"prize":"3S"},"feature":{"name":"bonus"}},"replayInfo":{"foItems":"16,53,26,61,31"}},{"drawId":1,"feature":{"name":"bonus","details":{"bonusPayout":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"initialSpins":' . $fsNum . ',"multiplier":' . $slotSettings->GetGameData('SunWukongPTMbonusMpl') . ',"pick":2,"spins":1,"totalPayout":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin')) . '}}},' . implode(',', $fsReel) . '],"savedState":{"attr21":"' . $fsCur . '","seq":0},"persistedProperties":{"attr21":"' . $fsCur . '"}},"ID":47340,"umid":31}';
                                }
                                else
                                {
                                    $result_tmp[] = '3:::{"data":{"gameId":2538685341,"drawStates":[{"drawId":0}],"savedState":{"attr21":"-1","seq":0},"persistedProperties":{"attr21":"-1"}},"ID":47340,"umid":30}';
                                }
                                break;
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"gtsswk","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lastEvent->serverResponse->slotLines);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    if( isset($lastEvent->serverResponse->freeSeq) ) 
                                    {
                                        $slotSettings->SetGameData('SunWukongPTMFreeSpins', $lastEvent->serverResponse->freeSeq);
                                        $slotSettings->SetGameData('SunWukongPTMFreeLogs', $lastEvent->serverResponse->freeLogSeq);
                                        $slotSettings->SetGameData('SunWukongPTMbonusMpl', $lastEvent->serverResponse->bonusMpl);
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'gtsswk');
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40030':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                }
                                break;
                            case '48300':
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":30}';
                                $result_tmp[] = '3:::{"data":{"waitingLogins":[],"waitingAlerts":[],"waitingDialogs":[],"waitingDialogMessages":[],"waitingToasterMessages":[]},"ID":48301,"umid":31}';
                                break;
                        }
                        $response = implode('------', $result_tmp);
                        $slotSettings->SaveGameData();
                        $slotSettings->SaveGameDataStatic();
                        echo $response;
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
