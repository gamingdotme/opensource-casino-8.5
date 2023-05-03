<?php 
namespace VanguardLTE\Games\BuffaloAT
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
                        $response = '';
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
                        $postData = [];
                        if( !isset($_POST['cmd']) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid game command"}';
                            exit( $response );
                        }
                        $postData['slotEvent'] = $_POST['cmd'];
                        if( $postData['slotEvent'] == 'gameSpin' ) 
                        {
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                                $postData['slotLines'] = $slotSettings->GetGameData($slotSettings->slotId . 'slotLines');
                                $postData['slotBet'] = $slotSettings->GetGameData($slotSettings->slotId . 'slotBet');
                            }
                            else
                            {
                                $postData['slotEvent'] = 'bet';
                                $postData['slotLines'] = $_POST['lines'];
                                $postData['slotBet'] = $_POST['bet'] / 100;
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotLines', $postData['slotLines']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotBet', $postData['slotBet']);
                            }
                        }
                        if( $postData['slotEvent'] == 'gameGamble' ) 
                        {
                            $postData['slotEvent'] = 'slotGamble';
                            $postData['gambleChoice'] = $_POST['color'];
                        }
                        if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            if( !in_array($postData['slotLines'], $slotSettings->gameLine) || !in_array($postData['slotBet'], $slotSettings->Bet) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['slotLines'] * $postData['slotBet']) && $postData['slotEvent'] == 'bet' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                                exit( $response );
                            }
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' && ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 || $slotSettings->GetBalance() < $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin')) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                            $slotSettings->InternalError($response . ' -- TotalWin = ' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . ' -- Balance = ' . $slotSettings->GetBalance());
                            exit( $response );
                        }
                        if( $postData['slotEvent'] == 'gameInit' ) 
                        {
                            $lastEvent = $slotSettings->GetHistory();
                            if( !$slotSettings->HasGameData($slotSettings->slotId . 'HistoryCards') ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'HistoryCards', []);
                            }
                            $freeStateStr = '';
                            $reelStr = 'null';
                            $restore = 'false';
                            if( $lastEvent != 'NULL' ) 
                            {
                                if( isset($lastEvent->serverResponse->expSymbol) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'ExpSymbol', $lastEvent->serverResponse->expSymbol);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotLines', $lastEvent->serverResponse->slotLines);
                                $slotSettings->SetGameData($slotSettings->slotId . 'slotBet', $lastEvent->serverResponse->slotBet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Mpl', $lastEvent->serverResponse->Mpl);
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $reelStrArr = [];
                                $reelStr = '';
                                $tstr = [];
                                for( $i = 1; $i <= 5; $i++ ) 
                                {
                                    $tstr0 = [];
                                    $ps = 0;
                                    for( $p = 3; $p >= 0; $p-- ) 
                                    {
                                        if( isset($reels->{'reel' . $i}[$p]) && $reels->{'reel' . $i}[$p] != '' ) 
                                        {
                                            $tstr0[] = '"' . ($ps + 1) . '":"' . $reels->{'reel' . $i}[$p] . '"';
                                            $ps++;
                                        }
                                    }
                                    $reelStrArr[] = '"' . $i . '":{' . implode(',', $tstr0) . '}';
                                }
                                $reelStr = '{' . implode(',', $reelStrArr) . '}';
                                if( $lastEvent->serverResponse->currentFreeGames < $lastEvent->serverResponse->totalFreeGames ) 
                                {
                                    $restore = 'true';
                                    $freeStateStr = '"id":"54594109_20200214110301","current":' . $lastEvent->serverResponse->currentFreeGames . ',"add":0,"total":' . $lastEvent->serverResponse->totalFreeGames . ',"totalWin":' . ($lastEvent->serverResponse->totalWin * 100) . '';
                                }
                            }
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $gameBets = $slotSettings->Bet;
                            foreach( $gameBets as &$bt ) 
                            {
                                $bt = $bt * 100;
                            }
                            $response = '{"status":"success","microtime":0.0077991485595703,"dateTime":"2020-02-13 13:16:03","error":"","content":{"cmd":"gameInit","balance":' . $balanceInCents . ',"session":"54594109_4133a64673d3883d42cd003ee905ba3e","betInfo":{"denomination":0.01,"bet":' . $gameBets[0] . ',"lines":40},"betSettings":{"denomination":[0.01],"bets":[' . implode(',', $gameBets) . '],"lines":[1,5,10,20,40]},"symbols":' . $reelStr . ',"reels":{"base":{"1":["' . implode('","', $slotSettings->reelStrip1) . '"],"2":["' . implode('","', $slotSettings->reelStrip2) . '"],"3":["' . implode('","', $slotSettings->reelStrip3) . '"],"4":["' . implode('","', $slotSettings->reelStrip4) . '"],"5":["' . implode('","', $slotSettings->reelStrip5) . '"]},"free":{"1":["' . implode('","', $slotSettings->reelStripBonus1) . '"],"2":["' . implode('","', $slotSettings->reelStripBonus2) . '"],"3":["' . implode('","', $slotSettings->reelStripBonus3) . '"],"4":["' . implode('","', $slotSettings->reelStripBonus4) . '"],"5":["' . implode('","', $slotSettings->reelStripBonus5) . '"]}},"exitUrl":"\/","pingInterval":60000,"restore":' . $restore . ',"freeSpin":{' . $freeStateStr . '},"hash":"da0a5044aebc413b6da3a8e42dea0246"}}';
                        }
                        else if( $postData['slotEvent'] == 'gameRefreshBalance' ) 
                        {
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $response = '{"status":"success","microtime":0.0065572261810303,"dateTime":"2020-02-15 12:46:27","error":"","content":{"cmd":"gameRefreshBalance","session":"54990161_9709e13bca7763b46396cda1868d2e7e","balance":' . $balanceInCents . ',"hash":"8d9f26101b61f471ac401c22d6b8d68c"}}';
                        }
                        else if( $postData['slotEvent'] == 'gamePing' ) 
                        {
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $response = '{"status":"success","microtime":0.0031919479370117,"dateTime":"2020-02-14 11:51:01","error":null,"content":{"cmd":"gamePing","session":"54594109_4133a64673d3883d42cd003ee905ba3e","balance":' . $balanceInCents . ',"hash":"fda108a357d692381d7ca82a1bea67b1"}}';
                        }
                        else if( $postData['slotEvent'] == 'sessionInfo' ) 
                        {
                            $response = '{"status":"success","microtime":0.0026731491088867,"dateTime":"2020-02-13 13:23:32","error":"","content":{"cmd":"sessionInfo","serverMathematics":"\/game\/BuffaloAT\/server","serverResources":"","sessionId":"54594109_4133a64673d3883d42cd003ee905ba3e","exitUlt":"\/","exitUrl":"\/","id":"341","name":"GameName","currency":"ALL","language":"en","type":"aristocrat","systemName":"buffalo","version":"2","mobile":"1"}}';
                        }
                        else if( $postData['slotEvent'] == 'gameTakeWin' ) 
                        {
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $response = '{"status":"success","microtime":0.033211946487427,"dateTime":"2020-02-14 10:09:04","error":null,"content":{"cmd":"gameTakeWin","session":"54594109_4133a64673d3883d42cd003ee905ba3e","balance":' . $balanceInCents . ',"actionId":"54594109_74_659","hash":"a12f5889add8917d6e705751e1d9a953"}}';
                        }
                        else if( $postData['slotEvent'] == 'gamePick' ) 
                        {
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            if( $_POST['pick'] == 0 ) 
                            {
                                $slotSettings->SetGameData('BuffaloATMpl', 10);
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BuffaloATFreeGames', 5);
                            }
                            else if( $_POST['pick'] == 1 ) 
                            {
                                $slotSettings->SetGameData('BuffaloATMpl', 5);
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BuffaloATFreeGames', 10);
                            }
                            else if( $_POST['pick'] == 2 ) 
                            {
                                $slotSettings->SetGameData('BuffaloATMpl', 3);
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BuffaloATFreeGames', 15);
                            }
                            else if( $_POST['pick'] == 3 ) 
                            {
                                $slotSettings->SetGameData('BuffaloATMpl', 2);
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BuffaloATFreeGames', 20);
                            }
                            $response = '{"status":"success","microtime":0.0093810558319092,"dateTime":"2020-02-15 14:26:21","error":"","content":{"session":"54990161_9709e13bca7763b46396cda1868d2e7e","cmd":"gamePick","balance":' . $balanceInCents . ',"win":0,"symbols":{"1":{"1":"symbol_6","2":"symbol_9","3":"symbol_13"},"2":{"1":"symbol_10","2":"symbol_5","3":"symbol_8"},"3":{"1":"symbol_10","2":"symbol_13","3":"symbol_11"},"4":{"1":"symbol_13","2":"symbol_10","3":"symbol_6"},"5":{"1":"symbol_3","2":"symbol_8","3":"symbol_1"}},"winLines":[],"freeSpin":{"id":"54990161_20200215132548","current":0,"add":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"total":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"totalWin":' . ($slotSettings->GetGameData('BuffaloATBonusWin') * 100) . ',"multiplayer":' . $slotSettings->GetGameData('BuffaloATMpl') . ',"pick":' . $_POST['pick'] . '},"actionId":"54990161_10_60","hash":"1ace145628d930d28bdf38789a28a9d7"}}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('BuffaloATTotalWin');
                            $gambleWin = 0;
                            $statBet = $totalWin;
                            if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                            {
                                $isGambleWin = 0;
                            }
                            if( $postData['gambleChoice'] == 'red' || $postData['gambleChoice'] == 'black' ) 
                            {
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                $actionNext = 'spin';
                                if( $isGambleWin == 1 ) 
                                {
                                    $actionNext = 'gamble\/takeWin';
                                    $gambleState = 'win';
                                    $gambleWin = $totalWin;
                                    $totalWin = $totalWin * 2;
                                    if( $postData['gambleChoice'] == 'red' ) 
                                    {
                                        $tmpCards = [
                                            'diamond', 
                                            'heart'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            'club', 
                                            'spade'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                                else
                                {
                                    $gambleState = 'lose';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    if( $postData['gambleChoice'] == 'red' ) 
                                    {
                                        $tmpCards = [
                                            'club', 
                                            'spade'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            'diamond', 
                                            'heart'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                            }
                            else
                            {
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 4) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                $actionNext = 'spin';
                                if( $isGambleWin == 1 ) 
                                {
                                    $actionNext = 'gamble\/takeWin';
                                    $gambleState = 'win';
                                    $gambleWin = $totalWin;
                                    $totalWin = $totalWin * 4;
                                    $dealerCard = $postData['gambleChoice'];
                                }
                                else
                                {
                                    $gambleState = 'lose';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    $tmpCards = [
                                        'club', 
                                        'spade', 
                                        'diamond', 
                                        'heart'
                                    ];
                                    shuffle($tmpCards);
                                    for( $i = 0; $i < 4; $i++ ) 
                                    {
                                        if( $postData['gambleChoice'] != $tmpCards[$i] ) 
                                        {
                                            $dealerCard = $tmpCards[$i];
                                        }
                                    }
                                }
                            }
                            $slotSettings->SetGameData('BuffaloATTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response_log = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $response = '{"status":"success","microtime":0.0097517967224121,"dateTime":"2020-02-14 09:37:51","error":null,"content":{"cmd":"gameGamble","session":"54594109_4133a64673d3883d42cd003ee905ba3e","balance":' . $balanceInCents . ',"card":"' . $dealerCard . '","win":' . ($totalWin * 100) . ',"actionId":"54594109_68_593","actionNext":"' . $actionNext . '","lastCard":["spade","diamond","diamond"],"hash":"c9f3dbf54134e682dead5ec1f89d48d5"}}';
                            $slotSettings->SaveLogReport($response_log, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' ) 
                        {
                            $linesId = [];
                            $linesId[0] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[2] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[4] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[5] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[6] = [
                                1, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[7] = [
                                1, 
                                1, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[8] = [
                                1, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[9] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[10] = [
                                1, 
                                1, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[12] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[13] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[16] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[17] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[18] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[19] = [
                                2, 
                                2, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[20] = [
                                2, 
                                2, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[21] = [
                                2, 
                                3, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[22] = [
                                2, 
                                1, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[23] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[24] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[25] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[26] = [
                                3, 
                                3, 
                                4, 
                                3, 
                                3
                            ];
                            $linesId[27] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[28] = [
                                3, 
                                4, 
                                4, 
                                4, 
                                3
                            ];
                            $linesId[29] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[30] = [
                                3, 
                                4, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[31] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[32] = [
                                3, 
                                3, 
                                3, 
                                4, 
                                3
                            ];
                            $linesId[33] = [
                                3, 
                                3, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[34] = [
                                3, 
                                4, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[35] = [
                                3, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[36] = [
                                3, 
                                4, 
                                2, 
                                4, 
                                3
                            ];
                            $linesId[37] = [
                                3, 
                                2, 
                                4, 
                                2, 
                                3
                            ];
                            $linesId[38] = [
                                4, 
                                4, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[39] = [
                                4, 
                                4, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[40] = [
                                4, 
                                3, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[41] = [
                                4, 
                                3, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[42] = [
                                4, 
                                3, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[43] = [
                                4, 
                                4, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[44] = [
                                4, 
                                3, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[45] = [
                                4, 
                                4, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[46] = [
                                4, 
                                3, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[47] = [
                                4, 
                                4, 
                                2, 
                                4, 
                                4
                            ];
                            $linesId[48] = [
                                4, 
                                4, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[49] = [
                                4, 
                                3, 
                                2, 
                                4, 
                                4
                            ];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = ($postData['slotBet'] * $postData['slotLines']) / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['slotBet'] * $postData['slotLines']);
                                $slotSettings->SetBalance(-1 * ($postData['slotBet'] * $postData['slotLines']), $postData['slotEvent']);
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('BuffaloATBonusWin', 0);
                                $slotSettings->SetGameData('BuffaloATFreeGames', 0);
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('BuffaloATTotalWin', 0);
                                $slotSettings->SetGameData('BuffaloATFreeBalance', 0);
                                $slotSettings->SetGameData('BuffaloATMpl', 1);
                            }
                            else
                            {
                                $slotSettings->SetGameData('BuffaloATCurrentFreeGame', $slotSettings->GetGameData('BuffaloATCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->GetGameData('BuffaloATMpl');
                            }
                            $Balance = $slotSettings->GetBalance();
                            if( isset($slotSettings->Jackpots['jackPay']) ) 
                            {
                                $Balance = $Balance - ($slotSettings->Jackpots['jackPay'] * $slotSettings->CurrentDenom);
                            }
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
                                    0
                                ];
                                $wild = 'symbol_13';
                                $scatter = 'symbol_12';
                                $linesId0 = [];
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $tmpStringWin = '';
                                $wildsMplArr = [];
                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                {
                                    $csym = $slotSettings->SymbolGame[$j];
                                    if( $csym == $scatter || !isset($slotSettings->Paytable[$csym]) ) 
                                    {
                                    }
                                    else
                                    {
                                        $waysCountArr = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $waysCount = 1;
                                        $wayPos = [];
                                        $waysLimit = [];
                                        $waysLimit[1] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2], 
                                            [2], 
                                            [2], 
                                            [2]
                                        ];
                                        $waysLimit[5] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2], 
                                            [2], 
                                            [2]
                                        ];
                                        $waysLimit[10] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2], 
                                            [2]
                                        ];
                                        $waysLimit[20] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2]
                                        ];
                                        $waysLimit[40] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ]
                                        ];
                                        $symPosConvert = [
                                            4, 
                                            3, 
                                            2, 
                                            1
                                        ];
                                        for( $rws = 1; $rws <= 5; $rws++ ) 
                                        {
                                            $curWays = $waysLimit[$postData['slotLines']][$rws - 1];
                                            $wildsMpl = 0;
                                            foreach( $curWays as $cws ) 
                                            {
                                                if( $postData['slotEvent'] == 'freespin' && $reels['reel' . $rws][$cws] == $wild ) 
                                                {
                                                    $wildsMpl = rand(2, 3);
                                                    $wildsMplArr[] = '[' . $rws . ', ' . $symPosConvert[$cws] . ', ' . $wildsMpl . ']';
                                                }
                                                if( $reels['reel' . $rws][$cws] == $csym || $reels['reel' . $rws][$cws] == $wild ) 
                                                {
                                                    $waysCountArr[$rws]++;
                                                    $wayPos[] = '[' . $rws . ',' . $symPosConvert[$cws] . ']';
                                                }
                                            }
                                            if( $waysCountArr[$rws] <= 0 ) 
                                            {
                                                break;
                                            }
                                            $waysCount = $waysCountArr[$rws] * $waysCount;
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * ($waysCount + $wildsMpl) * $bonusMpl;
                                            $tmpStringWin = '{"line":null,"symbol":"' . $csym . '","count":2,"side":"left","elements":[' . implode(',', $wayPos) . '],"xWin":' . ($waysCount + $wildsMpl) . ',"win":' . ($cWins[$j] * 100) . '}';
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * ($waysCount + $wildsMpl) * $bonusMpl;
                                            $tmpStringWin = '{"line":null,"symbol":"' . $csym . '","count":3,"side":"left","elements":[' . implode(',', $wayPos) . '],"xWin":' . ($waysCount + $wildsMpl) . ',"win":' . ($cWins[$j] * 100) . '}';
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * ($waysCount + $wildsMpl) * $bonusMpl;
                                            $tmpStringWin = '{"line":null,"symbol":"' . $csym . '","count":4,"side":"left","elements":[' . implode(',', $wayPos) . '],"xWin":' . ($waysCount + $wildsMpl) . ',"win":' . ($cWins[$j] * 100) . '}';
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 && $waysCountArr[5] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * ($waysCount + $wildsMpl) * $bonusMpl;
                                            $tmpStringWin = '{"line":null,"symbol":"' . $csym . '","count":5,"side":"left","elements":[' . implode(',', $wayPos) . '],"xWin":' . ($waysCount + $wildsMpl) . ',"win":' . ($cWins[$j] * 100) . '}';
                                        }
                                        if( $cWins[$j] > 0 && $tmpStringWin != '' ) 
                                        {
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$j];
                                        }
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersPos = [];
                                $scattersCount = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            if( $p == 0 ) 
                                            {
                                                $pr = 4;
                                            }
                                            else if( $p == 1 ) 
                                            {
                                                $pr = 3;
                                            }
                                            else if( $p == 2 ) 
                                            {
                                                $pr = 2;
                                            }
                                            else if( $p == 3 ) 
                                            {
                                                $pr = 1;
                                            }
                                            $scattersPos[] = '[' . $r . ',"' . $pr . '"]';
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable[$scatter][$scattersCount] * $postData['slotBet'] * $postData['slotLines'];
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr = '{"line":"scatter","symbol":"' . $scatter . '","count":' . $scattersCount . ',"elements":[' . implode(',', $scattersPos) . '],"xWin":' . $bonusMpl . ',"freeSpinAdd":' . $slotSettings->slotFreeCount[$scattersCount] . ',"win":' . ($scattersWin * 100) . '}';
                                    array_push($lineWins, $scattersStr);
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr = '{"line":"scatter","symbol":"' . $scatter . '","count":' . $scattersCount . ',"elements":[' . implode(',', $scattersPos) . '],"xWin":' . $bonusMpl . ',"freeSpinAdd":0,"win":' . ($scattersWin * 100) . '}';
                                    array_push($lineWins, $scattersStr);
                                }
                                else
                                {
                                    $scattersStr = '';
                                }
                                $totalWin += $scattersWin;
                                if( $i > 1500 ) 
                                {
                                    $winType = 'none';
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
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['slotBet'] * $postData['slotLines']) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        if( $i > 2500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                            exit( $response );
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
                                $slotSettings->SetBalance($totalWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            $reportWin = $totalWin;
                            $freeStateStr = '';
                            $reelStr = 'null';
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('BuffaloATBonusWin', $slotSettings->GetGameData('BuffaloATBonusWin') + $totalWin);
                                $slotSettings->SetGameData('BuffaloATTotalWin', $slotSettings->GetGameData('BuffaloATTotalWin') + $totalWin);
                                $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('BuffaloATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('BuffaloATMpl') . ',"xWin":[' . implode(',', $wildsMplArr) . '],"add":0,"total":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"totalWin":' . ($slotSettings->GetGameData('BuffaloATBonusWin') * 100) . '';
                            }
                            else
                            {
                                $slotSettings->SetGameData('BuffaloATTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('BuffaloATFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('BuffaloATFreeGames', $slotSettings->GetGameData('BuffaloATFreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                    $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('BuffaloATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('BuffaloATMpl') . ',"add":' . $slotSettings->slotFreeCount[$scattersCount] . ',"total":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"xWin":[' . implode(',', $wildsMplArr) . '],"totalWin":' . ($slotSettings->GetGameData('BuffaloATBonusWin') * 100) . '';
                                }
                                else
                                {
                                    $slotSettings->SetGameData('BuffaloATFreeBalance', $Balance);
                                    $slotSettings->SetGameData('BuffaloATBonusWin', $totalWin);
                                    $slotSettings->SetGameData('BuffaloATFreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                    $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('BuffaloATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('BuffaloATMpl') . ',"add":' . $slotSettings->slotFreeCount[$scattersCount] . ',"total":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"xWin":[' . implode(',', $wildsMplArr) . '],"totalWin":' . ($slotSettings->GetGameData('BuffaloATBonusWin') * 100) . '';
                                }
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('BuffaloATFreeGames') . ',"Mpl":' . $slotSettings->GetGameData('BuffaloATMpl') . ',"currentFreeGames":' . $slotSettings->GetGameData('BuffaloATCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":{},"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $reelStrArr = [];
                            for( $i = 1; $i <= 5; $i++ ) 
                            {
                                $reelStrArr[] = '"' . $i . '":{"1":"' . $reels['reel' . $i][3] . '","2":"' . $reels['reel' . $i][2] . '","3":"' . $reels['reel' . $i][1] . '","4":"' . $reels['reel' . $i][0] . '"}';
                            }
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $response = '{"status":"success","microtime":0.012106895446777,"dateTime":"2020-02-13 15:56:37","error":"","content":{"session":"54594109_4133a64673d3883d42cd003ee905ba3e","cmd":"gameSpin","balance":' . $balanceInCents . ',"win":' . ($totalWin * 100) . ',"symbols":{' . implode(',', $reelStrArr) . '},"winLines":[' . $winString . '],"freeSpin":{' . $freeStateStr . '},"actionId":"54594109_0_315","hash":"3f8366f9f05bada378a5a4a37034e744"}}';
                            $slotSettings->SaveLogReport($response_log, $postData['slotBet'], $postData['slotLines'], $reportWin, $postData['slotEvent']);
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
