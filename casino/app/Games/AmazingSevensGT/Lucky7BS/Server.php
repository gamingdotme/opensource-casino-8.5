<?php 
namespace VanguardLTE\Games\Lucky7BS
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
                        $aid = '';
                        $aid = (string)$_POST['CMD'];
                        switch( $aid ) 
                        {
                            case 'ENTER':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2];
                                    $curReels .= ('|' . $reels->reel2[0] . ',' . $reels->reel2[1] . ',' . $reels->reel2[2]);
                                    $curReels .= ('|' . $reels->reel3[0] . ',' . $reels->reel3[1] . ',' . $reels->reel3[2]);
                                    $curReels .= ('|' . $reels->reel4[0] . ',' . $reels->reel4[1] . ',' . $reels->reel4[2]);
                                    $curReels .= ('|' . $reels->reel5[0] . ',' . $reels->reel5[1] . ',' . $reels->reel5[2]);
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet;
                                }
                                else
                                {
                                    $tr = (object)[
                                        'reel1' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel2' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel3' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel4' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel5' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ]
                                    ];
                                    $bet = 0;
                                    $lines = 1;
                                    $curReels = '' . $tr->reel1[0] . ',' . $tr->reel1[1] . ',' . $tr->reel1[2];
                                    $curReels .= ('|' . $tr->reel2[0] . ',' . $tr->reel2[1] . ',' . $tr->reel2[2]);
                                    $curReels .= ('|' . $tr->reel3[0] . ',' . $tr->reel3[1] . ',' . $tr->reel3[2]);
                                    $curReels .= ('|' . $tr->reel4[0] . ',' . $tr->reel4[1] . ',' . $tr->reel4[2]);
                                    $curReels .= ('|' . $tr->reel5[0] . ',' . $tr->reel5[1] . ',' . $tr->reel5[2]);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Betline', $bet);
                                $result_tmp[0] = 'RESULT=OK&BALANCE=' . $balanceInCents . '&game_mode=free&server_type=AP&LASTHAND=NO&AUTOPLAY_VALUES=5|10|15|20|25|30|40|50|100&IBETVALUES=' . implode('', $slotSettings->Bet) . '&DEFCOIN=' . $bet . '&IREEL0=1 4 0 5 2 3 1 4 2 0 3 &IREEL1=1 4 0 5 2 3 1 4 2 0 3 &IREEL2=1 4 0 5 2 3 1 4 2 0 3 &IBETSPERLINE=3&ILINES=1&IPAYOUT0=2 3 10 20 50 100 150 250 1000 &IPAYOUT1=4 6 20 40 100 200 300 500 2000 &IPAYOUT2=6 9 30 60 150 300 450 750 5000 &DEFAULTNUMLINES=1&DEFAULTBETPERLINE=1&BONUSBALANCE=0&GID=2&TIME=' . time() . '&END=0';
                                break;
                            case 'DOBONUS':
                            case 'PLACEBET':
                                $linesId = [];
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                if( $aid == 'DOBONUS' ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $be = $slotSettings->GetGameData($slotSettings->slotId . 'Betline');
                                    $betline = $slotSettings->Bet[$be];
                                }
                                else
                                {
                                    $tmpInputData = explode(' ', $_POST['BET']);
                                    $lines = (int)$tmpInputData[1];
                                    $betline = $slotSettings->Bet[$tmpInputData[0]];
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Betline', $tmpInputData[0]);
                                    $postData['slotEvent'] = 'bet';
                                }
                                $allbet = $betline * $lines;
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                if( $postData['slotEvent'] == 'bet' ) 
                                {
                                    if( $lines <= 0 || $betline <= 0.0001 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->GetBalance() < $allbet ) 
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
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $symViewPosition = [
                                    [
                                        0, 
                                        3, 
                                        6, 
                                        9, 
                                        12
                                    ], 
                                    [
                                        1, 
                                        4, 
                                        7, 
                                        10, 
                                        13
                                    ], 
                                    [
                                        2, 
                                        5, 
                                        8, 
                                        11, 
                                        14
                                    ]
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
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
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
                                    $wild = [''];
                                    $bars = [
                                        '5', 
                                        '3', 
                                        '1'
                                    ];
                                    $scatter = '14';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $mpl = 1;
                                    $payoutKind = -1;
                                    for( $k = 0; $k < 1; $k++ ) 
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
                                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                $svp = [];
                                                $svp[0] = $symViewPosition[$linesId[$k][0] - 1][0];
                                                $svp[1] = $symViewPosition[$linesId[$k][1] - 1][1];
                                                $svp[2] = $symViewPosition[$linesId[$k][2] - 1][2];
                                                if( $s[0] == 0 || $s[1] == 0 || $s[2] == 0 ) 
                                                {
                                                    $tmpWin = 2 * $allbet * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = 2;
                                                    }
                                                }
                                                if( in_array($s[0], $bars) && in_array($s[1], $bars) && in_array($s[2], $bars) ) 
                                                {
                                                    $tmpWin = 3 * $allbet * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = 3;
                                                    }
                                                }
                                                if( $s[0] == 0 && $s[1] == 0 || $s[1] == 0 && $s[2] == 0 || $s[0] == 0 && $s[2] == 0 ) 
                                                {
                                                    $tmpWin = 10 * $allbet * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = 10;
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $allbet * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = $slotSettings->Paytable['SYM_' . $csym][3];
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
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scPos = [];
                                    $totalWin += $scattersWin;
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
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
                                    $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                }
                                $fs = 0;
                                $freeDataStr = '&STATE=MAIN&TOTALPAYOUT=' . round($totalWin / $betline) . '&COMPLEXWIN=' . round($totalWin / $betline);
                                $curReels = $reels['reel1'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel1'][2];
                                $curReels .= ('|' . $reels['reel2'][0] . ',' . $reels['reel2'][1] . ',' . $reels['reel2'][2]);
                                $curReels .= ('|' . $reels['reel3'][0] . ',' . $reels['reel3'][1] . ',' . $reels['reel3'][2]);
                                $curReels .= ('|' . $reels['reel4'][0] . ',' . $reels['reel4'][1] . ',' . $reels['reel4'][2]);
                                $curReels .= ('|' . $reels['reel5'][0] . ',' . $reels['reel5'][1] . ',' . $reels['reel5'][2]);
                                $winString = '&PAYOUT=0';
                                if( count($lineWins) > 0 ) 
                                {
                                    $winString = '&PAYOUT=' . ($lineWins[0] * $lines);
                                    switch( $lineWins[0] ) 
                                    {
                                        case '2':
                                            $payoutKind = 0;
                                            break;
                                        case '3':
                                            $payoutKind = 1;
                                            break;
                                        case '10':
                                            $payoutKind = 2;
                                            break;
                                        case '20':
                                            $payoutKind = 3;
                                            break;
                                        case '50':
                                            $payoutKind = 4;
                                            break;
                                        case '100':
                                            $payoutKind = 5;
                                            break;
                                        case '150':
                                            $payoutKind = 6;
                                            break;
                                        case '250':
                                            $payoutKind = 7;
                                            break;
                                        case '1000':
                                            $payoutKind = 8;
                                            break;
                                    }
                                }
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . '' . $slotSettings->GetGameData($slotSettings->slotId . 'FirstSpin') . ',"slotBet":' . $slotSettings->GetGameData($slotSettings->slotId . 'Betline') . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . ',"winLines":["' . $winString . '"],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $result_tmp[0] = 'RESULT=OK&BALANCE=' . $balanceInCents . $winString . '&SLOT1=1&SLOT2=13&SLOT3=5&STOPREEL0=' . $reels['rp'][1] . '&STOPREEL1=' . $reels['rp'][2] . '&STOPREEL2=' . $reels['rp'][3] . '&PAYOUTKIND=' . $payoutKind . '&TIME=' . time() . '&BONUSBALANCE=0&GID=2&END=0';
                                break;
                        }
                        $response = implode('------:::', $result_tmp);
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
