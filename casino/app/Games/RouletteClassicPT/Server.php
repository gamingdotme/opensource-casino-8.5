<?php 
namespace VanguardLTE\Games\RouletteClassicPT
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
                        if( $umid == '40063' ) 
                        {
                            $result_tmp = [];
                            $paysArr = [];
                            $paysArr['straight'] = 36;
                            $paysArr['split'] = 18;
                            $paysArr['street'] = 12;
                            $paysArr['four'] = 9;
                            $paysArr['corner'] = 9;
                            $paysArr['line'] = 6;
                            $paysArr['column'] = 3;
                            $paysArr['twelve'] = 3;
                            $paysArr['low'] = 2;
                            $paysArr['high'] = 2;
                            $paysArr['red'] = 2;
                            $paysArr['black'] = 2;
                            $paysArr['odd'] = 2;
                            $paysArr['even'] = 2;
                            $bsArr = [];
                            $bsArr['zero'] = 0;
                            $bsArr['four'] = 0;
                            $bsArr['straight'] = 0;
                            $bsArr['split'] = 0;
                            $bsArr['street'] = 0;
                            $bsArr['corner'] = 0;
                            $bsArr['line'] = 0;
                            $bsArr['column'] = 0;
                            $bsArr['twelve'] = 0;
                            $bsArr['low'] = 0;
                            $bsArr['high'] = 0;
                            $bsArr['red'] = 0;
                            $bsArr['black'] = 0;
                            $bsArr['odd'] = 0;
                            $bsArr['even'] = 0;
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $randNumber = rand(0, 36);
                                $wins = [];
                                $totalWin = 0;
                                $allbet = 0;
                                foreach( $postData['bets'] as $key => $vl ) 
                                {
                                    $allbet += ($vl[1] / 100);
                                    $curNums = $slotSettings->GetNumbersByField($vl[0]);
                                    if( $curNums[0] == 'straight' && $curNums[1][0] == '0' ) 
                                    {
                                        $bsArr['zero'] = 1;
                                    }
                                    else
                                    {
                                        $bsArr[$curNums[0]]++;
                                    }
                                    if( in_array($randNumber, $curNums[1]) ) 
                                    {
                                        $curWin = $paysArr[$curNums[0]] * ($vl[1] / 100);
                                        $totalWin += $curWin;
                                    }
                                }
                                if( $totalWin <= $bank ) 
                                {
                                    break;
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                            }
                            $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                            if( $bsArr['zero'] > 0 && ($bsArr['red'] > 0 && $bsArr['black'] > 0 || $bsArr['odd'] > 0 && $bsArr['even'] > 0 || $bsArr['high'] > 0 && $bsArr['low'] > 0 || $bsArr['twelve'] >= 3 || $bsArr['column'] >= 3 || $bsArr['straight'] >= 36) ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $allbet);
                                $slotSettings->SetBalance(-1 * $allbet, 'bet');
                            }
                            else
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->SetBalance(-1 * $allbet, 'bet');
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{},"ID":40173,"umid":35}';
                            foreach( $postData['bets'] as $key => $vl ) 
                            {
                                $result_tmp[] = '3:::{"data":{"windowId":"m4Un7g"},"ID":40129,"umid":36}';
                            }
                            $result_tmp[] = '3:::{"data":{"result":' . $randNumber . ',"credit":' . $balanceInCents . ',"windowId":"aelrii"},"ID":40111,"umid":36}';
                            $response = '3:::{"data":{"result":' . $randNumber . ',"credit":' . $balanceInCents . ',"windowId":"aelrii"},"ID":40111,"umid":36}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
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
                                $gameBets0 = [];
                                foreach( $gameBets as $vl ) 
                                {
                                    $bb = explode('=', $vl);
                                    $gameBets0[$bb[0]] = $bb[1] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"ro","minPosBet":0,"maxPosBet":0,"rouletteLimits":[{"tableLimitsName":"ro","infoAlternative":{"gameGroup":"ro","minBet":' . $gameBets0['minBet'] . ',"maxBet":' . $gameBets0['maxBet'] . ',"minPosBet":0,"maxPosBet":0},"limits":{"STRAIGHT_LIMIT":{"minBet":' . $gameBets0['minBetStraight'] . ',"maxBet":' . $gameBets0['maxBetStraight'] . '},"COLUMN_AND_DOZEN_LIMIT":{"minBet":' . $gameBets0['minBetColumnAndDozen'] . ',"maxBet":' . $gameBets0['maxBetColumnAndDozen'] . '},"FIFTY_FIFTY_LIMIT":{"minBet":' . $gameBets0['minBetFiftyFifty'] . ',"maxBet":' . $gameBets0['maxBetFiftyFifty'] . '},"TABLE_LIMIT":{"minBet":' . $gameBets0['minBetTable'] . ',"maxBet":' . $gameBets0['maxBetTable'] . '}}}]},"ID":40025,"umid":19}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData('RouletteClassicPTBets', []);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40050':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
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
