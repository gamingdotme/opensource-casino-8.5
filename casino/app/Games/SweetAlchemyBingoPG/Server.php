<?php 
namespace VanguardLTE\Games\SweetAlchemyBingoPG
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
                                $InTableCards = [];
                                for( $i = 1; $i <= 99; $i++ ) 
                                {
                                    $allNums[] = $i;
                                }
                                shuffle($allNums);
                                $cc = [
                                    [
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
                                    ], 
                                    [
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
                                    ], 
                                    [
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
                                    ], 
                                    [
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
                                    ]
                                ];
                                $cardNumOrder = [
                                    0, 
                                    5, 
                                    10, 
                                    1, 
                                    6, 
                                    11, 
                                    2, 
                                    7, 
                                    12, 
                                    3, 
                                    8, 
                                    13, 
                                    4, 
                                    9, 
                                    14
                                ];
                                for( $i = 0; $i < 4; $i++ ) 
                                {
                                    $cr = 0;
                                    for( $j = $i * 15; $j < ($i * 15 + 15); $j++ ) 
                                    {
                                        $cc[$i][$cardNumOrder[$cr]] = $allNums[$j];
                                        $InTableCards[] = $allNums[$j];
                                        $cr++;
                                    }
                                }
                                $slotCards = [
                                    'card1' => $cc[0], 
                                    'card2' => $cc[1], 
                                    'card3' => $cc[2], 
                                    'card4' => $cc[3]
                                ];
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotCards', $slotCards);
                                $slotSettings->SetGameData($slotSettings->slotId . 'InTableCards', $InTableCards);
                                $resultNums = [];
                                for( $j = 0; $j < 15; $j++ ) 
                                {
                                    for( $i = 0; $i < 4; $i++ ) 
                                    {
                                        $resultNums[] = $cc[$i][$j];
                                    }
                                }
                                $response = "d=104 1\r\n54 " . count($bets) . ' ' . implode(' ', $bets) . " 1\r\n57 \"<custom><RTP Value=\"97\" /></custom>\"\r\n52 " . $balanceInCents . " 0 0\r\n56 60 " . implode(' ', $resultNums) . "\r\n91 10349\r\n109";
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
                            if( trim($gameData[0]) == '2' && trim($gameData[1]) == '4' ) 
                            {
                                $aid = 'stepbonus';
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
                            $rtStr = '{"hasMysteryJackpot":false,"hasGuaranteedJackpot":false,"jackpots":null,"disableSwipeToFullscreenPortraitIos":false,"disableSwipeToFullscreenLandscapeIos":false,"disableSwipeToFullscreenIos":false,"defaultHyperSpin":false,"disableHyperSpin":true,"disableVideoActivationScreen":false,"alwaysShowDecimals":false,"useExternalBalanceOnly":false,"disableScrollToFullscreenMessage":false,"bundleMode":0,"disableInGameModals":false,"disableFastplay":false,"unsupportedDeviceMessage":"This game is currently not supported by your device.","jackpotNotifications":true,"bgColor":"green","hideExit":true,"hideHelp":false,"hideHistory":false,"hideFastplay":false,"hideLobby":false,"hideSound":false,"hideAutoAdjustBet":false,"hideSpaceBarToSpin":false,"disableHistory":false,"disableHelp":false,"disableSound":false,"enforceRoundTime":false,"minQuickRoundTime":-1,"autoPlayResume":false,"disableSpacebarToSpin":false,"resourceLevel":-1,"videoLevel":"-1","fps":0,"matchId":"","betMaxMode":0,"betMaxSpin":false,"playForRealDelay":-1,"renderer":"","disableExitInRound":false,"cId":"","defaultFastPlay":false,"defaultSpacebarToSpin":true,"defaultSound":true,"disableFastplayQuestion":false,"disableVideo":"0","requiredPlatformFeatureSupport":"StencilBuffer,EnforceHardwareAcceleration","customDeviceBlockRegex":"","debug":false,"debugAlert":false,"fullScreenMode":true,"defaultAutoAdjustBet":true,"defaultAutoSpins":"50","limits":"","autoSpins":"10,20,50,75,100","cashierUrl":"","lobbyUrl":"","mobileGameHistoryUrl":"/CasinoHistoryMobile","gameModules":"{\"bundleconfig\":{\"script\":\"\",\"resource\":\"resources/games/videobingo/sweetalchemybingo/config_${CHANNEL}.json\"}, \"featurepreview\":{\"script\":\"\",\"resource\":\"resources/games/videobingo/sweetalchemybingo/featurepreview_bundle.json\"}, \"game\":{\"script\":\"\",\"resource\":\"resources/games/videobingo/sweetalchemybingo/game_bundle.json\"}, \"ui\":{\"script\":\"games/videobingo/sweetalchemybingo/ui/desktop/sweetalchemybingo_viewfactory.js\",\"resource\":\"resources/games/videobingo/sweetalchemybingo/ui_${CHANNEL}_bundle.json\"}, \"mysteryjackpot\": {\"script\":\"\", \"resource\":\"resources/games/videobingo/sweetalchemybingo/mysteryjackpot_bundle.json\"}}","availableModules":[],"uiVersion":"","gameURL":"games/videobingo/sweetalchemybingo/sweetalchemybingo_main.js","playForRealUrl":"","desktopGameHistoryUrl":"/CasinoHistory","hasInGameJackpots":false,"hasFreeInGameJackpots":false,"enforceShowGameName":false,"disableMobileBlurHandling":false,"integrationErrorCodes":"{\"IDS_IERR_UNKNOWN\":\"Internal error\",\"IDS_IERR_UNKNOWNUSER\":\"User unknown\",\"IDS_IERR_INTERNAL\":\"Internal error\",\"IDS_IERR_INVALIDCURRENCY\":\"Unknown currency\",\"IDS_IERR_WRONGUSERNAMEPASSWORD\":\"Unable to authenticate user\",\"IDS_IERR_ACCOUNTLOCKED\":\"Account is locked\",\"IDS_IERR_ACCOUNTDISABLED\":\"Account is disabled\",\"IDS_IERR_NOTENOUGHMONEY\":\"There isnt enough funds on the account\",\"IDS_IERR_MAXCONCURRENTCALLS\":\"The system is currently under heavy load. Please try again later\",\"IDS_IERR_SPENDINGBUDGETEXCEEDED\":\"Your spending budget has been reached.\",\"IDS_IERR_SESSIONEXPIRED\":\"Session has expired. Please restart the game\",\"IDS_IERR_TIMEBUDGETEXCEEDED\":\"Your time budget has been reached\",\"IDS_IERR_SERVICEUNAVAILABLE\":\"The system is temporarily unavailable. Please try again later\",\"IDS_IERR_INVALIDIPLOCATION\":\"You are logging in from a restricted location. Your login has been denied.\",\"IDS_IERR_USERISUNDERAGE\":\"You are blocked from playing these games due to being underage. If you have any questions please contact your customer support\",\"IDS_IERR_SESSIONLIMITEXCEEDED\":\"Your session limit has been reached. Please exit the game and start a new game session to continue playing.\"}","autoplayReset":false,"autoplayLimits":false,"settings":"&settings=%3croot%3e%3csettings%3e%3cDenominations%3e%3cdenom+Value%3d%220.01%22+%2f%3e%3cdenom+Value%3d%220.02%22+%2f%3e%3cdenom+Value%3d%220.03%22+%2f%3e%3cdenom+Value%3d%220.04%22+%2f%3e%3cdenom+Value%3d%220.05%22+%2f%3e%3cdenom+Value%3d%220.06%22+%2f%3e%3cdenom+Value%3d%220.07%22+%2f%3e%3cdenom+Value%3d%220.08%22+%2f%3e%3cdenom+Value%3d%220.09%22+%2f%3e%3cdenom+Value%3d%220.1%22+%2f%3e%3c%2fDenominations%3e%3c%2fsettings%3e%3c%2froot%3e","resourceRoot":"/games/SweetAlchemyBingoPG/3.3.0-sweetalchemybingo.345/","showSplash":true,"loaderMessage":"","loaderMinShowDuration":0,"realityCheck":"","hasJackpots":false,"helpUrl":"/casino/gamehelp?pid=2&gameid=434&lang=en_GB&brand=&jurisdiction=&context=&channel=desktop","showClientVersionInHelp":false,"showFFGamesVersionInHelp":false,"disableAutoplay":false,"waterMark":false,"displayClock":false,"useServerTime":false,"rCmga":0,"minRoundTime":-1,"detailedFreegameMessage":false,"minSpinningTime":"","creditDisplay":0,"pingIncreaseInterval":0,"minPingTime":0,"baccaratHistory":7,"gameRoundBalanceCheck":false,"quickStopEnabled":true,"neverGamble":false,"autoHold":false,"denom":"5","brand":"common","defaultLimit":0,"freeGameEndLogout":false,"lines":0,"mjDemoText":"","mjsupportmessage":"","mjcongratulations":";","mjprizes":",,,","mjnames":"Mini,Minor,Major,Grand"}';
                            echo $rtStr;
                        }
                        $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                        switch( $aid ) 
                        {
                            case 'freestep':
                                echo 'd=83 ' . $gameData[1] . "\r\n\r\n";
                            case 'stepbonus':
                                echo 'd=2 4 ' . trim($gameData[2]) . ' ' . trim($gameData[3]) . " \r\n";
                            case 'exit':
                                echo "d=102 1\r\n\r\n";
                            case 'collect':
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'totalWin');
                                $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                                $response = "d=5 3\r\n6 " . ($totalWin * 100) . ' ' . ($allbet * 100) . "\r\n52 " . $balanceInCents . ' 0 0';
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
                            case 'resetcards':
                                $allNums = [];
                                $InTableCards = [];
                                for( $i = 1; $i <= 99; $i++ ) 
                                {
                                    $allNums[] = $i;
                                }
                                shuffle($allNums);
                                $cc = [
                                    [
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
                                    ], 
                                    [
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
                                    ], 
                                    [
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
                                    ], 
                                    [
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
                                    ]
                                ];
                                $cardNumOrder = [
                                    0, 
                                    5, 
                                    10, 
                                    1, 
                                    6, 
                                    11, 
                                    2, 
                                    7, 
                                    12, 
                                    3, 
                                    8, 
                                    13, 
                                    4, 
                                    9, 
                                    14
                                ];
                                for( $i = 0; $i < 4; $i++ ) 
                                {
                                    $cr = 0;
                                    for( $j = $i * 15; $j < ($i * 15 + 15); $j++ ) 
                                    {
                                        $cc[$i][$cardNumOrder[$cr]] = $allNums[$j];
                                        $InTableCards[] = $allNums[$j];
                                        $cr++;
                                    }
                                }
                                $slotCards = [
                                    'card1' => $cc[0], 
                                    'card2' => $cc[1], 
                                    'card3' => $cc[2], 
                                    'card4' => $cc[3]
                                ];
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotCards', $slotCards);
                                $slotSettings->SetGameData($slotSettings->slotId . 'InTableCards', $InTableCards);
                                $resultNums = [];
                                for( $j = 0; $j < 15; $j++ ) 
                                {
                                    for( $i = 0; $i < 4; $i++ ) 
                                    {
                                        $resultNums[] = $cc[$i][$j];
                                    }
                                }
                                $response = 'd=10 60 ' . implode(' ', $resultNums) . "\r\n\r\n";
                                break;
                            case 'spin':
                                $lines = 4;
                                $cardsArr = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    5
                                ];
                                $gameData[4] = trim($gameData[4]);
                                if( $gameData[4] == 1 ) 
                                {
                                    $lines = 1;
                                    $cardsArr = [1];
                                }
                                else if( $gameData[4] == 2 ) 
                                {
                                    $lines = 1;
                                    $cardsArr = [2];
                                }
                                else if( $gameData[4] == 4 ) 
                                {
                                    $lines = 1;
                                    $cardsArr = [3];
                                }
                                else if( $gameData[4] == 8 ) 
                                {
                                    $lines = 1;
                                    $cardsArr = [4];
                                }
                                else if( $gameData[4] == 3 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        1, 
                                        2
                                    ];
                                }
                                else if( $gameData[4] == 5 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        1, 
                                        3
                                    ];
                                }
                                else if( $gameData[4] == 9 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        1, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 6 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        2, 
                                        3
                                    ];
                                }
                                else if( $gameData[4] == 8 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        2, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 12 ) 
                                {
                                    $lines = 2;
                                    $cardsArr = [
                                        3, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 7 ) 
                                {
                                    $lines = 3;
                                    $cardsArr = [
                                        1, 
                                        2, 
                                        3
                                    ];
                                }
                                else if( $gameData[4] == 13 ) 
                                {
                                    $lines = 3;
                                    $cardsArr = [
                                        1, 
                                        3, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 14 ) 
                                {
                                    $lines = 3;
                                    $cardsArr = [
                                        2, 
                                        3, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 11 ) 
                                {
                                    $lines = 3;
                                    $cardsArr = [
                                        1, 
                                        2, 
                                        4
                                    ];
                                }
                                else if( $gameData[4] == 15 ) 
                                {
                                    $lines = 4;
                                    $cardsArr = [
                                        1, 
                                        2, 
                                        3, 
                                        4
                                    ];
                                }
                                $patternArr = [];
                                $paysTable = [];
                                $paysTable[2] = 3;
                                $paysTable[3] = 4;
                                $paysTable[4] = 4;
                                $paysTable[5] = 12;
                                $paysTable[6] = 12;
                                $paysTable[7] = 40;
                                $paysTable[8] = 40;
                                $paysTable[9] = 100;
                                $paysTable[10] = 150;
                                $paysTable[11] = 250;
                                $paysTable[12] = 500;
                                $paysTable[13] = 0;
                                $paysTable[15] = 0;
                                $paysTable[14] = 1500;
                                $betline = ($gameData[1] * $gameData[3]) / 100;
                                $allbet = $betline * $lines;
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
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
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
                                    $isExtraBall = true;
                                    $isBonus = true;
                                    $patternArrCards = [];
                                    $patternArr = [];
                                    $patternArr[0] = [
                                        2, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4
                                    ];
                                    $patternArr[1] = [
                                        2, 
                                        0, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9
                                    ];
                                    $patternArr[2] = [
                                        2, 
                                        0, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArr[3] = [
                                        3, 
                                        0, 
                                        2, 
                                        6, 
                                        8, 
                                        10, 
                                        14
                                    ];
                                    $patternArr[4] = [
                                        4, 
                                        0, 
                                        0, 
                                        4, 
                                        6, 
                                        8, 
                                        12
                                    ];
                                    $patternArr[5] = [
                                        5, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        6, 
                                        8, 
                                        12
                                    ];
                                    $patternArr[6] = [
                                        6, 
                                        0, 
                                        2, 
                                        6, 
                                        8, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArr[7] = [
                                        7, 
                                        0, 
                                        0, 
                                        2, 
                                        4, 
                                        6, 
                                        8, 
                                        10, 
                                        12, 
                                        14
                                    ];
                                    $patternArr[8] = [
                                        8, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        6, 
                                        8, 
                                        11, 
                                        12, 
                                        13
                                    ];
                                    $patternArr[9] = [
                                        9, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9
                                    ];
                                    $patternArr[10] = [
                                        9, 
                                        0, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArr[11] = [
                                        10, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        6, 
                                        8, 
                                        11, 
                                        13
                                    ];
                                    $patternArr[12] = [
                                        11, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        6, 
                                        8, 
                                        11, 
                                        12, 
                                        13
                                    ];
                                    $patternArr[13] = [
                                        12, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        6, 
                                        8, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArr[15] = [
                                        13, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        9, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArr[14] = [
                                        14, 
                                        0, 
                                        0, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9, 
                                        10, 
                                        11, 
                                        12, 
                                        13, 
                                        14
                                    ];
                                    $patternArrCards[0] = $patternArr;
                                    $patternArrCards[1] = $patternArr;
                                    $patternArrCards[2] = $patternArr;
                                    $patternArrCards[3] = $patternArr;
                                    $winsArrCards = [];
                                    $winsArrCards[0] = [];
                                    $winsArrCards[1] = [];
                                    $winsArrCards[2] = [];
                                    $winsArrCards[3] = [];
                                    $lastNumCard = [];
                                    $lastNumCard[0] = 0;
                                    $lastNumCard[1] = 0;
                                    $lastNumCard[2] = 0;
                                    $lastNumCard[3] = 0;
                                    $slotCards = $slotSettings->GetGameData($slotSettings->slotId . 'slotCards');
                                    $InTableCards = $slotSettings->GetGameData($slotSettings->slotId . 'InTableCards');
                                    $resultNumsDetail = [];
                                    $allNums = $InTableCards;
                                    shuffle($allNums);
                                    if( $winType == 'bonus' ) 
                                    {
                                        shuffle($cardsArr);
                                        $bonusCard = $cardsArr[0];
                                        $bonusNums = $slotCards['card' . $bonusCard];
                                        $allNums = [];
                                        $crdn0 = $slotCards['card' . $cardsArr[1]];
                                        $crdn1 = $slotCards['card' . $cardsArr[2]];
                                        $crdn2 = $slotCards['card' . $cardsArr[3]];
                                        $otherNums = array_merge($crdn0, $crdn1, $crdn2);
                                        shuffle($otherNums);
                                        for( $j = 0; $j < 18; $j++ ) 
                                        {
                                            $allNums[] = $otherNums[$j];
                                        }
                                        $allNums[18] = $bonusNums[0];
                                        $allNums[19] = $bonusNums[1];
                                        $allNums[20] = $bonusNums[2];
                                        $allNums[21] = $bonusNums[3];
                                        $allNums[22] = $bonusNums[4];
                                        $allNums[23] = $bonusNums[5];
                                        $allNums[24] = $bonusNums[9];
                                        $allNums[25] = $bonusNums[10];
                                        $allNums[26] = $bonusNums[11];
                                        shuffle($allNums);
                                        $allNums[27] = $bonusNums[12];
                                        $allNums[28] = $bonusNums[13];
                                        $allNums[29] = $bonusNums[14];
                                    }
                                    for( $j = 0; $j < 30; $j++ ) 
                                    {
                                        $num = $allNums[$j];
                                        for( $d = 1; $d <= 4; $d++ ) 
                                        {
                                            if( !in_array($d, $cardsArr) ) 
                                            {
                                            }
                                            else
                                            {
                                                $curCard = $d - 1;
                                                $curNumPos = $slotSettings->GetPositionInCard($slotCards['card' . $d], $num);
                                                $curPay = $slotSettings->CheckPattern($patternArrCards[$curCard], $slotCards['card' . $d], $num);
                                                if( $curNumPos != -1 ) 
                                                {
                                                    $lastNumCard[$curCard] = $num;
                                                    break;
                                                }
                                            }
                                        }
                                        if( count($curPay) > 0 ) 
                                        {
                                            foreach( $curPay as $cpy ) 
                                            {
                                                $payRate_ = $paysTable[$cpy[1]];
                                                $winsArrCards[$curCard][] = $cpy[0] . ' ' . $payRate_ . ' ';
                                                $totalWin += ($payRate_ * $betline);
                                            }
                                        }
                                    }
                                    $resultNums = [];
                                    for( $j = 0; $j < 30; $j++ ) 
                                    {
                                        $resultNums[$j] = $allNums[$j];
                                        $num = $resultNums[$j];
                                        for( $d = 1; $d <= 4; $d++ ) 
                                        {
                                            if( !in_array($d, $cardsArr) ) 
                                            {
                                            }
                                            else
                                            {
                                                $curCard = $d - 1;
                                                $curNumPos = $slotSettings->GetPositionInCard($slotCards['card' . $d], $num);
                                                if( $curNumPos != -1 ) 
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                        if( $curNumPos == -1 ) 
                                        {
                                            $curNumPos = 15;
                                        }
                                        if( count($winsArrCards[$curCard]) > 0 && $lastNumCard[$curCard] == $num ) 
                                        {
                                            $payStr = count($winsArrCards[$curCard]) . ' ';
                                            $payStr .= implode(' ', $winsArrCards[$curCard]);
                                        }
                                        else
                                        {
                                            $payStr = '0 0';
                                        }
                                        $resultNumsDetail[] = '2 2 7 ' . $num . ' ' . $curCard . ' ' . $curNumPos . ' ' . $payStr . "\r\n";
                                    }
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        exit();
                                    }
                                    if( $winType == 'bonus' ) 
                                    {
                                        $bonusData = $slotSettings->CalculateBonus($bonusCard);
                                        $bonusWin = $bonusData[1] * $betline;
                                        $bonusString = $bonusData[0];
                                        $totalWin += $bonusWin;
                                    }
                                    if( $totalWin <= $cBank && $winType == 'bonus' ) 
                                    {
                                        break;
                                    }
                                    if( $winType == 'win' && $totalWin == 0 ) 
                                    {
                                    }
                                    else if( $winType != 'win' && $totalWin > 0 ) 
                                    {
                                    }
                                    else if( $totalWin <= $cBank ) 
                                    {
                                        break;
                                    }
                                }
                                $freeState = '';
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
                                $response_log = '{"responseEvent":"spin","$slotCards":' . json_encode($slotCards) . ',"responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $resultCardsNum = [];
                                for( $j = 0; $j < 15; $j++ ) 
                                {
                                    for( $i = 1; $i <= 4; $i++ ) 
                                    {
                                        $resultCardsNum[] = $slotCards['card' . $i][$j];
                                    }
                                }
                                if( $isExtraBall ) 
                                {
                                    $response = 'd=1 ' . $gameData[1] . ' ' . $gameData[2] . ' ' . $gameData[3] . ' ' . $gameData[4] . ' 60 ' . implode(' ', $resultCardsNum) . "\r\n2 0 0 0 0 0\r\n" . implode('', $resultNumsDetail) . "\r\n2 2 6 1\r\n2 1\r\n";
                                }
                                else if( $winType == 'bonus' ) 
                                {
                                    $bws = [
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $bws[$bonusCard - 1] = $bonusData[1];
                                    $response = 'd=1 ' . $gameData[1] . ' ' . $gameData[2] . ' ' . $gameData[3] . ' ' . $gameData[4] . ' 60 ' . implode(' ', $resultCardsNum) . "\r\n2 0 0 0 0 0\r\n" . implode('', $resultNumsDetail) . "\r\n" . $bonusString . "\r\n2 2 16 4 " . implode(' ', $bws) . "\r\n2 5 1\r\n";
                                    $postData['slotEvent'] = 'BG';
                                }
                                else
                                {
                                    $response = 'd=1 ' . $gameData[1] . ' ' . $gameData[2] . ' ' . $gameData[3] . ' ' . $gameData[4] . ' 60 ' . implode(' ', $resultCardsNum) . "\r\n2 0 0 0 0 0\r\n" . implode('', $resultNumsDetail) . "\r\n2 5 1\r\n";
                                }
                                $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
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
