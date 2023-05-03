<?php 
namespace VanguardLTE\Games\OceanKing2MN
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
                        if( isset($postData['command']) && $postData['command'] == 'CheckAuth' ) 
                        {
                            $response = '{"responseEvent":"CheckAuth","startTimeSystem":' . (time() * 1000) . ',"userId":' . $userId . ',"shop_id":' . $slotSettings->shop_id . ',"username":"' . $slotSettings->username . '"}';
                            exit( $response );
                        }
                        $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$postData['action'];
                        switch( $aid ) 
                        {
                            case 'Init1':
                            case 'Init2':
                            case 'Act61':
                            case 'Ping':
                            case 'Act58':
                            case 'getBalance':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . '}';
                                break;
                            case 'Act41':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                $result_tmp[0] = '{"action":"' . $aid . '","nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . '}';
                                break;
                            case 'Act18':
                                $fishPays = [];
                                $fishPays[1] = 2;
                                $fishPays[2] = 0;
                                $fishPays[3] = 3;
                                $fishPays[4] = 4;
                                $fishPays[5] = 5;
                                $fishPays[6] = 6;
                                $fishPays[7] = 7;
                                $fishPays[8] = 8;
                                $fishPays[9] = 9;
                                $fishPays[10] = 10;
                                $fishPays[11] = 12;
                                $fishPays[12] = 15;
                                $fishPays[13] = 18;
                                $fishPays[14] = 20;
                                $fishPays[15] = 22;
                                $fishPays[16] = 30;
                                $fishPays[17] = 30;
                                $fishPays[18] = 30;
                                $fishPays[19] = 40;
                                $fishPays[20] = 50;
                                $fishPays[21] = 50;
                                $fishPays[22] = 50;
                                $fishPays[23] = 100;
                                $fishPays[24] = 100;
                                $fishPays[25] = 150;
                                if( isset($postData['reqDat']) ) 
                                {
                                    $aid = 'Act19';
                                    $hits = $postData['reqDat']['hits'];
                                    $lose = false;
                                    for( $i = 0; $i < 2000; $i++ ) 
                                    {
                                        $allbet = 0;
                                        $totalWin = 0;
                                        $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        foreach( $hits as $key => $hit ) 
                                        {
                                            $fishType = $hit['fishType'];
                                            $bet = $hit['bet'];
                                            $cwin = 0;
                                            if( !isset($fishPays[$fishType]) ) 
                                            {
                                                $cwin = $postData['reqDat']['hits'][$key]['win'];
                                            }
                                            else
                                            {
                                                $cwin = $fishPays[$fishType] * $bet;
                                            }
                                            if( $cwin != $postData['reqDat']['hits'][$key] ) 
                                            {
                                                $cwin = $postData['reqDat']['hits'][$key]['win'];
                                            }
                                            if( $lose ) 
                                            {
                                                $postData['reqDat']['hits'][$key]['win'] = 0;
                                                $cwin = 0;
                                            }
                                            $totalWin += $cwin;
                                            $allbet += $bet;
                                        }
                                        if( $totalWin <= $bank ) 
                                        {
                                            break;
                                        }
                                        if( $i > 100 ) 
                                        {
                                            $lose = true;
                                        }
                                    }
                                    if( $allbet < 0.0001 || $slotSettings->GetBalance() < $allbet ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"bet","serverResponse":"invalid bet state"}';
                                        exit( $response );
                                    }
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                    }
                                    $jsSet = '{"dealerCard":"","gambleState":"","totalWin":' . $totalWin . ',"afterBalance":' . $balanceInCents . ',"Balance":' . $balanceInCents . '}';
                                    $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                    $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                }
                                $balanceInCents = floor(sprintf('%01.2f', $slotSettings->GetBalance()));
                                $result_tmp[0] = '{"action":"' . $aid . '","hits":' . json_encode($postData) . ',"nickName":"' . $slotSettings->username . '","currency":"' . $slotSettings->slotCurrency . '","Credit":' . $balanceInCents . '}';
                                break;
                        }
                        $response = $result_tmp[0];
                        $slotSettings->SaveGameData();
                        echo ':::' . $response;
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
