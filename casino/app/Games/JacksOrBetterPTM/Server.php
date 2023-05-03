<?php 
namespace VanguardLTE\Games\JacksOrBetterPTM
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
                        if( $umid == '40186' ) 
                        {
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
                            if( $slotSettings->GetGameData('JacksOrBetterPTMDAmount') < 1 ) 
                            {
                                $slotSettings->SetGameData('JacksOrBetterPTMTotalWin', $slotSettings->GetGameData('JacksOrBetterPTMTotalWin') / 2);
                            }
                            $cWin = $slotSettings->GetGameData('JacksOrBetterPTMTotalWin');
                            $aWin = $cWin * 2;
                            $slotSettings->SetBalance(-1 * $cWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $cWin);
                            shuffle($cardsArr);
                            $resultCards = [
                                $cardsArr[0], 
                                $cardsArr[1], 
                                $cardsArr[2], 
                                $cardsArr[3], 
                                $cardsArr[4]
                            ];
                            $isWin = rand(1, 2);
                            if( $isWin == 1 ) 
                            {
                                $pCard = rand($slotSettings->GetGameData('JacksOrBetterPTMDCard'), 12);
                            }
                            else
                            {
                                $pCard = rand(0, $slotSettings->GetGameData('JacksOrBetterPTMDCard'));
                            }
                            $resultCards = [
                                $cardsArr[0], 
                                $cardsArr[1], 
                                $cardsArr[2], 
                                $cardsArr[3], 
                                $cardsArr[4]
                            ];
                            $resultCards[(int)$postData['index'] - 1] = [
                                'value' => $pCard . '', 
                                'suit' => rand(0, 3) . ''
                            ];
                            if( $slotSettings->GetGameData('JacksOrBetterPTMDCard') == $pCard ) 
                            {
                                $totalWin = $cWin;
                            }
                            else if( $slotSettings->GetGameData('JacksOrBetterPTMDCard') < $pCard ) 
                            {
                                $totalWin = $aWin;
                            }
                            else
                            {
                                $totalWin = 0;
                            }
                            $slotSettings->SetGameData('JacksOrBetterPTMTotalWin', $totalWin);
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"amount":' . $slotSettings->GetGameData('JacksOrBetterPTMDAmount') . ',"cWin":' . $cWin . ',"pcard":' . $slotSettings->GetGameData('JacksOrBetterPTMDCard') . ',"totalWin":' . $totalWin . ',"data":{"cards":' . json_encode($resultCards) . ',"windowId":"VRqbhm"},"ID":40187,"umid":46}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $response = '{"totalWin":' . $totalWin . ',"data":{"cards":' . json_encode($resultCards) . ',"windowId":"VRqbhm"},"ID":40187,"umid":46}';
                            $slotSettings->SaveLogReport($response, $slotSettings->GetGameData('JacksOrBetterPTMBet'), 1, $totalWin, 'double');
                        }
                        if( $umid == '40182' ) 
                        {
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
                            $slotSettings->SetGameData('JacksOrBetterPTMDCard', (int)$cardsArr[0]->value);
                            $slotSettings->SetGameData('JacksOrBetterPTMDAmount', (double)$postData['amount']);
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"am":' . $slotSettings->GetGameData('JacksOrBetterPTMDAmount') . ',"card":{"suit":"' . $cardsArr[0]->suit . '","value":"' . $cardsArr[0]->value . '"},"windowId":"VRqbhm"},"ID":40183,"umid":46}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                        }
                        if( $umid == '40179' ) 
                        {
                            $resultCards = $slotSettings->GetGameData('JacksOrBetterPTMCurrentCards');
                            $cardsArr = $slotSettings->GetGameData('JacksOrBetterPTMCards');
                            $crdCount = 0;
                            $BankReserved = $slotSettings->GetGameData('JacksOrBetterPTMBankReserved');
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $BankReserved);
                            $slotSettings->SetGameData('JacksOrBetterPTMBankReserved', 0);
                            $totalWin = 0;
                            array_shift($cardsArr);
                            array_shift($cardsArr);
                            array_shift($cardsArr);
                            array_shift($cardsArr);
                            array_shift($cardsArr);
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $resultCards = $slotSettings->GetGameData('JacksOrBetterPTMCurrentCards');
                                $crdCount = 0;
                                shuffle($cardsArr);
                                for( $j = 0; $j < 5; $j++ ) 
                                {
                                    if( $postData['cardHolds'][$j] == 0 ) 
                                    {
                                        $resultCards[$j] = $cardsArr[$crdCount];
                                        $crdCount++;
                                    }
                                }
                                $payrate = $slotSettings->GetCombination([
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
                                $totalWin = $payrate * $slotSettings->GetGameData('JacksOrBetterPTMBet');
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
                            $slotSettings->SetGameData('JacksOrBetterPTMTotalWin', $totalWin);
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"totalWin":' . $totalWin . ',"data":{"cards":[{"cards":' . json_encode($resultCards) . '}],"windowId":"OxupG1"},"ID":40180,"umid":53}';
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"cardsArr":' . json_encode($cardsArr) . ',"state":"idle","slotLines":1,"slotBet":' . $slotSettings->GetGameData('JacksOrBetterPTMBet') . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"cards":' . json_encode($resultCards) . '}}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $slotSettings->SaveLogReport($response, 0, 1, $totalWin, 'bet');
                        }
                        if( $umid == '40175' ) 
                        {
                            $postData['bet'] = $postData['bet'] / 100;
                            $allbet = $postData['bet'];
                            if( $slotSettings->GetBalance() < $allbet ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $umid . '","serverResponse":"invalid balance"}';
                                echo $response;
                            }
                            if( $allbet < 0.0001 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $umid . '","serverResponse":"invalid bet"}';
                                echo $response;
                            }
                            if( !isset($postData['slotEvent']) ) 
                            {
                                $postData['slotEvent'] = 'bet';
                            }
                            $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                            $slotSettings->UpdateJackpots($postData['bet']);
                            $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                            $result_tmp = [];
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
                                $payrate = $slotSettings->GetCombination([
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
                                $totalWin = $payrate * $allbet;
                                if( $totalWin <= $bank ) 
                                {
                                    $slotSettings->SetGameData('JacksOrBetterPTMBankReserved', $totalWin);
                                    break;
                                }
                            }
                            $resultCards = [
                                $cardsArr[0], 
                                $cardsArr[1], 
                                $cardsArr[2], 
                                $cardsArr[3], 
                                $cardsArr[4]
                            ];
                            $slotSettings->SetGameData('JacksOrBetterPTMBet', $allbet);
                            $slotSettings->SetGameData('JacksOrBetterPTMCurrentCards', $resultCards);
                            $slotSettings->SetGameData('JacksOrBetterPTMCards', $cardsArr);
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"cards":' . json_encode($resultCards) . ',"windowId":"OxupG1"},"ID":40176,"umid":53}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"cardsArr":' . json_encode($cardsArr) . ',"state":"draw","slotLines":1,"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"cards":' . json_encode($resultCards) . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, 1, 0, 'bet');
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
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"po","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":19}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData('JacksOrBetterPTMBets', []);
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40030':
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40050':
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' && $lastEvent->serverResponse->state == 'draw' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Cards', $lastEvent->serverResponse->cardsArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentCards', $lastEvent->serverResponse->cards);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $result_tmp[] = '3:::{"data":{},"ID":40031,"umid":18}';
                                    $result_tmp[] = '3:::{"data":{"windowId":"ErbtIw"},"ID":48047,"umid":19}';
                                    $result_tmp[] = '3:::{"data":{},"ID":40031,"umid":18}';
                                    $result_tmp[] = '3:::{"data":{"type":"MAIN","bet":' . ($lastEvent->serverResponse->slotBet * 100) . ',"numCoins":1,"cards":' . json_encode($lastEvent->serverResponse->cards) . ',"windowId":"ztmjLs"},"ID":49289,"umid":28}';
                                }
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
