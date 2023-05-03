<?php 
namespace VanguardLTE\Games\FruitTumblingGTM
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
                        if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            if( !in_array($postData['slotLines'], $slotSettings->gameLine) || !in_array($postData['slotBet'], $slotSettings->Bet) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['slotLines'] * $postData['slotBet']) && $postData['slotEvent'] == 'bet' ) 
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
                        if( $postData['slotEvent'] == 'getSettings' ) 
                        {
                            $lastEvent = $slotSettings->GetHistory();
                            if( $lastEvent != 'NULL' ) 
                            {
                                if( isset($lastEvent->serverResponse->expSymbol) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'ExpSymbol', $lastEvent->serverResponse->expSymbol);
                                }
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'FruitTumblingGTMChangeMap', $lastEvent->serverResponse->ChangeMap);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FruitTumblingGTMReelsMap', $lastEvent->serverResponse->ReelsMap);
                            }
                            else
                            {
                                $slotSettings->SetGameData('FruitTumblingGTMChangeMap', [
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
                            $jsSet = json_encode($slotSettings);
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"responseEvent":"getSettings","slotLanguage":' . $lang . ',"serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('FruitTumblingGTMDealerCard');
                            $totalWin = $slotSettings->GetGameData('FruitTumblingGTMTotalWin');
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
                            if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                            {
                                $isGambleWin = 0;
                            }
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
                            $slotSettings->SetGameData('FruitTumblingGTMTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('FruitTumblingGTMDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('FruitTumblingGTMTotalWin');
                            $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $slotSettings->GetGameData('FruitTumblingGTMBonusWin') - $totalWin);
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
                            $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $slotSettings->GetGameData('FruitTumblingGTMBonusWin') + $totalWin);
                            $slotSettings->SetGameData('FruitTumblingGTMTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"bonusWin":' . $slotSettings->GetGameData('FruitTumblingGTMBonusWin') . ',"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                            $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
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
                            $linesId[6] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[7] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[8] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[9] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $spinWinLimit < ($postData['slotBet'] * $postData['slotLines'] * 2) ) 
                            {
                                $winType = 'none';
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
                                $slotSettings->SetGameData('FruitTumblingGTMBonusWin', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMFreeGames', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMTotalWin', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMFreeBalance', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMFreeMpl', 2);
                            }
                            else if( $postData['slotEvent'] != 'respin' ) 
                            {
                                $slotSettings->SetGameData('FruitTumblingGTMCurrentFreeGame', $slotSettings->GetGameData('FruitTumblingGTMCurrentFreeGame') + 1);
                                $bonusMpl = 2;
                                $slotSettings->SetGameData('FruitTumblingGTMFreeMpl', 2);
                            }
                            else if( $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $slotSettings->GetGameData('FruitTumblingGTMFreeMpl') < 16 ) 
                                {
                                    $slotSettings->SetGameData('FruitTumblingGTMFreeMpl', $slotSettings->GetGameData('FruitTumblingGTMFreeMpl') * 2);
                                }
                                if( $slotSettings->GetGameData('FruitTumblingGTMCurrentFreeGame') > 0 ) 
                                {
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
                                else
                                {
                                    $bonusMpl = 1;
                                }
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
                                    0
                                ];
                                $wild = ['P_1'];
                                $scatter = 'SCAT';
                                $tmpChangeMap = $slotSettings->GetGameData('FruitTumblingGTMChangeMap');
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
                                $slotSettings->SetGameData('FruitTumblingGTMChangeMap', $tmpChangeMap_);
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $respinReels = [
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    [], 
                                    []
                                ];
                                if( $postData['slotEvent'] == 'respin' ) 
                                {
                                    $reels = $slotSettings->GetGameData('FruitTumblingGTMReelsMap');
                                    $reels_ = $slotSettings->GetGameData('FruitTumblingGTMReelsMap');
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $tmpChangeMap[$r - 1][2] == -1 && $tmpChangeMap[$r - 1][1] != -1 && $tmpChangeMap[$r - 1][0] != -1 ) 
                                        {
                                            $rs = 0;
                                            if( $rs <= 100 ) 
                                            {
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][1];
                                                $reels['reel' . $r][1] = $reels['reel' . $r][0];
                                                $reels['reel' . $r][0] = $rndSym;
                                                break;
                                            }
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][1] == -1 && $tmpChangeMap[$r - 1][2] != -1 && $tmpChangeMap[$r - 1][0] != -1 ) 
                                        {
                                            $rs = 0;
                                            if( $rs <= 100 ) 
                                            {
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][2];
                                                $reels['reel' . $r][1] = $reels['reel' . $r][0];
                                                $reels['reel' . $r][0] = $rndSym;
                                                break;
                                            }
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][0] == -1 && $tmpChangeMap[$r - 1][2] != -1 && $tmpChangeMap[$r - 1][1] != -1 ) 
                                        {
                                            $rs = 0;
                                            if( $rs <= 100 ) 
                                            {
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][2];
                                                $reels['reel' . $r][1] = $reels['reel' . $r][1];
                                                $reels['reel' . $r][0] = $rndSym;
                                                break;
                                            }
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][1] == -1 && $tmpChangeMap[$r - 1][2] == -1 && $tmpChangeMap[$r - 1][0] != -1 ) 
                                        {
                                            $rs = 0;
                                            if( $rs <= 100 ) 
                                            {
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][0];
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][1] = $rndSym;
                                                $respinReels[$r][] = $rndSym;
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][0] = $rndSym;
                                                break;
                                            }
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][1] == -1 && $tmpChangeMap[$r - 1][0] == -1 && $tmpChangeMap[$r - 1][2] != -1 ) 
                                        {
                                            $rs = 0;
                                            if( $rs <= 100 ) 
                                            {
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][2];
                                                $rndSym1 = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][1] = $rndSym1;
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][0] = $rndSym;
                                                break;
                                            }
                                            $respinReels[$r][] = $rndSym1;
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][0] == -1 && $tmpChangeMap[$r - 1][2] == -1 && $tmpChangeMap[$r - 1][1] != -1 ) 
                                        {
                                            for( $rs = 0; $rs <= 100; $rs++ ) 
                                            {
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $reels['reel' . $r][2] = $reels['reel' . $r][1];
                                                $rndSym1 = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][1] = $rndSym1;
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][0] = $rndSym;
                                                if( $slotSettings->CheckDuplicateSym($reels['reel' . $r]) ) 
                                                {
                                                    break;
                                                }
                                            }
                                            $respinReels[$r][] = $rndSym1;
                                            $respinReels[$r][] = $rndSym;
                                        }
                                        else if( $tmpChangeMap[$r - 1][0] == -1 && $tmpChangeMap[$r - 1][2] == -1 && $tmpChangeMap[$r - 1][1] == -1 ) 
                                        {
                                            for( $rs = 0; $rs <= 100; $rs++ ) 
                                            {
                                                $reels['reel' . $r] = $reels_['reel' . $r];
                                                $rndSym1 = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][2] = $rndSym1;
                                                $rndSym2 = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][1] = $rndSym2;
                                                $rndSym = $slotSettings->SymbolGame[rand(2, count($slotSettings->SymbolGame) - 1)];
                                                $reels['reel' . $r][0] = $rndSym;
                                                if( $slotSettings->CheckDuplicateSym($reels['reel' . $r]) ) 
                                                {
                                                    break;
                                                }
                                            }
                                            $respinReels[$r][] = $rndSym1;
                                            $respinReels[$r][] = $rndSym2;
                                            $respinReels[$r][] = $rndSym;
                                        }
                                    }
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
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable[$csym][1] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FruitTumblingGTMTotalWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    for( $wld = 0; $wld < 2; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                            $s[$wld] = 'P_1';
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k][1] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FruitTumblingGTMTotalWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    for( $wld = 0; $wld < 3; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                            $s[$wld] = 'P_1';
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k][2] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FruitTumblingGTMTotalWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    for( $wld = 0; $wld < 4; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                            $s[$wld] = 'P_1';
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k][2] - 1] = -1;
                                                    $tmpChangeMap_[3][$linesId[$k][3] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FruitTumblingGTMTotalWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                    for( $wld = 0; $wld < 5; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                            $s[$wld] = 'P_1';
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $tmpChangeMap_[0][$linesId[$k][0] - 1] = -1;
                                                    $tmpChangeMap_[1][$linesId[$k][1] - 1] = -1;
                                                    $tmpChangeMap_[2][$linesId[$k][2] - 1] = -1;
                                                    $tmpChangeMap_[3][$linesId[$k][3] - 1] = -1;
                                                    $tmpChangeMap_[4][$linesId[$k][4] - 1] = -1;
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('FruitTumblingGTMTotalWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                $slotSettings->SetGameData('FruitTumblingGTMChangeMap', $tmpChangeMap_);
                                $scattersWin = 0;
                                $scattersStr = '{';
                                $scattersCount = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
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
                                $scattersWin = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'];
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
                                    else
                                    {
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            else if( $slotSettings->GetGameData('FruitTumblingGTMTotalWin') > 0 ) 
                            {
                                $slotSettings->SetBalance($slotSettings->GetGameData('FruitTumblingGTMTotalWin'));
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $slotSettings->GetGameData('FruitTumblingGTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FruitTumblingGTMTotalWin', $totalWin);
                            }
                            else if( $postData['slotEvent'] == 'respin' ) 
                            {
                                $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $slotSettings->GetGameData('FruitTumblingGTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FruitTumblingGTMTotalWin', $slotSettings->GetGameData('FruitTumblingGTMTotalWin') + $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('FruitTumblingGTMBonusWin', 0);
                                $slotSettings->SetGameData('FruitTumblingGTMTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('FruitTumblingGTMFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('FruitTumblingGTMFreeBalance', $Balance);
                                    $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $totalWin);
                                    $slotSettings->SetGameData('FruitTumblingGTMFreeGames', $slotSettings->GetGameData('FruitTumblingGTMFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('FruitTumblingGTMBonusWin', $slotSettings->GetGameData('FruitTumblingGTMBonusWin') + $totalWin);
                                    $slotSettings->SetGameData('FruitTumblingGTMFreeGames', $slotSettings->slotFreeCount);
                                }
                            }
                            $slotSettings->SetGameData('FruitTumblingGTMReelsMap', $reels);
                            $jsSpin = '' . json_encode($reels) . '';
                            $respinReelsStr = '' . json_encode($respinReels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"ReelsMap":' . json_encode($slotSettings->GetGameData('FruitTumblingGTMReelsMap')) . ' ,"ChangeMap":' . json_encode($slotSettings->GetGameData('FruitTumblingGTMChangeMap')) . ',"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"bonusWin":' . $slotSettings->GetGameData('FruitTumblingGTMBonusWin') . ',"freeMpl":' . $slotSettings->GetGameData('FruitTumblingGTMFreeMpl') . ',"respinReels":' . $respinReelsStr . ',"respinWin":' . $slotSettings->GetGameData('FruitTumblingGTMTotalWin') . ',"totalFreeGames":' . $slotSettings->GetGameData('FruitTumblingGTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('FruitTumblingGTMCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('FruitTumblingGTMBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
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
