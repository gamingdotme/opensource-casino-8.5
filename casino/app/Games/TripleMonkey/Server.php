<?php 
namespace VanguardLTE\Games\TripleMonkey
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
                        $response = '';
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
                        $postData['slotEvent'] = $postData['request'];
                        if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') <= $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 && $postData['slotEvent'] != 'init' ) 
                        {
                            $postData['slotEvent'] = 'freespin';
                        }
                        if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            if( $postData['lines'] <= 0 || $postData['bet'] <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['bet'] * $postData['lines']) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                            if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                                exit( $response );
                            }
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' && ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 || $slotSettings->GetBalance() < $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin')) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                            $slotSettings->InternalError($response . ' -- TotalWin = ' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . ' -- Balance = ' . $slotSettings->GetBalance());
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'init' ) 
                        {
                            $slotSettings->SetGameData('TripleMonkeyBonusWin', 0);
                            $slotSettings->SetGameData('TripleMonkeyFreeGames', 0);
                            $slotSettings->SetGameData('TripleMonkeyCurrentFreeGame', 0);
                            $slotSettings->SetGameData('TripleMonkeyTotalWin', 0);
                            $slotSettings->SetGameData('TripleMonkeyStartBonusWin', 0);
                            $slotSettings->SetGameData('TripleMonkeyFreeBalance', 0);
                            $slotSettings->SetGameData('TripleMonkeyIsRespin', 0);
                            $slotSettings->SetGameData('TripleMonkeyReSpinCount', 0);
                            $slotSettings->SetGameData('TripleMonkeyScene', '');
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'Scene', $lastEvent->serverResponse->Scene);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $lastEvent->serverResponse->reelsSymbols->reel1 = (array)$lastEvent->serverResponse->reelsSymbols->reel1;
                                $lastEvent->serverResponse->reelsSymbols->reel2 = (array)$lastEvent->serverResponse->reelsSymbols->reel2;
                                $lastEvent->serverResponse->reelsSymbols->reel3 = (array)$lastEvent->serverResponse->reelsSymbols->reel3;
                                $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                $rp2 = '[' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ']';
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ']');
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ']');
                                $bet = $lastEvent->serverResponse->bet;
                                $prevResult = ',"previousResult":' . json_encode($lastEvent->lastResponse->result);
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
                                $rp2 = '[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ']';
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ']');
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ']');
                                $bet = $slotSettings->Bet[0];
                                $prevResult = '';
                            }
                            $jsSet = json_encode($slotSettings);
                            $Balance = $slotSettings->GetBalance();
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"gameSession":"","balance":{"currency":"' . $slotSettings->slotCurrency . '","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init"' . $prevResult . ',"name":"TripleMonkey","gameId":"sw_tm","settings":{"winMax":500000,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $bet . ',"stakeMax":' . $slotSettings->Bet[count($slotSettings->Bet) - 1] . ',"stakeMin":' . $slotSettings->Bet[0] . ',"maxTotalStake":' . ($slotSettings->Bet[count($slotSettings->Bet) - 1] * 40) . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"slot":{"sets":{"main":{"reels":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . ']]},"reSpin100":{"reels":[[0],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6]]},"reSpin010":{"reels":[[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[0],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6]]},"reSpin001":{"reels":[[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[0]]},"reSpin110":{"reels":[[0],[0],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6]]},"reSpin011":{"reels":[[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[0],[0]]},"reSpin101":{"reels":[[0],[4,4,4,2,2,2,1,1,1,3,3,3,5,5,5,7,7,7,8,8,8,6,6,6],[0]]}},"reels":{"set":"main","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"linesDefinition":{"fixedLinesCount":5},"paytable":{"stake":{"value":1,"multiplier":1,"payouts":[[0,0,300],[0,0,200],[0,0,100],[0,0,50],[0,0,25],[0,0,15],[0,0,10],[0,0,5]]}},"lines":[[1,1,1],[0,0,0],[2,2,2],[0,1,2],[2,1,0]]},"stake":null,"version":"1.1.0"},"roundEnded":true}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('TripleMonkeyDealerCard');
                            $totalWin = $slotSettings->GetGameData('TripleMonkeyTotalWin');
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
                            $slotSettings->SetGameData('TripleMonkeyTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('TripleMonkeyDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('TripleMonkeyTotalWin');
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
                            $slotSettings->SetGameData('TripleMonkeyTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' ) 
                        {
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
                            if( $slotSettings->GetGameData('TripleMonkeyCurrentFreeGame') <= $slotSettings->GetGameData('TripleMonkeyFreeGames') && $slotSettings->GetGameData('TripleMonkeyFreeGames') > 0 ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                            }
                            $postData['slotBet'] = $postData['bet'];
                            $postData['slotLines'] = $postData['lines'];
                            $allbet = $postData['slotBet'] * $postData['slotLines'];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            $isBonusAccept = rand(1, 10);
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = ($postData['slotBet'] * $postData['slotLines']) / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines']);
                                $slotSettings->SetBalance(-1 * ($postData['slotBet'] * $postData['slotLines']), $postData['slotEvent']);
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('TripleMonkeyStartBonusWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyBonusWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyFreeGames', 0);
                                $slotSettings->SetGameData('TripleMonkeyCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TripleMonkeyTotalWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyFreeBalance', 0);
                            }
                            else
                            {
                                $slotSettings->SetGameData('TripleMonkeyCurrentFreeGame', $slotSettings->GetGameData('TripleMonkeyCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $Balance = $slotSettings->GetBalance();
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
                                    0
                                ];
                                $wild = ['0'];
                                $scatter = '1';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin011' ) 
                                    {
                                        $reels['reel2'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['reel3'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][1] = 0;
                                        $reels['rp'][2] = 0;
                                    }
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin110' ) 
                                    {
                                        $reels['reel1'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['reel2'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][0] = 0;
                                        $reels['rp'][1] = 0;
                                    }
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin101' ) 
                                    {
                                        $reels['reel1'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['reel3'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][0] = 0;
                                        $reels['rp'][2] = 0;
                                    }
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin100' ) 
                                    {
                                        $reels['reel1'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][0] = 0;
                                    }
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin010' ) 
                                    {
                                        $reels['reel2'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][1] = 0;
                                    }
                                    if( $slotSettings->GetGameData('TripleMonkeyScene') == 'reSpin001' ) 
                                    {
                                        $reels['reel3'] = [
                                            '0', 
                                            '0', 
                                            '0'
                                        ];
                                        $reels['rp'][2] = 0;
                                    }
                                }
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                        $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                        $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
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
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . ($csym - 1) . ',2]}';
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
                                $startBonus = false;
                                $scattersStr = '';
                                $scattersRows = [
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $scattersStrArr = [];
                                for( $r = 1; $r <= 3; $r++ ) 
                                {
                                    $scattersCount = 0;
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                    }
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $scattersRows[$r] = 1;
                                        $startBonus = true;
                                    }
                                }
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"Bad Reel Strip"}';
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
                                    else if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        if( $totalWin <= $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) ) 
                                        {
                                            break;
                                        }
                                    }
                                    else if( $scattersCount >= 3 && $spinWinLimit < ($postData['slotBet'] * $postData['slotLines'] * 15) ) 
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
                            if( $scattersRows[1] == 1 && $scattersRows[2] == 1 && $scattersRows[3] == 1 ) 
                            {
                                $slotSettings->SetGameData('TripleMonkeyScene', '');
                                $startBonus = false;
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('TripleMonkeyBonusWin', $slotSettings->GetGameData('TripleMonkeyBonusWin') + $totalWin);
                                $Balance = $slotSettings->GetGameData('TripleMonkeyFreeBalance');
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData('TripleMonkeyFreeGames') - $slotSettings->GetGameData('TripleMonkeyCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('TripleMonkeyBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('TripleMonkeyStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('TripleMonkeyFreeGames');
                                $spinEvent = '';
                                $roundEnded = 'false';
                                $curScene = $slotSettings->GetGameData('TripleMonkeyScene');
                                $reelSet = $slotSettings->GetGameData('TripleMonkeyScene');
                            }
                            else
                            {
                                $spinState = '"currentScene":"main","multiplier":1';
                                $spinEvent = '';
                                $roundEnded = 'true';
                                $curScene = 'main';
                                $reelSet = 'main';
                                $slotSettings->SetGameData('TripleMonkeyTotalWin', $totalWin);
                            }
                            if( $startBonus && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $reels0 = $slotSettings->GetReelStrips($reels['rp'], $postData['slotEvent']);
                                $rpTmp = '[' . $reels0['reel1'][0] . ',' . $reels0['reel2'][0] . ',' . $reels0['reel3'][0] . '],[' . $reels0['reel1'][1] . ',' . $reels0['reel2'][1] . ',' . $reels0['reel3'][1] . '],[' . $reels0['reel1'][2] . ',' . $reels0['reel2'][2] . ',' . $reels0['reel3'][2] . ']';
                                $slotSettings->SetGameData('TripleMonkeyStartBonusInfo', ',"reels":{"view":[' . $rpTmp . '],"positions":[' . implode(',', $reels['rp']) . ']}');
                                $slotSettings->SetGameData('TripleMonkeyFreeBalance', $Balance);
                                $slotSettings->SetGameData('TripleMonkeyStartBonusWin', $totalWin);
                                $slotSettings->SetGameData('TripleMonkeyBonusWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyFreeGames', $slotSettings->slotFreeCount);
                                if( $scattersRows[1] == 0 && $scattersRows[2] == 0 && $scattersRows[3] == 1 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin001');
                                }
                                if( $scattersRows[1] == 1 && $scattersRows[2] == 0 && $scattersRows[3] == 0 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin100');
                                }
                                if( $scattersRows[1] == 0 && $scattersRows[2] == 1 && $scattersRows[3] == 0 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin010');
                                }
                                if( $scattersRows[1] == 1 && $scattersRows[2] == 1 && $scattersRows[3] == 0 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin110');
                                }
                                if( $scattersRows[1] == 0 && $scattersRows[2] == 1 && $scattersRows[3] == 1 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin011');
                                }
                                if( $scattersRows[1] == 1 && $scattersRows[2] == 0 && $scattersRows[3] == 1 ) 
                                {
                                    $slotSettings->SetGameData('TripleMonkeyScene', 'reSpin101');
                                }
                                $spinEvent = '{"id":"reSpinsStart","reels":{"set":"main","positions":[' . implode(',', $reels['rp']) . '],"view":[[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . '],[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . '],[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ']]},"amount":' . $slotSettings->slotFreeCount . ',"reels":{"view":[' . $rpTmp . '],"set":"' . $slotSettings->GetGameData('TripleMonkeyScene') . '","positions":[' . implode(',', $reels['rp']) . ']},"triggeredSceneId":"' . $slotSettings->GetGameData('TripleMonkeyScene') . '","triggerSymbols":[' . implode(',', $scattersStrArr) . ']}';
                                $spinState = '"currentScene":"' . $slotSettings->GetGameData('TripleMonkeyScene') . '","multiplier":1,"reSpinWin":' . $totalWin;
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            if( $postData['slotEvent'] == 'freespin' && $totalWin > 0 ) 
                            {
                                $spinState = '"currentScene":"main","multiplier":1,"reSpinWin":' . $totalWin;
                                $reels_ = $reels;
                                for( $ii = 1; $ii <= 3; $ii++ ) 
                                {
                                    for( $jj = 0; $jj < 3; $jj++ ) 
                                    {
                                        if( $reels_['reel' . $ii][$jj] == '0' ) 
                                        {
                                            $reels_['reel' . $ii][$jj] = '1';
                                        }
                                    }
                                }
                                $spinEvent = '{"id":"reSpinsEnd","reels":{"set":"main","positions":[' . implode(',', $reels_['rp']) . '],"view":[[' . $reels_['reel1'][0] . ',' . $reels_['reel2'][0] . ',' . $reels_['reel3'][0] . '],[' . $reels_['reel1'][1] . ',' . $reels_['reel2'][1] . ',' . $reels_['reel3'][1] . '],[' . $reels_['reel1'][2] . ',' . $reels_['reel2'][2] . ',' . $reels_['reel3'][2] . ']]}}';
                                $roundEnded = 'true';
                                $curScene = $slotSettings->GetGameData('TripleMonkeyScene');
                                $slotSettings->SetGameData('TripleMonkeyBonusWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyFreeGames', 0);
                                $slotSettings->SetGameData('TripleMonkeyCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TripleMonkeyTotalWin', 0);
                                $slotSettings->SetGameData('TripleMonkeyFreeBalance', 0);
                            }
                            $response = '{"gameSession":"","dbg":"' . $postData['slotEvent'] . '|' . $slotSettings->GetGameData('TripleMonkeyCurrentFreeGame') . '|' . $slotSettings->GetGameData('TripleMonkeyFreeGames') . '","balance":{"currency":"USD","amount":' . $slotSettings->GetBalance() . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"spin","stake":{"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"coin":1},"totalBet":' . ($postData['slotBet'] * $postData['slotLines']) . ',"totalWin":' . $totalWin . ',"scene":"' . $curScene . '","multiplier":1,"state":{' . $spinState . '},"reels":{"set":"' . $reelSet . '","positions":[' . implode(',', $reels['rp']) . '],"view":[[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . '],[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . '],[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ']]},"rewards":[' . $winString . '],"events":[' . $spinEvent . '],"roundEnded":' . $roundEnded . ',"version":"1.0.2"},"requestId":1,"roundEnded":' . $roundEnded . '}';
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","lastResponse":' . $response . ',"serverResponse":{"Scene":"' . $slotSettings->GetGameData('TripleMonkeyScene') . '","StartBonusWin":' . $slotSettings->GetGameData('TripleMonkeyStartBonusWin') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('TripleMonkeyFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('TripleMonkeyCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('TripleMonkeyBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response_log, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
                        }
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
