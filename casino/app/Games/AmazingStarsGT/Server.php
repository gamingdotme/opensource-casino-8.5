<?php 
namespace VanguardLTE\Games\AmazingStarsGT
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'ScatterWin', $lastEvent->serverResponse->scattersWinTmp);
                            }
                            $slotSettings->slotJackpot0 = $slotSettings->slotJackpot[0];
                            $slotSettings->slotJackpot = $slotSettings->slotJackpot[1];
                            $jsSet = json_encode($slotSettings);
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"responseEvent":"getSettings","slotLanguage":' . $lang . ',"serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('AmazingStarsGTDealerCard');
                            $totalWin = $slotSettings->GetGameData('AmazingStarsGTTotalWin');
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
                            $slotSettings->SetGameData('AmazingStarsGTTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('AmazingStarsGTDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('AmazingStarsGTTotalWin');
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
                            $slotSettings->SetGameData('AmazingStarsGTTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                            $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' ) 
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
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = ($postData['slotBet'] * $postData['slotLines']) / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines'], false);
                                $slotSettings->SetBalance(-1 * ($postData['slotBet'] * $postData['slotLines']), $postData['slotEvent']);
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('AmazingStarsGTBonusWin', 0);
                                $slotSettings->SetGameData('AmazingStarsGTFreeGames', 0);
                                $slotSettings->SetGameData('AmazingStarsGTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('AmazingStarsGTTotalWin', 0);
                                $slotSettings->SetGameData('AmazingStarsGTFreeBalance', 0);
                                $slotSettings->SetGameData('AmazingStarsGTScatterWin', 0);
                                $slotSettings->SetGameData('AmazingStarsGTFreeStacked', [
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false', 
                                    'false'
                                ]);
                                for( $ii = 0; $ii < 16; $ii++ ) 
                                {
                                    $slotSettings->SetGameData('AmazingStarsGTFreeStacked.' . $ii, 'false');
                                }
                            }
                            else
                            {
                                $slotSettings->SetGameData('AmazingStarsGTCurrentFreeGame', $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                                if( $slotSettings->GetGameData('AmazingStarsGTFreeGames') == $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') ) 
                                {
                                    $jackState = $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines'], true);
                                }
                            }
                            $Balance = $slotSettings->GetBalance();
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $isSlotJack = false;
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
                                $isSlotJack = false;
                                $wild = ['NONE'];
                                $scatter = 'SCAT';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                if( isset($jackState) && $jackState['isJackPay'] ) 
                                {
                                    $rline = 1;
                                    for( $jl = 1; $jl <= 5; $jl++ ) 
                                    {
                                        if( $jackState['isJackId'] == 0 ) 
                                        {
                                            $jreel = $slotSettings->PutBonusToLine($jl, $linesId[$rline][$jl - 1], 'SCAT');
                                            $reels['reel' . $jl] = [
                                                'SCAT', 
                                                'SCAT', 
                                                'SCAT', 
                                                ''
                                            ];
                                            $reels['rp'][$jl - 1] = $jreel['rp'];
                                            $winType = 'none';
                                        }
                                        else
                                        {
                                            $jreel = $slotSettings->PutBonusToLine($jl, $linesId[$rline][$jl - 1], 'P_1');
                                            $reels['reel' . $jl] = $jreel['reel'];
                                            $reels['rp'][$jl - 1] = $jreel['rp'];
                                            $winType = 'none';
                                        }
                                    }
                                }
                                $reelsTmp = $reels;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $stackCount = 1;
                                    $stack0 = $slotSettings->GetGameData('AmazingStarsGTFreeStacked');
                                    for( $ii = 1; $ii <= 5; $ii++ ) 
                                    {
                                        if( $stack0[$stackCount] == 'true' ) 
                                        {
                                            $reels['reel' . $ii][0] = 'SCAT';
                                        }
                                        $stackCount++;
                                        if( $stack0[$stackCount] == 'true' ) 
                                        {
                                            $reels['reel' . $ii][1] = 'SCAT';
                                        }
                                        $stackCount++;
                                        if( $stack0[$stackCount] == 'true' ) 
                                        {
                                            $reels['reel' . $ii][2] = 'SCAT';
                                        }
                                        $stackCount++;
                                    }
                                }
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable[$csym]) || $postData['slotEvent'] == 'freespin' ) 
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
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                if( $csym == 'P_1' ) 
                                                {
                                                    $isSlotJack = true;
                                                }
                                                if( $isSlotJack && !isset($slotSettings->Jackpots['jackPay']) ) 
                                                {
                                                    $isSlotJack = false;
                                                }
                                                else
                                                {
                                                    if( $cWins[$k] < $tmpWin && !$isSlotJack ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                                    }
                                                    if( $isSlotJack ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":"' . $slotSettings->Jackpots['jackPay'] . '","stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('AmazingStarsGTBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if( $cWins[$k] > 0 && $tmpStringWin != '' || $isSlotJack && $tmpStringWin != '' ) 
                                    {
                                        array_push($lineWins, $tmpStringWin);
                                        $totalWin += $cWins[$k];
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '{';
                                $scattersStr0 = '{';
                                $scattersCount = 0;
                                $isSlotJack0 = false;
                                $jackSymCnt = 0;
                                $jackSymCnt0 = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '_' . $p . '":[' . $p . ',"' . $scatter . '"],');
                                            $scattersStr0 .= ('"winReel' . $r . '_' . $p . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                        if( $reels['reel' . $r][$p] == 'P_1' ) 
                                        {
                                            $jackSymCnt++;
                                        }
                                        if( $reels['reel' . $r][$p] == 'SCAT' ) 
                                        {
                                            $jackSymCnt0++;
                                        }
                                    }
                                }
                                if( $jackSymCnt0 >= 15 ) 
                                {
                                    $totalWin = 0;
                                    $isSlotJack0 = true;
                                }
                                $scattersWinTmp = 0;
                                if( $slotSettings->GetGameData('AmazingStarsGTFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTScatterWin');
                                    $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    $spinWinLimit = $cBank;
                                }
                                else if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTScatterWin');
                                }
                                else if( $winType == 'bonus' ) 
                                {
                                    $scattersWinTmp = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'] - $slotSettings->GetGameData('AmazingStarsGTScatterWin');
                                }
                                else
                                {
                                    $scattersWin = 0;
                                }
                                if( $scattersCount >= 3 && $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                    $scattersWin = 0;
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
                                if( $isSlotJack0 && $isSlotJack ) 
                                {
                                    $totalWin = 0;
                                    $winType = 'none';
                                }
                                if( $isSlotJack0 ) 
                                {
                                    $totalWin = 0;
                                    break;
                                }
                                if( $isSlotJack ) 
                                {
                                    $totalWin = $slotSettings->Jackpots['jackPay'];
                                    break;
                                }
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $postData['slotEvent'] != 'freespin' ) 
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
                                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['slotBet'] * $postData['slotLines']) ) 
                                        {
                                        }
                                    }
                                }
                                if( $jackSymCnt0 >= 15 && !$jackState['isJackPay'] ) 
                                {
                                }
                                else if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin + $scattersWinTmp) && ($postData['slotEvent'] == 'freespin' || $winType == 'bonus') ) 
                                {
                                }
                                else
                                {
                                    if( $slotSettings->GetGameData('AmazingStarsGTFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $scattersWin = 0;
                                        $totalWin = 0;
                                        $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        $spinWinLimit = $cBank;
                                        if( $totalWin <= $spinWinLimit ) 
                                        {
                                            break;
                                        }
                                    }
                                    if( $scattersCount >= 3 && $winType != 'bonus' && $postData['slotEvent'] != 'freespin' ) 
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
                            if( $totalWin + $scattersWinTmp > 0 && !$isSlotJack && !$isSlotJack0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * ($totalWin + $scattersWinTmp));
                                if( $scattersWinTmp > 0 ) 
                                {
                                    $slotSettings->SetGameData('AmazingStarsGTScatterWin', $slotSettings->GetGameData('AmazingStarsGTScatterWin') + $scattersWinTmp);
                                }
                            }
                            if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('AmazingStarsGTFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') && $winType != 'bonus' && $slotSettings->GetGameData('AmazingStarsGTTotalWin') + $totalWin + $slotSettings->GetGameData('AmazingStarsGTScatterWin') > 0 && !$isSlotJack && !$isSlotJack0 ) 
                            {
                                $slotSettings->SetBalance($slotSettings->GetGameData('AmazingStarsGTTotalWin') + $totalWin + $slotSettings->GetGameData('AmazingStarsGTScatterWin'));
                            }
                            else if( $postData['slotEvent'] != 'freespin' && $winType != 'bonus' && $totalWin > 0 && !$isSlotJack && !$isSlotJack0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('AmazingStarsGTBonusWin', $slotSettings->GetGameData('AmazingStarsGTBonusWin') + $totalWin);
                                $slotSettings->SetGameData('AmazingStarsGTTotalWin', $slotSettings->GetGameData('AmazingStarsGTTotalWin') + $totalWin);
                                $totalWin = $slotSettings->GetGameData('AmazingStarsGTBonusWin');
                                $Balance = $slotSettings->GetGameData('AmazingStarsGTFreeBalance');
                                $stackCount = 1;
                                $stackedA = $slotSettings->GetGameData('AmazingStarsGTFreeStacked');
                                for( $i = 1; $i <= 5; $i++ ) 
                                {
                                    if( $reels['reel' . $i][0] == 'SCAT' ) 
                                    {
                                        $stackedA[$stackCount] = 'true';
                                    }
                                    $stackCount++;
                                    if( $reels['reel' . $i][1] == 'SCAT' ) 
                                    {
                                        $stackedA[$stackCount] = 'true';
                                    }
                                    $stackCount++;
                                    if( $reels['reel' . $i][2] == 'SCAT' ) 
                                    {
                                        $stackedA[$stackCount] = 'true';
                                    }
                                    $stackCount++;
                                }
                                $slotSettings->SetGameData('AmazingStarsGTFreeStacked', $stackedA);
                            }
                            else
                            {
                                $slotSettings->SetGameData('AmazingStarsGTTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 && $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( $slotSettings->GetGameData('AmazingStarsGTFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('AmazingStarsGTFreeBalance', $Balance);
                                    $slotSettings->SetGameData('AmazingStarsGTBonusWin', $totalWin);
                                    $slotSettings->SetGameData('AmazingStarsGTFreeGames', $slotSettings->GetGameData('AmazingStarsGTFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('AmazingStarsGTFreeBalance', $Balance);
                                    $slotSettings->SetGameData('AmazingStarsGTBonusWin', $totalWin);
                                    $slotSettings->SetGameData('AmazingStarsGTFreeGames', $slotSettings->slotFreeCount);
                                    $stackCount = 1;
                                    $stackedA = $slotSettings->GetGameData('AmazingStarsGTFreeStacked');
                                    for( $i = 1; $i <= 5; $i++ ) 
                                    {
                                        if( $reels['reel' . $i][0] == 'SCAT' ) 
                                        {
                                            $stackedA[$stackCount] = 'true';
                                        }
                                        $stackCount++;
                                        if( $reels['reel' . $i][1] == 'SCAT' ) 
                                        {
                                            $stackedA[$stackCount] = 'true';
                                        }
                                        $stackCount++;
                                        if( $reels['reel' . $i][2] == 'SCAT' ) 
                                        {
                                            $stackedA[$stackCount] = 'true';
                                        }
                                        $stackCount++;
                                    }
                                    $slotSettings->SetGameData('AmazingStarsGTFreeStacked', $stackedA);
                                }
                            }
                            $reels = $reelsTmp;
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $AmazingStarsGTFreeStacked = [
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0', 
                                '0'
                            ];
                            $aws = $slotSettings->GetGameData('AmazingStarsGTFreeStacked');
                            for( $i = 0; $i <= 15; $i++ ) 
                            {
                                if( $aws[$i] == 'true' ) 
                                {
                                    $AmazingStarsGTFreeStacked[$i] = '1';
                                }
                            }
                            if( $slotSettings->GetGameData('AmazingStarsGTFreeGames') <= $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') && $postData['slotEvent'] == 'freespin' && !$isSlotJack && !$isSlotJack0 ) 
                            {
                                $totalWin += $slotSettings->GetGameData('AmazingStarsGTScatterWin');
                                $reportWin = $totalWin;
                                $scattersStr0 .= '"scattersType":"win",';
                                $scattersStr0 .= ('"scattersWin":' . $slotSettings->GetGameData('AmazingStarsGTScatterWin') . '}');
                                $scattersStr = $scattersStr0;
                            }
                            if( !isset($jackState) ) 
                            {
                                $jackState = [];
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"$aws":[' . implode(',', $slotSettings->GetGameData('AmazingStarsGTFreeStacked')) . '],"$jackState":' . json_encode($jackState) . ',"scattersWinTmp":' . $slotSettings->GetGameData('AmazingStarsGTScatterWin') . ',"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"stackedWilds":[' . implode(',', $AmazingStarsGTFreeStacked) . '],"slotJackpot0":' . $slotSettings->slotJackpot[0] . ',"slotJackpot":' . $slotSettings->slotJackpot[1] . ',"totalFreeGames":' . $slotSettings->GetGameData('AmazingStarsGTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('AmazingStarsGTCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
                            if( isset($slotSettings->Jackpots['jackPay']) ) 
                            {
                                $slotSettings->SaveLogReport($response, $postData['slotBet'], $postData['slotLines'], $slotSettings->Jackpots['jackPay'], 'JPG');
                            }
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
