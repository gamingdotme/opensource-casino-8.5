<?php 
namespace VanguardLTE\Games\HatTrick
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
                        $slotSettings->SetGameData('HatTrickFreeGames', 0);
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
                            $slotSettings->SetGameData('HatTrickFreeMpl', $lastEvent->serverResponse->multiplier);
                        }
                        $jsSet = json_encode($slotSettings);
                        $lang = json_encode(\Lang::get('games.' . $game));
                        $response = '{"responseEvent":"getSettings","slotLanguage":' . $lang . ',"serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = $slotSettings->GetGameData('HatTrickDealerCard');
                        $totalWin = $slotSettings->GetGameData('HatTrickTotalWin');
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
                        $slotSettings->SetGameData('HatTrickTotalWin', $totalWin);
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
                        $slotSettings->SetGameData('HatTrickDealerCard', $tmpDc);
                        $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                        $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                        $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                    }
                    else if( $postData['slotEvent'] == 'slotGamble' ) 
                    {
                        $Balance = $slotSettings->GetBalance();
                        $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                        $dealerCard = '';
                        $totalWin = $slotSettings->GetGameData('HatTrickTotalWin');
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
                        $slotSettings->SetGameData('HatTrickTotalWin', $totalWin);
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
                            $slotSettings->SetGameData('HatTrickBonusWin', 0);
                            $slotSettings->SetGameData('HatTrickFreeGames', 0);
                            $slotSettings->SetGameData('HatTrickCurrentFreeGame', 0);
                            $slotSettings->SetGameData('HatTrickTotalWin', 0);
                            $slotSettings->SetGameData('HatTrickFreeBalance', 0);
                        }
                        else
                        {
                            $slotSettings->SetGameData('HatTrickCurrentFreeGame', $slotSettings->GetGameData('HatTrickCurrentFreeGame') + 1);
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
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $wild = [
                                    'SCAT', 
                                    'P_1'
                                ];
                                $scatter = 'SCAT';
                                if( $slotSettings->slotMultiScatter ) 
                                {
                                    $tmp_scatter = $scatter;
                                    $scatter = 'EMPTY';
                                }
                            }
                            else
                            {
                                $wild = ['P_1'];
                                $scatter = 'SCAT';
                            }
                            if( $slotSettings->increaseFreeSpinsMpl && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $bonusMpl = 1;
                            }
                            else if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $bonusMpl = $slotSettings->GetGameData('HatTrickFreeMpl');
                            }
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
                                            $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($slotSettings->slotWinLineMode == 'LeftRight' || $slotSettings->slotWinLineMode == 'LeftRightMiddle') && ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
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
                                            $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"]}';
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
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($slotSettings->slotWinLineMode == 'LeftRight' || $slotSettings->slotWinLineMode == 'LeftRightMiddle') && ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
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
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"],"winReel2":["none","none"]}';
                                            }
                                        }
                                        if( $slotSettings->slotWinLineMode == 'LeftRightMiddle' && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
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
                                            $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel1":["none","none"],"winReel5":["none","none"]}';
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
                                            $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
                                            }
                                        }
                                        if( ($slotSettings->slotWinLineMode == 'LeftRight' || $slotSettings->slotWinLineMode == 'LeftRightMiddle') && ($s[4] == $csym || in_array($s[4], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
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
                                            $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"],"winReel1":["none","none"]}';
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
                                            $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                            if( $cWins[$k] < $tmpWin ) 
                                            {
                                                $cWins[$k] = $tmpWin;
                                                $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . ($cWins[$k] + $totalWin) . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('HatTrickBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                if( $slotSettings->slotBonusReelsFixed['countFixedReels'] > 0 ) 
                                {
                                    if( $reels['reel' . $r][0] == $scatter && $reels['reel' . $r][1] == $scatter && $reels['reel' . $r][2] == $scatter ) 
                                    {
                                        $scattersCount++;
                                        $scattersStr .= ('"winReel' . $r . '":[' . '0' . ',"' . $scatter . '"],');
                                    }
                                }
                                else if( $reels['reel' . $r][0] == $scatter ) 
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
                            $advancedEffects = '';
                            $doubleWin = 0;
                            if( $slotSettings->doubleFreeWin && $postData['slotEvent'] == 'freespin' ) 
                            {
                                if( $reels['reel3'][0] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $doubleWin = $slotSettings->GetGameData('HatTrickTotalWin');
                                    $advancedEffects = '"freeMultiplier":["' . $slotSettings->doubleFreeWinSym . '",0,"WIN X2",' . (($doubleWin + $totalWin) * 2) . '],';
                                }
                                else if( $reels['reel3'][1] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $doubleWin = $slotSettings->GetGameData('HatTrickTotalWin');
                                    $advancedEffects = '"freeMultiplier":["' . $slotSettings->doubleFreeWinSym . '",1,"WIN X2",' . (($doubleWin + $totalWin) * 2) . '],';
                                }
                                else if( $reels['reel3'][2] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $doubleWin = $slotSettings->GetGameData('HatTrickTotalWin');
                                    $advancedEffects = '"freeMultiplier":["' . $slotSettings->doubleFreeWinSym . '",2,"WIN X2",' . (($doubleWin + $totalWin) * 2) . '],';
                                }
                            }
                            if( $slotSettings->doubleFreeWin && $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( $reels['reel3'][0] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $advancedEffects = '"freeMultiplierStatic":["' . $slotSettings->doubleFreeWinSym . '",0,"",0],';
                                }
                                else if( $reels['reel3'][1] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $advancedEffects = '"freeMultiplierStatic":["' . $slotSettings->doubleFreeWinSym . '",1,"",0],';
                                }
                                else if( $reels['reel3'][2] == $slotSettings->doubleFreeWinSym ) 
                                {
                                    $advancedEffects = '"freeMultiplierStatic":["' . $slotSettings->doubleFreeWinSym . '",2,"",0],';
                                }
                            }
                            if( $slotSettings->increaseFreeSpinsMpl && $totalWin > 0 && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $doubleWin = $totalWin * ($slotSettings->GetGameData('HatTrickFreeMpl') - 1);
                                $advancedEffects = '"freeMultiplier":["' . $slotSettings->doubleFreeWinSym . '",-1,"X' . $slotSettings->GetGameData('HatTrickFreeMpl') . '",' . ($totalWin + $doubleWin + $slotSettings->GetGameData('HatTrickTotalWin')) . ',' . ($totalWin * $slotSettings->GetGameData('HatTrickFreeMpl')) . '],';
                            }
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
                                else if( $totalWin + $doubleWin <= $spinWinLimit && $winType == 'bonus' ) 
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
                                else if( $totalWin > 0 && $totalWin + $doubleWin <= $spinWinLimit && $winType == 'win' ) 
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
                        if( $totalWin + $doubleWin > 0 ) 
                        {
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * ($totalWin + $doubleWin));
                        }
                        if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('HatTrickFreeGames') <= $slotSettings->GetGameData('HatTrickCurrentFreeGame') && $winType != 'bonus' && $slotSettings->GetGameData('HatTrickTotalWin') + $totalWin > 0 ) 
                        {
                            $slotSettings->SetBalance($slotSettings->GetGameData('HatTrickTotalWin') + $totalWin + $doubleWin);
                        }
                        else if( $postData['slotEvent'] != 'freespin' && $winType != 'bonus' && $totalWin + $doubleWin > 0 ) 
                        {
                            $slotSettings->SetBalance($totalWin + $doubleWin);
                        }
                        $reportWin = $totalWin + $doubleWin;
                        if( $postData['slotEvent'] == 'freespin' ) 
                        {
                            $slotSettings->SetGameData('HatTrickBonusWin', $slotSettings->GetGameData('HatTrickBonusWin') + $totalWin);
                            $slotSettings->SetGameData('HatTrickTotalWin', $slotSettings->GetGameData('HatTrickTotalWin') + $totalWin);
                            $totalWin = $slotSettings->GetGameData('HatTrickBonusWin');
                            $Balance = $slotSettings->GetGameData('HatTrickFreeBalance');
                            $slotSettings->SetGameData('HatTrickBonusWin', $slotSettings->GetGameData('HatTrickBonusWin') + $doubleWin);
                            $slotSettings->SetGameData('HatTrickTotalWin', $slotSettings->GetGameData('HatTrickTotalWin') + $doubleWin);
                        }
                        else
                        {
                            $slotSettings->SetGameData('HatTrickTotalWin', $totalWin);
                        }
                        if( $slotSettings->slotBonusLimitStart <= $scattersCount && $slotSettings->slotBonus ) 
                        {
                            if( $slotSettings->GetGameData('HatTrickFreeGames') > 0 ) 
                            {
                                $slotSettings->SetGameData('HatTrickBonusWin', $totalWin);
                                $slotSettings->SetGameData('HatTrickFreeGames', $slotSettings->GetGameData('HatTrickFreeGames') + $slotSettings->slotFreeCount);
                            }
                            else
                            {
                                $slotSettings->SetGameData('HatTrickFreeMpl', $slotSettings->slotFreeMpl);
                                $slotSettings->SetGameData('HatTrickFreeGames', $slotSettings->slotFreeCount);
                                $slotSettings->SetGameData('HatTrickFreeBalance', $Balance);
                                $slotSettings->SetGameData('HatTrickCurrentFreeGame', 0);
                            }
                            if( $slotSettings->increaseFreeSpinsMpl == 1 ) 
                            {
                                $slotSettings->SetGameData('HatTrickFreeMpl', $slotSettings->GetGameData('HatTrickFreeMpl') + 1);
                            }
                        }
                        $jsSpin = '' . json_encode($reels) . '';
                        $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                        $winString = implode(',', $lineWins);
                        $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"multiplier":"' . $slotSettings->GetGameData('HatTrickFreeMpl') . '",' . $advancedEffects . '"totalFreeGames":' . $slotSettings->GetGameData('HatTrickFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('HatTrickCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
