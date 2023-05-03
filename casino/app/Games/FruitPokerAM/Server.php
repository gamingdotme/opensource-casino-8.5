<?php 
namespace VanguardLTE\Games\FruitPokerAM
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'cardsHistory', [
                                    '07', 
                                    '05', 
                                    '07', 
                                    '07', 
                                    '07', 
                                    '07', 
                                    '03', 
                                    '03'
                                ]);
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'Cards') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Cards', []);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'cardsInStack') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'cardsInStack', [
                                        11, 
                                        16, 
                                        16, 
                                        19, 
                                        19, 
                                        12, 
                                        45, 
                                        1
                                    ]);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'BonusFourArr') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusFourArr', [
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ]);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'BonusProgress') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusProgress', 0);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'BonusPos') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusPos', 0);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'BonusSum') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSum', 0);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'CurrentCards') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentCards', []);
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'Action') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Action', 0);
                                }
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData('FruitPokerAMBonusWin', 0);
                                $slotSettings->SetGameData('FruitPokerAMFreeGames', 0);
                                $slotSettings->SetGameData('FruitPokerAMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('FruitPokerAMTotalWin', 0);
                                $slotSettings->SetGameData('FruitPokerAMFreeBalance', 0);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->UnpackGameData($lastEvent->serverResponse->allData);
                                }
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
                                $BonusSum = $slotSettings->GetGameData('FruitPokerAMBonusSum');
                                $BonusProgress = $slotSettings->GetGameData('FruitPokerAMBonusProgress');
                                $BonusPos = $slotSettings->GetGameData('FruitPokerAMBonusPos');
                                $BonusFourArr = $slotSettings->GetGameData('FruitPokerAMBonusFourArr');
                                $resultCards = $slotSettings->GetGameData('FruitPokerAMCurrentCards');
                                $cardsInStack = $slotSettings->GetGameData('FruitPokerAMcardsInStack');
                                $cards = $slotSettings->GetGameData('FruitPokerAMCards');
                                $cardsInStackStr = [
                                    '0b', 
                                    '0f', 
                                    '10', 
                                    '11', 
                                    '11', 
                                    '0c', 
                                    '2d', 
                                    '01'
                                ];
                                for( $i = 0; $i < count($cardsInStack); $i++ ) 
                                {
                                    $cardsInStackStr[$i] = dechex($cardsInStack[$i]);
                                    if( strlen($cardsInStackStr[$i]) <= 1 ) 
                                    {
                                        $cardsInStackStr[$i] = '0' . $cardsInStackStr[$i];
                                    }
                                }
                                $cardsOut = 139 - count($cards);
                                $BonusProgress0 = 40;
                                if( $BonusSum >= 1 && $BonusSum < 10 ) 
                                {
                                    $BonusProgress0 = 41;
                                }
                                if( $BonusSum >= 10 && $BonusSum < 20 ) 
                                {
                                    $BonusProgress0 = 42;
                                }
                                if( $BonusSum >= 20 && $BonusSum < 30 ) 
                                {
                                    $BonusProgress0 = 43;
                                }
                                if( $BonusSum >= 30 && $BonusSum < 40 ) 
                                {
                                    $BonusProgress0 = 44;
                                }
                                if( $BonusSum >= 40 && $BonusSum < 50 ) 
                                {
                                    $BonusProgress0 = 45;
                                }
                                if( $BonusSum >= 50 ) 
                                {
                                    $BonusProgress0 = 46;
                                }
                                if( $BonusFourArr[0] ) 
                                {
                                    $BonusPos = 1;
                                }
                                if( $BonusFourArr[1] ) 
                                {
                                    $BonusPos = 2;
                                }
                                if( $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 4;
                                }
                                if( $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 8;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] ) 
                                {
                                    $BonusPos = 3;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 5;
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 6;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 9;
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'a';
                                }
                                if( $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'c';
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 7;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'b';
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'd';
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'e';
                                }
                                $DealCards = '';
                                if( count($resultCards) > 0 && is_array($resultCards) ) 
                                {
                                    for( $i = 0; $i < count($resultCards); $i++ ) 
                                    {
                                        $DealCards .= ($resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                    }
                                }
                                else
                                {
                                    $DealCards = '1010101010';
                                }
                                $holds = $slotSettings->GetGameData('FruitPokerAMHolds');
                                $act = $slotSettings->GetGameData('FruitPokerAMAction');
                                if( $act != 2 && $act != 4 ) 
                                {
                                    $act = 0;
                                }
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'Holds') ) 
                                {
                                    $holds = [
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10'
                                    ];
                                }
                                $response = '00' . $act . '010' . $balanceFormated . '101000' . $minBets . $maxBets . '100000000000000000' . $DealCards . implode('', $holds) . '0000030000' . $betsLength . $betString . '09101010101010101010' . $slotSettings->HexFormat($cardsOut) . '0000' . implode('', $cardsInStackStr) . $BonusProgress0 . '02041101010101010101010' . $slotSettings->HexFormat($BonusSum * 100) . $slotSettings->HexFormat($BonusSum * 100) . '1010101010101010101' . $BonusPos . '1' . $BonusPos . '1' . $BonusPos . '1010101010101' . $BonusPos . '';
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
                                $response = '100010' . $balanceFormated . '101000' . $minBets . $maxBets . '10000000000000000010101010101010101010000003000000100100000000000000000010101010101010101010101010101010101010100010101010101010101010#';
                                break;
                            case 'A/u253':
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $resultCards = $slotSettings->GetGameData('FruitPokerAMCurrentCards');
                                $cards = $slotSettings->GetGameData('FruitPokerAMCards');
                                $totalWin = 0;
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $l = 0; $l <= 2000; $l++ ) 
                                {
                                    $BonusSum = $slotSettings->GetGameData('FruitPokerAMBonusSum');
                                    $cardsInStack = $slotSettings->GetGameData('FruitPokerAMcardsInStack');
                                    $BonusProgress = $slotSettings->GetGameData('FruitPokerAMBonusProgress');
                                    $BonusPos = $slotSettings->GetGameData('FruitPokerAMBonusPos');
                                    $BonusFourArr = $slotSettings->GetGameData('FruitPokerAMBonusFourArr');
                                    $resultCards = $slotSettings->GetGameData('FruitPokerAMCurrentCards');
                                    shuffle($cards);
                                    $crdCount = 0;
                                    for( $j = 0; $j < 5; $j++ ) 
                                    {
                                        if( $tmpPar[$j + 1] <= 0 ) 
                                        {
                                            $resultCards[$j] = $cards[$crdCount];
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
                                    ]);
                                    $payrate = $cc[0];
                                    $rang = $cc[1];
                                    $totalWin = $payrate * $slotSettings->GetGameData('FruitPokerAMBet');
                                    $holds = [
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10', 
                                        '10'
                                    ];
                                    $holds_ = $slotSettings->GetTips([
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
                                    $cherryCount = 0;
                                    $bellCount = 0;
                                    $melonCount = 0;
                                    $plumCount = 0;
                                    $wildsCount = 0;
                                    $cherryPos = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    for( $i = 0; $i < 5; $i++ ) 
                                    {
                                        if( $resultCards[$i]->value == 6 ) 
                                        {
                                            $cherryCount++;
                                            $cherryPos[$i] = 6;
                                        }
                                        if( $resultCards[$i]->value == 8 ) 
                                        {
                                            $wildsCount++;
                                        }
                                        if( $resultCards[$i]->value == 3 ) 
                                        {
                                            $melonCount++;
                                        }
                                        if( $resultCards[$i]->value == 4 ) 
                                        {
                                            $plumCount++;
                                        }
                                        if( $resultCards[$i]->value == 2 ) 
                                        {
                                            $bellCount++;
                                        }
                                    }
                                    if( $BonusProgress < 45 && $cherryCount == 2 ) 
                                    {
                                        $BonusProgress++;
                                        $BonusSum += $slotSettings->GetGameData('FruitPokerAMBet');
                                        $rang = 2;
                                        for( $i = 0; $i < 5; $i++ ) 
                                        {
                                            if( $cherryPos[$i] == 6 ) 
                                            {
                                                $holds[$i] = '16';
                                            }
                                        }
                                    }
                                    if( $cherryCount + $wildsCount >= 4 ) 
                                    {
                                        $BonusFourArr[3] = 1;
                                    }
                                    if( $bellCount + $wildsCount >= 4 ) 
                                    {
                                        $BonusFourArr[0] = 1;
                                    }
                                    if( $melonCount + $wildsCount >= 4 ) 
                                    {
                                        $BonusFourArr[1] = 1;
                                    }
                                    if( $plumCount + $wildsCount >= 4 ) 
                                    {
                                        $BonusFourArr[2] = 1;
                                    }
                                    if( $BonusFourArr[0] ) 
                                    {
                                        $BonusPos = 1;
                                    }
                                    if( $BonusFourArr[1] ) 
                                    {
                                        $BonusPos = 2;
                                    }
                                    if( $BonusFourArr[2] ) 
                                    {
                                        $BonusPos = 4;
                                    }
                                    if( $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 8;
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[1] ) 
                                    {
                                        $BonusPos = 3;
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[2] ) 
                                    {
                                        $BonusPos = 5;
                                    }
                                    if( $BonusFourArr[1] && $BonusFourArr[2] ) 
                                    {
                                        $BonusPos = 6;
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 9;
                                    }
                                    if( $BonusFourArr[1] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'a';
                                    }
                                    if( $BonusFourArr[2] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'c';
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] ) 
                                    {
                                        $BonusPos = 7;
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'b';
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'd';
                                    }
                                    if( $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'e';
                                    }
                                    if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                    {
                                        $BonusPos = 'f';
                                        $rang = 9;
                                        $totalWin += ($slotSettings->GetGameData('FruitPokerAMBet') * 50);
                                    }
                                    if( $BonusSum >= 100 ) 
                                    {
                                        $totalWin += $BonusSum;
                                    }
                                    if( $l >= 1800 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"calculate loop limit"}';
                                        exit( $response );
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
                                $slotSettings->SetGameData('FruitPokerAMTotalWin', $totalWin);
                                $DealCards = '';
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $DealCards .= ($resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                }
                                $WinCards = '1010101010';
                                if( $totalWin > 0 ) 
                                {
                                    for( $h = 0; $h < 5; $h++ ) 
                                    {
                                        if( $holds[$h] > 0 ) 
                                        {
                                            $holds[$h] = '1' . $holds_[$h];
                                        }
                                    }
                                }
                                $winStep = $rang;
                                $cardsInStackStr = [
                                    '0b', 
                                    '0f', 
                                    '10', 
                                    '11', 
                                    '11', 
                                    '0c', 
                                    '2d', 
                                    '01'
                                ];
                                for( $i = 0; $i < $crdCount; $i++ ) 
                                {
                                    $curc = $cards[$i]->value;
                                    $cardsInStack[$curc - 1]--;
                                }
                                for( $i = 0; $i < $crdCount; $i++ ) 
                                {
                                    array_shift($cards);
                                }
                                for( $i = 0; $i < count($cardsInStack); $i++ ) 
                                {
                                    $cardsInStackStr[$i] = dechex($cardsInStack[$i]);
                                    if( strlen($cardsInStackStr[$i]) <= 1 ) 
                                    {
                                        $cardsInStackStr[$i] = '0' . $cardsInStackStr[$i];
                                    }
                                }
                                $cardsOut = 139 - count($cards);
                                $BonusProgress0 = 40;
                                if( $BonusSum >= 1 && $BonusSum < 10 ) 
                                {
                                    $BonusProgress0 = 41;
                                }
                                if( $BonusSum >= 10 && $BonusSum < 20 ) 
                                {
                                    $BonusProgress0 = 42;
                                }
                                if( $BonusSum >= 20 && $BonusSum < 30 ) 
                                {
                                    $BonusProgress0 = 43;
                                }
                                if( $BonusSum >= 30 && $BonusSum < 40 ) 
                                {
                                    $BonusProgress0 = 44;
                                }
                                if( $BonusSum >= 40 && $BonusSum < 50 ) 
                                {
                                    $BonusProgress0 = 45;
                                }
                                if( $BonusSum >= 50 ) 
                                {
                                    $BonusProgress0 = 46;
                                }
                                $response = '104010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . '100' . dechex($slotSettings->GetGameData('FruitPokerAMBetCounter')) . '1a33e8150000000000000000' . $DealCards . implode('', $holds) . '030' . dechex($winStep) . '03000000' . $slotSettings->HexFormat($cardsOut) . '0000' . implode('', $cardsInStackStr) . $BonusProgress0 . '10101010101010101010' . $slotSettings->HexFormat($BonusSum * 100) . '101010101010101010101' . $BonusPos . '1' . $BonusPos . '1' . $BonusPos . '1010101010101' . $BonusPos . '#';
                                $response_collect = $slotSettings->HexFormat(round($totalWin * 100)) . '10001a33e8100000000000000000' . $DealCards . implode('', $holds) . '020' . $winStep . '03000000' . $slotSettings->HexFormat($cardsOut) . '0000' . implode('', $cardsInStackStr) . '10101010101010101010101010101010101010100010101010101010101010#' . json_encode($cardsInStack);
                                $response_collect0 = '10001a33e8100000000000000000' . $DealCards . implode('', $holds) . '020' . $winStep . '03000000' . $slotSettings->HexFormat($cardsOut) . '0000' . implode('', $cardsInStackStr) . '10101010101010101010101010101010101010100010101010101010101010#' . json_encode($cardsInStack);
                                $response_gamble = $DealCards . implode('', $holds) . '030303000100110000' . implode('', $cardsInStackStr) . $BonusProgress . '10101010101010101010' . $slotSettings->HexFormat($BonusSum * 100) . '101010101010101010101' . $BonusPos . '101010101010101010#' . json_encode($cardsOut);
                                $response_gamble = '131616111610161610160203030001001100000b101013130c2c0110101010101010101010101010101010101010101010101010101010101010';
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusFourArr[0] = 0;
                                    $BonusFourArr[1] = 0;
                                    $BonusFourArr[2] = 0;
                                    $BonusFourArr[3] = 0;
                                }
                                $slotSettings->SetGameData('FruitPokerAMCollect', $response_collect);
                                $slotSettings->SetGameData('FruitPokerAMCollect0', $response_collect0);
                                $slotSettings->SetGameData('FruitPokerAMGamble', $response_gamble);
                                $slotSettings->SetGameData('FruitPokerAMHolds', $holds);
                                $slotSettings->SetGameData('FruitPokerAMAction', '4');
                                $slotSettings->SetGameData('FruitPokerAMBonusFourArr', $BonusFourArr);
                                $slotSettings->SetGameData('FruitPokerAMBonusSum', $BonusSum);
                                $slotSettings->SetGameData('FruitPokerAMBonusProgress', $BonusProgress);
                                $slotSettings->SetGameData('FruitPokerAMBonusPos', $BonusPos);
                                $slotSettings->SetGameData('FruitPokerAMCurrentCards', $resultCards);
                                $slotSettings->SetGameData('FruitPokerAMCards', $cards);
                                $slotSettings->SetGameData('FruitPokerAMcardsInStack', $cardsInStack);
                                if( $BonusSum >= 100 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusProgress', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSum', 0);
                                }
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"allData":' . $slotSettings->PackGameData() . '}}';
                                $slotSettings->SaveLogReport($response_log, 0, 1, $totalWin, 'bet');
                                break;
                            case 'A/u257':
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $response_collect = $slotSettings->GetGameData('FruitPokerAMCollect');
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response = '107010' . $balanceFormated . $response_collect;
                                $slotSettings->SetGameData($slotSettings->slotId . 'Cards', []);
                                $slotSettings->SetGameData($slotSettings->slotId . 'cardsInStack', [
                                    11, 
                                    16, 
                                    16, 
                                    19, 
                                    19, 
                                    12, 
                                    45, 
                                    1
                                ]);
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
                                $cardsArr = $slotSettings->GetGameData('FruitPokerAMCards');
                                $cardsInStack = $slotSettings->GetGameData('FruitPokerAMcardsInStack');
                                $BonusSum = $slotSettings->GetGameData('FruitPokerAMBonusSum');
                                $BonusProgress = $slotSettings->GetGameData('FruitPokerAMBonusProgress');
                                $BonusPos = $slotSettings->GetGameData('FruitPokerAMBonusPos');
                                $BonusFourArr = $slotSettings->GetGameData('FruitPokerAMBonusFourArr');
                                $resetStack = 0;
                                if( count($cardsArr) < 40 ) 
                                {
                                    $resetStack = 1;
                                    $cardsInStack = [
                                        11, 
                                        16, 
                                        16, 
                                        19, 
                                        19, 
                                        12, 
                                        45, 
                                        1
                                    ];
                                    $cardsArr = [];
                                    for( $j = 1; $j <= 11; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 1
                                        ];
                                    }
                                    for( $j = 1; $j <= 16; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 2
                                        ];
                                    }
                                    for( $j = 1; $j <= 16; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 3
                                        ];
                                    }
                                    for( $j = 1; $j <= 19; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 4
                                        ];
                                    }
                                    for( $j = 1; $j <= 19; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 5
                                        ];
                                    }
                                    for( $j = 1; $j <= 12; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 6
                                        ];
                                    }
                                    for( $j = 1; $j <= 45; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 7
                                        ];
                                    }
                                    for( $j = 1; $j <= 1; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => 1, 
                                            'value' => 8
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
                                    ]);
                                    $payrate = $cc[0];
                                    $rang = $cc[1];
                                    $totalWin = $payrate * $allbet;
                                    if( $totalWin <= $bank ) 
                                    {
                                        break;
                                    }
                                }
                                $DealCards = '';
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $DealCards .= ($resultCards[$i]->suit . dechex($resultCards[$i]->value));
                                }
                                $holds = [
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10', 
                                    '10'
                                ];
                                $holds_ = $slotSettings->GetTips([
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
                                ]);
                                for( $h = 0; $h < 5; $h++ ) 
                                {
                                    if( $holds[$h] > 0 ) 
                                    {
                                        $holds[$h] = '1' . $holds_[$h];
                                    }
                                }
                                $cardsInStackStr = [
                                    '0b', 
                                    '0f', 
                                    '10', 
                                    '11', 
                                    '11', 
                                    '0c', 
                                    '2d', 
                                    '01'
                                ];
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    $curc = $cardsArr[$i]->value;
                                    $cardsInStack[$curc - 1]--;
                                }
                                for( $i = 0; $i < 5; $i++ ) 
                                {
                                    array_shift($cardsArr);
                                }
                                for( $i = 0; $i < count($cardsInStack); $i++ ) 
                                {
                                    $cardsInStackStr[$i] = dechex($cardsInStack[$i]);
                                    if( strlen($cardsInStackStr[$i]) <= 1 ) 
                                    {
                                        $cardsInStackStr[$i] = '0' . $cardsInStackStr[$i];
                                    }
                                }
                                $cardsOut = 139 - count($cardsArr);
                                if( $BonusFourArr[0] ) 
                                {
                                    $BonusPos = 1;
                                }
                                if( $BonusFourArr[1] ) 
                                {
                                    $BonusPos = 2;
                                }
                                if( $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 4;
                                }
                                if( $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 8;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] ) 
                                {
                                    $BonusPos = 3;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 5;
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 6;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 9;
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'a';
                                }
                                if( $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'c';
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] ) 
                                {
                                    $BonusPos = 7;
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'b';
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'd';
                                }
                                if( $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'e';
                                }
                                if( $BonusFourArr[0] && $BonusFourArr[1] && $BonusFourArr[2] && $BonusFourArr[3] ) 
                                {
                                    $BonusPos = 'f';
                                }
                                $BonusProgress0 = 40;
                                if( $BonusSum >= 1 && $BonusSum < 10 ) 
                                {
                                    $BonusProgress0 = 41;
                                }
                                if( $BonusSum >= 10 && $BonusSum < 20 ) 
                                {
                                    $BonusProgress0 = 42;
                                }
                                if( $BonusSum >= 20 && $BonusSum < 30 ) 
                                {
                                    $BonusProgress0 = 43;
                                }
                                if( $BonusSum >= 30 && $BonusSum < 40 ) 
                                {
                                    $BonusProgress0 = 44;
                                }
                                if( $BonusSum >= 40 && $BonusSum < 50 ) 
                                {
                                    $BonusProgress0 = 45;
                                }
                                if( $BonusSum >= 50 ) 
                                {
                                    $BonusProgress0 = 46;
                                }
                                $response = '102010' . $balanceFormated . '10100' . dechex($tmpPar[1]) . '1f41388150000000000000000' . $DealCards . implode('', $holds) . '010003000000' . $slotSettings->HexFormat($cardsOut) . '0' . $resetStack . '00' . implode('', $cardsInStackStr) . '' . $BonusProgress0 . '10101010101010101010' . $slotSettings->HexFormat($BonusSum * 100) . '101010101010101010101' . $BonusPos . '1' . $BonusPos . '1' . $BonusPos . '1010101010101' . $BonusPos . '';
                                $slotSettings->SetGameData('FruitPokerAMHolds', $holds);
                                $slotSettings->SetGameData('FruitPokerAMAction', '2');
                                $slotSettings->SetGameData('FruitPokerAMBet', $allbet);
                                $slotSettings->SetGameData('FruitPokerAMBetCounter', $tmpPar[1]);
                                $slotSettings->SetGameData('FruitPokerAMCurrentCards', $resultCards);
                                $slotSettings->SetGameData('FruitPokerAMCards', $cardsArr);
                                $slotSettings->SetGameData('FruitPokerAMcardsInStack', $cardsInStack);
                                $slotSettings->SetGameData('FruitPokerAMbalanceFormated', $balanceFormated);
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"allData":' . $slotSettings->PackGameData() . '}}';
                                $slotSettings->SaveLogReport($response_log, $allbet, 1, 0, 'bet');
                                break;
                            case 'A/u258':
                                $doubleWin = rand(1, 2);
                                $winall = $slotSettings->GetGameData('FruitPokerAMTotalWin');
                                $cardsHistory = $slotSettings->GetGameData('FruitPokerAMcardsHistory');
                                $dbet = $winall;
                                $daction = $tmpPar[1];
                                if( $slotSettings->MaxWin < ($winall * $slotSettings->CurrentDenom) ) 
                                {
                                    $doubleWin = 0;
                                }
                                if( $winall > 0 ) 
                                {
                                    $slotSettings->SetBalance(-1 * $winall);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $winall);
                                }
                                $ucard = '';
                                $casbank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                if( $casbank < ($winall * 2) ) 
                                {
                                    $doubleWin = 0;
                                }
                                if( $daction == 2 ) 
                                {
                                    if( $doubleWin == 1 ) 
                                    {
                                        $ucard = rand(1, 5);
                                        $winall = $winall * 2;
                                    }
                                    else
                                    {
                                        $ucard = rand(6, 7);
                                        $winall = 0;
                                    }
                                }
                                else if( $doubleWin == 1 ) 
                                {
                                    $winall = $winall * 2;
                                    $ucard = rand(6, 8);
                                }
                                else
                                {
                                    $ucard = rand(1, 5);
                                    $winall = 0;
                                }
                                $winall = sprintf('%01.2f', $winall) * $floatBet;
                                $winall_h1 = str_replace('.', '', $winall . '');
                                $winall_h = dechex($winall_h1);
                                $winall = $winall / 100;
                                if( $winall > 0 ) 
                                {
                                    $slotSettings->SetBalance($winall);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $winall);
                                }
                                $slotSettings->SetGameData('FruitPokerAMTotalWin', $winall);
                                $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                                $response_collect = strlen($winall_h) . $winall_h . $slotSettings->GetGameData('FruitPokerAMCollect0');
                                $slotSettings->SetGameData('FruitPokerAMCollect', $response_collect);
                                $response_log = '{"responseEvent":"gambleResult","serverResponse":{"totalWin":' . $winall . '}}';
                                if( $winall <= 0 ) 
                                {
                                    $winall = -1 * $dbet;
                                }
                                $slotSettings->SaveLogReport($response_log, $dbet, 1, $winall, 'slotGamble');
                                $response = '108010' . $slotSettings->GetGameData('FruitPokerAMbalanceFormated') . '' . strlen($winall_h) . $winall_h . '100' . dechex($slotSettings->GetGameData('FruitPokerAMBetCounter')) . '1f41388150' . $ucard . '01010101000000' . $slotSettings->GetGameData('FruitPokerAMGamble');
                                array_pop($cardsHistory);
                                array_unshift($cardsHistory, '0' . $ucard);
                                $response = '108010' . $slotSettings->GetGameData('FruitPokerAMbalanceFormated') . strlen($winall_h) . $winall_h . '10011f41388150' . $ucard . '05070507000000131616111610161610160203030001001100000b101013130c2c0110101010101010101010101010101010101010101010101010101010101010';
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
