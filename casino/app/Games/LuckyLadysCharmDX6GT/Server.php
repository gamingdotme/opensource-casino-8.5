<?php 
namespace VanguardLTE\Games\LuckyLadysCharmDX6GT
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
                        $extraMpl = 1;
                        if( isset($postData['slotExtraBet']) && $postData['slotExtraBet'] == 'true' ) 
                        {
                            $extraMpl = 2;
                        }
                        if( $postData['slotEvent'] == 'bet' && $slotSettings->GetBalance() < ($postData['slotBet'] * $postData['slotLines'] * $extraMpl) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"text_error"}';
                            exit( $response );
                        }
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
                            }
                            $jsSet = json_encode($slotSettings);
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"responseEvent":"getSettings","slotLanguage":' . $lang . ',"serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('LuckyLadysCharmDX6GTDealerCard');
                            $totalWin = $slotSettings->GetGameData('LuckyLadysCharmDX6GTTotalWin');
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
                            $slotSettings->SetGameData('LuckyLadysCharmDX6GTTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('LuckyLadysCharmDX6GTDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('LuckyLadysCharmDX6GTTotalWin');
                            $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', $slotSettings->GetGameData('LuckyLadysCharmDX6GTBonusWin') - $totalWin);
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
                            $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', $slotSettings->GetGameData('LuckyLadysCharmDX6GTBonusWin') + $totalWin);
                            $slotSettings->SetGameData('LuckyLadysCharmDX6GTTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"bonusWin":' . $slotSettings->GetGameData('LuckyLadysCharmDX6GTBonusWin') . ',"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
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
                                2, 
                                2
                            ];
                            $linesId[1] = [
                                1, 
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
                                3, 
                                3
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[4] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[5] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[6] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[7] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[8] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[9] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $postData['slotExtraBet'] == 'true' ) 
                            {
                                $extraMpl = 2;
                            }
                            else
                            {
                                $extraMpl = 1;
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = ($postData['slotBet'] * $postData['slotLines'] * $extraMpl) / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines']);
                                $slotSettings->SetBalance(-1 * ($postData['slotBet'] * $postData['slotLines'] * $extraMpl), $postData['slotEvent']);
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTExpSymbol', 'NONE');
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', 0);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTFreeGames', 0);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTTotalWin', 0);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTFreeBalance', 0);
                            }
                            else
                            {
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTCurrentFreeGame', $slotSettings->GetGameData('LuckyLadysCharmDX6GTCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                                if( $postData['slotExtraBet'] == 'true' ) 
                                {
                                    $bonusMpl = $slotSettings->slotFreeMpl * 2;
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
                                $wild = 'P_1';
                                $scatter = 'SCAT';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $tmpReels = $reels;
                                if( $postData['slotExtraBet'] != 'true' ) 
                                {
                                    $reels['reel6'] = [
                                        '', 
                                        '', 
                                        '', 
                                        ''
                                    ];
                                }
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
                                            $s[5] = $reels['reel6'][$linesId[$k][5] - 1];
                                            if( ($s[0] == $csym || $s[0] == $wild) && ($s[1] == $csym || $s[1] == $wild) ) 
                                            {
                                                $mpl = 1;
                                                if( $s[0] == $wild || $s[1] == $wild ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                else if( $s[0] == $wild && $s[1] == $wild ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"],"winReel6":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || $s[0] == $wild) && ($s[1] == $csym || $s[1] == $wild) && ($s[2] == $csym || $s[2] == $wild) ) 
                                            {
                                                $mpl = 1;
                                                if( $s[0] == $wild || $s[1] == $wild || $s[2] == $wild ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                else if( $s[0] == $wild && $s[1] == $wild && $s[2] == $wild ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"],"winReel6":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || $s[0] == $wild) && ($s[1] == $csym || $s[1] == $wild) && ($s[2] == $csym || $s[2] == $wild) && ($s[3] == $csym || $s[3] == $wild) ) 
                                            {
                                                $mpl = 1;
                                                if( $s[0] == $wild || $s[1] == $wild || $s[2] == $wild || $s[3] == $wild ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                else if( $s[0] == $wild && $s[1] == $wild && $s[2] == $wild && $s[3] == $wild ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"],"winReel6":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || $s[0] == $wild) && ($s[1] == $csym || $s[1] == $wild) && ($s[2] == $csym || $s[2] == $wild) && ($s[3] == $csym || $s[3] == $wild) && ($s[4] == $csym || $s[4] == $wild) ) 
                                            {
                                                $mpl = 1;
                                                if( $s[0] == $wild || $s[1] == $wild || $s[2] == $wild || $s[3] == $wild || $s[4] == $wild ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                else if( $s[0] == $wild && $s[1] == $wild && $s[2] == $wild && $s[3] == $wild && $s[4] == $wild ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel6":["none","none"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || $s[0] == $wild) && ($s[1] == $csym || $s[1] == $wild) && ($s[2] == $csym || $s[2] == $wild) && ($s[3] == $csym || $s[3] == $wild) && ($s[4] == $csym || $s[4] == $wild) && ($s[5] == $csym || $s[5] == $wild) ) 
                                            {
                                                $mpl = 1;
                                                if( $s[0] == $wild || $s[1] == $wild || $s[2] == $wild || $s[3] == $wild || $s[4] == $wild || $s[5] == $wild ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                else if( $s[0] == $wild && $s[1] == $wild && $s[2] == $wild && $s[3] == $wild && $s[4] == $wild && $s[5] == $wild ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][6] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":6,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel6":[' . ($linesId[$k][5] - 1) . ',"' . $s[5] . '"]}';
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
                                $scatter = 'SCAT';
                                for( $r = 1; $r <= 6; $r++ ) 
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
                                if( $scattersCount >= 3 ) 
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
                                        if( $winType != 'bonus' && $scattersCount >= 3 ) 
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
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('LuckyLadysCharmDX6GTFreeGames') <= $slotSettings->GetGameData('LuckyLadysCharmDX6GTCurrentFreeGame') && $winType != 'bonus' && $slotSettings->GetGameData('LuckyLadysCharmDX6GTTotalWin') + $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($slotSettings->GetGameData('LuckyLadysCharmDX6GTTotalWin') + $totalWin);
                            }
                            else if( $postData['slotEvent'] != 'freespin' && $winType != 'bonus' && $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTTotalWin', $totalWin);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', $slotSettings->GetGameData('LuckyLadysCharmDX6GTBonusWin') + $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTTotalWin', $totalWin);
                                $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', 0);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('LuckyLadysCharmDX6GTFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTFreeBalance', $Balance);
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', $totalWin);
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTFreeGames', $slotSettings->GetGameData('LuckyLadysCharmDX6GTFreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTExpSymbol', $slotSettings->SymbolGameExp[0]);
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTBonusWin', $totalWin);
                                    $slotSettings->SetGameData('LuckyLadysCharmDX6GTFreeGames', $slotSettings->slotFreeCount);
                                }
                            }
                            $reels = $tmpReels;
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            if( $slotSettings->GetGameData('LuckyLadysCharmDX6GTExpSymbol') != 'NONE' ) 
                            {
                                $expSymbol = '"expSymbol":"' . $slotSettings->GetGameData('LuckyLadysCharmDX6GTExpSymbol') . '",';
                            }
                            else
                            {
                                $expSymbol = '';
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"slotExtraBet":"' . $postData['slotExtraBet'] . '","bonusWin":' . $slotSettings->GetGameData('LuckyLadysCharmDX6GTBonusWin') . ',"totalFreeGames":' . $slotSettings->GetGameData('LuckyLadysCharmDX6GTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('LuckyLadysCharmDX6GTCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $postData['slotBet'] * $extraMpl, $postData['slotLines'], $reportWin, $postData['slotEvent']);
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
