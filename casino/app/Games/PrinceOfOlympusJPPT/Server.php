<?php 
namespace VanguardLTE\Games\PrinceOfOlympusJPPT
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
                            if( isset($postData['ID']) && $postData['ID'] == 40041 ) 
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            else if( isset($postData['ID']) ) 
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['spinType']) ) 
                        {
                            $result_tmp = [];
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusWin', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeGames', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTTotalWin', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeBalance', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeStartWin', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeMpl', $slotSettings->slotFreeMpl);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTIncreaseMpl', 0);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStep', 0);
                            }
                            else if( $postData['spinType'] == 'free' || $postData['spinType'] == 're' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTCurrentFreeGame', $slotSettings->GetGameData('PrinceOfOlympusJPPTCurrentFreeGame') + 1);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTIncreaseMpl', $slotSettings->GetGameData('PrinceOfOlympusJPPTIncreaseMpl') + 1);
                                if( $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusStep') != 1 ) 
                                {
                                    $bonusMpl = 3;
                                }
                                else
                                {
                                    $bonusMpl = 1;
                                }
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
                            $postData['bet'] = $postData['bet'] / 100;
                            for( $i = 0; $i < count($postData['lines']); $i++ ) 
                            {
                                if( $postData['lines'][$i] > 0 ) 
                                {
                                    $lines = $i + 1;
                                }
                                else
                                {
                                    break;
                                }
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
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($postData['bet']);
                                if( is_array($jackState) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                }
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $randomBonusIdRand = 1;
                                $winType = 'bonus';
                            }
                            else
                            {
                                $randomBonusIdRand = 0;
                            }
                            $winType2 = '';
                            if( $randomBonusIdRand == 1 && $winType == 'bonus' ) 
                            {
                                $winType2 = 'bonus2';
                                $winType = 'none';
                            }
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
                                    0
                                ];
                                $wild = ['0'];
                                $scatter = '12';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                if( $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusStep') == 1 && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $reels['reel3'][0] = 0;
                                    $reels['reel3'][1] = 0;
                                    $reels['reel3'][2] = 0;
                                }
                                for( $k = 0; $k < $lines; $k++ ) 
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
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                $scattersStr = '{';
                                $scattersCount = 0;
                                $scattersCount2 = 0;
                                $scattersCount3 = 0;
                                $scattersCount4 = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                        if( $reels['reel' . $r][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                        if( $reels['reel' . $r][$p] == '0' ) 
                                        {
                                            $scattersCount3++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                        if( $reels['reel' . $r][$p] == '0' && $p == 1 && $r == 3 ) 
                                        {
                                            $scattersCount4++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betLine * $lines * $bonusMpl;
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
                                            exit( $response );
                                        }
                                        if( $postData['slotEvent'] == 'freespin' && $totalWin <= $spinWinLimit && $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusStep') == 1 ) 
                                        {
                                            break;
                                        }
                                        if( ($scattersCount >= 3 || $scattersCount2 >= 2 || $scattersCount3 >= 3 || $scattersCount4 >= 1 && $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusStep') != 1) && $winType != 'bonus' && $postData['slotEvent'] != 'freespin' ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $scattersCount2 >= 2 ) 
                                            {
                                                $wins_ = [
                                                    1, 
                                                    1, 
                                                    1, 
                                                    1, 
                                                    2, 
                                                    2, 
                                                    2, 
                                                    2, 
                                                    2, 
                                                    3, 
                                                    3, 
                                                    3, 
                                                    3, 
                                                    4, 
                                                    4, 
                                                    4, 
                                                    4, 
                                                    5, 
                                                    5, 
                                                    5, 
                                                    5, 
                                                    5, 
                                                    8, 
                                                    8, 
                                                    8, 
                                                    8, 
                                                    10, 
                                                    10, 
                                                    10, 
                                                    10
                                                ];
                                                shuffle($wins_);
                                                $hBonusMpl = rand(1, 5);
                                                $hBonusWin = 0;
                                                $hBonusWinArr = [];
                                                for( $cb = 0; $cb < 3; $cb++ ) 
                                                {
                                                    $cw = $wins_[$cb] * $betLine * $lines * $hBonusMpl;
                                                    $hBonusWin += $cw;
                                                    $hBonusWinArr[] = $wins_[$cb];
                                                }
                                                $slotSettings->SetGameData('PrinceOfOlympusJPPTHydraBonusArr', $hBonusWinArr);
                                                $slotSettings->SetGameData('PrinceOfOlympusJPPTHydraBonusMpl', $hBonusMpl);
                                                $totalWin += $hBonusWin;
                                            }
                                            if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
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
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusWin', $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin') + $totalWin);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTTotalWin', $totalWin);
                            }
                            $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStart', false);
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStart', true);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStep', 0);
                                if( $slotSettings->GetGameData('PrinceOfOlympusJPPTFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeGames', $slotSettings->GetGameData('PrinceOfOlympusJPPTFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTIncreaseMpl', 0);
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeStartWin', $totalWin);
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusWin', 0);
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTFreeGames', $slotSettings->slotFreeCount);
                                }
                            }
                            if( $scattersCount4 >= 1 ) 
                            {
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStart', true);
                                $slotSettings->SetGameData('PrinceOfOlympusJPPTBonusStep', 1);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $spinState = 'REGULAR';
                            if( $postData['spinType'] == 'free' ) 
                            {
                                $spinState = 'FREE';
                            }
                            $isBonus = 'false';
                            if( $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusStart') ) 
                            {
                                $isBonus = 'true';
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"IncreaseMpl":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTIncreaseMpl') . ',"linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTBonusWin') . ',"FreeMpl":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTFreeMpl') . ',"freeStartWin":' . $slotSettings->GetGameData('PrinceOfOlympusJPPTFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $result_tmp[] = '3:::{"data":{"$winType":"' . $winType . '","$i":' . $i . ',"credit":' . $balanceInCents . ',"results":[' . implode(',', $reels['rp']) . '],"windowId":"Adbmao"},"ID":40022,"umid":59}';
                            if( $winType2 == 'bonus2' && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $winsArr = [
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
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $jids = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $jid = 0;
                                $JackWinID = $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID') + 1;
                                shuffle($winsArr);
                                for( $i = 0; $i < 20; $i++ ) 
                                {
                                    if( $jids[$winsArr[$i]] == 2 ) 
                                    {
                                        $winsArr[$i] = $JackWinID;
                                    }
                                    $jids[$winsArr[$i]]++;
                                    if( $jids[$winsArr[$i]] >= 3 ) 
                                    {
                                        $jid = $winsArr[$i];
                                        break;
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                                $slotSettings->SetBalance($slotSettings->slotJackpot[$jid - 1]);
                                $jackWin = $slotSettings->slotJackpot[$jid - 1] * 100;
                                $jWin = $slotSettings->slotJackpot[$jid - 1];
                                $result_tmp[] = '3:::{"data":{"startTime":1,"winningLevel":' . $jid . ',"totalWin":' . $jackWin . ',"reelInfo":[' . implode(',', $winsArr) . '],"windowId":"33zr6v"},"ID":40071,"umid":40}';
                                $slotSettings->SaveLogReport($response, $betLine, $lines, $jWin, 'JPG');
                            }
                            else
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                            }
                            $result_tmp[] = '3:::{"data":{"windowId":"Hr1cOy"},"ID":40072,"umid":44}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":45}';
                            $response = implode('------', $result_tmp);
                            $slotSettings->SaveGameData();
                            $slotSettings->SaveGameDataStatic();
                            echo $response;
                        }
                        switch( $umid ) 
                        {
                            case '47850':
                                if( $postData['stage'] == 0 ) 
                                {
                                    $BonusWinArr = $slotSettings->GetGameData('PrinceOfOlympusJPPTHydraBonusArr');
                                    if( !is_array($BonusWinArr) ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid slot state"}';
                                        exit( $response );
                                    }
                                    if( count($BonusWinArr) == 1 ) 
                                    {
                                        $aStr = ',"rage":0,"remainingValues": [2, 1, 3, 2, 8, 5, 1]';
                                    }
                                    else
                                    {
                                        $aStr = '';
                                    }
                                    $result_tmp[] = '3:::{"data":{"value":' . array_shift($BonusWinArr) . ',"stage":0' . $aStr . ',"pickIndex":' . $postData['pickIndex'] . ',"windowId":"LZTK9E"},"ID":47851,"umid":209}';
                                    $slotSettings->SetGameData('PrinceOfOlympusJPPTHydraBonusArr', $BonusWinArr);
                                }
                                else
                                {
                                    $mpls = [
                                        2, 
                                        5, 
                                        3
                                    ];
                                    $mpls[$postData['pickIndex']] = $slotSettings->GetGameData('PrinceOfOlympusJPPTHydraBonusMpl');
                                    $result_tmp[] = '3:::{"data":{"allValues":[' . implode(',', $mpls) . '],"stage":1,"pickIndex":' . $postData['pickIndex'] . ',"windowId":"LZTK9E"},"ID":47852,"umid":3008}';
                                }
                                break;
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
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
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"hrcls","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeMpl', $lastEvent->serverResponse->FreeMpl);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'IncreaseMpl', $lastEvent->serverResponse->IncreaseMpl);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'hrcls');
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
                                    $bonusOpt = '';
                                    $result_tmp[] = '3:::{"data":{"freespins":{"respinreels":[0,0,0,0,0],"respinsleft":0,"numFreeSpins":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100) . ',"rows":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'LinesArr')) . '],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[31,40,10,36,43],"coins":1,"multiplier":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeMpl') . ',"mode":1},"reelinfo":[15,7,36,1,19],"windowId":"LZTK9E"},"ID":47854,"umid":27}';
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
