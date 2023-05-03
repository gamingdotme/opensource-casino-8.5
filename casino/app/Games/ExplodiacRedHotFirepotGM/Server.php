<?php 
namespace VanguardLTE\Games\ExplodiacRedHotFirepotGM
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
                        $postData['command'] = $postData['func'];
                        if( $postData['command'] == 'gamble' && $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid gamble state"}';
                            exit( $response );
                        }
                        if( $postData['command'] == 'spin' ) 
                        {
                            $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                            $betline = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
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
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['freegame'] == '1' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bonus state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= 0 && $postData['freegame'] == '1' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bonus state"}';
                                exit( $response );
                            }
                        }
                        $aid = (string)$postData['command'];
                        switch( $aid ) 
                        {
                            case 'init':
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BetStep', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $slotSettings->Bet[0]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Lines', 10);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFirepotRate', 0);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $lastEvent->serverResponse->CurrentBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BetStep', $lastEvent->serverResponse->BetStep);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lastEvent->serverResponse->Lines);
                                    $reels = (array)$lastEvent->serverResponse->reelsSymbols;
                                    $reels['reel1'] = (array)$reels['reel1'];
                                    $reels['reel2'] = (array)$reels['reel2'];
                                    $reels['reel3'] = (array)$reels['reel3'];
                                    $reels['reel4'] = (array)$reels['reel4'];
                                    $reels['reel5'] = (array)$reels['reel5'];
                                    $curBet = $lastEvent->serverResponse->CurrentBet;
                                    $curLines = $lastEvent->serverResponse->Lines;
                                    $r1 = 'i:0;i:' . $reels['reel1'][2] . ';i:1;i:' . $reels['reel1'][1] . ';i:2;i:' . $reels['reel1'][0] . ';i:3;i:' . rand(1, 7) . ';';
                                    $r2 = 'i:0;i:' . $reels['reel2'][2] . ';i:1;i:' . $reels['reel2'][1] . ';i:2;i:' . $reels['reel2'][0] . ';i:3;i:' . rand(1, 7) . ';';
                                    $r3 = 'i:0;i:' . $reels['reel3'][2] . ';i:1;i:' . $reels['reel3'][1] . ';i:2;i:' . $reels['reel3'][0] . ';i:3;i:' . rand(1, 7) . ';';
                                    $r4 = 'i:0;i:' . $reels['reel4'][2] . ';i:1;i:' . $reels['reel4'][1] . ';i:2;i:' . $reels['reel4'][0] . ';i:3;i:' . rand(1, 7) . ';';
                                    $r5 = 'i:0;i:' . $reels['reel5'][2] . ';i:1;i:' . $reels['reel5'][1] . ';i:2;i:' . $reels['reel5'][0] . ';i:3;i:' . rand(1, 7) . ';';
                                    $actFreegame = 0;
                                }
                                else
                                {
                                    $curBet = $slotSettings->Bet[0];
                                    $curLines = 10;
                                    $actFreegame = 0;
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $curLines);
                                    $r1 = 'i:0;i:' . rand(1, 7) . ';i:1;i:' . rand(1, 7) . ';i:2;i:' . rand(1, 7) . ';i:3;i:' . rand(1, 7) . ';';
                                    $r2 = 'i:0;i:' . rand(1, 7) . ';i:1;i:' . rand(1, 7) . ';i:2;i:' . rand(1, 7) . ';i:3;i:' . rand(1, 7) . ';';
                                    $r3 = 'i:0;i:' . rand(1, 7) . ';i:1;i:' . rand(1, 7) . ';i:2;i:' . rand(1, 7) . ';i:3;i:' . rand(1, 7) . ';';
                                    $r4 = 'i:0;i:' . rand(1, 7) . ';i:1;i:' . rand(1, 7) . ';i:2;i:' . rand(1, 7) . ';i:3;i:' . rand(1, 7) . ';';
                                    $r5 = 'i:0;i:' . rand(1, 7) . ';i:1;i:' . rand(1, 7) . ';i:2;i:' . rand(1, 7) . ';i:3;i:' . rand(1, 7) . ';';
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $actFreegame = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1;
                                }
                                $betString = '';
                                for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                                {
                                    $betString .= ('i:' . $i . ';i:' . ($slotSettings->Bet[$i] * 100) . ';');
                                }
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:8:"initData";a:41:{s:5:"reels";a:5:{s:21:"reel_a_initsymboldata";a:4:{' . $r1 . '}s:21:"reel_b_initsymboldata";a:4:{' . $r2 . '}s:21:"reel_c_initsymboldata";a:4:{' . $r3 . '}s:21:"reel_d_initsymboldata";a:4:{' . $r4 . '}s:21:"reel_e_initsymboldata";a:4:{' . $r5 . '}}s:9:"possLines";a:1:{i:0;i:5;}s:15:"maxLinesPerReel";i:0;s:12:"possLinebets";a:' . count($slotSettings->Bet) . ':{' . $betString . '}s:18:"linesForOneLinebet";i:1;s:8:"maxiplay";b:0;s:17:"maxiplayBetFactor";i:1;s:5:"money";i:' . $balanceInCents . ';s:7:"linebet";i:' . ($curBet * 100) . ';s:5:"lines";i:10;s:14:"autospinConfig";a:9:{i:0;i:10;i:1;i:25;i:2;i:50;i:3;i:75;i:4;i:100;i:5;i:250;i:6;i:500;i:7;i:750;i:8;i:1000;}s:18:"unlimitedAutospins";b:1;s:19:"gamblePulseInterval";i:544;s:18:"hasMaxiplayFeature";b:0;s:13:"attentionSpin";b:0;s:12:"maxGambleBet";i:15000;s:14:"maxGambleTries";i:0;s:15:"useGambleLadder";b:1;s:18:"maxGambleLadderBet";i:600000000;s:13:"useGambleCard";b:1;s:19:"useUniqueStopButton";b:0;s:10:"inDemoMode";b:0;s:12:"actFreeRound";i:0;s:13:"maxFreeRounds";i:0;s:12:"freeRoundBet";i:0;s:12:"freeRoundWin";i:0;s:30:"useExternalFreeRoundsIntroSign";b:0;s:27:"dontShowFreeRoundsOutroSign";b:0;s:32:"dontShowWinOnFreeRoundsOutroSign";b:1;s:20:"freeRoundsIntroDelay";i:0;s:20:"freeRoundsOutroDelay";i:3000;s:11:"jackpotGame";a:3:{s:6:"actBet";i:0;s:8:"possBets";a:4:{i:0;i:0;i:1;i:' . (($curBet * $curLines * $slotSettings->FirepotBetRate[1]) / 10 * 100) . ';i:2;i:' . (($curBet * $curLines * $slotSettings->FirepotBetRate[2]) / 10 * 100) . ';i:3;i:' . (($curBet * $curLines * $slotSettings->FirepotBetRate[3]) / 10 * 100) . ";}s:7:\"actStep\";i:0;}s:18:\"linemarkersContent\";s:5014:\"\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n<div class=\"linemarkersBox\">\n\n\t<div class=\"fl linemarkersColumn\">\n\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines4  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">4</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines2  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">2</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines9  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">9</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines6  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">6</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines1  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">1</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines7  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">7</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines10  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">10</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines8  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">8</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines3  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">3</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines5  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">5</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t<div class=\"fr linemarkersColumn\">\n\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines4  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">4</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines2  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">2</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines8  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">8</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines10  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">10</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines1  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">1</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines6  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">6</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines7  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">7</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines9  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">9</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines3  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">3</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarker small lines5  \">\n\t\t\t\t\t<div class=\"linemarkerValue\">5</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"dimmer hidden\"></div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\n\t<div class=\"clear\"></div>\n\n</div>\n\n\n<div class=\"linemarkerClickareasBox\">\n\n\t<div class=\"fl linemarkerClickareasColumn\">\n\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines4 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines2 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines9 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines6 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines1 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines7 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines10 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines8 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines3 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines5 deactivated\"></div>\n\t\t\t\t\t\t</div>\n\t<div class=\"fr linemarkerClickareasColumn\">\n\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines4 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines2 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines8 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines10 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines1 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines6 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines7 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines9 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines3 deactivated\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"linemarkerClickarea small lines5 deactivated\"></div>\n\t\t\t\t\t\t</div>\n\t\n\t<div class=\"clear\"></div>\n\n</div>\";s:19:\"linemarkersContentX\";s:324:\"\n<div class=\"linemarkersBoxX\">\n\n\t<div class=\"fl linemarkersColumn\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t<div class=\"fr linemarkersColumn\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\n\t<div class=\"clear\"></div>\n\n</div>\";s:5:\"sound\";b:1;s:8:\"spaceBar\";b:1;s:9:\"quickSpin\";b:0;s:12:\"leftHandMode\";b:0;s:16:\"featureCountdown\";b:0;s:16:\"showJackpotIntro\";b:1;s:9:\"sessionID\";s:26:\"g2f00dics0r529vs6iktr56mc5\";}}";
                                break;
                            case 'applyUserRequest':
                                $gameBets = $slotSettings->Bet;
                                $req = $_REQUEST['request'];
                                $bstep = $slotSettings->GetGameData($slotSettings->slotId . 'BetStep');
                                if( $req == 'linebetplus' ) 
                                {
                                    $bstep++;
                                    if( count($gameBets) - 1 < $bstep ) 
                                    {
                                        $bstep = 0;
                                    }
                                    $cbet = $gameBets[$bstep];
                                    $fpb = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                    $gameBets = $slotSettings->Bet;
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $bet = $cbet;
                                    $tbets = [];
                                    $tbets[0] = ($bet * $lines * $slotSettings->FirepotBetRate[$fpb]) / 10 * 100;
                                    $tbets[1] = ($bet * $lines * $slotSettings->FirepotBetRate[1]) / 10 * 100;
                                    $tbets[2] = ($bet * $lines * $slotSettings->FirepotBetRate[2]) / 10 * 100;
                                    $tbets[3] = ($bet * $lines * $slotSettings->FirepotBetRate[3]) / 10 * 100;
                                    $topWin = $bet * $slotSettings->FirepotBetRate[$fpb] * 100 * 10000;
                                    $addScore = [
                                        0, 
                                        0, 
                                        2, 
                                        5
                                    ];
                                    $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:2:{i:0;a:2:{s:4:"type";s:15:"possJackpotBets";s:5:"value";a:2:{s:8:"possBets";a:4:{i:0;i:0;i:1;i:' . $tbets[1] . ';i:2;i:' . $tbets[2] . ';i:3;i:' . $tbets[3] . ';}s:8:"infoData";a:5:{s:3:"bet";i:' . $tbets[0] . ';s:6:"topWin";i:' . ($bet * $lines * $slotSettings->FirepotPaytable[$fpb][6] * 100) . ';s:20:"topWinOnHighestLevel";i:0;s:10:"allTopWins";a:3:{i:' . $tbets[1] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[1][6] * 100) . ';i:' . $tbets[2] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[2][6] * 100) . ';i:' . $tbets[3] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[3][6] * 100) . ';}s:5:"bonus";i:0;}}}i:1;a:2:{s:4:"type";s:7:"linebet";s:5:"value";i:' . ($cbet * 100) . ';}}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BetStep', $bstep);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $cbet);
                                }
                                if( $req == 'maxbet' ) 
                                {
                                    $bstep++;
                                    $bstep = count($gameBets) - 1;
                                    $cbet = $gameBets[$bstep];
                                    $fpb = $slotSettings->FirepotBetRate[$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate')];
                                    $gameBets = $slotSettings->Bet;
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $bet = $cbet;
                                    $tbets = [];
                                    $tbets[0] = ($bet * $lines * $slotSettings->FirepotBetRate[$fpb]) / 10 * 100;
                                    $tbets[1] = ($bet * $lines * $slotSettings->FirepotBetRate[1]) / 10 * 100;
                                    $tbets[2] = ($bet * $lines * $slotSettings->FirepotBetRate[2]) / 10 * 100;
                                    $tbets[3] = ($bet * $lines * $slotSettings->FirepotBetRate[3]) / 10 * 100;
                                    $topWin = $bet * $slotSettings->FirepotBetRate[$fpb] * 100 * 10000;
                                    $addScore = [
                                        0, 
                                        0, 
                                        2, 
                                        5
                                    ];
                                    $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:2:{i:0;a:2:{s:4:"type";s:15:"possJackpotBets";s:5:"value";a:2:{s:8:"possBets";a:4:{i:0;i:0;i:1;i:' . $tbets[1] . ';i:2;i:' . $tbets[2] . ';i:3;i:' . $tbets[3] . ';}s:8:"infoData";a:5:{s:3:"bet";i:' . $tbets[0] . ';s:6:"topWin";i:' . ($bet * $lines * $slotSettings->FirepotPaytable[$fpb][6] * 100) . ';s:20:"topWinOnHighestLevel";i:0;s:10:"allTopWins";a:3:{i:' . $tbets[1] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[1][6] * 100) . ';i:' . $tbets[2] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[2][6] * 100) . ';i:' . $tbets[3] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[3][6] * 100) . ';}s:5:"bonus";i:' . $addScore[$fpb] . ';}}}i:1;a:2:{s:4:"type";s:7:"linebet";s:5:"value";i:' . ($cbet * 100) . ';}}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BetStep', $bstep);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $cbet);
                                }
                                if( $req == 'linebetminus' ) 
                                {
                                    $bstep--;
                                    if( $bstep < 0 ) 
                                    {
                                        $bstep = count($gameBets) - 1;
                                    }
                                    $cbet = $gameBets[$bstep];
                                    $fpb = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                    $gameBets = $slotSettings->Bet;
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $bet = $cbet;
                                    $tbets = [];
                                    $tbets[0] = ($bet * $lines * $slotSettings->FirepotBetRate[$fpb]) / 10 * 100;
                                    $tbets[1] = ($bet * $lines * $slotSettings->FirepotBetRate[1]) / 10 * 100;
                                    $tbets[2] = ($bet * $lines * $slotSettings->FirepotBetRate[2]) / 10 * 100;
                                    $tbets[3] = ($bet * $lines * $slotSettings->FirepotBetRate[3]) / 10 * 100;
                                    $topWin = $bet * $slotSettings->FirepotBetRate[$fpb] * 100 * 10000;
                                    $addScore = [
                                        0, 
                                        0, 
                                        2, 
                                        5
                                    ];
                                    $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:2:{i:0;a:2:{s:4:"type";s:15:"possJackpotBets";s:5:"value";a:2:{s:8:"possBets";a:4:{i:0;i:0;i:1;i:' . $tbets[1] . ';i:2;i:' . $tbets[2] . ';i:3;i:' . $tbets[3] . ';}s:8:"infoData";a:5:{s:3:"bet";i:' . $tbets[0] . ';s:6:"topWin";i:' . ($bet * $lines * $slotSettings->FirepotPaytable[$fpb][6] * 100) . ';s:20:"topWinOnHighestLevel";i:0;s:10:"allTopWins";a:3:{i:' . $tbets[1] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[1][6] * 100) . ';i:' . $tbets[2] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[2][6] * 100) . ';i:' . $tbets[3] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[3][6] * 100) . ';}s:5:"bonus";i:' . $addScore[$fpb] . ';}}}i:1;a:2:{s:4:"type";s:7:"linebet";s:5:"value";i:' . ($cbet * 100) . ';}}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BetStep', $bstep);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $cbet);
                                }
                                if( $req == 'linesplus' ) 
                                {
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $lines += 10;
                                    if( $lines > 30 ) 
                                    {
                                        $lines = 10;
                                    }
                                    $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:5:"lines";s:5:"value";i:' . $lines . ';}}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                }
                                if( $req == 'linesminus' ) 
                                {
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $lines -= 10;
                                    if( $lines < 10 ) 
                                    {
                                        $lines = 30;
                                    }
                                    $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:5:"lines";s:5:"value";i:' . $lines . ';}}}';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                }
                                break;
                            case 'closeJackpotIntro':
                                $bstep = $slotSettings->GetGameData($slotSettings->slotId . 'BetStep');
                                $cbet = $slotSettings->Bet[$bstep];
                                $fpb = $slotSettings->FirepotBetRate[$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate')];
                                $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:17:"closeJackpotIntro";s:5:"value";a:2:{s:3:"bet";i:0;s:15:"jackpotBetLevel";i:0;}}}}';
                                break;
                            case 'increaseJackpotBet':
                                $fpb = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                $fpb++;
                                if( $fpb > 3 ) 
                                {
                                    $fpb = 0;
                                }
                                $gameBets = $slotSettings->Bet;
                                $bstep = $slotSettings->GetGameData($slotSettings->slotId . 'BetStep');
                                $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                $bet = $gameBets[$bstep];
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFirepotRate', $fpb);
                                $tbets = [];
                                $tbets[0] = ($bet * $lines * $slotSettings->FirepotBetRate[$fpb]) / 10 * 100;
                                $tbets[1] = ($bet * $lines * $slotSettings->FirepotBetRate[1]) / 10 * 100;
                                $tbets[2] = ($bet * $lines * $slotSettings->FirepotBetRate[2]) / 10 * 100;
                                $tbets[3] = ($bet * $lines * $slotSettings->FirepotBetRate[3]) / 10 * 100;
                                $topWin = $bet * $slotSettings->FirepotBetRate[$fpb] * 100 * 10000;
                                $addScore = [
                                    0, 
                                    0, 
                                    2, 
                                    5
                                ];
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:10:"jackpotBet";s:5:"value";a:5:{s:3:"bet";i:' . $tbets[0] . ';s:6:"topWin";i:' . ($bet * $lines * $slotSettings->FirepotPaytable[$fpb][6] * 100) . ';s:20:"topWinOnHighestLevel";i:0;s:10:"allTopWins";a:3:{i:' . $tbets[1] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[1][6] * 100) . ';i:' . $tbets[2] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[2][6] * 100) . ';i:' . $tbets[3] . ';i:' . ($bet * $lines * $slotSettings->FirepotPaytable[3][6] * 100) . ';}s:5:"bonus";i:' . $addScore[$fpb] . ';}}}}';
                                break;
                            case 'switchToJackpotPlayout':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'finishJackpot':
                                $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:13:"jackpotResult";s:5:"value";a:2:{s:10:"jackpotWin";i:' . ($slotSettings->GetGameData($slotSettings->slotId . 'JackpotWin') * 100) . ';s:11:"playerCoins";i:' . $balanceInCents . ';}}}}';
                                break;
                            case 'setLines':
                                if( $postData['lines'] <= 0 ) 
                                {
                                    exit();
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $postData['lines']);
                                $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:5:"lines";s:5:"value";i:' . $postData['lines'] . ';}}}';
                                break;
                            case 'setSpacebar':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'setQuickspin':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'setFeatureCountdown':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'noGamble':
                                $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:8:"noGamble";s:5:"value";a:1:{s:17:"enforceNextAction";s:4:"wait";}}}}';
                                break;
                            case 'setSound':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'showInfoSite':
                                if( $_REQUEST['section'] == 'paytable' ) 
                                {
                                    $bet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $frate = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                    $hlight = '';
                                    if( $frate <= 0 ) 
                                    {
                                        $frate = 1;
                                        $hlight = 'inactive';
                                    }
                                    $firebet = $slotSettings->FirepotBetRate[$frate] * $bet;
                                    $infoStr = "<div class=\"infoPopup webInfo info1 en\">\n\t\t\n\t<div class=\"scrollContainer\">\n\t\t<div class=\"bigHeadline\">PAYTABLE</div>\n\t\t<div class=\"headline headline-jackpot\">RED HOT FIREPOT</div>\n\t\t<div class=\"description\">With an additional bet the Red Hot Firepot Jackpot Feature is activated, which can trigger a jackpot side game with every spin.</div>\n\t\t<div class=\"jackpot-phases\"></div>\n\t\t<div class=\"description\">When the Red Hot Firepot is triggered, three ovens appear that open successively from left to right. If all three ovens are lit, continue to the Firepot draw. The collected points on the reels fill the thermometer and thus determine the Jackpot win.</div>\n\t\t<div class=\"jackpot-winValues winValueBox  " . $hlight . " \" style=\"min-width:600px;\" id=\"jackpotWinValues\">\n\t\t\t<div class=\"fl thermometer\"></div>\n\t\t\t<div class=\"fl winValues\">\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][6] * $bet * $lines) . "</div>\r\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][5] * $bet * $lines) . "</div>\r\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][4] * $bet * $lines) . "</div>\r\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][3] * $bet * $lines) . "</div>\r\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][2] * $bet * $lines) . "</div>\r\n\t\t\t\t\t\t\t\t\t<div class=\"winValue\">" . sprintf('%01.2f', $slotSettings->FirepotPaytable[$frate][1] * $bet * $lines) . "</div>\n\t\t\t\t\t\t\t\t<div class=\"winValue topWinOnHighestLevel\">60,000.00</div>\n\t\t\t</div>\n\t\t\t<div class=\"clear\"></div>\n\t\t</div>\n\t\t<div class=\"headline\">WILD</div>\n\t\t<div class=\"description\">\"Bomb\" is Wild and substitutes for all symbols</div>\n\t\t<div class=\"wildSymbolBox\"></div>\n\t\t<div class=\"description\">\"Bomb\" explodes and transforms itself and all surrounding symbols into Wilds</div>\n\t\t<div class=\"specialFeature\"></div>\n\t\t\n\t\t<div class=\"headline\">PAYOUT VALUES</div>\n\t\t<div class=\"centerColumn paytableValues\" id=\"paytableValues\">\n\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fl winValueBox winValues-7\" id=\"winValues-7\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-7\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value0-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_7'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value1-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_7'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value2-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_7'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fr winValueBox winValues-6\" id=\"winValues-6\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-6\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value3-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_6'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value4-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_6'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value5-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_6'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fl winValueBox winValues-5\" id=\"winValues-5\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-5\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value6-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_5'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value7-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_5'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value8-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_5'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fr winValueBox winValues-4\" id=\"winValues-4\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-4\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value9-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_4'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value10-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_4'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value11-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_4'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fl winValueBox winValues-3\" id=\"winValues-3\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-3\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value12-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_3'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value13-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_3'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value14-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_3'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fr winValueBox winValues-2\" id=\"winValues-2\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-2\"></div>\n\t\t\t\t\t<div class=\"fr values \">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value15-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_2'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value16-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_2'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value17-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_2'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"fl winValueBox winValues-1\" id=\"winValues-1\">\n\t\t\t\t\t<div class=\"fl symbolImg symbolImg-1\"></div>\n\t\t\t\t\t<div class=\"fr values fourLines\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue widthReference\" id=\"value18-5\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">5</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_1'][5] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value19-4\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">4</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_1'][4] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"winValue \" id=\"value20-3\">\n\t\t\t\t\t\t\t\t<span class=\"multiplicator\">3</span>\n\t\t\t\t\t\t\t\t<span class=\"winValueText\">" . sprintf('%01.2f', $slotSettings->Paytable['SYM_1'][3] * $bet) . "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"clear\"></div>\n\t\t</div>\n\t\t\n\t\t<div class=\"headline\">PAYLINES</div>\n\t\t<div class=\"paylines\"></div>\n\t\t\n\t</div>\n\n</div>";
                                    $result_tmp[] = 'INFO$$1$$a:1:{s:7:"display";a:2:{i:0;a:2:{s:4:"type";s:7:"linebet";s:5:"value";i:' . ($bet * 100) . ';}i:1;a:2:{s:4:"type";s:4:"info";s:5:"value";s:' . strlen($infoStr) . ':"' . $infoStr . '";}}}';
                                }
                                else
                                {
                                    $result_tmp[0] = "INFO\$\$1\$\$a:1:{s:7:\"display\";a:1:{i:0;a:3:{s:4:\"type\";s:4:\"info\";s:5:\"value\";s:960:\"<div class=\"infoPopup webInfo info2 en\">\n\t\n\t\t<div class=\"settingsBox\">\n\t\t\t\t\t\t\t<div class=\"setting\">\n\t\t\t\t\t<div class=\"setGameSetting settingCheckbox active fl setting1\" id=\"settingCheckbox-1\"></div>\n\t\t\t\t\t<div class=\"settingText fl setting1\">SPIN WITH SPACEBAR</div>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"setting quickspin\">\n\t\t\t\t<div class=\"setGameSetting settingCheckbox active fl setting2\" id=\"settingCheckbox-2\"></div>\n\t\t\t\t<div class=\"settingText fl setting2\">TURBO SPIN</div>\n\t\t\t\t<div class=\"clear\"></div>\n\t\t\t</div>\n\t\t\t<div class=\"setting featureCountdown\">\n\t\t\t\t<div class=\"setGameSetting settingCheckbox active fl setting3\" id=\"settingCheckbox-3\"></div>\n\t\t\t\t<div class=\"settingText fl setting3\">\n\t\t\t\t\t<span class=\"freegame hidden\">AUTOMATIC FREE GAMES ENTRY</span>\n\t\t\t\t\t<span class=\"feature hidden\">AUTOMATIC FEATURE ENTRY</span>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"clear\"></div>\n\t\t\t</div>\n\t\t\t\t\t</div>\n\t\n\t<div class=\"closeButton\"></div>\t\n</div>\";s:14:\"isSettingsSite\";b:1;}}}";
                                }
                                break;
                            case 'triggerGameEvent':
                                $result_tmp[] = 'INFO$$1$$a:0:{}';
                                break;
                            case 'spin':
                                $linesId = [];
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[1] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[2] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[3] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[4] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[5] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[6] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[7] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[8] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[9] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[11] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[13] = [
                                    1, 
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[14] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[17] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[18] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[21] = [
                                    1, 
                                    3, 
                                    2, 
                                    3, 
                                    1
                                ];
                                $linesId[22] = [
                                    3, 
                                    1, 
                                    2, 
                                    1, 
                                    3
                                ];
                                $linesId[23] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[25] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[26] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $linesId[27] = [
                                    1, 
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[28] = [
                                    3, 
                                    3, 
                                    1, 
                                    3, 
                                    3
                                ];
                                $linesId[29] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                $betline = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $betline * $lines;
                                $postData['slotEvent'] = 'bet';
                                $frate = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                $firebet = ($betline * $lines * $slotSettings->FirepotBetRate[$frate]) / 10;
                                if( $postData['freegame'] == '1' ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $postData['slotEvent'] != 'freespin' ) 
                                {
                                    if( !isset($postData['slotEvent']) ) 
                                    {
                                        $postData['slotEvent'] = 'bet';
                                    }
                                    if( $slotSettings->GetBalance() < ($allbet + $firebet) ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
                                        exit( $response );
                                    }
                                    $bankSum = ($allbet + $firebet) / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                    $slotSettings->UpdateJackpots($allbet + $firebet);
                                    $slotSettings->SetBalance(-1 * ($allbet + $firebet), $postData['slotEvent']);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                    $bonusMpl = 1;
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
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
                                        0
                                    ];
                                    $wild = ['8'];
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reels2 = $reels;
                                    $reelsTmp = $reels;
                                    $specFeatureSymbolPositions = 'b:0;';
                                    $specFeatureSymbolPositionsArr = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][0] == 8 ) 
                                        {
                                            $reels['reel2'][0] = 8;
                                            $reels['reel2'][1] = 8;
                                            $reels['reel3'][0] = 8;
                                            $reels['reel3'][1] = 8;
                                            $reels['reel4'][0] = 8;
                                            $reels['reel4'][1] = 8;
                                            $specFeatureSymbolPositions = 'a:3:{s:1:"b";a:2:{i:1;i:1;i:0;i:1;}s:1:"c";a:2:{i:1;i:1;i:0;i:1;}s:1:"d";a:2:{i:1;i:1;i:0;i:1;}}';
                                            break;
                                        }
                                        if( $reels['reel' . $r][1] == 8 ) 
                                        {
                                            $reels['reel2'][0] = 8;
                                            $reels['reel2'][1] = 8;
                                            $reels['reel2'][2] = 8;
                                            $reels['reel3'][0] = 8;
                                            $reels['reel3'][1] = 8;
                                            $reels['reel3'][2] = 8;
                                            $reels['reel4'][0] = 8;
                                            $reels['reel4'][1] = 8;
                                            $reels['reel4'][2] = 8;
                                            $specFeatureSymbolPositions = 'a:3:{s:1:"b";a:3:{i:2;i:1;i:1;i:1;i:0;i:1;}s:1:"c";a:3:{i:2;i:1;i:1;i:1;i:0;i:1;}s:1:"d";a:3:{i:2;i:1;i:1;i:1;i:0;i:1;}}';
                                            break;
                                        }
                                        if( $reels['reel' . $r][2] == 8 ) 
                                        {
                                            $reels['reel2'][1] = 8;
                                            $reels['reel2'][2] = 8;
                                            $reels['reel3'][1] = 8;
                                            $reels['reel3'][2] = 8;
                                            $reels['reel4'][1] = 8;
                                            $reels['reel4'][2] = 8;
                                            $specFeatureSymbolPositions = 'a:3:{s:1:"b";a:2:{i:2;i:1;i:1;i:1;}s:1:"c";a:2:{i:2;i:1;i:1;i:1;}s:1:"d";a:2:{i:2;i:1;i:1;i:1;}}';
                                            break;
                                        }
                                    }
                                    $spc = 0;
                                    $rids = [
                                        '', 
                                        'a', 
                                        'b', 
                                        'c', 
                                        'd', 
                                        'e'
                                    ];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $specFeatureSymbolPositionsArr[$spc] = 's:1:"' . $rids[$r] . '";';
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == 8 ) 
                                            {
                                                $specFeatureSymbolPositionsArr[$spc] = 's:1:"b";a:2:{i:2;i:1;i:1;i:1;}';
                                                $spc++;
                                            }
                                        }
                                    }
                                    $winCount = 0;
                                    $symbolAnimsArr = [];
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
                                                if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                                {
                                                    $mpl = 1;
                                                    if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                    {
                                                        $mpl = 1;
                                                    }
                                                    else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                    {
                                                        $mpl = $slotSettings->slotWildMpl;
                                                    }
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = 'i:' . $winCount . ';a:6:{i:0;i:' . ($k + 1) . ';i:1;i:' . $s[0] . ';i:2;i:' . $s[1] . ';i:3;b:0;i:4;b:0;i:5;b:0;}';
                                                        $reels2['reel1'][$linesId[$k][0] - 1] = -1;
                                                        $reels2['reel2'][$linesId[$k][1] - 1] = -1;
                                                    }
                                                }
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
                                                        $tmpStringWin = 'i:' . $winCount . ';a:6:{i:0;i:' . ($k + 1) . ';i:1;i:' . $s[0] . ';i:2;i:' . $s[1] . ';i:3;i:' . $s[2] . ';i:4;b:0;i:5;b:0;}';
                                                        $reels2['reel1'][$linesId[$k][0] - 1] = -1;
                                                        $reels2['reel2'][$linesId[$k][1] - 1] = -1;
                                                        $reels2['reel3'][$linesId[$k][2] - 1] = -1;
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
                                                        $tmpStringWin = 'i:' . $winCount . ';a:6:{i:0;i:' . ($k + 1) . ';i:1;i:' . $s[0] . ';i:2;i:' . $s[1] . ';i:3;i:' . $s[2] . ';i:4;i:' . $s[3] . ';i:5;b:0;}';
                                                        $reels2['reel1'][$linesId[$k][0] - 1] = -1;
                                                        $reels2['reel2'][$linesId[$k][1] - 1] = -1;
                                                        $reels2['reel3'][$linesId[$k][2] - 1] = -1;
                                                        $reels2['reel4'][$linesId[$k][3] - 1] = -1;
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
                                                        $tmpStringWin = 'i:' . $winCount . ';a:6:{i:0;i:' . ($k + 1) . ';i:1;i:' . $s[0] . ';i:2;i:' . $s[1] . ';i:3;i:' . $s[2] . ';i:4;i:' . $s[3] . ';i:5;i:' . $s[4] . ';}';
                                                        $reels2['reel1'][$linesId[$k][0] - 1] = -1;
                                                        $reels2['reel2'][$linesId[$k][1] - 1] = -1;
                                                        $reels2['reel3'][$linesId[$k][2] - 1] = -1;
                                                        $reels2['reel4'][$linesId[$k][3] - 1] = -1;
                                                        $reels2['reel5'][$linesId[$k][4] - 1] = -1;
                                                    }
                                                }
                                            }
                                        }
                                        if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                        {
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$k];
                                            $winCount++;
                                        }
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scPos = [
                                        'i:0;a:6:{i:0;i:0;', 
                                        'i:1;b:0;', 
                                        'i:2;b:0;', 
                                        'i:3;b:0;', 
                                        'i:4;b:0;', 
                                        'i:5;b:0;', 
                                        '}'
                                    ];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][0] == $scatter || $reels['reel' . $r][1] == $scatter || $reels['reel' . $r][2] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[$r] = 'i:' . $r . ';i:' . $scatter . ';';
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    $scattersStr = implode('', $scPos);
                                    if( $scattersCount >= 3 || $scattersWin > 0 ) 
                                    {
                                        array_push($lineWins, $scattersStr);
                                    }
                                    $winCountAnim = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == 8 ) 
                                            {
                                                $animStr = 'wild';
                                                $symbolAnimsArr[] = 'i:' . $winCountAnim . ';a:3:{s:4:"reel";i:' . ($r - 1) . ';s:3:"pos";i:' . $p . ';s:9:"animation";s:4:"wild";}';
                                                $winCountAnim++;
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
                                            $chanceFirepot = 0;
                                            $frate = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFirepotRate');
                                            if( $frate == 1 ) 
                                            {
                                                $chanceFirepot = rand(1, 50);
                                            }
                                            else if( $frate == 2 ) 
                                            {
                                                $chanceFirepot = rand(1, 30);
                                            }
                                            else if( $frate == 3 ) 
                                            {
                                                $chanceFirepot = rand(1, 15);
                                            }
                                            $slotSettings->SetGameData($slotSettings->slotId . 'JackpotWin', 0);
                                            if( $chanceFirepot == 1 && $postData['slotEvent'] != 'freespin' ) 
                                            {
                                                if( $frate == 1 ) 
                                                {
                                                    $fireCount = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3
                                                    ];
                                                    $startPoints = 0;
                                                }
                                                else if( $frate == 2 ) 
                                                {
                                                    $fireCount = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3
                                                    ];
                                                    $startPoints = 2;
                                                }
                                                else if( $frate == 3 ) 
                                                {
                                                    $fireCount = [
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3
                                                    ];
                                                    $startPoints = 5;
                                                }
                                                shuffle($fireCount);
                                                if( $fireCount[0] >= 3 ) 
                                                {
                                                    $winType = 'win';
                                                    $spinWinLimit = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                                    $fireScore = $startPoints;
                                                    $fireReelsInit = [
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
                                                        100, 
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
                                                        100, 
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
                                                        100
                                                    ];
                                                    $fireReelsResult = [];
                                                    $fireReelsArr = [];
                                                    $fireReelsArr[0] = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        4, 
                                                        4, 
                                                        4, 
                                                        5, 
                                                        5, 
                                                        5, 
                                                        6, 
                                                        6, 
                                                        6, 
                                                        7, 
                                                        7, 
                                                        7, 
                                                        8, 
                                                        8, 
                                                        8, 
                                                        9, 
                                                        9, 
                                                        9, 
                                                        100, 
                                                        100, 
                                                        100
                                                    ];
                                                    $fireReelsArr[1] = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        4, 
                                                        4, 
                                                        4, 
                                                        5, 
                                                        5, 
                                                        5, 
                                                        6, 
                                                        6, 
                                                        6, 
                                                        7, 
                                                        7, 
                                                        7, 
                                                        8, 
                                                        8, 
                                                        8, 
                                                        9, 
                                                        9, 
                                                        9, 
                                                        100, 
                                                        100, 
                                                        100
                                                    ];
                                                    $fireReelsArr[2] = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        4, 
                                                        4, 
                                                        4, 
                                                        5, 
                                                        5, 
                                                        5, 
                                                        6, 
                                                        6, 
                                                        6, 
                                                        7, 
                                                        7, 
                                                        7, 
                                                        8, 
                                                        8, 
                                                        8, 
                                                        9, 
                                                        9, 
                                                        9, 
                                                        100, 
                                                        100, 
                                                        100
                                                    ];
                                                    $fireReelsArr[3] = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        4, 
                                                        4, 
                                                        4, 
                                                        5, 
                                                        5, 
                                                        5, 
                                                        6, 
                                                        6, 
                                                        6, 
                                                        7, 
                                                        7, 
                                                        7, 
                                                        8, 
                                                        8, 
                                                        8, 
                                                        9, 
                                                        9, 
                                                        9, 
                                                        100, 
                                                        100, 
                                                        100
                                                    ];
                                                    $fireReelsArr[4] = [
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        1, 
                                                        2, 
                                                        2, 
                                                        2, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        3, 
                                                        4, 
                                                        4, 
                                                        4, 
                                                        5, 
                                                        5, 
                                                        5, 
                                                        6, 
                                                        6, 
                                                        6, 
                                                        7, 
                                                        7, 
                                                        7, 
                                                        8, 
                                                        8, 
                                                        8, 
                                                        9, 
                                                        9, 
                                                        9, 
                                                        100, 
                                                        100, 
                                                        100
                                                    ];
                                                    shuffle($fireReelsArr[0]);
                                                    shuffle($fireReelsArr[1]);
                                                    shuffle($fireReelsArr[2]);
                                                    shuffle($fireReelsArr[3]);
                                                    shuffle($fireReelsArr[4]);
                                                    shuffle($fireReelsInit);
                                                    $spinSymbolsJack = 's:11:"spinSymbols";a:5:{';
                                                    $spinSymbolsJack .= ('i:0;a:15:{i:0;i:6;i:1;i:1;i:2;i:100;i:3;i:4;i:4;i:2;i:5;i:8;i:6;i:100;i:7;i:1;i:8;i:9;i:9;i:2;i:10;i:8;i:11;i:1;i:12;i:7;i:13;i:' . $fireReelsArr[0][0] . ';i:14;i:100;}');
                                                    $spinSymbolsJack .= ('i:1;a:35:{i:0;i:100;i:1;i:2;i:2;i:7;i:3;i:3;i:4;i:8;i:5;i:100;i:6;i:3;i:7;i:9;i:8;i:1;i:9;i:5;i:10;i:100;i:11;i:1;i:12;i:8;i:13;i:2;i:14;i:100;i:15;i:1;i:16;i:8;i:17;i:3;i:18;i:100;i:19;i:1;i:20;i:100;i:21;i:4;i:22;i:2;i:23;i:6;i:24;i:100;i:25;i:1;i:26;i:9;i:27;i:2;i:28;i:100;i:29;i:1;i:30;i:7;i:31;i:2;i:32;i:100;i:33;i:' . $fireReelsArr[1][0] . ';i:34;i:7;}');
                                                    $spinSymbolsJack .= ('i:2;a:58:{i:0;i:5;i:1;i:100;i:2;i:1;i:3;i:100;i:4;i:2;i:5;i:8;i:6;i:1;i:7;i:100;i:8;i:3;i:9;i:6;i:10;i:1;i:11;i:100;i:12;i:4;i:13;i:2;i:14;i:8;i:15;i:100;i:16;i:1;i:17;i:7;i:18;i:2;i:19;i:8;i:20;i:1;i:21;i:100;i:22;i:1;i:23;i:9;i:24;i:2;i:25;i:100;i:26;i:3;i:27;i:100;i:28;i:2;i:29;i:7;i:30;i:3;i:31;i:9;i:32;i:1;i:33;i:100;i:34;i:5;i:35;i:100;i:36;i:1;i:37;i:100;i:38;i:2;i:39;i:8;i:40;i:1;i:41;i:100;i:42;i:3;i:43;i:6;i:44;i:1;i:45;i:100;i:46;i:4;i:47;i:2;i:48;i:8;i:49;i:100;i:50;i:1;i:51;i:7;i:52;i:2;i:53;i:8;i:54;i:1;i:55;i:100;i:56;i:' . $fireReelsArr[2][0] . ';i:57;i:9;}');
                                                    $spinSymbolsJack .= ('i:3;a:80:{i:0;i:2;i:1;i:8;i:2;i:100;i:3;i:1;i:4;i:9;i:5;i:2;i:6;i:100;i:7;i:1;i:8;i:7;i:9;i:8;i:10;i:2;i:11;i:7;i:12;i:3;i:13;i:8;i:14;i:2;i:15;i:6;i:16;i:3;i:17;i:9;i:18;i:1;i:19;i:100;i:20;i:5;i:21;i:100;i:22;i:1;i:23;i:100;i:24;i:2;i:25;i:100;i:26;i:1;i:27;i:100;i:28;i:3;i:29;i:100;i:30;i:1;i:31;i:100;i:32;i:4;i:33;i:2;i:34;i:8;i:35;i:100;i:36;i:1;i:37;i:9;i:38;i:2;i:39;i:100;i:40;i:1;i:41;i:7;i:42;i:8;i:43;i:2;i:44;i:7;i:45;i:3;i:46;i:8;i:47;i:2;i:48;i:6;i:49;i:3;i:50;i:9;i:51;i:1;i:52;i:100;i:53;i:5;i:54;i:100;i:55;i:1;i:56;i:100;i:57;i:2;i:58;i:100;i:59;i:1;i:60;i:100;i:61;i:3;i:62;i:100;i:63;i:1;i:64;i:100;i:65;i:4;i:66;i:2;i:67;i:8;i:68;i:100;i:69;i:1;i:70;i:9;i:71;i:2;i:72;i:100;i:73;i:1;i:74;i:7;i:75;i:8;i:76;i:2;i:77;i:7;i:78;i:' . $fireReelsArr[3][0] . ';i:79;i:8;}');
                                                    $spinSymbolsJack .= ('i:4;a:109:{i:0;i:100;i:1;i:2;i:2;i:100;i:3;i:3;i:4;i:8;i:5;i:2;i:6;i:7;i:7;i:3;i:8;i:9;i:9;i:1;i:10;i:100;i:11;i:5;i:12;i:100;i:13;i:1;i:14;i:100;i:15;i:6;i:16;i:100;i:17;i:1;i:18;i:100;i:19;i:8;i:20;i:2;i:21;i:1;i:22;i:100;i:23;i:4;i:24;i:2;i:25;i:8;i:26;i:100;i:27;i:1;i:28;i:9;i:29;i:2;i:30;i:3;i:31;i:1;i:32;i:7;i:33;i:100;i:34;i:2;i:35;i:100;i:36;i:3;i:37;i:8;i:38;i:2;i:39;i:7;i:40;i:3;i:41;i:9;i:42;i:1;i:43;i:100;i:44;i:5;i:45;i:100;i:46;i:1;i:47;i:100;i:48;i:6;i:49;i:100;i:50;i:1;i:51;i:100;i:52;i:8;i:53;i:2;i:54;i:1;i:55;i:100;i:56;i:4;i:57;i:2;i:58;i:8;i:59;i:100;i:60;i:1;i:61;i:9;i:62;i:2;i:63;i:3;i:64;i:1;i:65;i:7;i:66;i:100;i:67;i:2;i:68;i:100;i:69;i:3;i:70;i:8;i:71;i:2;i:72;i:7;i:73;i:3;i:74;i:9;i:75;i:1;i:76;i:100;i:77;i:5;i:78;i:100;i:79;i:1;i:80;i:100;i:81;i:6;i:82;i:100;i:83;i:1;i:84;i:100;i:85;i:8;i:86;i:2;i:87;i:1;i:88;i:100;i:89;i:4;i:90;i:2;i:91;i:8;i:92;i:100;i:93;i:1;i:94;i:9;i:95;i:2;i:96;i:3;i:97;i:1;i:98;i:7;i:99;i:100;i:100;i:2;i:101;i:100;i:102;i:3;i:103;i:8;i:104;i:2;i:105;i:7;i:106;i:3;i:107;i:' . $fireReelsArr[4][0] . ';i:108;i:1;}}');
                                                    $transformReels = [
                                                        'b:0;', 
                                                        'b:0;', 
                                                        'b:0;', 
                                                        'b:0;', 
                                                        'b:0;'
                                                    ];
                                                    $currentFireWin = 0;
                                                    $fRateSections = [
                                                        0, 
                                                        5, 
                                                        20, 
                                                        30, 
                                                        40, 
                                                        47, 
                                                        50
                                                    ];
                                                    for( $fr = 0; $fr < 5; $fr++ ) 
                                                    {
                                                        if( $fireReelsArr[$fr][0] == 100 ) 
                                                        {
                                                            $trSym = rand(1, 15);
                                                            $fireReelsArr[$fr][0] = $trSym;
                                                            $transformReels[$fr] = 'i:' . $trSym . ';';
                                                        }
                                                        $fireScore += $fireReelsArr[$fr][0];
                                                    }
                                                    for( $fr = 1; $fr <= 6; $fr++ ) 
                                                    {
                                                        if( $fRateSections[$fr] <= $fireScore ) 
                                                        {
                                                            $currentFireWin = $slotSettings->FirepotPaytable[$frate][$fr] * $betline * $lines;
                                                        }
                                                    }
                                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackpotWin', $currentFireWin);
                                                    $lastSpinWin = $totalWin;
                                                    $totalWin += $currentFireWin;
                                                    $jackpotInfo = 'a:2:{s:7:"actStep";i:1;s:6:"result";a:9:{s:6:"chance";a:3:{i:0;b:1;i:1;b:1;i:2;b:1;}s:11:"initSymbols";a:5:{i:0;a:2:{i:0;i:' . $fireReelsInit[0] . ';i:1;i:' . $fireReelsInit[0] . ';}i:1;a:2:{i:0;i:' . $fireReelsInit[1] . ';i:1;i:' . $fireReelsInit[1] . ';}i:2;a:2:{i:0;i:' . $fireReelsInit[2] . ';i:1;i:' . $fireReelsInit[2] . ';}i:3;a:2:{i:0;i:' . $fireReelsInit[3] . ';i:1;i:' . $fireReelsInit[3] . ';}i:4;a:2:{i:0;i:' . $fireReelsInit[4] . ';i:1;i:' . $fireReelsInit[4] . ';}}' . $spinSymbolsJack . 's:14:"levelWinValues";a:6:{i:0;i:' . ($slotSettings->FirepotPaytable[$frate][1] * $allbet * 100) . ';i:1;i:' . ($slotSettings->FirepotPaytable[$frate][2] * $allbet * 100) . ';i:2;i:' . ($slotSettings->FirepotPaytable[$frate][3] * $allbet * 100) . ';i:3;i:' . ($slotSettings->FirepotPaytable[$frate][4] * $allbet * 100) . ';i:4;i:' . ($slotSettings->FirepotPaytable[$frate][5] * $allbet * 100) . ';i:5;i:' . ($slotSettings->FirepotPaytable[$frate][6] * $allbet * 100) . ';}s:14:"transformReels";a:5:{i:0;' . $transformReels[0] . 'i:1;' . $transformReels[1] . 'i:2;' . $transformReels[2] . 'i:3;' . $transformReels[3] . 'i:4;' . $transformReels[4] . '}s:13:"pointsPerReel";a:5:{i:0;i:' . $fireReelsArr[0][0] . ';i:1;i:' . $fireReelsArr[1][0] . ';i:2;i:' . $fireReelsArr[2][0] . ';i:3;i:' . $fireReelsArr[3][0] . ';i:4;i:' . $fireReelsArr[4][0] . ';}s:11:"startPoints";i:' . $startPoints . ';s:8:"totalWin";i:' . ($currentFireWin * 100) . ';s:11:"lastSpinWin";i:' . ($lastSpinWin * 100) . ';}}}';
                                                }
                                                else
                                                {
                                                    $caseArr = [
                                                        0, 
                                                        0, 
                                                        0, 
                                                        0, 
                                                        1, 
                                                        1
                                                    ];
                                                    shuffle($caseArr);
                                                    $jackpotInfo = 'a:2:{s:7:"actStep";i:1;s:6:"result";a:2:{s:6:"chance";a:3:{i:0;b:' . $caseArr[0] . ';i:1;b:' . $caseArr[1] . ';i:2;b:' . $caseArr[2] . ';}s:11:"lastSpinWin";i:' . ($totalWin * 100) . ';}}';
                                                }
                                            }
                                            else
                                            {
                                                $jackpotInfo = 'b:0;';
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
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $reportWin = $totalWin;
                                $actFreegame = 0;
                                $isFreeGame = 0;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $actFreegame = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1;
                                    $isFreeGame = 1;
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
                                    $actFreegame = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1;
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount);
                                    }
                                    $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                }
                                $reels = $reelsTmp;
                                if( isset($currentFireWin) ) 
                                {
                                    $totalWin -= $currentFireWin;
                                }
                                $winString = 'a:' . count($lineWins) . ':{' . implode('', $lineWins) . '}';
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BetStep":' . $slotSettings->GetGameData($slotSettings->slotId . 'BetStep') . ',"Lines":' . $slotSettings->GetGameData($slotSettings->slotId . 'Lines') . ',"CurrentBet":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet') . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet + $firebet, $lines, $reportWin, $postData['slotEvent']);
                                $reelStr = [];
                                $rsCount = [
                                    8, 
                                    12, 
                                    16, 
                                    20, 
                                    24
                                ];
                                for( $rlp = 0; $rlp < 5; $rlp++ ) 
                                {
                                    $reelStr[$rlp] = 'a:' . $rsCount[$rlp] . ':{';
                                    for( $rlp0 = 0; $rlp0 < ($rsCount[$rlp] - 4); $rlp0++ ) 
                                    {
                                        $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                        $reelStr[$rlp] .= ('i:' . $rlp0 . ';i:' . $rsym . ';');
                                    }
                                }
                                $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                $reelStr[0] .= ('i:4;i:' . $reels['reel1'][2] . ';i:5;i:' . $reels['reel1'][1] . ';i:6;i:' . $reels['reel1'][0] . ';i:7;i:' . $rsym . ';}');
                                $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                $reelStr[1] .= ('i:8;i:' . $reels['reel2'][2] . ';i:9;i:' . $reels['reel2'][1] . ';i:10;i:' . $reels['reel2'][0] . ';i:11;i:' . $rsym . ';}');
                                $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                $reelStr[2] .= ('i:12;i:' . $reels['reel3'][2] . ';i:13;i:' . $reels['reel3'][1] . ';i:14;i:' . $reels['reel3'][0] . ';i:15;i:' . $rsym . ';}');
                                $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                $reelStr[3] .= ('i:16;i:' . $reels['reel4'][2] . ';i:17;i:' . $reels['reel4'][1] . ';i:18;i:' . $reels['reel4'][0] . ';i:19;i:' . $rsym . ';}');
                                $rsym = $slotSettings->SymbolGame[rand(0, count($slotSettings->SymbolGame) - 1)];
                                $reelStr[4] .= ('i:20;i:' . $reels['reel5'][2] . ';i:21;i:' . $reels['reel5'][1] . ';i:22;i:' . $reels['reel5'][0] . ';i:23;i:' . $rsym . ';}');
                                $wwCnt = 0;
                                $wwStr = '';
                                for( $ww = 0; $ww < $lines; $ww++ ) 
                                {
                                    if( $cWins[$ww] > 0 ) 
                                    {
                                        $wwCnt++;
                                        $wwStr .= ('i:' . ($ww + 1) . ';i:' . ($cWins[$ww] * 100) . ';');
                                    }
                                }
                                if( $scattersCount >= 3 ) 
                                {
                                    $wwCnt++;
                                    $wwStr .= ('i:0;i:' . ($scattersWin * 100) . ';');
                                }
                                $wins_ = 'a:' . $wwCnt . ':{' . $wwStr . '}';
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                if( $postData['slotEvent'] == 'freespin' || $winType == 'bonus' ) 
                                {
                                    $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                                }
                                if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $isFreeGame = 0;
                                    $actFreegame = 0;
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                }
                                $symbolAnims = 'a:' . count($symbolAnimsArr) . ':{' . implode('', $symbolAnimsArr) . '}';
                                $teaserReelsArr = [
                                    's:1:"a";b:0;', 
                                    's:1:"b";b:0;', 
                                    's:1:"c";b:0;', 
                                    's:1:"d";b:0;', 
                                    's:1:"e";b:0;'
                                ];
                                $rid = [
                                    'a', 
                                    'b', 
                                    'c', 
                                    'd', 
                                    'e'
                                ];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == 8 ) 
                                        {
                                            $teaserReelsArr[$r - 1] = 's:1:"' . $rid[$r - 1] . '";a:2:{i:0;i:' . $scatter . ';i:1;i:' . ($p + 1) . ';}';
                                        }
                                    }
                                }
                                $teaserReels = 'a:5:{' . implode('', $teaserReelsArr) . '}';
                                $totalWin = $totalWin - $slotSettings->GetGameData($slotSettings->slotId . 'JackpotWin');
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:10:"spinresult";s:5:"value";a:49:{s:8:"winCoins";i:' . ($totalWin * 100) . ';s:8:"winLines";' . $winString . 's:13:"winLinesCount";i:' . $wwCnt . ';s:10:"winPerLine";' . $wins_ . 's:12:"winFGPerLine";b:0;s:13:"bonusWinLines";b:0;s:15:"bonusWinPerLine";b:0;s:11:"bonusSymbol";b:0;s:11:"fiveOfAKind";b:0;s:9:"freeGames";b:0;s:11:"actFreegame";b:0;s:12:"maxFreegames";b:0;s:11:"freegameWin";b:0;s:10:"isFreeGame";b:0;s:12:"wonFreegames";b:0;s:11:"playerCoins";i:' . $balanceInCents . ';s:9:"winFactor";d:' . ($totalWin / $allbet) . ';s:3:"x25";b:0;s:11:"symbolAnims";' . $symbolAnims . 's:9:"blockAnim";b:0;s:12:"blockAnimPos";b:0;s:15:"actWinFreegames";b:0;s:19:"freeGamesAnimations";b:0;s:13:"replaceSymbol";b:0;s:9:"multiData";b:0;s:10:"totalMulti";b:0;s:8:"maxiplay";b:0;s:18:"additionalWinAnims";a:1:{s:6:"mpLogo";b:0;}s:10:"multiLines";b:0;s:15:"specSymbolAnims";b:0;s:16:"extraTeaserReels";b:0;s:11:"stickyReels";b:0;s:12:"newStickyPos";b:0;s:19:"playRandomRetrigger";b:0;s:19:"wildSymbolPositions";b:0;s:12:"bonusSymbols";b:0;s:13:"isBookFeature";b:0;s:12:"actFreeRound";i:0;s:13:"maxFreeRounds";i:0;s:12:"freeRoundWin";i:0;s:11:"teaserReels";' . $teaserReels . 's:13:"amountSymbols";a:5:{s:1:"a";i:6;s:1:"b";i:9;s:1:"c";i:12;s:1:"d";i:15;s:1:"e";i:18;}s:26:"specFeatureSymbolPositions";' . $specFeatureSymbolPositions . 's:17:"reel_a_symboldata";' . $reelStr[0] . 's:17:"reel_b_symboldata";' . $reelStr[1] . 's:17:"reel_c_symboldata";' . $reelStr[2] . 's:17:"reel_d_symboldata";' . $reelStr[3] . 's:17:"reel_e_symboldata";' . $reelStr[4] . 's:11:"jackpotGame";' . $jackpotInfo . '}}}}}';
                                break;
                            case 'initGambleCard':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                if( !$slotSettings->HasGameData($slotSettings->slotId . 'CardsHistory') ) 
                                {
                                    $hst = [
                                        's:4:"club";', 
                                        's:4:"club";', 
                                        's:4:"club";', 
                                        's:4:"club";', 
                                        's:4:"club";', 
                                        's:5:"heart";', 
                                        's:5:"heart";', 
                                        's:5:"heart";', 
                                        's:5:"heart";', 
                                        's:5:"heart";'
                                    ];
                                    shuffle($hst);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CardsHistory', $hst);
                                }
                                $cHist = $slotSettings->GetGameData($slotSettings->slotId . 'CardsHistory');
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:14:"initGambleCard";s:5:"value";a:4:{s:3:"bet";i:' . ($gambleAmount * 100) . ';s:12:"collectedWin";i:0;s:7:"history";a:5:{i:0;' . $cHist[0] . 'i:1;' . $cHist[1] . 'i:2;' . $cHist[2] . 'i:3;' . $cHist[3] . 'i:4;' . $cHist[4] . '}s:13:"pulseInterval";i:545;}}}}';
                                break;
                            case 'initGambleLadder':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $bet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $LadderStep = -1;
                                $LadderStep0 = 0;
                                $LadderStep1 = 0;
                                $ladder = $slotSettings->gambleLadder;
                                $ladderStr = '';
                                for( $i = 0; $i < count($ladder); $i++ ) 
                                {
                                    if( $ladder[$i] < 0 ) 
                                    {
                                        $ladderStr .= ('i:' . $i . ';b:0;');
                                    }
                                    else
                                    {
                                        $ladderStr .= ('i:' . $i . ';i:' . ($bet * $ladder[$i] * 100) . ';');
                                        if( $bet * $ladder[$i] < $gambleAmount && $ladder[$i] >= 0 ) 
                                        {
                                            $LadderStep = -1;
                                            $LadderStep1 = $i + 1;
                                        }
                                        else if( $gambleAmount == ($bet * $ladder[$i]) && $ladder[$i] >= 0 ) 
                                        {
                                            $LadderStep = $i;
                                        }
                                    }
                                }
                                if( $LadderStep <= 4 && $LadderStep >= 0 ) 
                                {
                                    $LadderStep0 = 0;
                                    $LadderStep1 = $LadderStep + 1;
                                }
                                else if( $LadderStep <= 10 && $LadderStep >= 0 ) 
                                {
                                    if( $LadderStep == 6 ) 
                                    {
                                        $LadderStep0 = 4;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                    else
                                    {
                                        $LadderStep0 = 6;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                }
                                else if( $LadderStep <= 14 && $LadderStep >= 0 ) 
                                {
                                    if( $LadderStep == 12 ) 
                                    {
                                        $LadderStep0 = 10;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                    else
                                    {
                                        $LadderStep0 = 6;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                }
                                if( $LadderStep1 <= 4 && $LadderStep < 0 ) 
                                {
                                    $LadderStep0 = $LadderStep1 - 1;
                                }
                                else if( $LadderStep1 <= 10 && $LadderStep < 0 ) 
                                {
                                    if( $LadderStep1 == 6 ) 
                                    {
                                        $LadderStep0 = 4;
                                    }
                                    else
                                    {
                                        $LadderStep0 = $LadderStep1 - 1;
                                    }
                                }
                                else if( $LadderStep1 <= 14 && $LadderStep < 0 ) 
                                {
                                    if( $LadderStep1 == 12 ) 
                                    {
                                        $LadderStep0 = 10;
                                    }
                                    else
                                    {
                                        $LadderStep0 = $LadderStep1 - 1;
                                    }
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep', $LadderStep);
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep0', $LadderStep0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep1', $LadderStep1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'СollectedWin', 0);
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:16:"initGambleLadder";s:5:"value";a:6:{s:3:"bet";i:' . ($gambleAmount * 100) . ';s:13:"allStepValues";a:15:{' . $ladderStr . '}s:10:"multiplier";i:1;s:11:"currentStep";i:' . $LadderStep . ';s:7:"winStep";i:' . $LadderStep1 . ';s:8:"loseStep";i:' . $LadderStep0 . ';}}}}';
                                break;
                            case 'splitGambleWin':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $bet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $LadderStep = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep');
                                $LadderStep0 = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep0');
                                $LadderStep1 = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep1');
                                $isLadder = $postData['inLadderMode'];
                                if( $isLadder ) 
                                {
                                    $ladder = $slotSettings->gambleLadder;
                                    $LadderStep--;
                                    if( $LadderStep == 5 || $LadderStep == 11 ) 
                                    {
                                        $LadderStep--;
                                    }
                                    if( $LadderStep == 6 ) 
                                    {
                                        $LadderStep0 = 0;
                                        $LadderStep1 = 7;
                                    }
                                    else if( $LadderStep == 12 ) 
                                    {
                                        $LadderStep0 = 6;
                                        $LadderStep1 = 13;
                                    }
                                    else
                                    {
                                        $LadderStep0 = $LadderStep - 1;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                    $splitWinTmp = $ladder[$LadderStep];
                                    $splitWin = $gambleAmount - ($bet * $splitWinTmp);
                                    $gambleAmount = $gambleAmount - $splitWin;
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep', $LadderStep);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep0', $LadderStep0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep1', $LadderStep1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $gambleAmount);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'СollectedWin', $slotSettings->GetGameData($slotSettings->slotId . 'СollectedWin') + $splitWin);
                                    $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:14:"splitGambleWin";s:5:"value";a:6:{s:3:"bet";i:' . ($gambleAmount * 100) . ';s:12:"collectedWin";i:' . ($slotSettings->GetGameData($slotSettings->slotId . 'СollectedWin') * 100) . ';s:10:"multiplier";i:1;s:11:"currentStep";i:' . $LadderStep . ';s:7:"winStep";i:' . $LadderStep1 . ';s:8:"loseStep";i:' . $LadderStep0 . ';}}}}';
                                }
                                else
                                {
                                    if( $gambleAmount <= 0.01 ) 
                                    {
                                        exit();
                                    }
                                    $splitWin = sprintf('%01.2f', $gambleAmount / 2);
                                    $gambleAmount = sprintf('%01.2f', $gambleAmount - $splitWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $gambleAmount);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'СollectedWin', $slotSettings->GetGameData($slotSettings->slotId . 'СollectedWin') + $splitWin);
                                    $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:14:"splitGambleWin";s:5:"value";a:2:{s:3:"bet";i:' . ($gambleAmount * 100) . ';s:12:"collectedWin";i:' . ($slotSettings->GetGameData($slotSettings->slotId . 'СollectedWin') * 100) . ';}}}}';
                                }
                                break;
                            case 'takeGambleWin':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:13:"takeGambleWin";s:5:"value";a:2:{s:9:"gambleWin";i:' . ($gambleAmount * 100) . ';s:11:"playerCoins";i:' . $balanceInCents . ';}}}}';
                                break;
                            case 'gambleLadder':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $bet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $LadderStep = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep');
                                $LadderStep0 = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep0');
                                $LadderStep1 = $slotSettings->GetGameData($slotSettings->slotId . 'LadderStep1');
                                $dbet = $gambleAmount;
                                if( $gambleAmount > 0 ) 
                                {
                                    $slotSettings->SetBalance(-1 * $gambleAmount);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleAmount);
                                }
                                else
                                {
                                    exit();
                                }
                                $doubleWin = rand(1, 2);
                                if( $slotSettings->MaxWin < ($gambleAmount * $slotSettings->CurrentDenom) ) 
                                {
                                    $doubleWin = 0;
                                }
                                $uCard = '';
                                $ladder = $slotSettings->gambleLadder;
                                $casBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                $LadderStepTmp = $LadderStep1;
                                if( $LadderStepTmp == 5 || $LadderStepTmp == 11 ) 
                                {
                                    $LadderStepTmp += 2;
                                }
                                if( $casBank < ($ladder[$LadderStepTmp] * $bet) ) 
                                {
                                    $doubleWin = 0;
                                }
                                if( $doubleWin == 1 ) 
                                {
                                    $LadderStep = $LadderStepTmp;
                                    $LadderStep0 = $LadderStep - 1;
                                    $LadderStep1 = $LadderStep + 1;
                                }
                                else
                                {
                                    $LadderStep = $LadderStep0;
                                    if( $LadderStep == 6 ) 
                                    {
                                        $LadderStep0 = 0;
                                        $LadderStep1 = 7;
                                    }
                                    else if( $LadderStep == 12 ) 
                                    {
                                        $LadderStep0 = 6;
                                        $LadderStep1 = 13;
                                    }
                                    else
                                    {
                                        $LadderStep0 = $LadderStep - 1;
                                        $LadderStep1 = $LadderStep + 1;
                                    }
                                }
                                $gambleAmount = $ladder[$LadderStep] * $bet;
                                if( $gambleAmount > 0 ) 
                                {
                                    $slotSettings->SetBalance($gambleAmount);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $gambleAmount);
                                }
                                else
                                {
                                    $gambleAmount = -1 * $gambleAmount;
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep', $LadderStep);
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep0', $LadderStep0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'LadderStep1', $LadderStep1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $gambleAmount);
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:18:"gambleLadderResult";s:5:"value";a:9:{s:10:"multiplier";i:1;s:11:"currentStep";i:' . $LadderStep . ';s:7:"winStep";i:' . $LadderStep1 . ';s:8:"loseStep";i:' . $LadderStep0 . ';s:3:"bet";i:' . ($gambleAmount * 100) . ';s:12:"collectedWin";i:' . ($slotSettings->GetGameData($slotSettings->slotId . 'СollectedWin') * 100) . ';s:3:"win";b:0;s:14:"matchedDrawOut";a:2:{i:5;b:0;i:11;b:0;}s:9:"actGamble";i:0;}}}}';
                                $response_log = '{"responseEvent":"gambleResult","serverResponse":{"totalWin":' . $gambleAmount . '}}';
                                $slotSettings->SaveLogReport($response_log, $dbet, 1, $gambleAmount, 'slotGamble2');
                                break;
                            case 'gambleCard':
                                $gambleAmount = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $dbet = $gambleAmount;
                                $gambleChoice = $postData['choice'];
                                $doubleWin = rand(1, 2);
                                if( $slotSettings->MaxWin < ($gambleAmount * $slotSettings->CurrentDenom) ) 
                                {
                                    $doubleWin = 0;
                                }
                                $uCard = '';
                                $casBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                if( $casBank < ($gambleAmount * 2) ) 
                                {
                                    $doubleWin = 0;
                                }
                                if( $doubleWin == 1 ) 
                                {
                                    $gambleAmountStr = $gambleAmount * 2;
                                    if( $gambleChoice == 'red' ) 
                                    {
                                        $uCard = 's:5:"heart";';
                                    }
                                    else
                                    {
                                        $uCard = 's:4:"club";';
                                    }
                                }
                                else
                                {
                                    if( $gambleChoice == 'black' ) 
                                    {
                                        $uCard = 's:5:"heart";';
                                    }
                                    else
                                    {
                                        $uCard = 's:4:"club";';
                                    }
                                    $gambleAmountStr = 0;
                                    $gambleAmount = -1 * $gambleAmount;
                                }
                                $cHist = $slotSettings->GetGameData($slotSettings->slotId . 'CardsHistory');
                                array_pop($cHist);
                                array_unshift($cHist, $uCard);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CardsHistory', $cHist);
                                if( $gambleAmount > 0 ) 
                                {
                                    $slotSettings->SetBalance($gambleAmount);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $gambleAmount);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $gambleAmount * 2);
                                }
                                else
                                {
                                    $slotSettings->SetBalance($gambleAmount);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleAmount * -1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                }
                                $response_log = '{"responseEvent":"gambleResult","serverResponse":{"totalWin":' . $gambleAmount . '}}';
                                $slotSettings->SaveLogReport($response_log, $dbet, 1, $gambleAmount, 'slotGamble');
                                $result_tmp[0] = 'INFO$$1$$a:1:{s:7:"display";a:1:{i:0;a:2:{s:4:"type";s:16:"gambleCardResult";s:5:"value";a:6:{s:4:"card";' . $uCard . 's:3:"bet";i:' . ($gambleAmountStr * 100) . ';s:12:"collectedWin";i:0;s:9:"actGamble";i:1;s:7:"history";a:5:{i:0;' . $cHist[0] . 'i:1;' . $cHist[1] . 'i:2;' . $cHist[2] . 'i:3;' . $cHist[3] . 'i:4;' . $cHist[4] . '}s:10:"quitGamble";b:0;}}}}';
                                break;
                        }
                        if( !isset($result_tmp[0]) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"Invalid request state"}';
                            exit( $response );
                        }
                        $response = $result_tmp[0];
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
