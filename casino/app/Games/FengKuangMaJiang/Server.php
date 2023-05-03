<?php 
namespace VanguardLTE\Games\FengKuangMaJiang
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
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
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
                            $slotSettings->SetGameData('FengKuangMaJiangBonusWin', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangFreeGames', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangCurrentFreeGame', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangTotalWin', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangStartBonusWin', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangFreeBalance', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 0);
                            $slotSettings->SetGameData('FengKuangMaJiangReSpinCount', 0);
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'StartBonusWin', $lastEvent->serverResponse->StartBonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $lastEvent->serverResponse->reelsSymbols->reel1 = (array)$lastEvent->serverResponse->reelsSymbols->reel1;
                                $lastEvent->serverResponse->reelsSymbols->reel2 = (array)$lastEvent->serverResponse->reelsSymbols->reel2;
                                $lastEvent->serverResponse->reelsSymbols->reel3 = (array)$lastEvent->serverResponse->reelsSymbols->reel3;
                                $lastEvent->serverResponse->reelsSymbols->reel4 = (array)$lastEvent->serverResponse->reelsSymbols->reel4;
                                $lastEvent->serverResponse->reelsSymbols->reel5 = (array)$lastEvent->serverResponse->reelsSymbols->reel5;
                                $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                $rp2 = '[' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0] . ']';
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1] . ']');
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2] . ']');
                                $bet = $lastEvent->serverResponse->bet;
                                $lastEvent->lastResponse->result->state->collapsingWin = 0;
                                $lastEvent->lastResponse->result->state->collapsingCount = 0;
                                $lastEvent->lastResponse->result->state->isReSpin = false;
                                $lastEvent->lastResponse->result->rewards = [];
                                $lastEvent->lastResponse->result->totalWin = 0;
                                $lastEvent->lastResponse->result->roundEnded = true;
                                $prevResult = ',"previousResult":' . json_encode($lastEvent->lastResponse->result);
                            }
                            else
                            {
                                $rp1 = implode(',', [
                                    rand(0, count($slotSettings->reelStrip1) - 4), 
                                    rand(0, count($slotSettings->reelStrip2) - 4), 
                                    rand(0, count($slotSettings->reelStrip3) - 4), 
                                    rand(0, count($slotSettings->reelStrip4) - 4), 
                                    rand(0, count($slotSettings->reelStrip5) - 4)
                                ]);
                                $rp_1 = rand(0, count($slotSettings->reelStrip1) - 4);
                                $rp_2 = rand(0, count($slotSettings->reelStrip2) - 4);
                                $rp_3 = rand(0, count($slotSettings->reelStrip3) - 4);
                                $rp_4 = rand(0, count($slotSettings->reelStrip4) - 4);
                                $rp_5 = rand(0, count($slotSettings->reelStrip5) - 4);
                                $rr1 = $slotSettings->reelStrip1[$rp_1];
                                $rr2 = $slotSettings->reelStrip2[$rp_2];
                                $rr3 = $slotSettings->reelStrip3[$rp_3];
                                $rr4 = $slotSettings->reelStrip4[$rp_4];
                                $rr5 = $slotSettings->reelStrip5[$rp_5];
                                $rp2 = '[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']';
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                $rr4 = $slotSettings->reelStrip4[$rp_4 + 1];
                                $rr5 = $slotSettings->reelStrip5[$rp_5 + 1];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                $rr4 = $slotSettings->reelStrip4[$rp_4 + 2];
                                $rr5 = $slotSettings->reelStrip5[$rp_5 + 2];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 3];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 3];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 3];
                                $rr4 = $slotSettings->reelStrip4[$rp_4 + 3];
                                $rr5 = $slotSettings->reelStrip5[$rp_5 + 3];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                $bet = $slotSettings->Bet[0];
                                $prevResult = '';
                            }
                            $jsSet = json_encode($slotSettings);
                            $Balance = $slotSettings->GetBalance();
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"gameSession":"","balance":{"currency":"' . $slotSettings->slotCurrency . '","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init"' . $prevResult . ',"name":"Feng Kuang Ma Jiang","gameId":"sw_fkmj","settings":{"winMax":500000,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $bet . ',"stakeMax":' . $slotSettings->Bet[count($slotSettings->Bet) - 1] . ',"stakeMin":' . $slotSettings->Bet[0] . ',"maxTotalStake":' . ($slotSettings->Bet[count($slotSettings->Bet) - 1] * 40) . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"slot":{"sets":{"main":{"reels":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . '],[' . implode(',', $slotSettings->reelStrip4) . '],[' . implode(',', $slotSettings->reelStrip5) . ']]},"freeSpins":{"reels":[[' . implode(',', $slotSettings->reelStripBonus1) . '],[' . implode(',', $slotSettings->reelStripBonus2) . '],[' . implode(',', $slotSettings->reelStripBonus3) . '],[' . implode(',', $slotSettings->reelStripBonus4) . '],[' . implode(',', $slotSettings->reelStripBonus5) . ']]}},"reels":{"set":"main","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"linesDefinition":{"fixedLinesCount":25},"paytable":{"stake":{"value":1,"multiplier":1,"payouts":[[0,0,100,250,5000],[0,0,75,150,300],[0,0,60,120,200],[0,0,30,80,150],[0,0,10,45,100],[0,0,10,35,90],[0,0,8,35,75],[0,0,6,25,60],[0,2,5,10,30],[0,0,0,0,0]]}},"lines":[[1,1,1,1,1],[0,0,0,0,0],[2,2,2,2,2],[0,1,2,1,0],[2,1,0,1,2],[0,0,1,2,2],[2,2,1,0,0],[1,0,1,2,1],[1,2,1,0,1],[1,0,0,0,0],[1,2,2,2,2],[0,1,1,1,1],[2,1,1,1,1],[0,1,0,1,0],[2,1,2,1,2],[1,1,0,1,1],[1,1,2,1,1],[0,0,2,0,0],[2,2,0,2,2],[0,2,0,2,0],[2,0,2,0,2],[1,0,2,2,2],[1,2,0,0,0],[0,2,2,2,1],[2,0,0,0,1]]},"stake":null,"version":"1.1.0"},"roundEnded":true}';
                            $slotSettings->SetGameData('FengKuangMaJiangChangeMap', [
                                [
                                    0, 
                                    0, 
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0
                                ]
                            ]);
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('FengKuangMaJiangDealerCard');
                            $totalWin = $slotSettings->GetGameData('FengKuangMaJiangTotalWin');
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
                            $statBet = $totalWin;
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
                            $slotSettings->SetGameData('FengKuangMaJiangTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('FengKuangMaJiangDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('FengKuangMaJiangTotalWin');
                            $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $slotSettings->GetGameData('FengKuangMaJiangBonusWin') - $totalWin);
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
                            $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $slotSettings->GetGameData('FengKuangMaJiangBonusWin') + $totalWin);
                            $slotSettings->SetGameData('FengKuangMaJiangTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"bonusWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            $linesId = [];
                            $linesId[1] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[2] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[3] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[4] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[5] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[6] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[7] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[8] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                3, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[11] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[12] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[13] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[14] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[15] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[16] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[17] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[18] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[19] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[20] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[21] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[22] = [
                                2, 
                                1, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[23] = [
                                2, 
                                3, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[24] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[25] = [
                                3, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $isFreeGame = false;
                            if( $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') <= $slotSettings->GetGameData('FengKuangMaJiangFreeGames') && $slotSettings->GetGameData('FengKuangMaJiangFreeGames') > 0 ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                                $isFreeGame = true;
                            }
                            if( $slotSettings->GetGameData('FengKuangMaJiangIsReSpin') == 1 ) 
                            {
                                $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 0);
                                $postData['slotEvent'] = 'respin';
                            }
                            $postData['slotBet'] = $postData['bet'];
                            $postData['slotLines'] = $postData['lines'];
                            $allbet = $postData['slotBet'] * $postData['slotLines'];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $isFreeGame && $winType == 'bonus' ) 
                            {
                                $winType = 'win';
                            }
                            if( $postData['slotEvent'] != 'freespin' && $postData['slotEvent'] != 'respin' ) 
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
                                $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangBonusWin', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangFreeGames', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangCurrentFreeGame', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangTotalWin', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangFreeBalance', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangFreeMpl', 1);
                            }
                            else if( $postData['slotEvent'] != 'respin' ) 
                            {
                                $slotSettings->SetGameData('FengKuangMaJiangCurrentFreeGame', $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') + 1);
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('FengKuangMaJiangFreeMpl', 1);
                            }
                            else if( $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $slotSettings->GetGameData('FengKuangMaJiangFreeMpl') < 5 ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeMpl', $slotSettings->GetGameData('FengKuangMaJiangFreeMpl') + 1);
                                }
                                if( $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') > 0 ) 
                                {
                                    $bonusMpl = $slotSettings->GetGameData('FengKuangMaJiangFreeMpl');
                                }
                                else
                                {
                                    $bonusMpl = 1;
                                }
                            }
                            $Balance = $slotSettings->GetBalance();
                            $tmpChangeMap = $slotSettings->GetGameData('FengKuangMaJiangChangeMap');
                            $rr = $slotSettings->GetGameData('FengKuangMaJiangReelsMap');
                            if( $postData['slotEvent'] == 'respin' ) 
                            {
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    if( $tmpChangeMap[$r - 1][2] < 0 && $tmpChangeMap[$r - 1][1] >= 0 && $tmpChangeMap[$r - 1][0] >= 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][1];
                                        $rr['reel' . $r][1] = $rr['reel' . $r][0];
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] >= 0 && $tmpChangeMap[$r - 1][1] < 0 && $tmpChangeMap[$r - 1][0] >= 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][2];
                                        $rr['reel' . $r][1] = $rr['reel' . $r][0];
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] >= 0 && $tmpChangeMap[$r - 1][1] >= 0 && $tmpChangeMap[$r - 1][0] < 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][2];
                                        $rr['reel' . $r][1] = $rr['reel' . $r][1];
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] < 0 && $tmpChangeMap[$r - 1][1] < 0 && $tmpChangeMap[$r - 1][0] >= 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][0];
                                        $rr['reel' . $r][1] = 'EMPTY';
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] >= 0 && $tmpChangeMap[$r - 1][1] < 0 && $tmpChangeMap[$r - 1][0] < 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][2];
                                        $rr['reel' . $r][1] = 'EMPTY';
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] < 0 && $tmpChangeMap[$r - 1][1] >= 0 && $tmpChangeMap[$r - 1][0] < 0 ) 
                                    {
                                        $rr['reel' . $r][2] = $rr['reel' . $r][1];
                                        $rr['reel' . $r][1] = 'EMPTY';
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                    else if( $tmpChangeMap[$r - 1][2] < 0 && $tmpChangeMap[$r - 1][1] < 0 && $tmpChangeMap[$r - 1][0] < 0 ) 
                                    {
                                        $rr['reel' . $r][2] = 'EMPTY';
                                        $rr['reel' . $r][1] = 'EMPTY';
                                        $rr['reel' . $r][0] = 'EMPTY';
                                    }
                                }
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
                                $scatter = '13';
                                $scatter2 = '12';
                                $respinReels = [
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    []
                                ];
                                $tmpChangeMap_ = [
                                    [
                                        0, 
                                        0, 
                                        0
                                    ], 
                                    [
                                        0, 
                                        0, 
                                        0
                                    ], 
                                    [
                                        0, 
                                        0, 
                                        0
                                    ], 
                                    [
                                        0, 
                                        0, 
                                        0
                                    ], 
                                    [
                                        0, 
                                        0, 
                                        0
                                    ]
                                ];
                                if( $postData['slotEvent'] == 'respin' ) 
                                {
                                    $reels = [];
                                    $reels['rp'] = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1
                                    ];
                                    $reels['reel1'] = [];
                                    $reels['reel2'] = [];
                                    $reels['reel3'] = [];
                                    $reels['reel4'] = [];
                                    $reels['reel5'] = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $reels['reel' . $r] = [];
                                        $respinReels[$r] = [];
                                        for( $i = 2; $i >= 0; $i-- ) 
                                        {
                                            if( $rr['reel' . $r][$i] == 'EMPTY' ) 
                                            {
                                                $rSym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][$i] = $rSym;
                                                $respinReels[$r][] = $rSym;
                                            }
                                            else
                                            {
                                                $reels['reel' . $r][$i] = $rr['reel' . $r][$i];
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                }
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable[$csym]) || !isset($slotSettings->Paytable[$csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $s = [];
                                            $s[0] = $reels['reel1'][$linesId[$k + 1][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k + 1][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k + 1][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k + 1][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k + 1][4] - 1];
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable[$csym][1] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',0]}';
                                                }
                                            }
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
                                                $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k + 1][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k + 1][1] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',1]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k + 1][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k + 1][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k + 1][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k + 1][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k + 1][4] - 1];
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
                                                    $tmpChangeMap_[0][$linesId[$k + 1][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k + 1][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k + 1][2] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',2]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k + 1][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k + 1][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k + 1][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k + 1][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k + 1][4] - 1];
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
                                                $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k + 1][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k + 1][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k + 1][2] - 1] = -1;
                                                    $tmpChangeMap_[3][$linesId[$k + 1][3] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',3]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k + 1][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k + 1][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k + 1][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k + 1][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k + 1][4] - 1];
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
                                                $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k + 1][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k + 1][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k + 1][2] - 1] = -1;
                                                    $tmpChangeMap_[3][$linesId[$k + 1][3] - 1] = -1;
                                                    $tmpChangeMap_[4][$linesId[$k + 1][4] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',4]}';
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
                                $scattersStr = '{';
                                $scattersStr2 = '{';
                                $scattersCount = 0;
                                $scattersCount2 = 0;
                                $scattersStrArr = [];
                                $scattersStrArr2 = [];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                            $scattersStrArr[] = '[' . $p . ',' . ($r - 1) . ']';
                                        }
                                        if( $reels['reel' . $r][$p] == $scatter2 ) 
                                        {
                                            $scattersCount2++;
                                            $scattersStr2 .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter2 . '"],');
                                            $scattersStrArr2[] = '[' . $p . ',' . ($r - 1) . ']';
                                        }
                                    }
                                }
                                $Events0 = '';
                                $scattersWin = 0;
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                    $Events0 = '{"id": "FKMJTriggerBonus", "triggerSymbols": [' . implode(',', $scattersStrArr) . ']}';
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
                                $scattersWin2 = $slotSettings->Paytable[$scatter2][$scattersCount2] * $postData['slotBet'] * $postData['slotLines'];
                                if( $scattersWin2 > 0 ) 
                                {
                                    $scattersStr2 .= '"scattersType":"win",';
                                    $tmpStringWin = '{"reward":"scatter","payout":' . $scattersWin2 . ',"lineMultiplier":1,"positions":[' . implode(',', $scattersStrArr2) . '],"paytable":[9,' . ($scattersCount2 - 1) . ']}';
                                    array_push($lineWins, $tmpStringWin);
                                }
                                else
                                {
                                    $scattersStr2 .= '"scattersType":"none",';
                                }
                                $scattersStr2 .= ('"scattersWin":' . $scattersWin2 . '}');
                                $totalWin += $scattersWin2;
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . json_encode($reels) . '|' . $scattersCount . '|' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $postData['slotEvent'] != 'respin' && $postData['slotEvent'] != 'freespin' ) 
                                {
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
                                    }
                                }
                                if( $scattersCount >= 3 && $winType != 'bonus' || $scattersCount > 5 ) 
                                {
                                }
                                else
                                {
                                    if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
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
                                if( $postData['slotEvent'] == 'respin' && $totalWin <= $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) ) 
                                {
                                    break;
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
                                $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $slotSettings->GetGameData('FengKuangMaJiangBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FengKuangMaJiangTotalWin', $totalWin);
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"collapsingCount": 0,"collapsingWin":0,"isReSpin":false,"freeSpinsCount":' . ($slotSettings->GetGameData('FengKuangMaJiangFreeGames') - $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames');
                                $spinEvent = '' . $Events0;
                                $roundEnded = 'false';
                                $curScene = 'freeSpins';
                                $reelSet = 'freeSpins';
                                if( $totalWin > 0 || $Events0 != '' ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 1);
                                    $slotSettings->SetGameData('FengKuangMaJiangReSpinCount', 1);
                                    $spinState = '"currentScene":"freeSpins","multiplier":1,"collapsingCount": ' . $slotSettings->GetGameData('FengKuangMaJiangReSpinCount') . ',"collapsingWin":' . $slotSettings->GetGameData('FengKuangMaJiangTotalWin') . ',"isReSpin":true,"freeSpinsCount":' . ($slotSettings->GetGameData('FengKuangMaJiangFreeGames') - $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') + 1) . ',"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames');
                                    $roundEnded = 'false';
                                }
                                if( $slotSettings->GetGameData('FengKuangMaJiangFreeGames') <= $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') && $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') > 0 && $totalWin <= 0 ) 
                                {
                                    $spinState = '"currentScene":"main","multiplier":1,"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"freeSpinsCount":0,"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames');
                                    $spinEvent = '{"id":"freeSpinsEnd","reels":{"set":"main","view":[[6,11,1,4,6],[9,5,8,11,9],[3,9,11,4,4],[9,6,8,7,5]],"positions":[43,82,23,29,66]}}';
                                    $roundEnded = 'true';
                                    $curScene = 'freeSpins';
                                    $slotSettings->SetGameData('FengKuangMaJiangBonusWin', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeGames', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangTotalWin', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeBalance', 0);
                                }
                            }
                            else if( $postData['slotEvent'] == 'respin' ) 
                            {
                                $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $slotSettings->GetGameData('FengKuangMaJiangBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FengKuangMaJiangTotalWin', $slotSettings->GetGameData('FengKuangMaJiangTotalWin') + $totalWin);
                                if( $isFreeGame ) 
                                {
                                    $scene = 'freeSpins';
                                    $fString = ',"freeSpinsCount":' . ($slotSettings->GetGameData('FengKuangMaJiangFreeGames') - $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames');
                                    $roundEnded = 'false';
                                    $mpl0 = $bonusMpl;
                                }
                                else
                                {
                                    $scene = 'main';
                                    $fString = '';
                                    $roundEnded = 'true';
                                    $mpl0 = 1;
                                }
                                $spinState = '"currentScene":"' . $scene . '","multiplier":' . $mpl0 . ',"collapsingCount": 0,"collapsingWin":' . $slotSettings->GetGameData('FengKuangMaJiangTotalWin') . ',"isReSpin":false' . $fString;
                                $spinEvent = '' . $Events0;
                                $curScene = $scene;
                                $reelSet = $scene;
                                if( $slotSettings->GetGameData('FengKuangMaJiangFreeGames') <= $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') && $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') > 0 && $totalWin <= 0 ) 
                                {
                                    $spinState = '"currentScene":"main","multiplier":1,"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"freeSpinsCount":0,"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames');
                                    $spinEvent = '{"id":"freeSpinsEnd","reels":{"set":"main","view":[[6,11,1,4,6],[9,5,8,11,9],[3,9,11,4,4],[9,6,8,7,5]],"positions":[43,82,23,29,66]}}';
                                    $roundEnded = 'true';
                                    $curScene = 'freeSpins';
                                    $slotSettings->SetGameData('FengKuangMaJiangBonusWin', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeGames', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangTotalWin', 0);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeBalance', 0);
                                }
                                if( $totalWin > 0 || $Events0 != '' ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 1);
                                    $slotSettings->SetGameData('FengKuangMaJiangReSpinCount', $slotSettings->GetGameData('FengKuangMaJiangReSpinCount') + 1);
                                    $spinState = '"currentScene":"' . $scene . '","multiplier":' . $mpl0 . ',"collapsingCount": ' . $slotSettings->GetGameData('FengKuangMaJiangReSpinCount') . ',"collapsingWin":' . $slotSettings->GetGameData('FengKuangMaJiangTotalWin') . ',"isReSpin":true' . $fString;
                                    $roundEnded = 'false';
                                }
                                else if( $slotSettings->GetGameData('FengKuangMaJiangWaitFreeGames') == 1 ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangWaitFreeGames', 0);
                                    $reels0 = $slotSettings->GetReelStrips('', $postData['slotEvent']);
                                    $rpTmp = '[' . $reels0['reel1'][0] . ',' . $reels0['reel2'][0] . ',' . $reels0['reel3'][0] . ',' . $reels0['reel4'][0] . ',' . $reels0['reel5'][0] . '],[' . $reels0['reel1'][1] . ',' . $reels0['reel2'][1] . ',' . $reels0['reel3'][1] . ',' . $reels0['reel4'][1] . ',' . $reels0['reel5'][1] . '],[' . $reels0['reel1'][2] . ',' . $reels0['reel2'][2] . ',' . $reels0['reel3'][2] . ',' . $reels0['reel4'][2] . ',' . $reels0['reel5'][2] . ']';
                                    $spinEvent = '{"id":"freeSpinsStart","amount":' . $slotSettings->slotFreeCount . ',"reels":{"view":[' . $rpTmp . '],"set":"freeSpins","positions":[' . implode(',', $reels['rp']) . ']},"triggeredSceneId":"freeSpins","triggerSymbols":[' . implode(',', $scattersStrArr) . ']}';
                                    $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData('FengKuangMaJiangFreeGames') - $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames') . ',"collapsingCount": 0,"collapsingWin":' . $slotSettings->GetGameData('FengKuangMaJiangTotalWin') . ',"isReSpin":false';
                                }
                            }
                            else if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                $slotSettings->SetGameData('FengKuangMaJiangWaitFreeGames', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangBonusWin', 0);
                                $slotSettings->SetGameData('FengKuangMaJiangTotalWin', $totalWin);
                                $spinState = '"currentScene":"main","multiplier":1,"collapsingCount": 0,"collapsingWin":0,"isReSpin":false';
                                $spinEvent = '' . $Events0;
                                $roundEnded = 'true';
                                $curScene = 'main';
                                $reelSet = 'main';
                                if( $totalWin > 0 || $Events0 != '' ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangIsReSpin', 1);
                                    $slotSettings->SetGameData('FengKuangMaJiangReSpinCount', 1);
                                    $spinState = '"currentScene":"main","multiplier":1,"collapsingCount": ' . $slotSettings->GetGameData('FengKuangMaJiangReSpinCount') . ',"collapsingWin":' . $slotSettings->GetGameData('FengKuangMaJiangTotalWin') . ',"isReSpin":true';
                                    $roundEnded = 'false';
                                }
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('FengKuangMaJiangFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeBalance', $Balance);
                                    $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $totalWin);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeGames', $slotSettings->GetGameData('FengKuangMaJiangFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FengKuangMaJiangBonusWin', $slotSettings->GetGameData('FengKuangMaJiangBonusWin') + $totalWin);
                                    $slotSettings->SetGameData('FengKuangMaJiangFreeGames', $slotSettings->slotFreeCount);
                                }
                                $slotSettings->SetGameData('FengKuangMaJiangWaitFreeGames', 1);
                            }
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == $scatter && $scattersCount >= 3 ) 
                                    {
                                        $tmpChangeMap_[$r - 1][$p] = -1;
                                    }
                                    if( $reels['reel' . $r][$p] == $scatter2 && $scattersCount2 >= 2 ) 
                                    {
                                        $tmpChangeMap_[$r - 1][$p] = -1;
                                    }
                                }
                            }
                            $rrr = $slotSettings->GetGameData('FengKuangMaJiangReelsMap');
                            $slotSettings->SetGameData('FengKuangMaJiangChangeMap', $tmpChangeMap_);
                            $slotSettings->SetGameData('FengKuangMaJiangReelsMap', $reels);
                            $jsSpin = '' . json_encode($reels) . '';
                            $respinReelsStr = '' . json_encode($respinReels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"gameSession":"","dbg":"' . $scattersCount . '|' . $scattersCount2 . '|' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames') . '","balance":{"currency":"USD","amount":' . $slotSettings->GetBalance() . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"spin","stake":{"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"coin":1},"totalBet":' . ($postData['slotBet'] * $postData['slotLines']) . ',"totalWin":' . $totalWin . ',"scene":"' . $curScene . '","multiplier":1,"state":{' . $spinState . '},"reels":{"set":"' . $reelSet . '","positions":[' . implode(',', $reels['rp']) . '],"view":[[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '],[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '],[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ']]},"rewards":[' . $winString . '],"events":[' . $spinEvent . '],"roundEnded":' . $roundEnded . ',"version":"1.0.2"},"requestId":1,"roundEnded":' . $roundEnded . '}';
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","lastResponse":' . $response . ',"serverResponse":{"WaitFreeGames":' . $slotSettings->GetGameData('FengKuangMaJiangWaitFreeGames') . ',"StartBonusWin":' . $slotSettings->GetGameData('FengKuangMaJiangStartBonusWin') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('FengKuangMaJiangFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('FengKuangMaJiangCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('FengKuangMaJiangBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
