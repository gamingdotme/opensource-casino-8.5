<?php 
namespace VanguardLTE\Games\NianNianYouYuPTM
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"drgj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
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
                            }
                            $umid = 0;
                        }
                        if( isset($postData['ID']) && $postData['ID'] == 41020 && $slotSettings->GetGameData('NianNianYouYuPTMBonusStart') ) 
                        {
                            $result_tmp = [];
                            if( $postData['type'] == 'spin' ) 
                            {
                                $jackWin = '';
                                $reelPos = [
                                    0, 
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    5, 
                                    6, 
                                    7, 
                                    8, 
                                    9
                                ];
                                shuffle($reelPos);
                                if( $reelPos[0] == 0 ) 
                                {
                                    $curJID = 1;
                                }
                                else if( $reelPos[0] >= 1 && $reelPos[0] <= 2 ) 
                                {
                                    $curJID = 2;
                                }
                                else if( $reelPos[0] >= 3 && $reelPos[0] <= 5 ) 
                                {
                                    $curJID = 3;
                                }
                                else if( $reelPos[0] >= 6 && $reelPos[0] <= 9 ) 
                                {
                                    $curJID = 4;
                                }
                                $gJackID = $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID');
                                if( $slotSettings->GetGameData('NianNianYouYuPTM_JP' . $curJID . 'Cnt') == 2 ) 
                                {
                                    $curJID = $gJackID + 1;
                                    if( $curJID == 1 ) 
                                    {
                                        $reelPos[0] = 0;
                                    }
                                    if( $curJID == 2 ) 
                                    {
                                        $reelPos[0] = rand(1, 2);
                                    }
                                    if( $curJID == 3 ) 
                                    {
                                        $reelPos[0] = rand(3, 5);
                                    }
                                    if( $curJID == 4 ) 
                                    {
                                        $reelPos[0] = rand(6, 9);
                                    }
                                }
                                $slotSettings->SetGameData('NianNianYouYuPTM_JP' . $curJID . 'Cnt', $slotSettings->GetGameData('NianNianYouYuPTM_JP' . $curJID . 'Cnt') + 1);
                                $jWin = 0;
                                if( $slotSettings->GetGameData('NianNianYouYuPTM_JP' . $curJID . 'Cnt') >= 3 ) 
                                {
                                    $slotSettings->SetBalance($slotSettings->slotJackpot[$curJID - 1]);
                                    $jackWin = ',"winAmount":' . ($slotSettings->slotJackpot[$curJID - 1] * 100);
                                    $jWin = $slotSettings->slotJackpot[$curJID - 1];
                                    $slotSettings->ClearJackpot($curJID - 1);
                                    $slotSettings->SetGameData('NianNianYouYuPTMBonusStart', false);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', -1);
                                }
                                $result_tmp[] = '3:::{"data":{"symbol":"","reelStop":' . $reelPos[0] . '' . $jackWin . ',"windowId":"h72Yp3"},"ID":41021,"umid":435}';
                                $rp = json_decode($slotSettings->GetGameData('NianNianYouYuPTMJackReport'));
                                $rp->serverResponse->jackpotSelected[0] = $slotSettings->GetGameData('NianNianYouYuPTM_JP1Cnt');
                                $rp->serverResponse->jackpotSelected[1] = $slotSettings->GetGameData('NianNianYouYuPTM_JP2Cnt');
                                $rp->serverResponse->jackpotSelected[2] = $slotSettings->GetGameData('NianNianYouYuPTM_JP3Cnt');
                                $rp->serverResponse->jackpotSelected[3] = $slotSettings->GetGameData('NianNianYouYuPTM_JP4Cnt');
                                $rp->serverResponse->jackpotReel = $reelPos[0];
                                $rp->serverResponse->BonusStart = $slotSettings->GetGameData('NianNianYouYuPTMBonusStart');
                                $rp_s = json_encode($rp);
                                $slotSettings->SetGameData('NianNianYouYuPTMJackReport', $rp_s);
                                if( $slotSettings->GetGameData('NianNianYouYuPTM_JP' . $curJID . 'Cnt') >= 3 ) 
                                {
                                    $slotSettings->SaveLogReport($rp_s, 0, 0, $jWin, 'JPG');
                                }
                                else
                                {
                                    $slotSettings->SaveLogReport($rp_s, 0, 0, $jWin, 'jackpot');
                                }
                            }
                            if( $postData['type'] == 'continue' ) 
                            {
                                $result_tmp[] = '3:::{"data":{"symbol":2,"reelStop":2,"windowId":"h72Yp3"},"umid":435}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['index']) ) 
                        {
                            $result_tmp = [];
                            if( $slotSettings->GetGameData('NianNianYouYuPTMBonusStep') == 0 ) 
                            {
                                $WinArr = [
                                    25, 
                                    22, 
                                    20, 
                                    17, 
                                    15, 
                                    12, 
                                    10, 
                                    8, 
                                    5, 
                                    3, 
                                    1, 
                                    -1, 
                                    -2, 
                                    -3, 
                                    -4, 
                                    -5, 
                                    -6, 
                                    -7, 
                                    -8
                                ];
                                shuffle($WinArr);
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusOpt', $WinArr);
                                $curWin = $WinArr[$postData['index']];
                                if( $curWin > 0 ) 
                                {
                                    $slotSettings->SetGameData('NianNianYouYuPTMFreeGames', $slotSettings->GetGameData('NianNianYouYuPTMFreeGames') + $curWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('NianNianYouYuPTMFreeMpl', $slotSettings->GetGameData('NianNianYouYuPTMFreeMpl') + ($curWin * -1));
                                }
                                $result_tmp[] = '3:::{"data":{"pick":' . $postData['index'] . ',"values":[' . $curWin . '],"windowId":"h72Yp3"},"ID":49022,"umid":448}';
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusStep', 1);
                            }
                            else if( $slotSettings->GetGameData('NianNianYouYuPTMBonusStep') == 1 ) 
                            {
                                $WinArr = $slotSettings->GetGameData('NianNianYouYuPTMBonusOpt');
                                $curWin = $WinArr[$postData['index']];
                                if( $curWin > 10 ) 
                                {
                                    $slotSettings->SetGameData('NianNianYouYuPTMFreeGames', $slotSettings->GetGameData('NianNianYouYuPTMFreeGames') + $curWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('NianNianYouYuPTMFreeMpl', $slotSettings->GetGameData('NianNianYouYuPTMFreeMpl') + ($curWin * -1));
                                }
                                $result_tmp[] = '3:::{"data":{"pick":' . $postData['index'] . ',"values":[' . $curWin . ',' . $WinArr[2] . ',' . $WinArr[3] . ',' . $WinArr[4] . '],"windowId":"h72Yp3"},"ID":49022,"umid":448}';
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusStep', 2);
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
                                $slotSettings->SetGameData('NianNianYouYuPTM_JP1Cnt', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTM_JP2Cnt', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTM_JP3Cnt', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTM_JP4Cnt', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusWin', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMFreeGames', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMTotalWin', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMFreeBalance', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMFreeStartWin', 0);
                                $slotSettings->SetGameData('NianNianYouYuPTMFreeMpl', $slotSettings->slotFreeMpl);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('NianNianYouYuPTMCurrentFreeGame', $slotSettings->GetGameData('NianNianYouYuPTMCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->GetGameData('NianNianYouYuPTMFreeMpl');
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
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
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
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', 0);
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $winType = 'bonus';
                            }
                            else if( $winType == 'bonus' ) 
                            {
                                $winType = 'win';
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
                                $scatter = '11';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
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
                                            if( $s[4] == $csym || in_array($s[4], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[4], $wild) || in_array($s[3], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[4], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[4], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('NianNianYouYuPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    $isScat = false;
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter || $reels['reel' . $r][$p] == '0' ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                            $isScat = true;
                                        }
                                    }
                                    if( !$isScat ) 
                                    {
                                        break;
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
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusWin', $slotSettings->GetGameData('NianNianYouYuPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('NianNianYouYuPTMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('NianNianYouYuPTMTotalWin', $totalWin);
                            }
                            $slotSettings->SetGameData('NianNianYouYuPTMBonusStart', false);
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $spinState = 'REGULAR';
                            if( $postData['spinType'] == 'free' ) 
                            {
                                $spinState = 'FREE';
                            }
                            $isBonus = 'false';
                            $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"results":[' . implode(',', $reels['rp']) . '],"windowId":"Adbmao"},"ID":40022,"umid":59}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $jackpotSelected = [
                                0, 
                                0, 
                                0, 
                                0
                            ];
                            $jackpotReel = 0;
                            if( $winType == 'bonus' ) 
                            {
                                $postData['slotEvent'] = 'jackpot';
                                $slotSettings->SetGameData('NianNianYouYuPTMBonusStart', true);
                                $result_tmp[] = '3:::{"data":{"state":"start_jackpot_game","windowId":"5Czr6v"},"ID":41021,"umid":40}';
                            }
                            $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"drgj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusStart":' . json_encode($slotSettings->GetGameData('NianNianYouYuPTMBonusStart')) . ',"JackWinID":' . $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID') . ',"jackpotReel":0,"jackpotSelected":[' . implode(',', $jackpotSelected) . '],"linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('NianNianYouYuPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('NianNianYouYuPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('NianNianYouYuPTMBonusWin') . ',"FreeMpl":' . $slotSettings->GetGameData('NianNianYouYuPTMFreeMpl') . ',"freeStartWin":' . $slotSettings->GetGameData('NianNianYouYuPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SetGameData('NianNianYouYuPTMJackReport', $response);
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $response = implode('------', $result_tmp);
                            $slotSettings->SaveGameData();
                            $slotSettings->SaveGameDataStatic();
                            echo $response;
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"drgj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '}]}},"ID":40042,"umid":11}';
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
                            case '40036':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"nian","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
                                break;
                            case '40020':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData('NianNianYouYuPTM_JP1Cnt', $lastEvent->serverResponse->jackpotSelected[0]);
                                    $slotSettings->SetGameData('NianNianYouYuPTM_JP2Cnt', $lastEvent->serverResponse->jackpotSelected[1]);
                                    $slotSettings->SetGameData('NianNianYouYuPTM_JP3Cnt', $lastEvent->serverResponse->jackpotSelected[2]);
                                    $slotSettings->SetGameData('NianNianYouYuPTM_JP4Cnt', $lastEvent->serverResponse->jackpotSelected[3]);
                                    $slotSettings->SetGameData('NianNianYouYuPTM_JPReel', $lastEvent->serverResponse->jackpotReel);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeMpl', $lastEvent->serverResponse->FreeMpl);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $lastEvent->serverResponse->JackWinID);
                                    if( $lastEvent->responseType == 'jackpot' && $lastEvent->serverResponse->jackpotSelected[0] < 3 && $lastEvent->serverResponse->jackpotSelected[1] < 3 && $lastEvent->serverResponse->jackpotSelected[2] < 3 && $lastEvent->serverResponse->jackpotSelected[3] < 3 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'nian');
                                        $slotSettings->SetGameData('NianNianYouYuPTMJackReport', json_encode($lastEvent));
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40024':
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
                                    $lastEvent = $slotSettings->GetHistory();
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                    if( $lastEvent != 'NULL' ) 
                                    {
                                        $slotSettings->SetGameData('NianNianYouYuPTM_JP1Cnt', $lastEvent->serverResponse->jackpotSelected[0]);
                                        $slotSettings->SetGameData('NianNianYouYuPTM_JP2Cnt', $lastEvent->serverResponse->jackpotSelected[1]);
                                        $slotSettings->SetGameData('NianNianYouYuPTM_JP3Cnt', $lastEvent->serverResponse->jackpotSelected[2]);
                                        $slotSettings->SetGameData('NianNianYouYuPTM_JP4Cnt', $lastEvent->serverResponse->jackpotSelected[3]);
                                        $slotSettings->SetGameData('NianNianYouYuPTM_JPReel', $lastEvent->serverResponse->jackpotReel);
                                        $slotSettings->SetGameData('NianNianYouYuPTMBonusStart', $lastEvent->serverResponse->BonusStart);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeMpl', $lastEvent->serverResponse->FreeMpl);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                        if( $lastEvent->responseType == 'jackpot' && $lastEvent->serverResponse->jackpotSelected[0] < 3 && $lastEvent->serverResponse->jackpotSelected[1] < 3 && $lastEvent->serverResponse->jackpotSelected[2] < 3 && $lastEvent->serverResponse->jackpotSelected[3] < 3 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'nian');
                                            $slotSettings->SetGameData('NianNianYouYuPTMJackReport', json_encode($lastEvent));
                                        }
                                    }
                                    $bonusOpt = '';
                                    $result_tmp[] = '3:::{"data":{"jackpotData":{"lastStop":' . $lastEvent->serverResponse->jackpotReel . ',selectedItems:[' . $lastEvent->serverResponse->jackpotSelected[0] . ',' . $lastEvent->serverResponse->jackpotSelected[1] . ',' . $lastEvent->serverResponse->jackpotSelected[2] . ',' . $lastEvent->serverResponse->jackpotSelected[3] . ']},bonusGameName:"",' . $bonusOpt . '"jpWin":100,"reelinfo":[96,93,66,6,22],"windowId":"bVDWxS"},"ID":41010,"umid":29}';
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
