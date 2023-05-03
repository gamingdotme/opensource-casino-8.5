<?php 
namespace VanguardLTE\Games\AnacondaWildPT
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
                        if( isset($postData['spinType']) ) 
                        {
                            $result_tmp = [];
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('AnacondaWildPTBonusWin', 0);
                                $slotSettings->SetGameData('AnacondaWildPTFreeGames', 0);
                                $slotSettings->SetGameData('AnacondaWildPTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('AnacondaWildPTTotalWin', 0);
                                $slotSettings->SetGameData('AnacondaWildPTFreeBalance', 0);
                                $slotSettings->SetGameData('AnacondaWildPTFreeStartWin', 0);
                                $slotSettings->SetGameData('AnacondaWildPTSnake', '');
                                $slotSettings->SetGameData('AnacondaWildPTSnakeDirection', 'right');
                            }
                            else if( $postData['spinType'] == 're' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('AnacondaWildPTCurrentFreeGame', $slotSettings->GetGameData('AnacondaWildPTCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $linesId = [];
                            $linesId[0] = [
                                2, 
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
                                3, 
                                3
                            ];
                            $linesId[2] = [
                                1, 
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
                                4, 
                                4
                            ];
                            $linesId[4] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[5] = [
                                4, 
                                4, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[6] = [
                                1, 
                                2, 
                                3, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[7] = [
                                2, 
                                3, 
                                4, 
                                4, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                4, 
                                3, 
                                2, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[9] = [
                                3, 
                                2, 
                                1, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[10] = [
                                2, 
                                2, 
                                1, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[11] = [
                                2, 
                                2, 
                                3, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[12] = [
                                3, 
                                3, 
                                4, 
                                4, 
                                3, 
                                3
                            ];
                            $linesId[13] = [
                                3, 
                                3, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[14] = [
                                4, 
                                4, 
                                3, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[15] = [
                                1, 
                                1, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                4, 
                                3, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[17] = [
                                2, 
                                1, 
                                2, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[18] = [
                                1, 
                                2, 
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
                                3, 
                                4
                            ];
                            $linesId[20] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[21] = [
                                3, 
                                4, 
                                4, 
                                4, 
                                4, 
                                3
                            ];
                            $linesId[22] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[23] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[24] = [
                                3, 
                                4, 
                                3, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[25] = [
                                4, 
                                3, 
                                4, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[26] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[27] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[28] = [
                                1, 
                                2, 
                                2, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[29] = [
                                4, 
                                3, 
                                3, 
                                4, 
                                4, 
                                3
                            ];
                            $linesId[30] = [
                                3, 
                                4, 
                                4, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[31] = [
                                2, 
                                1, 
                                1, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[32] = [
                                1, 
                                2, 
                                2, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[33] = [
                                4, 
                                3, 
                                3, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[34] = [
                                1, 
                                2, 
                                1, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[35] = [
                                2, 
                                3, 
                                2, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[36] = [
                                3, 
                                4, 
                                3, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[37] = [
                                4, 
                                3, 
                                4, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[38] = [
                                3, 
                                2, 
                                3, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[39] = [
                                2, 
                                1, 
                                2, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[40] = [
                                1, 
                                1, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[41] = [
                                2, 
                                2, 
                                3, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[42] = [
                                4, 
                                4, 
                                3, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[43] = [
                                3, 
                                3, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[44] = [
                                4, 
                                4, 
                                4, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[45] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[46] = [
                                2, 
                                2, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[47] = [
                                3, 
                                3, 
                                3, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[48] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[49] = [
                                4, 
                                3, 
                                2, 
                                3, 
                                2, 
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
                            $betLine = $postData['bet'] / 25;
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $postData['bet'] ) 
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
                                    0
                                ];
                                $wild = [
                                    '0', 
                                    '11'
                                ];
                                $scatter = '11';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $snakeStartPos = '';
                                if( $winType == 'bonus' && $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $snakeStartPos = rand(8, 19);
                                    $rSnake = floor($snakeStartPos / 4) + 1;
                                    $rpSnake = $snakeStartPos - (($rSnake - 1) * 4);
                                    $reels['reel' . $rSnake][$rpSnake] = '11';
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $snake = json_decode($slotSettings->GetGameData('AnacondaWildPTSnake'));
                                    $snakeDir = $slotSettings->GetGameData('AnacondaWildPTSnakeDirection');
                                    $lastPos = count($snake->positions) - 1;
                                    if( $snakeDir == 'right' ) 
                                    {
                                        $snake->positions[] = $snake->positions[$lastPos] + 4;
                                        $newPos = $snake->positions[$lastPos] + 4;
                                        $changeDirect = rand(1, 3);
                                        if( $changeDirect == 1 || $newPos >= 20 ) 
                                        {
                                            $nextDir = rand(1, 2);
                                            if( $nextDir == 1 && $newPos != 0 && $newPos != 4 && $newPos != 8 && $newPos != 12 && $newPos != 16 && $newPos != 20 ) 
                                            {
                                                $snakeDir = 'up';
                                            }
                                            else if( $nextDir == 1 ) 
                                            {
                                                $snakeDir = 'down';
                                            }
                                            if( $nextDir == 2 && $newPos != 3 && $newPos != 7 && $newPos != 11 && $newPos != 15 && $newPos != 19 && $newPos != 23 ) 
                                            {
                                                $snakeDir = 'down';
                                            }
                                            else if( $nextDir == 2 ) 
                                            {
                                                $snakeDir = 'up';
                                            }
                                        }
                                    }
                                    else if( $snakeDir == 'left' ) 
                                    {
                                        $snake->positions[] = $snake->positions[$lastPos] - 4;
                                        $newPos = $snake->positions[$lastPos] - 4;
                                        $startPos = $snake->positions[0];
                                        if( $newPos - 1 == $startPos ) 
                                        {
                                            $snakeDir = 'up';
                                        }
                                        if( $newPos + 1 == $startPos ) 
                                        {
                                            $snakeDir = 'down';
                                        }
                                    }
                                    else if( $snakeDir == 'up' ) 
                                    {
                                        $snake->positions[] = $snake->positions[$lastPos] - 1;
                                        $snakeDir = 'left';
                                    }
                                    else if( $snakeDir == 'down' ) 
                                    {
                                        $snake->positions[] = $snake->positions[$lastPos] + 1;
                                        $snakeDir = 'left';
                                    }
                                    for( $sn = 0; $sn < count($snake->positions); $sn++ ) 
                                    {
                                        $snakePos = $snake->positions[$sn];
                                        $rSnake = floor($snakePos / 4) + 1;
                                        $rpSnake = $snakePos - (($rSnake - 1) * 4);
                                        $reels['reel' . $rSnake][$rpSnake] = '11';
                                    }
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
                                            $s[5] = $reels['reel6'][$linesId[$k][5] - 1];
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) && ($s[5] == $csym || in_array($s[5], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) && in_array($s[5], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) || in_array($s[5], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][6] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":6,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AnacondaWildPTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                    }
                                }
                                $scattersWin = 0;
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
                            $adv = '';
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('AnacondaWildPTBonusWin', $slotSettings->GetGameData('AnacondaWildPTBonusWin') + $totalWin);
                                $slotSettings->SetGameData('AnacondaWildPTTotalWin', $totalWin);
                                $slotSettings->SetGameData('AnacondaWildPTSnake', json_encode($snake));
                                $slotSettings->SetGameData('AnacondaWildPTSnakeDirection', $snakeDir);
                                $adv = ',"adv":" ' . $snake->positions[count($snake->positions) - 1] . '|' . $snake->positions[0] . ' "';
                                if( $snake->positions[count($snake->positions) - 1] == $snake->positions[0] ) 
                                {
                                    $slotSettings->SetGameData('AnacondaWildPTFreeGames', 0);
                                    $slotSettings->SetGameData('AnacondaWildPTCurrentFreeGame', 0);
                                }
                                $respinFollows = 'true';
                                $spinType = 'RE';
                            }
                            else
                            {
                                $spinType = 'REGULAR';
                                $respinFollows = 'false';
                                $slotSettings->SetGameData('AnacondaWildPTTotalWin', $totalWin);
                            }
                            if( $winType == 'bonus' ) 
                            {
                                $slotSettings->SetGameData('AnacondaWildPTFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('AnacondaWildPTBonusWin', $totalWin);
                                $slotSettings->SetGameData('AnacondaWildPTFreeGames', $slotSettings->slotFreeCount);
                                $respinFollows = 'true';
                                $slotSettings->SetGameData('AnacondaWildPTSnake', '{"positions":[' . $snakeStartPos . ']}');
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"spinType":"' . $spinType . '"' . $adv . ',"reelset":0,"siv":[8,8,4,4,6,2,2,5,7,11,3,3,3,6,6,6,6,6,6,0,8,8,8,7],"respinFollows":' . $respinFollows . ',"snakePositions":[' . $slotSettings->GetGameData('AnacondaWildPTSnake') . '],"credit":' . $balanceInCents . ',"results":[' . implode(',', $reels['rp']) . '],"windowId":"2yV1fd"},"ID":49340,"umid":37}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $snakeStr_ = $slotSettings->GetGameData('AnacondaWildPTSnake');
                            if( $snakeStr_ == '' ) 
                            {
                                $snakeStr_ = '""';
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"Snake":' . $snakeStr_ . ',"SnakeDirection":"' . $slotSettings->GetGameData('AnacondaWildPTSnakeDirection') . '","linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('AnacondaWildPTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AnacondaWildPTCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('AnacondaWildPTBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('AnacondaWildPTFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"anwild","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Snake', json_encode($lastEvent->serverResponse->Snake));
                                    $slotSettings->SetGameData($slotSettings->slotId . 'SnakeDirection', $lastEvent->serverResponse->SnakeDirection);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'anwild');
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
                                    $result_tmp[] = '3:::{"data":{"freeSpinsData":{"numFreeSpins":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100 * 25) . ',"totalBet":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100 * 25) . ',"rows":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[71,82,76,79,108,100],"coins":1,"multiplier":1,"mode":0,"startBonus":0},"lpReelset":0,"tgReelset":0,"respinWin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"siv":[7,7,4,4,7,7,11,3,5,11,11,4,7,1,1,6,8,1,1,9,8,4,4,7],"respinFollows":true,"snakePositions":[' . $slotSettings->GetGameData($slotSettings->slotId . 'Snake') . '],"bonusGameName":"anwildAWFeature","reelinfo":[17,76,21,105,3,72],"windowId":"YINqgu"},"ID":49341,"umid":28}';
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
