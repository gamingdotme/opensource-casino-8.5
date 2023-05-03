<?php 
namespace VanguardLTE\Games\FruitKingCQ9
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
                        $aid = '';
                        if( isset($_GET['command']) && $_GET['command'] == 'token' ) 
                        {
                            echo '{"data":{"ip":"0.0.0.0","code":"1234"}}';
                            exit();
                        }
                        if( isset($postData['gameData'][1]) && ($postData['gameData'][1] == 'WEB' || $postData['gameData'][1] == 'MOBILE') ) 
                        {
                            $eResp = '{"err":200,"res":1,"vals":[1,{"E":"guest35cca6b9-c263-4b6f-82fb-214","V":3}],"msg":null}';
                            echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                            exit();
                        }
                        else if( isset($postData['gameData'][1]) ) 
                        {
                            $jsnArr = json_decode(trim($postData['gameData'][1]), true);
                            if( $jsnArr['Type'] == 1 && $jsnArr['ID'] == 11 ) 
                            {
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":111,"Version":0,"ErrorCode":0,"DenomDefine":[1,1,1,1,1,1,1,1,1,1,1,1,1,1],"BetButton":[1,2,3,5,10,20,30,50,100,200],"DefaultDenomIdx":1,"MaxBet":200,"MaxLine":9,"WinLimitLock":30000000000,"DollarSignId":1,"EmulatorType":3,"GameExtraDataCount":0,"ExtraData":null,"ExtendFeatureByGame":null,"ExtendFeatureByGame2":null,"IsReelPayType":false,"Cobrand":null,"PlayerOrderURL":null,"PromotionData":null,"IsShowFreehand":false,"IsAllowFreehand":false,"FeedbackURL":null}],"msg":null}';
                                echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                return null;
                            }
                            if( $jsnArr['Type'] == 1 && $jsnArr['ID'] == 12 ) 
                            {
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":112,"Version":0,"ErrorCode":0,"BGStripStartID":0,"FGStripStartID":-1,"BGStripCount":1,"BGContext":[["WMdRnliy77Tob2so3SXkYwKPbjSHKscp7Jstqy+s1np/SNy/E1JzB8sBU4JgAfsDKub6laYU9yJkHvYc/MKFkZ2pPXTUhAKn3//qefpNKcHRdVbsbilMw7t4ml6TeXHbBUARJicmCu6GdBmxwtm0HOuEkXs5Bm82L9GT0rB3X8ySs8wRE5NkS2VWiw0=","flVn66r8Fzm5ZcS0exahXZnE8IXB7o9ZQWTKKt48awFapjaRVn8wd/gBN2FUzEXTn9cgtOAxzZz5vmO3ZVx0C0K3GPmpekLvUi7yF6QBcwfjR5p2rr1Y8WiwBKmu5Kn/gnHJ4zBaGm4F6I7N5mX2lo4WeQEwM1tvo6+zzhBIjytr9qOUZIISqpcMWotCIRVrDVNWBXxvISW/YytG","PqUNX0MYi65pAj4nmZUwkr8ejsuyib8EXZp3CFDa5ZUt4+KjNopIKhs1Q5yDVzcdqdwxsi2FVEhLmQgeoNdJq2DJJU7g7jCvJAj5WaGcad7oHCkY0CgqEyU/nJYuTc132cliwrwqhdPkZOh3s3eeymXW1+4G+0/b7twBzQ==","w4VpDSMnsN6OhmxoduvNtW+WaiUp3jC7SuGNFUrjBYArOTtvswnaR4yToZjg2S04NBgdc5MpW7hpInI43R3a+p1gGouwi3P6QtY/dN+wuoCIJFzAJzI963LIGLgdNySfri2/g6R3Gq9yt4j5SY62oEdwLKOEA9g4qnA0lj+sF1q2l7nG21+DIEoGra/5b4iH51ZOQKU6+pB+0JLJ","KMTXFrKC4vOleRxwKM+8kpKmZ9glXnQO49BfmvXeCsiI/25FPKTQk45EMIRyTsl/Oj8nzy4GM3GXTKPMP3gr05VLDbNla5P40VzAj9h3L2n8+Yeaw5s+FxqgySgm/6iaX8slZN7d8xJrOsgdYJQUbUzsj4ojKK57FoLmN9HbDIAoXAIb9dR4c7lX4a4="]],"FGStripCount":0,"FGContext":[]}],"msg":null}';
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
                            $betline = $jsnArr['PlayBet'] / 100;
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
                                    $slotSettings->SetGameData('FruitKingCQ9BonusStart', 0);
                                    $slotSettings->SetGameData('FruitKingCQ9BonusBet', $allbet);
                                    $slotSettings->SetGameData('FruitKingCQ9BonusWin', 0);
                                    $slotSettings->SetGameData('FruitKingCQ9FreeGames', 0);
                                    $slotSettings->SetGameData('FruitKingCQ9CurrentFreeGame', 0);
                                    $slotSettings->SetGameData('FruitKingCQ9TotalWin', 0);
                                    $slotSettings->SetGameData('FruitKingCQ9FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                    $slotSettings->SetGameData('FruitKingCQ9Multiplier', 1);
                                    $bonusMpl = 1;
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FruitKingCQ9CurrentFreeGame', $slotSettings->GetGameData('FruitKingCQ9CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->GetGameData('FruitKingCQ9Multiplier');
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
                                    $scatter = 'SC';
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
                                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
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
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":2,"SymbolCount":2,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
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
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":3,"SymbolCount":3,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
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
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":4,"SymbolCount":4,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
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
                                                        $tmpStringWin = '{"SymbolId":"' . $csym . '","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":5,"SymbolCount":5,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
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
                                    for( $r = 1; $r <= 5; $r++ ) 
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
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    $sgwin = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $sgwin = $slotSettings->slotFreeCount;
                                        $scattersStr = '{"scatterName":' . $scatter . ',"cells":[' . implode(',', $scPos) . '],"winAmount":' . ($scattersWin * 100) . ',"freespins":' . $sgwin . '}';
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
                                    $slotSettings->SetGameData('FruitKingCQ9BonusWin', $slotSettings->GetGameData('FruitKingCQ9BonusWin') + $totalWin);
                                    $slotSettings->SetGameData('FruitKingCQ9TotalWin', $slotSettings->GetGameData('FruitKingCQ9TotalWin') + $totalWin);
                                    $balanceInCents = $slotSettings->GetGameData('FruitKingCQ9FreeBalance');
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FruitKingCQ9TotalWin', $totalWin);
                                }
                                $fs = 0;
                                if( $scattersCount >= 3 || $scattersCount2 >= 3 ) 
                                {
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData('FruitKingCQ9FreeGames', $slotSettings->GetGameData('FruitKingCQ9FreeGames') + 1);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('FruitKingCQ9FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData('FruitKingCQ9BonusWin', $totalWin);
                                    }
                                    $fs = $slotSettings->GetGameData('FruitKingCQ9FreeGames');
                                }
                                $curReels = '';
                                $curReels .= ('"' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '",');
                                $curReels .= ('"' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '",');
                                $curReels .= ('"' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . '"');
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . '' . $slotSettings->GetGameData($slotSettings->slotId . 'FirstSpin') . ',"Multiplier":' . $slotSettings->GetGameData('FruitKingCQ9Multiplier') . ',"FreeWild":' . $slotSettings->GetGameData('FruitKingCQ9FreeWild') . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('FruitKingCQ9FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('FruitKingCQ9CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('FruitKingCQ9BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":131,"Version":0,"ErrorCode":0,"GamePlaySerialNumber":"","RngData":[' . implode(',', $reels['rp']) . '],"SymbolResult":[' . $curReels . '],"EmulatorType":0,"WinType":' . $WinType . ',"BaseWin":' . $slotSettings->FormatFloat($totalWin) . ',"TotalWin":' . $slotSettings->FormatFloat($totalWin) . ',"IsTriggerFG":false,"NextModule":0,"WinLineCount":0,"ExtraDataCount":1,"ExtraData":[0],"ReellPosChg":[0],"BonusType":0,"SpecialAward":0,"SpecialSymbol":0,"ReelLenChange":[],"ReelPay":[],"IsRespin":false,"RespinReels":[0,0,0,0,0],"Multiple":"0","NextSTable":0,"FreeSpin":[0],"IsHitJackPot":false,"udsOutputWinLine":[' . $winString . ']}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $eResp = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                break;
                            case 'spin_accept':
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":132,"Version":0,"ErrorCode":0,"IsAllowFreeHand":false}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $slotSettings->SetGameData('FruitKingCQ9TotalWin', 0);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
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
