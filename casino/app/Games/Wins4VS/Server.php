<?php 
namespace VanguardLTE\Games\Wins4VS
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
                                $slotSettings->SetGameData('Wins4VSCards', $hist);
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100 * 25;
                                }
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData('Wins4VSBonusWin', 0);
                                $slotSettings->SetGameData('Wins4VSFreeGames', 0);
                                $slotSettings->SetGameData('Wins4VSCurrentFreeGame', 0);
                                $slotSettings->SetGameData('Wins4VSTotalWin', 0);
                                $slotSettings->SetGameData('Wins4VSFreeBalance', 0);
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
                                    $curReels = '[' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . '],[' . $reels->reel2[0] . ',' . $reels->reel2[1] . ',' . $reels->reel2[2] . '],[' . $reels->reel3[0] . ',' . $reels->reel3[1] . ',' . $reels->reel3[2] . '],[' . $reels->reel4[0] . ',' . $reels->reel4[1] . ',' . $reels->reel4[2] . ']';
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * $lines * 100;
                                    $gtype = 1;
                                    if( $slotSettings->GetGameData('Wins4VSCurrentFreeGame') < $slotSettings->GetGameData('Wins4VSFreeGames') && $slotSettings->GetGameData('Wins4VSFreeGames') > 0 ) 
                                    {
                                        $gtype = 2;
                                    }
                                }
                                else
                                {
                                    $gtype = 1;
                                    $curReels = '[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . '],[' . rand(1, 8) . ',' . rand(1, 8) . ',' . rand(1, 8) . ']';
                                    $lines = 25;
                                    $bet = $gameBets[0];
                                }
                                $result_tmp[] = '{"status":"OK","authtoken":"1/10104330035310674419","for":"SELGAME","id":"00054020","dirname":"g_re_4wins","reels":4,"vissym":3,"rno":29960,"gtype":1,"credit":{"tot":' . $balanceInCents . '},"allowedbets":[' . implode(',', $gameBets) . '],"curbet":' . $bet . ',"curlines":' . $lines . ',"scr":[' . $curReels . '],"prevsym":[6,5,1,8],"nextsym":[8,6,7,1],"gtypes":[{"gtype":1,"winlines":25,"midx":400,"wlines":[{"pos":[2,2,2,2],"bmp":"winline_01.png","y":296,"frcol":[255,0,0]},{"pos":[1,1,1,1],"bmp":"winline_02.png","y":168,"frcol":[42,251,0]},{"pos":[3,3,3,3],"bmp":"winline_03.png","y":424,"frcol":[44,195,255]},{"pos":[1,2,2,3],"bmp":"winline_04.png","y":182,"frcol":[255,255,0]},{"pos":[3,2,2,1],"bmp":"winline_05.png","y":168,"frcol":[255,1,255]},{"pos":[2,1,2,1],"bmp":"winline_06.png","y":178,"frcol":[0,245,226]},{"pos":[2,3,2,3],"bmp":"winline_07.png","y":308,"frcol":[255,0,255]},{"pos":[2,1,1,2],"bmp":"winline_08.png","y":178,"frcol":[108,251,81]},{"pos":[2,3,3,2],"bmp":"winline_09.png","y":288,"frcol":[250,232,0]},{"pos":[1,1,2,2],"bmp":"winline_10.png","y":148,"frcol":[255,5,255]},{"pos":[3,3,2,2],"bmp":"winline_11.png","y":282,"frcol":[255,187,0]},{"pos":[2,2,1,1],"bmp":"winline_12.png","y":149,"frcol":[100,212,255]},{"pos":[2,2,3,3],"bmp":"winline_13.png","y":295,"frcol":[255,18,9]},{"pos":[1,2,1,2],"bmp":"winline_14.png","y":178,"frcol":[140,255,0]},{"pos":[3,2,3,2],"bmp":"winline_15.png","y":308,"frcol":[4,4,255]},{"pos":[1,2,2,1],"bmp":"winline_16.png","y":193,"frcol":[255,221,0]},{"pos":[3,2,2,3],"bmp":"winline_17.png","y":308,"frcol":[0,252,196]},{"pos":[1,1,2,3],"bmp":"winline_18.png","y":128,"frcol":[255,72,197]},{"pos":[3,3,2,1],"bmp":"winline_19.png","y":138,"frcol":[0,228,0]},{"pos":[3,2,1,1],"bmp":"winline_20.png","y":118,"frcol":[164,101,0]},{"pos":[1,2,3,3],"bmp":"winline_21.png","y":158,"frcol":[55,191,255]},{"pos":[1,1,3,3],"bmp":"winline_22.png","y":173,"frcol":[36,240,21]},{"pos":[3,3,1,1],"bmp":"winline_23.png","y":165,"frcol":[197,0,198]},{"pos":[1,3,3,1],"bmp":"winline_24.png","y":168,"frcol":[38,251,240]},{"pos":[3,1,1,3],"bmp":"winline_25.png","y":160,"frcol":[0,112,131]}],"scatfrcol":[255,255,0],"wnl":[6,-1,-1,2,10,5,7,-1,-1,1,11,-1,-1,3,9,-1,-1,0,12,-1,-1,4,8,-1,-1],"wnr":[6,2,10,-1,-1,-1,-1,5,7,-1,-1,1,11,-1,-1,3,9,-1,-1,0,12,-1,-1,4,8],"allx":35,"ally":117,"allbmp":"winlineall_25.png","spin":{"pic10sec":120,"back":36,"backms":120,"backwait":80,"accel":200,"over":35,"breakfrom":-200,"backpct":50},"paysyms":[{"sym":1,"m_3":500,"m_4":5000},{"sym":2,"m_3":150,"m_4":1000},{"sym":3,"m_3":100,"m_4":500},{"sym":4,"m_3":25,"m_4":150},{"sym":5,"m_3":20,"m_4":75},{"sym":6,"m_3":15,"m_4":50},{"sym":7,"m_3":50,"m_4":250},{"sym":8,"m_3":12,"m_4":25,"full":240}]}],"sounds":["3count01","3count02","3count03","3count04","3count05","3count06","3count07","3count08","3count09","3count10","3count11","3count12","3count13","3count14","3count15","3count16","3count17","3count18","3count19","3count20","3count21","3count22","3count23","3count24","3count25","applause2","gamble2lost","gamble2win1","gamble2win2","gamble2win3","gamble2win4","gamble2win5","scatter_rovid1","scatter_rovid2","scatter_rovid3","scatter_rovid4","slidein","win_big1","win_big2","win_big3","win_small1","win_small2","win_small3"],"state":0,"scrfl":[[0,0,0],[0,0,0],[0,0,0],[0,0,0]]}';
                                break;
                            case 'GA_SPIN':
                                $linesId = [];
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[1] = [
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[2] = [
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[3] = [
                                    1, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[4] = [
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[5] = [
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[6] = [
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[7] = [
                                    2, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[8] = [
                                    2, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[9] = [
                                    1, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[10] = [
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[11] = [
                                    2, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    2, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[13] = [
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[14] = [
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[17] = [
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[18] = [
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[19] = [
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[20] = [
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[21] = [
                                    1, 
                                    1, 
                                    3, 
                                    3
                                ];
                                $linesId[22] = [
                                    3, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[23] = [
                                    1, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[24] = [
                                    3, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $lines = $postData['winlines'];
                                $betline = $postData['bet'] / 100 / $lines;
                                $allbet = $postData['bet'] / 100;
                                $postData['slotEvent'] = 'bet';
                                if( $slotSettings->GetGameData('Wins4VSCurrentFreeGame') < $slotSettings->GetGameData('Wins4VSFreeGames') && $slotSettings->GetGameData('Wins4VSFreeGames') > 0 ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                    $slotSettings->SetGameData('Wins4VSCurrentFreeGame', $slotSettings->GetGameData('Wins4VSCurrentFreeGame') + 1);
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
                                    $slotSettings->SetGameData('Wins4VSBonusWin', 0);
                                    $slotSettings->SetGameData('Wins4VSFreeGames', 0);
                                    $slotSettings->SetGameData('Wins4VSCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('Wins4VSTotalWin', 0);
                                    $slotSettings->SetGameData('Wins4VSFreeBalance', 0);
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
                                    $wild = [''];
                                    $scatter = '';
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
                                for( $i = 1; $i <= 4; $i++ ) 
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
                                $reelsStr = '{"syms":[' . implode(',', $syms[0]) . ']},{"syms":[' . implode(',', $syms[1]) . ']},{"syms":[' . implode(',', $syms[2]) . ']},{"syms":[' . implode(',', $syms[3]) . ']}';
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('Wins4VSFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('Wins4VSCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('Wins4VSBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
                                $hist = $slotSettings->GetGameData('Wins4VSCards');
                                $gtypeaft = 1;
                                $slotSettings->SetGameData('Wins4VSBonusStart', 0);
                                if( $scattersCount >= 3 ) 
                                {
                                    if( $slotSettings->GetGameData('Wins4VSFreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData('Wins4VSFreeGames', $slotSettings->GetGameData('Wins4VSFreeGames') + $slotSettings->slotFreeCount);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('Wins4VSBonusWin', 0);
                                        $slotSettings->SetGameData('Wins4VSBonusStart', 1);
                                        $slotSettings->SetGameData('Wins4VSFreeGames', $slotSettings->slotFreeCount);
                                        $gtypeaft = 2;
                                    }
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $slotSettings->SetGameData('Wins4VSTotalWin', $totalWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('Wins4VSTotalWin', $totalWin);
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $gtypeaft = 2;
                                    if( $slotSettings->GetGameData('Wins4VSFreeGames') == $slotSettings->GetGameData('Wins4VSCurrentFreeGame') ) 
                                    {
                                        $gtypeaft = 1;
                                    }
                                    $result_tmp[] = '{"status":"OK","authtoken":"1/90317550874224233854","for":"GA_SPIN","rno":29689,"state":' . $state . ',"gtype":2' . $winstring . ',"inscatwin":' . ($slotSettings->GetGameData('Wins4VSBonusWin') * 100) . ',"plsg":' . $slotSettings->GetGameData('Wins4VSCurrentFreeGame') . ',"totsg":' . $slotSettings->GetGameData('Wins4VSFreeGames') . ',"noofwl":' . $state0 . ',"reels":[' . $reelsStr . '],"nextsym":[9,8,11,12,10],"scrflbef":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"win":' . ($totalWin * 100) . ',"gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"sgwin":0' . $scattersStr . ',"gtypeaft":' . $gtypeaft . ',"credit":{"tot":' . $balanceInCents . '}}';
                                }
                                else
                                {
                                    $result_tmp[] = '{"status":"OK","authtoken":"1/10104330035310674419","for":"GA_SPIN","rno":29961,"state":' . $state . ',"gtype":1,"noofwl":' . $state0 . ',"animallwinsym":2' . $winstring . ',"reels":[' . $reelsStr . '],"nextsym":[8,8,4,5],"scrflbef":[[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"scrflaft":[[0,0,0],[0,0,0],[0,0,0],[0,0,0]],"win":' . ($totalWin * 100) . ',"sgwin":0,"gtypeaft":1,"gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"gmblsnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"credit":{"tot":' . $balanceInCents . '}}';
                                }
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/1","inst":1,"val":' . (int)($slotSettings->jpgs[0]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/2","inst":2,"val":' . (int)($slotSettings->jpgs[1]->balance * 100) . ',"flags":4097}';
                                $result_tmp[] = '{"amsg":"MJPUPD","mjpid":"MC_BON/3","inst":6,"val":' . (int)($slotSettings->jpgs[2]->balance * 100) . ',"flags":4097}';
                                break;
                            case 'GA_TAKE':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $gtype = 1;
                                if( $slotSettings->GetGameData('Wins4VSCurrentFreeGame') < $slotSettings->GetGameData('Wins4VSFreeGames') && $slotSettings->GetGameData('Wins4VSFreeGames') > 0 ) 
                                {
                                    $gtype = 2;
                                    $slotSettings->SetGameData('Wins4VSBonusWin', $slotSettings->GetGameData('Wins4VSBonusWin') + $slotSettings->GetGameData('Wins4VSTotalWin'));
                                }
                                $slotSettings->SetGameData('Wins4VSTotalWin', 0);
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550919705477001","for":"GA_TAKE","totsg":' . $slotSettings->GetGameData('Wins4VSFreeGames') . ',"plsg":' . $slotSettings->GetGameData('Wins4VSCurrentFreeGame') . ',"inscatwin":' . ($slotSettings->GetGameData('Wins4VSBonusWin') * 100) . ',"gtype":' . $gtype . ',"credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'QUITGAME':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $result_tmp[] = '{"status":"OK","authtoken":"1/90317550926313646767","for":"QUITGAME","credit":{"tot":' . $balanceInCents . '}}';
                                break;
                            case 'GA_GAMBLE':
                                $Balance = $slotSettings->GetBalance();
                                $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData('Wins4VSTotalWin');
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
                                $slotSettings->SetGameData('Wins4VSTotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, 'slotGamble');
                                $hist = $slotSettings->GetGameData('Wins4VSCards');
                                array_pop($hist);
                                array_unshift($hist, $dealerCard);
                                $slotSettings->SetGameData('Wins4VSCards', $hist);
                                $gtype = 1;
                                $gtype0 = 1;
                                if( $slotSettings->GetGameData('Wins4VSCurrentFreeGame') < $slotSettings->GetGameData('Wins4VSFreeGames') && $slotSettings->GetGameData('Wins4VSFreeGames') > 0 ) 
                                {
                                    $gtype = 2;
                                }
                                if( $slotSettings->GetGameData('Wins4VSBonusStart') == 1 ) 
                                {
                                    $gtype0 = 2;
                                }
                                $result_tmp[] = '{"status":"OK","gtypeaft":' . $gtype . ',"gtype":' . $gtype . ',"totsg":' . $slotSettings->GetGameData('Wins4VSFreeGames') . ',"plsg":' . $slotSettings->GetGameData('Wins4VSCurrentFreeGame') . ',"inscatwin":' . ($slotSettings->GetGameData('Wins4VSBonusWin') * 100) . ',"authtoken":"1/90317550923628407358","for":"GA_GAMBLE","rno":29444,"state":3,"gmblwinf":2,"card":' . $dealerCard . ',"halfs":0,"snd":"' . $sndID . '","gmblamount":' . ($totalWin * 100) . ',"gmblmayhalf":2,"gmblhalfmin":100,"takesnd":"slidein","gmblhist":[' . implode(',', $hist) . '],"win":' . ($totalWin * 100) . ',"sgwin":0,"credit":{"tot":40885}}';
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
