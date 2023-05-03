<?php 
namespace VanguardLTE\Games\HeavenlyRulerSW
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
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
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
                            $slotSettings->SetGameData('HeavenlyRulerSWBonusWin', 0);
                            $slotSettings->SetGameData('HeavenlyRulerSWFreeGames', 0);
                            $slotSettings->SetGameData('HeavenlyRulerSWCurrentFreeGame', 0);
                            $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', 0);
                            $slotSettings->SetGameData('HeavenlyRulerSWStartBonusWin', 0);
                            $slotSettings->SetGameData('HeavenlyRulerSWFreeBalance', 0);
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
                                $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                $rp2 = '[' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0] . ']';
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1] . ']');
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2] . ']');
                                $bet = $lastEvent->serverResponse->bet;
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
                                $bet = $slotSettings->Bet[0];
                                $prevResult = '';
                            }
                            $jsSet = json_encode($slotSettings);
                            $Balance = $slotSettings->GetBalance();
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE6cGxheWVyMTYxNzQ2MTEzMzIzMzpzd19ocjp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTYxNzQ2MTEzNywiaXNzIjoic2t5d2luZGdyb3VwIn0.F8T-33ov4G_GEcMNxx81DC_52ZEOxFyQax5q-E_OSBT-qJ4xjxSmMr4r6BIr7saAN8DMCU45t4h1AdDOtOP3EQ","balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init","name":"Heavenly Ruler","gameId":"sw_hr","settings":{"winMax":607610,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $bet . ',"stakeMax":' . $slotSettings->Bet[count($slotSettings->Bet) - 1] . ',"stakeMin":' . $slotSettings->Bet[0] . ',"maxTotalStake":' . ($slotSettings->Bet[count($slotSettings->Bet) - 1] * 30) . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"slot":{"sets":{"main":{"reels":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . '],[' . implode(',', $slotSettings->reelStrip4) . '],[' . implode(',', $slotSettings->reelStrip5) . ']]},"freeSpins":{"reels":[[' . implode(',', $slotSettings->reelStripBonus1) . '],[' . implode(',', $slotSettings->reelStripBonus2) . '],[' . implode(',', $slotSettings->reelStripBonus3) . '],[' . implode(',', $slotSettings->reelStripBonus4) . '],[' . implode(',', $slotSettings->reelStripBonus5) . ']]}},"reels":{"set":"main","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"linesDefinition":{"fixedLinesCount":30},"paytable":{"stake":{"value":1,"multiplier":1,"payouts":[[0,10,50,500,10000],[0,5,25,100,500],[0,0,20,75,300],[0,0,15,40,200],[0,0,10,25,150],[0,0,5,20,125],[0,0,5,15,100],[0,2,5,15,100],[0,30,60,150,600]]}},"lines":[[1,1,1,1,1],[0,0,0,0,0],[2,2,2,2,2],[0,1,2,1,0],[2,1,0,1,2],[0,0,1,0,0],[2,2,1,2,2],[1,0,0,0,1],[1,2,2,2,1],[1,1,0,1,1],[1,1,2,1,1],[1,0,1,0,1],[1,2,1,2,1],[0,1,0,1,0],[2,1,2,1,2],[2,0,2,0,2],[0,2,0,2,0],[0,1,1,1,0],[2,1,1,1,2],[0,2,2,2,0],[2,0,0,0,2],[0,0,2,0,0],[2,2,0,2,2],[0,2,1,2,0],[2,0,1,0,2],[1,0,2,0,1],[1,2,0,2,1],[0,0,1,2,2],[2,2,1,0,0],[1,0,1,2,1]]},"stake":null,"version":"1.2.1"},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true,"brandInfo":{"name":"MASTER","title":"Master Entity"},"clientSettings":{"ivp":false,"hsWrn":false,"sPblkCtBk":false,"sPrlkCtBk":false}}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('HeavenlyRulerSWDealerCard');
                            $totalWin = $slotSettings->GetGameData('HeavenlyRulerSWTotalWin');
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
                            $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('HeavenlyRulerSWDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('HeavenlyRulerSWTotalWin');
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
                            $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', $totalWin);
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
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[6] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[7] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[11] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[12] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[13] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[14] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[15] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[17] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $linesId[20] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[21] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[22] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[23] = [
                                1, 
                                3, 
                                2, 
                                3, 
                                1
                            ];
                            $linesId[24] = [
                                3, 
                                1, 
                                2, 
                                1, 
                                3
                            ];
                            $linesId[25] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[26] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[27] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[28] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[29] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            if( $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') <= $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') && $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') > 0 ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                            }
                            $postData['slotBet'] = $postData['bet'];
                            $postData['slotLines'] = $postData['lines'];
                            $allbet = $postData['slotBet'] * $postData['slotLines'];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
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
                                $slotSettings->SetGameData('HeavenlyRulerSWStartBonusWin', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWBonusWin', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWFreeGames', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWCurrentFreeGame', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWFreeBalance', 0);
                            }
                            else
                            {
                                $slotSettings->SetGameData('HeavenlyRulerSWCurrentFreeGame', $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') + 1);
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
                                $scatter = '12';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || $csym == $wild ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $csym == 11 ) 
                                            {
                                                $psym = 7;
                                            }
                                            if( $csym == 10 ) 
                                            {
                                                $psym = 6;
                                            }
                                            if( $csym == 9 || $csym == 8 ) 
                                            {
                                                $psym = 5;
                                            }
                                            if( $csym == 7 || $csym == 6 ) 
                                            {
                                                $psym = 4;
                                            }
                                            if( $csym == 5 || $csym == 4 ) 
                                            {
                                                $psym = 3;
                                            }
                                            if( $csym == 3 || $csym == 2 ) 
                                            {
                                                $psym = 2;
                                            }
                                            if( $csym == 1 ) 
                                            {
                                                $psym = 1;
                                            }
                                            if( $csym == 0 ) 
                                            {
                                                $psym = 0;
                                            }
                                            $s = [];
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            if( $s[0] == $csym ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable[$csym][1] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $psym . ',0]}';
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $psym . ',1]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
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
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $psym . ',2]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $psym . ',3]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $psym . ',4]}';
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
                                $scattersStrArr = [];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStrArr[] = '[' . $p . ',' . ($r - 1) . ']';
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'];
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                    $tmpStringWin = '{"reward":"scatter","payout":' . $scattersWin . ',"lineMultiplier":1,"positions":[' . implode(',', $scattersStrArr) . '],"paytable":[9,' . ($scattersCount - 1) . ']}';
                                    array_push($lineWins, $tmpStringWin);
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr .= '"scattersType":"win",';
                                    $tmpStringWin = '{"reward":"scatter","payout":' . $scattersWin . ',"lineMultiplier":1,"positions":[' . implode(',', $scattersStrArr) . '],"paytable":[9,' . ($scattersCount - 1) . ']}';
                                    array_push($lineWins, $tmpStringWin);
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
                                $slotSettings->SetGameData('HeavenlyRulerSWBonusWin', $slotSettings->GetGameData('HeavenlyRulerSWBonusWin') + $totalWin);
                                $Balance = $slotSettings->GetGameData('HeavenlyRulerSWFreeBalance');
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData('HeavenlyRulerSWFreeGames') - $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('HeavenlyRulerSWBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('HeavenlyRulerSWStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('HeavenlyRulerSWFreeGames');
                                $spinEvent = '';
                                $roundEnded = 'false';
                                $curScene = 'freeSpins';
                                $reelSet = 'freeSpins';
                            }
                            else
                            {
                                $spinState = '"currentScene":"main","multiplier":1';
                                $spinEvent = '';
                                $roundEnded = 'true';
                                $curScene = 'main';
                                $reelSet = 'main';
                                $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                $reels0 = $slotSettings->GetReelStrips($reels['rp'], $postData['slotEvent']);
                                $rpTmp = '[' . $reels0['reel1'][0] . ',' . $reels0['reel2'][0] . ',' . $reels0['reel3'][0] . ',' . $reels0['reel4'][0] . ',' . $reels0['reel5'][0] . '],[' . $reels0['reel1'][1] . ',' . $reels0['reel2'][1] . ',' . $reels0['reel3'][1] . ',' . $reels0['reel4'][1] . ',' . $reels0['reel5'][1] . '],[' . $reels0['reel1'][2] . ',' . $reels0['reel2'][2] . ',' . $reels0['reel3'][2] . ',' . $reels0['reel4'][2] . ',' . $reels0['reel5'][2] . ']';
                                $slotSettings->SetGameData('HeavenlyRulerSWStartBonusInfo', ',"reels":{"view":[' . $rpTmp . '],"positions":[' . implode(',', $reels['rp']) . ']}');
                                if( $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('HeavenlyRulerSWFreeBalance', $Balance);
                                    $slotSettings->SetGameData('HeavenlyRulerSWFreeGames', $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('HeavenlyRulerSWFreeBalance', $Balance);
                                    $slotSettings->SetGameData('HeavenlyRulerSWStartBonusWin', $totalWin);
                                    $slotSettings->SetGameData('HeavenlyRulerSWBonusWin', 0);
                                    $slotSettings->SetGameData('HeavenlyRulerSWFreeGames', $slotSettings->slotFreeCount);
                                }
                                $spinEvent = '{"id":"freeSpinsStart","amount":' . $slotSettings->slotFreeCount . ',"reels":{"view":[' . $rpTmp . '],"set":"freeSpins","positions":[' . implode(',', $reels['rp']) . ']},"triggeredSceneId":"freeSpins","triggerSymbols":[' . implode(',', $scattersStrArr) . ']}';
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData('HeavenlyRulerSWFreeGames') - $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData('HeavenlyRulerSWBonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData('HeavenlyRulerSWStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('HeavenlyRulerSWFreeGames');
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            if( $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') <= $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') && $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') > 0 ) 
                            {
                                $spinState = '"currentScene":"main","multiplier":1,"freeSpinsWin":' . $slotSettings->GetGameData('HeavenlyRulerSWBonusWin') . ',"freeSpinsCount":0,"initialFreeSpinWin":' . $slotSettings->GetGameData('HeavenlyRulerSWStartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData('HeavenlyRulerSWFreeGames');
                                $spinEvent = '{"id":"freeSpinsEnd","reels":{"set":"main","view":[[6,11,1,4,6],[9,5,8,11,9],[3,9,11,4,4]],"positions":[43,82,23,29,66]}}';
                                $roundEnded = 'true';
                                $curScene = 'freeSpins';
                                $slotSettings->SetGameData('HeavenlyRulerSWBonusWin', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWFreeGames', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWCurrentFreeGame', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWTotalWin', 0);
                                $slotSettings->SetGameData('HeavenlyRulerSWFreeBalance', 0);
                            }
                            $response = '{"gameSession":"","dbg":"' . $postData['slotEvent'] . '|' . $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') . '|' . $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') . '","balance":{"currency":"","amount":' . $slotSettings->GetBalance() . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"spin","stake":{"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"coin":1},"totalBet":' . ($postData['slotBet'] * $postData['slotLines']) . ',"totalWin":' . $totalWin . ',"scene":"' . $curScene . '","multiplier":1,"state":{' . $spinState . '},"reels":{"set":"' . $reelSet . '","positions":[' . implode(',', $reels['rp']) . '],"view":[[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '],[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '],[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ']]},"rewards":[' . $winString . '],"events":[' . $spinEvent . '],"roundEnded":' . $roundEnded . ',"version":"1.0.2"},"requestId":1,"roundEnded":' . $roundEnded . '}';
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","lastResponse":' . $response . ',"serverResponse":{"StartBonusWin":' . $slotSettings->GetGameData('HeavenlyRulerSWStartBonusWin') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('HeavenlyRulerSWFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('HeavenlyRulerSWCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('HeavenlyRulerSWBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
