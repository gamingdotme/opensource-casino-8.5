<?php 
namespace VanguardLTE\Games\JokersJewelPM
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
                        $fsInfo = '';
                        $slotSettings->SetGameData('JokersJewelPMBonusWin', 0);
                        $slotSettings->SetGameData('JokersJewelPMFreeGames', 0);
                        $slotSettings->SetGameData('JokersJewelPMCurrentFreeGame', 0);
                        $slotSettings->SetGameData('JokersJewelPMTotalWin', 0);
                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $slotSettings->GetBalance());
                        $slotSettings->SetGameData('JokersJewelPMBonusState', 0);
                        $slotSettings->SetGameData('JokersJewelPMBonusMpl', 0);
                        if( $lastEvent != 'NULL' ) 
                        {
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusMpl', $lastEvent->serverResponse->BonusMpl);
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $fsInfo = '&fs=' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . '&fsmax=' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '&fswin=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&tw=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&fsmul=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusMpl') . '';
                            }
                            $lastEvent->serverResponse->reelsSymbols->reel1 = (array)$lastEvent->serverResponse->reelsSymbols->reel1;
                            $lastEvent->serverResponse->reelsSymbols->reel2 = (array)$lastEvent->serverResponse->reelsSymbols->reel2;
                            $lastEvent->serverResponse->reelsSymbols->reel3 = (array)$lastEvent->serverResponse->reelsSymbols->reel3;
                            $lastEvent->serverResponse->reelsSymbols->reel4 = (array)$lastEvent->serverResponse->reelsSymbols->reel4;
                            $lastEvent->serverResponse->reelsSymbols->reel5 = (array)$lastEvent->serverResponse->reelsSymbols->reel5;
                            $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                            $rp2 = '' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0];
                            $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1]);
                            $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2]);
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
                            $rp_4 = rand(0, count($slotSettings->reelStrip4) - 3);
                            $rp_5 = rand(0, count($slotSettings->reelStrip5) - 3);
                            $rr1 = $slotSettings->reelStrip1[$rp_1];
                            $rr2 = $slotSettings->reelStrip2[$rp_2];
                            $rr3 = $slotSettings->reelStrip3[$rp_3];
                            $rr4 = $slotSettings->reelStrip3[$rp_4];
                            $rr5 = $slotSettings->reelStrip3[$rp_5];
                            $rp2 = $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5;
                            $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                            $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                            $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                            $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5);
                            $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                            $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                            $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                            $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5);
                            $bet = $slotSettings->Bet[0];
                        }
                        $jsSet = json_encode($slotSettings);
                        $lang = json_encode(\Lang::get('games.' . $game));
                        $Balance = $slotSettings->GetBalance();
                        $response = 'def_s=8,7,4,9,8,6,7,4,9,8,3,7,7,6,6&balance=' . $Balance . '&cfgs=1&reel1=' . implode(',', $slotSettings->reelStrip2) . '&ver=2&reel0=' . implode(',', $slotSettings->reelStrip1) . '&index=1&balance_cash=' . $Balance . '&def_sb=9,9,9,5,6&def_sa=8,8,8,7,7&reel3=' . implode(',', $slotSettings->reelStrip4) . '&reel2=' . implode(',', $slotSettings->reelStrip3) . '&reel4=' . implode(',', $slotSettings->reelStrip5) . '&balance_bonus=0.00&na=s&scatters=1~250,50,10,0,0~0,0,0,0,0~1,1,1,1,1&gmb=0,0,0&rt=d&stime=' . floor(microtime(true) * 1000) . '&sa=8,8,8,7,7&sb=9,9,9,5,6&sc=' . implode(',', $slotSettings->Bet) . '&defc=' . $slotSettings->Bet[0] . '&sh=3&wilds=2~0,0,0~1,1,1&bonuses=0&fsbonus=&c=' . $slotSettings->Bet[0] . '&sver=5&counter=2&paytable=0,0,0,0,0;0,0,0,0,0;0,0,0,0,0;5000,1000,100,0,0;1000,200,50,0,0;1000,200,50,0,0;200,50,20,0,0;200,50,20,0,0;200,40,20,0,0;200,40,20,5,0&l=5&rtp=96.50&s=' . $rp2 . '';
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = $slotSettings->GetGameData('JokersJewelPMDealerCard');
                        $totalWin = $slotSettings->GetGameData('JokersJewelPMTotalWin');
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
                        $slotSettings->SetGameData('JokersJewelPMTotalWin', $totalWin);
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
                        $slotSettings->SetGameData('JokersJewelPMDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('JokersJewelPMTotalWin');
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
                        $slotSettings->SetGameData('JokersJewelPMTotalWin', $totalWin);
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
                        $response = 'balance=' . $Balance . '&index=' . $postData['index'] . '&balance_cash=' . $Balance . '&balance_bonus=0.00&na=s&stime=' . floor(microtime(true) * 1000) . '&sver=5&counter=' . ((int)$postData['counter'] + 1);
                    }
                    else if( $postData['slotEvent'] == 'doSpin' ) 
                    {
                        if( $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') < $slotSettings->GetGameData('JokersJewelPMFreeGames') && $slotSettings->GetGameData('JokersJewelPMFreeGames') > 0 ) 
                        {
                            $postData['slotEvent'] = 'freespin';
                        }
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
                        $psArr = [];
                        $psArr[0] = [
                            0, 
                            5, 
                            10
                        ];
                        $psArr[1] = [
                            1, 
                            6, 
                            11
                        ];
                        $psArr[2] = [
                            2, 
                            7, 
                            12
                        ];
                        $psArr[3] = [
                            3, 
                            8, 
                            13
                        ];
                        $psArr[4] = [
                            4, 
                            9, 
                            14
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
                            $slotSettings->SetGameData('JokersJewelPMBonusWin', 0);
                            $slotSettings->SetGameData('JokersJewelPMFreeGames', 0);
                            $slotSettings->SetGameData('JokersJewelPMCurrentFreeGame', 0);
                            $slotSettings->SetGameData('JokersJewelPMTotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $slotSettings->GetBalance());
                            $slotSettings->SetGameData('JokersJewelPMBonusState', 0);
                            $slotSettings->SetGameData('JokersJewelPMBonusMpl', 0);
                        }
                        else
                        {
                            $slotSettings->SetGameData('JokersJewelPMCurrentFreeGame', $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') + 1);
                            $bonusMpl = $slotSettings->GetGameData('JokersJewelPMBonusMpl');
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
                            $wild = '2';
                            $scatter = '1';
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
                                        $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                        $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                        if( ($s[0] == $csym || $wild == $s[0]) && ($s[1] == $csym || $wild == $s[1]) && ($s[2] == $csym || $wild == $s[2]) ) 
                                        {
                                            if( $wild == $s[0] && $wild == $s[1] && $wild == $s[2] ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( $wild == $s[0] || $wild == $s[1] || $wild == $s[2] ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            else
                                            {
                                                $mpl = 1;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $bonusMpl * $mpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $psym1 = $psArr[0][$linesId[$k][0] - 1];
                                                $psym2 = $psArr[1][$linesId[$k][1] - 1];
                                                $psym3 = $psArr[2][$linesId[$k][2] - 1];
                                                $tmpStringWin = 'l' . $ln . '=' . $k . '~' . $cWins[$k] . '~' . $psym1 . '~' . $psym2 . '~' . $psym3 . '';
                                            }
                                        }
                                        if( ($s[0] == $csym || $wild == $s[0]) && ($s[1] == $csym || $wild == $s[1]) && ($s[2] == $csym || $wild == $s[2]) && ($s[3] == $csym || $wild == $s[3]) ) 
                                        {
                                            if( $wild == $s[0] && $wild == $s[1] && $wild == $s[2] && $wild == $s[3] ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( $wild == $s[0] || $wild == $s[1] || $wild == $s[2] || $wild == $s[3] ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            else
                                            {
                                                $mpl = 1;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $bonusMpl * $mpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $psym1 = $psArr[0][$linesId[$k][0] - 1];
                                                $psym2 = $psArr[1][$linesId[$k][1] - 1];
                                                $psym3 = $psArr[2][$linesId[$k][2] - 1];
                                                $psym4 = $psArr[3][$linesId[$k][3] - 1];
                                                $tmpStringWin = 'l' . $ln . '=' . $k . '~' . $cWins[$k] . '~' . $psym1 . '~' . $psym2 . '~' . $psym3 . '~' . $psym4 . '';
                                            }
                                        }
                                        if( ($s[0] == $csym || $wild == $s[0]) && ($s[1] == $csym || $wild == $s[1]) && ($s[2] == $csym || $wild == $s[2]) && ($s[3] == $csym || $wild == $s[3]) && ($s[4] == $csym || $wild == $s[4]) ) 
                                        {
                                            if( $wild == $s[0] && $wild == $s[1] && $wild == $s[2] && $wild == $s[3] && $wild == $s[4] ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( $wild == $s[0] || $wild == $s[1] || $wild == $s[2] || $wild == $s[3] || $wild == $s[4] ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            else
                                            {
                                                $mpl = 1;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $bonusMpl * $mpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $psym1 = $psArr[0][$linesId[$k][0] - 1];
                                                $psym2 = $psArr[1][$linesId[$k][1] - 1];
                                                $psym3 = $psArr[2][$linesId[$k][2] - 1];
                                                $psym4 = $psArr[3][$linesId[$k][3] - 1];
                                                $psym5 = $psArr[4][$linesId[$k][4] - 1];
                                                $tmpStringWin = 'l' . $ln . '=' . $k . '~' . $cWins[$k] . '~' . $psym1 . '~' . $psym2 . '~' . $psym3 . '~' . $psym4 . '~' . $psym5 . '';
                                            }
                                        }
                                    }
                                }
                                if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                {
                                    array_push($lineWins, $tmpStringWin);
                                    $ln++;
                                    $totalWin += $cWins[$k];
                                }
                            }
                            $scattersStr = [];
                            $scattersCount = 0;
                            $winString = '';
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == $scatter ) 
                                    {
                                        $scattersCount++;
                                        $scattersStr[] = $psArr[$r - 1][$p];
                                    }
                                }
                            }
                            if( isset($slotSettings->Paytable[$scatter]) ) 
                            {
                                $scattersWin = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'] * $bonusMpl;
                            }
                            else
                            {
                                $scattersWin = 0;
                            }
                            if( $scattersWin > 0 ) 
                            {
                                $winString .= ('&psym=1~' . $scattersWin . '~' . implode(',', $scattersStr));
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
                                if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['slotBet'] * $postData['slotLines']) ) 
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
                        $spinType = 's';
                        if( $totalWin > 0 ) 
                        {
                            $spinType = 'c';
                            $slotSettings->SetBalance($totalWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                        }
                        $reportWin = $totalWin;
                        if( $postData['slotEvent'] == 'freespin' ) 
                        {
                            $slotSettings->SetGameData('JokersJewelPMBonusWin', $slotSettings->GetGameData('JokersJewelPMBonusWin') + $totalWin);
                            $slotSettings->SetGameData('JokersJewelPMTotalWin', $slotSettings->GetGameData('JokersJewelPMTotalWin') + $totalWin);
                            $spinType = 's';
                            $Balance = $slotSettings->GetGameData('JokersJewelPMFreeBalance');
                            if( $slotSettings->GetGameData('JokersJewelPMFreeGames') <= $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') && $slotSettings->GetGameData('JokersJewelPMFreeGames') > 0 ) 
                            {
                                $spinType = 'c';
                                $winString .= ('&fs_total=' . $slotSettings->GetGameData('JokersJewelPMFreeGames') . '&fsmul_total=' . $slotSettings->GetGameData('JokersJewelPMBonusMpl') . '&fswin_total=' . $slotSettings->GetGameData('JokersJewelPMBonusWin') . '&fsres_total=' . $slotSettings->GetGameData('JokersJewelPMBonusWin') . '');
                            }
                            else
                            {
                                $winString .= ('&fsmul=' . $slotSettings->GetGameData('JokersJewelPMBonusMpl') . '&fsmax=' . $slotSettings->GetGameData('JokersJewelPMFreeGames') . '&fswin=' . $slotSettings->GetGameData('JokersJewelPMTotalWin') . '&fs=' . $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') . '&fsres=' . $slotSettings->GetGameData('JokersJewelPMBonusWin'));
                            }
                            $totalWinRaw = $totalWin / $bonusMpl;
                        }
                        else
                        {
                            $totalWinRaw = $totalWin;
                            $slotSettings->SetGameData('JokersJewelPMTotalWin', $totalWin);
                            $slotSettings->SetGameData('JokersJewelPMBonusWin', $totalWin);
                        }
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString .= ('&' . implode('&', $lineWins));
                        $s = $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2];
                        $response = 'tw=' . $slotSettings->GetGameData('JokersJewelPMBonusWin') . '&bstate=' . $slotSettings->GetGameData('JokersJewelPMBonusState') . '&balance=' . $Balance . '&index=' . $postData['index'] . '&balance_cash=' . $Balance . '&balance_bonus=0.00' . $winString . '&na=' . $spinType . '&stime=' . floor(microtime(true) * 1000) . '&sa=' . $reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . ',' . $reels['reel4'][3] . ',' . $reels['reel5'][3] . '&sb=' . $reels['reel1'][-1] . ',' . $reels['reel2'][-1] . ',' . $reels['reel3'][-1] . ',' . $reels['reel4'][-1] . ',' . $reels['reel5'][-1] . '&sh=3&c=0.01&sver=5&counter=' . ((int)$postData['counter'] + 1) . '&l=25&s=' . $s . '&w=' . $totalWinRaw . '';
                        if( $slotSettings->GetGameData('JokersJewelPMFreeGames') <= $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') && $slotSettings->GetGameData('JokersJewelPMFreeGames') > 0 ) 
                        {
                            $slotSettings->SetGameData('JokersJewelPMTotalWin', 0);
                            $slotSettings->SetGameData('JokersJewelPMBonusWin', 0);
                        }
                        $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusMpl":' . $slotSettings->GetGameData('JokersJewelPMBonusMpl') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('JokersJewelPMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('JokersJewelPMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"bonusWin":' . $slotSettings->GetGameData('JokersJewelPMBonusWin') . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
