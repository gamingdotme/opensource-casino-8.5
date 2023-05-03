<?php 
namespace VanguardLTE\Games\KenoPop1x2
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
                    $postData = $_REQUEST;
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                    $result_tmp = [];
                    $aid = '';
                    if( $postData['command'] == 'bet' ) 
                    {
                        $lines = 1;
                        $betline = $postData['stake'] / 100;
                        if( $lines <= 0 || $betline <= 0.0001 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetBalance() < ($lines * $betline) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['bet']['bonus'] == 'true' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bonus state"}';
                            exit( $response );
                        }
                    }
                    $aid = (string)$postData['command'];
                    switch( $aid ) 
                    {
                        case 'setup':
                            if( $slotSettings->Bet[0] < 0.1 ) 
                            {
                                $slotSettings->Bet[0] = 0.1;
                            }
                            $result_tmp[0] = "\r\n<xml>\r\n<gamevalues maxStake='" . array_pop($slotSettings->Bet) . '\' minStake=\'' . $slotSettings->Bet[0] . "' maxPayout='10000' percentage='5.5'/>\r\n\r\n<variables totalBalls='80' numBallsDrawn='20' maxBallsSelected='15' />\r\n<odds odds1='0,3.5,0,0,0,0,0,0,0,0,0,0,0,0,0,0'  odds2='0,1,9.5,0,0,0,0,0,0,0,0,0,0,0,0,0'  odds3='0,1,2,18,0,0,0,0,0,0,0,0,0,0,0,0'  odds4='0,0.5,2,6,18,0,0,0,0,0,0,0,0,0,0,0'  odds5='0,0.5,1,3,16,50,0,0,0,0,0,0,0,0,0,0'  odds6='0,0.5,1,2,4,24,70,0,0,0,0,0,0,0,0,0'  odds7='0,0.5,0.5,1,5,16,50,250,0,0,0,0,0,0,0,0'  odds8='0,0.5,0.5,1,3,7,20,80,750,0,0,0,0,0,0,0'  odds9='0,0.5,0.5,1,2,4,10,25,60,1250,0,0,0,0,0,0'  odds10='0,0,0.5,1,2,3,5,10,20,200,1600,0,0,0,0,0'  odds11='0,0,0.5,1,1,2.5,4,8,16,150,800,2500,0,0,0,0'  odds12='0,0,0,1,1,2,4,8,12,120,400,1500,4000,0,0,0'  odds13='0,0,0,0.5,1,2,4,6,10,60,150,500,3000,6000,0,0'  odds14='0,0,0,0.5,0.5,2,3,4,8,40,120,400,1000,2000,8000,0'  odds15='0,0,0,0.5,0.5,1.5,2,4,7,25,80,250,500,1000,2500,10000'   />\r\n\r\n</xml>\r\n";
                            break;
                        case 'subscribe':
                            $hist = [
                                78, 
                                30, 
                                46, 
                                62, 
                                46, 
                                30
                            ];
                            shuffle($hist);
                            $slotSettings->SetGameData('KenoPop1x2Cards', $hist);
                            $lastEvent = $slotSettings->GetHistory();
                            $slotSettings->SetGameData('KenoPop1x2BonusWin', 0);
                            $slotSettings->SetGameData('KenoPop1x2FreeGames', 0);
                            $slotSettings->SetGameData('KenoPop1x2CurrentFreeGame', 0);
                            $slotSettings->SetGameData('KenoPop1x2TotalWin', 0);
                            $slotSettings->SetGameData('KenoPop1x2FreeBalance', 0);
                            if( $lastEvent != 'NULL' ) 
                            {
                                if( isset($lastEvent->serverResponse->bonusWin) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->totalWin);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $lines = $lastEvent->serverResponse->slotLines;
                                $bet = $lastEvent->serverResponse->slotBet * $lines * 100;
                                $gtype = 1;
                            }
                            else
                            {
                                $gtype = 1;
                                $lines = 10;
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                            $result_tmp[0] = 'start=start&guestID=-400289&minStakeMultiplier=1&maxStakeMultiplier=1&maxLiabilityMultiplier=1&end=end&balance=' . $balanceInCents;
                            break;
                        case 'ping':
                            $result_tmp[] = '{"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.BaseResponse","command":"ping","eventTimestamp":' . time() . '}';
                            $result_tmp[] = '{"complex":{"levelI":' . ($slotSettings->slotJackpot[0] * 100) . ',"levelII":' . ($slotSettings->slotJackpot[1] * 100) . ',"levelIII":' . ($slotSettings->slotJackpot[2] * 100) . ',"levelIV":' . ($slotSettings->slotJackpot[3] * 100) . ',"winsLevelI":2,"largestWinLevelI":0,"largestWinDateLevelI":"","largestWinUserLevelI":"","lastWinLevelI":0,"lastWinDateLevelI":"","lastWinUserLevelI":"player","winsLevelII":0,"largestWinLevelII":0,"largestWinDateLevelII":"","largestWinUserLevelII":"","lastWinLevelII":0,"lastWinDateLevelII":"","lastWinUserLevelII":"player","winsLevelIII":0,"largestWinLevelIII":0,"largestWinDateLevelIII":"","largestWinUserLevelIII":"","lastWinLevelIII":0,"lastWinDateLevelIII":"","lastWinUserLevelIII":"","winsLevelIV":0,"largestWinLevelIV":0,"largestWinDateLevelIV":"","largestWinUserLevelIV":"","lastWinLevelIV":0,"lastWinDateLevelIV":"","lastWinUserLevelIV":""},"gameIdentificationNumber":1,"gameNumber":"","msg":"success","messageId":"f73a429df116252e537e403d12bcdb92","qName":"app.services.messages.response.GameEventResponse","command":"event","eventTimestamp":' . time() . '}';
                            break;
                        case 'bet':
                            $lines = 1;
                            $betline = $postData['stake'] / 100;
                            $allbet = $betline * $lines;
                            $postData['slotEvent'] = 'bet';
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            $balls = [];
                            for( $b = 0; $b < 80; $b++ ) 
                            {
                                $balls[] = $b + 1;
                            }
                            $ballSelected = explode(',', $postData['ballsSelected']);
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                shuffle($balls);
                                $matchNumbers = [];
                                $drawnNumbers = [];
                                $drawnNumbersStr = [];
                                for( $a = 0; $a < 20; $a++ ) 
                                {
                                    $drawnNumbers[] = $balls[$a];
                                    $drawnNumbersStr[] = '<b num=\'' . $balls[$a] . '\'/>';
                                }
                                for( $b = 0; $b < count($ballSelected); $b++ ) 
                                {
                                    $curBall = $ballSelected[$b];
                                    if( in_array($curBall, $drawnNumbers) ) 
                                    {
                                        $matchNumbers[] = $curBall;
                                    }
                                }
                                $curPays = $slotSettings->Paytable[count($ballSelected)];
                                $totalWin = $betline * $curPays[count($matchNumbers)];
                                if( $totalWin <= $bank ) 
                                {
                                    break;
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $slotSettings->SetGameData('KenoPop1x2TotalWin', $totalWin);
                            $slotSettings->SetGameData('KenoPop1x2GambleStep', 5);
                            $hist = $slotSettings->GetGameData('KenoPop1x2Cards');
                            $result_tmp[0] = '<xml><betPlaced  errorCode=\'0\'  error_description=\'\' errorIn=\'PLACE\'/><ballsDrawn>' . implode('', $drawnNumbersStr) . '</ballsDrawn><ballsMatched num=\'' . count($matchNumbers) . '\' balls=\'' . implode(',', $matchNumbers) . '\'/><echo   amount=\'' . ($totalWin * 100) . '\'/><newBalance  amount=\'' . ($totalWin * 100 - ($allbet * 100)) . '\' /><message show=\'false\' ></message></xml>';
                            break;
                    }
                    $response = $result_tmp[0];
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
