<?php 
namespace VanguardLTE\Games\AztecGemsPM
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
        {
            \DB::transaction(function() use ($request, $game)
            {
                try
                {
                    $response = '';
                    $userId = \Auth::id();
                    if( $userId == null ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid login"}';
                        exit( $response );
                    }
                    $slotSettings = new SlotSettings($game, $userId);
                    $postData = trim(file_get_contents('php://input'));
                    $tmpPar = explode('&', $postData);
                    $postData = [];
                    foreach( $tmpPar as $par ) 
                    {
                        $tmpPar2 = explode('=', $par);
                        $postData[$tmpPar2[0]] = $tmpPar2[1];
                    }
                    if( !isset($postData['action']) ) 
                    {
                        exit( '' );
                    }
                    $postData['slotEvent'] = $postData['action'];
                    if( $postData['slotEvent'] == 'update' ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                        exit( $response );
                    }
                    if( $postData['slotEvent'] == 'doInit' ) 
                    {
                        $lastEvent = $slotSettings->GetHistory();
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
                            $lastEvent->serverResponse->reelsSymbols->reel1 = (array)$lastEvent->serverResponse->reelsSymbols->reel1;
                            $lastEvent->serverResponse->reelsSymbols->reel2 = (array)$lastEvent->serverResponse->reelsSymbols->reel2;
                            $lastEvent->serverResponse->reelsSymbols->reel3 = (array)$lastEvent->serverResponse->reelsSymbols->reel3;
                            $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                            $rp2 = '' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . '';
                            $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . '');
                            $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . '');
                            $bet = $lastEvent->serverResponse->bet;
                        }
                        else
                        {
                            $rp1 = implode(',', [
                                rand(0, count($slotSettings->reelStrip1) - 3), 
                                rand(0, count($slotSettings->reelStrip2) - 3), 
                                rand(0, count($slotSettings->reelStrip3) - 3)
                            ]);
                            $rp_1 = rand(0, count($slotSettings->reelStrip1) - 3);
                            $rp_2 = rand(0, count($slotSettings->reelStrip2) - 3);
                            $rp_3 = rand(0, count($slotSettings->reelStrip3) - 3);
                            $rr1 = $slotSettings->reelStrip1[$rp_1];
                            $rr2 = $slotSettings->reelStrip2[$rp_2];
                            $rr3 = $slotSettings->reelStrip3[$rp_3];
                            $rp2 = '' . $rr1 . ',' . $rr2 . ',' . $rr3 . '';
                            $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                            $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                            $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                            $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . '');
                            $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                            $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                            $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                            $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . '');
                            $bet = $slotSettings->Bet[0];
                        }
                        $jsSet = json_encode($slotSettings);
                        $lang = json_encode(\Lang::get('games.' . $game));
                        $Balance = $slotSettings->GetBalance();
                        $response = 'def_s=7,4,9,7,4,9,7,4,9&balance=' . $Balance . '&cfgs=1&reel1=' . implode(',', $slotSettings->reelStrip2) . '&ver=2&reel0=' . implode(',', $slotSettings->reelStrip1) . '&index=1&balance_cash=' . $Balance . '&def_sb=4,4,4&def_sa=8,8,8&reel2=' . implode(',', $slotSettings->reelStrip3) . '&balance_bonus=0.00&na=s&aw=3&scatters=1~0,0,0~0,0,0~1,1,1&gmb=0,0,0&rt=d&base_aw=m~1;m~2;m~3;m~5;m~10;m~15&stime=' . floor(microtime(true) * 1000) . '&sa=8,8,8&sb=4,4,4&sc=' . implode(',', $slotSettings->Bet) . '&defc=' . $slotSettings->Bet[0] . '&def_aw=3&sh=3&wilds=2~25,0,0~1,1,1&bonuses=0&fsbonus=&c=' . $slotSettings->Bet[0] . '&sver=5&counter=2&paytable=0,0,0;0,0,0;0,0,0;20,0,0;15,0,0;12,0,0;10,0,0;8,0,0;5,0,0;2,0,0&l=5&rtp=96.52&s=' . $rp2 . '&awt=6rl';
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = $slotSettings->GetGameData('AztecGemsPMDealerCard');
                        $totalWin = $slotSettings->GetGameData('AztecGemsPMTotalWin');
                        $gambleWin = 0;
                        $gambleChoice = $postData['gambleChoice'] - 2;
                        $gambleState = '';
                        $gambleCards = [
                            2, 
                            3, 
                            4, 
                            5, 
                            6, 
                            7, 
                            8, 
                            9, 
                            10, 
                            11, 
                            12, 
                            13, 
                            14
                        ];
                        $gambleSuits = [
                            'C', 
                            'S', 
                            'D', 
                            'H'
                        ];
                        $gambleId = [
                            '', 
                            '', 
                            '2', 
                            '3', 
                            '4', 
                            '5', 
                            '6', 
                            '7', 
                            '8', 
                            '9', 
                            '10', 
                            'J', 
                            'Q', 
                            'K', 
                            'A'
                        ];
                        $userCard = 0;
                        if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                        {
                            $isGambleWin = 0;
                        }
                        if( $isGambleWin == 1 ) 
                        {
                            $userCard = rand($dealerCard, 14);
                        }
                        else
                        {
                            $userCard = rand(2, $dealerCard);
                        }
                        if( $dealerCard < $userCard ) 
                        {
                            $gambleWin = $totalWin;
                            $totalWin = $totalWin * 2;
                            $gambleState = 'win';
                        }
                        else if( $userCard < $dealerCard ) 
                        {
                            $gambleWin = -1 * $totalWin;
                            $totalWin = 0;
                            $gambleState = 'lose';
                        }
                        else
                        {
                            $gambleWin = $totalWin;
                            $totalWin = $totalWin;
                            $gambleState = 'draw';
                        }
                        if( $gambleWin != $totalWin ) 
                        {
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                        }
                        $afterBalance = $slotSettings->GetBalance();
                        $userCards = [
                            rand(2, 14), 
                            rand(2, 14), 
                            rand(2, 14), 
                            rand(2, 14)
                        ];
                        $userCards[$gambleChoice] = $userCard;
                        for( $i = 0; $i < 4; $i++ ) 
                        {
                            $userCards[$i] = '"' . $gambleId[$userCards[$i]] . $gambleSuits[rand(0, 3)] . '"';
                        }
                        $userCardsStr = implode(',', $userCards);
                        $slotSettings->SetGameData('AztecGemsPMTotalWin', $totalWin);
                        $jsSet = '{"dealerCard":"' . $dealerCard . '","playerCards":[' . $userCardsStr . '],"gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                        $response = '{"responseEvent":"gambleResult","deb":' . $userCards[$gambleChoice] . ',"serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetDealerCard' ) 
                    {
                        $gambleCards = [
                            2, 
                            3, 
                            4, 
                            5, 
                            6, 
                            7, 
                            8, 
                            9, 
                            10, 
                            11, 
                            12, 
                            13, 
                            14
                        ];
                        $gambleId = [
                            '', 
                            '', 
                            '2', 
                            '3', 
                            '4', 
                            '5', 
                            '6', 
                            '7', 
                            '8', 
                            '9', 
                            '10', 
                            'J', 
                            'Q', 
                            'K', 
                            'A'
                        ];
                        $gambleSuits = [
                            'C', 
                            'S', 
                            'D', 
                            'H'
                        ];
                        $tmpDc = $gambleCards[rand(0, 12)];
                        $slotSettings->SetGameData('AztecGemsPMDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('AztecGemsPMTotalWin');
                        $gambleWin = 0;
                        $statBet = $totalWin;
                        if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                        {
                            $isGambleWin = 0;
                        }
                        if( $isGambleWin == 1 ) 
                        {
                            $gambleState = 'win';
                            $gambleWin = $totalWin;
                            $totalWin = $totalWin * 2;
                            if( $postData['gambleChoice'] == 'red' ) 
                            {
                                $tmpCards = [
                                    'D', 
                                    'H'
                                ];
                                $dealerCard = $tmpCards[rand(0, 1)];
                            }
                            else
                            {
                                $tmpCards = [
                                    'C', 
                                    'S'
                                ];
                                $dealerCard = $tmpCards[rand(0, 1)];
                            }
                        }
                        else
                        {
                            $gambleState = 'lose';
                            $gambleWin = -1 * $totalWin;
                            $totalWin = 0;
                            if( $postData['gambleChoice'] == 'red' ) 
                            {
                                $tmpCards = [
                                    'C', 
                                    'S'
                                ];
                                $dealerCard = $tmpCards[rand(0, 1)];
                            }
                            else
                            {
                                $tmpCards = [
                                    'D', 
                                    'H'
                                ];
                                $dealerCard = $tmpCards[rand(0, 1)];
                            }
                        }
                        $slotSettings->SetGameData('AztecGemsPMTotalWin', $totalWin);
                        $slotSettings->SetBalance($gambleWin);
                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                        $afterBalance = $slotSettings->GetBalance();
                        $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                        $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                        $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                    }
                    else if( $postData['slotEvent'] == 'doCollect' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $response = 'balance=' . $Balance . '&index=5&balance_cash=' . $Balance . '&balance_bonus=0.00&na=s&stime=' . floor(microtime(true) * 1000) . '&sver=5&counter=10';
                    }
                    else if( $postData['slotEvent'] == 'doSpin' ) 
                    {
                        $linesId = [];
                        $linesId[0] = [
                            2, 
                            2, 
                            2
                        ];
                        $linesId[1] = [
                            1, 
                            1, 
                            1
                        ];
                        $linesId[2] = [
                            3, 
                            3, 
                            3
                        ];
                        $linesId[3] = [
                            1, 
                            2, 
                            3
                        ];
                        $linesId[4] = [
                            3, 
                            2, 
                            1
                        ];
                        $psArr = [];
                        $psArr[0] = [
                            0, 
                            3, 
                            6
                        ];
                        $psArr[1] = [
                            1, 
                            4, 
                            7
                        ];
                        $psArr[2] = [
                            2, 
                            5, 
                            8
                        ];
                        $postData['slotBet'] = $postData['c'];
                        $postData['slotLines'] = 5;
                        if( $postData['slotEvent'] == 'doSpin' ) 
                        {
                            $lines = $postData['slotBet'];
                            $betline = $postData['slotLines'];
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
                        $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                        $winType = $winTypeTmp[0];
                        $spinWinLimit = $winTypeTmp[1];
                        if( $postData['slotEvent'] != 'freespin' ) 
                        {
                            if( !isset($postData['slotEvent']) ) 
                            {
                                $postData['slotEvent'] = 'bet';
                            }
                            $slotSettings->SetBalance(-1 * ($postData['slotBet'] * $postData['slotLines']), $postData['slotEvent']);
                            $bankSum = ($postData['slotBet'] * $postData['slotLines']) / 100 * $slotSettings->GetPercent();
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                            $bonusMpl = 1;
                            $slotSettings->SetGameData('AztecGemsPMBonusWin', 0);
                            $slotSettings->SetGameData('AztecGemsPMFreeGames', 0);
                            $slotSettings->SetGameData('AztecGemsPMCurrentFreeGame', 0);
                            $slotSettings->SetGameData('AztecGemsPMTotalWin', 0);
                            $slotSettings->SetGameData('AztecGemsPMFreeBalance', 0);
                        }
                        else
                        {
                            $slotSettings->SetGameData('AztecGemsPMCurrentFreeGame', $slotSettings->GetGameData('AztecGemsPMCurrentFreeGame') + 1);
                            $bonusMpl = $slotSettings->slotFreeMpl;
                        }
                        $Balance = $slotSettings->GetBalance();
                        if( $postData['slotEvent'] != 'freespin' ) 
                        {
                            $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines']);
                        }
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
                                0
                            ];
                            $wild = '2';
                            $scatter = 'NONE';
                            $mplReel = [
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
                                3, 
                                5, 
                                5, 
                                5, 
                                10, 
                                10, 
                                15, 
                                15
                            ];
                            shuffle($mplReel);
                            $bonusMpl = $mplReel[0];
                            $ln = 0;
                            $reels = $slotSettings->GetReelStrips($winType);
                            for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                            {
                                $tmpStringWin = '';
                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                {
                                    $csym = $slotSettings->SymbolGame[$j];
                                    if( $csym == $scatter || !isset($slotSettings->Paytable[$csym]) ) 
                                    {
                                    }
                                    else
                                    {
                                        $s = [];
                                        $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                        $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                        $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                        if( ($s[0] == $csym || $wild == $s[0]) && ($s[1] == $csym || $wild == $s[1]) && ($s[2] == $csym || $wild == $s[2]) ) 
                                        {
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $psym1 = $psArr[0][$linesId[$k][0] - 1];
                                                $psym2 = $psArr[1][$linesId[$k][1] - 1];
                                                $psym3 = $psArr[2][$linesId[$k][2] - 1];
                                                $tmpStringWin = 'l' . $ln . '=' . $k . '~' . $cWins[$k] . '~' . $psym1 . '~' . $psym2 . '~' . $psym3 . '';
                                                $ln++;
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
                            for( $r = 1; $r <= 3; $r++ ) 
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
                                if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['slotBet'] * $postData['slotLines']) ) 
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
                            $slotSettings->SetBalance($totalWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                        }
                        $reportWin = $totalWin;
                        if( $postData['slotEvent'] == 'freespin' ) 
                        {
                            $slotSettings->SetGameData('AztecGemsPMBonusWin', $slotSettings->GetGameData('AztecGemsPMBonusWin') + $totalWin);
                            $slotSettings->SetGameData('AztecGemsPMTotalWin', $slotSettings->GetGameData('AztecGemsPMTotalWin') + $totalWin);
                            $totalWin = $slotSettings->GetGameData('AztecGemsPMBonusWin');
                            $Balance = $slotSettings->GetGameData('AztecGemsPMFreeBalance');
                        }
                        else
                        {
                            $slotSettings->SetGameData('AztecGemsPMTotalWin', $totalWin);
                        }
                        if( $scattersCount >= 3 ) 
                        {
                            if( $slotSettings->GetGameData('AztecGemsPMFreeGames') > 0 ) 
                            {
                                $slotSettings->SetGameData('AztecGemsPMFreeBalance', $Balance);
                                $slotSettings->SetGameData('AztecGemsPMBonusWin', $totalWin);
                                $slotSettings->SetGameData('AztecGemsPMFreeGames', $slotSettings->GetGameData('AztecGemsPMFreeGames') + $slotSettings->slotFreeCount);
                            }
                            else
                            {
                                $slotSettings->SetGameData('AztecGemsPMFreeBalance', $Balance);
                                $slotSettings->SetGameData('AztecGemsPMBonusWin', $totalWin);
                                $slotSettings->SetGameData('AztecGemsPMFreeGames', $slotSettings->slotFreeCount);
                            }
                        }
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString = '&' . implode('&', $lineWins);
                        $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('AztecGemsPMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AztecGemsPMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                        if( $bonusMpl == 1 ) 
                        {
                            $gmpl = 0;
                        }
                        if( $bonusMpl == 2 ) 
                        {
                            $gmpl = 1;
                        }
                        if( $bonusMpl == 3 ) 
                        {
                            $gmpl = 2;
                        }
                        if( $bonusMpl == 5 ) 
                        {
                            $gmpl = 3;
                        }
                        if( $bonusMpl == 10 ) 
                        {
                            $gmpl = 4;
                        }
                        if( $bonusMpl == 15 ) 
                        {
                            $gmpl = 5;
                        }
                        $response = 'com=5,5&tw=' . $totalWin . '&balance=' . $slotSettings->GetBalance() . '&index=3&balance_cash=' . $slotSettings->GetBalance() . '&balance_bonus=0.00&na=c&aw=' . $gmpl . '' . $winString . '&stime=' . floor(microtime(true) * 1000) . '&sa=' . $reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . '&sb=' . $reels['reel1'][-1] . ',' . $reels['reel2'][-1] . ',' . $reels['reel3'][-1] . '&sh=3&c=0.01&sver=5&counter=6&l=5&s=' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . '&w=' . $totalWin . '&gwm=' . $bonusMpl . '&awt=6rl';
                        $slotSettings->SaveLogReport($response_log, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
                    }
                    $slotSettings->SaveGameData();
                    $slotSettings->SaveGameDataStatic();
                    echo $response;
                }
                catch( \Exception $e ) 
                {
                    $slotSettings->InternalErrorSilent($e);
                }
            }, 5);
        }
    }

}
