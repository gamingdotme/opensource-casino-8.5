<?php 
namespace VanguardLTE\Games\Dragons888PM
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
                        $response = 'balance=' . $Balance . '&cfgs=1&reel1=' . implode(',', $slotSettings->reelStrip2) . '&ver=2&reel0=' . implode(',', $slotSettings->reelStrip1) . '&index=1&balance_cash=' . $Balance . '&reel2=' . implode(',', $slotSettings->reelStrip3) . '&balance_bonus=0.00&na=s&scatters=1~0,0,0~0,0,0~1,1,1&gmb=0,0,0&rt=d&stime=' . time() . '&sa=1,1,1&sb=6,5,4&sc=' . implode(',', $slotSettings->Bet) . '&defc=1&sh=3&wilds=2~0,0,0~1,1,1&bonuses=0&fsbonus=&c=' . $slotSettings->Bet[0] . '&sver=5&counter=2&paytable=0,0,0;0,0,0;0,0,0;100,0,0;50,0,0;25,0,0;0,0,0&l=1&rtp=96.84&s=' . $rp2;
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = $slotSettings->GetGameData('Dragons888PMDealerCard');
                        $totalWin = $slotSettings->GetGameData('Dragons888PMTotalWin');
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
                        $slotSettings->SetGameData('Dragons888PMTotalWin', $totalWin);
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
                        $slotSettings->SetGameData('Dragons888PMDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('Dragons888PMTotalWin');
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
                        $slotSettings->SetGameData('Dragons888PMTotalWin', $totalWin);
                        $slotSettings->SetBalance($gambleWin);
                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                        $afterBalance = $slotSettings->GetBalance();
                        $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                        $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                        $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                    }
                    else if( $postData['slotEvent'] == 'doSpin' ) 
                    {
                        $linesId = [];
                        $linesId[0] = [
                            1, 
                            1, 
                            1, 
                            1, 
                            1
                        ];
                        $postData['slotBet'] = $postData['c'];
                        $postData['slotLines'] = 1;
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
                            $slotSettings->SetGameData('Dragons888PMBonusWin', 0);
                            $slotSettings->SetGameData('Dragons888PMFreeGames', 0);
                            $slotSettings->SetGameData('Dragons888PMCurrentFreeGame', 0);
                            $slotSettings->SetGameData('Dragons888PMTotalWin', 0);
                            $slotSettings->SetGameData('Dragons888PMFreeBalance', 0);
                        }
                        else
                        {
                            $slotSettings->SetGameData('Dragons888PMCurrentFreeGame', $slotSettings->GetGameData('Dragons888PMCurrentFreeGame') + 1);
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
                            $wild = ['NONE'];
                            $scatter = 'NONE';
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
                                        $s[0] = $reels['reel1'][1];
                                        $s[1] = $reels['reel2'][1];
                                        $s[2] = $reels['reel3'][1];
                                        if( $s[0] == $csym && $s[1] == $csym && $s[2] == $csym ) 
                                        {
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"reward":"line","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":1,"paytable":[]}';
                                            }
                                        }
                                        if( $s[0] != '6' && $s[1] != '6' && $s[2] != '6' ) 
                                        {
                                            $tmpWin = $slotSettings->Paytable[7][3] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"reward":"line","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":1,"paytable":[3,2]}';
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
                            $slotSettings->SetGameData('Dragons888PMBonusWin', $slotSettings->GetGameData('Dragons888PMBonusWin') + $totalWin);
                            $slotSettings->SetGameData('Dragons888PMTotalWin', $slotSettings->GetGameData('Dragons888PMTotalWin') + $totalWin);
                            $totalWin = $slotSettings->GetGameData('Dragons888PMBonusWin');
                            $Balance = $slotSettings->GetGameData('Dragons888PMFreeBalance');
                        }
                        else
                        {
                            $slotSettings->SetGameData('Dragons888PMTotalWin', $totalWin);
                        }
                        if( $scattersCount >= 3 ) 
                        {
                            if( $slotSettings->GetGameData('Dragons888PMFreeGames') > 0 ) 
                            {
                                $slotSettings->SetGameData('Dragons888PMFreeBalance', $Balance);
                                $slotSettings->SetGameData('Dragons888PMBonusWin', $totalWin);
                                $slotSettings->SetGameData('Dragons888PMFreeGames', $slotSettings->GetGameData('Dragons888PMFreeGames') + $slotSettings->slotFreeCount);
                            }
                            else
                            {
                                $slotSettings->SetGameData('Dragons888PMFreeBalance', $Balance);
                                $slotSettings->SetGameData('Dragons888PMBonusWin', $totalWin);
                                $slotSettings->SetGameData('Dragons888PMFreeGames', $slotSettings->slotFreeCount);
                            }
                        }
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString = implode(',', $lineWins);
                        $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('Dragons888PMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('Dragons888PMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                        $response = 'tw=' . $totalWin . '&balance=' . $slotSettings->GetBalance() . '&index=2&balance_cash=' . $slotSettings->GetBalance() . '&balance_bonus=0.00&na=s&stime=' . time() . '&sa=' . $reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . '&sb=' . $reels['reel1'][-1] . ',' . $reels['reel2'][-1] . ',' . $reels['reel3'][-1] . '&sh=3&c=0.01&sver=5&counter=4&l=1&s=' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . '&w=' . $totalWin . '';
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
