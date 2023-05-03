<?php 
namespace VanguardLTE\Games\JustJewels
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
                        $slotSettings->SetGameData('JustJewelsFreeGames', 0);
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
                        $dealerCard = $slotSettings->GetGameData('JustJewelsDealerCard');
                        $totalWin = $slotSettings->GetGameData('JustJewelsTotalWin');
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
                        $slotSettings->SetGameData('JustJewelsTotalWin', $totalWin);
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
                        $slotSettings->SetGameData('JustJewelsDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('JustJewelsTotalWin');
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
                        $slotSettings->SetGameData('JustJewelsTotalWin', $totalWin);
                        $slotSettings->SetBalance($gambleWin);
                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                        $afterBalance = $slotSettings->GetBalance();
                        $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                        $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
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
                            $slotSettings->SetGameData('JustJewelsBonusWin', 0);
                            $slotSettings->SetGameData('JustJewelsFreeGames', 0);
                            $slotSettings->SetGameData('JustJewelsCurrentFreeGame', 0);
                            $slotSettings->SetGameData('JustJewelsTotalWin', 0);
                            $slotSettings->SetGameData('JustJewelsFreeBalance', 0);
                        }
                        else
                        {
                            $slotSettings->SetGameData('JustJewelsCurrentFreeGame', $slotSettings->GetGameData('JustJewelsCurrentFreeGame') + 1);
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
                            $wild = [''];
                            $scatter = 'SCAT';
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
                                            $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                        {
                                            $mpl = 1;
                                            if( in_array($s[4], $wild) && in_array($s[3], $wild) ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( in_array($s[4], $wild) || in_array($s[3], $wild) ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"]}';
                                            }
                                        }
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
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                        {
                                            $mpl = 1;
                                            if( in_array($s[4], $wild) && in_array($s[3], $wild) && in_array($s[2], $wild) ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( in_array($s[4], $wild) || in_array($s[3], $wild) || in_array($s[2], $wild) ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"],"winReel2":["none","none"]}';
                                            }
                                        }
                                        if( ($s[3] == $csym || in_array($s[3], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                        {
                                            $mpl = 1;
                                            if( in_array($s[3], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( in_array($s[3], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel1":["none","none"],"winReel5":["none","none"]}';
                                            }
                                        }
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
                                            $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                        {
                                            $mpl = 1;
                                            if( in_array($s[4], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                            {
                                                $mpl = 1;
                                            }
                                            else if( in_array($s[4], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                            {
                                                $mpl = $slotSettings->slotWildMpl;
                                            }
                                            $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"]}';
                                            }
                                        }
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
                                            $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'];
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('JustJewelsBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                            if( $postData['slotEvent'] == 'freespin' && $slotSettings->slotMultiScatter ) 
                            {
                                $scatter = $tmp_scatter;
                            }
                            $scattersWin = 0;
                            $scattersStr = '{';
                            $scattersCount = 0;
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                if( $reels['reel' . $r][0] == $scatter ) 
                                {
                                    $scattersCount++;
                                    $scattersStr .= ('"winReel' . $r . '":[' . '0' . ',"' . $scatter . '"],');
                                }
                                else if( $reels['reel' . $r][1] == $scatter ) 
                                {
                                    $scattersCount++;
                                    $scattersStr .= ('"winReel' . $r . '":[' . '1' . ',"' . $scatter . '"],');
                                }
                                else if( $reels['reel' . $r][2] == $scatter ) 
                                {
                                    $scattersCount++;
                                    $scattersStr .= ('"winReel' . $r . '":[' . '2' . ',"' . $scatter . '"],');
                                }
                            }
                            $scattersWin = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'];
                            if( $slotSettings->slotBonusLimitStart <= $scattersCount ) 
                            {
                                $scattersStr .= '"scattersType":"bonus",';
                            }
                            else if( $scattersWin > 0 ) 
                            {
                                $scattersStr .= '"scattersType":"win",';
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
                        $slotSettings->SetGameData('JustJewelsTotalWin', $totalWin);
                        $dbg = $slotSettings->slotWinLineMode;
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString = implode(',', $lineWins);
                        $advancedEffects = '';
                        $response = '{"responseEvent":"spin","dbg":"' . $dbg . '","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',' . $advancedEffects . '"totalFreeGames":' . $slotSettings->GetGameData('JustJewelsFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('JustJewelsCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                        $slotSettings->SaveLogReport($response, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
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
