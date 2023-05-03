<?php 
namespace VanguardLTE\Games\LeprechaunGoesWildPG
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
                        if( isset($tempData[3]) && $tempData[3] != '' ) 
                        {
                            $gameData = explode(' ', $tempData[3]);
                            if( $gameData[0] == '1' ) 
                            {
                                $aid = 'spin';
                                $postData['slotEvent'] = 'bet';
                            }
                        }
                        else if( isset($tempData[2]) && trim($tempData[2]) == '0' ) 
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
                            if( $gameData[0] == '1' ) 
                            {
                                $aid = 'spin';
                                $postData['slotEvent'] = 'bet';
                            }
                            if( $gameData[0] == '2' ) 
                            {
                                $aid = 'spin';
                                $postData['slotEvent'] = 'freespin';
                            }
                            if( $gameData[0] == '4' ) 
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
                            $rtStr = '{"hasMysteryJackpot":false,"hasGuaranteedJackpot":false,"jackpots":null,"disableSwipeToFullscreenPortraitIos":true,"disableSwipeToFullscreenLandscapeIos":false,"disableSwipeToFullscreenIos":false,"defaultHyperSpin":false,"disableHyperSpin":true,"disableVideoActivationScreen":false,"alwaysShowDecimals":false,"useExternalBalanceOnly":false,"disableScrollToFullscreenMessage":false,"bundleMode":0,"disableInGameModals":false,"disableFastplay":false,"unsupportedDeviceMessage":"This game is currently not supported by your device.","jackpotNotifications":true,"bgColor":"green","hideExit":true,"hideHelp":false,"hideHistory":false,"hideFastplay":false,"hideLobby":false,"hideSound":false,"hideAutoAdjustBet":false,"hideSpaceBarToSpin":false,"disableHistory":false,"disableHelp":false,"disableSound":false,"enforceRoundTime":false,"minQuickRoundTime":-1,"autoPlayResume":false,"disableSpacebarToSpin":false,"resourceLevel":-1,"videoLevel":"-1","fps":0,"matchId":"","betMaxMode":0,"betMaxSpin":false,"playForRealDelay":315300,"renderer":"","disableExitInRound":false,"cId":"","defaultFastPlay":false,"defaultSpacebarToSpin":true,"defaultSound":true,"disableFastplayQuestion":false,"disableVideo":"0","requiredPlatformFeatureSupport":"StencilBuffer,EnforceHardwareAcceleration","customDeviceBlockRegex":"","debug":false,"debugAlert":false,"fullScreenMode":true,"defaultAutoAdjustBet":true,"defaultAutoSpins":"50","limits":"","autoSpins":"10,20,50,75,100","cashierUrl":"","lobbyUrl":"","mobileGameHistoryUrl":"/CasinoHistoryMobile","gameModules":"{\"bundleconfig\":{\"script\":\"\",\"resource\":\"resources/games/videoslot/leprechaungoeswild/config_desktop.json\"}, \"featurepreview\":{\"script\":\"\",\"resource\":\"resources/games/videoslot/leprechaungoeswild/featurepreview_bundle.json\"}, \"game\":{\"script\":\"\",\"resource\":\"resources/games/videoslot/leprechaungoeswild/game_bundle.json\"}, \"ui\":{\"script\":\"games/videoslot/leprechaungoeswild/ui/desktop/leprechaungoeswild_viewfactory.js\",\"resource\":\"resources/games/videoslot/leprechaungoeswild/ui_desktop_bundle.json\"}, \"mysteryjackpot\": {\"script\":\"\", \"resource\":\"resources/games/videoslot/leprechaungoeswild/mysteryjackpot_bundle.json\"}}","availableModules":[],"uiVersion":"","gameURL":"games/videoslot/leprechaungoeswild/leprechaungoeswild_main.js","playForRealUrl":"","desktopGameHistoryUrl":"/CasinoHistory","hasInGameJackpots":false,"hasFreeInGameJackpots":false,"enforceShowGameName":false,"disableMobileBlurHandling":false,"integrationErrorCodes":"{\"IDS_IERR_UNKNOWN\":\"Internal error\",\"IDS_IERR_UNKNOWNUSER\":\"User unknown\",\"IDS_IERR_INTERNAL\":\"Internal error\",\"IDS_IERR_INVALIDCURRENCY\":\"Unknown currency\",\"IDS_IERR_WRONGUSERNAMEPASSWORD\":\"Unable to authenticate user\",\"IDS_IERR_ACCOUNTLOCKED\":\"Account is locked\",\"IDS_IERR_ACCOUNTDISABLED\":\"Account is disabled\",\"IDS_IERR_NOTENOUGHMONEY\":\"There isnt enough funds on the account\",\"IDS_IERR_MAXCONCURRENTCALLS\":\"The system is currently under heavy load. Please try again later\",\"IDS_IERR_SPENDINGBUDGETEXCEEDED\":\"Your spending budget has been reached.\",\"IDS_IERR_SESSIONEXPIRED\":\"Session has expired. Please restart the game\",\"IDS_IERR_TIMEBUDGETEXCEEDED\":\"Your time budget has been reached\",\"IDS_IERR_SERVICEUNAVAILABLE\":\"The system is temporarily unavailable. Please try again later\",\"IDS_IERR_INVALIDIPLOCATION\":\"You are logging in from a restricted location. Your login has been denied.\",\"IDS_IERR_USERISUNDERAGE\":\"You are blocked from playing these games due to being underage. If you have any questions please contact your customer support\",\"IDS_IERR_SESSIONLIMITEXCEEDED\":\"Your session limit has been reached. Please exit the game and start a new game session to continue playing.\"}","autoplayReset":false,"autoplayLimits":false,"settings":"&settings=%3croot%3e%3csettings%3e%3cDenominations%3e%3cdenom+Value%3d%220.01%22+%2f%3e%3cdenom+Value%3d%220.02%22+%2f%3e%3cdenom+Value%3d%220.03%22+%2f%3e%3cdenom+Value%3d%220.04%22+%2f%3e%3cdenom+Value%3d%220.05%22+%2f%3e%3cdenom+Value%3d%220.1%22+%2f%3e%3cdenom+Value%3d%220.15%22+%2f%3e%3cdenom+Value%3d%220.2%22+%2f%3e%3cdenom+Value%3d%220.25%22+%2f%3e%3cdenom+Value%3d%220.3%22+%2f%3e%3cdenom+Value%3d%220.4%22+%2f%3e%3cdenom+Value%3d%220.5%22+%2f%3e%3cdenom+Value%3d%221%22+%2f%3e%3cdenom+Value%3d%222.5%22+%2f%3e%3cdenom+Value%3d%225%22+%2f%3e%3c%2fDenominations%3e%3c%2fsettings%3e%3c%2froot%3e","resourceRoot":"/games/LeprechaunGoesWildPG/2.4.0-leprechaungoeswild.348/","showSplash":true,"loaderMessage":"","loaderMinShowDuration":0,"realityCheck":"","hasJackpots":false,"helpUrl":"","showClientVersionInHelp":false,"showPlatformVersionInHelp":false,"disableAutoplay":false,"waterMark":false,"displayClock":false,"useServerTime":false,"rCmga":0,"minRoundTime":-1,"detailedFreegameMessage":false,"minSpinningTime":"0","creditDisplay":0,"pingIncreaseInterval":0,"minPingTime":0,"baccaratHistory":7,"gameRoundBalanceCheck":false,"quickStopEnabled":true,"neverGamble":false,"autoHold":false,"denom":"10","brand":"common","defaultLimit":0,"freeGameEndLogout":false,"lines":0,"mjDemoText":"","mjsupportmessage":"","mjcongratulations":"Congratulations;You Win","mjprizes":",,,","mjnames":"Mini,Minor,Major,Grand"}';
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
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[5] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[6] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[7] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[8] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[9] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[11] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[12] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[13] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[14] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[15] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[16] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[17] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[18] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[19] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $lines = 20;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $mainLoopLimit = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $response = "d=2 6\r\n3 0 0 0 " . $mainLoopLimit . " 1\r\n";
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
                                        $allbet = $betline * $lines;
                                        if( $slotSettings->GetBalance() < $allbet ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                            exit( $response );
                                        }
                                        if( $slotSettings->GetBalance() < $allbet ) 
                                        {
                                            $response = 'd=90';
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
                                    }
                                    else
                                    {
                                        $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                        $allbet = $betline * $lines;
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
                                    $curSeq = [];
                                    $curSeqWin = 0;
                                    if( $winType == 'bonus' ) 
                                    {
                                        $randomFreeCount = $slotSettings->slotFreeCount[rand(0, 3)];
                                        $winLimitBonus = [
                                            500, 
                                            1000, 
                                            2000, 
                                            5000, 
                                            10000
                                        ];
                                        shuffle($winLimitBonus);
                                        $wlb = 'NULL';
                                        for( $pr = 0; $pr < count($winLimitBonus); $pr++ ) 
                                        {
                                            if( $winLimitBonus[$pr] * $betline <= $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) ) 
                                            {
                                                $wlb = $winLimitBonus[$pr];
                                                break;
                                            }
                                        }
                                        if( $wlb != 'NULL' ) 
                                        {
                                            $presetData = json_decode(trim(file_get_contents(dirname(__FILE__) . '/presets_' . $randomFreeCount . '/p_' . $wlb . '/preset_' . $lines . '.json')), true);
                                            $curSeq = $presetData[rand(0, count($presetData) - 1)];
                                            $curSeqWin = $curSeq['allWin'] * $betline;
                                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentSeq', $curSeq);
                                            if( $curSeqWin > 0 ) 
                                            {
                                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $curSeqWin);
                                            }
                                        }
                                        else
                                        {
                                            $winType = 'none';
                                        }
                                    }
                                    $mainSymAnim = '';
                                    for( $i = 0; $i <= 2000; $i++ ) 
                                    {
                                        $totalWin = 0;
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
                                        $wild = [
                                            '10', 
                                            '11'
                                        ];
                                        $scatter = '11';
                                        $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                        if( $postData['slotEvent'] == 'freespin' || $winType == 'bonus' ) 
                                        {
                                            $curSeq = (array)$slotSettings->GetGameData($slotSettings->slotId . 'CurrentSeq');
                                            $curSeq['result'] = (array)$curSeq['result'];
                                            $curSeq['result'][$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')] = (array)$curSeq['result'][$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')];
                                            $curSeq['result'][$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')]['reels'] = (array)$curSeq['result'][$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')]['reels'];
                                            $reels = $curSeq['result'][$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')]['reels'];
                                        }
                                        if( $postData['slotEvent'] == 'freespin' ) 
                                        {
                                            $stickyWilds = $slotSettings->GetGameData($slotSettings->slotId . 'StickyWilds');
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                for( $p = 0; $p <= 3; $p++ ) 
                                                {
                                                    if( $reels['reel' . $r][$p] == '10' ) 
                                                    {
                                                        $stickyWilds['reel' . $r][$p] = '10';
                                                    }
                                                }
                                            }
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                for( $p = 0; $p <= 3; $p++ ) 
                                                {
                                                    if( $stickyWilds['reel' . $r][$p] == '10' ) 
                                                    {
                                                        $reels['reel' . $r][$p] = '10';
                                                    }
                                                }
                                            }
                                        }
                                        $winLineCount = 0;
                                        for( $k = 0; $k < $lines; $k++ ) 
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
                                                    $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                    $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                    $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                    $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                                    $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                        {
                                                            $mpl = 1;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = $k . ' 0 3 1 ' . ($slotSettings->Paytable['SYM_' . $csym][3] * $mpl * $bonusMpl);
                                                            $mainSymAnim = $csym;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                        {
                                                            $mpl = 1;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = $k . ' 0 4 1 ' . ($slotSettings->Paytable['SYM_' . $csym][4] * $mpl * $bonusMpl);
                                                            $mainSymAnim = $csym;
                                                        }
                                                    }
                                                    if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                                    {
                                                        $mpl = 1;
                                                        if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                        {
                                                            $mpl = 1;
                                                        }
                                                        else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                        {
                                                            $mpl = $slotSettings->slotWildMpl;
                                                        }
                                                        $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $mpl * $bonusMpl;
                                                        if( $cWins[$k] < $tmpWin ) 
                                                        {
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = $tmpStringWin = $k . ' 0 5 1 ' . ($slotSettings->Paytable['SYM_' . $csym][5] * $mpl * $bonusMpl);
                                                        }
                                                    }
                                                }
                                            }
                                            if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                            {
                                                array_push($lineWins, $tmpStringWin);
                                                $totalWin += $cWins[$k];
                                                $winLineCount++;
                                            }
                                        }
                                        $scattersWin = 0;
                                        $scattersStr = '';
                                        $scattersCount = 0;
                                        $scPos = [];
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            for( $p = 0; $p <= 3; $p++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == $scatter ) 
                                                {
                                                    $scattersCount++;
                                                    $scPos[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                                }
                                            }
                                        }
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
                                        else
                                        {
                                            $minWin = $slotSettings->GetRandomPay();
                                            if( $i > 700 ) 
                                            {
                                                $minWin = 0;
                                            }
                                            if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $allbet) ) 
                                            {
                                            }
                                            else
                                            {
                                                if( $postData['slotEvent'] == 'freespin' ) 
                                                {
                                                    break;
                                                }
                                                if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                                        }
                                    }
                                    $freeState = '';
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBalance($totalWin);
                                        if( $postData['slotEvent'] != 'freespin' && $winType != 'bonus' ) 
                                        {
                                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        }
                                    }
                                    $reportWin = $totalWin;
                                    $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                                    $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                                    $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                                    $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                                    $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                    }
                                    $fs = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $randomFreeCount);
                                        $stickyWildsInit = [
                                            'reel1' => [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ], 
                                            'reel2' => [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ], 
                                            'reel3' => [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ], 
                                            'reel4' => [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ], 
                                            'reel5' => [
                                                0, 
                                                0, 
                                                0, 
                                                0
                                            ]
                                        ];
                                        $slotSettings->SetGameData($slotSettings->slotId . 'StickyWilds', $stickyWildsInit);
                                    }
                                    $winString = implode(' ', $lineWins);
                                    $jsSpin = '' . json_encode($reels) . '';
                                    $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                    $winstring = '';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'GambleStep', 5);
                                    $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                                    $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                    $slotSettings->SaveLogReport($response_log, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                    $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                    $resultReels = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p < 4; $p++ ) 
                                        {
                                            $resultReels[] = $reels['reel' . $r][$p];
                                        }
                                    }
                                    $fStart = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $fStart = 1;
                                    }
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $fsLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $mainLoop - 1;
                                        $response .= ('1 1 ' . $lines . ' ' . ($betline * 100) . ' ' . implode(' ', $resultReels) . ' ' . $fStart . ' ' . count($lineWins) . ' ' . $winString . ' 0');
                                        $response .= ("\r\n3 0 " . round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $betline) . ' ' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ' ' . $fsLeft . " 1\r\n");
                                        $slotSettings->SetGameData($slotSettings->slotId . 'StickyWilds', $stickyWilds);
                                    }
                                    else if( $totalWin > 0 ) 
                                    {
                                        $response .= ('d=1 1 ' . $lines . ' ' . ($betline * 100) . ' ' . implode(' ', $resultReels) . ' ' . $fStart . ' ' . count($lineWins) . ' ' . $winString . ' 0');
                                        if( $scattersCount >= 3 ) 
                                        {
                                            $response .= ("\r\n2 0 0 " . $scatter . ' ' . $scattersCount . " 0\r\n2 2 1 10\r\n2 5 1");
                                        }
                                        else
                                        {
                                            $response .= ("\r\n3 0 10 " . ($totalWin * 100) . " 0 1\r\n6 " . ($totalWin * 100) . "\r\n52 " . $balanceInCents . " 0 0\r\n\r\n");
                                        }
                                    }
                                    else
                                    {
                                        $response .= ('d=1 1 ' . $lines . ' ' . ($betline * 100) . ' ' . implode(' ', $resultReels) . ' ' . $fStart . " 0 0\r\n");
                                        if( $scattersCount >= 3 ) 
                                        {
                                            $response .= ('2 0 0 ' . $scatter . ' ' . $scattersCount . " 0\r\n2 2 1 10\r\n2 5 1");
                                        }
                                        else
                                        {
                                            $response .= ("3 0 0 0 0 1\r\n6 0\r\n52 " . $balanceInCents . ' 0 0');
                                        }
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
