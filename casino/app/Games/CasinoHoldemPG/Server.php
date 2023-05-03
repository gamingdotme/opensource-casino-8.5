<?php 
namespace VanguardLTE\Games\CasinoHoldemPG
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
                                $response = "d=104 1\r\n53 4 " . $bets[0] . ' 10000 ' . $bets[0] . " 5000\r\n54 " . count($bets) . ' ' . implode(' ', $bets) . ' ' . $bets[0] . "\r\n52 " . $balanceInCents . " 0 0\r\n91 12723\r\n109";
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
                            if( trim($gameData[0]) == '2' && trim($gameData[1]) == '1' ) 
                            {
                                $aid = 'call';
                            }
                            if( trim($gameData[0]) == '2' && trim($gameData[1]) == '0' ) 
                            {
                                $aid = 'fold';
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
                            $rtStr = '{"hasMysteryJackpot":false,"hasGuaranteedJackpot":false,"jackpots":null,"disableSwipeToFullscreenPortraitIos":true,"disableSwipeToFullscreenLandscapeIos":false,"disableSwipeToFullscreenIos":false,"defaultHyperSpin":false,"disableHyperSpin":true,"disableVideoActivationScreen":false,"alwaysShowDecimals":false,"useExternalBalanceOnly":false,"disableScrollToFullscreenMessage":false,"bundleMode":0,"disableInGameModals":false,"disableFastplay":false,"unsupportedDeviceMessage":"This game is currently not supported by your device.","jackpotNotifications":true,"bgColor":"green","hideExit":true,"hideHelp":false,"hideHistory":false,"hideFastplay":false,"hideLobby":false,"hideSound":false,"hideAutoAdjustBet":false,"hideSpaceBarToSpin":false,"disableHistory":false,"disableHelp":false,"disableSound":false,"enforceRoundTime":false,"minQuickRoundTime":-1,"autoPlayResume":false,"disableSpacebarToSpin":false,"resourceLevel":-1,"videoLevel":"-1","fps":0,"matchId":"","betMaxMode":0,"betMaxSpin":false,"playForRealDelay":315300,"renderer":"","disableExitInRound":false,"cId":"","defaultFastPlay":false,"defaultSpacebarToSpin":true,"defaultSound":true,"disableFastplayQuestion":false,"disableVideo":"0","requiredPlatformFeatureSupport":"StencilBuffer,EnforceHardwareAcceleration","customDeviceBlockRegex":"","debug":false,"debugAlert":false,"fullScreenMode":true,"defaultAutoAdjustBet":true,"defaultAutoSpins":"50","limits":"","autoSpins":"10,20,50,75,100","cashierUrl":"","lobbyUrl":"","mobileGameHistoryUrl":"/CasinoHistoryMobile","gameModules":"{\"bundleconfig\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/casinoholdem/config_${CHANNEL}.json\"}, \"featurepreview\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/casinoholdem/featurepreview_bundle.json\"}, \"game\":{\"script\":\"\",\"resource\":\"resources/games/tablegame/casinoholdem/game_bundle.json\"}, \"ui\":{\"script\":\"games/tablegame/casinoholdem/ui/desktop/casinoholdem_viewfactory.js\",\"resource\":\"resources/games/tablegame/casinoholdem/ui_${CHANNEL}_bundle.json\"}, \"tablebrand\": {\"script\":\"\", \"resource\": \"resources/gamelibs/tablegame/common/brand/${Brand}/brand/brand.json\"}}","availableModules":[],"uiVersion":"","gameURL":"games/tablegame/casinoholdem/casinoholdem_main.js","playForRealUrl":"","desktopGameHistoryUrl":"/CasinoHistory","hasInGameJackpots":false,"hasFreeInGameJackpots":false,"enforceShowGameName":false,"disableMobileBlurHandling":false,"integrationErrorCodes":"{\"IDS_IERR_UNKNOWN\":\"Internal error\",\"IDS_IERR_UNKNOWNUSER\":\"User unknown\",\"IDS_IERR_INTERNAL\":\"Internal error\",\"IDS_IERR_INVALIDCURRENCY\":\"Unknown currency\",\"IDS_IERR_WRONGUSERNAMEPASSWORD\":\"Unable to authenticate user\",\"IDS_IERR_ACCOUNTLOCKED\":\"Account is locked\",\"IDS_IERR_ACCOUNTDISABLED\":\"Account is disabled\",\"IDS_IERR_NOTENOUGHMONEY\":\"There isnt enough funds on the account\",\"IDS_IERR_MAXCONCURRENTCALLS\":\"The system is currently under heavy load. Please try again later\",\"IDS_IERR_SPENDINGBUDGETEXCEEDED\":\"Your spending budget has been reached.\",\"IDS_IERR_SESSIONEXPIRED\":\"Session has expired. Please restart the game\",\"IDS_IERR_TIMEBUDGETEXCEEDED\":\"Your time budget has been reached\",\"IDS_IERR_SERVICEUNAVAILABLE\":\"The system is temporarily unavailable. Please try again later\",\"IDS_IERR_INVALIDIPLOCATION\":\"You are logging in from a restricted location. Your login has been denied.\",\"IDS_IERR_USERISUNDERAGE\":\"You are blocked from playing these games due to being underage. If you have any questions please contact your customer support\",\"IDS_IERR_SESSIONLIMITEXCEEDED\":\"Your session limit has been reached. Please exit the game and start a new game session to continue playing.\"}","autoplayReset":false,"autoplayLimits":false,"settings":"&settings=%3croot%3e%3climits%3e%3cAnte+Min%3d%221%22+Max%3d%22100%22+%2f%3e%3cAcesPlus+Min%3d%221%22+Max%3d%2250%22+%2f%3e%3c%2flimits%3e%3csettings%3e%3cDenominations%3e%3cdenom+Value%3d%221%22+%2f%3e%3cdenom+Value%3d%225%22+%2f%3e%3cdenom+Value%3d%2210%22+%2f%3e%3cdenom+Value%3d%2225%22+%2f%3e%3cdenom+Value%3d%22100%22+%2f%3e%3c%2fDenominations%3e%3c%2fsettings%3e%3c%2froot%3e","resourceRoot":"/games/CasinoHoldemPG/2.0.0-games.28/","showSplash":true,"loaderMessage":"","loaderMinShowDuration":0,"realityCheck":"","hasJackpots":false,"helpUrl":"","showClientVersionInHelp":false,"showPlatformVersionInHelp":false,"disableAutoplay":false,"waterMark":false,"displayClock":false,"useServerTime":false,"rCmga":0,"minRoundTime":-1,"detailedFreegameMessage":false,"minSpinningTime":"0","creditDisplay":0,"pingIncreaseInterval":0,"minPingTime":0,"baccaratHistory":7,"gameRoundBalanceCheck":false,"quickStopEnabled":true,"neverGamble":false,"autoHold":false,"denom":"","brand":"common","defaultLimit":0,"freeGameEndLogout":false,"lines":0,"mjDemoText":"","mjsupportmessage":"","mjcongratulations":"Congratulations;You Win","mjprizes":",,,","mjnames":"Mini,Minor,Major,Grand"}';
                            echo $rtStr;
                        }
                        $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                        switch( $aid ) 
                        {
                            case 'fold':
                                $response = "d=2 0 0\r\n3 0 0 0 38 35 23 7 2 2 0 0 1\r\n52 " . $balanceInCents . " 0 0\r\n\r\n";
                                break;
                            case 'exit':
                                echo "d=102 1\r\n\r\n";
                            case 'collect':
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'totalWin');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $response = "d=5 3\r\n6 " . ($totalWin * 100) . ' ' . $allbet . "\r\n52 " . $balanceInCents . ' 0 0';
                                break;
                            case 'call':
                                $postData['slotEvent'] = 'bet';
                                $lines = 1;
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'internalState') != 'ante' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid command"}';
                                    exit( $response );
                                }
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'totalWin');
                                $slotSettings->SetGameData($slotSettings->slotId . 'totalWin', 0);
                                $ante = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $BonusBetWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusBetWin');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet') * 2;
                                $betline = $allbet;
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
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $pays = [];
                                $pays[0] = 0;
                                $pays[1] = 1;
                                $pays[2] = 1;
                                $pays[3] = 1;
                                $pays[4] = 1;
                                $pays[5] = 2;
                                $pays[6] = 3;
                                $pays[7] = 10;
                                $pays[8] = 20;
                                $pays[9] = 100;
                                $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [];
                                    $allCards = $slotSettings->GetGameData($slotSettings->slotId . 'allCards');
                                    shuffle($allCards);
                                    $callCards = [];
                                    $callCards[0] = $allCards[0];
                                    $callCards[1] = $allCards[1];
                                    $callCards[2] = $allCards[2];
                                    $callCards[3] = $allCards[3];
                                    $tableCards = $slotSettings->GetGameData($slotSettings->slotId . 'tableCards');
                                    $dealerCards = [
                                        $callCards[0], 
                                        $callCards[1], 
                                        $tableCards[2], 
                                        $tableCards[3], 
                                        $tableCards[4], 
                                        $callCards[2], 
                                        $callCards[3]
                                    ];
                                    $playerCards = [
                                        $tableCards[0], 
                                        $tableCards[1], 
                                        $tableCards[2], 
                                        $tableCards[3], 
                                        $tableCards[4], 
                                        $callCards[2], 
                                        $callCards[3]
                                    ];
                                    $playerResult = [
                                        0, 
                                        1, 
                                        0, 
                                        0
                                    ];
                                    $dealerResult = [
                                        0, 
                                        1, 
                                        0, 
                                        0
                                    ];
                                    $dealerWinState = '0';
                                    $kickerPlayerCards = 0;
                                    $kickerDealerCards = 0;
                                    $temp_result0 = $slotSettings->GetEachCombination($dealerCards);
                                    $dealerResult = $temp_result0[0];
                                    $temp_result1 = $slotSettings->GetEachCombination($playerCards);
                                    $playerResult = $temp_result1[0];
                                    $kickerDealerCards = '321' . $dealerResult[2] . '321' . $dealerResult[3];
                                    $kickerPlayerCards = '321' . $playerResult[2] . '321' . $playerResult[3];
                                    $totalWinAnte = 0;
                                    $totalWinCall = 0;
                                    if( $dealerResult[1] < $playerResult[1] ) 
                                    {
                                        $totalWinAnte = $ante + ($ante * $pays[$playerResult[1]]);
                                        $totalWinCall = $allbet * 2;
                                        $totalWin = $totalWinAnte + $totalWinCall;
                                        $dealerWinState = '2';
                                    }
                                    else if( $dealerResult[1] == $playerResult[1] ) 
                                    {
                                        if( $dealerResult[2] < $playerResult[2] ) 
                                        {
                                            $totalWinAnte = $ante + ($ante * $pays[$playerResult[1]]);
                                            $totalWinCall = $allbet * 2;
                                            $totalWin = $totalWinAnte + $totalWinCall;
                                            $dealerWinState = '2';
                                        }
                                        else if( $playerResult[2] < $dealerResult[2] ) 
                                        {
                                            $totalWinAnte = 0;
                                            $totalWinCall = 0;
                                            $totalWin = 0;
                                            $dealerWinState = '0';
                                        }
                                        else
                                        {
                                            $totalWinAnte = $ante;
                                            $totalWinCall = $allbet;
                                            $totalWin = $totalWinAnte + $totalWinCall;
                                            $dealerWinState = '1';
                                        }
                                    }
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
                                $reportWin = $totalWin;
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $response = 'd=2 ' . ($allbet * 100) . ' ' . ($BonusBetWin * 100) . "\r\n3 " . $dealerWinState . ' ' . ($totalWinCall * 100) . ' ' . ($totalWinAnte * 100) . ' ' . implode(' ', $callCards) . ' ' . ($dealerResult[1] + 1) . ' ' . ($playerResult[1] + 1) . ' ' . $kickerDealerCards . ' ' . $kickerPlayerCards . " 0\r\n52 " . $balanceInCents . " 0 0\r\n\r\n\r\n";
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusBetWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'internalState', 'call');
                                $response_log = '{"responseEvent":"spin","$dealerResult":' . json_encode($dealerResult) . ',"$playerResult":' . json_encode($playerResult) . ',"$dealerCards":' . json_encode($dealerCards) . ',"$playerCards":' . json_encode($playerCards) . ',"responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                break;
                            case 'spin':
                                $lines = 1;
                                $ante = trim($gameData[1]) / 100;
                                $bonus = trim($gameData[2]) / 100;
                                $betline = (trim($gameData[1]) + trim($gameData[2])) / 100;
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
                                $pays = [];
                                $pays[0] = 0;
                                $pays[1] = 7;
                                $pays[2] = 7;
                                $pays[3] = 7;
                                $pays[4] = 0;
                                $pays[5] = 20;
                                $pays[6] = 30;
                                $pays[7] = 40;
                                $pays[8] = 50;
                                $pays[9] = 100;
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $lineWins = [];
                                    $allCards = [];
                                    for( $j = 0; $j < 52; $j++ ) 
                                    {
                                        $allCards[] = $j;
                                    }
                                    shuffle($allCards);
                                    $tResult = $slotSettings->GetCombination([
                                        $allCards[0], 
                                        $allCards[1], 
                                        $allCards[2], 
                                        $allCards[3], 
                                        $allCards[4]
                                    ]);
                                    if( $pays[$tResult[1]] > 0 && $tResult[1] < 14 ) 
                                    {
                                        $tResult[1] = 0;
                                    }
                                    if( $pays[$tResult[1]] > 0 ) 
                                    {
                                        $totalWin = $bonus + ($bonus * $pays[$tResult[1]]);
                                    }
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusBetWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $ante);
                                $slotSettings->SetGameData($slotSettings->slotId . 'internalState', 'ante');
                                $reportWin = $totalWin;
                                $winString = implode(' ', $lineWins);
                                $winstring = '';
                                $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $kicker = '321' . $tResult[2] . '321' . $tResult[3];
                                if( $tResult[1] + 1 >= 15 ) 
                                {
                                    $tResult[1] = 13;
                                }
                                $response = 'd=1 ' . ($ante * 100) . ' ' . ($bonus * 100) . ' ' . $allCards[0] . ' ' . $allCards[1] . ' ' . $allCards[2] . ' ' . $allCards[3] . ' ' . $allCards[4] . ' ' . ($tResult[1] + 1) . ' ' . $kicker . "\r\n";
                                $tableCards = [];
                                $tableCards[0] = array_shift($allCards);
                                $tableCards[1] = array_shift($allCards);
                                $tableCards[2] = array_shift($allCards);
                                $tableCards[3] = array_shift($allCards);
                                $tableCards[4] = array_shift($allCards);
                                $slotSettings->SetGameData($slotSettings->slotId . 'allCards', $allCards);
                                $slotSettings->SetGameData($slotSettings->slotId . 'tableCards', $tableCards);
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
