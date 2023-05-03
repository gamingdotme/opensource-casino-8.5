<?php 
namespace VanguardLTE\Games\FortuneGodKA
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
                        if( $postData['command'] == 'bet' && $postData['bet']['gameCommand'] == 'collect' ) 
                        {
                            $postData['command'] = 'collect';
                        }
                        $aid = (string)$postData['command'];
                        switch( $aid ) 
                        {
                            case 'startGame':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '' . rand(0, 6) . ',' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . ',' . rand(0, 6);
                                    $curReels = '';
                                    $curReels .= ($reels->reel1[3] . ',' . $reels->reel2[3] . ',' . $reels->reel3[3] . ',' . $reels->reel4[3] . ',' . $reels->reel5[3]);
                                    $curReels .= (',' . $reels->reel1[2] . ',' . $reels->reel2[2] . ',' . $reels->reel3[2] . ',' . $reels->reel4[2] . ',' . $reels->reel5[2]);
                                    $curReels .= (',' . $reels->reel1[1] . ',' . $reels->reel2[1] . ',' . $reels->reel3[1] . ',' . $reels->reel4[1] . ',' . $reels->reel5[1]);
                                    $curReels .= (',' . $reels->reel1[0] . ',' . $reels->reel2[0] . ',' . $reels->reel3[0] . ',' . $reels->reel4[0] . ',' . $reels->reel5[0]);
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * 100;
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                    }
                                }
                                else
                                {
                                    $curReels = '' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6);
                                    $lines = 5;
                                    $bet = $slotSettings->Bet[0] * 100;
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $acb = 1;
                                    $fsr = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $fs = 'true';
                                    $rd = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $tfw = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100;
                                }
                                else
                                {
                                    $rd = 0;
                                    $fs = 'false';
                                    $acb = 0;
                                    $fsr = 0;
                                    $tfw = 0;
                                }
                                $result_tmp[0] = '{"sgr":{"gn":"ImperialGirls","lsd":{"sid":' . rand(0, 9999999) . ',"tid":"1fdbe371480a4b4a8971101c2a170721","sel":5,"cps":1,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":' . $fs . ',"twg":' . (50 * $bet) . ',"swm":0,"sw":0,"swu":0,"tw":0,"fsw":0,"fsr":' . $fsr . ',"tfw":' . $tfw . ',"st":[' . $curReels . '],"swi":[],"snm":[],"ssm":[],"sm":[0,0,0,0,0],"acb":0,"rf":0,"as":[{"asi":392042761,"st":[' . $curReels . '],"swm":0,"sw":0,"swu":0,"fsw":0,"tw":0}],"sp":0,"cr":"USD","rd":0,"pbb":0,"obb":0,"mb":false,"pwa":0},"drs":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . '],[' . implode(',', $slotSettings->reelStrip4) . '],[' . implode(',', $slotSettings->reelStrip5) . ']],"pt":[[0,0,0,0,0],[0,10,400,600,1000],[0,5,250,400,700],[0,0,100,200,500],[0,0,50,100,400],[0,0,25,50,200],[0,0,20,40,150],[0,0,15,30,100],[0,0,15,30,100],[0,0,10,20,50],[0,0,10,20,50],[0,0,10,15,35],[0,0,2,3,4]],"cps":[1,2,3,4,5,7,8,10,15,20,25,30,50,75,100],"e":false,"ec":0,"cc":""},"un":"accessKey|USD|887553269","bl":' . $balanceInCents . ',"gn":"ImperialGirls","lgn":"Imperial Girls","gv":0,"fs":false,"si":"71564036dec148e68115c0df1498f606","dn":[0.01,0.05,0.1,0.25,0.5,1.0,2.0],"cs":"$","cd":2,"cp":false,"gs":",","ds":".","ase":[5],"gm":0,"mi":-1,"cud":0.01,"cup":[' . implode(',', $gameBets) . '],"mm":0,"e":false,"ec":0,"cc":"EN"}';
                                break;
                            case 'ping':
                                $result_tmp[0] = '{"v":' . $balanceInCents . ',"e":false,"ec":0,"cc":"EN"}';
                                break;
                            case 'spin':
                                $lines = $postData['sel'];
                                $betline = $postData['cps'] / 100;
                                $allbet = $betline * 50;
                                $postData['slotEvent'] = 'bet';
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $allbet <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
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
                                    $slotSettings->UpdateJackpots($allbet);
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
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $linesId = $slotSettings->WaysToLines();
                                $lines_ = count($linesId);
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [];
                                    $cWins = [];
                                    $cWinsCount = [];
                                    $cWinsMpl = [];
                                    $wild = ['0'];
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $k = 0;
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = (string)$slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $isSeq = true;
                                            $wlines = [];
                                            $reels_t = [];
                                            $reels_t['reel1'] = [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $reels_t['reel2'] = [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $reels_t['reel3'] = [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $reels_t['reel4'] = [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $reels_t['reel5'] = [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $reelWayMpl = [
                                                0, 
                                                0, 
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ];
                                            $isWild2 = false;
                                            $isWild4 = false;
                                            for( $rl = 1; $rl <= 5; $rl++ ) 
                                            {
                                                $isSeq = false;
                                                for( $rs = 0; $rs <= 3; $rs++ ) 
                                                {
                                                    if( $rl == 2 && in_array($reels['reel' . $rl][$rs], $wild) ) 
                                                    {
                                                        $isWild2 = true;
                                                    }
                                                    if( $rl == 4 && in_array($reels['reel' . $rl][$rs], $wild) ) 
                                                    {
                                                        $isWild4 = true;
                                                    }
                                                    if( $reels['reel' . $rl][$rs] == $csym || in_array($reels['reel' . $rl][$rs], $wild) ) 
                                                    {
                                                        $reels_t['reel' . $rl][$rs] = -1;
                                                        $reelWayMpl[$rl]++;
                                                        $isSeq = true;
                                                    }
                                                }
                                                if( !$isSeq ) 
                                                {
                                                    break;
                                                }
                                            }
                                            $cWin = 0;
                                            $wwMpl = 1;
                                            if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 ) 
                                            {
                                                $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2];
                                                $cWinsCount[$k] = $slotSettings->GetSymPositions($reels_t);
                                                $wwMpl = $reelWayMpl[1] * $reelWayMpl[2];
                                            }
                                            if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 ) 
                                            {
                                                $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3];
                                                $cWinsCount[$k] = $slotSettings->GetSymPositions($reels_t);
                                                $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3];
                                            }
                                            if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 && $reelWayMpl[4] > 0 ) 
                                            {
                                                $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4];
                                                $cWinsCount[$k] = $slotSettings->GetSymPositions($reels_t);
                                                $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4];
                                            }
                                            if( $reelWayMpl[1] > 0 && $reelWayMpl[2] > 0 && $reelWayMpl[3] > 0 && $reelWayMpl[4] > 0 && $reelWayMpl[5] > 0 ) 
                                            {
                                                $cWin = ($tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $bonusMpl) * $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4] * $reelWayMpl[5];
                                                $cWinsCount[$k] = $slotSettings->GetSymPositions($reels_t);
                                                $wwMpl = $reelWayMpl[1] * $reelWayMpl[2] * $reelWayMpl[3] * $reelWayMpl[4] * $reelWayMpl[5];
                                            }
                                            if( $postData['slotEvent'] == 'freespin' ) 
                                            {
                                                if( $isWild2 ) 
                                                {
                                                    $bonusMpl = 3;
                                                }
                                                if( $isWild4 ) 
                                                {
                                                    $bonusMpl = 6;
                                                }
                                                if( $isWild2 && $isWild4 ) 
                                                {
                                                    $bonusMpl = 8;
                                                }
                                            }
                                            if( $cWin > 0 ) 
                                            {
                                                if( $wwMpl == 0 ) 
                                                {
                                                    $wwMpl = 1;
                                                }
                                                $cWinsMpl[$k] = $bonusMpl;
                                                $cWins[$k] = $cWin / $bonusMpl;
                                                $totalWin += $cWin;
                                                $k++;
                                            }
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
                                    $swm_ = [
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    for( $p = 3; $p >= 0; $p-- ) 
                                    {
                                        if( $reels['reel1'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 1;
                                        }
                                        if( $reels['reel2'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 2;
                                        }
                                        if( $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 4;
                                        }
                                        if( $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 8;
                                        }
                                        if( $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 16;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 3;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 5;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 6;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 9;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 10;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 12;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 17;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 18;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 20;
                                        }
                                        if( $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 24;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 7;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 11;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 13;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 14;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 19;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 21;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 22;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 25;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 26;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 28;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 27;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 29;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 30;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 23;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 15;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 31;
                                        }
                                    }
                                    for( $rl = 1; $rl <= 5; $rl++ ) 
                                    {
                                        for( $rs = 0; $rs <= 3; $rs++ ) 
                                        {
                                            if( $reels['reel' . $rl][$rs] == $scatter ) 
                                            {
                                                $scattersCount++;
                                            }
                                        }
                                    }
                                    for( $rl = 1; $rl <= 5; $rl++ ) 
                                    {
                                        if( $reels['reel' . $rl][0] == '4' || $reels['reel' . $rl][1] == '4' || $reels['reel' . $rl][2] == '4' || $reels['reel' . $rl][3] == '4' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                        else
                                        {
                                            break;
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
                                        else if( $scattersCount >= 3 && $scattersCount2 >= 5 ) 
                                        {
                                        }
                                        else if( ($scattersCount >= 3 || $scattersCount2 >= 5) && $winType != 'bonus' ) 
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
                                if( $scattersCount >= 3 ) 
                                {
                                    $swm = $swm_[3] + (32 * $swm_[2]) + (1024 * $swm_[1]) + (32768 * $swm_[0]);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $swu = 1;
                                }
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $curReels = '';
                                $curReels .= ($reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . ',' . $reels['reel4'][3] . ',' . $reels['reel5'][3]);
                                $curReels .= (',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2]);
                                $curReels .= (',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1]);
                                $curReels .= (',' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0]);
                                $acb = 0;
                                $fs_ = 'false';
                                if( $winType == 'bonus' ) 
                                {
                                    $acb = 1;
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $fs_ = 'true';
                                    $acb = 1;
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin'));
                                    }
                                }
                                $cWinsStr = [];
                                for( $ww = 0; $ww < count($cWins); $ww++ ) 
                                {
                                    $cWinsStr[$ww] = $cWins[$ww] * 100;
                                }
                                $balanceInCents2 = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $pickBonusData = '';
                                if( $scattersCount2 >= 5 ) 
                                {
                                    $acb = 2;
                                    $pickBonusData = ',"bpd":{"c":false,"s":false,"lt":0,"np":4,"msb":4,"w":0,"lpw":0,"nfw":0,"m":0,"mk":0,"m2":0,"m3":0,"csw":0}';
                                    $PickBonusWins = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3, 
                                        4, 
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        10, 
                                        10, 
                                        10, 
                                        10, 
                                        15, 
                                        15, 
                                        15, 
                                        20, 
                                        20, 
                                        20, 
                                        25, 
                                        25, 
                                        50, 
                                        50, 
                                        100, 
                                        100
                                    ];
                                    $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    for( $bs = 0; $bs <= 2000; $bs++ ) 
                                    {
                                        $totalPickWin = 0;
                                        $PickBonusValue = [
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        shuffle($PickBonusWins);
                                        for( $bc = 0; $bc < 1; $bc++ ) 
                                        {
                                            $curPickWin = $PickBonusWins[$bc] * $betline;
                                            $totalPickWin += $curPickWin;
                                            $PickBonusValue[$bc] = $curPickWin;
                                        }
                                        if( $totalPickWin <= $bank ) 
                                        {
                                            break;
                                        }
                                    }
                                    if( $totalPickWin > 0 ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalPickWin);
                                        $slotSettings->SetBalance($totalPickWin);
                                    }
                                    $PickBonusResult = [
                                        -1, 
                                        -1, 
                                        -1, 
                                        -1, 
                                        -1
                                    ];
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusCount', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusValue', $PickBonusValue);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusResult', $PickBonusResult);
                                    $slotSettings->SaveLogReport($response, 0, 0, $totalPickWin, $postData['slotEvent']);
                                }
                                $result_tmp[0] = '{"sid":' . rand(0, 9999999) . ',"md":{"sid":' . rand(0, 9999999) . ',"tid":"f1fe09a17cf24a35a7c300e6b2d77eec","sel":' . $lines . ',"cps":5,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":' . $fs_ . ',"twg":' . (25 * $betline * 100) . ',"swm":' . $swm . ',"sw":' . ($scattersWin * 100) . ',"swu":' . $swu . ',"tw":' . ($totalWin * 100) . ',"fsw":' . $fs . ',"fsr":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"tfw":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"st":[' . $curReels . '],"swi":[' . implode(',', $cWinsStr) . '],"snm":[' . implode(',', $cWinsMpl) . '],"ssm":[' . implode(',', $cWinsCount) . ']' . $pickBonusData . ',"sm":[1],"acb":' . $acb . ',"rf":0,"sp":1,"cr":"USD","sessionId":"ac7df4a24f4f441896ec13a994b7b178","rd":0,"pbb":' . $balanceInCents . ',"obb":' . $balanceInCents2 . ',"mb":false,"pwa":0,"pac":{}},"pcr":' . $balanceInCents . ',"cr":' . $balanceInCents2 . ',"xp":0,"lvl":{"lvl":1,"xp":0,"bc":0,"cps":5,"sc":200,"wb":0},"dl":0,"cps":[1,2,3,5,10,15,20,25,30,50,75,100,150,250,500],"e":false,"ec":0,"cc":"EN"}';
                                $PickAnswer = '{"md":{"sid":2371159497,"tid":"6e60cdc944754acba254b058afafb17d","sel":' . $lines . ',"cps":5,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":false,"twg":' . (25 * $betline * 100) . ',"swm":' . $swm . ',"sw":' . ($scattersWin * 100) . ',"swu":' . $swu . ',"tw":' . ($totalWin * 100) . ',"fsw":' . $fs . ',"fsr":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"tfw":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"st":[' . $curReels . '],"swi":[' . implode(',', $cWinsStr) . '],"snm":[' . implode(',', $cWinsMpl) . '],"ssm":[' . implode(',', $cWinsCount) . '],"bpd":{"c":false,"s":true,"lt":0,"np":4,"msb":4,"w":0,"lpw":0,"nfw":0,"m":0,"mk":3,"m2":0,"m3":0,"rv":[-1,-1,-1,-1,-1],"csw":0},"acb":2,"rf":0,"as":[],"sp":40,"cr":"USD","sessionId":"20685ede9e4a4c91ab030dec86cca651","rd":0,"pbb":' . $balanceInCents . ',"obb":10002743,"mb":false,"pwa":0,"pac":{}},"p":0,"cr":10002743,"cps":[1,2,3,5,10,15,20,25,30,50,75,100,150,250,500],"e":false,"ec":0,"cc":"EN"}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickAnswer', $PickAnswer);
                                break;
                            case 'bonusPick':
                                $PickBonusCount = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusCount');
                                $PickBonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusWin');
                                $PickBonusValue = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusValue');
                                $PickBonusResult = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusResult');
                                $PickAnswer = json_decode($slotSettings->GetGameData($slotSettings->slotId . 'PickAnswer'), true);
                                $CurWin = $PickBonusValue[$PickBonusCount] * 100;
                                $PickBonusWin += $CurWin;
                                $PickBonusResult[$PickBonusCount] = $CurWin;
                                $c = false;
                                if( $PickBonusCount == 0 ) 
                                {
                                    $c = true;
                                    $pbw = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3, 
                                        4, 
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        10, 
                                        10, 
                                        10, 
                                        10, 
                                        15, 
                                        15, 
                                        15, 
                                        20, 
                                        20, 
                                        20, 
                                        25, 
                                        25, 
                                        50, 
                                        50, 
                                        100, 
                                        100
                                    ];
                                    shuffle($pbw);
                                    $PickBonusResult[0] = $pbw[0] * 100;
                                    $PickBonusResult[1] = $pbw[1] * 100;
                                    $PickBonusResult[2] = $pbw[2] * 100;
                                    $PickBonusResult[3] = $pbw[3] * 100;
                                    $PickBonusResult[4] = $pbw[4] * 100;
                                    $PickBonusResult[5] = $pbw[5] * 100;
                                    $PickBonusResult[$postData['p']] = $CurWin;
                                }
                                $mk = [
                                    1, 
                                    3, 
                                    7, 
                                    15
                                ];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $PickAnswer['md']['obb'] = $balanceInCents;
                                $PickAnswer['cr'] = $balanceInCents;
                                $PickAnswer['md']['bpd']['mk'] = $mk[$PickBonusCount];
                                $PickAnswer['md']['bpd']['c'] = $c;
                                $PickAnswer['md']['bpd']['s'] = true;
                                $PickAnswer['md']['bpd']['w'] = $PickBonusWin;
                                $PickAnswer['md']['bpd']['lpw'] = $CurWin;
                                $PickAnswer['md']['bpd']['rv'] = $PickBonusResult;
                                $result_tmp[0] = json_encode($PickAnswer);
                                $PickBonusCount++;
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusCount', $PickBonusCount);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusWin', $PickBonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusValue', $PickBonusValue);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusResult', $PickBonusResult);
                                break;
                            case 'updatePlayerInfo':
                                $result_tmp[0] = '{"e":false,"ec":0,"cc":"EN"}';
                                break;
                        }
                        if( !isset($result_tmp[0]) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"Invalid request state"}';
                            exit( $response );
                        }
                        $response = $result_tmp[0];
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
