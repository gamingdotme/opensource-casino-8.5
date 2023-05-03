<?php 
namespace VanguardLTE\Games\EightEightEightCQ9
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
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":111,"Version":0,"ErrorCode":0,"DenomDefine":[1,1,1,1,1,1,1,1,1,1,1,1,1,1],"BetButton":[1,2,3,5,10,20,30,50,100,200],"DefaultDenomIdx":1,"MaxBet":200,"MaxLine":10,"WinLimitLock":300000000000,"DollarSignId":0,"EmulatorType":0,"GameExtraDataCount":0,"ExtraData":null,"ExtendFeatureByGame":null,"ExtendFeatureByGame2":null,"IsReelPayType":false,"Cobrand":null,"PlayerOrderURL":null,"PromotionData":null,"IsShowFreehand":false,"IsAllowFreehand":false,"FeedbackURL":null}],"msg":null}';
                                echo '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                return null;
                            }
                            if( $jsnArr['Type'] == 1 && $jsnArr['ID'] == 12 ) 
                            {
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":1,"ID":112,"Version":0,"ErrorCode":0,"BGStripStartID":0,"FGStripStartID":-1,"BGStripCount":1,"BGContext":[["0r4M8WLUWdtbkOX6h8vXxBlijvFrhgqZHoJC2xOlD/HcaObqiJZO5KRQMZpIsMApOq6QJ04A7WzEfTNw2SsdL8IwPwd1wgpoKfIjPO+r1dp8JBXw8Hw4IqZ5R6FQKm0mfrExB42GXz8ZWiwtFtb4GIINSqerfuoxzwgaLxU//hEO7+HjA+Rv+eyePMU=","v4XXVO8NRuYGQiXE33Ylj8OZQz1HTMY+uv3yjUx8S81ahWkafeMZ5iQJ8YUVq0ch5IpCT7Un2mg3G/zWqMTYH0Y5OIL3zVjWRC+IzWoxtWOzOM4+nKFni2MosMilgpkkCAu/ux0Wi0+uHtFlO3uopLWWrXkZ+0l8adptcA==","kWF8JrEBN8NynjvW2NFfyWgCtRT2JSn+iRoSwhZW+mR9xR8QlX8F/ONUVgNkLPaVfkJUDFaYCca1ztnh5FO6+8Dz8ePHeBgHGCrLI5h+3nVGzZAKc1vc84cA8E3vs3ekPjXkhEjuGxK/t7TuPgz5kxuFaMgX9EC000T5vIjjlBPlMs60QTV74/WjDQ0=","2GE8VKAxLtNirAFXh/NsKxfao5P965RdJwcOb97d59SBEZ7bLjZZddXTwN9Yym417ZRmFvPr7P2pJXywzYMny1ewSiINfQXy8HV7/h/7jR2Bi9gQnG4ppRb3knTmpacHR37lp9fOYSBUJ8q9zAQ0o/KYJ9akBDhQDNQRxqnW2B1paa9VCv+7Lz4oGNw=","JrDUuYaHPE9rpJzJ3DsZVdphBm7R42F7z6j9T8qoKTAH+sZ+axx3+TxG+HPN2JrCaznVIuYPnvX/AVX9uVe9VFynTl/gJav04LKgy+/DA6gWuCthqK3oR796qdruScS+1LWDWJ4Qf4TVZdOunKF7sJ4V7epHPmp9dwoFug=="]],"FGStripCount":0,"FGContext":[]}],"msg":null}';
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
                            else if( $jsnArr['Type'] == 3 && $jsnArr['ID'] == 44 ) 
                            {
                                $aid = 'bonus_accept';
                            }
                            else if( $jsnArr['Type'] == 3 && $jsnArr['ID'] == 45 ) 
                            {
                                $aid = 'bonus_step';
                            }
                            else if( $jsnArr['Type'] == 3 && $jsnArr['ID'] == 46 ) 
                            {
                                $aid = 'bonus_end';
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
                                $linesId[10] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[11] = [
                                    2, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[12] = [
                                    2, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[15] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[16] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[18] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[19] = [
                                    1, 
                                    1, 
                                    1, 
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
                                    $slotSettings->SetGameData('EightEightEightCQ9BonusStart', 0);
                                    $slotSettings->SetGameData('EightEightEightCQ9BonusBet', $allbet);
                                    $slotSettings->SetGameData('EightEightEightCQ9BonusWin', 0);
                                    $slotSettings->SetGameData('EightEightEightCQ9FreeGames', 0);
                                    $slotSettings->SetGameData('EightEightEightCQ9CurrentFreeGame', 0);
                                    $slotSettings->SetGameData('EightEightEightCQ9TotalWin', 0);
                                    $slotSettings->SetGameData('EightEightEightCQ9FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                    $slotSettings->SetGameData('EightEightEightCQ9Multiplier', 1);
                                    $bonusMpl = 1;
                                }
                                else
                                {
                                    $slotSettings->SetGameData('EightEightEightCQ9CurrentFreeGame', $slotSettings->GetGameData('EightEightEightCQ9CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->GetGameData('EightEightEightCQ9Multiplier');
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
                                    $wild = [''];
                                    $scatter = 'B';
                                    $anyBar = [
                                        '13', 
                                        '12', 
                                        '11'
                                    ];
                                    $any12 = [
                                        '1', 
                                        '2'
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
                                                if( in_array($s[0], $anyBar) && in_array($s[1], $anyBar) && in_array($s[2], $anyBar) && in_array($s[3], $anyBar) && in_array($s[4], $anyBar) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_ANY1'][5] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"14","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":5,"SymbolCount":5,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( in_array($s[0], $any12) && in_array($s[1], $any12) && in_array($s[2], $any12) && in_array($s[3], $any12) && in_array($s[4], $any12) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_ANY2'][5] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"5","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":5,"SymbolCount":5,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( in_array($s[0], $any12) && in_array($s[1], $any12) && in_array($s[2], $any12) && in_array($s[3], $any12) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_ANY2'][4] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"5","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":4,"SymbolCount":4,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
                                                    }
                                                }
                                                if( in_array($s[0], $any12) && in_array($s[1], $any12) && in_array($s[2], $any12) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_ANY2'][3] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"SymbolId":"5","LinePrize":' . ($cWins[$k] * 100) . ',"NumOfKind":3,"SymbolCount":3,"WinLineNo":' . $k . ',"LineMultiplier":1,"WinPosition":[[' . implode(',', $winPosArr[$k][0]) . '],[' . implode(',', $winPosArr[$k][1]) . '],[' . implode(',', $winPosArr[$k][2]) . ']],"LineExtraData":[0],"LineType":0}';
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
                                    $scPos = [
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
                                    $scPos2 = [];
                                    $EmulatorType = 0;
                                    $WinLineCount = 0;
                                    $NextModule = 0;
                                    $Multiple = 0;
                                    $WinType = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scPos[$p][$r - 1] = 1;
                                            }
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $EmulatorType = 3;
                                        $WinLineCount = 1;
                                        $NextModule = 30;
                                        $Multiple = 15;
                                        $WinType = 4;
                                        $scatPayArr = [
                                            5, 
                                            10, 
                                            15, 
                                            20, 
                                            50, 
                                            100, 
                                            200
                                        ];
                                        shuffle($scatPayArr);
                                        $scatPayWin = $scatPayArr[0] * $allbet;
                                        $scattersWin = $scatPayWin;
                                        $scattersStr = '{"SymbolId":"B","LinePrize":' . round($scatPayWin * $allbet) . ',"NumOfKind":3,"SymbolCount":3,"WinLineNo":997,"LineMultiplier":' . $scatPayArr[0] . ',"WinPosition":[[' . implode(',', $scPos[0]) . '],[' . implode(',', $scPos[1]) . '],[' . implode(',', $scPos[2]) . ']],"LineExtraData":[0],"LineType":0}';
                                        array_push($lineWins, $scattersStr);
                                        $slotSettings->SetGameData('EightEightEightCQ9scatPayMpl', $scatPayArr[0]);
                                        $slotSettings->SetGameData('EightEightEightCQ9scatPayWin', $scatPayWin);
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
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $WinType = 1;
                                }
                                $reportWin = $totalWin;
                                $slotSettings->SetGameData('EightEightEightCQ9TotalWin', $totalWin);
                                $curReels = '';
                                $curReels .= ('"' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '",');
                                $curReels .= ('"' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '",');
                                $curReels .= ('"' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . '"');
                                if( $scattersCount >= 3 ) 
                                {
                                    $slotSettings->SetGameData('EightEightEightCQ9BonusTrigger', 1);
                                    $WinType = 4;
                                }
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . '' . $slotSettings->GetGameData($slotSettings->slotId . 'FirstSpin') . ',"Multiplier":' . $slotSettings->GetGameData('EightEightEightCQ9Multiplier') . ',"FreeWild":' . $slotSettings->GetGameData('EightEightEightCQ9FreeWild') . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('EightEightEightCQ9FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('EightEightEightCQ9CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('EightEightEightCQ9BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":131,"Version":0,"ErrorCode":0,"GamePlaySerialNumber":"","RngData":[' . implode(',', $reels['rp']) . '],"SymbolResult":[' . $curReels . '],"EmulatorType":' . $EmulatorType . ',"WinType":' . $WinType . ',"BaseWin":' . $slotSettings->FormatFloat($totalWin) . ',"TotalWin":' . $slotSettings->FormatFloat($totalWin) . ',"IsTriggerFG":false,"NextModule":' . $NextModule . ',"WinLineCount":' . $WinLineCount . ',"ExtraDataCount":1,"ExtraData":[0],"ReellPosChg":[0],"BonusType":0,"SpecialAward":0,"SpecialSymbol":0,"ReelLenChange":[],"ReelPay":[],"IsRespin":false,"RespinReels":[0,0,0,0,0],"Multiple":"' . $Multiple . '","NextSTable":0,"FreeSpin":[0],"IsHitJackPot":false,"udsOutputWinLine":[' . $winString . ']}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $eResp = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                break;
                            case 'spin_accept':
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":132,"Version":0,"ErrorCode":0,"IsAllowFreeHand":false}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                $slotSettings->SetGameData('EightEightEightCQ9TotalWin', 0);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $eResp = '{"vals":[1,' . $balanceInCents . '],"evt":1}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                break;
                            case 'bonus_step':
                                if( $slotSettings->GetGameData('EightEightEightCQ9BonusTrigger') == 1 ) 
                                {
                                    $scatPayMpl = $slotSettings->GetGameData('EightEightEightCQ9scatPayMpl');
                                    $scatPayWin = $slotSettings->GetGameData('EightEightEightCQ9scatPayWin');
                                    $scatPayArr = [
                                        5, 
                                        10, 
                                        15, 
                                        20, 
                                        50, 
                                        100, 
                                        200
                                    ];
                                    shuffle($scatPayArr);
                                    $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":145,"Version":0,"ErrorCode":0,"PlayerBet":50,"AccumlateWinAmt":' . round($scatPayWin * 100) . ',"ScatterPayFromBaseGame":' . round($scatPayWin * 100) . ',"GameComplete":true,"udcDataSet":{"SelExtraData":[],"SelMultiplier":[' . $scatPayMpl . ', ' . $scatPayArr[0] . ', ' . $scatPayArr[1] . '],"SelSpinTimes":[],"SelWin":[],"PlayerSelected":[0]}}],"msg":null}';
                                    $slotSettings->SetGameData('EightEightEightCQ9BonusTrigger', 0);
                                    $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                }
                                break;
                            case 'bonus_accept':
                                if( $slotSettings->GetGameData('EightEightEightCQ9BonusTrigger') == 1 ) 
                                {
                                    $scatPayMpl = $slotSettings->GetGameData('EightEightEightCQ9scatPayMpl');
                                    $scatPayWin = $slotSettings->GetGameData('EightEightEightCQ9scatPayWin');
                                    $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":144,"Version":0,"ErrorCode":0,"PlayerBet":50,"AccumlateWinAmt":0,"ScatterPayFromBaseGame":' . round($scatPayWin * 100) . ',"MaxRound":1,"CurrentRound":1,"udcDataSet":{"SelExtraData":[],"SelMultiplier":[],"SelSpinTimes":[],"SelWin":[],"PlayerSelected":[0]}}],"msg":null}';
                                    $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
                                }
                                break;
                            case 'bonus_end':
                                $scatPayMpl = $slotSettings->GetGameData('EightEightEightCQ9scatPayMpl');
                                $scatPayWin = $slotSettings->GetGameData('EightEightEightCQ9scatPayWin');
                                $eResp = '{"err":200,"res":2,"vals":[1,{"Type":3,"ID":146,"Version":0,"ErrorCode":0,"PlayerBet":50,"TotalWinAmt":' . round($scatPayWin * 100) . ',"ScatterPayFromBaseGame":' . round($scatPayWin * 100) . ',"NextModule":0}],"msg":null}';
                                $result_tmp[] = '~m~' . (strlen($eResp) + 3) . '~m~~j~' . $eResp;
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
