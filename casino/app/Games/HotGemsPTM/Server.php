<?php 
namespace VanguardLTE\Games\HotGemsPTM
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
                        if( isset($postData['ID']) && ($postData['ID'] == '46311' || $postData['ID'] == '46302') && $slotSettings->GetGameData('HotGemsPTMCurrentFreeGame') <= $slotSettings->GetGameData('HotGemsPTMFreeGames') && $slotSettings->GetGameData('HotGemsPTMFreeGames') > 0 ) 
                        {
                            $fsNum = $slotSettings->GetGameData('HotGemsPTMCurrentFreeGame');
                            $spins_ = $slotSettings->GetGameData('HotGemsPTMFreeSpins');
                            $logs_ = $slotSettings->GetGameData('HotGemsPTMFreeLogs');
                            $spins = json_decode(trim($spins_[$fsNum]), true);
                            $logs = json_decode(trim($logs_[$fsNum]), true);
                            $totalWin = $spins['data']['currentWin'];
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $logs['serverResponse']['freeSeq'] = $spins_;
                            $logs['serverResponse']['freeLogSeq'] = $logs_;
                            $slotSettings->SaveLogReport(json_encode($logs), $logs['serverResponse']['slotLines'], $logs['serverResponse']['slotBet'], $totalWin, 'freespin');
                            if( $postData['ID'] == '46302' ) 
                            {
                                $result_tmp[] = '3:::' . $spins_[$slotSettings->GetGameData('HotGemsPTMCurrentFreeGame')];
                            }
                            $slotSettings->SetGameData('HotGemsPTMCurrentFreeGame', $slotSettings->GetGameData('HotGemsPTMCurrentFreeGame') + 1);
                        }
                        if( isset($postData['ID']) && $postData['ID'] == '40029' ) 
                        {
                            $fCount = 15;
                            $bonusMpl = 1;
                            $betLine = $slotSettings->GetGameData('HotGemsPTMBet');
                            $lines = 25;
                            $allWin = 0;
                            $spins = [];
                            $responseLog = [];
                            for( $s = 1; $s <= $fCount; $s++ ) 
                            {
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
                                    3, 
                                    3
                                ];
                                $linesId[6] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[7] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[8] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[9] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[10] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[11] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[12] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    2
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
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[20] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[21] = [
                                    2, 
                                    1, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[22] = [
                                    2, 
                                    3, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[23] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[24] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $winTypeTmp = $slotSettings->GetSpinSettings('freespin', $betLine * $lines, $lines);
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
                                        0
                                    ];
                                    $wild = ['0'];
                                    $scatter = '13';
                                    $reels = $slotSettings->GetReelStrips($winType, 'freespin');
                                    $resultReels = $reels;
                                    $rStr = '';
                                    $mpl = 1;
                                    $scattersCount = 0;
                                    for( $sd = 1; $sd <= 15; $sd++ ) 
                                    {
                                        $curSpinData = $slotSettings->GetReelsWin($reels, $lines, $betLine, $linesId, $cWins, $mpl);
                                        $totalWin += $curSpinData['totalWin'];
                                        if( $curSpinData['bonusSym'] >= 3 ) 
                                        {
                                            $scattersCount = $curSpinData['bonusSym'];
                                        }
                                        if( $curSpinData['totalWin'] <= 0 && $curSpinData['bonusSym'] < 3 ) 
                                        {
                                            break;
                                        }
                                        $reels = $slotSettings->OffsetReels($curSpinData['reels'], 'freespin');
                                        $mpl++;
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '{';
                                    if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                    {
                                        $scattersStr .= '"scattersType":"bonus",';
                                    }
                                    else if( $scattersWin > 0 ) 
                                    {
                                        $scattersStr .= '"scattersType":"win",';
                                    }
                                    else
                                    {
                                        $scattersStr .= '"scattersType":"none",';
                                    }
                                    $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                                    $totalWin += $scattersWin;
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
                                            $response = '{"responseEvent":"error","responseType":"freespin","serverResponse":"Bad Reel Strip"}';
                                            $slotSettings->SaveLogReport($response, 0, 0, 0, '');
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
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $allWin += $totalWin;
                                }
                                $reels = $resultReels;
                                $slotSettings->SetGameData('HotGemsPTMBonusWin', $slotSettings->GetGameData('HotGemsPTMBonusWin') + $totalWin);
                                $spins[] = '{"data":{"betLine":' . $betLine . ',"currentWin":' . $totalWin . ',"drawState":{"event":[{"seq":0,"id":' . $reels['rp'][0] . ',"type":"24:A"},{"seq":1,"id":' . $reels['rp'][1] . ',"type":"4:Q"},{"seq":2,"id":' . $reels['rp'][2] . ',"type":"23:T"},{"seq":3,"id":' . $reels['rp'][3] . ',"type":"32:Q"},{"seq":4,"id":' . $reels['rp'][4] . ',"type":"4:A"}],"gameStages":[{"stage":[{"display":"J,A,J,W,J;A,Q,T,Q,A;N,L,K,K,D","multiplier":1,"stage":1}],"spins":0,"stages":1,"wCapMaxWin":1000000.0}],"bet":[{"toPayout":0.25,"pick":"L25","seq":0,"stake":0.25,"type":"line","won":"false"}],"drawId":0,"seed":389802986,"state":"settling"},"gameId":2501019450},"ID":46303,"umid":49}';
                                $jsSpin = '' . json_encode($reels) . '';
                                $responseLog[] = '{"responseEvent":"spin","responseType":"freespin","serverResponse":{"bonusMpl":' . $bonusMpl . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $fCount . ',"currentFreeGames":' . $s . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('HotGemsPTMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('HotGemsPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":"","Jackpots":"","reelsSymbols":' . $jsSpin . '}}';
                            }
                            $slotSettings->SetGameData('HotGemsPTMFreeSpins', $spins);
                            $slotSettings->SetGameData('HotGemsPTMFreeLogs', $responseLog);
                            $slotSettings->SetGameData('HotGemsPTMFreeGames', $fCount);
                            $slotSettings->SetGameData('HotGemsPTMCurrentFreeGame', 0);
                            $fSpinList = implode(',', $spins);
                            $result_tmp[] = '3:::{"data":{"gameId":2543168908,"drawStates":[{"drawId":1,"feature":{"name":"bonus","details":{"bonusPayout":' . $slotSettings->GetGameData('HotGemsPTMFreeStartWin') . ',"initialSpins":' . $fCount . ',"multiplier":' . $bonusMpl . ',"spins":' . $fCount . ',"totalPayout":' . $allWin . '}}},' . $fSpinList . ']},"ID":47342,"umid":1951}';
                        }
                        if( isset($postData['ID']) && $postData['ID'] == '46299' ) 
                        {
                            $result_tmp = [];
                            $postData['spinType'] = 'regular';
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('HotGemsPTMBonusWin', 0);
                                $slotSettings->SetGameData('HotGemsPTMFreeGames', 0);
                                $slotSettings->SetGameData('HotGemsPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('HotGemsPTMTotalWin', 0);
                                $slotSettings->SetGameData('HotGemsPTMFreeBalance', 0);
                                $slotSettings->SetGameData('HotGemsPTMFreeStartWin', 0);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('HotGemsPTMCurrentFreeGame', $slotSettings->GetGameData('HotGemsPTMCurrentFreeGame') + 1);
                                $bonusMpl = 1;
                            }
                            $postData['bet'] = $postData['bets'][0]['stake'];
                            $lines_ = explode('L', $postData['bets'][0]['pick']);
                            $lines = (int)$lines_[1];
                            $betLine = $postData['bet'] / $lines;
                            $slotSettings->SetGameData('HotGemsPTMBet', $betLine);
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    $slotSettings->SaveLogReport($response, 0, 0, 0, '');
                                }
                                if( $slotSettings->GetBalance() < ($lines * $betLine) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                    $slotSettings->SaveLogReport($response, 0, 0, 0, '');
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                                    $slotSettings->SaveLogReport($response, 0, 0, 0, '');
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
                            if( $postData['slotEvent'] == 'freespin' && $winType == 'bonus' ) 
                            {
                                $winType = 'none';
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
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[6] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[7] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                3, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[10] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                2
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
                                1, 
                                3, 
                                1
                            ];
                            $linesId[20] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[21] = [
                                2, 
                                1, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[22] = [
                                2, 
                                3, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[23] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[24] = [
                                3, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
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
                                    0
                                ];
                                $wild = ['0'];
                                $scatter = '13';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $resultReels = $reels;
                                $rStr = '';
                                $mpl = 1;
                                $scattersCount = 0;
                                for( $sd = 1; $sd <= 15; $sd++ ) 
                                {
                                    $curSpinData = $slotSettings->GetReelsWin($reels, $lines, $betLine, $linesId, $cWins, $mpl);
                                    $totalWin += $curSpinData['totalWin'];
                                    if( $curSpinData['bonusSym'] >= 3 ) 
                                    {
                                        $scattersCount = $curSpinData['bonusSym'];
                                    }
                                    if( $curSpinData['totalWin'] <= 0 && $curSpinData['bonusSym'] < 3 ) 
                                    {
                                        break;
                                    }
                                    $reels = $slotSettings->OffsetReels($curSpinData['reels'], 'regular');
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $mpl++;
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '{';
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr .= '"scattersType":"win",';
                                }
                                else
                                {
                                    $scattersStr .= '"scattersType":"none",';
                                }
                                $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                                $totalWin += $scattersWin;
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
                                            $slotSettings->SaveLogReport($response, 0, 0, 0, '');
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
                            $reels = $resultReels;
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('HotGemsPTMBonusWin', $slotSettings->GetGameData('HotGemsPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('HotGemsPTMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('HotGemsPTMTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('HotGemsPTMFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('HotGemsPTMBonusWin', 0);
                                $slotSettings->SetGameData('HotGemsPTMFreeGames', 15);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"slotBet":' . $betLine . ',"gameId":2500966479,"drawId":0,"newBalance":' . $balanceInCents . '},"ID":46300,"umid":31}';
                            $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":31}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":32}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40083,"umid":33}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40083}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40083}';
                            $result_tmp[] = '3:::{"data":{"currentWin":' . $totalWin . ',"drawState":{"event":[{"seq":0,"id":' . $reels['rp'][0] . ',"type":"24:A"},{"seq":1,"id":' . $reels['rp'][1] . ',"type":"4:Q"},{"seq":2,"id":' . $reels['rp'][2] . ',"type":"23:T"},{"seq":3,"id":' . $reels['rp'][3] . ',"type":"32:Q"},{"seq":4,"id":' . $reels['rp'][4] . ',"type":"4:A"}],"gameStages":[{"stage":[{"display":"J,A,J,W,J;A,Q,T,Q,A;N,L,K,K,D","multiplier":1,"stage":1}],"spins":0,"stages":1,"wCapMaxWin":1000000.0}],"bet":[{"toPayout":0.25,"pick":"L25","seq":0,"stake":0.25,"type":"line","won":"false"}],"drawId":0,"seed":389802986,"state":"settling"},"gameId":2501019450},"ID":46303,"umid":49}';
                            $result_tmp[] = '3:::{"data":{},"ID":46297,"umid":39}';
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"rStr":"' . $rStr . '","sc":' . $scattersCount . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('HotGemsPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('HotGemsPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('HotGemsPTMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('HotGemsPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"gts50","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
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
                                    if( isset($lastEvent->serverResponse->freeSeq) ) 
                                    {
                                        $slotSettings->SetGameData('HotGemsPTMFreeSpins', $lastEvent->serverResponse->freeSeq);
                                        $slotSettings->SetGameData('HotGemsPTMFreeLogs', $lastEvent->serverResponse->freeLogSeq);
                                        $slotSettings->SetGameData('HotGemsPTMbonusMpl', $lastEvent->serverResponse->bonusMpl);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                        if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'gts50');
                                        }
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '46290':
                                $lastEvent = $slotSettings->GetHistory();
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                    $fsReel = $slotSettings->GetGameData($slotSettings->slotId . 'FreeLogs');
                                    $fsNum = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $startWin = $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin');
                                    $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                    $drawId = 1;
                                    $freeSpinList = [];
                                    $cSpin = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    for( $i = 0; $i <= 14; $i++ ) 
                                    {
                                        $vl = json_decode($fsReel[$i], true);
                                        $cWin = $vl['serverResponse']['totalWin'];
                                        $rp = $vl['serverResponse']['reelsSymbols']['rp'];
                                        $freeSpinList[] = '{"event":[{"seq":0,"id":' . $rp[0] . ',"type":"29:Q"},{"seq":1,"id":' . $rp[1] . ',"type":"25:D"},{"seq":2,"id":' . $rp[2] . ',"type":"13:L"},{"seq":3,"id":' . $rp[3] . ',"type":"0:K"},{"seq":4,"id":' . $rp[4] . ',"type":"15:H"}],"gameStages":[{"stage":[{"winLine":[{"length":3,"line":10,"offsets":"5,1,2","payout":' . $cWin . ',"prize":"3Q"}],"display":"W,Q,Q,T,A;Q,D,L,K,H;J,J,A,N,J","multiplier":1,"stage":1}],"currentSpins":' . (15 - $cSpin) . ',"runningTotal":' . ($totalWin + $startWin) . ',"spins":15,"stages":1}],"drawId":' . $drawId . ',"seed":-2034361349,"state":"settling"}';
                                        $drawId++;
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                    $result_tmp[] = '3:::{"data":{"savedState":[{"seq":0,"attr21":"1"}],"history":{"game":{"savedState":[{"seq":0,"attr21":"8"}],"drawState":[{"event":[{"seq":0,"id":24,"type":"24:A"},{"seq":1,"id":18,"type":"18:J"},{"seq":2,"id":3,"type":"3:N"},{"seq":3,"id":30,"type":"30:T"},{"seq":4,"id":2,"type":"2:Q"}],"drawId":0}],"gameId":2483023545}},"drawState":[{"event":[{"seq":0,"id":2,"type":"2:Q"},{"seq":1,"id":19,"type":"19:T"},{"seq":2,"id":7,"type":"7:Z"},{"seq":3,"id":17,"type":"17:S"},{"seq":4,"id":32,"type":"32:F"}],"gameStages":[{"stage":[{"winLine":[{"length":3,"line":9,"offsets":"5,11,7","payout":0,"prize":"3Q"}],"display":"W,J,T,T,L;Q,T,Z,S,F;J,Q,F,J,J","multiplier":1,"stage":1},{"winLine":[{"length":2,"line":20,"offsets":"0,11","payout":0.00,"prize":"2T"},{"length":2,"line":24,"offsets":"0,11","payout":0,"prize":"2T"}],"scatter":{"length":2,"offsets":"1,8","payout":' . $startWin . '},"display":"T,S,H,T,L;W,J,T,S,F;J,T,F,J,J","multiplier":1,"stage":2},{"bonus":{"offsets":"0,9,12","spins":15},"display":"F,T,H,H,L;W,N,T,T,F;J,J,F,J,J","multiplier":1,"stage":3},{"winLine":[{"length":2,"line":4,"offsets":"0,6","payout":0.00,"prize":"2N"},{"length":2,"line":12,"offsets":"0,6","payout":0.00,"prize":"2N"},{"length":2,"line":14,"offsets":"0,6","payout":0.00,"prize":"2N"}],"display":"N,T,A,H,T;W,N,H,T,L;J,J,T,J,J","multiplier":1,"stage":4},{"display":"J,W,A,H,T;W,T,H,T,L;J,J,T,J,J","multiplier":1,"stage":5}],"currentSpins":0,"spins":15,"stages":5,"wCapMaxWin":1000000}],"bet":[{"payout":' . $totalWin . ',"pick":"L25","seq":0,"stake":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 25) . ',"type":"line","won":"true"}],"drawId":0,"seed":-2033607610,"state":"settling"},' . implode(',', $freeSpinList) . '],"gameId":2483023643,"nextDrawId":""},"ID":46291,"umid":29}';
                                }
                                else
                                {
                                    $result_tmp[] = '3:::{"data":{"history":{"game":{"savedState":[{"seq":0,"attr21":"null"}],"drawState":[],"gameId":2383768333}},"gameId":2478431530,"nextDrawId":"0"},"ID":46291,"umid":28}';
                                }
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
