<?php 
namespace VanguardLTE\Games\MultiWinAM
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
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $lastAction = $lastEvent->serverResponse->action;
                                    $holds = $lastEvent->serverResponse->holds;
                                    $resultCards = $lastEvent->serverResponse->CurrentCards;
                                    $DealCards = '';
                                    $slotSettings->SetGameData('MultiWinAMBet', $lastEvent->serverResponse->Bet);
                                    $slotSettings->SetGameData('MultiWinAMCurrentCards', $lastEvent->serverResponse->CurrentCards);
                                    $slotSettings->SetGameData('MultiWinAMCards', $lastEvent->serverResponse->Cards);
                                    $slotSettings->SetGameData('MultiWinAMbalanceFormated', $balanceFormated);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', $lastEvent->serverResponse->BankReserved);
                                    for( $i = 0; $i < 5; $i++ ) 
                                    {
                                        $DealCards .= ('2' . $resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                    }
                                    $betLevel = 0;
                                    for( $b = 0; $b < count($slotSettings->Bet); $b++ ) 
                                    {
                                        if( $slotSettings->Bet[$b] == $slotSettings->GetGameData('MultiWinAMBet') ) 
                                        {
                                            $betLevel = $b;
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    $lastAction = 0;
                                    $holds = [
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10'
                                    ];
                                    $DealCards = '1010101010';
                                    $betLevel = 0;
                                }
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
                                    $cardsID['c_' . $i] = [
                                        $cc1, 
                                        $cc2, 
                                        $cc3, 
                                        $cc4
                                    ];
                                    shuffle($cardsID['c_' . $i]);
                                    $cnt += 4;
                                }
                                $slotSettings->SetGameData('MultiWinAMHistoryCards', [
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0], 
                                    $cardsID['c_' . rand(2, 14)][0]
                                ]);
                                $response = '00' . $lastAction . '010' . $balanceFormated . '10100' . dechex($betLevel) . $minBets . $maxBets . '100000000000000000' . $DealCards . implode('', $holds) . '0000030000' . $betsLength . $betString . '09101010101010101010010201001010151a26431f4';
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
                                $response = '100010' . $balanceFormated . '101000' . $minBets . $maxBets . '10000000000000000010101010101010101010000000000000#';
                                break;
                            case 'A/u253':
                                $reserved = $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved');
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $reserved);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $resultCards_ = $lastEvent->serverResponse->CurrentCards;
                                    $cards = $lastEvent->serverResponse->Cards;
                                    $resultCards0 = $slotSettings->GetGameData('MultiWinAMCurrentCards');
                                }
                                else
                                {
                                    $resultCards_ = $slotSettings->GetGameData('MultiWinAMCurrentCards');
                                    $cards = $slotSettings->GetGameData('MultiWinAMCards');
                                }
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $resultCards = $resultCards_;
                                    $crdCount = 0;
                                    $totalWin = 0;
                                    shuffle($cards);
                                    for( $j = 0; $j < 5; $j++ ) 
                                    {
                                        if( $tmpPar[$j + 1] <= 0 ) 
                                        {
                                            $resultCards[$j] = $cards[$j];
                                            $crdCount++;
                                        }
                                    }
                                    $cc = $slotSettings->GetCombination([
                                        $resultCards[0]->value, 
                                        $resultCards[1]->value, 
                                        $resultCards[2]->value, 
                                        $resultCards[3]->value, 
                                        $resultCards[4]->value
                                    ], [
                                        $resultCards[0]->suit, 
                                        $resultCards[1]->suit, 
                                        $resultCards[2]->suit, 
                                        $resultCards[3]->suit, 
                                        $resultCards[4]->suit
                                    ], false);
                                    $totalWin = $cc[0] * $slotSettings->GetGameData('MultiWinAMBet');
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
                                $slotSettings->SetGameData('MultiWinAMTotalWin', $totalWin);
                                $DealCards = '';
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $DealCards .= ('2' . $resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                }
                                $WinCards = '1010101010';
                                $winStep = dechex($cc[1]);
                                $holds = [
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $holds_ = $cc[2];
                                for( $h = 0; $h < 5; $h++ ) 
                                {
                                    if( $holds[$h] > 0 ) 
                                    {
                                        $holds[$h] = '1' . dechex($holds_[$h]);
                                    }
                                }
                                $response = '104010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '10011f41388100209030804070506' . $DealCards . implode('', $holds) . '020' . $winStep . '03000000001010151a26431f4';
                                $response_collect = $slotSettings->HexFormat(round($totalWin * 100)) . '10001a33e8100000000000000000' . $DealCards . $WinCards . '020' . $winStep . '00000000';
                                $slotSettings->SetGameData('MultiWinAMCollect', $response_collect);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                                $logArr = (object)[
                                    'Bet' => $slotSettings->GetGameData('MultiWinAMBet'), 
                                    'CurrentCards' => $resultCards, 
                                    'Cards' => $slotSettings->GetGameData('MultiWinAMCards'), 
                                    'BankReserved' => $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved'), 
                                    'action' => '4', 
                                    'holds' => $holds
                                ];
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":' . json_encode($logArr) . '}';
                                $slotSettings->SaveLogReport($response_log, 0, 1, $totalWin, 'bet');
                                break;
                            case 'A/u257':
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $response_collect = $slotSettings->GetGameData('MultiWinAMCollect');
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '107010' . $balanceFormated . $response_collect;
                                break;
                            case 'A/u251':
                                $betLine = $slotSettings->Bet[$tmpPar[1]];
                                $postData['bet'] = $betLine;
                                $allbet = $postData['bet'];
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
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['bet']);
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                                $slotSettings->SetGameData('MultiWinAMDoubleBalance', $balanceFormated);
                                $cardsArr = [];
                                for( $i = 0; $i <= 3; $i++ ) 
                                {
                                    for( $j = 2; $j <= 14; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => '' . $i, 
                                            'value' => '' . $j
                                        ];
                                    }
                                }
                                $cardsArr[] = (object)[
                                    'suit' => '0', 
                                    'value' => '15'
                                ];
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    shuffle($cardsArr);
                                    $resultCards = [
                                        $cardsArr[0], 
                                        $cardsArr[1], 
                                        $cardsArr[2], 
                                        $cardsArr[3], 
                                        $cardsArr[4]
                                    ];
                                    $cc = $slotSettings->GetCombination([
                                        $resultCards[0]->value, 
                                        $resultCards[1]->value, 
                                        $resultCards[2]->value, 
                                        $resultCards[3]->value, 
                                        $resultCards[4]->value
                                    ], [
                                        $resultCards[0]->suit, 
                                        $resultCards[1]->suit, 
                                        $resultCards[2]->suit, 
                                        $resultCards[3]->suit, 
                                        $resultCards[4]->suit
                                    ], true);
                                    $totalWin = $cc[0] * $allbet;
                                    if( $totalWin <= $bank ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', $totalWin);
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        break;
                                    }
                                }
                                $DealCards = '';
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $DealCards .= ('2' . $resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                    array_shift($cardsArr);
                                }
                                $holds = [
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $holds0 = [
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $holds_ = $cc[2];
                                for( $h = 0; $h < 5; $h++ ) 
                                {
                                    if( $holds[$h] > 0 ) 
                                    {
                                        $holds[$h] = '1' . dechex($holds_[$h]);
                                    }
                                    if( $cc[4][$h] > 0 ) 
                                    {
                                        $holds0[$h] = '1' . dechex($cc[4][$h]);
                                    }
                                }
                                $winStep = dechex($cc[1]);
                                $winStepTip = dechex($cc[3]);
                                if( $cc[1] == 0 && $cc[3] > 0 ) 
                                {
                                    $holds = $holds0;
                                }
                                $response = '102010' . $balanceFormated . '1010001a33e810161a31071d1e3104' . $DealCards . implode('', $holds) . '010' . $winStep . '000000000' . $winStepTip . '1010151a26431f4';
                                $logArr = (object)[
                                    'Bet' => $allbet, 
                                    'CurrentCards' => $resultCards, 
                                    'Cards' => $cardsArr, 
                                    'BankReserved' => $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved'), 
                                    'action' => '2', 
                                    'holds' => $holds
                                ];
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":' . json_encode($logArr) . '}';
                                $slotSettings->SaveLogReport($response_log, $allbet, 1, 0, 'bet');
                                $slotSettings->SetGameData('MultiWinAMBet', $allbet);
                                $slotSettings->SetGameData('MultiWinAMCurrentCards', $resultCards);
                                $slotSettings->SetGameData('MultiWinAMCards', $cardsArr);
                                $slotSettings->SetGameData('MultiWinAMbalanceFormated', $balanceFormated);
                                $slotSettings->SetGameData('MultiWinAMGambleStep', 0);
                                break;
                            case 'A/u258':
                                $doubleWin = rand(1, 2);
                                $winall = $slotSettings->GetGameData('MultiWinAMTotalWin');
                                $historyCards = $slotSettings->GetGameData('MultiWinAMHistoryCards');
                                $stepGamble = $slotSettings->GetGameData('MultiWinAMGambleStep');
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                $reportWin = $winall;
                                $reportBet = $winall;
                                $userSelect = $tmpPar[1];
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
                                    $cardsID['c_' . $i] = [
                                        $cc1, 
                                        $cc2, 
                                        $cc3, 
                                        $cc4
                                    ];
                                    shuffle($cardsID['c_' . $i]);
                                    $cnt += 4;
                                }
                                if( $bank < ($winall * 2) || $stepGamble >= 7 ) 
                                {
                                    $doubleWin = 0;
                                }
                                if( $doubleWin == 1 ) 
                                {
                                    if( $userSelect == 3 ) 
                                    {
                                        $playerCard = rand(2, 7);
                                    }
                                    else
                                    {
                                        $playerCard = rand(7, 14);
                                    }
                                }
                                else
                                {
                                    if( $userSelect == 2 ) 
                                    {
                                        $playerCard = rand(2, 7);
                                    }
                                    else
                                    {
                                        $playerCard = rand(7, 14);
                                    }
                                    $winall = 0;
                                }
                                if( $winall > 0 ) 
                                {
                                    $slotSettings->SetBalance($winall);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $winall);
                                }
                                else
                                {
                                    $slotSettings->SetBalance(-1 * $reportWin);
                                    $reportWin = -1 * $reportWin;
                                }
                                $stepGamble++;
                                array_pop($historyCards);
                                array_unshift($historyCards, $cardsID['c_' . $playerCard][0]);
                                $response = '108010' . $slotSettings->GetGameData('MultiWinAMDoubleBalance') . $slotSettings->HexFormat($winall * 100) . '10011f4138810' . implode('', $historyCards) . '10101010101010101010020103000' . dechex($stepGamble) . '00001010151a26431f4';
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $slotSettings->SetGameData('MultiWinAMTotalWin', $winall);
                                $slotSettings->SetGameData('MultiWinAMHistoryCards', $historyCards);
                                $slotSettings->SetGameData('MultiWinAMGambleStep', $stepGamble);
                                $response_log = '{"responseEvent":"gambleResult","serverResponse":{}}';
                                $slotSettings->SaveLogReport($response_log, $reportBet, 1, $reportWin, 'double');
                                break;
                            case 'A/u259':
                                $winall = $slotSettings->GetGameData('MultiWinAMTotalWin');
                                if( $winall > 0.01 ) 
                                {
                                    $winall22 = sprintf('%01.2f', $winall / 2);
                                }
                                else
                                {
                                    $winall22 = 0;
                                }
                                $winall = $winall - $winall22;
                                $user_balance = $slotSettings->GetBalance() - $winall;
                                $slotSettings->SetGameData('MultiWinAMTotalWin', $winall);
                                $user_balance = sprintf('%01.2f', $user_balance);
                                $str_balance = str_replace('.', '', $user_balance . '');
                                $hexBalance = dechex($str_balance - 0);
                                $rtnBalance = strlen($hexBalance) . $hexBalance;
                                $slotSettings->SetGameData('MultiWinAMDoubleBalance', $rtnBalance);
                                $winall = sprintf('%01.2f', $winall) * $floatBet;
                                $winall_h1 = str_replace('.', '', $winall . '');
                                $winall_h = dechex($winall_h1);
                                $doubleCards = '26280b2714161d0c';
                                $response = '109010' . $slotSettings->GetGameData('MultiWinAMDoubleBalance') . strlen($winall_h) . $winall_h . '10001a33e810060d15210f07131e21324f21b22e2291024f1022e1002010300000004101012152322fa';
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
