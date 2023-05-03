<?php 
namespace VanguardLTE\Games\TheWizardOfOZMN
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
                        $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$postData['action'];
                        switch( $aid ) 
                        {
                            case 'Init1':
                            case 'Init2':
                            case 'Act61':
                            case 'Ping':
                            case 'Act58':
                            case 'getBalance':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . ',"Denom":' . ($slotSettings->CurrentDenom * 100) . '}';
                                break;
                            case 'Act41':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . '}';
                                break;
                            case 'Act18':
                                if( isset($postData['reqDat']) ) 
                                {
                                    $aid = 'Act26';
                                    $lines = 50;
                                    $betline = $postData['reqDat']['bet'];
                                    $allbet = $betline * $lines;
                                    $postData['slotEvent'] = 'bet';
                                    if( $postData['reqDat']['freegamesMode'] == 'true' ) 
                                    {
                                        $postData['slotEvent'] = 'freespin';
                                    }
                                    if( $postData['slotEvent'] == 'bet' && $allbet < 1 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Bet "}';
                                        exit( $response );
                                    }
                                    if( $postData['slotEvent'] == 'bet' && $slotSettings->GetBalance() < $allbet ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Balance"}';
                                        exit( $response );
                                    }
                                    if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Invalid Bonus State"}';
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
                                        if( is_array($jackState) ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                        }
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
                                        $bonusMpl = 1;
                                    }
                                    $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                    $winType = $winTypeTmp[0];
                                    $spinWinLimit = $winTypeTmp[1];
                                    $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                    $linesId = [];
                                    $linesId[0] = [
                                        3, 
                                        3, 
                                        3, 
                                        3, 
                                        3
                                    ];
                                    $linesId[1] = [
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        2
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
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5
                                    ];
                                    $linesId[5] = [
                                        1, 
                                        1, 
                                        2, 
                                        1, 
                                        1
                                    ];
                                    $linesId[6] = [
                                        2, 
                                        2, 
                                        3, 
                                        2, 
                                        2
                                    ];
                                    $linesId[7] = [
                                        3, 
                                        3, 
                                        4, 
                                        3, 
                                        3
                                    ];
                                    $linesId[8] = [
                                        4, 
                                        4, 
                                        5, 
                                        4, 
                                        4
                                    ];
                                    $linesId[9] = [
                                        5, 
                                        5, 
                                        4, 
                                        5, 
                                        5
                                    ];
                                    $linesId[10] = [
                                        4, 
                                        4, 
                                        3, 
                                        4, 
                                        4
                                    ];
                                    $linesId[11] = [
                                        3, 
                                        3, 
                                        2, 
                                        3, 
                                        3
                                    ];
                                    $linesId[12] = [
                                        2, 
                                        2, 
                                        1, 
                                        2, 
                                        2
                                    ];
                                    $linesId[13] = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1
                                    ];
                                    $linesId[14] = [
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        2
                                    ];
                                    $linesId[15] = [
                                        3, 
                                        3, 
                                        3, 
                                        3, 
                                        3
                                    ];
                                    $linesId[16] = [
                                        4, 
                                        4, 
                                        4, 
                                        4, 
                                        4
                                    ];
                                    $linesId[17] = [
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5
                                    ];
                                    $linesId[18] = [
                                        6, 
                                        6, 
                                        6, 
                                        6, 
                                        6
                                    ];
                                    $linesId[19] = [
                                        7, 
                                        7, 
                                        7, 
                                        7, 
                                        7
                                    ];
                                    $linesId[20] = [
                                        8, 
                                        8, 
                                        8, 
                                        8, 
                                        8
                                    ];
                                    $linesId[21] = [
                                        9, 
                                        9, 
                                        9, 
                                        9, 
                                        9
                                    ];
                                    $linesId[22] = [
                                        10, 
                                        10, 
                                        10, 
                                        10, 
                                        10
                                    ];
                                    $linesId[23] = [
                                        1, 
                                        1, 
                                        2, 
                                        1, 
                                        1
                                    ];
                                    $linesId[24] = [
                                        2, 
                                        2, 
                                        3, 
                                        2, 
                                        2
                                    ];
                                    $linesId[25] = [
                                        3, 
                                        3, 
                                        4, 
                                        3, 
                                        3
                                    ];
                                    $linesId[26] = [
                                        4, 
                                        4, 
                                        5, 
                                        4, 
                                        4
                                    ];
                                    $linesId[27] = [
                                        5, 
                                        5, 
                                        6, 
                                        5, 
                                        5
                                    ];
                                    $linesId[28] = [
                                        6, 
                                        6, 
                                        7, 
                                        6, 
                                        6
                                    ];
                                    $linesId[29] = [
                                        7, 
                                        7, 
                                        8, 
                                        7, 
                                        7
                                    ];
                                    $linesId[30] = [
                                        8, 
                                        8, 
                                        9, 
                                        8, 
                                        8
                                    ];
                                    $linesId[31] = [
                                        9, 
                                        9, 
                                        10, 
                                        9, 
                                        9
                                    ];
                                    $linesId[32] = [
                                        2, 
                                        2, 
                                        1, 
                                        2, 
                                        2
                                    ];
                                    $linesId[33] = [
                                        3, 
                                        3, 
                                        2, 
                                        3, 
                                        3
                                    ];
                                    $linesId[34] = [
                                        4, 
                                        4, 
                                        3, 
                                        4, 
                                        4
                                    ];
                                    $linesId[35] = [
                                        5, 
                                        5, 
                                        4, 
                                        5, 
                                        5
                                    ];
                                    $linesId[36] = [
                                        6, 
                                        6, 
                                        5, 
                                        6, 
                                        6
                                    ];
                                    $linesId[37] = [
                                        7, 
                                        7, 
                                        6, 
                                        7, 
                                        7
                                    ];
                                    $linesId[38] = [
                                        8, 
                                        8, 
                                        7, 
                                        8, 
                                        8
                                    ];
                                    $linesId[39] = [
                                        9, 
                                        9, 
                                        8, 
                                        9, 
                                        9
                                    ];
                                    $linesId[40] = [
                                        10, 
                                        10, 
                                        9, 
                                        10, 
                                        10
                                    ];
                                    $linesId[41] = [
                                        1, 
                                        2, 
                                        1, 
                                        2, 
                                        1
                                    ];
                                    $linesId[42] = [
                                        2, 
                                        3, 
                                        2, 
                                        3, 
                                        2
                                    ];
                                    $linesId[43] = [
                                        3, 
                                        4, 
                                        3, 
                                        4, 
                                        3
                                    ];
                                    $linesId[44] = [
                                        4, 
                                        5, 
                                        4, 
                                        5, 
                                        4
                                    ];
                                    $linesId[45] = [
                                        5, 
                                        6, 
                                        5, 
                                        6, 
                                        5
                                    ];
                                    $linesId[46] = [
                                        6, 
                                        7, 
                                        6, 
                                        7, 
                                        6
                                    ];
                                    $linesId[47] = [
                                        7, 
                                        8, 
                                        7, 
                                        8, 
                                        7
                                    ];
                                    $linesId[48] = [
                                        8, 
                                        9, 
                                        8, 
                                        9, 
                                        8
                                    ];
                                    $linesId[49] = [
                                        9, 
                                        10, 
                                        9, 
                                        10, 
                                        9
                                    ];
                                    for( $i = 0; $i <= 2000; $i++ ) 
                                    {
                                        $totalWin = 0;
                                        $lineWins = [];
                                        $cWins = [];
                                        $cWinsCount = [];
                                        $cWinsMpl = [];
                                        $wild = ['0'];
                                        $scatter = '11';
                                        $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                        for( $k = 0; $k < $lines; $k++ ) 
                                        {
                                            $cWins[$k] = 0;
                                            $cWinsCount[$k] = 1;
                                            $cWinsMpl[$k] = 1;
                                        }
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
                                                    $offset = 0;
                                                    if( $k > 12 ) 
                                                    {
                                                        $offset = 5;
                                                    }
                                                    $s[0] = $reels['reel1'][$linesId[$k][0] - 1 + $offset];
                                                    $s[1] = $reels['reel2'][$linesId[$k][1] - 1 + $offset];
                                                    $s[2] = $reels['reel3'][$linesId[$k][2] - 1 + $offset];
                                                    $s[3] = $reels['reel4'][$linesId[$k][3] - 1 + $offset];
                                                    $s[4] = $reels['reel5'][$linesId[$k][4] - 1 + $offset];
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
                                                            $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                            $cWinsCount[$k] = 24;
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
                                                            $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                            $cWinsCount[$k] = 28;
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
                                                            $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                            $cWinsCount[$k] = 30;
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
                                                            $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . ',4,' . ($linesId[$k][4] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                            $cWinsCount[$k] = 31;
                                                        }
                                                    }
                                                }
                                            }
                                            if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                            {
                                                $cWinsMpl[$k] = $bonusMpl;
                                                array_push($lineWins, $tmpStringWin);
                                                $totalWin += $cWins[$k];
                                                $cWins[$k] = $cWins[$k] / $bonusMpl;
                                            }
                                        }
                                        $scattersWin = 0;
                                        $scattersStr = '';
                                        $scattersCount = 0;
                                        $scattersCount2 = 0;
                                        $scPos = [];
                                        $scRPos = [
                                            0, 
                                            1, 
                                            2, 
                                            4, 
                                            8, 
                                            16
                                        ];
                                        $reels_ts = [];
                                        $reels_ts['reel1'] = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel2'] = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel3'] = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel4'] = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $reels_ts['reel5'] = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        for( $rl = 1; $rl <= 5; $rl++ ) 
                                        {
                                            for( $rs = 0; $rs <= 14; $rs++ ) 
                                            {
                                                if( $reels['reel' . $rl][$rs] == $scatter ) 
                                                {
                                                    $reels_ts['reel' . $rl][$rs] = -1;
                                                    $scattersCount++;
                                                    break;
                                                }
                                            }
                                        }
                                        $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                        $totalWin += $scattersWin;
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
                                            else if( ($scattersCount >= 3 || $scattersCount2 >= 4) && $winType != 'bonus' ) 
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
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                    }
                                    $fs = 0;
                                    $swu = 0;
                                    $swm = 0;
                                    if( $scattersCount == 2 && $scattersWin > 0 ) 
                                    {
                                        $swm = $slotSettings->GetSymPositions($reels_ts);
                                    }
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $swm = $slotSettings->GetSymPositions($reels_ts);
                                        if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                        }
                                        else
                                        {
                                            $balanceInCents0 = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $balanceInCents0);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                        }
                                        $fs = $slotSettings->slotFreeCount[$scattersCount];
                                        $swu = 1;
                                    }
                                    $winString = implode(',', $lineWins);
                                    $jsSpin = '' . json_encode($reels) . '';
                                    $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"FreeBalance":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance') . ',"scattersWin":' . $scattersWin . ',"swm":' . $swm . ',"fsnew":' . $fs . ',"fscount":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"slotLines":' . $lines . '' . $lines . ',"spinWinsMask":[' . implode(',', $cWinsCount) . '],"spinWinsPrize":[' . implode(',', $cWins) . '],"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"Jackpots":[],"reelsSymbols":' . $jsSpin . '}}';
                                    $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                    $winstring = '';
                                    $postData['payload'] = $response;
                                }
                                else
                                {
                                    $lastEvent = $slotSettings->GetHistory();
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    if( $lastEvent != 'NULL' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->FreeBalance);
                                        $reels = $lastEvent->serverResponse->reelsSymbols;
                                        $lines = $lastEvent->serverResponse->slotLines;
                                        $bet = $lastEvent->serverResponse->slotBet * 100;
                                        $postData['payload'] = $lastEvent;
                                    }
                                    $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                }
                                $result_tmp[0] = '{"action":"' . $aid . '","gameid":576,"serverResponse":' . json_encode($postData) . ',"nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . ',"Denom":' . ($slotSettings->CurrentDenom * 100) . '}';
                                break;
                        }
                        $response = $result_tmp[0];
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
