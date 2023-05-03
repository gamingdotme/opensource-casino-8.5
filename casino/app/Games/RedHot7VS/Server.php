<?php 
namespace VanguardLTE\Games\RedHot7VS
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
                        if( !isset($postData['gameData']) ) 
                        {
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            exit( 'Balance:' . $balanceInCents );
                        }
                        $postData0 = explode('&', $postData['gameData']);
                        $postData = [];
                        foreach( $postData0 as $vl ) 
                        {
                            $tmp = explode('=', $vl);
                            $postData[$tmp[0]] = $tmp[1];
                        }
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$postData['action'];
                        switch( $aid ) 
                        {
                            case 'QRY_CHECK':
                                $result_tmp[] = '{"status":"OK","for":"QRY_CHECK","device":"S4617","login":"user","username":"","firstname":"","lastname":"CARD_4116078299570565","usejp":1,"forward":"e1","game":"000540d0","credit":{"tot":' . $balanceInCents . '},"account":{"tot":0}}';
                                break;
                            case 'LOGIN':
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550781670648058","for":"LOGIN","device":"S4617","bookamount":{"tot":' . $balanceInCents . '},"login":"user","username":"","firstname":"","lastname":"CARD_4116078299570565","usejp":1,"forward":"e1","games":[],"credit":{"tot":' . $balanceInCents . '},"account":{"tot":0}}';
                                break;
                            case 'GETJPCFG':
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550853820105222","for":"GETJPCFG","noof":3,"jps":[{"mjpid":"MC_BON/1","cfgflags":4,"dispslot":1,"name":"Maxi Bonus","textname":"Maxi Pot","bgpic":"diabon_bg.png","winsnd":"diabon_win"},{"mjpid":"MC_BON/2","cfgflags":4,"dispslot":2,"name":"Midi Bonus","textname":"Midi Pot","bgpic":"groupbon_bg.png","winsnd":"groupbon_win"},{"mjpid":"MC_BON/3","cfgflags":4,"dispslot":3,"name":"Mini Bonus","textname":"Mini Pot","bgpic":"slotbon_bg.png","winsnd":"slotbon_win"}]}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/1","inst":1,"val":' . (int)($slotSettings->jpgs[0]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/2","inst":2,"val":' . (int)($slotSettings->jpgs[1]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/3","inst":6,"val":' . (int)($slotSettings->jpgs[2]->balance * 100) . ',"flags":4097}';
                                break;
                            case 'SELGAME':
                                $hist = [
                                    78, 
                                    30, 
                                    46, 
                                    62, 
                                    46, 
                                    30
                                ];
                                shuffle($hist);
                                $slotSettings->SetGameData('RedHot7VSCards', $hist);
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100 * 20;
                                }
                                $curReels = '[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . ']';
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550939230150115","for":"SELGAME","id":"000540d0","dirname":"g_re_redho7","reels":5,"vissym":3,"rno":29588,"gtype":1,"credit":{"tot":' . $balanceInCents . '},"allowedbets":[' . implode(',', $gameBets) . '],"curbet":' . $gameBets[0] . ',"curlines":20,"scr":[' . $curReels . '],"prevsym":[8,4,5,7,3],"nextsym":[4,1,6,5,5],"gtypes":[{"gtype":1,"winlines":20,"midx":401,"wlines":[{"pos":[2,2,2,2,2],"bmp":"winline_01.png","y":308,"frcol":[174,96,158]},{"pos":[1,1,1,1,1],"bmp":"winline_02.png","y":175,"frcol":[149,105,57]},{"pos":[3,3,3,3,3],"bmp":"winline_03.png","y":441,"frcol":[227,0,79]},{"pos":[1,2,3,2,1],"bmp":"winline_04.png","y":194,"frcol":[205,205,205]},{"pos":[3,2,1,2,3],"bmp":"winline_05.png","y":174,"frcol":[233,93,15]},{"pos":[2,3,3,3,2],"bmp":"winline_06.png","y":327,"frcol":[226,0,122]},{"pos":[2,1,1,1,2],"bmp":"winline_07.png","y":198,"frcol":[0,144,54]},{"pos":[3,3,2,1,1],"bmp":"winline_08.png","y":157,"frcol":[48,179,173]},{"pos":[1,1,2,3,3],"bmp":"winline_09.png","y":156,"frcol":[0,255,255]},{"pos":[3,2,2,2,1],"bmp":"winline_10.png","y":213,"frcol":[208,223,153]},{"pos":[1,2,2,2,3],"bmp":"winline_11.png","y":213,"frcol":[227,0,79]},{"pos":[1,1,2,1,1],"bmp":"winline_12.png","y":137,"frcol":[241,159,193]},{"pos":[3,3,2,3,3],"bmp":"winline_13.png","y":370,"frcol":[0,74,153]},{"pos":[2,1,2,3,2],"bmp":"winline_14.png","y":149,"frcol":[0,0,255]},{"pos":[2,3,2,1,2],"bmp":"winline_15.png","y":170,"frcol":[251,203,140]},{"pos":[1,2,1,2,1],"bmp":"winline_16.png","y":233,"frcol":[147,16,126]},{"pos":[3,2,3,2,3],"bmp":"winline_17.png","y":275,"frcol":[142,154,201]},{"pos":[2,2,1,2,2],"bmp":"winline_18.png","y":160,"frcol":[255,245,155]},{"pos":[2,2,3,2,2],"bmp":"winline_19.png","y":366,"frcol":[157,13,21]},{"pos":[1,2,2,2,1],"bmp":"winline_20.png","y":119,"frcol":[255,0,0]}],"scatfrcol":[255,0,0],"wnl":[10,3,17,4,16,11,9,18,2,15,5,1,19,8,12,6,14,7,13,0],"wnr":[10,3,17,4,16,11,9,2,18,5,15,1,19,8,12,6,14,7,13,0],"allx":50,"ally":119,"allbmp":"winlineall_20.png","spin":{"pic10sec":145,"accel":160,"over":55,"breakfrom":-240,"backpct":45},"paysyms":[{"sym":1,"m_3":100,"m_4":1000,"m_5":5000,"full":5000},{"sym":2,"m_3":50,"m_4":200,"m_5":500},{"sym":3,"m_2":5,"m_3":25,"m_4":50,"m_5":180},{"sym":4,"m_3":25,"m_4":50,"m_5":180},{"sym":5,"m_3":25,"m_4":50,"m_5":180},{"sym":6,"m_3":50,"m_4":200,"m_5":500},{"sym":7,"m_3":100,"m_4":400,"m_5":1000},{"sym":8,"m_3":25,"m_4":50,"m_5":180}]}],"sounds":["3count01","3count02","3count03","3count04","3count05","3count06","3count07","3count08","3count09","3count10","3count11","3count12","3count13","3count14","3count15","3count16","3count17","3count18","3count19","3count20","applause2","csengo","gamble2lost","gamble2win1","gamble2win2","gamble2win3","gamble2win4","gamble2win5","scatter_rovid1","scatter_rovid2","scatter_rovid3","scatter_rovid4","scatter_rovid5","slidein","win_big1","win_big2","win_big3","win_small1","win_small2","win_small3"],"state":2,"scrfl":[[0,16,16],[0,16,16],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,16,16],[0,16,16],[0,0,0],[0,0,0],[0,0,0]],"win":275,"sgwin":0,"gtypeaft":1,"gmblamount":275,"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[0,0,0,0,0,0]}';
                                break;
                            case 'GA_SPIN':
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
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[6] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[7] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[8] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[9] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[11] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[13] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[17] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[18] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[19] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $lines = $postData['winlines'];
                                $betline = $postData['bet'] / 100 / $lines;
                                $allbet = $postData['bet'] / 100;
                                $postData['slotEvent'] = 'bet';
                                if( $postData['slotEvent'] == 'bet' ) 
                                {
                                    if( $lines <= 0 || $betline <= 0.0001 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->GetBalance() < ($lines * $betline) ) 
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
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetGameData('RedHot7VSBonusWin', 0);
                                    $slotSettings->SetGameData('RedHot7VSFreeGames', 0);
                                    $slotSettings->SetGameData('RedHot7VSCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('RedHot7VSTotalWin', 0);
                                    $slotSettings->SetGameData('RedHot7VSFreeBalance', 0);
                                }
                                $bonusMpl = 1;
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
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
                                    $wild = [''];
                                    $scatter = '0';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    for( $k = 0; $k < $lines; $k++ ) 
                                    {
                                        $tmpStringWin = '';
                                        for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                        {
                                            $csym = $slotSettings->SymbolGame[$j];
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
                                                    $tmpStringWin = '{"n":' . $k . ',"win":' . ($cWins[$k] * 100) . ',"hw":0,"pos":[' . $linesId[$k][0] . ',' . $linesId[$k][1] . ',0,0,0],"frame":[0,0,0],"wnl":1,"wnr":1,"snd":"win_big3"}';
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
                                                    $tmpStringWin = '{"n":' . $k . ',"win":' . ($cWins[$k] * 100) . ',"hw":0,"pos":[' . $linesId[$k][0] . ',' . $linesId[$k][1] . ',' . $linesId[$k][2] . ',0,0],"frame":[0,0,0],"wnl":1,"wnr":1,"snd":"win_big3"}';
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
                                                    $tmpStringWin = '{"n":' . $k . ',"win":' . ($cWins[$k] * 100) . ',"hw":0,"pos":[' . $linesId[$k][0] . ',' . $linesId[$k][1] . ',' . $linesId[$k][2] . ',' . $linesId[$k][3] . ',0],"frame":[0,0,0],"wnl":1,"wnr":1,"snd":"win_big2"}';
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
                                                    $tmpStringWin = '{"n":' . $k . ',"win":' . ($cWins[$k] * 100) . ',"hw":0,"pos":[' . $linesId[$k][0] . ',' . $linesId[$k][1] . ',' . $linesId[$k][2] . ',' . $linesId[$k][3] . ',' . $linesId[$k][4] . '],"frame":[0,0,0],"wnl":1,"wnr":1,"snd":"win_big1"}';
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
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '7' ) 
                                            {
                                                $scattersCount++;
                                                $scPos[] = '[' . ($r - 1) . ',' . $p . ']';
                                            }
                                        }
                                    }
                                    if( $scattersWin > 0 ) 
                                    {
                                        $scattersStr = ',"scatwin":[{"win":' . ($scattersWin * 100) . ',"sgwin":15,"hw":0,"spos":[' . implode(',', $scPos) . '],"frame":[174,0,0],"snd":"win_big1","ms":1500,"musictime":1}]';
                                    }
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
                                $rLen = [
                                    4, 
                                    7, 
                                    10, 
                                    13, 
                                    16
                                ];
                                $syms = [
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    []
                                ];
                                for( $i = 1; $i <= 5; $i++ ) 
                                {
                                    $rc = $reels['rp'][$i - 1];
                                    for( $j = 0; $j <= $rLen[$i - 1]; $j++ ) 
                                    {
                                        $rc--;
                                        if( $rc < 0 ) 
                                        {
                                            $rc = count($slotSettings->{'reelStrip' . $i}) - 1;
                                        }
                                        $syms[$i - 1][] = $slotSettings->{'reelStrip' . $i}[$rc];
                                    }
                                    $syms[$i - 1][] = $reels['reel' . $i][2];
                                    $syms[$i - 1][] = $reels['reel' . $i][1];
                                    $syms[$i - 1][] = $reels['reel' . $i][0];
                                }
                                $reelsStr = '{"syms":[' . implode(',', $syms[0]) . ']},{"syms":[' . implode(',', $syms[1]) . ']},{"syms":[' . implode(',', $syms[2]) . ']},{"syms":[' . implode(',', $syms[3]) . ']},{"syms":[' . implode(',', $syms[4]) . ']}';
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('RedHot7VSFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('RedHot7VSCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('RedHot7VSBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $betline, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $state = '0';
                                $state0 = '0';
                                if( $totalWin > 0 ) 
                                {
                                    $state = '2';
                                    $state0 = '1';
                                    $winstring = ',"winlines":[' . $winString . '],"wlseqms":1500,"wlmusictime":1';
                                    $slotSettings->SetGameData('RedHot7VSTotalWin', $totalWin);
                                }
                                $hist = $slotSettings->GetGameData('RedHot7VSCards');
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550874224233854","for":"GA_SPIN","rno":29083,"state":' . $state . ',"gtype":1,"noofwl":' . $state0 . ',"animallwinsym":2' . $winstring . ',"reels":[' . $reelsStr . '],"nextsym":[8,1,6,6,6],"scrflbef":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"win":' . ($totalWin * 100) . ',"sgwin":0' . $scattersStr . ',"gtypeaft":1,"gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"credit":{"tot":' . $balanceInCents . '}}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/1","inst":1,"val":' . (int)($slotSettings->jpgs[0]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/2","inst":2,"val":' . (int)($slotSettings->jpgs[1]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/3","inst":6,"val":' . (int)($slotSettings->jpgs[2]->balance * 100) . ',"flags":4097}';
                                break;
                            case 'GA_TAKE':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $slotSettings->SetGameData('RedHot7VSTotalWin', 0);
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550919705477001","for":"GA_TAKE","gtype":1,"credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'QUITGAME':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550926313646767","for":"QUITGAME","credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'GA_GAMBLE':
                                $Balance = $slotSettings->GetBalance();
                                $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData('RedHot7VSTotalWin');
                                $gambleWin = 0;
                                $statBet = $totalWin;
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                $sndID = 'gamble2lost';
                                if( $isGambleWin == 1 ) 
                                {
                                    $gambleState = 'win';
                                    $sndID = 'gamble2win1';
                                    $gambleWin = $totalWin;
                                    $totalWin = $totalWin * 2;
                                    if( $postData['choice'] == '1' ) 
                                    {
                                        $tmpCards = [
                                            '30', 
                                            '46'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            '62', 
                                            '78'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                                else
                                {
                                    $gambleState = 'lose';
                                    $sndID = 'gamble2lost';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    if( $postData['choice'] == '1' ) 
                                    {
                                        $tmpCards = [
                                            '62', 
                                            '78'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            '30', 
                                            '46'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                                $slotSettings->SetGameData('RedHot7VSTotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, 'slotGamble');
                                $hist = $slotSettings->GetGameData('RedHot7VSCards');
                                array_pop($hist);
                                array_unshift($hist, $dealerCard);
                                $slotSettings->SetGameData('RedHot7VSCards', $hist);
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550923628407358","for":"GA_GAMBLE","rno":29444,"state":3,"gmblwinf":2,"card":' . $dealerCard . ',"halfs":0,"snd":"' . $sndID . '","gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"takesnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"gtype":1,"win":' . ($totalWin * 100) . ",\"sgwin\":0,\"gtypeaft\":1,\"credit\":{\"tot\":40885}}\n";
                                break;
                        }
                        $response = implode('------:::', $result_tmp);
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
