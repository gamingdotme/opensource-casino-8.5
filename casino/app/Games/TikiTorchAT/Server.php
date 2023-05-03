<?php 
namespace VanguardLTE\Games\TikiTorchAT
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
                                $freeStateStr = '"id":"54990161_20200215162832","current":' . $lastEvent->serverResponse->currentFreeGames . ',"add":0,"total":' . $lastEvent->serverResponse->totalFreeGames . ',"totalWin":' . ($lastEvent->serverResponse->totalWin * 100) . ',"multiplayer":' . $lastEvent->serverResponse->Mpl . '';
                                if( $lastEvent->serverResponse->currentFreeGames < $lastEvent->serverResponse->totalFreeGames ) 
                                {
                                    $restore = 'true';
                                }
                            }
                            $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                            $gameBets = $slotSettings->Bet;
                            foreach( $gameBets as &$bt ) 
                            {
                                $bt = $bt * 100;
                            }
                            $response = '{"status":"success","microtime":0.0077991485595703,"dateTime":"2020-02-13 13:16:03","error":"","content":{"cmd":"gameInit","balance":' . $balanceInCents . ',"session":"54594109_4133a64673d3883d42cd003ee905ba3e","betInfo":{"denomination":0.01,"bet":' . $gameBets[0] . ',"lines":25},"betSettings":{"denomination":[0.01],"bets":[' . implode(',', $gameBets) . '],"lines":[1,5,10,15,20,25]},"symbols":' . $reelStr . ',"reels":{},"exitUrl":"\/","pingInterval":60000,"restore":' . $restore . ',"freeSpin":{' . $freeStateStr . '},"hash":"da0a5044aebc413b6da3a8e42dea0246"}}';
                            $response = '{"status":"success","microtime":0.2377917766571,"dateTime":"2020-02-18 15:50:46","error":"","content":{"cmd":"gameInit","balance":' . $balanceInCents . ',"session":"55520234_b64d5c8e3495d291120765ad7c95c322","betInfo":{"denomination":0.01,"bet":' . $gameBets[0] . ',"lines":20},"betSettings":{"denomination":[0.01],"bets":[' . implode(',', $gameBets) . '],"lines":[1,5,10,15,20]},"symbols":' . $reelStr . ',"reels":{"base":{"1":["' . implode('","', $slotSettings->reelStrip1) . '"],"2":["' . implode('","', $slotSettings->reelStrip2) . '"],"3":["' . implode('","', $slotSettings->reelStrip3) . '"],"4":["' . implode('","', $slotSettings->reelStrip4) . '"],"5":["' . implode('","', $slotSettings->reelStrip5) . '"]}},"exitUrl":"\/","freeSpin":{' . $freeStateStr . '},"pingInterval":60000,"restore":' . $restore . ',"hash":"159ede03c3e477340e5a1e305bf4f884"}}';
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
                            $response = '{"status":"success","microtime":0.0026731491088867,"dateTime":"2020-02-13 13:23:32","error":"","content":{"cmd":"sessionInfo","serverMathematics":"\/game\/TikiTorchAT\/server","serverResources":"","sessionId":"54594109_4133a64673d3883d42cd003ee905ba3e","exitUlt":"\/","exitUrl":"\/","id":"341","name":"GameName","currency":"ALL","language":"en","type":"aristocrat","systemName":"tiki_torch","version":"2","mobile":"1"}}';
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
                                $slotSettings->SetGameData('TikiTorchATMpl', 10);
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeGames', 5);
                            }
                            else if( $_POST['pick'] == 1 ) 
                            {
                                $slotSettings->SetGameData('TikiTorchATMpl', 5);
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeGames', 10);
                            }
                            else if( $_POST['pick'] == 2 ) 
                            {
                                $slotSettings->SetGameData('TikiTorchATMpl', 3);
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeGames', 15);
                            }
                            else if( $_POST['pick'] == 3 ) 
                            {
                                $slotSettings->SetGameData('TikiTorchATMpl', 2);
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeGames', 20);
                            }
                            $response = '{"status":"success","microtime":0.0093810558319092,"dateTime":"2020-02-15 14:26:21","error":"","content":{"session":"54990161_9709e13bca7763b46396cda1868d2e7e","cmd":"gamePick","balance":' . $balanceInCents . ',"win":0,"symbols":{"1":{"1":"symbol_6","2":"symbol_9","3":"symbol_12"},"2":{"1":"symbol_10","2":"symbol_5","3":"symbol_8"},"3":{"1":"symbol_10","2":"symbol_12","3":"symbol_11"},"4":{"1":"symbol_12","2":"symbol_10","3":"symbol_6"},"5":{"1":"symbol_3","2":"symbol_8","3":"symbol_1"}},"winLines":[],"freeSpin":{"id":"54990161_20200215132548","current":0,"add":' . $slotSettings->GetGameData('TikiTorchATFreeGames') . ',"total":' . $slotSettings->GetGameData('TikiTorchATFreeGames') . ',"totalWin":' . ($slotSettings->GetGameData('TikiTorchATBonusWin') * 100) . ',"multiplayer":' . $slotSettings->GetGameData('TikiTorchATMpl') . ',"pick":' . $_POST['pick'] . '},"actionId":"54990161_10_60","hash":"1ace145628d930d28bdf38789a28a9d7"}}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('TikiTorchATTotalWin');
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
                            $slotSettings->SetGameData('TikiTorchATTotalWin', $totalWin);
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
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[6] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[7] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[9] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[10] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[13] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[15] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[16] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[17] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[19] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[20] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[21] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[22] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[23] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $linesId[24] = [
                                3, 
                                1, 
                                1, 
                                1, 
                                3
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
                                $slotSettings->SetGameData('TikiTorchATBonusWin', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeGames', 0);
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', 0);
                                $slotSettings->SetGameData('TikiTorchATTotalWin', 0);
                                $slotSettings->SetGameData('TikiTorchATFreeBalance', 0);
                                $slotSettings->SetGameData('TikiTorchATMpl', 3);
                            }
                            else
                            {
                                $slotSettings->SetGameData('TikiTorchATCurrentFreeGame', $slotSettings->GetGameData('TikiTorchATCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->GetGameData('TikiTorchATMpl');
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
                                    0
                                ];
                                $wild = ['symbol_1'];
                                $scatter = 'symbol_12';
                                $linesId0 = [];
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    $linesId0[$k] = $linesId[$k];
                                    for( $kl = 0; $kl < count($linesId0[$k]); $kl++ ) 
                                    {
                                        if( $linesId0[$k][$kl] == 1 ) 
                                        {
                                            $linesId0[$k][$kl] = 3;
                                        }
                                        else if( $linesId0[$k][$kl] == 3 ) 
                                        {
                                            $linesId0[$k][$kl] = 1;
                                        }
                                    }
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable[$csym]) ) 
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
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable[$csym][1] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . ($k + 1) . ',"symbol":"' . $csym . '","count":1,"side":"left","elements":[[1,' . $linesId0[$k][0] . ']],"xWin":' . ($mpl + $bonusMpl) . ',"win":' . ($cWins[$k] * 100) . '}';
                                                }
                                            }
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
                                                    for( $wld = 0; $wld < 2; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . ($k + 1) . ',"symbol":"' . $csym . '","count":2,"side":"left","elements":[[1,' . $linesId0[$k][0] . '],[2,' . $linesId0[$k][1] . ']],"xWin":' . ($mpl + $bonusMpl) . ',"win":' . ($cWins[$k] * 100) . '}';
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
                                                    for( $wld = 0; $wld < 3; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . ($k + 1) . ',"symbol":"' . $csym . '","count":3,"side":"left","elements":[[1,' . $linesId0[$k][0] . '],[2,' . $linesId0[$k][1] . '],[3,' . $linesId0[$k][2] . ']],"xWin":' . ($mpl + $bonusMpl) . ',"win":' . ($cWins[$k] * 100) . '}';
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
                                                    for( $wld = 0; $wld < 4; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . ($k + 1) . ',"symbol":"' . $csym . '","count":4,"side":"left","elements":[[1,' . $linesId0[$k][0] . '],[2,' . $linesId0[$k][1] . '],[3,' . $linesId0[$k][2] . '],[4,' . $linesId0[$k][3] . ']],"xWin":' . ($mpl + $bonusMpl) . ',"win":' . ($cWins[$k] * 100) . '}';
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
                                                    for( $wld = 0; $wld < 5; $wld++ ) 
                                                    {
                                                        if( in_array($s[$wld], $wild) ) 
                                                        {
                                                        }
                                                    }
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . ($k + 1) . ',"symbol":"' . $csym . '","count":5,"side":"left","elements":[[1,' . $linesId0[$k][0] . '],[2,' . $linesId0[$k][1] . '],[3,' . $linesId0[$k][2] . '],[4,' . $linesId0[$k][3] . '],[5,' . $linesId0[$k][4] . ']],"xWin":' . ($mpl + $bonusMpl) . ',"win":' . ($cWins[$k] * 100) . '}';
                                                }
                                            }
                                        }
                                    }
                                    if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                    {
                                        array_push($lineWins, $tmpStringWin);
                                        $totalWin += $cWins[$k];
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersPos = [];
                                $scattersCount = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            if( $p == 0 ) 
                                            {
                                                $pr = 3;
                                            }
                                            else if( $p == 1 ) 
                                            {
                                                $pr = 2;
                                            }
                                            else if( $p == 2 ) 
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
                                    $scattersStr = '{"line":"scatter","symbol":"' . $scatter . '","count":' . $scattersCount . ',"elements":[' . implode(',', $scattersPos) . '],"xWin":' . $bonusMpl . ',"freeSpinAdd":-1,"win":' . ($scattersWin * 100) . '}';
                                    array_push($lineWins, $scattersStr);
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr = '{"line":"scatter","symbol":"' . $scatter . '","count":' . $scattersCount . ',"elements":[' . implode(',', $scattersPos) . '],"xWin":' . $bonusMpl . ',"freeSpinAdd":0,"win":' . ($scattersWin * 100) . '}';
                                    array_push($lineWins, $scattersStr);
                                }
                                else
                                {
                                    $scattersStr .= '';
                                }
                                $totalWin += $scattersWin;
                                if( $i > 1000 ) 
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
                                $slotSettings->SetGameData('TikiTorchATBonusWin', $slotSettings->GetGameData('TikiTorchATBonusWin') + $totalWin);
                                $slotSettings->SetGameData('TikiTorchATTotalWin', $slotSettings->GetGameData('TikiTorchATTotalWin') + $totalWin);
                                $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('TikiTorchATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('TikiTorchATMpl') . ',"add":0,"total":' . $slotSettings->GetGameData('TikiTorchATFreeGames') . ',"totalWin":' . ($slotSettings->GetGameData('TikiTorchATBonusWin') * 100) . '';
                            }
                            else
                            {
                                $slotSettings->SetGameData('TikiTorchATTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData('TikiTorchATFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData('TikiTorchATFreeGames', $slotSettings->GetGameData('TikiTorchATFreeGames') + $slotSettings->slotFreeCount);
                                    $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('TikiTorchATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('TikiTorchATMpl') . ',"add":' . $slotSettings->slotFreeCount . ',"total":' . $slotSettings->GetGameData('TikiTorchATFreeGames') . ',"totalWin":' . ($slotSettings->GetGameData('TikiTorchATBonusWin') * 100) . '';
                                }
                                else
                                {
                                    $slotSettings->SetGameData('TikiTorchATFreeBalance', $Balance);
                                    $slotSettings->SetGameData('TikiTorchATBonusWin', $totalWin);
                                    $slotSettings->SetGameData('TikiTorchATFreeGames', $slotSettings->slotFreeCount);
                                    $freeStateStr = '"id":"54594109_20200214110301","current":' . $slotSettings->GetGameData('TikiTorchATCurrentFreeGame') . ',"multiplayer":' . $slotSettings->GetGameData('TikiTorchATMpl') . ',"add":-1,"total":-1,"totalWin":' . ($slotSettings->GetGameData('TikiTorchATBonusWin') * 100) . '';
                                }
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $postData['slotLines'] . ',"slotBet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData('TikiTorchATFreeGames') . ',"Mpl":' . $slotSettings->GetGameData('TikiTorchATMpl') . ',"currentFreeGames":' . $slotSettings->GetGameData('TikiTorchATCurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":{},"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $reelStrArr = [];
                            for( $i = 1; $i <= 5; $i++ ) 
                            {
                                $reelStrArr[] = '"' . $i . '":{"1":"' . $reels['reel' . $i][2] . '","2":"' . $reels['reel' . $i][1] . '","3":"' . $reels['reel' . $i][0] . '"}';
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
