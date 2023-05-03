<?php 
namespace VanguardLTE\Games\BeatleDanceVS
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
                                $result_tmp[] = '{"status":"OK","for":"QRY_CHECK","device":"S4617","login":"user","username":"","firstname":"","lastname":"CARD_4116078299570565","usejp":1,"forward":"e1","game":"00050070","credit":{"tot":' . $balanceInCents . '},"account":{"tot":0}}';
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
                                $slotSettings->SetGameData('BeatleDanceVSCards', $hist);
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100 * 5;
                                }
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData('BeatleDanceVSBonusWin', 0);
                                $slotSettings->SetGameData('BeatleDanceVSFreeGames', 0);
                                $slotSettings->SetGameData('BeatleDanceVSCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BeatleDanceVSTotalWin', 0);
                                $slotSettings->SetGameData('BeatleDanceVSFreeBalance', 0);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    if( isset($lastEvent->serverResponse->bonusWin) ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->totalWin);
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '[' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . '],[' . $reels->reel2[0] . ',' . $reels->reel2[1] . ',' . $reels->reel2[2] . '],[' . $reels->reel3[0] . ',' . $reels->reel3[1] . ',' . $reels->reel3[2] . '],[' . $reels->reel4[0] . ',' . $reels->reel4[1] . ',' . $reels->reel4[2] . '],[' . $reels->reel5[0] . ',' . $reels->reel5[1] . ',' . $reels->reel5[2] . ']';
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * $lines * 100;
                                    $gtype = 1;
                                    if( $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') < $slotSettings->GetGameData('BeatleDanceVSFreeGames') && $slotSettings->GetGameData('BeatleDanceVSFreeGames') > 0 ) 
                                    {
                                        $gtype = 2;
                                    }
                                }
                                else
                                {
                                    $gtype = 1;
                                    $curReels = '[' . rand(1, 12) . ',' . rand(1, 12) . ',' . rand(1, 12) . '],[' . rand(1, 12) . ',' . rand(1, 12) . ',' . rand(1, 12) . '],[' . rand(1, 12) . ',' . rand(1, 12) . ',' . rand(1, 12) . '],[' . rand(1, 12) . ',' . rand(1, 12) . ',' . rand(1, 12) . '],[' . rand(1, 12) . ',' . rand(1, 12) . ',' . rand(1, 12) . ']';
                                    $lines = 5;
                                    $bet = $gameBets[0];
                                }
                                $result_tmp[] = '{"status":"OK","authtoken":"1/10104330032997345119","for":"SELGAME","id":"00050370","inscatwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"plsg":' . $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') . ',"totsg":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . ',"dirname":"g_re_beedan","reels":5,"vissym":3,"rno":29940,"gtype":' . $gtype . ',"credit":{"tot":' . $balanceInCents . '},"allowedbets":[' . implode(',', $gameBets) . '],"curbet":' . $bet . ',"curlines":' . $lines . ',"scr":[' . $curReels . '],"prevsym":[9,9,3,3,5],"nextsym":[8,9,1,6,2],"gtypes":[{"gtype":1,"winlines":5,"midx":401,"wlines":[{"pos":[2,2,2,2,2],"bmp":"winline_01.png","y":299,"frcol":[207,0,27]},{"pos":[1,1,1,1,1],"bmp":"winline_02.png","y":173,"frcol":[72,255,49]},{"pos":[3,3,3,3,3],"bmp":"winline_03.png","y":425,"frcol":[77,15,255]},{"pos":[1,2,3,2,1],"bmp":"winline_04.png","y":130,"frcol":[255,229,4]},{"pos":[3,2,1,2,3],"bmp":"winline_05.png","y":171,"frcol":[200,64,255]}],"scatfrcol":[255,0,0],"wnl":[4,1,7,0,8],"wnr":[4,1,7,0,8],"allx":51,"ally":130,"allbmp":"winlineall_5.png","spin":{"pic10sec":130,"back":30,"backms":180,"backwait":80,"accel":110,"over":35,"breakfrom":-150,"backpct":60},"jokers":[{"sym":1}],"paysyms":[{"sym":1,"m_2":10,"m_3":80,"m_4":1000,"m_5":5000},{"sym":2,"m_2":5,"m_3":30,"m_4":200,"m_5":1000},{"sym":3,"m_2":5,"m_3":25,"m_4":100,"m_5":500},{"sym":4,"m_2":5,"m_3":15,"m_4":65,"m_5":250},{"sym":5,"m_3":10,"m_4":40,"m_5":200},{"sym":6,"m_3":10,"m_4":40,"m_5":200},{"sym":8,"m_3":5,"m_4":20,"m_5":100},{"sym":9,"m_3":5,"m_4":20,"m_5":100},{"sym":10,"m_3":5,"m_4":20,"m_5":100}],"scatters":[{"sym":7,"nfrom":5,"ntill":0,"m":50,"m_div":0,"sg":10},{"sym":7,"nfrom":4,"ntill":4,"m":15,"m_div":0,"sg":10},{"sym":7,"nfrom":3,"ntill":3,"m":2,"m_div":0,"sg":10}]},{"gtype":2,"winlines":5,"spin":{"pic10sec":130,"back":30,"backms":180,"backwait":80,"accel":110,"over":35,"breakfrom":-150,"backpct":60},"jokers":[{"sym":1}],"paysyms":[{"sym":1,"m_2":10,"m_3":80,"m_4":1000,"m_5":5000},{"sym":2,"m_2":5,"m_3":30,"m_4":200,"m_5":1000},{"sym":3,"m_2":5,"m_3":25,"m_4":100,"m_5":500},{"sym":4,"m_2":5,"m_3":15,"m_4":65,"m_5":250},{"sym":5,"m_3":10,"m_4":40,"m_5":200},{"sym":6,"m_3":10,"m_4":40,"m_5":200},{"sym":8,"m_3":5,"m_4":20,"m_5":100},{"sym":9,"m_3":5,"m_4":20,"m_5":100},{"sym":10,"m_3":5,"m_4":20,"m_5":100}],"scatters":[{"sym":7,"nfrom":5,"ntill":0,"m":50,"m_div":0,"sg":0},{"sym":7,"nfrom":4,"ntill":4,"m":15,"m_div":0,"sg":0},{"sym":7,"nfrom":3,"ntill":3,"m":2,"m_div":0,"sg":0}]}],"sounds":["applause2","fire_scatter","gamble2lost","gamble2win1","gamble2win2","gamble2win3","gamble2win4","gamble2win5","reel_aft2scat","scatter_ending","scatter_rovid1","scatter_rovid2","scatter_rovid3","scatter_rovid4","scatter_rovid5","scatter_win","slidein","win_big1","win_big2","win_big3","win_huge1","win_huge2","win_huge3","win_small1","win_small2","win_small3","wormanim_inscatter","wpay_end"],"state":0,"scrfl":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]]}';
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
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[6] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[7] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[9] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $lines = $postData['winlines'];
                                $betline = $postData['bet'] / 100 / $lines;
                                $allbet = $postData['bet'] / 100;
                                $postData['slotEvent'] = 'bet';
                                if( $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') < $slotSettings->GetGameData('BeatleDanceVSFreeGames') && $slotSettings->GetGameData('BeatleDanceVSFreeGames') > 0 ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                    $slotSettings->SetGameData('BeatleDanceVSCurrentFreeGame', $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') + 1);
                                }
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
                                    $slotSettings->SetGameData('BeatleDanceVSBonusWin', 0);
                                    $slotSettings->SetGameData('BeatleDanceVSFreeGames', 0);
                                    $slotSettings->SetGameData('BeatleDanceVSCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('BeatleDanceVSTotalWin', 0);
                                    $slotSettings->SetGameData('BeatleDanceVSFreeBalance', 0);
                                    $bonusMpl = 1;
                                }
                                else
                                {
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
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
                                    $wild = ['1'];
                                    $scatter = '7';
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
                                    $scattersWin = $slotSettings->Paytable['SYM_7'][$scattersCount] * $allbet;
                                    if( $scattersWin > 0 ) 
                                    {
                                        $sgwin = 0;
                                        if( $scattersCount >= 3 ) 
                                        {
                                            $sgwin = $slotSettings->slotFreeCount;
                                        }
                                        $scattersStr = ',"scatwin":[{"win":' . ($scattersWin * 100) . ',"sgwin":' . $sgwin . ',"hw":0,"spos":[' . implode(',', $scPos) . '],"frame":[174,0,0],"snd":"win_big1","ms":1500,"musictime":1}]';
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
                                        else
                                        {
                                            $aftertake = '';
                                            if( $postData['slotEvent'] == 'freespin' && ($reels['reel3'][0] == '11' || $reels['reel3'][1] == '11' || $reels['reel3'][2] == '11') && $totalWin > 0 ) 
                                            {
                                                $totalWin = $slotSettings->GetGameData('BeatleDanceVSBonusWin') + $totalWin;
                                                $aftertake = ',"aftertake":{"addon_type":1,"addon_sym":11,"addon_snd1":"wormanim_inscatter","addon_inscatwinto":' . (($slotSettings->GetGameData('BeatleDanceVSBonusWin') + $totalWin) * 2 * 100) . ',"addon_snd2":"wpay_end","addon_ms2":2000}';
                                            }
                                            else if( $postData['slotEvent'] == 'freespin' && ($reels['reel3'][0] == '11' || $reels['reel3'][1] == '11' || $reels['reel3'][2] == '11') && $totalWin == 0 ) 
                                            {
                                            }
                                            if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                                $rPref = '';
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $rPref = 'Bonus';
                                }
                                for( $i = 1; $i <= 5; $i++ ) 
                                {
                                    $rc = $reels['rp'][$i - 1];
                                    for( $j = 0; $j <= $rLen[$i - 1]; $j++ ) 
                                    {
                                        $rc--;
                                        if( $rc < 0 ) 
                                        {
                                            $rc = count($slotSettings->{'reelStrip' . $rPref . $i}) - 1;
                                        }
                                        $syms[$i - 1][] = $slotSettings->{'reelStrip' . $rPref . $i}[$rc];
                                    }
                                    $syms[$i - 1][] = $reels['reel' . $i][2];
                                    $syms[$i - 1][] = $reels['reel' . $i][1];
                                    $syms[$i - 1][] = $reels['reel' . $i][0];
                                }
                                $reelsStr = '{"syms":[' . implode(',', $syms[0]) . ']},{"syms":[' . implode(',', $syms[1]) . ']},{"syms":[' . implode(',', $syms[2]) . ']},{"syms":[' . implode(',', $syms[3]) . ']},{"syms":[' . implode(',', $syms[4]) . ']}';
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('BeatleDanceVSBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $betline, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $state = '0';
                                $state0 = '0';
                                if( $totalWin > 0 ) 
                                {
                                    $state = '2';
                                    $state0 = '1';
                                    $winstring = ',"winlines":[' . $winString . '],"wlseqms":1500,"wlmusictime":1';
                                }
                                $hist = $slotSettings->GetGameData('BeatleDanceVSCards');
                                $gtypeaft = 1;
                                $slotSettings->SetGameData('BeatleDanceVSBonusStart', 0);
                                if( $scattersCount >= 3 ) 
                                {
                                    if( $slotSettings->GetGameData('BeatleDanceVSFreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData('BeatleDanceVSFreeGames', $slotSettings->GetGameData('BeatleDanceVSFreeGames') + $slotSettings->slotFreeCount);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('BeatleDanceVSBonusWin', 0);
                                        $slotSettings->SetGameData('BeatleDanceVSBonusStart', 1);
                                        $slotSettings->SetGameData('BeatleDanceVSFreeGames', $slotSettings->slotFreeCount);
                                        $gtypeaft = 2;
                                    }
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $slotSettings->SetGameData('BeatleDanceVSTotalWin', $totalWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('BeatleDanceVSTotalWin', $totalWin);
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $gtypeaft = 2;
                                    if( $slotSettings->GetGameData('BeatleDanceVSFreeGames') == $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') ) 
                                    {
                                        $gtypeaft = 1;
                                    }
                                    $result_tmp[] = '{"status":"OK","authtoken":"1/90317550874224233854","for":"GA_SPIN"' . $aftertake . ',"rno":29689,"state":' . $state . ',"gtype":2' . $winstring . ',"inscatwin":' . ($slotSettings->GetGameData('BeatleDanceVSBonusWin') * 100) . ',"plsg":' . $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') . ',"totsg":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . ',"noofwl":' . $state0 . ',"reels":[' . $reelsStr . '],"nextsym":[9,8,11,12,10],"scrflbef":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"win":' . ($totalWin * 100) . ',"gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"sgwin":0' . $scattersStr . ',"gtypeaft":' . $gtypeaft . ',"credit":{"tot":' . $balanceInCents . '}}';
                                }
                                else
                                {
                                    $result_tmp[] = '{"status":"OK","authtoken":"1/90317550874224233854","for":"GA_SPIN","rno":29083,"state":' . $state . ',"gtype":1,"noofwl":' . $state0 . ',"animallwinsym":2' . $winstring . ',"reels":[' . $reelsStr . '],"nextsym":[8,1,6,6,6],"scrflbef":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"win":' . ($totalWin * 100) . ',"sgwin":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . '' . $scattersStr . ',"gtypeaft":' . $gtypeaft . ',"gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"credit":{"tot":' . $balanceInCents . '}}';
                                }
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/1","inst":1,"val":' . (int)($slotSettings->jpgs[0]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/2","inst":2,"val":' . (int)($slotSettings->jpgs[1]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/3","inst":6,"val":' . (int)($slotSettings->jpgs[2]->balance * 100) . ',"flags":4097}';
                                break;
                            case 'GA_TAKE':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $gtype = 1;
                                if( $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') < $slotSettings->GetGameData('BeatleDanceVSFreeGames') && $slotSettings->GetGameData('BeatleDanceVSFreeGames') > 0 ) 
                                {
                                    $gtype = 2;
                                    $slotSettings->SetGameData('BeatleDanceVSBonusWin', $slotSettings->GetGameData('BeatleDanceVSBonusWin') + $slotSettings->GetGameData('BeatleDanceVSTotalWin'));
                                }
                                $slotSettings->SetGameData('BeatleDanceVSTotalWin', 0);
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550919705477001","for":"GA_TAKE","totsg":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . ',"plsg":' . $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') . ',"inscatwin":' . ($slotSettings->GetGameData('BeatleDanceVSBonusWin') * 100) . ',"gtype":' . $gtype . ',"credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'QUITGAME':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550926313646767","for":"QUITGAME","credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'GA_GAMBLE':
                                $Balance = $slotSettings->GetBalance();
                                $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData('BeatleDanceVSTotalWin');
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
                                $slotSettings->SetGameData('BeatleDanceVSTotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, 'slotGamble');
                                $hist = $slotSettings->GetGameData('BeatleDanceVSCards');
                                array_pop($hist);
                                array_unshift($hist, $dealerCard);
                                $slotSettings->SetGameData('BeatleDanceVSCards', $hist);
                                $gtype = 1;
                                $gtype0 = 1;
                                if( $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') < $slotSettings->GetGameData('BeatleDanceVSFreeGames') && $slotSettings->GetGameData('BeatleDanceVSFreeGames') > 0 ) 
                                {
                                    $gtype = 2;
                                }
                                if( $slotSettings->GetGameData('BeatleDanceVSBonusStart') == 1 ) 
                                {
                                    $gtype0 = 2;
                                }
                                $result_tmp[] = '{"status":"OK","gtypeaft":' . $gtype . ',"gtype":' . $gtype . ',"totsg":' . $slotSettings->GetGameData('BeatleDanceVSFreeGames') . ',"plsg":' . $slotSettings->GetGameData('BeatleDanceVSCurrentFreeGame') . ',"inscatwin":' . ($slotSettings->GetGameData('BeatleDanceVSBonusWin') * 100) . ',"authtoken":"1/90317550923628407358","for":"GA_GAMBLE","rno":29444,"state":3,"gmblwinf":2,"card":' . $dealerCard . ',"halfs":0,"snd":"' . $sndID . '","gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"takesnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"win":' . ($totalWin * 100) . ',"sgwin":0,"credit":{"tot":40885}}';
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
