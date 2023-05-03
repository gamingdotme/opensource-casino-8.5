<?php 
namespace VanguardLTE\Games\BlackJackAM
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
                        $checked = new \VanguardLTE\Lib\LicenseDK();
                        $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
                        if( $license_notifications_array['notification_case'] != 'notification_license_ok' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"error","serverResponse":"Error LicenseDK"}';
                            exit( $response );
                        }
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
                        $floatBet = 100;
                        $response = '';
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                        $gameData = [];
                        $tmpPar = explode(',', $postData['gameData']);
                        $gameData['slotEvent'] = $tmpPar[0];
                        switch( $gameData['slotEvent'] ) 
                        {
                            case 'A/u350':
                                $winall = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                if( !is_numeric($winall) ) 
                                {
                                    $winall = 0;
                                }
                                $balance = $slotSettings->GetBalance() - $winall;
                                $response = 'UPDATE#' . (sprintf('%01.2f', $balance) * $floatBet);
                                break;
                            case 'A/u25':
                                $slotSettings->SetGameData($slotSettings->slotId . 'Cards', [
                                    '00', 
                                    '00', 
                                    '00', 
                                    '00', 
                                    '00', 
                                    '00', 
                                    '00', 
                                    '00'
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                $slotSettings->SetGameData('BlackJackAMStep', 'gameIsOver');
                                $betsArr = $slotSettings->Bet;
                                $betString = '';
                                for( $b = 0; $b < count($betsArr); $b++ ) 
                                {
                                    $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                                    $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                                }
                                $minBets = '';
                                $maxBets = '';
                                $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                                $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1])) . dechex($betsArr[count($betsArr) - 1]));
                                $betsLength = count($betsArr);
                                $betsLength = dechex($betsLength);
                                if( strlen($betsLength) <= 1 ) 
                                {
                                    $betsLength = '0' . $betsLength;
                                }
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                $LastAction = $slotSettings->GetGameData('BlackJackAMLastAction');
                                $insuranceBet = $slotSettings->GetGameData('BlackJackAMinsuranceBet');
                                $insuranceState = $slotSettings->GetGameData('BlackJackAMinsuranceState');
                                if( $slotSettings->GetGameData('BlackJackAMStep') != 'gameIsOver' ) 
                                {
                                    $betString2 = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                    $response = '00' . $LastAction . '010' . $balanceFormated . '10' . $minBets . $maxBets . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '000000000' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString2 . '101010101010101010' . implode('', $insuranceState) . '101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '0615' . $betString . '00';
                                }
                                else
                                {
                                    $response = '000010' . $balanceFormated . '10' . $minBets . $maxBets . '1010010a0a000000000000101010101010101010101010101010101010101010101010010101010101010101150000000000000000000000000000000000000000001500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000001500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000001500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000000615' . $betString . '00';
                                }
                                break;
                            case 'A/u250':
                                $betsArr = $slotSettings->Bet;
                                $betString = '';
                                for( $b = 0; $b < count($betsArr); $b++ ) 
                                {
                                    $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                                    $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                                }
                                $minBets = '';
                                $maxBets = '';
                                $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                                $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1])) . dechex($betsArr[count($betsArr) - 1]));
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '100010' . $balanceFormated . '101010010a0a0000000000001010101010101010101010101010101010101010101010100101010101010101011500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000001500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000001500000000000000000000000000000000000000000015000000000000000000000000000000000000000000150000000000000000000000000000000000000000001500000000000000000000000000000000000000000000';
                                break;
                            case 'A/u2810':
                                $gameStep = $slotSettings->GetGameData('BlackJackAMStep');
                                if( $gameStep != 'betIsPlaced' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid state"}';
                                    exit( $response );
                                }
                                $slotSettings->SetGameData('BlackJackAMStep', 'gameIsOver');
                                $BankReserved = $slotSettings->GetGameData('BlackJackAMBankReserved');
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $BankReserved);
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                $insuranceBet = $slotSettings->GetGameData('BlackJackAMinsuranceBet');
                                $insuranceState = $slotSettings->GetGameData('BlackJackAMinsuranceState');
                                $insuranceWinState = [
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $boxId = 0;
                                $cardsArr = array_slice($cardsArr, $totalCnt);
                                $cardsReserveArr = $slotSettings->GetGameData('BlackJackAMCardsReserve');
                                $smallBank = $slotSettings->GetGameData('BlackJackAMsmallBank');
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    shuffle($cardsArr);
                                    shuffle($cardsReserveArr);
                                    $totalWin = 0;
                                    $totalCnt = 0;
                                    $boxPlayerTmp = $boxPlayer[$boxId];
                                    $playerMinScore = 22;
                                    for( $i = 1; $i <= 9; $i++ ) 
                                    {
                                        if( $boxPlayer[$i]['mainScore'] < $playerMinScore && $boxPlayer[$i]['mainScore'] > 0 ) 
                                        {
                                            $playerMinScore = $boxPlayer[$i]['mainScore'];
                                        }
                                    }
                                    $isDealerBJ = false;
                                    for( $i = 0; $i < count($cardsArr); $i++ ) 
                                    {
                                        if( $smallBank ) 
                                        {
                                            $boxPlayerTmp['cards'][$boxPlayerTmp['cnt']] = $cardsReserveArr[$i];
                                        }
                                        else
                                        {
                                            $boxPlayerTmp['cards'][$boxPlayerTmp['cnt']] = $cardsArr[$totalCnt];
                                        }
                                        $slotSettings->GetScore($boxPlayerTmp);
                                        $boxPlayerTmp['strOut'] = $slotSettings->FormatBox($boxPlayerTmp['cards'], $cardsID);
                                        $totalCnt++;
                                        if( $boxPlayerTmp['mainScore'] >= 17 || $playerMinScore > 21 ) 
                                        {
                                            if( $boxPlayerTmp['mainScore'] == 21 && $i == 0 ) 
                                            {
                                                $isDealerBJ = true;
                                            }
                                            break;
                                        }
                                    }
                                    for( $i = 1; $i <= 9; $i++ ) 
                                    {
                                        if( $boxPlayer[$i]['bet'] > 0 && $boxPlayer[$i]['mainScore'] <= 21 ) 
                                        {
                                            $cWin = 0;
                                            if( $boxPlayerTmp['mainScore'] < $boxPlayer[$i]['mainScore'] || $boxPlayerTmp['mainScore'] > 21 ) 
                                            {
                                                $cWin = $boxPlayer[$i]['bet'] * 2;
                                            }
                                            if( $boxPlayer[$i]['blackjack'] ) 
                                            {
                                                $cWin = $boxPlayer[$i]['bet'] * 2.5;
                                            }
                                            if( $boxPlayerTmp['mainScore'] == $boxPlayer[$i]['mainScore'] && $boxPlayer[$i]['mainScore'] <= 21 && !$isDealerBJ && !$boxPlayer[$i]['blackjack'] ) 
                                            {
                                                $cWin = $boxPlayer[$i]['bet'] * 1;
                                            }
                                            $boxPlayer[$i]['win'] = $cWin;
                                            $totalWin += $cWin;
                                        }
                                        $boxPlayer[$i]['strOut'] = $slotSettings->FormatBox($boxPlayer[$i]['cards'], $cardsID);
                                    }
                                    if( $boxPlayerTmp['mainScore'] == 21 && $isDealerBJ ) 
                                    {
                                        for( $in = 0; $in <= 2; $in++ ) 
                                        {
                                            if( $insuranceBet[$in] > 0 ) 
                                            {
                                                $insuranceWinState[$in] = $slotSettings->HexFormat($insuranceBet[$in] * 2);
                                                $totalWin += ($insuranceBet[$in] * 2);
                                            }
                                        }
                                    }
                                    if( $totalWin <= $bank ) 
                                    {
                                        break;
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBalance($totalWin);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                }
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $hitState = 'a';
                                $slotSettings->SetGameData('BlackJackAMBoxes', 'NULL');
                                $slotSettings->SetGameData('BlackJackAMBet', 'NULL');
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', 'NULL');
                                $slotSettings->SetGameData('BlackJackAMCards', 'NULL');
                                $slotSettings->SetGameData('BlackJackAMboxState', 'NULL');
                                $slotSettings->SetGameData('BlackJackAMaState', 'NULL');
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $winString = $slotSettings->HexFormat($boxPlayer[1]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['win'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['win'] * $floatBet);
                                $response = '10' . $hitState . '010' . $balanceFormated . $slotSettings->HexFormat($totalWin * $floatBet) . $slotSettings->HexFormat($allbet * $floatBet) . '100103000000000' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . $winString . '101010101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayerTmp['strOut'] . '00#';
                                $response_end = $slotSettings->HexFormat($totalWin * $floatBet) . $slotSettings->HexFormat($allbet * $floatBet) . '100103000000000' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . $winString . implode('', $insuranceState) . implode('', $insuranceWinState) . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayerTmp['strOut'] . '00#';
                                $slotSettings->SetGameData('BlackJackAMEnd', $response_end);
                                $slotSettings->SetGameData('BlackJackAMLastAction', $hitState);
                                $slotSettings->SaveLogReport($response, 0, 1, $totalWin, 'bet');
                                break;
                            case 'A/u2811':
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '10b010' . $balanceFormated . $slotSettings->GetGameData('BlackJackAMEnd');
                                $slotSettings->SetGameData('BlackJackAMLastAction', 'b');
                                break;
                            case 'A/u285':
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                if( $tmpPar[1] != 0 && $tmpPar[1] != 1 && $tmpPar[1] != 2 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid arguments"}';
                                    exit( $response );
                                }
                                if( $tmpPar[1] == 0 ) 
                                {
                                    $boxId = 1;
                                    $boxState = '0';
                                }
                                if( $tmpPar[1] == 1 ) 
                                {
                                    $boxId = 4;
                                    $boxState = '1';
                                }
                                if( $tmpPar[1] == 2 ) 
                                {
                                    $boxId = 7;
                                    $boxState = '2';
                                }
                                if( $tmpPar[2] == 0 ) 
                                {
                                    $boxState1 = '0';
                                }
                                if( $tmpPar[2] == 1 ) 
                                {
                                    $boxState1 = '1';
                                }
                                if( $tmpPar[3] == 0 ) 
                                {
                                    $boxState2 = '0';
                                }
                                if( $tmpPar[3] == 1 ) 
                                {
                                    $boxState2 = '1';
                                }
                                $aState[$boxId + 1] = '00';
                                $boxPlayer[$boxId]['state'] = '3';
                                $boxPlayer[$boxId + 1]['cards'][0] = $boxPlayer[$boxId]['cards'][1];
                                $boxPlayer[$boxId + 1]['state'] = $boxPlayer[$boxId]['state'];
                                $boxPlayer[$boxId + 1]['bet'] = $boxPlayer[$boxId]['bet'];
                                if( $boxPlayer[$boxId]['bet'] <= $slotSettings->GetBalance() ) 
                                {
                                    $bankSum = $boxPlayer[$boxId]['bet'] / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->UpdateJackpots($boxPlayer[$boxId]['bet']);
                                    $slotSettings->SetBalance(-1 * $boxPlayer[$boxId]['bet'], 'bet');
                                    $slotSettings->SaveLogReport($response, $boxPlayer[$boxId]['bet'], 1, 0, 'bet');
                                    $allbet += $boxPlayer[$boxId]['bet'];
                                }
                                else
                                {
                                    echo '{"error":"invalid_balance","state":"double"}';
                                }
                                $boxPlayer[$boxId]['cnt'] = 1;
                                $boxPlayer[$boxId + 1]['cnt'] = 1;
                                $boxPlayer[$boxId]['cards'][$boxPlayer[$boxId]['cnt']] = $cardsArr[$totalCnt];
                                $slotSettings->GetScore($boxPlayer[$boxId]);
                                $boxPlayer[$boxId]['strOut'] = $slotSettings->FormatBox($boxPlayer[$boxId]['cards'], $cardsID);
                                $totalCnt++;
                                $boxPlayer[$boxId + 1]['cards'][$boxPlayer[$boxId + 1]['cnt']] = $cardsArr[$totalCnt];
                                $slotSettings->GetScore($boxPlayer[$boxId + 1]);
                                $boxPlayer[$boxId + 1]['strOut'] = $slotSettings->FormatBox($boxPlayer[$boxId + 1]['cards'], $cardsID);
                                $totalCnt++;
                                $hitState = '5';
                                $splitState = '';
                                if( $boxPlayer[2]['bet'] > 0 ) 
                                {
                                    $splitState .= '01';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[5]['bet'] > 0 ) 
                                {
                                    $splitState .= '01';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[8]['bet'] > 0 ) 
                                {
                                    $splitState .= '01';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', $totalCnt);
                                $slotSettings->SetGameData('BlackJackAMCards', $cardsArr);
                                $slotSettings->SetGameData('BlackJackAMboxState', $boxState);
                                $slotSettings->SetGameData('BlackJackAMaState', $aState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', $hitState);
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '10' . $hitState . '010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '0' . $boxState1 . $splitState . '0' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010101010101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#';
                                break;
                            case 'A/u283':
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                if( $tmpPar[1] != 0 && $tmpPar[1] != 1 && $tmpPar[1] != 2 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid arguments"}';
                                    exit( $response );
                                }
                                if( $tmpPar[1] == 0 ) 
                                {
                                    $boxId = 1;
                                    $boxState = '0';
                                    $boxState1 = '0';
                                    $boxPlayer[$boxId]['state'] = '3';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 2;
                                    }
                                }
                                if( $tmpPar[1] == 1 ) 
                                {
                                    $boxId = 4;
                                    $boxState = '1';
                                    $boxPlayer[$boxId]['state'] = '3';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 5;
                                    }
                                }
                                if( $tmpPar[1] == 2 ) 
                                {
                                    $boxId = 7;
                                    $boxState = '2';
                                    $boxPlayer[$boxId]['state'] = '3';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 8;
                                    }
                                }
                                $boxPlayer[$boxId]['cards'][$boxPlayer[$boxId]['cnt']] = $cardsArr[$totalCnt];
                                $slotSettings->GetScore($boxPlayer[$boxId]);
                                $boxPlayer[$boxId]['strOut'] = $slotSettings->FormatBox($boxPlayer[$boxId]['cards'], $cardsID);
                                $totalCnt++;
                                if( $boxPlayer[$boxId]['mainScore'] > 21 ) 
                                {
                                    $hitState = '9';
                                    $boxPlayer[$boxId]['state'] = '9';
                                }
                                else if( $boxPlayer[$boxId]['mainScore'] == 21 ) 
                                {
                                    $hitState = '3';
                                    $boxPlayer[$boxId]['state'] = '4';
                                }
                                else
                                {
                                    $hitState = '3';
                                }
                                $splitState = '';
                                if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '1' && $boxId == 2 ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[2]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[2] = '01';
                                        $boxPlayer[2]['state'] = '9';
                                    }
                                    else if( $boxPlayer[2]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '4';
                                        $aState[2] = '01';
                                        $boxPlayer[2]['state'] = '3';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '0' && $boxId == 1 ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[1]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[1] = '01';
                                        $boxPlayer[1]['state'] = '3';
                                    }
                                    else if( $boxPlayer[1]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '3';
                                        $aState[1] = '01';
                                        $boxPlayer[1]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[5]['bet'] > 0 && $boxState1 == '1' && $boxId == 5 ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[5]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[5] = '01';
                                        $boxPlayer[5]['state'] = '9';
                                    }
                                    else if( $boxPlayer[5]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '3';
                                        $aState[5] = '01';
                                        $boxPlayer[5]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else if( $boxPlayer[5]['bet'] > 0 && $boxState1 == '0' && $boxId == 4 ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[4]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[4] = '01';
                                        $boxPlayer[4]['state'] = '3';
                                    }
                                    else if( $boxPlayer[4]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '3';
                                        $aState[4] = '01';
                                        $boxPlayer[4]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '1' && $boxId == 8 ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[8]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[8] = '01';
                                        $boxPlayer[8]['state'] = '9';
                                    }
                                    else if( $boxPlayer[8]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '3';
                                        $aState[8] = '01';
                                        $boxPlayer[8]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '0' && $boxId == 7 ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[7]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[7] = '01';
                                        $boxPlayer[7]['state'] = '3';
                                    }
                                    else if( $boxPlayer[7]['mainScore'] == 21 ) 
                                    {
                                        $hitState = '3';
                                        $aState[7] = '01';
                                        $boxPlayer[7]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '3';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', $totalCnt);
                                $slotSettings->SetGameData('BlackJackAMCards', $cardsArr);
                                $slotSettings->SetGameData('BlackJackAMboxState', $boxState);
                                $slotSettings->SetGameData('BlackJackAMaState', $aState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', $hitState);
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '10' . $hitState . '010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '0' . $boxState1 . $splitState . '0' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010101010101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#' . $boxPlayer[$boxId]['mainScore'] . '|' . $boxId . '|' . implode(',', $boxPlayer[$boxId]['cards']);
                                break;
                            case 'A/u286':
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                if( $tmpPar[1] != 0 && $tmpPar[1] != 1 && $tmpPar[1] != 2 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid arguments"}';
                                    exit( $response );
                                }
                                if( $tmpPar[1] == 0 ) 
                                {
                                    $boxId = 1;
                                    $boxState = '0';
                                    $boxState1 = '0';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 2;
                                    }
                                }
                                if( $tmpPar[1] == 1 ) 
                                {
                                    $boxId = 4;
                                    $boxState = '1';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 5;
                                    }
                                }
                                if( $tmpPar[1] == 2 ) 
                                {
                                    $boxId = 7;
                                    $boxState = '2';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 8;
                                    }
                                }
                                $boxPlayer[$boxId]['cards'][$boxPlayer[$boxId]['cnt']] = $cardsArr[$totalCnt];
                                $slotSettings->GetScore($boxPlayer[$boxId]);
                                $boxPlayer[$boxId]['strOut'] = $slotSettings->FormatBox($boxPlayer[$boxId]['cards'], $cardsID);
                                $totalCnt++;
                                if( $boxPlayer[$boxId]['bet'] <= $slotSettings->GetBalance() ) 
                                {
                                    $bankSum = $boxPlayer[$boxId]['bet'] / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->UpdateJackpots($boxPlayer[$boxId]['bet']);
                                    $slotSettings->SetBalance(-1 * $boxPlayer[$boxId]['bet'], 'bet');
                                    $slotSettings->SaveLogReport($response, $boxPlayer[$boxId]['bet'], 1, 0, 'bet');
                                    $allbet += $boxPlayer[$boxId]['bet'];
                                    $boxPlayer[$boxId]['bet'] = $boxPlayer[$boxId]['bet'] * 2;
                                }
                                else
                                {
                                    echo '{"error":"invalid_balance","state":"double"}';
                                }
                                if( $boxPlayer[$boxId]['mainScore'] > 21 ) 
                                {
                                    $hitState = '9';
                                    $boxPlayer[$boxId]['state'] = '9';
                                }
                                else
                                {
                                    $hitState = '6';
                                }
                                $splitState = '';
                                if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '1' ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[2]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[2] = '01';
                                        $boxPlayer[1]['state'] = '9';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '0' ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[1]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[1] = '01';
                                        $boxPlayer[1]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[5]['bet'] > 0 && $boxState1 == '1' ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[5]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[5] = '01';
                                        $boxPlayer[4]['state'] = '9';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '0' ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[4]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[4] = '01';
                                        $boxPlayer[4]['state'] = '9';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '1' ) 
                                {
                                    $splitState .= '01';
                                    if( $boxPlayer[8]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[8] = '01';
                                        $boxPlayer[7]['state'] = '9';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '0' ) 
                                {
                                    $splitState .= '00';
                                    if( $boxPlayer[7]['mainScore'] > 21 ) 
                                    {
                                        $hitState = '9';
                                        $aState[7] = '01';
                                        $boxPlayer[7]['state'] = '4';
                                    }
                                    else
                                    {
                                        $hitState = '6';
                                    }
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', $totalCnt);
                                $slotSettings->SetGameData('BlackJackAMCards', $cardsArr);
                                $slotSettings->SetGameData('BlackJackAMboxState', $boxState);
                                $slotSettings->SetGameData('BlackJackAMaState', $aState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', $hitState);
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '10' . $hitState . '010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '0' . $boxState1 . $splitState . '0' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010101010101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#';
                                break;
                            case 'A/u284':
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                if( $tmpPar[1] != 0 && $tmpPar[1] != 1 && $tmpPar[1] != 2 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid arguments"}';
                                    exit( $response );
                                }
                                if( $tmpPar[1] == 0 ) 
                                {
                                    $boxId = 1;
                                    $boxState = '0';
                                    $boxState1 = '0';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 2;
                                    }
                                }
                                if( $tmpPar[1] == 1 ) 
                                {
                                    $boxId = 4;
                                    $boxState = '1';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 5;
                                    }
                                }
                                if( $tmpPar[1] == 2 ) 
                                {
                                    $boxId = 7;
                                    $boxState = '2';
                                    $boxPlayer[$boxId]['state'] = '4';
                                    $boxState1 = '0';
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                        $boxState1 = '1';
                                        $boxId = 8;
                                    }
                                }
                                if( !isset($boxId) ) 
                                {
                                    $aState[$boxId] = '01';
                                }
                                $splitState = '';
                                if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '1' && $tmpPar[1] == 0 ) 
                                {
                                    $splitState .= '01';
                                    $boxPlayer[2]['state'] = '4';
                                }
                                else if( $boxPlayer[2]['bet'] > 0 && $boxState1 == '0' && $tmpPar[1] == 0 ) 
                                {
                                    $splitState .= '00';
                                    $boxPlayer[1]['state'] = '3';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[5]['bet'] > 0 && $boxState1 == '1' && $tmpPar[1] == 1 ) 
                                {
                                    $splitState .= '01';
                                    $boxPlayer[5]['state'] = '4';
                                }
                                else if( $boxPlayer[5]['bet'] > 0 && $boxState1 == '0' && $tmpPar[1] == 1 ) 
                                {
                                    $splitState .= '00';
                                    $boxPlayer[4]['state'] = '3';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '1' && $tmpPar[1] == 2 ) 
                                {
                                    $splitState .= '01';
                                    $boxPlayer[8]['state'] = '4';
                                }
                                else if( $boxPlayer[8]['bet'] > 0 && $boxState1 == '0' && $tmpPar[1] == 2 ) 
                                {
                                    $splitState .= '00';
                                    $boxPlayer[7]['state'] = '3';
                                }
                                else
                                {
                                    $splitState .= '00';
                                }
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', $totalCnt);
                                $slotSettings->SetGameData('BlackJackAMCards', $cardsArr);
                                $slotSettings->SetGameData('BlackJackAMboxState', $boxState);
                                $slotSettings->SetGameData('BlackJackAMaState', $aState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', '4');
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '104010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '0' . $boxState1 . $splitState . '0' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010101010101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#';
                                break;
                            case 'A/u288':
                                $cardsID = [];
                                $cnt = 0;
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    $cardsID['card_' . $i . '_0'] = $cc1;
                                    $cardsID['card_' . $i . '_1'] = $cc2;
                                    $cardsID['card_' . $i . '_2'] = $cc3;
                                    $cardsID['card_' . $i . '_3'] = $cc4;
                                    $cnt += 4;
                                }
                                $boxPlayer = $slotSettings->GetGameData('BlackJackAMBoxes');
                                $allbet = $slotSettings->GetGameData('BlackJackAMBet');
                                $totalCnt = $slotSettings->GetGameData('BlackJackAMtotalCnt');
                                $cardsArr = $slotSettings->GetGameData('BlackJackAMCards');
                                $boxState = $slotSettings->GetGameData('BlackJackAMboxState');
                                $aState = $slotSettings->GetGameData('BlackJackAMaState');
                                $insuranceState = $slotSettings->GetGameData('BlackJackAMinsuranceState');
                                $insuranceBet = $slotSettings->GetGameData('BlackJackAMinsuranceBet');
                                if( $tmpPar[1] != 0 && $tmpPar[1] != 1 && $tmpPar[1] != 2 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid arguments"}';
                                    exit( $response );
                                }
                                if( $tmpPar[1] == 0 ) 
                                {
                                    $boxId = 1;
                                    $boxState = '0';
                                    $boxState1 = '0';
                                    if( $boxPlayer[$boxId]['state'] == '8' ) 
                                    {
                                        $boxPlayer[$boxId]['state'] = '3';
                                    }
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                    }
                                    else
                                    {
                                        $insuranceState[0] = $slotSettings->HexFormat($boxPlayer[$boxId]['bet'] / 2 * $floatBet);
                                        $insuranceBet[0] = $boxPlayer[$boxId]['bet'] / 2;
                                        $bankSum = $insuranceBet[0] / 100 * $slotSettings->GetPercent();
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                        $slotSettings->UpdateJackpots($insuranceBet[0]);
                                        $slotSettings->SetBalance(-1 * $insuranceBet[0]);
                                        $slotSettings->SaveLogReport($response, $insuranceBet[0], 1, 0, 'bet');
                                    }
                                }
                                if( $tmpPar[1] == 1 ) 
                                {
                                    $boxId = 4;
                                    $boxState = '1';
                                    $boxState1 = '0';
                                    if( $boxPlayer[$boxId]['state'] == '8' ) 
                                    {
                                        $boxPlayer[$boxId]['state'] = '3';
                                    }
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                    }
                                    else
                                    {
                                        $insuranceState[1] = $slotSettings->HexFormat($boxPlayer[$boxId]['bet'] / 2 * $floatBet);
                                        $insuranceBet[1] = $boxPlayer[$boxId]['bet'] / 2;
                                        $bankSum = $insuranceBet[1] / 100 * $slotSettings->GetPercent();
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                        $slotSettings->UpdateJackpots($insuranceBet[1]);
                                        $slotSettings->SetBalance(-1 * $insuranceBet[1]);
                                        $slotSettings->SaveLogReport($response, $insuranceBet[1], 1, 0, 'bet');
                                    }
                                }
                                if( $tmpPar[1] == 2 ) 
                                {
                                    $boxId = 7;
                                    $boxState = '2';
                                    $boxState1 = '0';
                                    if( $boxPlayer[$boxId]['state'] == '8' ) 
                                    {
                                        $boxPlayer[$boxId]['state'] = '3';
                                    }
                                    if( $tmpPar[2] == 1 ) 
                                    {
                                    }
                                    else
                                    {
                                        $insuranceState[2] = $slotSettings->HexFormat($boxPlayer[$boxId]['bet'] / 2 * $floatBet);
                                        $insuranceBet[2] = $boxPlayer[$boxId]['bet'] / 2;
                                        $bankSum = $insuranceBet[2] / 100 * $slotSettings->GetPercent();
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                        $slotSettings->UpdateJackpots($insuranceBet[2]);
                                        $slotSettings->SetBalance(-1 * $insuranceBet[2]);
                                        $slotSettings->SaveLogReport($response, $insuranceBet[2], 1, 0, 'bet');
                                    }
                                }
                                $splitState = '';
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMinsuranceBet', $insuranceBet);
                                $slotSettings->SetGameData('BlackJackAMinsuranceState', $insuranceState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', '8');
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '108010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '0' . $boxState1 . $splitState . '0000000' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010' . implode('', $insuranceState) . '101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#';
                                break;
                            case 'A/u281':
                                $tmpPar[4] = $tmpPar[2];
                                $tmpPar[5] = 0;
                                $tmpPar[6] = 0;
                                $tmpPar[7] = $tmpPar[3];
                                $tmpPar[8] = 0;
                                $tmpPar[9] = 0;
                                $tmpPar[2] = 0;
                                $tmpPar[3] = 0;
                                $allbet = 0;
                                for( $i = 1; $i <= 9; $i++ ) 
                                {
                                    $tmpPar[$i] = (int)$tmpPar[$i];
                                    if( $tmpPar[$i] > 0 ) 
                                    {
                                        $allbet += ($tmpPar[$i] / $floatBet);
                                    }
                                }
                                $cardsID = [];
                                $cardsArr = [];
                                $cardsReserveArr = [];
                                $cnt = 0;
                                $slotSettings->SetGameData('BlackJackAMBankReserved', 0);
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                $smallBank = false;
                                $acceptInsurance = true;
                                if( $bank < ($allbet * 2.5) ) 
                                {
                                    $smallBank = true;
                                    if( $bank < ($allbet * 3) ) 
                                    {
                                        $acceptInsurance = false;
                                    }
                                }
                                for( $i = 2; $i <= 14; $i++ ) 
                                {
                                    $cc1 = dechex($cnt);
                                    $cc2 = dechex($cnt + 1);
                                    $cc3 = dechex($cnt + 2);
                                    $cc4 = dechex($cnt + 3);
                                    if( strlen($cc1) <= 1 ) 
                                    {
                                        $cc1 = '0' . $cc1;
                                    }
                                    if( strlen($cc2) <= 1 ) 
                                    {
                                        $cc2 = '0' . $cc2;
                                    }
                                    if( strlen($cc3) <= 1 ) 
                                    {
                                        $cc3 = '0' . $cc3;
                                    }
                                    if( strlen($cc4) <= 1 ) 
                                    {
                                        $cc4 = '0' . $cc4;
                                    }
                                    if( $smallBank && $i >= 12 ) 
                                    {
                                        $cardsID['card_' . $i . '_0'] = $cc1;
                                        $cardsID['card_' . $i . '_1'] = $cc2;
                                        $cardsID['card_' . $i . '_2'] = $cc3;
                                        $cardsID['card_' . $i . '_3'] = $cc4;
                                        $cardsReserveArr[] = 'card_' . $i . '_0';
                                        $cardsReserveArr[] = 'card_' . $i . '_1';
                                        $cardsReserveArr[] = 'card_' . $i . '_2';
                                        $cardsReserveArr[] = 'card_' . $i . '_3';
                                    }
                                    else
                                    {
                                        $cardsID['card_' . $i . '_0'] = $cc1;
                                        $cardsID['card_' . $i . '_1'] = $cc2;
                                        $cardsID['card_' . $i . '_2'] = $cc3;
                                        $cardsID['card_' . $i . '_3'] = $cc4;
                                        $cardsArr[] = 'card_' . $i . '_0';
                                        $cardsArr[] = 'card_' . $i . '_1';
                                        $cardsArr[] = 'card_' . $i . '_2';
                                        $cardsArr[] = 'card_' . $i . '_3';
                                    }
                                    $cnt += 4;
                                }
                                $boxPlayer = [];
                                for( $i = 0; $i <= 9; $i++ ) 
                                {
                                    $boxPlayer[$i] = [];
                                    $boxPlayer[$i]['cards'] = [
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63', 
                                        '63'
                                    ];
                                    $boxPlayer[$i]['mainScore'] = 0;
                                    $boxPlayer[$i]['altScore'] = 0;
                                    $boxPlayer[$i]['bet'] = 0;
                                    $boxPlayer[$i]['insurance'] = 0;
                                    $boxPlayer[$i]['cnt'] = 0;
                                    $boxPlayer[$i]['state'] = 'd';
                                    $boxPlayer[$i]['win'] = 0;
                                    $boxPlayer[$i]['blackjack'] = false;
                                }
                                if( $allbet <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    shuffle($cardsArr);
                                    $totalWin = 0;
                                    $totalCnt = 0;
                                    if( $smallBank ) 
                                    {
                                        shuffle($cardsReserveArr);
                                        if( !$acceptInsurance ) 
                                        {
                                            for( $in = 0; $in <= 10; $in++ ) 
                                            {
                                                shuffle($cardsReserveArr);
                                                if( $cardsReserveArr[0] != 'card_14_0' && $cardsReserveArr[0] != 'card_14_1' && $cardsReserveArr[0] != 'card_14_2' && $cardsReserveArr[0] != 'card_14_3' ) 
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                        $boxPlayer[0]['cards'][0] = $cardsReserveArr[0];
                                        array_shift($cardsReserveArr);
                                    }
                                    else
                                    {
                                        $boxPlayer[0]['cards'][0] = $cardsArr[$totalCnt];
                                    }
                                    $slotSettings->GetScore($boxPlayer[0]);
                                    $boxPlayer[0]['strOut'] = $slotSettings->FormatBox($boxPlayer[0]['cards'], $cardsID);
                                    $totalCnt++;
                                    $aState = [
                                        '', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01', 
                                        '01'
                                    ];
                                    $bcnt = 0;
                                    if( $tmpPar[1] > 0 ) 
                                    {
                                        $boxState = '0';
                                        $bcnt++;
                                    }
                                    if( $tmpPar[4] > 0 ) 
                                    {
                                        $boxState = '1';
                                        $bcnt++;
                                    }
                                    if( $tmpPar[7] > 0 ) 
                                    {
                                        $boxState = '2';
                                        $bcnt++;
                                    }
                                    if( $bcnt > 1 ) 
                                    {
                                        $boxState = '0';
                                    }
                                    if( $tmpPar[4] > 0 && $tmpPar[7] > 0 && $tmpPar[1] <= 0 ) 
                                    {
                                        $boxState = '1';
                                        $bcnt++;
                                    }
                                    for( $i = 1; $i <= 9; $i++ ) 
                                    {
                                        $tmpPar[$i] = (int)$tmpPar[$i];
                                        if( $tmpPar[$i] > 0 ) 
                                        {
                                            $aState[$i] = '00';
                                            $boxPlayer[$i]['cards'][$boxPlayer[$i]['cnt'] + 0] = $cardsArr[$totalCnt];
                                            $totalCnt++;
                                            $boxPlayer[$i]['cards'][$boxPlayer[$i]['cnt'] + 1] = $cardsArr[$totalCnt];
                                            $totalCnt++;
                                            $boxPlayer[$i]['bet'] = $tmpPar[$i] / $floatBet;
                                            $boxPlayer[$i]['state'] = '3';
                                            $slotSettings->GetScore($boxPlayer[$i]);
                                            if( $boxPlayer[$i]['mainScore'] >= 9 && $boxPlayer[$i]['mainScore'] <= 11 ) 
                                            {
                                                $boxPlayer[$i]['state'] = '6';
                                            }
                                            if( $boxPlayer[$i]['mainScore'] == 21 ) 
                                            {
                                                $boxPlayer[$i]['state'] = 'c';
                                                $boxPlayer[$i]['blackjack'] = true;
                                            }
                                            $tmpboxScore0 = explode('_', $boxPlayer[$i]['cards'][0]);
                                            $tmpboxScore1 = explode('_', $boxPlayer[$i]['cards'][1]);
                                            if( $tmpboxScore0[1] > 10 && $tmpboxScore0[1] < 14 ) 
                                            {
                                                $tmpboxScore0[1] = 10;
                                            }
                                            if( $tmpboxScore1[1] > 10 && $tmpboxScore1[1] < 14 ) 
                                            {
                                                $tmpboxScore1[1] = 10;
                                            }
                                            if( $boxPlayer[$i]['cnt'] == 2 && $tmpboxScore0[1] == $tmpboxScore1[1] && $boxPlayer[$i]['state'] != 'c' ) 
                                            {
                                                $boxPlayer[$i]['state'] = '5';
                                            }
                                            if( $boxPlayer[0]['mainScore'] == 11 && $boxPlayer[$i]['state'] != 'c' ) 
                                            {
                                                $boxPlayer[$i]['state'] = '8';
                                            }
                                        }
                                        $boxPlayer[$i]['strOut'] = $slotSettings->FormatBox($boxPlayer[$i]['cards'], $cardsID);
                                    }
                                    if( $totalWin <= $bank ) 
                                    {
                                        if( !$smallBank ) 
                                        {
                                            $slotSettings->SetGameData('BlackJackAMBankReserved', $allbet * 2.5);
                                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * ($allbet * 2.5));
                                        }
                                        break;
                                    }
                                }
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $slotSettings->SetGameData('BlackJackAMBoxes', $boxPlayer);
                                $slotSettings->SetGameData('BlackJackAMBet', $allbet);
                                $slotSettings->SetGameData('BlackJackAMtotalCnt', $totalCnt);
                                $slotSettings->SetGameData('BlackJackAMCards', $cardsArr);
                                $slotSettings->SetGameData('BlackJackAMCardsReserve', $cardsReserveArr);
                                $slotSettings->SetGameData('BlackJackAMsmallBank', $smallBank);
                                $slotSettings->SetGameData('BlackJackAMboxState', $boxState);
                                $slotSettings->SetGameData('BlackJackAMaState', $aState);
                                $slotSettings->SetGameData('BlackJackAMLastAction', '2');
                                $insuranceState = [
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $insuranceBet = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $slotSettings->SetGameData('BlackJackAMinsuranceState', $insuranceState);
                                $slotSettings->SetGameData('BlackJackAMinsuranceBet', $insuranceBet);
                                $slotSettings->SetGameData('BlackJackAMStep', 'betIsPlaced');
                                $betString = $slotSettings->HexFormat($boxPlayer[1]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[2]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[3]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[4]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[5]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[6]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[7]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[8]['bet'] * $floatBet) . $slotSettings->HexFormat($boxPlayer[9]['bet'] * $floatBet);
                                $response = '102010' . $balanceFormated . '10' . $slotSettings->HexFormat($allbet * $floatBet) . '10010' . $boxState . '000000000' . $boxPlayer[1]['state'] . '0' . $boxPlayer[4]['state'] . '0' . $boxPlayer[7]['state'] . $betString . '101010101010101010' . implode('', $insuranceState) . '101010' . implode('', $aState) . $boxPlayer[1]['strOut'] . $boxPlayer[2]['strOut'] . $boxPlayer[3]['strOut'] . $boxPlayer[4]['strOut'] . $boxPlayer[5]['strOut'] . $boxPlayer[6]['strOut'] . $boxPlayer[7]['strOut'] . $boxPlayer[8]['strOut'] . $boxPlayer[9]['strOut'] . $boxPlayer[0]['strOut'] . '00#';
                                $slotSettings->SaveLogReport($response, $allbet, 1, 0, 'bet');
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
