<?php 
namespace VanguardLTE\Games\AncientEgyptPM
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
                        $slotSettings->SetGameData('AncientEgyptPMBonusWin', 0);
                        $slotSettings->SetGameData('AncientEgyptPMFreeGames', 0);
                        $slotSettings->SetGameData('AncientEgyptPMCurrentFreeGame', 0);
                        $slotSettings->SetGameData('AncientEgyptPMTotalWin', 0);
                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $slotSettings->GetBalance());
                        $slotSettings->SetGameData('AncientEgyptPMBonusState', 0);
                        $slotSettings->SetGameData('AncientEgyptPMBonusMpl', 0);
                        if( $lastEvent != 'NULL' ) 
                        {
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusMpl', $lastEvent->serverResponse->BonusMpl);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Sym', $lastEvent->serverResponse->Sym);
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
                        $rsp0 = implode(',', $slotSettings->reelStrip1) . '~' . implode(',', $slotSettings->reelStrip2) . '~' . implode(',', $slotSettings->reelStrip3) . '~' . implode(',', $slotSettings->reelStrip4) . '~' . implode(',', $slotSettings->reelStrip5) . '';
                        $rsp1 = implode(',', $slotSettings->reelStripBonus1) . '~' . implode(',', $slotSettings->reelStripBonus2) . '~' . implode(',', $slotSettings->reelStripBonus3) . '~' . implode(',', $slotSettings->reelStripBonus4) . '~' . implode(',', $slotSettings->reelStripBonus5) . '';
                        $response = 'wsc=1~bg~50,10,1,0,0~0,0,0,0,0~fs~50,10,1,0,0~10,10,10,0,0&def_s=5,8,7,9,8,8,7,3,4,4,11,6,8,11,10' . $fsInfo . '&balance=' . $Balance . '&cfgs=1&ver=2&index=1&balance_cash=' . $Balance . '&reel_set_size=2&def_sb=8,8,7,5,9&def_sa=9,1,8,4,10&balance_bonus=0.00&na=s&scatters=&gmb=0,0,0&rt=d&stime=' . floor(microtime(true) * 1000) . '&sa=9,1,8,4,10&sb=8,8,7,5,9&sc=' . implode(',', $slotSettings->Bet) . '&defc=' . $slotSettings->Bet[0] . '&sh=3&wilds=2~0,0,0,0,0~1,1,1,1,1&bonuses=0&fsbonus=&c=' . $slotSettings->Bet[0] . '&sver=5&n_reel_set=0&counter=2&paytable=0,0,0,0,0;0,0,0,0,0;0,0,0,0,0;5000,1000,100,10,0;2000,400,40,5,0;500,100,15,2,0;500,100,15,2,0;150,40,5,0,0;150,40,5,0,0;100,25,5,0,0;100,25,5,0,0;100,25,5,0,0&l=10&rtp=96.13&reel_set0=' . $rsp0 . '&s=' . $rp2 . '&reel_set1=' . $rsp1 . '';
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = $slotSettings->GetGameData('AncientEgyptPMDealerCard');
                        $totalWin = $slotSettings->GetGameData('AncientEgyptPMTotalWin');
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
                        $slotSettings->SetGameData('AncientEgyptPMTotalWin', $totalWin);
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
                        $slotSettings->SetGameData('AncientEgyptPMDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('AncientEgyptPMTotalWin');
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
                        $slotSettings->SetGameData('AncientEgyptPMTotalWin', $totalWin);
                        $slotSettings->SetBalance($gambleWin);
                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                        $afterBalance = $slotSettings->GetBalance();
                        $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                        $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                        $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                    }
                    else if( $postData['slotEvent'] == 'doBonus' ) 
                    {
                        $bstate = $slotSettings->GetGameData('AncientEgyptPMBonusState');
                        $Balance = $slotSettings->GetBalance();
                        if( $bstate == 0 ) 
                        {
                            if( $postData['ind'] == 1 ) 
                            {
                                $postData['ind'] = 0;
                            }
                            if( $postData['ind'] == 0 ) 
                            {
                                $postData['ind'] = 1;
                            }
                            if( $postData['ind'] == 2 ) 
                            {
                                $postData['ind'] = 2;
                            }
                            $curWin = $slotSettings->GetGameData('AncientEgyptPMBonusWin');
                            $curBet = $slotSettings->GetGameData('AncientEgyptPMAllBet');
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            $winsArr = [
                                12, 
                                12, 
                                12, 
                                12
                            ];
                            for( $i = 0; $i <= 200; $i++ ) 
                            {
                                shuffle($winsArr);
                                if( $winsArr[0] > 12 || $winsArr[0] <= 10 && $winsArr[0] * $curBet <= $bank ) 
                                {
                                    break;
                                }
                            }
                            $statusStr = [
                                0, 
                                0, 
                                0
                            ];
                            $winsStr = [
                                $winsArr[1], 
                                $winsArr[2], 
                                $winsArr[3]
                            ];
                            $wmStr = [
                                'w', 
                                'w', 
                                'w'
                            ];
                            if( $winsStr[0] > 10 ) 
                            {
                                $winsStr[0] = 10;
                                $wmStr[1] = 'ms';
                            }
                            if( $winsStr[1] > 10 ) 
                            {
                                $winsStr[1] = 10;
                                $wmStr[0] = 'ms';
                            }
                            if( $winsStr[2] > 10 ) 
                            {
                                $winsStr[2] = 10;
                                $wmStr[2] = 'ms';
                            }
                            $statusStr[$postData['ind']] = 1;
                            $winsStr[$postData['ind']] = $winsArr[0];
                            if( $winsArr[0] <= 10 ) 
                            {
                                $curWin0 = $winsArr[0] * $curBet;
                                $slotSettings->SetBalance($curWin0);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $curWin0);
                                $curWin += $curWin0;
                                $Balance = $slotSettings->GetBalance();
                                $response = 'tw=' . $curWin . '&bgid=0&balance=' . $Balance . '&wins=' . implode(',', $winsStr) . '&coef=' . $curBet . '&level=1&index=10&balance_cash=' . $Balance . '&balance_bonus=0.00&na=cb&status=' . implode(',', $statusStr) . '&rw=' . $curWin0 . '&stime=' . floor(microtime(true) * 1000) . '&bgt=9&lifes=0&wins_mask=' . implode(',', $wmStr) . '&wp=' . $winsArr[0] . '&end=1&sver=5&counter=20';
                            }
                            else
                            {
                                $winsStr[$postData['ind']] = '10';
                                $response = 'tw=' . $curWin . '&fsmul=1&bgid=0&balance=' . $Balance . '&wins=' . implode(',', $winsStr) . '&coef=' . $curBet . '&fsmax=10&level=1&index=83&balance_cash=' . $Balance . '&balance_bonus=0.00&na=m&fswin=0.00&status=' . implode(',', $statusStr) . '&rw=0.00&stime=' . floor(microtime(true) * 1000) . '&fs=1&bgt=9&lifes=0&wins_mask=' . implode(',', $wmStr) . '&wp=0&end=1&fsres=0.00&sver=5&counter=166';
                            }
                            $bstate++;
                        }
                        $slotSettings->SetGameData('AncientEgyptPMBonusState', $bstate);
                    }
                    else if( $postData['slotEvent'] == 'doMysteryScatter' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $rSym = rand(3, 11);
                        $slotSettings->SetGameData('AncientEgyptPMFreeGames', $slotSettings->slotFreeCount);
                        $slotSettings->SetGameData('AncientEgyptPMCurrentFreeGame', 0);
                        $slotSettings->SetGameData('AncientEgyptPMSym', $rSym);
                        $response = 'fsmul=1&balance=' . $Balance . '&fsmax=10&ms=' . $rSym . '&index=84&balance_cash=' . $Balance . '&balance_bonus=0.00&na=s&fswin=0.00&stime=' . floor(microtime(true) * 1000) . '&fs=1&fsres=0.00&sver=5&n_reel_set=0&counter=168';
                    }
                    else if( $postData['slotEvent'] == 'doCollect' || $postData['slotEvent'] == 'doCollectBonus' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $response = 'balance=' . $Balance . '&index=' . $postData['index'] . '&balance_cash=' . $Balance . '&balance_bonus=0.00&na=s&stime=' . floor(microtime(true) * 1000) . '&sver=5&counter=' . ((int)$postData['counter'] + 1);
                    }
                    else if( $postData['slotEvent'] == 'doSpin' ) 
                    {
                        if( $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') < $slotSettings->GetGameData('AncientEgyptPMFreeGames') && $slotSettings->GetGameData('AncientEgyptPMFreeGames') > 0 ) 
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
                        $linesId[5] = [
                            2, 
                            1, 
                            1, 
                            1, 
                            2
                        ];
                        $linesId[6] = [
                            2, 
                            3, 
                            3, 
                            3, 
                            2
                        ];
                        $linesId[7] = [
                            1, 
                            1, 
                            2, 
                            3, 
                            3
                        ];
                        $linesId[8] = [
                            3, 
                            3, 
                            2, 
                            1, 
                            1
                        ];
                        $linesId[9] = [
                            2, 
                            3, 
                            2, 
                            1, 
                            2
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
                        $postData['slotLines'] = 10;
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
                            $slotSettings->SetGameData('AncientEgyptPMBonusWin', 0);
                            $slotSettings->SetGameData('AncientEgyptPMFreeGames', 0);
                            $slotSettings->SetGameData('AncientEgyptPMCurrentFreeGame', 0);
                            $slotSettings->SetGameData('AncientEgyptPMTotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $slotSettings->GetBalance());
                            $slotSettings->SetGameData('AncientEgyptPMBonusState', 0);
                            $slotSettings->SetGameData('AncientEgyptPMBonusMpl', 0);
                            $slotSettings->SetGameData('AncientEgyptPMSym', 0);
                            $slotSettings->SetGameData('AncientEgyptPMAllBet', $postData['slotBet'] * $postData['slotLines']);
                        }
                        else
                        {
                            $slotSettings->SetGameData('AncientEgyptPMCurrentFreeGame', $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') + 1);
                            $bonusMpl = $slotSettings->GetGameData('AncientEgyptPMBonusMpl');
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
                            $wild = '1';
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
                                                $mpl = 0;
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
                                                $mpl = 0;
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
                                                $mpl = 0;
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
                            $scattersStr2 = [];
                            $scattersCount = 0;
                            $scattersCount2 = 0;
                            $winString = '';
                            $reels2 = $reels;
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == $scatter ) 
                                    {
                                        $scattersCount++;
                                        $scattersStr[] = $psArr[$r - 1][$p];
                                    }
                                    if( $reels['reel' . $r][$p] == $slotSettings->GetGameData('AncientEgyptPMSym') ) 
                                    {
                                        $scattersCount2++;
                                        $scattersStr2[] = $psArr[$r - 1][$p];
                                        break;
                                    }
                                }
                            }
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels2['reel' . $r][$p] == $slotSettings->GetGameData('AncientEgyptPMSym') ) 
                                    {
                                        $reels2['reel' . $r][0] = $slotSettings->GetGameData('AncientEgyptPMSym');
                                        $reels2['reel' . $r][1] = $slotSettings->GetGameData('AncientEgyptPMSym');
                                        $reels2['reel' . $r][2] = $slotSettings->GetGameData('AncientEgyptPMSym');
                                        break;
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
                            if( isset($slotSettings->Paytable[$slotSettings->GetGameData('AncientEgyptPMSym')]) && $slotSettings->Paytable[$slotSettings->GetGameData('AncientEgyptPMSym')][$scattersCount2] > 0 ) 
                            {
                                $scattersWin2 = $slotSettings->Paytable['' . $slotSettings->GetGameData('AncientEgyptPMSym')][$scattersCount2] * $postData['slotBet'] * $postData['slotLines'] * $bonusMpl;
                            }
                            else
                            {
                                $scattersWin2 = 0;
                            }
                            if( $scattersWin > 0 ) 
                            {
                                $winString .= ('&psym=1~' . $scattersWin . '~' . implode(',', $scattersStr));
                            }
                            if( $scattersWin2 > 0 ) 
                            {
                                $winString .= ('&psym=' . $slotSettings->GetGameData('AncientEgyptPMSym') . '~' . $scattersWin . '~' . implode(',', $scattersStr));
                                $mes = $reels2['reel1'][0] . ',' . $reels2['reel2'][0] . ',' . $reels2['reel3'][0] . ',' . $reels2['reel4'][0] . ',' . $reels2['reel5'][0] . ',' . $reels2['reel1'][1] . ',' . $reels2['reel2'][1] . ',' . $reels2['reel3'][1] . ',' . $reels2['reel4'][1] . ',' . $reels2['reel5'][1] . ',' . $reels2['reel1'][2] . ',' . $reels2['reel2'][2] . ',' . $reels2['reel3'][2] . ',' . $reels2['reel4'][2] . ',' . $reels2['reel5'][2];
                                $winString .= ('&mes=' . $mes);
                            }
                            $totalWin += ($scattersWin + $scattersWin2);
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
                        $spinType = 's';
                        if( $totalWin > 0 ) 
                        {
                            $spinType = 'c';
                            $slotSettings->SetBalance($totalWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                        }
                        $reportWin = $totalWin;
                        if( $scattersCount >= 3 ) 
                        {
                            if( $slotSettings->GetGameData('AncientEgyptPMFreeGames') == 0 ) 
                            {
                                $slotSettings->SetGameData('AncientEgyptPMBonusMpl', 1);
                                $spinType = 'b&status=0,0,0&rw=0.00&wins=0,0,0&bw=1&wins_mask=h,h,h&wp=0&end=0&bgt=28';
                            }
                            else
                            {
                                $slotSettings->SetGameData('AncientEgyptPMFreeGames', $slotSettings->GetGameData('AncientEgyptPMFreeGames') + $slotSettings->slotFreeCount);
                            }
                        }
                        if( $postData['slotEvent'] == 'freespin' ) 
                        {
                            $slotSettings->SetGameData('AncientEgyptPMBonusWin', $slotSettings->GetGameData('AncientEgyptPMBonusWin') + $totalWin);
                            $slotSettings->SetGameData('AncientEgyptPMTotalWin', $slotSettings->GetGameData('AncientEgyptPMTotalWin') + $totalWin);
                            $spinType = 's';
                            $Balance = $slotSettings->GetGameData('AncientEgyptPMFreeBalance');
                            if( $slotSettings->GetGameData('AncientEgyptPMFreeGames') <= $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') && $slotSettings->GetGameData('AncientEgyptPMFreeGames') > 0 ) 
                            {
                                $spinType = 'c';
                                $winString .= ('&ms=' . $slotSettings->GetGameData('AncientEgyptPMSym') . '&fs_total=' . $slotSettings->GetGameData('AncientEgyptPMFreeGames') . '&fsmul_total=' . $slotSettings->GetGameData('AncientEgyptPMBonusMpl') . '&fswin_total=' . $slotSettings->GetGameData('AncientEgyptPMBonusWin') . '&fsres_total=' . $slotSettings->GetGameData('AncientEgyptPMBonusWin') . '');
                            }
                            else
                            {
                                $winString .= ('&ms=' . $slotSettings->GetGameData('AncientEgyptPMSym') . '&fsmul=' . $slotSettings->GetGameData('AncientEgyptPMBonusMpl') . '&fsmax=' . $slotSettings->GetGameData('AncientEgyptPMFreeGames') . '&fswin=' . $slotSettings->GetGameData('AncientEgyptPMTotalWin') . '&fs=' . $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') . '&fsres=' . $slotSettings->GetGameData('AncientEgyptPMBonusWin'));
                            }
                            $totalWinRaw = $totalWin / $bonusMpl;
                        }
                        else
                        {
                            $totalWinRaw = $totalWin;
                            $slotSettings->SetGameData('AncientEgyptPMTotalWin', $totalWin);
                            $slotSettings->SetGameData('AncientEgyptPMBonusWin', $totalWin);
                        }
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString .= ('&' . implode('&', $lineWins));
                        $s = $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2];
                        $response = 'tw=' . $slotSettings->GetGameData('AncientEgyptPMBonusWin') . '&bstate=' . $slotSettings->GetGameData('AncientEgyptPMBonusState') . '&balance=' . $Balance . '&index=' . $postData['index'] . '&balance_cash=' . $Balance . '&balance_bonus=0.00' . $winString . '&na=' . $spinType . '&stime=' . floor(microtime(true) * 1000) . '&sa=' . $reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . ',' . $reels['reel4'][3] . ',' . $reels['reel5'][3] . '&sb=' . $reels['reel1'][-1] . ',' . $reels['reel2'][-1] . ',' . $reels['reel3'][-1] . ',' . $reels['reel4'][-1] . ',' . $reels['reel5'][-1] . '&sh=3&c=0.01&sver=5&n_reel_set=0&counter=' . ((int)$postData['counter'] + 1) . '&l=10&s=' . $s . '&w=' . $totalWinRaw . '';
                        if( $slotSettings->GetGameData('AncientEgyptPMFreeGames') <= $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') && $slotSettings->GetGameData('AncientEgyptPMFreeGames') > 0 ) 
                        {
                            $slotSettings->SetGameData('AncientEgyptPMTotalWin', 0);
                            $slotSettings->SetGameData('AncientEgyptPMBonusWin', 0);
                        }
                        $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"Sym":' . $slotSettings->GetGameData('AncientEgyptPMSym') . ',"BonusMpl":' . $slotSettings->GetGameData('AncientEgyptPMBonusMpl') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('AncientEgyptPMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AncientEgyptPMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"bonusWin":' . $slotSettings->GetGameData('AncientEgyptPMBonusWin') . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                        $slotSettings->SaveLogReport($response_log, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
                        if( $scattersCount >= 3 ) 
                        {
                            if( $slotSettings->GetGameData('AncientEgyptPMFreeGames') > 0 ) 
                            {
                                $slotSettings->SetGameData('AncientEgyptPMBonusWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('AncientEgyptPMFreeBalance', $Balance);
                                $slotSettings->SetGameData('AncientEgyptPMTotalWin', 0);
                                $slotSettings->SetGameData('AncientEgyptPMBonusState', 0);
                                $slotSettings->SetGameData('AncientEgyptPMBonusWin', $totalWin);
                            }
                        }
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
