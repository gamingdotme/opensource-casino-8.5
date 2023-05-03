<?php 
namespace VanguardLTE\Games\FeiCuiGongZhuJPPTM
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
                        if( isset($postData['ID']) && $postData['ID'] == 41020 && $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusStart') ) 
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
                                if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP' . $curJID . 'Cnt') == 2 ) 
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
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP' . $curJID . 'Cnt', $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP' . $curJID . 'Cnt') + 1);
                                $jWin = 0;
                                if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP' . $curJID . 'Cnt') >= 3 ) 
                                {
                                    $slotSettings->SetBalance($slotSettings->slotJackpot[$curJID - 1]);
                                    $jackWin = ',"winAmount":' . ($slotSettings->slotJackpot[$curJID - 1] * 100);
                                    $jWin = $slotSettings->slotJackpot[$curJID - 1];
                                    $slotSettings->ClearJackpot($curJID - 1);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStart', false);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', -1);
                                }
                                $result_tmp[] = '3:::{"data":{"symbol":"","reelStop":' . $reelPos[0] . '' . $jackWin . ',"windowId":"h72Yp3"},"ID":41021,"umid":435}';
                                $rp = json_decode($slotSettings->GetGameData('FeiCuiGongZhuJPPTMJackReport'));
                                $rp->serverResponse->jackpotSelected[0] = $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP1Cnt');
                                $rp->serverResponse->jackpotSelected[1] = $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP2Cnt');
                                $rp->serverResponse->jackpotSelected[2] = $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP3Cnt');
                                $rp->serverResponse->jackpotSelected[3] = $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP4Cnt');
                                $rp->serverResponse->jackpotReel = $reelPos[0];
                                $rp->serverResponse->BonusStart = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusStart');
                                $rp_s = json_encode($rp);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMJackReport', $rp_s);
                                if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTM_JP' . $curJID . 'Cnt') >= 3 ) 
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
                            if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusStep') == 0 ) 
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
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusOpt', $WinArr);
                                $curWin = $WinArr[$postData['index']];
                                if( $curWin > 0 ) 
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeGames', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeGames') + $curWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeMpl', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeMpl') + ($curWin * -1));
                                }
                                $result_tmp[] = '3:::{"data":{"pick":' . $postData['index'] . ',"values":[' . $curWin . '],"windowId":"h72Yp3"},"ID":49022,"umid":448}';
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStep', 1);
                            }
                            else if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusStep') == 1 ) 
                            {
                                $WinArr = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusOpt');
                                $curWin = $WinArr[$postData['index']];
                                if( $curWin > 10 ) 
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeGames', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeGames') + $curWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeMpl', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeMpl') + ($curWin * -1));
                                }
                                $result_tmp[] = '3:::{"data":{"pick":' . $postData['index'] . ',"values":[' . $curWin . ',' . $WinArr[2] . ',' . $WinArr[3] . ',' . $WinArr[4] . '],"windowId":"h72Yp3"},"ID":49022,"umid":448}';
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStep', 2);
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
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP1Cnt', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP2Cnt', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP3Cnt', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP4Cnt', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusWin', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeGames', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMTotalWin', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeBalance', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeStartWin', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeMpl', $slotSettings->slotFreeMpl);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMCurrentFreeGame', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeMpl');
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
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMAllBet', $postData['bet']);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBetLine', $betLine);
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', 0);
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $winType == 'bonus' ) 
                            {
                                $winType = 'bonus2';
                            }
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $winType = 'bonus';
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
                                $scatter = '9';
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
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
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
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    $isScat = false;
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                            $isScat = true;
                                        }
                                        if( $reels['reel' . $r][$p] == '10' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                    }
                                }
                                $prizeBonusNum = 0;
                                $prizeBonusStart = false;
                                if( $scattersCount2 >= 3 && $scattersCount < 3 && $winType == 'bonus2' ) 
                                {
                                    if( $scattersCount2 >= 3 ) 
                                    {
                                        $prizeBonusStart = true;
                                        $prizeBonusNum = 3;
                                    }
                                    if( $scattersCount2 >= 4 ) 
                                    {
                                        $prizeBonusStart = true;
                                        $prizeBonusNum = 4;
                                    }
                                    if( $scattersCount2 >= 5 ) 
                                    {
                                        $prizeBonusStart = true;
                                        $prizeBonusNum = 5;
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
                                        if( ($scattersCount >= 3 || $scattersCount2 >= 3) && $winType != 'bonus2' ) 
                                        {
                                        }
                                        else if( $totalWin <= $spinWinLimit && $winType == 'bonus2' ) 
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
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStep', 0);
                                if( $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeGames', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeGames') + 10);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMIncreaseMpl', 0);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeStartWin', $totalWin);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusWin', 0);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMFreeGames', $slotSettings->slotFreeCount);
                                }
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusWin', $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMTotalWin', $totalWin);
                            }
                            $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStart', false);
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
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStart', true);
                                $result_tmp[] = '3:::{"data":{"state":"start_jackpot_game","windowId":"5Czr6v"},"ID":41021,"umid":40}';
                            }
                            $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"drgj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $bStr = '"winValues":[0,0,0,0,0],"prizeBonusNum":' . $prizeBonusNum . ',"PickedCount":0,"PickedCountArr":[],';
                            if( $prizeBonusStart ) 
                            {
                                $postData['slotEvent'] = 'prize_bonus';
                                $prizeResult = $slotSettings->EggBonus($betLine, $prizeBonusNum);
                                $bStr = '"winValues":[' . implode(',', $prizeResult['curValues']) . '],"prizeBonusNum":' . $prizeBonusNum . ',"PickedCount":0,"PickedCountArr":[],';
                                $result_tmp[] = '3:::{"data":{"winValues":[' . implode(',', $prizeResult['curValues']) . '],"windowId":"NdXGjL"},"ID":40146,"umid":1825}';
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMWinValues', $prizeResult['curValues']);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMPickedCount', 0);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMPickedCountArr', []);
                                $slotSettings->SetGameData('FeiCuiGongZhuJPPTMprizeBonusNum', $prizeBonusNum);
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{' . $bStr . '"BonusStart":' . json_encode($slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusStart')) . ',"JackWinID":' . $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID') . ',"jackpotReel":0,"jackpotSelected":[' . implode(',', $jackpotSelected) . '],"linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('FeiCuiGongZhuJPPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBonusWin') . ',"FreeMpl":' . $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeMpl') . ',"freeStartWin":' . $slotSettings->GetGameData('FeiCuiGongZhuJPPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SetGameData('FeiCuiGongZhuJPPTMLastResponse', $response);
                            $slotSettings->SetGameData('FeiCuiGongZhuJPPTMJackReport', $response);
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $response = implode('------', $result_tmp);
                            $slotSettings->SaveGameData();
                            $slotSettings->SaveGameDataStatic();
                            echo $response;
                        }
                        switch( $umid ) 
                        {
                            case '40145':
                                if( $postData['pickIndex'] >= 0 && $slotSettings->GetGameData('FeiCuiGongZhuJPPTMPickedCount') < $slotSettings->GetGameData('FeiCuiGongZhuJPPTMprizeBonusNum') ) 
                                {
                                    $lr = json_decode($slotSettings->GetGameData('FeiCuiGongZhuJPPTMLastResponse'));
                                    $values = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMWinValues');
                                    $pick = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMPickedCount');
                                    $pickArr = $slotSettings->GetGameData('FeiCuiGongZhuJPPTMPickedCountArr');
                                    $reportWin = $values[$pick] * $slotSettings->GetGameData('FeiCuiGongZhuJPPTMBetLine');
                                    $slotSettings->SetBalance($reportWin);
                                    $pick++;
                                    $pickArr[] = $postData['pickIndex'];
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMPickedCount', $pick);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMPickedCountArr', $pickArr);
                                    $lr->serverResponse->PickedCount = $pick;
                                    $lr->serverResponse->PickedCountArr = $pickArr;
                                    $slotSettings->SaveLogReport(json_encode($lr), 0, 0, $reportWin, 'BG2');
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTMLastResponse', json_encode($lr));
                                }
                                $result_tmp[] = '3:::{"data":{"pickId":' . $postData['pickIndex'] . ',"windowId":"I8B1nC"},"ID":41031,"umid":70}';
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"drgj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '}]}},"ID":40042,"umid":11}';
                                $result_tmp[] = '3:::{"data":{"commandId":11666,"params":["0","0","0"]},"ID":50001,"umid":11}';
                                $result_tmp[] = '3:::{"data":{"commandId":13981,"params":["0","1"]},"ID":50001,"umid":12}';
                                $result_tmp[] = '3:::{"data":{"commandId":14080,"params":["0","0"]},"ID":50001,"umid":14}';
                                $result_tmp[] = '3:::{"data":{"keyValueCount":5,"elementsPerKey":1,"params":["10","1","11","500","12","1","13","0","14","0"]},"ID":40716,"umid":15}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":16}';
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":' . $balanceInCents . '},"ID":10006,"umid":17}';
                                $result_tmp[] = '3:::{"data":{},"ID":40292,"umid":18}';
                                break;
                            case '10021':
                                $result_tmp[] = '3:::{"data":{"urls":{"casino-cashier-myaccount":[],"regulation_pt_self_exclusion":[],"link_legal_aams":[],"regulation_pt_player_protection":[],"mobile_cashier":[],"mobile_bank":[],"mobile_bonus_terms":[],"mobile_help":[],"link_responsible":[],"cashier":[{"url":"","priority":1},{"url":"","priority":1}],"gambling_commission":[{"url":"","priority":1},{"url":"","priority":1}],"desktop_help":[],"chat_token":[],"mobile_login_error":[],"mobile_error":[],"mobile_login":[{"url":"","priority":1}],"playerprofile":[{"url":"","priority":1},{"url":"","priority":10}],"link_legal_half":[],"ngmdesktop_quick_deposit":[],"external_login_form":[],"mobile_main_promotions":[],"mobile_lobby":[],"mobile_promotion":[],{"url":"","priority":1},{"url":"","priority":10}],"mobile_withdraw":[],"mobile_funds_trans":[],"mobile_quick_deposit":[],"mobile_history":[],"mobile_deposit_limit":[],"minigames_help":[],"link_legal_18":[],"mobile_responsible":[],"mobile_share":[],"mobile_lobby_error":[],"mobile_mobile_comp_points":[],"mobile_support":[{"url":"","priority":1}],"mobile_chat":[],"mobile_logout":[],"mobile_deposit":[],"invite_friend":[]}},"ID":10011,"umid":19}';
                                break;
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"fcgz","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP1Cnt', $lastEvent->serverResponse->jackpotSelected[0]);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP2Cnt', $lastEvent->serverResponse->jackpotSelected[1]);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP3Cnt', $lastEvent->serverResponse->jackpotSelected[2]);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP4Cnt', $lastEvent->serverResponse->jackpotSelected[3]);
                                    $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JPReel', $lastEvent->serverResponse->jackpotReel);
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
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '"fcgz"');
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTMJackReport', json_encode($lastEvent));
                                    }
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '"fcgz"');
                                    $slotSettings->SetGameData($slotSettings->slotId . 'isFree', 1);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'isFree', 0);
                                }
                                break;
                            case '40036':
                                $result_tmp[] = '3:::{"data":{"brokenGames":[' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '],"windowId":"SuJLru"},"ID":40037,"umid":22}';
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
                                    $lastEvent = $slotSettings->GetHistory();
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                    if( $lastEvent != 'NULL' ) 
                                    {
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP1Cnt', $lastEvent->serverResponse->jackpotSelected[0]);
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP2Cnt', $lastEvent->serverResponse->jackpotSelected[1]);
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP3Cnt', $lastEvent->serverResponse->jackpotSelected[2]);
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JP4Cnt', $lastEvent->serverResponse->jackpotSelected[3]);
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTM_JPReel', $lastEvent->serverResponse->jackpotReel);
                                        $slotSettings->SetGameData('FeiCuiGongZhuJPPTMBonusStart', $lastEvent->serverResponse->BonusStart);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeMpl', $lastEvent->serverResponse->FreeMpl);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    }
                                    $bonusOpt = '';
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'isFree') == 1 ) 
                                    {
                                        $result_tmp[] = '3:::{"data":{"freeSpins":{"numFreeSpins":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100) . ',"rows":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'LinesArr')) . '],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[127,118,41,118,15],"coins":1,"multiplier":3,"mode":1,"startBonus":1},"reelinfo":[34,26,26,78,122],"windowId":"I8B1nC"},"ID":41030,"umid":29}';
                                    }
                                    else
                                    {
                                        $result_tmp[] = '3:::{"data":{"jackpotData":{"lastStop":' . $lastEvent->serverResponse->jackpotReel . ',selectedItems:[' . $lastEvent->serverResponse->jackpotSelected[0] . ',' . $lastEvent->serverResponse->jackpotSelected[1] . ',' . $lastEvent->serverResponse->jackpotSelected[2] . ',' . $lastEvent->serverResponse->jackpotSelected[3] . ']},bonusGameName:"",' . $bonusOpt . '"jpWin":100,"reelinfo":[96,93,66,6,22],"windowId":"bVDWxS"},"ID":48676,"umid":29}';
                                    }
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
