<?php 
namespace VanguardLTE\Games\BlackjackSurrenderPTM
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        if( isset($postData['umid']) ) 
                        {
                            $umid = $postData['umid'];
                            if( isset($postData['ID']) ) 
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
                            }
                            $umid = 0;
                        }
                        $dealerPay = false;
                        if( $umid == '40124' && $postData['action'] == '3' ) 
                        {
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBox');
                            $state = -1;
                            $playerBoxes[$currentBox]['state'] = 'stand';
                            $result_tmp[] = '3:::{"data":{"action":3,"cardsInfo":[],"index":' . $state . ',"windowId":"qOpl9d"},"ID":40122,"umid":290}';
                            for( $i = $currentBox - 1; $i >= 0; $i-- ) 
                            {
                                $currentBox = $i;
                                if( $playerBoxes[$i]['mainBet'] > 0 && $playerBoxes[$i]['state'] != 'blackjack' && $playerBoxes[$i]['state'] != 'stand' && $playerBoxes[$i]['state'] != 'bust' ) 
                                {
                                    break;
                                }
                                if( $i == 0 && $state == -1 ) 
                                {
                                    $dealerPay = true;
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $acts = $slotSettings->GetGameData($slotSettings->slotId . 'actions');
                            $acts[] = $postData['action'];
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', $acts);
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"0","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport($response, 0, 1, 0, 'bet');
                        }
                        if( $umid == '40124' && ($postData['action'] == '8' || $postData['action'] == '10') ) 
                        {
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBoxInsurance');
                            $state = 30;
                            if( $postData['action'] == '8' ) 
                            {
                                $dbet = $playerBoxes[$currentBox]['mainBet'] / 2;
                                $allbet -= $playerBoxes[$currentBox]['mainBet'];
                                $playerBoxes[$currentBox]['insuranceBet'] = $dbet;
                                if( $slotSettings->GetBalance() < $dbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                $bankSum = $dbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->SetBalance(-1 * $dbet);
                                $slotSettings->UpdateJackpots($dbet);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $response = '{"totalWin":0,"data":{"cards":[],"windowId":"OxupG1"},"ID":40180,"umid":53}';
                                $slotSettings->SaveLogReport($response, $dbet, 1, 0, 'insurance');
                            }
                            $result_tmp[] = '3:::{"data":{"action":' . $postData['action'] . ',"cardsInfo":[],"index":30,"windowId":"amOGGK"},"ID":40122,"umid":231}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            for( $i = $currentBox - 1; $i >= 0; $i-- ) 
                            {
                                $currentBox = $i;
                                if( $playerBoxes[$i]['mainBet'] > 0 ) 
                                {
                                    break;
                                }
                                if( $i == 0 && $state == -1 ) 
                                {
                                    $dealerPay = true;
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBoxInsurance', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                        }
                        if( $umid == '40124' && $postData['action'] == '9' ) 
                        {
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBox');
                            $state = 30;
                            $dbet = $playerBoxes[$currentBox]['mainBet'] / 2;
                            $allbet -= $playerBoxes[$currentBox]['mainBet'];
                            $playerBoxes[$currentBox]['surBet'] = $playerBoxes[$currentBox]['mainBet'];
                            $playerBoxes[$currentBox]['mainBet'] = 0;
                            $playerBoxes[$currentBox]['state'] = 'surrender';
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"action":9,"cardsInfo":[],"index":30,"windowId":"amOGGK"},"ID":40122,"umid":41}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $response = '{"totalWin":0,"data":{"cards":[],"windowId":"OxupG1"},"ID":40180,"umid":53}';
                            for( $i = $currentBox - 1; $i >= 0; $i-- ) 
                            {
                                $currentBox = $i;
                                if( $playerBoxes[$i]['mainBet'] > 0 && $playerBoxes[$i]['state'] != 'blackjack' && $playerBoxes[$i]['state'] != 'stand' && $playerBoxes[$i]['state'] != 'bust' ) 
                                {
                                    break;
                                }
                                if( $i == 0 ) 
                                {
                                    $dealerPay = true;
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $acts = $slotSettings->GetGameData($slotSettings->slotId . 'actions');
                            $acts[] = $postData['action'];
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', $acts);
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"0","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport($response, 0, 1, 0, 'bet');
                        }
                        if( $umid == '40124' && ($postData['action'] == '2' || $postData['action'] == '4') ) 
                        {
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBox');
                            if( !is_array($cardsArr) ) 
                            {
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' && $lastEvent->serverResponse->state != -1 ) 
                                {
                                    for( $i = 10; $i >= 0; $i-- ) 
                                    {
                                        $lastEvent->serverResponse->Boxes[$i] = (array)$lastEvent->serverResponse->Boxes[$i];
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'cardsArr', $lastEvent->serverResponse->cardsArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $lastEvent->serverResponse->Boxes);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'actions', $lastEvent->serverResponse->actions);
                                }
                            }
                            $state = 30;
                            $dbet = 0;
                            if( $postData['action'] == '4' && $playerBoxes[$currentBox]['mainBet'] < $slotSettings->GetBalance() ) 
                            {
                                $dbet = $playerBoxes[$currentBox]['mainBet'];
                                $allbet += $dbet;
                                $playerBoxes[$currentBox]['mainBet'] = $dbet * 2;
                                if( $slotSettings->GetBalance() < $dbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                $bankSum = $dbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->UpdateJackpots($dbet);
                                $slotSettings->SetBalance(-1 * $dbet);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            }
                            $cCard0 = array_shift($cardsArr);
                            $playerBoxes[$currentBox]['cards'][] = $cCard0;
                            $slotSettings->GetScore($playerBoxes[$currentBox]);
                            $tt = $playerBoxes[$currentBox];
                            $result_tmp[] = '3:::{"data":{"currentBox":' . $currentBox . ',"state":"' . $playerBoxes[$currentBox]['state'] . '","score":' . $playerBoxes[$currentBox]['mainScore'] . ',"action":2,"cardsInfo2":' . json_encode($tt['cards']) . ',"cardsInfo":[' . json_encode($cCard0) . '],"index":' . $state . ',"windowId":"qOpl9d"},"ID":40122,"umid":290}';
                            if( $playerBoxes[$currentBox]['mainScore'] == 21 ) 
                            {
                                $playerBoxes[$currentBox]['state'] = 'stand';
                            }
                            if( $playerBoxes[$currentBox]['state'] == 'bust' || $playerBoxes[$currentBox]['mainScore'] == 21 || $postData['action'] == '4' ) 
                            {
                                $state = -1;
                                for( $i = $currentBox - 1; $i >= 0; $i-- ) 
                                {
                                    $currentBox = $i;
                                    if( $playerBoxes[$i]['mainBet'] > 0 && $playerBoxes[$i]['state'] != 'blackjack' && $playerBoxes[$i]['state'] != 'stand' && $playerBoxes[$i]['state'] != 'bust' ) 
                                    {
                                        break;
                                    }
                                    if( $i == 0 && $state == -1 ) 
                                    {
                                        $dealerPay = true;
                                    }
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $acts = $slotSettings->GetGameData($slotSettings->slotId . 'actions');
                            $acts[] = $postData['action'];
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', $acts);
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"0","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport($response, $dbet, 1, 0, 'bet');
                        }
                        if( $umid == '40124' && $postData['action'] == '5' ) 
                        {
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBox');
                            if( $playerBoxes[$currentBox]['mainBet'] < $slotSettings->GetBalance() ) 
                            {
                                $dbet = $playerBoxes[$currentBox]['mainBet'];
                                $allbet += $dbet;
                                if( $slotSettings->GetBalance() < $dbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                $bankSum = $dbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->UpdateJackpots($dbet);
                                $slotSettings->SetBalance(-1 * $dbet);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                                $state = 30;
                                $currentBoxNew = $currentBox + 5;
                                $cCard0 = array_shift($cardsArr);
                                $cCard1 = array_shift($cardsArr);
                                if( !isset($playerBoxes[$currentBox]['cards'][1]) ) 
                                {
                                    $slotSettings->InternalError($slotSettings->PackGameData());
                                }
                                $playerBoxes[$currentBoxNew]['cards'][0] = $playerBoxes[$currentBox]['cards'][1];
                                $playerBoxes[$currentBoxNew]['cards'][1] = $cCard1;
                                $playerBoxes[$currentBoxNew]['mainBet'] = $playerBoxes[$currentBox]['mainBet'];
                                $playerBoxes[$currentBox]['cards'][1] = $cCard0;
                                $slotSettings->GetScore($playerBoxes[$currentBox]);
                                $slotSettings->GetScore($playerBoxes[$currentBoxNew]);
                                $tt = $playerBoxes[$currentBox];
                                if( $playerBoxes[$currentBoxNew]['mainScore'] != 21 || $playerBoxes[$currentBoxNew]['mainScore'] != 20 ) 
                                {
                                    $playerBoxes[$currentBoxNew]['state'] = 'stand';
                                    $currentBox = $currentBoxNew;
                                }
                                $result_tmp[] = '3:::{"data":{"currentBox":' . $currentBox . ',"state":"' . $playerBoxes[$currentBox]['state'] . '","score":' . $playerBoxes[$currentBox]['mainScore'] . ',"action":5,"cardsInfo2":' . json_encode($tt['cards']) . ',"cardsInfo":[' . json_encode($cCard0) . ',' . json_encode($cCard1) . '],"index":' . $state . ',"windowId":"qOpl9d"},"ID":40122,"umid":290}';
                            }
                            $acts = $slotSettings->GetGameData($slotSettings->slotId . 'actions');
                            $acts[] = $postData['action'];
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', $acts);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"0","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport($response, $dbet, 1, 0, 'bet');
                        }
                        if( $umid == '49260' ) 
                        {
                            $allbet = 0;
                            $playerBoxes = [];
                            $playerBoxes[0] = $slotSettings->CreateBox();
                            $dealerBetPair = $postData['dealerpairBet'] / 100;
                            $allbet += $dealerBetPair;
                            $sideBets = [
                                0, 
                                0, 
                                0, 
                                0, 
                                0, 
                                0, 
                                0
                            ];
                            $plusThree = [
                                0, 
                                0, 
                                0, 
                                0, 
                                0, 
                                0, 
                                0
                            ];
                            $boxesCnt = 0;
                            for( $i = 1; $i <= 5; $i++ ) 
                            {
                                $playerBoxes[$i] = $slotSettings->CreateBox();
                                if( $postData['bets'][$i - 1] > 0 ) 
                                {
                                    $bbet = $postData['bets'][$i - 1] / 100;
                                    $playerBoxes[$i]['mainBet'] = $bbet;
                                    $allbet += $bbet;
                                    $boxesCnt++;
                                }
                                if( isset($postData['sideBets'][$i - 1]['plusThree']) ) 
                                {
                                    $tbet = $postData['sideBets'][$i - 1]['plusThree'] / 100;
                                    $plusThree[$i - 1] = $tbet;
                                    $allbet += $tbet;
                                }
                                if( $postData['sideBets'][$i - 1]['sideBet'] > 0 ) 
                                {
                                    $tbet = $postData['sideBets'][$i - 1]['sideBet'] / 100;
                                    $sideBets[$i - 1] = $tbet;
                                    $allbet += $tbet;
                                }
                            }
                            for( $i = 6; $i <= 10; $i++ ) 
                            {
                                $playerBoxes[$i] = $slotSettings->CreateBox();
                            }
                            if( $slotSettings->GetBalance() < $allbet ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                            $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                            $slotSettings->UpdateJackpots($allbet);
                            $slotSettings->SetBalance(-1 * $allbet);
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $c = 0; $c <= 2000; $c++ ) 
                            {
                                $totalWin = 0;
                                $cardsArr = [];
                                for( $i = 0; $i <= 3; $i++ ) 
                                {
                                    for( $j = 0; $j <= 12; $j++ ) 
                                    {
                                        $cardsArr[] = (object)[
                                            'suit' => '' . $i, 
                                            'value' => '' . $j
                                        ];
                                    }
                                }
                                shuffle($cardsArr);
                                $playerResult = [];
                                $dealerResult = [];
                                $currentBox = 0;
                                $currentBoxInsurance = 0;
                                $dCard = array_shift($cardsArr);
                                $playerBoxes[0]['cards'] = [$dCard];
                                $dealerResult[] = $dCard;
                                $cWin3 = 0;
                                for( $i = 1; $i <= 5; $i++ ) 
                                {
                                    if( $playerBoxes[$i]['mainBet'] > 0 ) 
                                    {
                                        $cCard0 = array_shift($cardsArr);
                                        $cCard1 = array_shift($cardsArr);
                                        $playerResult[] = $cCard0;
                                        $playerResult[] = $cCard1;
                                        $playerBoxes[$i]['cards'] = [
                                            $cCard0, 
                                            $cCard1
                                        ];
                                        $slotSettings->GetScore($playerBoxes[$i]);
                                        $currentBox = $i;
                                        $currentBoxInsurance = $i;
                                        $cWin0 = $slotSettings->GetThreeWin([
                                            $cCard0, 
                                            $cCard1, 
                                            $dealerResult[0]
                                        ]) * $plusThree[$i - 1];
                                        $cWin1 = $slotSettings->GetSideWin([
                                            $cCard0, 
                                            $cCard1
                                        ]) * $sideBets[$i - 1];
                                        $totalWin += $cWin0;
                                        $totalWin += $cWin1;
                                        if( $playerBoxes[$i]['state'] == 'blackjack' ) 
                                        {
                                            $cWin3 += ($playerBoxes[$i]['mainBet'] * 2.5);
                                        }
                                    }
                                }
                                if( $boxesCnt == 1 && $cWin3 > 0 ) 
                                {
                                }
                                else if( $totalWin + $cWin3 <= $bank ) 
                                {
                                    if( $cWin3 > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', $cWin3);
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $cWin3);
                                    }
                                    break;
                                }
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBoxInsurance', $currentBoxInsurance);
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $allbet);
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', []);
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            $result_tmp[] = '3:::{"data":{"windowId":"qOpl9d"},"ID":40155,"umid":32}';
                            $result_tmp[] = '3:::{"data":{"windowId":"qOpl9d"},"ID":40156,"umid":32}';
                            $result_tmp[] = '3:::{"data":{"ww":' . $totalWin . ',"credit":' . $balanceInCents . ',"playerCards":' . json_encode($playerResult) . ',"dealerCard":' . json_encode($dealerResult[0]) . ',"windowId":"mip3DY"},"ID":40118,"umid":34}';
                            $result_tmp[] = '3:::{"data":{"index":30,"windowId":"mip3DY"},"ID":40120,"umid":35}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"0","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport($response, $allbet, 1, 0, 'bet');
                        }
                        if( $dealerPay ) 
                        {
                            $BankReserved = $slotSettings->GetGameData($slotSettings->slotId . 'BankReserved');
                            if( $BankReserved > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $BankReserved);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BankReserved', 0);
                            }
                            $cardsArr = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $playerBoxes = $slotSettings->GetGameData($slotSettings->slotId . 'Boxes');
                            $currentBox = $slotSettings->GetGameData($slotSettings->slotId . 'currentBox');
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            $isAllBust = true;
                            for( $i = 10; $i >= 1; $i-- ) 
                            {
                                if( $playerBoxes[$i]['state'] != 'bust' && $playerBoxes[$i]['mainBet'] > 0 ) 
                                {
                                    $isAllBust = false;
                                }
                            }
                            for( $c = 0; $c <= 2000; $c++ ) 
                            {
                                $totalWin = 0;
                                $dealerTmp = $playerBoxes[0];
                                $dealerTmp0 = $playerBoxes[0];
                                $dealerCard = [];
                                $scoresTmp = [];
                                shuffle($cardsArr);
                                $cr = 0;
                                for( $i = 0; $i <= 15; $i++ ) 
                                {
                                    $dealerTmp['cards'][] = $cardsArr[$cr];
                                    $dealerTmp0['cards'][] = $cardsArr[$cr];
                                    $dealerTmp0['cards'][] = $cardsArr[$cr + 1];
                                    $dealerCard[] = $cardsArr[$cr];
                                    $cr++;
                                    $slotSettings->GetScore($dealerTmp);
                                    $slotSettings->GetScore($dealerTmp0);
                                    $scoresTmp[] = $dealerTmp['mainScore'];
                                    if( $dealerTmp['mainScore'] >= 17 || $dealerTmp0['mainScore'] > 21 && $dealerTmp['mainScore'] != 16 || $isAllBust && $dealerTmp['mainScore'] != 16 ) 
                                    {
                                        break;
                                    }
                                }
                                $vWins = [
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
                                for( $i = 10; $i >= 1; $i-- ) 
                                {
                                    $cWin = 0;
                                    if( $playerBoxes[$i]['state'] == 'surrender' ) 
                                    {
                                        $cWin = $playerBoxes[$i]['surBet'] / 2;
                                    }
                                    if( $playerBoxes[$i]['mainBet'] > 0 && $playerBoxes[$i]['state'] != 'bust' ) 
                                    {
                                        if( $dealerTmp['mainScore'] > 21 ) 
                                        {
                                            $cWin = $playerBoxes[$i]['mainBet'] * 2;
                                        }
                                        if( $dealerTmp['mainScore'] < $playerBoxes[$i]['mainScore'] ) 
                                        {
                                            $cWin = $playerBoxes[$i]['mainBet'] * 2;
                                        }
                                        if( $dealerTmp['mainScore'] == $playerBoxes[$i]['mainScore'] ) 
                                        {
                                            $cWin = $playerBoxes[$i]['mainBet'] * 1;
                                        }
                                        if( $playerBoxes[$i]['state'] == 'blackjack' ) 
                                        {
                                            $cWin = $playerBoxes[$i]['mainBet'] * 2.5;
                                        }
                                    }
                                    $vWins[$i] = $cWin;
                                    $totalWin += $cWin;
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
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"dsc":[' . implode(',', $vWins) . '],"ds":' . $dealerTmp['mainScore'] . ',"ww":' . $totalWin . ',"debug":' . json_encode($playerBoxes) . ',"dealerCards":' . json_encode($dealerCard) . ',"windowId":"mip3DY"},"ID":40125,"umid":43}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $slotSettings->SetGameData($slotSettings->slotId . 'currentBox', $currentBox);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $cardsArr);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $playerBoxes);
                            $acts = $slotSettings->GetGameData($slotSettings->slotId . 'actions');
                            $acts[] = $postData['action'];
                            $slotSettings->SetGameData($slotSettings->slotId . 'actions', $acts);
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"actions":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'actions')) . ',"Boxes":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Boxes')) . ',"cardsArr":' . json_encode($cardsArr) . ',"state":"-1","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":0,"winLines":[]}}';
                            $slotSettings->SaveLogReport0($response, 0, 1, $totalWin, 'bet');
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
                            case '40024':
                                $gameBets = $slotSettings->Bet;
                                $minBet = $gameBets[0] * 100;
                                $maxBet = $gameBets[count($gameBets) - 1] * 100;
                                if( $minBet < 10 ) 
                                {
                                    $minBet = 10;
                                }
                                if( $maxBet < 10 ) 
                                {
                                    $maxBet = 10;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"bjsd2","minBet":' . $minBet . ',"maxBet":' . $maxBet . ',"minPosBet":0,"maxPosBet":0,"coinSizes":[1,500,1,500,1,500],"sidebetLimits":{"playerMinBet":' . $minBet . ',"playerMaxBet": ' . $maxBet . ',"dealerMinBet":' . $minBet . ',"dealerMaxBet": ' . $maxBet . ',"twentyOnePlusThreeMinBet":' . $minBet . ',"twentyOnePlusThreeMaxBet": ' . $maxBet . '},"altLimits":[]},"ID":40025,"umid":20}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData('BlackjackSurrenderPTMBets', []);
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' && $lastEvent->serverResponse->state != -1 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'bjsd2');
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40030':
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' && $lastEvent->serverResponse->state != -1 ) 
                                {
                                    for( $i = 10; $i >= 0; $i-- ) 
                                    {
                                        $lastEvent->serverResponse->Boxes[$i] = (array)$lastEvent->serverResponse->Boxes[$i];
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'cardsArr', $lastEvent->serverResponse->cardsArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Boxes', $lastEvent->serverResponse->Boxes);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'actions', $lastEvent->serverResponse->actions);
                                    $result_tmp[] = '3:::{"data":{},"ID":40031,"umid":18}';
                                    $result_tmp[] = '3:::{"data":{"windowId":"ErbtIw"},"ID":48047,"umid":19}';
                                    $result_tmp[] = '3:::{"data":{"credit":3378,"windowId":"yLn5hn"},"ID":40026,"umid":29}';
                                    $playerCards = [];
                                    $Boxes = $lastEvent->serverResponse->Boxes;
                                    $bets = [];
                                    for( $j = 5; $j >= 1; $j-- ) 
                                    {
                                        $bets[] = $Boxes[$j]['mainBet'] * 100;
                                    }
                                    for( $j = 10; $j >= 6; $j-- ) 
                                    {
                                        $bets[] = $Boxes[$j]['mainBet'] * 100;
                                    }
                                    for( $j = 10; $j >= 1; $j-- ) 
                                    {
                                        for( $i = 10; $i >= 1; $i-- ) 
                                        {
                                            if( count($Boxes[$i]['cards']) > 0 ) 
                                            {
                                                $playerCards[] = array_shift($Boxes[$i]['cards']);
                                            }
                                        }
                                    }
                                    $result_tmp[] = '3:::{"data":{"totalBet":' . ($lastEvent->serverResponse->slotBet * 100) . ',"bets":' . json_encode($bets) . ',"dealerBet":0,"sideBets":[{"sideBet":0,"plusThree":0},{"sideBet":0,"plusThree":0},{"sideBet":0,"plusThree":0},{"sideBet":0,"plusThree":0},{"sideBet":0,"plusThree":0}],"actions":' . json_encode($lastEvent->serverResponse->actions) . ',"playerCards":' . json_encode($playerCards) . ',"dealerCards":' . json_encode($lastEvent->serverResponse->Boxes[0]['cards']) . ',"windowId":"3YG1v6"},"ID":49261,"umid":28}';
                                }
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                $result_tmp[] = '3:::{"data":{"limitType":[1,2,3,4],"limitMin":[100,100,100,100],"limitMax":[1000,10000,8000,50000],"windowId":"MVRRkz"},"ID":40008,"umid":29}';
                                break;
                            case '40050':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                $result_tmp[] = '3:::{"data":{"limitType":[1,2,3,4],"limitMin":[100,100,100,100],"limitMax":[1000,10000,8000,50000],"windowId":"MVRRkz"},"ID":40008,"umid":29}';
                                break;
                            case '48300':
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":30}';
                                $result_tmp[] = '3:::{"data":{"waitingLogins":[],"waitingAlerts":[],"waitingDialogs":[],"waitingDialogMessages":[],"waitingToasterMessages":[]},"ID":48301,"umid":31}';
                                break;
                        }
                        $response = implode('------', $result_tmp);
                        $slotSettings->SaveGameData();
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
