<?php 
namespace VanguardLTE\Games\SevenSevenSevenCQ9
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                        $result_tmp = [];
                        $aid = '';
                        if( isset($_GET['command']) && $_GET['command'] == 'token' ) 
                        {
                            echo '{"data":{"ip":"0.0.0.0","code":"1234"}}';
                            exit();
                        }
                        if( isset($postData['gameData'][1]) && ($postData['gameData'][1] == 'WEB' || $postData['gameData'][1] == 'MOBILE') ) 
                        {
                            $eResp = '{"err":200,"res":1,"vals":[1,{"E":"guest4ab46d87-bfc0-4c8b-928b-fd5","V":12}],"msg":null}';
                            echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                            exit();
                        }
                        else if( isset($postData['gameData'][1]) ) 
                        {
                            $jsnArr = json_decode(trim($postData['gameData'][1]), true);
                            if( $jsnArr['Type'] == 1 && $jsnArr['ID'] == 11 ) 
                            {
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":111,"Version":0,"ErrorCode":0,"DenomDefine":[1,1,1,1,1,1,1,1,1,1,1,1,1,1],"BetButton":[1,2,3,5,10,20,30,50,100,200],"DefaultDenomIdx":1,"MaxBet":200,"MaxLine":1,"WinLimitLock":300000000000,"DollarSignId":1,"EmulatorType":3,"GameExtraDataCount":0,"ExtraData":null,"ExtendFeatureByGame":null,"ExtendFeatureByGame2":null,"IsReelPayType":false,"Cobrand":null,"PlayerOrderURL":null,"PromotionData":null,"IsShowFreehand":false,"IsAllowFreehand":false,"FeedbackURL":null}],"msg":null}';
                                echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                return null;
                            }
                            if( $jsnArr['Type'] == 1 && $jsnArr['ID'] == 12 ) 
                            {
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":112,"Version":0,"ErrorCode":0,"BGStripStartID":0,"FGStripStartID":-1,"BGStripCount":1,"BGContext":[["2TQ4ew5zEtsbY8kQNIezNwFKK9EADXmKnnRx53GnW98UrsnC/Ke5LecY34eYE4fFKogC4j/+XQVA35Jo0wjLafJIXB0oGaf0Kzv4SYzFhVnAP016bxaCgb5GBH5PLFNNa23yMXaoTZqtxfLCfDAzvaT9wwz9smlS5CQD6G7/n+6fDl2PMliwMUaXBBn42il05NWYNSBA8oUECgn4rTGGNSyOagOgdVURvpXhwpuDv5iZyDaVzZPm7ZlL0jXSwA3EUbfNoNpBKJnEreO804JqTc0ISIG+W74cZzIx1Jds0/P3DssY+1sh1NcVl3LlyZctvc0KpNi2QIrS+gWCTFAxlQIBjtZiOnnxPo1HcFMbmdM9NfzzXIamYw62Apnttha6P7qBrADtXCQSWLIL5nK31M+mlawmIMLObJqqZBjgfTH7VNNR6Exzp4MkhQM=","bziQ4EtjOF9cm2AlJnJDPBMBTAx1xLiGymCYALlNFF5Gg+MorRtFHubZmtx6iCnd5YxOGzialYphHeZ3ABhtryPATI1Nb/k4y8ewu80R20I2mnVrAo57wkiNXUUF80hmVLErm8jMvRDTOBqnR5O0sM0TpFLPGGIk96MFm8TI+VDxnFZZIEglZuywWRXsUExxHFDSId/0YXti64c/HRm5ggt8dRCW3bMFGknbnXjwUBtKN2Qn6hQYt1cdP/Bx6DjxOOA3+rSiy7Fc64ISdg3uR3WklT/yjtWeDY/D+nCNE5AoPFv51trzkjD0QqI3XGvG7RHJJz6B1ONhXeCkZVNjul4vnYZEa+Qgwt+1USQyGDVjxbAHyD49eySIoEnMJTJ0SQC10QfuOWPgbalaNwh9634a9AyXhFYUVw8VkA==","GAuQfLjqb20q9GLYtSaLd856PevljFIr668SNWqS1eJNj3i2J8g4iR3BUGzPP+KmLmmFe7i/FlcyKAXq71Np6Reoy89rdF6Ck66d4cfqSeoOA9V82AG7NZ3+IDxdmILx0qn0w1TPr79xAWRoltr3hzkEv4LYiUQMSycrhLJthI5KHHNNvjNoeZMGDC+aRC+tl2fJFVVTm/3vCEjEUI3ciHBwbfl74X7CjJ4IMprV0zS9HR2wzKJYLIrTGFd6I6IxqAer8BlbzfKiUA1seOnTXKxv6j0ItvrBWg7Gp4VWb0EPwYChEAExEevWCPswYPhi2JAKmfn90Hfz9ZwHiahEr3ZPsu5xBbVhIdaSJA=="]],"FGStripCount":0,"FGContext":[]}],"msg":null}';
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $eResp0 = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                echo '~m~' . (strlen($eResp0) + 3) . '~m~~j~' . $eResp0 . '------';
                                echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                return null;
                            }
                            if( $jsnArr['Type'] == 3 && $jsnArr['ID'] == 31 ) 
                            {
                                $aid = 'bet';
                            }
                            else if( $jsnArr['Type'] == 3 && $jsnArr['ID'] == 32 ) 
                            {
                                $aid = 'spin_accept';
                            }
                        }
                        if( $aid == 'bet' ) 
                        {
                            $lines = $jsnArr['PlayLine'];
                            $betline = $jsnArr['PlayBet'] / 100 * 10;
                            if( $lines <= 0 || $betline <= 0.0001 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $aid . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($lines * $betline) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $aid . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                        }
                        switch( $aid ) 
                        {
                            case 'bet':
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
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $winPosArr = [];
                                for( $lnc = 0; $lnc < $lines; $lnc++ ) 
                                {
                                    $crl = $linesId[$lnc];
                                    $winPosArr[$lnc] = [
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ], 
                                        [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ]
                                    ];
                                    for( $lnc_ = 0; $lnc_ < 5; $lnc_++ ) 
                                    {
                                        $wpa = $crl[$lnc_];
                                        $winPosArr[$lnc][$wpa - 1][$lnc_] = 1;
                                    }
                                }
                                $allbet = $betline * $lines;
                                $postData['slotEvent'] = 'bet';
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
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9BonusStart', 0);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9BonusBet', $allbet);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9BonusWin', 0);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9FreeGames', 0);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9CurrentFreeGame', 0);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9TotalWin', 0);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9Multiplier', 1);
                                    $bonusMpl = 1;
                                }
                                else
                                {
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9CurrentFreeGame', $slotSettings->GetGameData('SevenSevenSevenCQ9CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->GetGameData('SevenSevenSevenCQ9Multiplier');
                                }
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
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
                                        0
                                    ];
                                    $wild = ['W'];
                                    $scatter = '';
                                    $anyBar = [
                                        '11', 
                                        '12', 
                                        '13'
                                    ];
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
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
                                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                if( $s[0] == $csym ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":1,"SymbolCount":1,"WinLineNo":998,"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[1,0,0],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( $s[1] == $csym ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":1,"SymbolCount":1,"WinLineNo":998,"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[0,1,0],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( $s[2] == $csym ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":1,"SymbolCount":1,"WinLineNo":998,"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[0,0,1],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":2,"SymbolCount":2,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[1,1,0],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
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
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":3,"SymbolCount":3,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[1,1,1],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( in_array($s[0], $anyBar) && in_array($s[1], $anyBar) && in_array($s[2], $anyBar) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_ANY1'][3] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"14","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":3,"SymbolCount":3,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[0,0,0],[0,0,0],[1,1,1],[0,0,0],[0,0,0]],"LineExtraData":[0],"LineType":0}';
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
                                    $scattersWin2 = 0;
                                    $scattersStr = '';
                                    $scattersStr2 = '';
                                    $scattersCount = 0;
                                    $scattersCount2 = 0;
                                    $scPos = [];
                                    $scPos2 = [];
                                    for( $r = 1; $r <= 3; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scPos[] = '' . ($r - 1) . ',' . $p . '';
                                            }
                                        }
                                    }
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
                                        else if( $scattersCount2 >= 3 && $winType == 'bonus' && $spinWinLimit < ($totalWin + (2 * $allbet)) ) 
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
                                $WinType = 0;
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $WinType = 1;
                                }
                                $reportWin = $totalWin;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9BonusWin', $slotSettings->GetGameData('SevenSevenSevenCQ9BonusWin') + $totalWin);
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9TotalWin', $slotSettings->GetGameData('SevenSevenSevenCQ9TotalWin') + $totalWin);
                                    $balanceInCents = $slotSettings->GetGameData('SevenSevenSevenCQ9FreeBalance');
                                }
                                else
                                {
                                    $slotSettings->SetGameData('SevenSevenSevenCQ9TotalWin', $totalWin);
                                }
                                $fs = 0;
                                if( $scattersCount >= 3 || $scattersCount2 >= 3 ) 
                                {
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData('SevenSevenSevenCQ9FreeGames', $slotSettings->GetGameData('SevenSevenSevenCQ9FreeGames') + 1);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('SevenSevenSevenCQ9FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData('SevenSevenSevenCQ9BonusWin', $totalWin);
                                    }
                                    $fs = $slotSettings->GetGameData('SevenSevenSevenCQ9FreeGames');
                                }
                                $curReels = '';
                                $curReels .= '"13,16,16",';
                                $curReels .= ('"' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . '' . '",');
                                $curReels .= ('"' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . '' . '",');
                                $curReels .= ('"' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . '' . '"');
                                $curReels .= ',"13,16,16"';
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . '' . $slotSettings->GetGameData($slotSettings->slotId . 'FirstSpin') . ',"Multiplier":' . $slotSettings->GetGameData('SevenSevenSevenCQ9Multiplier') . ',"FreeWild":' . $slotSettings->GetGameData('SevenSevenSevenCQ9FreeWild') . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('SevenSevenSevenCQ9FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('SevenSevenSevenCQ9CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('SevenSevenSevenCQ9BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":131,"Version":0,"ErrorCode":0,"GamePlaySerialNumber":"","RngData":[' . implode(',', $reels['rp']) . '],"SymbolResult":[' . $curReels . '],"EmulatorType":3,"WinType":' . $WinType . ',"BaseWin":' . $slotSettings->FormatFloat($totalWin) . ',"TotalWin":' . $slotSettings->FormatFloat($totalWin) . ',"IsTriggerFG":false,"NextModule":0,"WinLineCount":' . count($lineWins) . ',"ExtraDataCount":1,"ExtraData":[0],"ReellPosChg":[0],"BonusType":0,"SpecialAward":0,"SpecialSymbol":0,"ReelLenChange":[],"ReelPay":[],"IsRespin":false,"RespinReels":[0,0,0],"Multiple":"0","NextSTable":0,"FreeSpin":[0],"IsHitJackPot":false,"udsOutputWinLine":[' . $winString . ']}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $eResp = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                break;
                            case 'spin_accept':
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":132,"Version":0,"ErrorCode":0,"IsAllowFreeHand":false}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $slotSettings->SetGameData('SevenSevenSevenCQ9TotalWin', 0);
                                $eResp = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                break;
                        }
                        $response = implode('------', $result_tmp);
                        $slotSettings->SaveGameData();
                        $slotSettings->SaveGameDataStatic();
                        echo '' . $response;
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
