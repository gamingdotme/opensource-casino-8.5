<?php 
namespace VanguardLTE\Games\LuckyOldMasterQSW
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
                        $postData = json_decode(trim(file_get_contents('php://input')), true);
                        $postData['slotEvent'] = $postData['request'];
                        if( $postData['slotEvent'] == 'update' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $slotSettings->GetBalance() . '"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') <= $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 && $postData['slotEvent'] != 'init' ) 
                        {
                            $postData['slotEvent'] = 'freespin';
                        }
                        if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            if( $postData['lines'] <= 0 || $postData['bet'] <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['bet'] * $postData['lines']) ) 
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
                        if( $postData['slotEvent'] == 'init' ) 
                        {
                            $slotSettings->SetGameData('LuckyOldMasterQSWBonusWin', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWFreeGames', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWCurrentFreeGame', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWTotalWin', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWStartBonusWin', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWFreeBalance', 0);
                            $slotSettings->SetGameData('LuckyOldMasterQSWBets', [
                                [
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
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ]
                            ]);
                            $slotSettings->SetGameData('LuckyOldMasterQSWAllBet', 0);
                            $Balance = $slotSettings->GetBalance();
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTkyMzAzMTEyNDgzOnN3X2x1Y2t5X29tcTp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTU5MjMwMzExOSwiaXNzIjoic2t5d2luZGdyb3VwIn0.RUI1uNW_rXWG-7yHJgehbxZ1C8ZEem-iwEvoziqf1negJh3rh9Mx8grow96a82XS_g0R8vEMLpslA5aYBac76A","balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init","settings":{"jackpotId":{"sw_lucky_omq":"sw_lucky_omq"},"winMax":500000,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $slotSettings->Bet[0] . ',"stakeMax":200,"stakeMin":0.01,"maxTotalStake":200,"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"version":"1.3.6"},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}';
                        }
                        else if( $postData['slotEvent'] == 'confirm-bet' ) 
                        {
                            $bets = $postData['bets'];
                            $betsAll = [
                                [
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
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ]
                            ];
                            $allbet = 0;
                            for( $i = 0; $i < 3; $i++ ) 
                            {
                                for( $j = 0; $j < 5; $j++ ) 
                                {
                                    if( isset($bets[$i][$j]['coins']) ) 
                                    {
                                        $betsAll[$i][$j] = $bets[$i][$j]['coins'] * $bets[$i][$j]['num'];
                                        $allbet += ($bets[$i][$j]['coins'] * $bets[$i][$j]['num']);
                                    }
                                }
                            }
                            $slotSettings->SetGameData('LuckyOldMasterQSWBetsRaw', $postData['bets']);
                            $slotSettings->SetGameData('LuckyOldMasterQSWBets', $betsAll);
                            $slotSettings->SetGameData('LuckyOldMasterQSWAllBet', $allbet);
                            $Balance = $slotSettings->GetBalance();
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTkyMzAzMTEyNDgzOnN3X2x1Y2t5X29tcTp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTU5MjMwMzExOSwiaXNzIjoic2t5d2luZGdyb3VwIn0.RUI1uNW_rXWG-7yHJgehbxZ1C8ZEem-iwEvoziqf1negJh3rh9Mx8grow96a82XS_g0R8vEMLpslA5aYBac76A","jpTrigger":true,"tickers":[{"pools":{"lucky":{"amount":' . $slotSettings->slotJackpot[0] . '}}}],"balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"confirm-bet","totalBet":' . $allbet . ',"roundEnded":false},"requestId":' . $postData['requestId'] . ',"roundEnded":true,"betsAll":' . json_encode($betsAll) . '}';
                        }
                        else if( $postData['slotEvent'] == 'reset-round' || $postData['slotEvent'] == 'play-bet' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $board = [
                                [
                                    0, 
                                    0
                                ], 
                                [
                                    0, 
                                    1
                                ], 
                                [
                                    0, 
                                    2
                                ], 
                                [
                                    0, 
                                    3
                                ], 
                                [
                                    0, 
                                    2
                                ], 
                                [
                                    0, 
                                    3
                                ], 
                                [
                                    1, 
                                    0
                                ], 
                                [
                                    1, 
                                    1
                                ], 
                                [
                                    1, 
                                    2
                                ], 
                                [
                                    1, 
                                    3
                                ], 
                                [
                                    1, 
                                    2
                                ], 
                                [
                                    1, 
                                    3
                                ], 
                                [
                                    2, 
                                    0
                                ], 
                                [
                                    2, 
                                    1
                                ], 
                                [
                                    2, 
                                    2
                                ], 
                                [
                                    2, 
                                    3
                                ], 
                                [
                                    2, 
                                    2
                                ], 
                                [
                                    2, 
                                    3
                                ]
                            ];
                            shuffle($board);
                            $payTable[0] = [
                                42, 
                                22, 
                                12, 
                                6, 
                                3
                            ];
                            $payTable[1] = [
                                33, 
                                16, 
                                8, 
                                5, 
                                2
                            ];
                            $payTable[2] = [
                                27, 
                                14, 
                                7, 
                                4, 
                                2
                            ];
                            $bank = $slotSettings->GetBank();
                            $betsRaw = $slotSettings->GetGameData('LuckyOldMasterQSWBetsRaw');
                            $bets = $slotSettings->GetGameData('LuckyOldMasterQSWBets');
                            $allbet = $slotSettings->GetGameData('LuckyOldMasterQSWAllBet');
                            $jpTrigger = 'false';
                            $jackState = [];
                            if( $allbet > 0 ) 
                            {
                                if( $allbet <= $slotSettings->GetBalance() && $allbet > 0 ) 
                                {
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $jackState = $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                }
                                else
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                for( $i = 0; $i < 2000; $i++ ) 
                                {
                                    shuffle($board);
                                    $totalWin = 0;
                                    $rewards = [];
                                    $cPaySym = $payTable[$board[0][0]][$board[0][1]];
                                    $cPayColor = $payTable[$board[0][0]][4];
                                    $symBet = $bets[$board[0][0]][$board[0][1]];
                                    $colorBet = $bets[$board[0][0]][4];
                                    $mpl = 1;
                                    $randMpl = rand(1, 30);
                                    $mplStr = '';
                                    if( $randMpl == 1 ) 
                                    {
                                        $mpA = [
                                            2, 
                                            3, 
                                            5, 
                                            6, 
                                            8
                                        ];
                                        shuffle($mpA);
                                        $mpl = $mpA[0];
                                        $mplStr = ',"multiplier":' . $mpl;
                                    }
                                    if( $symBet * $cPaySym > 0 ) 
                                    {
                                        $rewards[] = '{"payout":' . ($symBet * $cPaySym) . ',"payTable":[' . $board[0][0] . ',' . $board[0][1] . ']}';
                                    }
                                    if( $colorBet * $cPayColor > 0 ) 
                                    {
                                        $rewards[] = '{"payout":' . ($colorBet * $cPayColor) . ',"payTable":[' . $board[0][0] . ',4]}';
                                    }
                                    $totalWin += ($symBet * $cPaySym);
                                    $totalWin += ($colorBet * $cPayColor);
                                    $totalWin = $totalWin * $mpl;
                                    if( $totalWin <= $bank ) 
                                    {
                                        break;
                                    }
                                }
                                if( isset($jackState['isJackPay']) && $jackState['isJackPay'] ) 
                                {
                                    $totalWin = 0;
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $response_log = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response_log, $allbet, 1, $totalWin, '');
                            }
                            if( $postData['slotEvent'] == 'reset-round' ) 
                            {
                                $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTkyMjkyNTEwOTgzOnN3X2x1Y2t5X29tcTp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTU5MjI5MjUxNiwiaXNzIjoic2t5d2luZGdyb3VwIn0.q5Be48SlSBOX78bksZ6UdoRLdbPqJPFtPp85EODSGDk5izFav8nXNI_dR4dt1xazgYpscBfeGK5iUg4z8oI5Ug","jackpot":{"amount":' . $slotSettings->slotJackpot[0] . '},"jpTrigger":' . $jpTrigger . ',"tickers":[{"pools":{"lucky":{"amount":' . $slotSettings->slotJackpot[0] . '}}}],"balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"reset-round","stopPositions":[[' . $board[0][0] . ',' . $board[0][1] . ']],"roundEnded":true},"requestId":' . $postData['requestId'] . ',"roundEnded":true}';
                            }
                            else
                            {
                                if( isset($jackState['isJackPay']) && $jackState['isJackPay'] ) 
                                {
                                    $jpTrigger = 'true';
                                    $slotSettings->SetBalance($slotSettings->slotJackpot[0]);
                                    $slotSettings->ClearJackpot(0);
                                }
                                if( !isset($rewards) ) 
                                {
                                    $rewards = [];
                                }
                                $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTkyNjA4MjA1NjQ3OnN3X2x1Y2t5X29tcTp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTU5MjYwODIyMCwiaXNzIjoic2t5d2luZGdyb3VwIn0.U9bPvjGkKo80aHkCAjjTy16Rsxb_ErZBp3TT4eTLU6HFxAz5u7fe7n0sGA0yfxmgaYMahGqB7PIjZyXhDBmp_w","jpTrigger":' . $jpTrigger . ',"tickers":[{"pools":{"lucky":{"amount":' . $slotSettings->slotJackpot[0] . '}}}],"jpTrigger":' . $jpTrigger . ',"jackpot":{"amount":' . $slotSettings->slotJackpot[0] . '},"balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"play-bet","stopPositions":[[' . $board[0][0] . ',' . $board[0][1] . ']],"rewards":[' . implode(',', $rewards) . '],"totalBet":' . $allbet . ',"bets":[' . json_encode($betsRaw) . ']' . $mplStr . ',"totalWin":' . $totalWin . ',"roundEnded":true},"requestId":' . $postData['requestId'] . ',"roundEnded":true}';
                            }
                            $slotSettings->SetGameData('LuckyOldMasterQSWBets', [
                                [
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
                                    0
                                ], 
                                [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ]
                            ]);
                            $slotSettings->SetGameData('LuckyOldMasterQSWAllBet', 0);
                        }
                        else if( $postData['slotEvent'] == 'start-round' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjE1MDpwbGF5ZXIxNTkyMjkyNTEwOTgzOnN3X2x1Y2t5X29tcTp3ZWIiLCJnYW1lTW9kZSI6ImZ1biIsImlhdCI6MTU5MjI5MjUxNiwiaXNzIjoic2t5d2luZGdyb3VwIn0.q5Be48SlSBOX78bksZ6UdoRLdbPqJPFtPp85EODSGDk5izFav8nXNI_dR4dt1xazgYpscBfeGK5iUg4z8oI5Ug","jpTrigger":true,"jackpot":{"amount":' . $slotSettings->slotJackpot[0] . '},"tickers":[{"pools":{"lucky":{"amount":' . $slotSettings->slotJackpot[0] . '}}}],"balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"start-round","roundEnded":false,"payTable":[[42,22,12,6,3],[33,16,8,5,2],[27,14,7,4,2]]},"requestId":' . $postData['requestId'] . ',"roundEnded":true}';
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
