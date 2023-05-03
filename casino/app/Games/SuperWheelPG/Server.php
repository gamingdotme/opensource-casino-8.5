<?php 
namespace VanguardLTE\Games\SuperWheelPG
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
                        $postData = $_POST;
                        $getData = $_GET;
                        $tempData = explode("\n", file_get_contents('php://input'));
                        $response = '';
                        if( trim($tempData[0]) == 'd=1' ) 
                        {
                            echo "d=103 \"WWgb5PqdGvk\"\r\n\r\n";
                        }
                        if( trim($tempData[0]) == 'd=2' ) 
                        {
                            echo "d=103 \"WWgb5PqdGvk!9108\"\r\n101 9108 \"DEMO\" \"\" \"\" \"user9108\" \"\"\r\n127 \"2020-08-23T14:43:01Z\"\r\n";
                        }
                        $result_tmp = [];
                        $aid = '';
                        if( isset($tempData[2]) && trim($tempData[2]) == '0' ) 
                        {
                            echo 'd=';
                        }
                        else if( isset($tempData[2]) ) 
                        {
                            $gameData = explode(' ', $tempData[2]);
                            if( trim($gameData[0]) == '104' ) 
                            {
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $bets = $slotSettings->Bet;
                                foreach( $bets as &$b ) 
                                {
                                    $b = $b * 100;
                                }
                                $allNums = [];
                                for( $i = 1; $i <= 99; $i++ ) 
                                {
                                    $allNums[] = $i;
                                }
                                shuffle($allNums);
                                $cc = [
                                    [], 
                                    [], 
                                    [], 
                                    []
                                ];
                                for( $i = 0; $i < 4; $i++ ) 
                                {
                                    for( $j = $i * 15; $j < ($i * 15 + 15); $j++ ) 
                                    {
                                        $cc[$i][] = $allNums[$j];
                                    }
                                }
                                $slotCards = [
                                    'card1' => $cc[0], 
                                    'card2' => $cc[1], 
                                    'card3' => $cc[2], 
                                    'card4' => $cc[3]
                                ];
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotCards', $slotCards);
                                $resultNums = [];
                                for( $j = 0; $j < 15; $j++ ) 
                                {
                                    for( $i = 0; $i < 4; $i++ ) 
                                    {
                                        $resultNums[] = $cc[$i][$j];
                                    }
                                }
                                $response = "d=104 1\r\n53 4 " . $bets[0] . ' 10000 ' . $bets[0] . " 50000\r\n54 " . count($bets) . ' ' . implode(' ', $bets) . ' ' . $bets[0] . "\r\n57 \"<custom><RTP Value=\"0\" /></custom>\"\r\n52 " . $balanceInCents . " 0 0\r\n91 23526\r\n109\r\n\r\n";
                            }
                            if( trim($gameData[0]) == '1' ) 
                            {
                                $aid = 'spin';
                                $postData['slotEvent'] = 'bet';
                            }
                            if( trim($gameData[0]) == '10' ) 
                            {
                                $aid = 'resetcards';
                            }
                            if( trim($gameData[0]) == '4' ) 
                            {
                                $aid = 'collect';
                            }
                            if( trim($gameData[0]) == '2' && trim($gameData[1]) == '6' ) 
                            {
                                $aid = 'endround';
                            }
                            if( trim($gameData[0]) == '102' ) 
                            {
                                $aid = 'exit';
                            }
                            if( trim($gameData[0]) == '7' ) 
                            {
                                $aid = 'freestep';
                            }
                        }
                        if( isset($getData['command']) && $getData['command'] == 'Configuration_v2' ) 
                        {
                            $rtStr = '{"hasMysteryJackpot":false,"hasGuaranteedJackpot":false,"jackpots":null,"disableSwipeToFullscreenPortraitIos":true,"disableSwipeToFullscreenLandscapeIos":false,"disableSwipeToFullscreenIos":false,"defaultHyperSpin":false,"disableHyperSpin":true,"disableVideoActivationScreen":false,"alwaysShowDecimals":false,"useExternalBalanceOnly":false,"disableScrollToFullscreenMessage":false,"bundleMode":0,"disableInGameModals":false,"disableFastplay":false,"unsupportedDeviceMessage":"This game is currently not supported by your device.","jackpotNotifications":true,"bgColor":"green","hideExit":true,"hideHelp":false,"hideHistory":false,"hideFastplay":false,"hideLobby":false,"hideSound":false,"hideAutoAdjustBet":false,"hideSpaceBarToSpin":false,"disableHistory":false,"disableHelp":false,"disableSound":false,"enforceRoundTime":false,"minQuickRoundTime":-1,"autoPlayResume":false,"disableSpacebarToSpin":false,"resourceLevel":-1,"videoLevel":"-1","fps":0,"matchId":"","betMaxMode":0,"betMaxSpin":false,"playForRealDelay":315300,"renderer":"","disableExitInRound":false,"cId":"","defaultFastPlay":false,"defaultSpacebarToSpin":true,"defaultSound":true,"disableFastplayQuestion":false,"disableVideo":"0","requiredPlatformFeatureSupport":"StencilBuffer,EnforceHardwareAcceleration","customDeviceBlockRegex":"","debug":false,"debugAlert":false,"fullScreenMode":true,"defaultAutoAdjustBet":true,"defaultAutoSpins":"50","limits":"","autoSpins":"10,20,50,75,100","cashierUrl":"","lobbyUrl":"","mobileGameHistoryUrl":"/CasinoHistoryMobile","gameModules":"{\"bundleconfig\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/superwheel/config_${CHANNEL}.json\"}, \"featurepreview\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/superwheel/featurepreview_bundle.json\"}, \"game\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/superwheel/game_bundle.json\"}, \"ui\":{\"script\":\"games/tablegame/superwheel/ui/desktop/superwheel_viewfactory.js\",\"resource\":\"resources/games/tablegame/superwheel/ui_${CHANNEL}_bundle.json\"}}","availableModules":[],"uiVersion":"","gameURL":"games/tablegame/superwheel/superwheel_main.js","playForRealUrl":"","desktopGameHistoryUrl":"/CasinoHistory","hasInGameJackpots":false,"hasFreeInGameJackpots":false,"enforceShowGameName":false,"disableMobileBlurHandling":false,"integrationErrorCodes":"{\"IDS_IERR_UNKNOWN\":\"Internal error\",\"IDS_IERR_UNKNOWNUSER\":\"User unknown\",\"IDS_IERR_INTERNAL\":\"Internal error\",\"IDS_IERR_INVALIDCURRENCY\":\"Unknown currency\",\"IDS_IERR_WRONGUSERNAMEPASSWORD\":\"Unable to authenticate user\",\"IDS_IERR_ACCOUNTLOCKED\":\"Account is locked\",\"IDS_IERR_ACCOUNTDISABLED\":\"Account is disabled\",\"IDS_IERR_NOTENOUGHMONEY\":\"There isnt enough funds on the account\",\"IDS_IERR_MAXCONCURRENTCALLS\":\"The system is currently under heavy load. Please try again later\",\"IDS_IERR_SPENDINGBUDGETEXCEEDED\":\"Your spending budget has been reached.\",\"IDS_IERR_SESSIONEXPIRED\":\"Session has expired. Please restart the game\",\"IDS_IERR_TIMEBUDGETEXCEEDED\":\"Your time budget has been reached\",\"IDS_IERR_SERVICEUNAVAILABLE\":\"The system is temporarily unavailable. Please try again later\",\"IDS_IERR_INVALIDIPLOCATION\":\"You are logging in from a restricted location. Your login has been denied.\",\"IDS_IERR_USERISUNDERAGE\":\"You are blocked from playing these games due to being underage. If you have any questions please contact your customer support\",\"IDS_IERR_SESSIONLIMITEXCEEDED\":\"Your session limit has been reached. Please exit the game and start a new game session to continue playing.\"}","autoplayReset":false,"autoplayLimits":false,"settings":"&settings=%3croot%3e%3climits%3e%3cSingle+Min%3d%221%22+Max%3d%22100%22+%2f%3e%3cTotal+Min%3d%221%22+Max%3d%22500%22+%2f%3e%3c%2flimits%3e%3csettings%3e%3cDenominations%3e%3cdenom+Value%3d%221%22+%2f%3e%3cdenom+Value%3d%225%22+%2f%3e%3cdenom+Value%3d%2210%22+%2f%3e%3cdenom+Value%3d%2225%22+%2f%3e%3cdenom+Value%3d%22100%22+%2f%3e%3c%2fDenominations%3e%3c%2fsettings%3e%3c%2froot%3e","resourceRoot":"/games/SuperWheelPG/2.0.0-games.28/","showSplash":true,"loaderMessage":"","loaderMinShowDuration":0,"realityCheck":"","hasJackpots":false,"helpUrl":"","showClientVersionInHelp":false,"showPlatformVersionInHelp":false,"disableAutoplay":false,"waterMark":false,"displayClock":false,"useServerTime":false,"rCmga":0,"minRoundTime":-1,"detailedFreegameMessage":false,"minSpinningTime":"0","creditDisplay":0,"pingIncreaseInterval":0,"minPingTime":0,"baccaratHistory":7,"gameRoundBalanceCheck":false,"quickStopEnabled":false,"neverGamble":false,"autoHold":false,"denom":"","brand":"common","defaultLimit":0,"freeGameEndLogout":false,"lines":0,"mjDemoText":"","mjsupportmessage":"","mjcongratulations":"Congratulations;You Win","mjprizes":",,,","mjnames":"Mini,Minor,Major,Grand"}';
                            echo $rtStr;
                        }
                        $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                        switch( $aid ) 
                        {
                            case 'freestep':
                                echo 'd=83 ' . $gameData[1] . "\r\n\r\n";
                            case 'exit':
                                echo "d=102 1\r\n\r\n";
                            case 'collect':
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'totalWin');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $response = "d=5 3\r\n6 " . ($totalWin * 100) . ' ' . $allbet . "\r\n52 " . $balanceInCents . ' 0 0';
                                break;
                            case 'endround':
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'totalWin');
                                $slotSettings->SetGameData($slotSettings->slotId . 'totalWin', 0);
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                if( $totalWin > 0 ) 
                                {
                                    $response = "d=2 6\r\n3 0 3 " . ($totalWin * 100) . ' 0 1';
                                }
                                else
                                {
                                    $response = "d=2 6\r\n3 0 0 0 0 1\r\n6 0 5\r\n52 " . $balanceInCents . ' 0 0';
                                }
                                break;
                            case 'spin':
                                $lines = 4;
                                $paysTable = [];
                                $paysTable['p_1'] = 2;
                                $paysTable['p_3'] = 4;
                                $paysTable['p_5'] = 6;
                                $paysTable['p_11'] = 12;
                                $paysTable['p_23'] = 24;
                                $paysTable['p_47'] = 48;
                                $wheelPos = [
                                    47, 
                                    1, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    11, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    1, 
                                    23, 
                                    1, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    11, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    1, 
                                    47, 
                                    1, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    11, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    1, 
                                    23, 
                                    1, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    11, 
                                    3, 
                                    1, 
                                    5, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $bets = [
                                    'p_1' => $gameData[1] / 100, 
                                    'p_3' => $gameData[2] / 100, 
                                    'p_5' => $gameData[3] / 100, 
                                    'p_11' => $gameData[4] / 100, 
                                    'p_23' => $gameData[5] / 100, 
                                    'p_47' => $gameData[6] / 100
                                ];
                                $betline = (trim($gameData[1]) + trim($gameData[2]) + trim($gameData[3]) + trim($gameData[4]) + trim($gameData[5]) + trim($gameData[6]) + trim($gameData[7])) / 100;
                                $allbet = $betline;
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = 'd=90';
                                    exit( $response );
                                }
                                if( $allbet < 0 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet"}';
                                    exit( $response );
                                }
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $bonusMpl = 1;
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [];
                                    $wheelPosition = rand(0, count($wheelPos) - 1);
                                    $wheelPay = $wheelPos[$wheelPosition];
                                    $payRate = $paysTable['p_' . $wheelPay];
                                    $totalWin = $payRate * $bets['p_' . $wheelPay];
                                    if( $totalWin <= $cBank ) 
                                    {
                                        break;
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'totalWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                $reportWin = $totalWin;
                                $winString = implode(' ', $lineWins);
                                $winstring = '';
                                $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $hlPos = 0;
                                if( $wheelPay == 3 ) 
                                {
                                    $hlPos = 1;
                                }
                                if( $wheelPay == 5 ) 
                                {
                                    $hlPos = 2;
                                }
                                if( $wheelPay == 11 ) 
                                {
                                    $hlPos = 3;
                                }
                                if( $wheelPay == 23 ) 
                                {
                                    $hlPos = 4;
                                }
                                if( $wheelPosition == 0 ) 
                                {
                                    $hlPos = 5;
                                }
                                if( $wheelPosition == 26 ) 
                                {
                                    $hlPos = 6;
                                }
                                $response = 'd=1 ' . $wheelPosition . ' ' . $hlPos . ' ' . $wheelPay . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . $gameData[1] . ' ' . ($totalWin * 100) . "\r\n52 " . $balanceInCents . " 0 0\r\n";
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
