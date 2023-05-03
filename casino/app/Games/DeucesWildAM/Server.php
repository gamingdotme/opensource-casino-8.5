<?php 
namespace VanguardLTE\Games\DeucesWildAM
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
                                    $slotSettings->SetGameData('DeucesWildAMBet', $lastEvent->serverResponse->Bet);
                                    $slotSettings->SetGameData('DeucesWildAMCurrentCards', $lastEvent->serverResponse->CurrentCards);
                                    $slotSettings->SetGameData('DeucesWildAMCards', $lastEvent->serverResponse->Cards);
                                    $slotSettings->SetGameData('DeucesWildAMbalanceFormated', $balanceFormated);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', $lastEvent->serverResponse->BankReserved);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Action', $lastEvent->serverResponse->action);
                                    for( $i = 0; $i < 5; $i++ ) 
                                    {
                                        $DealCards .= ('2' . $resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                    }
                                    for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                                    {
                                        if( $lastEvent->serverResponse->Bet == $slotSettings->Bet[$i] ) 
                                        {
                                            $betLevel = $i;
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
                                $response = '00' . $lastAction . '010' . $balanceFormated . '10100' . dechex($betLevel) . $minBets . $maxBets . '100000000000000000' . $DealCards . implode('', $holds) . '0000000000' . $betsLength . $betString . '09101010101010101010';
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
                                $act = $slotSettings->GetGameData($slotSettings->slotId . 'Action');
                                $reserved = $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved');
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $reserved);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $resultCards_ = $lastEvent->serverResponse->CurrentCards;
                                    $cards = $lastEvent->serverResponse->Cards;
                                    $resultCards0 = $slotSettings->GetGameData('DeucesWildAMCurrentCards');
                                }
                                else
                                {
                                    $resultCards_ = $slotSettings->GetGameData('DeucesWildAMCurrentCards');
                                    $cards = $slotSettings->GetGameData('DeucesWildAMCards');
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
                                    $totalWin = $cc[0] * $slotSettings->GetGameData('DeucesWildAMBet');
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
                                $slotSettings->SetGameData('DeucesWildAMTotalWin', $totalWin);
                                $DealCards = '';
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $DealCards .= ('2' . $resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                }
                                $WinCards = '1010101010';
                                $winStep = $cc[1];
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
                                $response = '104010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '10001a33e8100000000000000000' . $DealCards . implode('', $holds) . '020' . $winStep . '00000000#';
                                $response_collect = $slotSettings->HexFormat(round($totalWin * 100)) . '10001a33e8100000000000000000' . $DealCards . $WinCards . '020' . $winStep . '00000000';
                                $slotSettings->SetGameData('DeucesWildAMCollect', $response_collect);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                                $logArr = (object)[
                                    'Bet' => $slotSettings->GetGameData('DeucesWildAMBet'), 
                                    'CurrentCards' => $resultCards, 
                                    'Cards' => $slotSettings->GetGameData('DeucesWildAMCards'), 
                                    'BankReserved' => $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved'), 
                                    'action' => '4', 
                                    'holds' => $holds
                                ];
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":' . json_encode($logArr) . '}';
                                $slotSettings->SaveLogReport($response_log, 0, 1, $totalWin, 'bet');
                                $slotSettings->SetGameData($slotSettings->slotId . 'Action', 4);
                                break;
                            case 'A/u257':
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $response_collect = $slotSettings->GetGameData('DeucesWildAMCollect');
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $cardsArr = [];
                                for( $i = 0; $i <= 3; $i++ ) 
                                {
                                    for( $j = 2; $j <= 14; $j++ ) 
                                    {
                                        $suit = $i;
                                        if( $j == 2 ) 
                                        {
                                            $suit += 4;
                                        }
                                        $cardsArr[] = (object)[
                                            'suit' => '' . $suit, 
                                            'value' => '' . $j
                                        ];
                                    }
                                }
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
                                $holds_ = $cc[2];
                                for( $h = 0; $h < 5; $h++ ) 
                                {
                                    if( $holds[$h] > 0 ) 
                                    {
                                        $holds[$h] = '1' . dechex($holds_[$h]);
                                    }
                                }
                                $response = '102010' . $balanceFormated . '1010001a33e8100000000000000000' . $DealCards . implode('', $holds) . '010000000000';
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
                                $slotSettings->SetGameData('DeucesWildAMBet', $allbet);
                                $slotSettings->SetGameData('DeucesWildAMCurrentCards', $resultCards);
                                $slotSettings->SetGameData('DeucesWildAMCards', $cardsArr);
                                $slotSettings->SetGameData('DeucesWildAMbalanceFormated', $balanceFormated);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Action', 2);
                                break;
                            case 'A/u2514':
                                $balanceFormated = $slotSettings->GetGameData('DeucesWildAMbalanceFormated');
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
                                    $cnt += 4;
                                }
                                $dCard = rand(2, 14);
                                $totalWin = $slotSettings->GetGameData('DeucesWildAMTotalWin');
                                $dealerCard = $cardsID['c_' . $dCard][rand(0, 3)];
                                $response = '10e010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '10001010101000000000000000101010101010101010100201000000' . $dealerCard;
                                $slotSettings->SetGameData('DeucesWildAMDealerCard', $dCard);
                                $slotSettings->SetGameData('DeucesWildAMDealerCardS', $dealerCard);
                                break;
                            case 'A/u2515':
                                $balanceFormated = $slotSettings->GetGameData('DeucesWildAMbalanceFormated');
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
                                    $cnt += 4;
                                }
                                $dCard = rand(2, 14);
                                $totalWin = $slotSettings->GetGameData('DeucesWildAMTotalWin');
                                $dealerCard = $cardsID['c_' . $dCard][rand(0, 3)];
                                $response = '10f010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '10001010101000000000000000101010101010101010100201000000' . $dealerCard;
                                $slotSettings->SetGameData('DeucesWildAMDealerCard', $dCard);
                                $slotSettings->SetGameData('DeucesWildAMDealerCardS', $dealerCard);
                                break;
                            case 'A/u2513':
                                $balanceFormated = $slotSettings->GetGameData('DeucesWildAMbalanceFormated');
                                $pSelect = $tmpPar[1];
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
                                    $cnt += 4;
                                }
                                $cWin = $slotSettings->GetGameData('DeucesWildAMTotalWin');
                                $aWin = $cWin * 2;
                                $slotSettings->SetBalance(-1 * $cWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $cWin);
                                $isWin = rand(1, 2);
                                $casbank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                if( $casbank < $aWin ) 
                                {
                                    $isWin = 0;
                                }
                                if( $isWin == 1 ) 
                                {
                                    $pCard = rand($slotSettings->GetGameData('DeucesWildAMDealerCard'), 14);
                                }
                                else
                                {
                                    $pCard = rand(2, $slotSettings->GetGameData('DeucesWildAMDealerCard'));
                                }
                                if( $slotSettings->GetGameData('DeucesWildAMDealerCard') == $pCard ) 
                                {
                                    $totalWin = $cWin;
                                }
                                else if( $slotSettings->GetGameData('DeucesWildAMDealerCard') < $pCard ) 
                                {
                                    $totalWin = $aWin;
                                }
                                else
                                {
                                    $totalWin = 0;
                                }
                                $slotSettings->SetGameData('DeucesWildAMTotalWin', $totalWin);
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBalance($totalWin);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                }
                                $pCardsArr = [
                                    $cardsID['c_' . rand(2, 14)][rand(0, 3)], 
                                    $cardsID['c_' . rand(2, 14)][rand(0, 3)], 
                                    $cardsID['c_' . rand(2, 14)][rand(0, 3)], 
                                    $cardsID['c_' . rand(2, 14)][rand(0, 3)]
                                ];
                                $pCardsArr[$pSelect - 1] = $cardsID['c_' . $pCard][rand(0, 3)];
                                $pCardsStr = implode('', $pCardsArr);
                                $response = '10d010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '10001a33e810' . $slotSettings->GetGameData('DeucesWildAMDealerCardS') . $pCardsStr . '0000001723c1d23b21b10101023b21b020100000' . $pSelect . $slotSettings->GetGameData('DeucesWildAMDealerCardS') . '#' . $pCard . '|' . $slotSettings->GetGameData('DeucesWildAMDealerCardS');
                                if( $totalWin <= 0 ) 
                                {
                                    $totalWin = -1 * $cWin;
                                }
                                $response_log = '{"responseEvent":"gambleResult","serverResponse":{"totalWin":' . $totalWin . '}}';
                                $slotSettings->SaveLogReport($response_log, $slotSettings->GetGameData('DeucesWildAMBet'), 1, $totalWin, 'double');
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
