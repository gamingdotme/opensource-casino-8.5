<?php 
namespace VanguardLTE\Games\LuckyHaunterIG
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
                        if( $postData['action'] == 'start' ) 
                        {
                            if( !in_array($postData['bet'], $slotSettings->Bet) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['action'] . '","serverResponse":"invalid bet"}';
                                exit( $response );
                            }
                            if( $postData['lines'] < 1 || $postData['lines'] > 9 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['action'] . '","serverResponse":"invalid lines"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['lines'] * $postData['bet']) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['action'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                        }
                        else if( $postData['action'] == 'risk' && $slotSettings->GetGameData('LuckyHaunterIGWin') <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['action'] . '","serverResponse":"invalid gamble state"}';
                            exit( $response );
                        }
                        switch( $postData['action'] ) 
                        {
                            case 'init':
                                $balance = $slotSettings->GetBalance();
                                $restoreLog = $slotSettings->GetHistory();
                                if( $restoreLog != 'NULL' ) 
                                {
                                    $bet = $restoreLog->Bet;
                                    $lines = $restoreLog->Lines;
                                    $reels = $slotSettings->GetReelStrips(false);
                                    $reelStr = '[' . implode(',', $restoreLog->Reels[0]) . '],' . '[' . implode(',', $restoreLog->Reels[1]) . '],' . '[' . implode(',', $restoreLog->Reels[2]) . ']';
                                }
                                else
                                {
                                    $bet = 1;
                                    $lines = 1;
                                    $reels = $slotSettings->GetReelStrips(false);
                                    $reelStr = '[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '],';
                                    $reelStr .= ('[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '],');
                                    $reelStr .= ('[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ']');
                                }
                                $response = '{"Amount":"' . sprintf('%01.2f', $balance * $slotSettings->CurrentDenom) . '","slotViewState":"' . $slotSettings->slotViewState . '","slotKeyConfig":' . $slotSettings->slotKeyConfig . ',"BetArray":[' . implode(',', $slotSettings->Bet) . '],"Credit":"' . floor($balance) . '","Currency":"' . $slotSettings->slotCurrency . '","Denomination":"' . sprintf('%01.2f', $slotSettings->CurrentDenom) . '","FuseBet":' . $slotSettings->fuseBet . ',"HelpCoef":[[0,0,0],[200,1000,5000],[100,500,2000],[30,100,500],[20,50,200],[10,30,100],[5,10,50],[3,5,20],[2,3,10]],"LastBet":' . $bet . ',"LastFuse":false,"LastLines":' . $lines . ',"MaxWin":675000,"RawCredit":1000,"Reels":[' . $reelStr . '],"Win":0,"args":{},"cmd":"init","time":"20181111T111512"}';
                                break;
                            case 'status':
                                if( !$slotSettings->HasGameData('LuckyHaunterIGLines') || !$slotSettings->HasGameData('LuckyHaunterIGBet') ) 
                                {
                                    $slotSettings->SetGameData('LuckyHaunterIGLines', 1);
                                    $slotSettings->SetGameData('LuckyHaunterIGBet', 1);
                                }
                                $balance = $slotSettings->GetBalance();
                                if( $slotSettings->GetGameData('LuckyHaunterIGWin') > 0 ) 
                                {
                                    $balance = $balance - $slotSettings->GetGameData('LuckyHaunterIGWin');
                                }
                                $response = '{"Amount":"' . sprintf('%01.2f', $balance * $slotSettings->CurrentDenom) . '","BetArray":[' . implode(',', $slotSettings->Bet) . '],"Credit":"' . floor($balance) . '","Currency":"' . $slotSettings->slotCurrency . '","Denomination":"' . sprintf('%01.2f', $slotSettings->CurrentDenom) . '","FuseBet":' . $slotSettings->fuseBet . ',"HelpCoef":[[0,0,0],[200,1000,5000],[100,500,2000],[30,100,500],[20,50,200],[10,30,100],[5,10,50],[3,5,20],[2,3,10]],"LastBet":' . $slotSettings->GetGameData('LuckyHaunterIGBet') . ',"LastFuse":false,"LastLines":' . $slotSettings->GetGameData('LuckyHaunterIGLines') . ',"MaxWin":675000,"RawCredit":"' . $balance . '","Reels":[[7,1,5,3,8],[5,3,6,7,7],[6,8,7,8,1]],"Win":0,"args":{},"cmd":"status","time":"20181111T111512"}';
                                break;
                            case 'start':
                                $balance = $slotSettings->GetBalance();
                                $bet = $postData['bet'];
                                $lines = $postData['lines'];
                                $allbet = $bet * $lines;
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $isWin = false;
                                $isBonus = false;
                                $spinState = $slotSettings->GetSpinSettings($bet * $lines, $lines);
                                if( $spinState[0] == 'bonus' ) 
                                {
                                    $isBonus = true;
                                }
                                else if( $spinState[0] == 'win' ) 
                                {
                                    $isWin = true;
                                }
                                $bank = $spinState[1];
                                $fuseBet = false;
                                if( $slotSettings->fuseBet < ($bet * $lines) ) 
                                {
                                    $fuseBet = true;
                                }
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
                                    1, 
                                    1
                                ];
                                $linesId[7] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
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
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $wild = 2;
                                $scatter = 0;
                                for( $i = 0; $i <= 1500; $i++ ) 
                                {
                                    if( $isBonus ) 
                                    {
                                        $bTmp = $slotSettings->Bonus($bet * $lines, $fuseBet);
                                        $totalWin = $bTmp['win'];
                                        $infoBonus = $bTmp['info'];
                                    }
                                    else
                                    {
                                        $totalWin = 0;
                                        $infoBonus = '';
                                    }
                                    $reels = $slotSettings->GetReelStrips($isBonus);
                                    $lineWins = [
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
                                    $lineWinsInfo = [];
                                    for( $line = 1; $line <= $lines; $line++ ) 
                                    {
                                        $lps = [];
                                        $cs = [
                                            -1, 
                                            -1, 
                                            -1, 
                                            -1, 
                                            -1, 
                                            -1
                                        ];
                                        for( $lp = 0; $lp <= 4; $lp++ ) 
                                        {
                                            $lps[$lp] = $linesId[$line][$lp] - 1;
                                            $cs[$lp + 1] = $reels['reel' . ($lp + 1)][$lps[$lp]];
                                        }
                                        $infoWinObj = null;
                                        foreach( $slotSettings->Paytable as $sym => $pays ) 
                                        {
                                            if( $sym == 1 ) 
                                            {
                                                $wild = -1;
                                            }
                                            else
                                            {
                                                $wild = 2;
                                            }
                                            if( ($cs[1] == $sym || $cs[1] == $wild) && ($cs[2] == $sym || $cs[2] == $wild) && ($cs[3] == $sym || $cs[3] == $wild) ) 
                                            {
                                                $curWin = $pays[3] * $bet;
                                                if( $lineWins[$line] < $curWin ) 
                                                {
                                                    $lineWins[$line] = $curWin;
                                                    $infoWinObj = (object)[
                                                        'Pos' => [
                                                            $lps[0], 
                                                            $lps[1], 
                                                            $lps[2], 
                                                            -1, 
                                                            -1
                                                        ], 
                                                        'Element' => $sym, 
                                                        'Count' => 3, 
                                                        'Line' => $line, 
                                                        'Coef' => $pays[3], 
                                                        'Win' => $curWin
                                                    ];
                                                }
                                            }
                                            if( ($cs[4] == $sym || $cs[4] == $wild) && ($cs[5] == $sym || $cs[5] == $wild) && ($cs[3] == $sym || $cs[3] == $wild) ) 
                                            {
                                                $curWin = $pays[3] * $bet;
                                                if( $lineWins[$line] < $curWin ) 
                                                {
                                                    $lineWins[$line] = $curWin;
                                                    $infoWinObj = (object)[
                                                        'Pos' => [
                                                            $lps[0], 
                                                            $lps[1], 
                                                            $lps[2], 
                                                            -1, 
                                                            -1
                                                        ], 
                                                        'Element' => $sym, 
                                                        'Count' => 3, 
                                                        'Line' => $line, 
                                                        'Coef' => $pays[3], 
                                                        'Win' => $curWin
                                                    ];
                                                }
                                            }
                                            if( ($cs[1] == $sym || $cs[1] == $wild) && ($cs[2] == $sym || $cs[2] == $wild) && ($cs[3] == $sym || $cs[3] == $wild) && ($cs[4] == $sym || $cs[4] == $wild) ) 
                                            {
                                                $curWin = $pays[4] * $bet;
                                                if( $lineWins[$line] < $curWin ) 
                                                {
                                                    $lineWins[$line] = $curWin;
                                                    $infoWinObj = (object)[
                                                        'Pos' => [
                                                            $lps[0], 
                                                            $lps[1], 
                                                            $lps[2], 
                                                            $lps[3], 
                                                            -1
                                                        ], 
                                                        'Element' => $sym, 
                                                        'Count' => 4, 
                                                        'Line' => $line, 
                                                        'Coef' => $pays[4], 
                                                        'Win' => $curWin
                                                    ];
                                                }
                                            }
                                            if( ($cs[5] == $sym || $cs[5] == $wild) && ($cs[2] == $sym || $cs[2] == $wild) && ($cs[3] == $sym || $cs[3] == $wild) && ($cs[4] == $sym || $cs[4] == $wild) ) 
                                            {
                                                $curWin = $pays[4] * $bet;
                                                if( $lineWins[$line] < $curWin ) 
                                                {
                                                    $lineWins[$line] = $curWin;
                                                    $infoWinObj = (object)[
                                                        'Pos' => [
                                                            $lps[0], 
                                                            $lps[1], 
                                                            $lps[2], 
                                                            $lps[3], 
                                                            -1
                                                        ], 
                                                        'Element' => $sym, 
                                                        'Count' => 4, 
                                                        'Line' => $line, 
                                                        'Coef' => $pays[4], 
                                                        'Win' => $curWin
                                                    ];
                                                }
                                            }
                                            if( ($cs[1] == $sym || $cs[1] == $wild) && ($cs[2] == $sym || $cs[2] == $wild) && ($cs[3] == $sym || $cs[3] == $wild) && ($cs[4] == $sym || $cs[4] == $wild) && ($cs[5] == $sym || $cs[5] == $wild) ) 
                                            {
                                                $curWin = $pays[5] * $bet;
                                                if( $lineWins[$line] < $curWin ) 
                                                {
                                                    $lineWins[$line] = $curWin;
                                                    $infoWinObj = (object)[
                                                        'Pos' => [
                                                            $lps[0], 
                                                            $lps[1], 
                                                            $lps[2], 
                                                            $lps[3], 
                                                            $lps[4]
                                                        ], 
                                                        'Element' => $sym, 
                                                        'Count' => 5, 
                                                        'Line' => $line, 
                                                        'Coef' => $pays[5], 
                                                        'Win' => $curWin
                                                    ];
                                                }
                                            }
                                        }
                                        if( $infoWinObj != null ) 
                                        {
                                            $lineWinsInfo[] = $infoWinObj;
                                            $totalWin += $lineWins[$line];
                                        }
                                    }
                                    if( $i >= 1200 ) 
                                    {
                                        $isBonus = false;
                                        $isWin = false;
                                    }
                                    if( $i >= 1500 ) 
                                    {
                                        exit( '{error:"Bad Reel Strips"}' );
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
                                        if( $slotSettings->increaseRTP && $isWin && $totalWin < ($minWin * $allbet) ) 
                                        {
                                        }
                                        else
                                        {
                                            $bonusSym = 0;
                                            for( $lp = 0; $lp <= 4; $lp++ ) 
                                            {
                                                if( $reels['reel' . ($lp + 1)][0] == 0 ) 
                                                {
                                                    $bonusSym++;
                                                }
                                                if( $reels['reel' . ($lp + 1)][1] == 0 ) 
                                                {
                                                    $bonusSym++;
                                                }
                                                if( $reels['reel' . ($lp + 1)][2] == 0 ) 
                                                {
                                                    $bonusSym++;
                                                }
                                            }
                                            if( $bonusSym >= 3 && !$isBonus ) 
                                            {
                                            }
                                            else if( $bank < $totalWin ) 
                                            {
                                            }
                                            else
                                            {
                                                if( $totalWin > 0 && $isBonus ) 
                                                {
                                                    break;
                                                }
                                                if( $totalWin > 0 && $isWin ) 
                                                {
                                                    break;
                                                }
                                                if( $totalWin == 0 && !$isWin ) 
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                $balanceIncrease = $totalWin;
                                if( $balanceIncrease != 0 ) 
                                {
                                    $slotSettings->SetBalance($balanceIncrease);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $balanceIncrease * -1);
                                }
                                $reelStr = '[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '],';
                                $reelStr .= ('[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '],');
                                $reelStr .= ('[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ']');
                                $balance = $balance - ($bet * $lines);
                                $card = $slotSettings->GetDealerCard();
                                if( isset($slotSettings->Jackpots['jackPay']) && $slotSettings->Jackpots['jackPay'] ) 
                                {
                                    $jackPay = $slotSettings->Jackpots['jackPay'];
                                }
                                else
                                {
                                    $jackPay = 0;
                                }
                                $response = '{"bs":"' . $bonusSym . '","Amount":"' . sprintf('%01.2f', $balance * $slotSettings->CurrentDenom) . '",' . $infoBonus . '"Credit":' . floor($balance) . ',"Fuse":false,"LineWins":' . json_encode($lineWinsInfo) . ',"RawCredit":' . floor($balance) . ',"RiskCard":' . $slotSettings->cardsID[$card] . ',"Reels":[' . $reelStr . '],"Lines":' . $lines . ',"Bet":' . $bet . ',"TotalBet":' . ($bet * $lines) . ',"Win":' . $totalWin . ',"args":{},"cmd":"start","Bonusing":' . $jackPay . ',"time":"20181111T174113"}';
                                $slotSettings->SetGameData('LuckyHaunterIGReels', $reelStr);
                                $slotSettings->SetGameData('LuckyHaunterIGWin', $totalWin);
                                $slotSettings->SetGameData('LuckyHaunterIGRisk', 1);
                                $slotSettings->SetGameData('LuckyHaunterIGLines', $lines);
                                $slotSettings->SetGameData('LuckyHaunterIGBet', $bet);
                                $slotSettings->SetGameData('LuckyHaunterIGDealerCard', $card);
                                $rState = 'bet';
                                if( $isBonus ) 
                                {
                                    $rState = 'BG';
                                }
                                $slotSettings->SaveLogReport($response, $bet, $lines, $totalWin, $rState);
                                break;
                            case 'finish':
                                $balance = $slotSettings->GetBalance();
                                $slotSettings->SetGameData('LuckyHaunterIGWin', 0);
                                $response = '{"Amount":"' . sprintf('%01.2f', $balance * $slotSettings->CurrentDenom) . '","Credit":' . floor($balance) . ',"RawCredit":' . floor($balance) . ',"Reels":[' . $slotSettings->GetGameData('LuckyHaunterIGReels') . '],"args":{},"cmd":"finish","time":"20181111T171830"}';
                                break;
                            case 'risk':
                                $onTabCards = [];
                                $onTabCards[] = $slotSettings->cardsID[$slotSettings->GetGameData($slotSettings->slotId . 'DealerCard')];
                                $idArr = [
                                    '2', 
                                    '3', 
                                    '4', 
                                    '5', 
                                    '6', 
                                    '7', 
                                    '8', 
                                    '9', 
                                    'T', 
                                    'J', 
                                    'Q', 
                                    'K', 
                                    'A', 
                                    'JOKER'
                                ];
                                $suitsArr = [
                                    'C', 
                                    'D', 
                                    'S', 
                                    'H'
                                ];
                                $dealerCard = $slotSettings->GetGameData($slotSettings->slotId . 'DealerCard');
                                $isWinAccept = rand(1, 2);
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                $curWin = $slotSettings->GetGameData($slotSettings->slotId . 'Win');
                                $reportBet = $slotSettings->GetGameData($slotSettings->slotId . 'Win');
                                $nextWin = $curWin * 2;
                                if( $slotSettings->MaxWin < ($nextWin * $slotSettings->CurrentDenom) ) 
                                {
                                    $isWinAccept = 0;
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'Risk', $slotSettings->GetGameData($slotSettings->slotId . 'Risk') + 1);
                                $riskStep = $slotSettings->GetGameData($slotSettings->slotId . 'Risk');
                                $otherCards = [];
                                $nextCard = $slotSettings->GetDealerCard();
                                $cardNum = array_search($dealerCard[0], $idArr);
                                if( $isWinAccept == 1 && $curWin <= $bank ) 
                                {
                                    $playerCardNum = rand($cardNum, 13);
                                }
                                else
                                {
                                    $playerCardNum = rand(0, $cardNum);
                                }
                                if( $cardNum < $playerCardNum ) 
                                {
                                    $balance = $slotSettings->SetBalance($curWin);
                                    $balance = $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $curWin);
                                }
                                else if( $playerCardNum == $cardNum ) 
                                {
                                    $nextWin = $curWin;
                                }
                                else
                                {
                                    $nextWin = 0;
                                    $balance = $slotSettings->SetBalance(-1 * $curWin);
                                    $balance = $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $curWin);
                                }
                                $ch = 0;
                                while( $ch < 500 ) 
                                {
                                    shuffle($suitsArr);
                                    $playerCard = $idArr[$playerCardNum] . $suitsArr[0];
                                    if( in_array($slotSettings->cardsID[$playerCard], $onTabCards) ) 
                                    {
                                        continue;
                                    }
                                    $onTabCards[] = $slotSettings->cardsID[$playerCard];
                                    break;
                                }
                                $ch = 0;
                                while( $ch < 500 ) 
                                {
                                    $cCnt = 0;
                                    shuffle($suitsArr);
                                    shuffle($idArr);
                                    if( in_array($slotSettings->cardsID[$idArr[0] . $suitsArr[0]], $onTabCards) ) 
                                    {
                                        continue;
                                    }
                                    $onTabCards[] = $slotSettings->cardsID[$idArr[0] . $suitsArr[0]];
                                    $otherCards[] = $slotSettings->cardsID[$idArr[0] . $suitsArr[0]];
                                    if( count($onTabCards) >= 5 ) 
                                    {
                                        break;
                                    }
                                }
                                $balance = $slotSettings->GetBalance();
                                $slotSettings->SetGameData($slotSettings->slotId . 'Win', $nextWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'DealerCard', $nextCard);
                                $response = '{"$onTabCards":' . json_encode($onTabCards) . ',"cc":"' . $playerCardNum . '|' . $cardNum . '","Lines":' . $slotSettings->GetGameData($slotSettings->slotId . 'Lines') . ',"Bet":' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . ',"Amount":"' . sprintf('%01.2f', $balance * $slotSettings->CurrentDenom) . '","Reels":[' . $slotSettings->GetGameData($slotSettings->slotId . 'Reels') . '],"Credit":' . floor($balance) . ',"Dealer":' . $slotSettings->cardsID[$dealerCard] . ',"Other":[' . implode(',', $otherCards) . '],"Player":' . $slotSettings->cardsID[$playerCard] . ',"PrevWin":' . $curWin . ',"RawCredit":' . floor($balance) . ',"RiskCard":' . $slotSettings->cardsID[$nextCard] . ',"Step":' . $riskStep . ',"Win":' . $nextWin . ',"args":{},"cmd":"risk","time":"20181111T172027"}';
                                $slotSettings->SaveLogReport($response, $reportBet, 1, $nextWin, 'slotGamble');
                                if( $nextWin == 0 ) 
                                {
                                    $nextWin = -1 * $curWin;
                                }
                                break;
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
