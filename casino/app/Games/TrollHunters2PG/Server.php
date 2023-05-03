<?php 
namespace VanguardLTE\Games\TrollHunters2PG
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
                                $response = "d=104 1\r\n54 " . count($bets) . ' ' . implode(' ', $bets) . " 1\r\n57 \"<custom><RTP Value=\"96\" /></custom>\"\r\n52 " . $balanceInCents . " 0 0\r\n83 0\r\n56 0\r\n91 9109\r\n109\r\n\r\n";
                            }
                            if( trim($gameData[0]) == '1' ) 
                            {
                                $aid = 'spin';
                                $postData['slotEvent'] = 'bet';
                            }
                            if( trim($gameData[0]) == '2' ) 
                            {
                                $aid = 'collect2';
                                if( trim($gameData[1]) == '4' ) 
                                {
                                    $isBonusStart0 = $slotSettings->GetGameData($slotSettings->slotId . 'isBonusStart0');
                                    if( $isBonusStart0 ) 
                                    {
                                        $aid = 'spin';
                                        $postData['slotEvent'] = 'freespin';
                                    }
                                    else
                                    {
                                        echo 'd=2 4 ' . trim($gameData[2]) . "\r\n";
                                    }
                                }
                            }
                            if( trim($gameData[0]) == '4' ) 
                            {
                                $aid = 'collect';
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
                            $rtStr = '{"hasMysteryJackpot":false,"hasGuaranteedJackpot":false,"jackpots":null,"disableSwipeToFullscreenPortraitIos":false,"disableSwipeToFullscreenLandscapeIos":false,"disableSwipeToFullscreenIos":false,"defaultHyperSpin":false,"disableHyperSpin":true,"disableVideoActivationScreen":false,"alwaysShowDecimals":false,"useExternalBalanceOnly":false,"disableScrollToFullscreenMessage":false,"bundleMode":0,"disableInGameModals":false,"disableFastplay":false,"unsupportedDeviceMessage":"This game is currently not supported by your device.","jackpotNotifications":true,"bgColor":"green","hideExit":true,"hideHelp":false,"hideHistory":false,"hideFastplay":false,"hideLobby":false,"hideSound":false,"hideAutoAdjustBet":false,"hideSpaceBarToSpin":false,"disableHistory":false,"disableHelp":false,"disableSound":false,"enforceRoundTime":false,"minQuickRoundTime":-1,"autoPlayResume":false,"disableSpacebarToSpin":false,"resourceLevel":-1,"videoLevel":"-1","fps":0,"matchId":"","betMaxMode":0,"betMaxSpin":false,"playForRealDelay":-1,"renderer":"","disableExitInRound":false,"cId":"","defaultFastPlay":false,"defaultSpacebarToSpin":true,"defaultSound":true,"disableFastplayQuestion":false,"disableVideo":"0","requiredPlatformFeatureSupport":"StencilBuffer,EnforceHardwareAcceleration","customDeviceBlockRegex":"","debug":false,"debugAlert":false,"fullScreenMode":true,"defaultAutoAdjustBet":true,"defaultAutoSpins":"50","limits":"","autoSpins":"10,20,50,75,100","cashierUrl":"","lobbyUrl":"","mobileGameHistoryUrl":"/CasinoHistoryMobile","gameModules":"{\"bundleconfig\":{\"script\":\"\",\"resource\":\"resources/games/gridslot/trollhunters2/config_${CHANNEL}.json\"}, \"featurepreview\":{\"script\":\"\",\"resource\":\"resources/games/gridslot/trollhunters2/featurepreview_bundle.json\"}, \"game\":{\"script\":\"\",\"resource\":\"resources/games/gridslot/trollhunters2/game_bundle.json\"}, \"ui\":{\"script\":\"games/gridslot/trollhunters2/ui/desktop/trollhunters2_viewfactory.js\",\"resource\":\"resources/games/gridslot/trollhunters2/ui_${CHANNEL}_bundle.json\"}, \"mysteryjackpot\": {\"script\":\"\", \"resource\":\"resources/games/gridslot/trollhunters2/mysteryjackpot_bundle.json\"}}","availableModules":[],"uiVersion":"","gameURL":"games/gridslot/trollhunters2/trollhunters2_main.js","playForRealUrl":"","desktopGameHistoryUrl":"/CasinoHistory","hasInGameJackpots":false,"hasFreeInGameJackpots":false,"enforceShowGameName":false,"disableMobileBlurHandling":false,"integrationErrorCodes":"{\"IDS_IERR_UNKNOWN\":\"Internal error\",\"IDS_IERR_UNKNOWNUSER\":\"User unknown\",\"IDS_IERR_INTERNAL\":\"Internal error\",\"IDS_IERR_INVALIDCURRENCY\":\"Unknown currency\",\"IDS_IERR_WRONGUSERNAMEPASSWORD\":\"Unable to authenticate user\",\"IDS_IERR_ACCOUNTLOCKED\":\"Account is locked\",\"IDS_IERR_ACCOUNTDISABLED\":\"Account is disabled\",\"IDS_IERR_NOTENOUGHMONEY\":\"There isnt enough funds on the account\",\"IDS_IERR_MAXCONCURRENTCALLS\":\"The system is currently under heavy load. Please try again later\",\"IDS_IERR_SPENDINGBUDGETEXCEEDED\":\"Your spending budget has been reached.\",\"IDS_IERR_SESSIONEXPIRED\":\"Session has expired. Please restart the game\",\"IDS_IERR_TIMEBUDGETEXCEEDED\":\"Your time budget has been reached\",\"IDS_IERR_SERVICEUNAVAILABLE\":\"The system is temporarily unavailable. Please try again later\",\"IDS_IERR_INVALIDIPLOCATION\":\"You are logging in from a restricted location. Your login has been denied.\",\"IDS_IERR_USERISUNDERAGE\":\"You are blocked from playing these games due to being underage. If you have any questions please contact your customer support\",\"IDS_IERR_SESSIONLIMITEXCEEDED\":\"Your session limit has been reached. Please exit the game and start a new game session to continue playing.\"}","autoplayReset":false,"autoplayLimits":false,"settings":"&settings=%3croot%3e%3csettings%3e%3cDenominations%3e%3cdenom+Value%3d%220.01%22+%2f%3e%3cdenom+Value%3d%220.02%22+%2f%3e%3cdenom+Value%3d%220.03%22+%2f%3e%3cdenom+Value%3d%220.04%22+%2f%3e%3cdenom+Value%3d%220.05%22+%2f%3e%3cdenom+Value%3d%220.1%22+%2f%3e%3cdenom+Value%3d%220.25%22+%2f%3e%3cdenom+Value%3d%220.5%22+%2f%3e%3cdenom+Value%3d%221%22+%2f%3e%3cdenom+Value%3d%222%22+%2f%3e%3cdenom+Value%3d%223%22+%2f%3e%3cdenom+Value%3d%224%22+%2f%3e%3cdenom+Value%3d%225%22+%2f%3e%3c%2fDenominations%3e%3c%2fsettings%3e%3c%2froot%3e","resourceRoot":"/games/TrollHunters2PG/3.0.0-trollhunters2.13/","showSplash":true,"loaderMessage":"","loaderMinShowDuration":0,"realityCheck":"","hasJackpots":false,"helpUrl":"/casino/gamehelp?pid=2&gameid=396&lang=en_GB&brand=&jurisdiction=&context=&channel=desktop","showClientVersionInHelp":false,"showFFGamesVersionInHelp":false,"disableAutoplay":false,"waterMark":false,"displayClock":false,"useServerTime":false,"rCmga":0,"minRoundTime":-1,"detailedFreegameMessage":false,"minSpinningTime":"","creditDisplay":0,"pingIncreaseInterval":0,"minPingTime":0,"baccaratHistory":7,"gameRoundBalanceCheck":false,"quickStopEnabled":true,"neverGamble":false,"autoHold":false,"denom":"10","brand":"common","defaultLimit":0,"freeGameEndLogout":false,"lines":0,"mjDemoText":"","mjsupportmessage":"","mjcongratulations":";","mjprizes":",,,","mjnames":"Mini,Minor,Major,Grand"}';
                            echo $rtStr;
                        }
                        $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                        switch( $aid ) 
                        {
                            case 'freestep':
                                echo 'd=83 ' . $gameData[1] . "\r\n\r\n";
                            case 'exit':
                                echo "d=102 1\r\n\r\n";
                            case 'collect2':
                                if( trim($gameData[1]) == '3' ) 
                                {
                                    if( !isset($gameData[2]) ) 
                                    {
                                        $gameData[2] = '0';
                                    }
                                    $selMode = trim($gameData[2]);
                                    if( $selMode == 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 9);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'WilMpl', 1);
                                    }
                                    else if( $selMode == 1 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 7);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'WilMpl', 2);
                                    }
                                    else if( $selMode == 2 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 5);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'WilMpl', 3);
                                    }
                                    $response = 'd=2 3 ' . $selMode . "\r\n2 2 1 5\r\n2 2 6 3\r\n2 5 1";
                                }
                                if( trim($gameData[1]) == '6' ) 
                                {
                                    $isBonusStart = $slotSettings->GetGameData($slotSettings->slotId . 'isBonusStart');
                                    $winInCents = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') * 100;
                                    $bet = $slotSettings->GetGameData($slotSettings->slotId . 'BetLine') * 100;
                                    $winInRate = $winInCents / $bet;
                                    if( $isBonusStart ) 
                                    {
                                        $isBonusStart0 = $slotSettings->GetGameData($slotSettings->slotId . 'isBonusStart0');
                                        if( $isBonusStart0 ) 
                                        {
                                            $response = "d=2 6\r\n2 0 0 2 0 0\r\n2 2 9 0\r\n2 5 1\t";
                                        }
                                        else
                                        {
                                            $slotSettings->SetGameData($slotSettings->slotId . 'isBonusStart0', 1);
                                            $response = "d=2 6\r\n2 0 0 11 0 0\r\n2 1";
                                        }
                                    }
                                    else
                                    {
                                        $freeSpinsLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                        $response = "d=2 6\r\n3 0 " . $winInRate . ' ' . $winInCents . ' ' . $freeSpinsLeft . " 1\r\n";
                                    }
                                }
                                break;
                            case 'collect':
                                echo "d=5 5\r\n6 75\r\n52 " . $balanceInCents . " 0 0\r\n\r\n";
                                break;
                            case 'spin':
                                $linesId = [];
                                $linesId[0] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[1] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[2] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[3] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[4] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[5] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[6] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[7] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[8] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[9] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $lines = 20;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                    $bonusWinCoins = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $betline);
                                    $bonusWinCents = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100);
                                    $freeSpinsLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $mainLoopLimit = $freeSpinsLeft;
                                    $response = "d=2 6\r\n3 0 " . $bonusWinCoins . ' ' . $bonusWinCents . ' ' . $freeSpinsLeft . " 1\r\n";
                                }
                                else
                                {
                                    $response = '';
                                    $mainLoopLimit = 1;
                                }
                                for( $mainLoop = 0; $mainLoop < $mainLoopLimit; $mainLoop++ ) 
                                {
                                    if( $postData['slotEvent'] != 'freespin' ) 
                                    {
                                        $betline = $gameData[3] / 100;
                                        $allbet = ($gameData[1] * $gameData[3]) / 100;
                                        if( $slotSettings->GetBalance() < $allbet ) 
                                        {
                                            $response = 'd=90';
                                            exit( $response );
                                        }
                                        if( $allbet <= 0 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet"}';
                                            exit( $response );
                                        }
                                        $slotSettings->UpdateJackpots($allbet);
                                        $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                        $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                        $slotSettings->UpdateJackpots($allbet);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'isBonusStart1', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'isBonusStart0', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'gameData', $gameData);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                        $bonusMpl = 1;
                                    }
                                    else
                                    {
                                        $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                        $gameData = $slotSettings->GetGameData($slotSettings->slotId . 'gameData');
                                        $allbet = ($gameData[1] * $gameData[3]) / 100;
                                        $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                        $bonusMpl = $slotSettings->slotFreeMpl;
                                    }
                                    $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                    $winType = $winTypeTmp[0];
                                    $spinWinLimit = $winTypeTmp[1];
                                    if( $postData['slotEvent'] == 'freespin' && $winType == 'bonus' ) 
                                    {
                                        $winType = 'win';
                                    }
                                    $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                    $mainSymAnim = '';
                                    for( $i = 0; $i <= 2000; $i++ ) 
                                    {
                                        $totalWin = 0;
                                        $bonusPosition = rand(1, 3);
                                        $allWinsArr = [];
                                        $wild = [8];
                                        $anySyms = [
                                            5, 
                                            6, 
                                            7
                                        ];
                                        $scatter = '9';
                                        $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent'], $bonusPosition);
                                        $tmpReels = $reels;
                                        $reelsOffset = $reels;
                                        $winLineCount = 0;
                                        $currentMpl = 0;
                                        if( $postData['slotEvent'] == 'freespin' ) 
                                        {
                                            $currentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'WilMpl') - 1;
                                        }
                                        for( $offsetLoop = 0; $offsetLoop < 10; $offsetLoop++ ) 
                                        {
                                            $totalWin_ = 0;
                                            $lineWins = [];
                                            $cWins = [
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
                                            $currentMpl++;
                                            $incWildMeter = 0;
                                            $incWildMeter0 = 0;
                                            for( $k = 0; $k < 10; $k++ ) 
                                            {
                                                $tmpStringWin = '';
                                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                                {
                                                    $csym = (string)$slotSettings->SymbolGame[$j];
                                                    if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                                    {
                                                    }
                                                    else
                                                    {
                                                        $s = [];
                                                        $sOffset = [];
                                                        $reelsPosAll = [
                                                            'reel1' => [
                                                                0, 
                                                                5, 
                                                                10, 
                                                                15, 
                                                                20
                                                            ], 
                                                            'reel2' => [
                                                                1, 
                                                                6, 
                                                                11, 
                                                                16, 
                                                                21
                                                            ], 
                                                            'reel3' => [
                                                                2, 
                                                                7, 
                                                                12, 
                                                                17, 
                                                                22
                                                            ], 
                                                            'reel4' => [
                                                                3, 
                                                                8, 
                                                                13, 
                                                                18, 
                                                                23
                                                            ], 
                                                            'reel5' => [
                                                                4, 
                                                                9, 
                                                                14, 
                                                                19, 
                                                                24
                                                            ]
                                                        ];
                                                        if( $k > 4 ) 
                                                        {
                                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                                            $sOffset[0] = $linesId[$k][0] - 1;
                                                            $sOffset[1] = $linesId[$k][1] - 1;
                                                            $sOffset[2] = $linesId[$k][2] - 1;
                                                            $sOffset[3] = $linesId[$k][3] - 1;
                                                            $sOffset[4] = $linesId[$k][4] - 1;
                                                            $dir = 0;
                                                        }
                                                        else
                                                        {
                                                            $s[0] = $reels['reel' . ($k + 1)][0];
                                                            $s[1] = $reels['reel' . ($k + 1)][1];
                                                            $s[2] = $reels['reel' . ($k + 1)][2];
                                                            $s[3] = $reels['reel' . ($k + 1)][3];
                                                            $s[4] = $reels['reel' . ($k + 1)][4];
                                                            $sOffset[0] = 0;
                                                            $sOffset[1] = 1;
                                                            $sOffset[2] = 2;
                                                            $sOffset[3] = 3;
                                                            $sOffset[4] = 4;
                                                            $dir = 1;
                                                        }
                                                        if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 1;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel1'][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel2'][$sOffset[1]] = 8;
                                                                    $reelsOffset['reel3'][$sOffset[2]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[1]] = 8;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[0]] . ' 3 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][3] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                        else if( ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 2;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel2'][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel3'][$sOffset[2]] = 8;
                                                                    $reelsOffset['reel4'][$sOffset[3]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = 8;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[3]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[1]] . ' 3 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][3] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                        else if( ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 3;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel3'][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel4'][$sOffset[3]] = 8;
                                                                    $reelsOffset['reel5'][$sOffset[4]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[3]] = 8;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[4]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[2]] . ' 3 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][3] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                        if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 1;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel1'][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel2'][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel3'][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel4'][$sOffset[3]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[3]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[0]] . ' 4 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][4] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                        if( ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 2;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel2'][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel3'][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel4'][$sOffset[3]] = -1;
                                                                    $reelsOffset['reel5'][$sOffset[4]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[3]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[4]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[1]] . ' 4 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][4] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                        if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                                        {
                                                            $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $currentMpl;
                                                            if( $cWins[$k] < $tmpWin ) 
                                                            {
                                                                $startReel = 1;
                                                                if( $dir == 0 ) 
                                                                {
                                                                    $reelsOffset['reel1'][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel2'][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel3'][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel4'][$sOffset[3]] = -1;
                                                                    $reelsOffset['reel5'][$sOffset[4]] = -1;
                                                                }
                                                                else
                                                                {
                                                                    $startReel = $k + 1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[0]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[1]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[2]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[3]] = -1;
                                                                    $reelsOffset['reel' . ($k + 1)][$sOffset[4]] = -1;
                                                                }
                                                                $cWins[$k] = $tmpWin;
                                                                $incWildMeter = 0;
                                                                if( in_array($csym, $anySyms) ) 
                                                                {
                                                                    $incWildMeter = 10;
                                                                    $incWildMeter0++;
                                                                }
                                                                $tmpStringWin = $incWildMeter . ' ' . $reelsPosAll['reel' . $startReel][$sOffset[0]] . ' 5 ' . $dir . ' ' . ($slotSettings->Paytable['SYM_' . $csym][5] * $currentMpl) . '  ';
                                                            }
                                                        }
                                                    }
                                                }
                                                if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                                {
                                                    array_push($lineWins, $tmpStringWin);
                                                    $totalWin_ += $cWins[$k];
                                                    $winLineCount++;
                                                }
                                            }
                                            $wildmeterString = '';
                                            if( $incWildMeter0 >= 3 ) 
                                            {
                                                $rwp0 = 0;
                                                $rwp1 = rand(0, 24);
                                                $rwp2 = rand(0, 24);
                                                for( $pp = 0; $pp < 5; $pp++ ) 
                                                {
                                                    for( $rr = 1; $rr <= 5; $rr++ ) 
                                                    {
                                                        if( $rwp0 == $rwp1 || $rwp0 == $rwp2 ) 
                                                        {
                                                            $reelsOffset['reel' . $rr][$pp] = 8;
                                                        }
                                                        $rwp0++;
                                                    }
                                                }
                                                $reelsOffset['reel'][$pp] = 8;
                                                $wildmeterString = '2 2 7 0 2 ' . $rwp1 . ' ' . $rwp2 . " \r\n";
                                            }
                                            if( $totalWin_ > 0 ) 
                                            {
                                                $allWinsArr[] = '2 2 8 ' . $currentMpl . ' ' . count($lineWins) . ' ' . implode('', $lineWins) . ' ' . ($offsetLoop + 1) . "\r\n" . $wildmeterString;
                                            }
                                            $reels = $slotSettings->SymbolsOffset($reelsOffset);
                                            $reelsOffset = $reels;
                                            $log_reels[] = [
                                                $reels, 
                                                $totalWin_
                                            ];
                                            $totalWin += $totalWin_;
                                            if( $totalWin_ <= 0 ) 
                                            {
                                                break;
                                            }
                                        }
                                        $scattersWin = 0;
                                        $scattersStr = '';
                                        $scattersCount = 0;
                                        $scPos = [];
                                        if( $reelsOffset['reel1'][$bonusPosition - 1] == -1 && $reelsOffset['reel2'][$bonusPosition - 1] == -1 && $reelsOffset['reel3'][$bonusPosition - 1] == -1 && $reelsOffset['reel4'][$bonusPosition - 1] == -1 && $reelsOffset['reel5'][$bonusPosition - 1] == -1 ) 
                                        {
                                            $scattersCount = 3;
                                        }
                                        $reels = $tmpReels;
                                        $totalWin += $scattersWin;
                                        if( $i > 1000 ) 
                                        {
                                            $winType = 'none';
                                        }
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                        {
                                        }
                                        else if( $scattersCount >= 3 && $winType != 'bonus' ) 
                                        {
                                        }
                                        else if( $scattersCount < 3 && $winType == 'bonus' ) 
                                        {
                                        }
                                        else if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
                                        {
                                            $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                            if( $cBank < $spinWinLimit ) 
                                            {
                                                $spinWinLimit = $cBank;
                                            }
                                            else
                                            {
                                                break;
                                            }
                                        }
                                        else if( $totalWin > 0 && $totalWin <= $spinWinLimit && $winType == 'win' ) 
                                        {
                                            $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                            if( $cBank < $spinWinLimit ) 
                                            {
                                                $spinWinLimit = $cBank;
                                            }
                                            else
                                            {
                                                break;
                                            }
                                        }
                                        else if( $totalWin == 0 && $winType == 'none' ) 
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
                                    $reportWin = $totalWin;
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BetLine', $betline);
                                    }
                                    $fs = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                        $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    }
                                    $winString = implode(' ', $lineWins);
                                    $jsSpin = '' . json_encode($reels) . '';
                                    $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                    $winstring = '';
                                    $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"$log_reels ":' . json_encode($log_reels) . ',"$reelsOffset":' . json_encode($reelsOffset) . ',"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                    $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                    $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                    $resultReels = [];
                                    for( $p = 0; $p < 5; $p++ ) 
                                    {
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            $resultReels[] = $reels['reel' . $r][$p];
                                        }
                                    }
                                    $fStart = false;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $fStart = true;
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'isBonusStart', $fStart);
                                    $bonusWinCoins = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $betline);
                                    $bonusWinCents = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100);
                                    $freeSpinsLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    if( $totalWin > 0 ) 
                                    {
                                        if( $postData['slotEvent'] == 'freespin' ) 
                                        {
                                            $response .= ('1 ' . $gameData[1] . ' 1 ' . $gameData[3] . ' ' . implode(' ', $resultReels) . " 1 0\r\n2 0 0 0 " . count($allWinsArr) . " 0\r\n" . implode('', $allWinsArr) . "2 5 1\r\n");
                                            break;
                                        }
                                        $response .= ('d=1 ' . $gameData[1] . ' 1 ' . $gameData[3] . ' ' . implode(' ', $resultReels) . ' 0 ' . $bonusPosition . "\r\n2 0 0 0 " . count($allWinsArr) . " 0\r\n" . implode('', $allWinsArr) . "2 5 1\r\n\r\n");
                                    }
                                    else if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $response .= ('1 ' . $gameData[1] . ' 1 ' . $gameData[3] . ' ' . implode(' ', $resultReels) . " 0 0\r\n3 0 " . $bonusWinCoins . ' ' . $bonusWinCents . ' ' . $freeSpinsLeft . " 1\r\n");
                                    }
                                    else
                                    {
                                        $response .= ('d=1 ' . $gameData[1] . ' 1 ' . $gameData[3] . ' ' . implode(' ', $resultReels) . ' 0 ' . $bonusPosition . "\r\n3 0 0 0 0 1\r\n6 0\r\n52 " . $balanceInCents . " 0 0\r\n\r\n");
                                    }
                                }
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
