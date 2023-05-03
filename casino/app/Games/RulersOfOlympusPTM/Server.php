<?php 
namespace VanguardLTE\Games\RulersOfOlympusPTM
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        if( isset($postData['umid']) ) 
                        {
                            $umid = $postData['umid'];
                            if( isset($postData['ID']) && $postData['ID'] == 40041 ) 
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            else if( isset($postData['ID']) ) 
                            {
                                $umid = $postData['ID'];
                            }
                        }
                        else
                        {
                            if( isset($postData['ID']) ) 
                            {
                                $result_tmp[] = '3:::{"ID":18}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['spinType']) ) 
                        {
                            $result_tmp = [];
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('RulersOfOlympusPTMBonusWin', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMFreeGames', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMTotalWin', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMFreeBalance', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMFreeStartWin', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMBonusType', '');
                                $slotSettings->SetGameData('RulersOfOlympusPTMOlympusFreeWilds', []);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('RulersOfOlympusPTMCurrentFreeGame', $slotSettings->GetGameData('RulersOfOlympusPTMCurrentFreeGame') + 1);
                                $slotSettings->SetGameData('RulersOfOlympusPTMIncreaseMpl', $slotSettings->GetGameData('RulersOfOlympusPTMIncreaseMpl') + 1);
                                $bonusMpl = 1;
                            }
                            $linesId = [];
                            if( $slotSettings->GetGameData('RulersOfOlympusPTMBonusType') == 'olympus' ) 
                            {
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[1] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[2] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[3] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[4] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
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
                                    4
                                ];
                                $linesId[7] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[9] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[11] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[13] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[14] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[15] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[16] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    3, 
                                    2, 
                                    3, 
                                    4, 
                                    2
                                ];
                                $linesId[18] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[19] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[21] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[22] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[23] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[24] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[25] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[26] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[27] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[28] = [
                                    1, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[29] = [
                                    4, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[30] = [
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[31] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[32] = [
                                    2, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[33] = [
                                    3, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[34] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[35] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[36] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[37] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[38] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[39] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[40] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[41] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[42] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[43] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[44] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[45] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[46] = [
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[47] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[48] = [
                                    2, 
                                    2, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[49] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                            }
                            else
                            {
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
                                    3, 
                                    3
                                ];
                                $linesId[6] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
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
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[10] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[11] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[12] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[17] = [
                                    1, 
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[18] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
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
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[21] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[22] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $linesId[23] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                            }
                            $postData['bet'] = $postData['bet'] / 100;
                            for( $i = 0; $i < count($postData['lines']); $i++ ) 
                            {
                                if( $postData['lines'][$i] > 0 ) 
                                {
                                    $lines = $i + 1;
                                }
                                else
                                {
                                    break;
                                }
                            }
                            $betLine = $postData['bet'] / 25;
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < ($lines * $betLine) ) 
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
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($postData['bet']);
                                if( is_array($jackState) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                }
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $randomBonusIdRand = 1;
                                $winType = 'bonus';
                            }
                            else
                            {
                                $randomBonusIdRand = 0;
                            }
                            $winType2 = '';
                            if( $randomBonusIdRand == 1 && $winType == 'bonus' ) 
                            {
                                $winType2 = 'bonus2';
                                $winType = 'none';
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
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $shiftingWilds = $slotSettings->GetGameData('RulersOfOlympusPTMShiftingWilds');
                                $wild = [
                                    '22', 
                                    '23', 
                                    '19'
                                ];
                                $scatter = '26';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent'], $slotSettings->GetGameData('RulersOfOlympusPTMBonusType'));
                                $reelsTmp = $reels;
                                $isBonusStart = false;
                                $reelSetIndex = 0;
                                if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('RulersOfOlympusPTMBonusType') == 'olympus' ) 
                                {
                                    $reelSetIndex = 12;
                                    $oWilds = $slotSettings->GetGameData('RulersOfOlympusPTMOlympusFreeWilds');
                                    $owc = 0;
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $oWilds[$owc] == 1 ) 
                                            {
                                                $reels['reel' . $r][$p] = 19;
                                            }
                                            $owc++;
                                        }
                                    }
                                }
                                if( $slotSettings->GetGameData('RulersOfOlympusPTMBonusType') != 'olympus' ) 
                                {
                                    for( $ww = 0; $ww < count($shiftingWilds); $ww++ ) 
                                    {
                                        $cw = $shiftingWilds[$ww];
                                        if( $cw[2] == '23' ) 
                                        {
                                            $shiftingWilds[$ww][0]--;
                                        }
                                        else if( $cw[2] == '22' ) 
                                        {
                                            $shiftingWilds[$ww][0]++;
                                        }
                                        $cw = $shiftingWilds[$ww];
                                        if( $shiftingWilds[$ww][0] < 1 || $shiftingWilds[$ww][0] > 5 ) 
                                        {
                                            unset($shiftingWilds[$ww]);
                                        }
                                        else
                                        {
                                            $reels['reel' . $cw[0]][$cw[1]] = $cw[2];
                                        }
                                    }
                                    $shiftingWilds = array_values($shiftingWilds);
                                    $isAdded = false;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( rand(1, 100) == 1 && $reels['reel' . $r][$p] != '22' && $reels['reel' . $r][$p] != '23' && $reels['reel' . $r][$p] != '26' && count($shiftingWilds) < 4 ) 
                                            {
                                                $reels['reel' . $r][$p] = $wild[rand(0, 1)];
                                                $shiftingWilds[] = [
                                                    $r, 
                                                    $p, 
                                                    $reels['reel' . $r][$p]
                                                ];
                                                $isAdded = false;
                                                break;
                                            }
                                            if( $isAdded ) 
                                            {
                                                break;
                                            }
                                        }
                                    }
                                }
                                for( $k = 0; $k < $lines; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter || $reels['reel' . $r][$p] == '21' ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                        }
                                    }
                                }
                                $scattersWin = 0;
                                if( $scattersCount >= 1 && $slotSettings->slotBonus ) 
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
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $scattersCount >= 1 && ($winType != 'bonus' || $postData['slotEvent'] == 'freespin') ) 
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
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('RulersOfOlympusPTMBonusWin', $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('RulersOfOlympusPTMTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('RulersOfOlympusPTMTotalWin', $totalWin);
                            }
                            $slotSettings->SetGameData('RulersOfOlympusPTMBonusStart', false);
                            $olympusWilds = '';
                            if( $scattersCount >= 1 ) 
                            {
                                $slotSettings->SetGameData('RulersOfOlympusPTMFreeGames', $slotSettings->slotFreeCount);
                                $slotSettings->SetGameData('RulersOfOlympusPTMFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('RulersOfOlympusPTMBonusWin', 0);
                                $slotSettings->SetGameData('RulersOfOlympusPTMBonusType', 'olympus');
                                $rwild = rand(1, 2);
                                $wrpos = [];
                                $wrpos[0] = [
                                    0, 
                                    3, 
                                    4, 
                                    5, 
                                    8, 
                                    9, 
                                    10, 
                                    13, 
                                    14, 
                                    15, 
                                    18, 
                                    19
                                ];
                                $wrpos[1] = [
                                    2, 
                                    3, 
                                    4, 
                                    7, 
                                    8, 
                                    9, 
                                    12, 
                                    13, 
                                    14, 
                                    17, 
                                    18, 
                                    19
                                ];
                                $cwrp = $wrpos[rand(0, 1)];
                                for( $i = 0; $i <= $rwild; $i++ ) 
                                {
                                    $olympusFreeWilds[$cwrp[$i]] = 1;
                                }
                                $olympusWilds = ',"wildsPattern":[' . implode(',', $olympusFreeWilds) . ']';
                                $slotSettings->SetGameData('RulersOfOlympusPTMOlympusFreeWilds', $olympusFreeWilds);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $spinState = 'REGULAR';
                            if( $postData['spinType'] == 'free' ) 
                            {
                                $spinState = 'FREE';
                            }
                            $isBonus = 'false';
                            if( $slotSettings->GetGameData('RulersOfOlympusPTMBonusStart') ) 
                            {
                                $isBonus = 'true';
                            }
                            $slotSettings->SetGameData('RulersOfOlympusPTMShiftingWilds', $shiftingWilds);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"OlympusFreeWilds":' . json_encode($slotSettings->GetGameData('RulersOfOlympusPTMOlympusFreeWilds')) . ',"BonusType":"' . $slotSettings->GetGameData('RulersOfOlympusPTMBonusType') . '","linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('RulersOfOlympusPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('RulersOfOlympusPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('RulersOfOlympusPTMBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('RulersOfOlympusPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $rSym = $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . ',';
                            $rSym .= ($reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . ',');
                            $rSym .= ($reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2]);
                            $result_tmp[] = '3:::{"data":{"ww":' . $totalWin . ',"wt":' . json_encode($slotSettings->GetGameData('RulersOfOlympusPTMShiftingWilds')) . ',"balance":' . $balanceInCents . ',"results":[' . implode(',', $reels['rp']) . '],"spinType":"' . $spinState . '","symbols":[3,1,8,1,6,6,1,9,2,2,6,6,3,5],"windowId":"qkpJt6"},"ID":40022,"umid":36}';
                            $result_tmp[] = '3:::{"data":{"reelSet":' . $reelSetIndex . ',"display":[' . $rSym . ']' . $olympusWilds . ',"windowId":"NXY2mU"},"ID":49301,"umid":71}';
                            $slotSettings->SetGameData('RulersOfOlympusPTMreelSetIndex', $reelSetIndex);
                            $slotSettings->SetGameData('RulersOfOlympusPTMresults', $reels['rp']);
                            $slotSettings->SetGameData('RulersOfOlympusPTMrSym', $rSym);
                            if( $winType2 == 'bonus2' && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $winsArr = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $jids = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $jid = 0;
                                $JackWinID = $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID') + 1;
                                shuffle($winsArr);
                                for( $i = 0; $i < 20; $i++ ) 
                                {
                                    if( $jids[$winsArr[$i]] == 2 ) 
                                    {
                                        $winsArr[$i] = $JackWinID;
                                    }
                                    $jids[$winsArr[$i]]++;
                                    if( $jids[$winsArr[$i]] >= 3 ) 
                                    {
                                        $jid = $winsArr[$i];
                                        break;
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                                $slotSettings->SetBalance($slotSettings->slotJackpot[$jid - 1]);
                                $jackWin = $slotSettings->slotJackpot[$jid - 1] * 100;
                                $jWin = $slotSettings->slotJackpot[$jid - 1];
                                $result_tmp[] = '3:::{"data":{"startTime":1,"winningLevel":' . $jid . ',"totalWin":' . $jackWin . ',"reelInfo":[' . implode(',', $winsArr) . '],"windowId":"33zr6v"},"ID":40071,"umid":40}';
                                $slotSettings->SaveLogReport($response, $betLine, $lines, $jWin, 'JPG');
                            }
                            else
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                            }
                            $result_tmp[] = '3:::{"data":{"windowId":"Hr1cOy"},"ID":40072,"umid":44}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":45}';
                            $response = implode('------', $result_tmp);
                            $slotSettings->SaveGameData();
                            $slotSettings->SaveGameDataStatic();
                            echo $response;
                        }
                        switch( $umid ) 
                        {
                            case '31031':
                                $result_tmp[] = '3:::{"data":{"urlList":[{"urlType":"mobile_login","url":"https://login.loc/register","priority":1},{"urlType":"mobile_support","url":"https://ww2.loc/support","priority":1},{"urlType":"playerprofile","url":"","priority":1},{"urlType":"playerprofile","url":"","priority":10},{"urlType":"gambling_commission","url":"","priority":1},{"urlType":"cashier","url":"","priority":1},{"urlType":"cashier","url":"","priority":1}]},"ID":100}';
                                break;
                            case '10001':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40083,"umid":3}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":4}';
                                $result_tmp[] = '3:::{"data":{"commandId":13218,"params":["0","null"]},"ID":50001,"umid":5}';
                                $result_tmp[] = '3:::{"token":{"secretKey":"","currency":"USD","balance":0,"loginTime":""},"ID":10002,"umid":7}';
                                break;
                            case '40294':
                                $result_tmp[] = '3:::{"nicknameInfo":{"nickname":""},"ID":10022,"umid":8}';
                                $result_tmp[] = '3:::{"data":{"commandId":10713,"params":["0","ba","bj","ct","gc","grel","hb","po","ro","sc","tr"]},"ID":50001,"umid":9}';
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                                $result_tmp[] = '3:::{"data":{"commandId":11666,"params":["0","0","0"]},"ID":50001,"umid":11}';
                                $result_tmp[] = '3:::{"data":{"commandId":13981,"params":["0","1"]},"ID":50001,"umid":12}';
                                $result_tmp[] = '3:::{"data":{"commandId":14080,"params":["0","0"]},"ID":50001,"umid":14}';
                                $result_tmp[] = '3:::{"data":{"keyValueCount":5,"elementsPerKey":1,"params":["10","1","11","500","12","1","13","0","14","0"]},"ID":40716,"umid":15}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":16}';
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":' . $balanceInCents . '},"ID":10006,"umid":17}';
                                $result_tmp[] = '3:::{"data":{},"ID":40292,"umid":18}';
                                break;
                            case '10010':
                                $result_tmp[] = '3:::{"data":{"urls":{"casino-cashier-myaccount":[],"regulation_pt_self_exclusion":[],"link_legal_aams":[],"regulation_pt_player_protection":[],"mobile_cashier":[],"mobile_bank":[],"mobile_bonus_terms":[],"mobile_help":[],"link_responsible":[],"cashier":[{"url":"","priority":1},{"url":"","priority":1}],"gambling_commission":[{"url":"","priority":1},{"url":"","priority":1}],"desktop_help":[],"chat_token":[],"mobile_login_error":[],"mobile_error":[],"mobile_login":[{"url":"","priority":1}],"playerprofile":[{"url":"","priority":1},{"url":"","priority":10}],"link_legal_half":[],"ngmdesktop_quick_deposit":[],"external_login_form":[],"mobile_main_promotions":[],"mobile_lobby":[],"mobile_promotion":[],{"url":"","priority":1},{"url":"","priority":10}],"mobile_withdraw":[],"mobile_funds_trans":[],"mobile_quick_deposit":[],"mobile_history":[],"mobile_deposit_limit":[],"minigames_help":[],"link_legal_18":[],"mobile_responsible":[],"mobile_share":[],"mobile_lobby_error":[],"mobile_mobile_comp_points":[],"mobile_support":[{"url":"","priority":1}],"mobile_chat":[],"mobile_logout":[],"mobile_deposit":[],"invite_friend":[]}},"ID":10011,"umid":19}';
                                $result_tmp[] = '3:::{"data":{"brokenGames":[],"windowId":"SuJLru"},"ID":40037,"umid":20}';
                                break;
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"aogroo","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                if( !$slotSettings->HasGameData('RulersOfOlympusPTMShiftingWilds') ) 
                                {
                                    $slotSettings->SetGameData('RulersOfOlympusPTMShiftingWilds', []);
                                }
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusType', $lastEvent->serverResponse->BonusType);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'OlympusFreeWilds', $lastEvent->serverResponse->OlympusFreeWilds);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'aogroo');
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '49149':
                                $reelSetIndex = $slotSettings->GetGameData('RulersOfOlympusPTMreelSetIndex');
                                $reels = $slotSettings->GetGameData('RulersOfOlympusPTMresults');
                                $rSym = $slotSettings->GetGameData('RulersOfOlympusPTMrSym');
                                if( !is_array($reels) ) 
                                {
                                    $reels = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                }
                                if( strlen($rSym) <= 0 ) 
                                {
                                    $rSym = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
                                }
                                if( !is_numeric($reelSetIndex) ) 
                                {
                                    $reelSetIndex = 0;
                                }
                                $result_tmp[] = '3:::{"data":{"bet":' . $postData['bet'] . ',"walkersPositions":[' . $rSym . '],"reelset":8,"reelstops":[' . implode(',', $reels) . '],"windowId":"Uliwrq"},"ID":49152,"umid":32}';
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40030':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                    $bonusOpt = '';
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'BonusType') == 'power' ) 
                                    {
                                        $result_tmp[] = '3:::{"data":{"freeSpinsData":{"numFreeSpins":1,"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100) . ',"rows":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[21,6,21,0,28],"coins":1,"multiplier":1,"mode":0,"startBonus":1},"lpReelSet":8,"tgReelSet":8,"bonusType":"POWER_FREEGAMES","display":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"playedFG":0,"jackpotWin":0,"reelinfo":[21,6,21,0,28],"windowId":"DHaqVt"},"ID":49303,"umid":31}';
                                    }
                                    else
                                    {
                                        $result_tmp[] = '3:::{"data":{"freeSpinsData":{"numFreeSpins":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100) . ',"rows":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[5,41,44,49,35],"coins":1,"multiplier":1,"mode":0,"startBonus":1},"lpReelSet":0,"tgReelSet":0,"bonusType":"OLYMPUS_FREEGAMES","wildsPattern":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'OlympusFreeWilds')) . '],"jackpotWin":0,"reelinfo":[5,41,44,49,35],"windowId":"hWKwC2"},"ID":49303,"umid":29}';
                                    }
                                }
                                break;
                            case '48300':
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":30}';
                                $result_tmp[] = '3:::{"data":{"waitingLogins":[],"waitingAlerts":[],"waitingDialogs":[],"waitingDialogMessages":[],"waitingToasterMessages":[]},"ID":48301,"umid":31}';
                                break;
                        }
                        $response = implode('------', $result_tmp);
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
