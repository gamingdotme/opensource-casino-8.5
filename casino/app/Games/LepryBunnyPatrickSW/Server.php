<?php 
namespace VanguardLTE\Games\LepryBunnyPatrickSW
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
                        if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $postData['slotEvent'] != 'init' ) 
                        {
                            $postData['slotEvent'] = 'freespin';
                        }
                        if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                        {
                            if( ($postData['lines'] <= 0 || $postData['bet'] <= 0) && $slotSettings->GetGameDataStatic($slotSettings->slotId . 'bonusPickWin') <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['bet'] * $postData['lines']) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' && ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 || $slotSettings->GetBalance() < $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin')) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                            $slotSettings->InternalError($response . ' -- TotalWin = ' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . ' -- Balance = ' . $slotSettings->GetBalance());
                            exit( $response );
                        }
                        if( $slotSettings->GetGameDataStatic($slotSettings->slotId . 'bonusPickWin') > 0 && $postData['slotEvent'] == 'spin' && isset($postData['selectionID']) ) 
                        {
                            $postData['slotEvent'] = 'pick_bonus';
                            $bonusPickWin = $slotSettings->GetGameDataStatic($slotSettings->slotId . 'bonusPickWin');
                            $slotLines = $slotSettings->GetGameDataStatic($slotSettings->slotId . 'slotLines');
                            $slotBet = $slotSettings->GetGameDataStatic($slotSettings->slotId . 'slotBet');
                            $selectionID = $postData['selectionID'];
                            $items = [];
                            $b2pw = [
                                1, 
                                2, 
                                3, 
                                4, 
                                1, 
                                2, 
                                3, 
                                4, 
                                5, 
                                10, 
                                15, 
                                20, 
                                25, 
                                5, 
                                10, 
                                15, 
                                20, 
                                25, 
                                5, 
                                10, 
                                15, 
                                20, 
                                25, 
                                50, 
                                100, 
                                50, 
                                100
                            ];
                            shuffle($b2pw);
                            $items[0] = '{"id":0,"available":false,"visibleValue":"' . ($b2pw[0] * $slotLines * $slotBet) . '"}';
                            $items[1] = '{"id":1,"available":false,"visibleValue":"' . ($b2pw[1] * $slotLines * $slotBet) . '"}';
                            $items[2] = '{"id":2,"available":false,"visibleValue":"' . ($b2pw[2] * $slotLines * $slotBet) . '"}';
                            $items[$selectionID] = '{"id":' . $selectionID . ',"available":false,"visibleValue":"' . ($b2pw[2] * $slotLines * $slotBet) . '"}';
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjQxOnBsYXllcjE1OTg0MTc0MDIzMDY6c3dfbGU6d2ViIiwiZ2FtZU1vZGUiOiJmdW4iLCJpYXQiOjE1OTg0MTc0MTYsImlzcyI6InNreXdpbmRncm91cCJ9.zHtNZtEYJQmkcBdbctglvNT8bczqsbw8CQ8qqsZrzAo8IIaZS7QPHAbNBFRsIKhOZcv0FiILFnKBlzZeesbqgA","balance":{"currency":"","amount":' . $slotSettings->GetBalance() . ',"real":{"amount":' . $slotSettings->GetBalance() . '},"bonus":{"amount":0}},"result":{"request":"bonusSelection","state":{"currentScene":"main","multiplier":1,"initialFreeSpinWin":0},"roundsInfo":{"currentRoundId":0,"rounds":[{"id":0,"selectedIds":[' . $postData['selectionID'] . '],"items":[' . implode(',', $items) . ']}],"totalWin":' . $bonusPickWin . ',"totalBet":' . ($slotLines * $slotBet) . "},\"totalBet\":0,\"totalWin\":0,\"scene\":\"bonus\",\"rewards\":[],\"events\":[{\"id\":\"bonusSelectionEnd\"}],\"roundEnded\":false,\"version\":\"1.1.1\"},\"requestId\":19,\"roundEnded\":false}\t";
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'bonusPickWin', 0);
                        }
                        if( $postData['slotEvent'] == 'init' ) 
                        {
                            $lastEvent = $slotSettings->GetHistory();
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                $rp2 = '[' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0] . ']';
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1] . ']');
                                $rp2 .= (',[' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2] . ']');
                                $bet = $lastEvent->serverResponse->bet;
                            }
                            else
                            {
                                $rp1 = implode(',', [
                                    rand(0, count($slotSettings->reelStrip1) - 3), 
                                    rand(0, count($slotSettings->reelStrip2) - 3), 
                                    rand(0, count($slotSettings->reelStrip3) - 3), 
                                    rand(0, count($slotSettings->reelStrip4) - 3), 
                                    rand(0, count($slotSettings->reelStrip5) - 3)
                                ]);
                                $rp_1 = rand(0, count($slotSettings->reelStrip1) - 3);
                                $rp_2 = rand(0, count($slotSettings->reelStrip2) - 3);
                                $rp_3 = rand(0, count($slotSettings->reelStrip3) - 3);
                                $rp_4 = rand(0, count($slotSettings->reelStrip4) - 3);
                                $rp_5 = rand(0, count($slotSettings->reelStrip5) - 3);
                                $rr1 = $slotSettings->reelStrip1[$rp_1];
                                $rr2 = $slotSettings->reelStrip2[$rp_2];
                                $rr3 = $slotSettings->reelStrip3[$rp_3];
                                $rr4 = $slotSettings->reelStrip4[$rp_4];
                                $rr5 = $slotSettings->reelStrip5[$rp_5];
                                $rp2 = '[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']';
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                $rr4 = $slotSettings->reelStrip4[$rp_4 + 1];
                                $rr5 = $slotSettings->reelStrip5[$rp_5 + 1];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                $rr4 = $slotSettings->reelStrip4[$rp_4 + 2];
                                $rr5 = $slotSettings->reelStrip5[$rp_5 + 2];
                                $rp2 .= (',[' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5 . ']');
                                $bet = $slotSettings->Bet[0];
                            }
                            $jsSet = json_encode($slotSettings);
                            $Balance = $slotSettings->GetBalance();
                            $lang = json_encode(\Lang::get('games.' . $game));
                            $response = '{"gameSession":"","balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init","name":"Zhao Cai Tong Zi","gameId":"sw_shctz","settings":{"winMax":500000,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $bet . ',"stakeMax":' . $slotSettings->Bet[count($slotSettings->Bet) - 1] . ',"stakeMin":' . $slotSettings->Bet[0] . ',"maxTotalStake":' . ($slotSettings->Bet[count($slotSettings->Bet) - 1] * 9) . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"slot":{"sets":{"main":{"reels":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . '],[' . implode(',', $slotSettings->reelStrip4) . '],[' . implode(',', $slotSettings->reelStrip5) . ']]}},"reels":{"set":"main","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"linesDefinition":{"fixedLinesCount":9,"directions":[0,1]},"paytable":{"stake":{"value":1,"multiplier":1,"payouts":[[0,0,0,0,0],[2,10,50,500,10000],[0,5,25,250,5000],[0,5,20,170,1200],[0,5,20,125,750],[0,2,10,50,350],[0,2,10,50,250],[0,0,5,25,200],[0,0,3,10,50],[0,1,5,10,100]]}},"lines":[[1,1,1,1,1],[0,0,0,0,0],[2,2,2,2,2],[0,1,2,1,0],[2,1,0,1,2],[0,0,1,0,0],[2,2,1,2,2],[1,0,0,0,1],[1,2,2,2,1]]},"stake":null,"version":"1.0.2"},"roundEnded":true}';
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjQxOnBsYXllcjE1OTgzOTAxNjQxNTM6c3dfbGU6d2ViIiwiZ2FtZU1vZGUiOiJmdW4iLCJpYXQiOjE1OTgzOTAxNzMsImlzcyI6InNreXdpbmRncm91cCJ9.MnCsbpjg9jM3SisYhprfHaDdCv_Oo8UEW399xWW22Zy-TY6ZI3rFyMZfQvFZ2M0bdNi4-RU-QTgig4DfBj0Qdg","balance":{"currency":"","amount":' . $Balance . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"init","name":"LepryBunny™","gameId":"sw_le","settings":{"winMax":500000,"stakeAll":[' . implode(',', $slotSettings->Bet) . '],"stakeDef":' . $bet . ',"stakeMax":' . $slotSettings->Bet[count($slotSettings->Bet) - 1] . ',"stakeMin":' . $slotSettings->Bet[0] . ',"maxTotalStake":' . ($slotSettings->Bet[count($slotSettings->Bet) - 1] * 30) . ',"defaultCoin":1,"coins":[1],"currencyMultiplier":100},"slot":{"sets":{"set1":{"reels":[[11,1,8,10,2,5,10,8,10,11,12,6,8,9,7,12,9,10,6,7,2,9,10,8,6,5,11,7,12,7,3,11,6,11,0,6,6,11,8,11,7,11,8,1,7,10,4,7,9,11,8,9,11,12,10,9,11,4,5,9,8,10,9,12,5,7,7,3,8,9,3,0,6,6,10,4,6,10,1,10,4,8,10,4,5,4,10,9,10,3,3,2,10,8,12,4,3,7,9,7,8,5,9,5,8,9,12,1,5,11,9,2,5,11,8,6,11,2,7,11],[10,7,9,3,11,6,9,8,11,3,2,10,9,6,4,4,7,8,10,9,11,7,1,9,11,9,5,7,11,9,7,11,8,4,11,3,10,6,0,5,3,9,9,2,10,9,11,7,10,3,10,6,2,7,9,8,1,11,4,6,6,5,8,4,3,10,8,11,5,6,1,8,2,9,6,5,7,10,8,11,5,0,7,4,2,5,9,11,7,8,11,5,4,6,3,2,8,5,4,6,7,10,8,11,6,10,10,9,1,8,10,0,8,3,10,7,11,6,4,5],[10,11,4,4,3,8,10,2,6,8,12,6,10,8,9,10,11,3,6,12,7,3,6,1,3,6,2,12,8,9,4,6,11,6,7,11,0,3,7,5,1,11,9,7,2,6,1,7,10,7,2,7,4,7,8,0,2,11,10,9,5,6,8,5,9,11,10,1,8,5,7,3,9,9,5,10,12,2,7,10,6,3,9,10,12,4,8,9,1,10,4,11,0,2,10,5,8,9,11,12,9,8,11,3,11,12,10,8,9,11,8,5,5,7,4,11,4,12,5,4],[6,11,5,6,7,8,11,3,10,4,6,9,5,5,8,6,1,11,8,8,11,10,2,6,11,3,9,3,9,5,1,2,6,9,4,7,3,4,8,7,6,0,9,9,2,9,10,7,2,4,4,11,3,2,9,9,7,2,11,10,11,5,11,10,2,3,1,3,10,8,10,7,9,10,8,10,8,4,10,7,5,7,1,3,11,4,11,3,7,6,1,4,7,9,6,0,5,9,10,8,10,4,3,10,6,11,5,10,4,8,11,11,8,5,6,8,2,7,5,1],[5,8,2,10,5,11,6,5,9,3,0,7,10,5,8,4,9,6,12,7,9,7,2,10,6,11,5,7,11,6,12,5,6,2,1,2,6,4,3,1,10,9,11,8,3,11,9,6,7,7,9,10,1,0,4,2,5,1,8,10,4,11,12,1,4,8,3,7,3,4,5,2,12,3,10,4,7,1,6,2,11,4,3,10,0,8,11,6,4,11,4,5,9,8,6,7,5,8,6,3,5,7,9,8,12,9,9,1,3,10,6,2,6,10,8,5,8,7,12,5]]},"set2":{"reels":[[11,9,8,10,2,5,10,8,10,12,6,10,11,9,7,12,9,10,6,7,2,9,10,8,6,5,11,7,12,7,3,11,6,11,1,6,6,11,8,11,7,11,8,1,7,10,4,4,9,11,8,9,11,12,10,9,11,4,5,9,8,10,9,8,5,7,7,3,11,9,3,2,6,6,10,4,6,10,1,10,4,8,10,4,5,4,10,9,10,3,3,2,12,8,5,4,3,7,3,7,8,12,9,5,8,9,12,11,5,11,9,2,5,11,8,6,11,2,7,11],[10,7,9,3,11,1,9,8,7,3,2,10,9,6,11,4,7,8,10,9,11,7,1,9,7,9,5,7,11,9,1,2,8,4,11,4,10,6,11,5,3,9,9,2,10,2,11,7,10,3,10,6,2,7,9,8,6,11,4,6,6,5,8,4,3,10,8,11,5,6,11,8,2,9,6,5,7,10,8,11,5,9,7,4,2,5,9,11,7,8,11,5,4,6,3,5,8,5,4,6,7,10,8,11,6,10,10,9,8,1,10,3,8,3,10,7,11,6,4,5],[10,12,4,4,3,8,10,2,6,8,5,6,1,8,9,10,11,3,6,9,7,3,6,11,3,6,2,12,8,9,4,6,11,6,7,11,2,12,7,5,1,11,9,7,2,6,1,7,10,7,2,7,4,7,8,3,2,12,10,9,5,6,8,5,9,11,10,1,8,5,7,3,9,9,5,10,11,2,7,10,6,3,9,10,12,4,8,6,1,10,4,11,4,2,10,5,8,9,11,9,9,8,11,3,11,12,10,8,9,11,8,5,5,7,12,11,4,10,5,4],[6,11,10,6,7,8,11,4,10,4,6,1,5,5,8,6,7,5,8,8,1,6,2,6,11,2,6,3,9,5,11,2,6,9,4,7,3,4,8,7,6,5,9,9,2,9,10,7,2,4,4,11,3,2,9,9,7,2,8,10,1,5,11,10,2,3,6,3,1,8,10,7,9,10,8,10,8,4,3,7,5,7,1,3,11,9,11,3,6,7,7,4,7,9,1,4,5,9,10,8,10,4,3,10,6,11,5,10,4,8,11,11,8,5,6,9,2,7,5,11],[5,8,2,4,5,12,6,10,9,3,1,7,10,5,8,11,9,6,11,7,9,7,2,10,6,3,5,7,11,6,12,5,6,2,1,2,6,4,3,1,10,9,11,8,10,11,9,6,7,7,9,6,1,2,4,2,5,1,8,10,4,11,12,11,4,8,3,7,3,12,5,2,10,3,3,4,7,1,6,2,11,4,3,10,4,8,11,12,4,11,4,5,9,8,6,7,5,8,6,12,5,7,9,8,12,9,9,1,3,10,6,2,6,10,8,5,8,7,3,5]]},"set3":{"reels":[[11,9,8,10,2,5,10,8,10,11,6,6,7,9,7,4,9,10,6,7,2,9,10,8,6,5,11,7,5,7,3,11,6,11,2,6,6,11,8,0,7,11,8,3,7,10,11,4,9,11,8,9,11,6,10,9,11,4,5,9,8,10,9,8,5,7,7,3,0,9,3,2,6,6,10,4,6,10,4,10,4,8,10,4,5,4,10,9,0,3,3,2,10,8,5,4,3,7,3,7,8,5,9,5,8,9,7,4,5,11,9,2,5,11,8,6,11,2,7,11],[10,7,9,3,2,3,9,8,11,3,2,10,9,6,4,4,7,8,10,9,11,7,0,9,7,9,5,7,11,9,4,2,8,4,11,3,10,6,11,5,3,9,9,2,10,2,11,7,10,3,10,6,2,7,9,8,4,11,4,6,6,5,8,4,3,10,8,11,5,6,11,8,2,9,6,0,7,10,8,11,5,6,7,4,2,5,9,11,7,8,11,5,4,6,3,5,8,5,4,6,7,10,8,11,6,10,0,9,8,5,10,3,10,3,10,7,11,6,4,5],[10,11,4,4,3,8,10,2,6,8,5,6,0,8,9,10,11,3,6,9,7,3,6,3,3,6,2,3,8,9,4,6,11,6,7,11,2,3,7,5,0,11,9,7,2,6,7,7,10,7,2,7,4,7,8,3,2,11,10,9,5,6,8,2,9,11,10,5,8,5,7,0,9,9,5,10,11,2,7,10,6,3,9,10,5,4,8,6,11,10,4,11,4,2,10,5,8,9,0,9,9,8,11,3,11,4,10,8,9,11,8,5,5,7,4,11,4,10,5,4],[6,11,5,6,7,8,11,2,10,4,6,9,5,5,8,6,10,11,8,9,2,10,2,6,11,0,11,3,9,5,4,2,6,9,4,7,3,4,8,7,6,4,9,9,2,9,10,7,2,4,0,11,3,2,9,9,7,2,8,10,11,5,11,10,2,3,4,3,5,8,10,7,9,10,8,10,8,0,3,7,5,7,6,3,11,3,11,3,8,6,7,4,7,0,6,4,5,9,10,8,10,4,3,10,6,11,5,10,4,8,11,11,8,5,6,9,2,7,5,7],[5,8,2,4,5,11,6,5,9,3,2,7,10,5,8,0,9,6,11,7,9,7,2,10,6,3,5,7,11,6,2,5,6,2,5,2,6,4,0,3,10,9,11,8,3,11,9,6,7,7,9,10,4,2,4,2,5,6,8,10,4,11,3,5,4,8,3,7,3,4,5,2,10,3,0,4,7,4,6,2,11,4,3,10,4,8,11,6,4,11,4,5,9,8,6,7,0,8,6,3,5,7,9,8,4,9,9,7,3,10,6,2,3,10,8,5,8,7,3,5]]},"set4":{"reels":[[11,9,8,12,2,5,10,8,1,11,6,6,7,9,7,12,9,10,6,7,2,9,1,8,6,5,2,7,12,7,3,11,6,11,0,6,6,11,8,0,7,3,8,1,12,10,4,4,9,11,8,9,11,12,10,9,11,4,5,0,8,10,9,12,5,7,7,12,8,9,3,0,6,6,10,4,6,10,1,10,4,8,10,4,12,4,10,9,0,3,3,2,10,8,5,12,3,7,0,7,8,5,9,5,8,9,12,1,5,11,9,2,4,0,8,6,11,2,7,11],[10,7,9,3,1,10,9,8,11,3,2,11,9,6,4,4,7,8,1,9,0,7,1,9,7,9,5,7,11,9,0,2,8,4,11,3,10,6,1,5,3,9,9,2,10,0,11,7,1,3,10,6,2,7,9,8,6,11,4,6,6,5,8,4,3,10,8,0,5,6,11,8,2,9,6,5,7,10,8,11,5,4,7,4,2,5,9,0,7,8,11,5,4,6,3,2,8,5,4,6,7,10,8,11,6,0,10,9,8,1,10,0,8,3,10,7,11,6,4,5],[10,11,4,4,3,8,12,2,6,8,5,6,0,8,9,12,11,3,6,9,7,3,6,1,3,6,2,12,8,9,4,6,11,6,7,12,0,3,7,5,1,11,5,7,2,6,1,7,10,7,2,7,5,7,8,0,2,11,4,5,12,6,8,5,9,0,10,1,8,5,7,3,9,9,12,3,0,2,7,10,6,3,9,10,12,4,8,6,1,10,4,11,0,2,1,5,8,9,12,9,9,8,1,3,11,12,10,8,9,11,8,12,5,7,4,0,4,10,5,4],[6,11,5,6,1,8,4,7,6,4,6,5,6,5,8,6,3,7,8,11,0,5,2,6,11,3,0,3,9,5,4,2,6,9,4,7,3,4,8,7,6,1,9,9,2,9,10,7,2,4,4,0,3,2,9,9,1,2,8,6,11,5,11,10,0,3,7,3,1,8,10,7,9,1,8,10,8,4,0,7,5,7,1,3,11,1,2,3,7,6,7,4,7,9,6,0,5,9,10,8,10,4,3,10,6,11,5,10,4,8,0,11,8,5,8,9,2,7,5,1],[5,8,2,4,5,0,6,5,12,3,0,7,10,5,12,4,9,0,11,7,9,7,2,10,6,12,5,7,11,6,12,5,6,2,1,2,6,4,3,1,10,9,11,0,3,11,12,6,7,7,9,10,1,0,4,2,5,1,8,10,4,11,12,1,4,8,3,7,3,4,5,2,10,3,0,4,7,1,6,2,11,4,5,1,0,8,11,6,4,12,4,5,9,8,6,7,12,8,6,3,5,7,9,8,12,9,9,1,3,10,6,2,6,12,8,5,8,7,3,5]]},"set5":{"reels":[[11,9,8,10,2,5,10,8,10,0,6,6,7,9,7,4,9,10,6,7,2,9,10,8,6,5,11,7,5,7,3,11,6,11,2,6,6,11,8,0,7,11,8,3,7,10,11,4,9,11,8,9,11,6,10,0,11,4,5,9,8,10,9,8,5,7,7,3,0,9,3,2,6,6,10,4,6,10,4,10,4,8,10,4,5,4,10,9,0,3,3,2,10,8,5,4,3,7,3,7,8,5,9,5,8,9,7,4,5,11,9,2,5,11,8,6,11,2,7,11],[10,7,9,3,2,3,9,8,11,3,2,10,9,6,4,4,7,8,10,9,11,7,0,9,7,9,5,7,11,9,4,2,8,4,0,3,10,6,11,5,3,9,9,2,10,2,11,7,10,3,10,6,2,7,9,8,4,11,4,6,6,5,8,4,3,10,8,11,5,6,11,8,2,9,6,0,7,10,8,11,5,6,7,4,2,5,9,11,7,8,11,5,4,6,3,5,8,5,4,6,7,10,8,11,6,10,0,9,8,5,10,3,10,3,0,7,11,6,4,5],[10,11,4,4,3,8,10,2,6,8,5,6,0,8,9,10,11,3,6,9,7,3,6,3,3,6,2,3,8,9,4,6,11,6,7,11,2,3,7,5,0,11,9,7,2,6,7,7,10,7,2,7,4,7,8,3,2,0,10,9,5,6,8,2,9,11,10,5,8,5,7,0,9,9,5,10,11,2,7,10,6,3,9,10,5,4,8,6,11,10,4,11,4,2,10,5,8,9,0,9,9,8,11,3,11,4,10,8,9,11,8,5,5,7,4,11,4,10,5,4],[6,11,5,6,7,8,11,2,10,4,6,9,5,5,8,6,10,11,8,9,2,10,2,6,11,0,11,3,9,5,4,2,6,9,4,7,3,4,8,7,6,4,9,9,2,9,10,7,2,4,0,11,3,2,9,9,7,2,8,10,11,5,0,10,2,3,4,3,5,8,10,7,9,10,8,10,8,0,3,7,5,7,6,3,11,3,11,3,8,6,7,4,7,0,6,4,5,9,10,8,10,4,3,10,6,11,5,10,4,8,11,11,8,5,6,9,2,7,5,7],[5,8,2,4,5,11,6,5,9,3,2,7,10,5,8,0,9,6,11,7,9,7,2,10,6,3,5,7,11,6,2,5,6,2,5,2,6,4,0,3,10,9,11,8,3,11,9,6,7,7,9,10,4,2,4,2,5,6,8,10,4,0,3,5,4,8,3,7,3,4,5,2,10,3,0,4,7,4,6,2,11,4,3,10,4,8,11,6,4,11,4,5,9,8,6,7,0,8,6,3,5,7,9,8,4,9,9,7,3,10,6,2,3,10,8,5,8,7,3,5]]},"beerDisplayedSet":{"reels":[[11,9,8,10,2,5,10,8,10,11,6,1,7,9,7,4,9,10,6,7,2,9,10,8,6,5,11,7,1,7,3,11,6,11,2,6,6,11,8,0,7,11,8,1,7,10,11,4,9,11,8,9,11,1,10,9,11,4,5,9,8,10,9,8,5,7,7,3,0,9,3,2,6,6,10,4,6,10,1,10,4,8,10,4,5,4,10,9,0,3,3,2,10,8,1,4,3,7,1,7,8,5,9,5,8,9,1,4,5,11,9,2,5,11,8,6,11,2,7,11],[10,7,9,3,1,3,9,8,11,1,2,10,9,6,4,4,7,8,10,9,11,1,0,9,7,9,5,7,1,9,4,2,8,4,11,3,10,6,11,5,3,9,9,2,10,2,11,7,10,3,10,6,2,7,9,8,1,11,4,6,6,5,8,4,3,10,8,1,5,6,11,8,2,9,6,0,7,10,8,11,5,1,7,4,2,5,9,11,7,8,11,5,4,6,3,5,8,5,4,6,7,10,8,11,6,10,0,9,1,5,10,3,10,3,10,7,11,6,4,5],[10,11,4,4,3,8,10,2,6,8,5,6,0,8,9,10,11,3,6,9,7,3,6,1,3,6,2,3,8,9,4,6,11,6,7,11,2,3,7,5,0,11,1,7,2,6,1,7,10,7,2,7,4,7,8,3,2,11,10,9,5,6,8,2,9,11,1,5,1,5,7,0,9,9,5,10,11,2,7,10,6,3,9,10,1,4,8,6,1,10,4,11,4,2,10,5,8,9,0,9,9,8,11,3,11,1,10,8,9,11,8,5,5,7,4,11,4,10,5,4],[6,11,5,6,7,8,1,2,10,4,6,9,5,5,8,6,10,11,8,9,1,11,2,6,11,0,11,3,9,5,4,2,6,9,4,7,3,4,8,7,6,1,9,9,2,9,10,7,2,4,0,11,3,2,9,9,7,2,8,10,11,5,11,10,2,3,4,3,5,8,10,7,9,10,8,10,8,0,3,7,5,7,1,3,11,1,11,3,1,6,7,4,7,0,6,4,5,9,10,8,10,4,3,10,6,11,5,10,4,8,1,11,8,5,6,9,2,7,5,1],[5,8,2,4,5,11,6,5,9,3,2,7,10,5,8,0,9,6,11,7,9,7,2,10,6,3,5,7,11,6,1,5,6,2,1,2,6,4,0,1,10,9,11,8,3,11,9,6,7,7,9,10,1,2,4,2,5,1,8,10,4,11,3,1,4,8,3,7,3,4,5,2,10,3,0,4,7,1,6,2,11,4,3,10,4,8,11,6,4,11,4,5,9,8,6,7,0,8,6,3,5,7,9,8,1,9,9,7,3,10,6,2,3,10,8,5,8,7,3,5]]}},"reels":{"set":"set1","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"linesDefinition":{"fixedMultiplierForTotalBet":30,"lineSelectionType":"fixed"},"paytable":{"stake":{"value":1,"multiplier":1,"payouts":[[0,0,100,500,1500],[0,0,100,500,1500],[0,0,80,400,1200],[0,0,60,300,1000],[0,0,55,200,500],[0,0,45,150,400],[0,0,35,100,300],[0,0,25,50,200],[0,0,20,40,80],[0,0,15,30,60],[0,0,10,20,40],[0,0,5,10,20]]}},"lines":[[1,1,1,1,1],[0,0,0,0,0],[2,2,2,2,2],[0,1,2,1,0],[2,1,0,1,2],[0,0,1,0,0],[2,2,1,2,2],[1,0,0,0,1],[1,2,2,2,1],[1,0,1,0,1],[1,2,1,2,1],[0,1,0,1,0],[2,1,2,1,2],[1,1,0,1,1],[1,1,2,1,1],[0,1,1,1,0],[2,1,1,1,2],[0,1,2,2,2],[2,1,0,0,0],[0,2,0,2,0],[2,0,2,0,2],[1,0,2,0,1],[1,2,0,2,1],[0,0,1,2,2],[2,2,1,0,0],[0,0,0,0,1],[2,2,2,2,1],[1,1,1,1,0],[1,1,1,1,2],[0,0,0,1,2]]},"stake":null,"defaultResult":{"events":[],"request":"spin","scene":"main","rewards":[],"reels":{"set":"set1","positions":[' . $rp1 . '],"view":[' . $rp2 . ']},"roundEnded":true,"state":{"currentScene":"main","multiplier":1},"totalBet":0,"totalWin":0,"multiplier":1},"version":"1.1.1"},"gameSettings":{},"brandSettings":{"fullscreen":true},"roundEnded":true}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetUserCards' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = $slotSettings->GetGameData('LepryBunnyPatrickSWDealerCard');
                            $totalWin = $slotSettings->GetGameData('LepryBunnyPatrickSWTotalWin');
                            $gambleWin = 0;
                            $gambleChoice = $postData['gambleChoice'] - 2;
                            $gambleState = '';
                            $gambleCards = [
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
                            $gambleSuits = [
                                'C', 
                                'S', 
                                'D', 
                                'H'
                            ];
                            $gambleId = [
                                '', 
                                '', 
                                '2', 
                                '3', 
                                '4', 
                                '5', 
                                '6', 
                                '7', 
                                '8', 
                                '9', 
                                '10', 
                                'J', 
                                'Q', 
                                'K', 
                                'A'
                            ];
                            $userCard = 0;
                            if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                            {
                                $isGambleWin = 0;
                            }
                            if( $isGambleWin == 1 ) 
                            {
                                $userCard = rand($dealerCard, 14);
                            }
                            else
                            {
                                $userCard = rand(2, $dealerCard);
                            }
                            if( $dealerCard < $userCard ) 
                            {
                                $gambleWin = $totalWin;
                                $totalWin = $totalWin * 2;
                                $gambleState = 'win';
                            }
                            else if( $userCard < $dealerCard ) 
                            {
                                $gambleWin = -1 * $totalWin;
                                $totalWin = 0;
                                $gambleState = 'lose';
                            }
                            else
                            {
                                $gambleWin = $totalWin;
                                $totalWin = $totalWin;
                                $gambleState = 'draw';
                            }
                            if( $gambleWin != $totalWin ) 
                            {
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            }
                            $afterBalance = $slotSettings->GetBalance();
                            $userCards = [
                                rand(2, 14), 
                                rand(2, 14), 
                                rand(2, 14), 
                                rand(2, 14)
                            ];
                            $userCards[$gambleChoice] = $userCard;
                            for( $i = 0; $i < 4; $i++ ) 
                            {
                                $userCards[$i] = '"' . $gambleId[$userCards[$i]] . $gambleSuits[rand(0, 3)] . '"';
                            }
                            $userCardsStr = implode(',', $userCards);
                            $slotSettings->SetGameData('LepryBunnyPatrickSWTotalWin', $totalWin);
                            $jsSet = '{"dealerCard":"' . $dealerCard . '","playerCards":[' . $userCardsStr . '],"gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","deb":' . $userCards[$gambleChoice] . ',"serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'gamble5GetDealerCard' ) 
                        {
                            $gambleCards = [
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
                            $gambleId = [
                                '', 
                                '', 
                                '2', 
                                '3', 
                                '4', 
                                '5', 
                                '6', 
                                '7', 
                                '8', 
                                '9', 
                                '10', 
                                'J', 
                                'Q', 
                                'K', 
                                'A'
                            ];
                            $gambleSuits = [
                                'C', 
                                'S', 
                                'D', 
                                'H'
                            ];
                            $tmpDc = $gambleCards[rand(0, 12)];
                            $slotSettings->SetGameData('LepryBunnyPatrickSWDealerCard', $tmpDc);
                            $dealerCard = $gambleId[$tmpDc] . $gambleSuits[rand(0, 3)];
                            $jsSet = '{"dealerCard":"' . $dealerCard . '"}';
                            $response = '{"responseEvent":"gamble5DealerCard","serverResponse":' . $jsSet . '}';
                        }
                        else if( $postData['slotEvent'] == 'slotGamble' ) 
                        {
                            $Balance = $slotSettings->GetBalance();
                            $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                            $dealerCard = '';
                            $totalWin = $slotSettings->GetGameData('LepryBunnyPatrickSWTotalWin');
                            $gambleWin = 0;
                            $statBet = $totalWin;
                            if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                            {
                                $isGambleWin = 0;
                            }
                            if( $isGambleWin == 1 ) 
                            {
                                $gambleState = 'win';
                                $gambleWin = $totalWin;
                                $totalWin = $totalWin * 2;
                                if( $postData['gambleChoice'] == 'red' ) 
                                {
                                    $tmpCards = [
                                        'D', 
                                        'H'
                                    ];
                                    $dealerCard = $tmpCards[rand(0, 1)];
                                }
                                else
                                {
                                    $tmpCards = [
                                        'C', 
                                        'S'
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
                                        'C', 
                                        'S'
                                    ];
                                    $dealerCard = $tmpCards[rand(0, 1)];
                                }
                                else
                                {
                                    $tmpCards = [
                                        'D', 
                                        'H'
                                    ];
                                    $dealerCard = $tmpCards[rand(0, 1)];
                                }
                            }
                            $slotSettings->SetGameData('LepryBunnyPatrickSWTotalWin', $totalWin);
                            $slotSettings->SetBalance($gambleWin);
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                            $afterBalance = $slotSettings->GetBalance();
                            $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                            $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                            $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, $postData['slotEvent']);
                        }
                        else if( $postData['slotEvent'] == 'spin' || $postData['slotEvent'] == 'freespin' ) 
                        {
                            $linesId = [];
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
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
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
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[17] = [
                                1, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[18] = [
                                3, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[20] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[21] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[22] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[23] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[24] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[25] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[26] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[27] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[28] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[29] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                3
                            ];
                            $postData['slotBet'] = $postData['bet'];
                            $postData['slotLines'] = $postData['lines'];
                            $allbet = $postData['slotBet'] * $postData['slotLines'];
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['slotBet'] * $postData['slotLines'], $postData['slotLines']);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $postData['slotEvent'] == 'freespin' && $winType == 'bonus' ) 
                            {
                                $winType = 'win';
                            }
                            if( $winType == 'bonus' ) 
                            {
                                $bonusType = rand(1, 2);
                                if( $bonusType == 1 ) 
                                {
                                    $winType = 'bonus2';
                                }
                            }
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
                                $slotSettings->SetGameData('LepryBunnyPatrickSWBonusWin', 0);
                                $slotSettings->SetGameData('LepryBunnyPatrickSWFreeGames', 0);
                                $slotSettings->SetGameData('LepryBunnyPatrickSWCurrentFreeGame', 0);
                                $slotSettings->SetGameData('LepryBunnyPatrickSWTotalWin', 0);
                                $slotSettings->SetGameData('LepryBunnyPatrickSWFreeBalance', 0);
                                $slotSettings->SetGameData('LepryBunnyPatrickSWrespinReels', [
                                    0, 
                                    0, 
                                    0
                                ]);
                            }
                            else
                            {
                                $slotSettings->SetGameData('LepryBunnyPatrickSWCurrentFreeGame', $slotSettings->GetGameData('LepryBunnyPatrickSWCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $Balance = $slotSettings->GetBalance();
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
                                    0
                                ];
                                $wild = ['0'];
                                $scatter = '12';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $reelsTmp = $reels;
                                $randomWilds = rand(1, 200);
                                if( $winType != 'bonus2' && $winType != 'bonus' && $randomWilds == 1 ) 
                                {
                                    $rwCnt = rand(2, 4);
                                    $rwArr = [
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5
                                    ];
                                    $rwReels = [];
                                    shuffle($rwArr);
                                    for( $rw = 0; $rw < $rwCnt; $rw++ ) 
                                    {
                                        $rwc = $rwArr[$rw];
                                        $reels['reel' . $rwc][0] = 0;
                                        $reels['reel' . $rwc][1] = 0;
                                        $reels['reel' . $rwc][2] = 0;
                                        $rwReels[] = $rwc - 1;
                                    }
                                }
                                else
                                {
                                    $randomWilds = 0;
                                }
                                for( $k = 0; $k < $postData['slotLines']; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || $csym == $wild ) 
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
                                            if( $s[0] == $csym ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable[$csym][1] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',0]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = 0;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][2] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',1]}';
                                                }
                                            }
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
                                                    $mpl = 0;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][3] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',2]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = 0;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][4] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',3]}';
                                                }
                                            }
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = 0;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable[$csym][5] * $postData['slotBet'] * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"reward":"line","direction":"LEFT_TO_RIGHT","lineId":' . $k . ',"payout":' . $cWins[$k] . ',"lineMultiplier":' . $mpl . ',"paytable":[' . $csym . ',4]}';
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
                                $scattersStr = '{';
                                $scattersCount = 0;
                                $scattersStrArr = [];
                                $respinReels = [
                                    0, 
                                    0, 
                                    0
                                ];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            if( $postData['slotEvent'] != 'freespin' ) 
                                            {
                                                $scattersCount++;
                                            }
                                            $respinReels[$r - 2] = 1;
                                            $scattersStrArr[] = '[' . $p . ',' . ($r - 1) . ']';
                                        }
                                    }
                                }
                                $scattersWin = 0;
                                if( $scattersCount >= 1 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr .= '"scattersType":"win",';
                                    $tmpStringWin = '{"reward":"scatter","payout":' . $scattersWin . ',"lineMultiplier":1,"positions":[' . implode(',', $scattersStrArr) . '],"paytable":[9,' . ($scattersCount - 1) . ']}';
                                    array_push($lineWins, $tmpStringWin);
                                }
                                else
                                {
                                    $scattersStr .= '"scattersType":"none",';
                                }
                                $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                                $totalWin += $scattersWin;
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 1200 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['slotBet']) ) 
                                    {
                                    }
                                    else
                                    {
                                        $bonusPickWin = 0;
                                        if( $winType == 'bonus2' ) 
                                        {
                                            $b2pw = [
                                                1, 
                                                2, 
                                                3, 
                                                4, 
                                                1, 
                                                2, 
                                                3, 
                                                4, 
                                                5, 
                                                10, 
                                                15, 
                                                20, 
                                                25, 
                                                5, 
                                                10, 
                                                15, 
                                                20, 
                                                25, 
                                                5, 
                                                10, 
                                                15, 
                                                20, 
                                                25, 
                                                50, 
                                                100, 
                                                50, 
                                                100
                                            ];
                                            shuffle($b2pw);
                                            $bonusPickWin = $b2pw[0] * ($postData['slotLines'] * $postData['slotBet']);
                                            $totalWin = $bonusPickWin;
                                        }
                                        if( $scattersCount >= 3 && $winType != 'bonus' ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $totalWin <= $spinWinLimit && $winType == 'bonus2' ) 
                                            {
                                                break;
                                            }
                                            if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
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
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                            }
                            $reportWin = $totalWin;
                            $currentScene = 'main';
                            $events = '';
                            $roundEnded = 'true';
                            $reels = $reelsTmp;
                            $currentScene = 'main';
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'slotLines', $postData['slotLines']);
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'slotBet', $postData['slotBet']);
                            if( $winType == 'bonus2' ) 
                            {
                                $events = '{"id":"bonusSelectionStart","roundsInfo":{"currentRoundId":0,"rounds":[{"id":0,"items":[{"id":0,"available":true},{"id":1,"available":true},{"id":2,"available":true}]}]}}';
                                $currentScene = 'bonus';
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'bonusPickWin', $bonusPickWin);
                            }
                            if( $randomWilds == 1 ) 
                            {
                                $events = '{"id":"wildExpanded","reels":[' . implode(',', $rwReels) . ']}';
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                $Balance = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'StartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $spinEvent = '';
                                $roundEnded = 'false';
                                $curScene = 'freeSpins';
                                $reelSet = 'freeSpins';
                            }
                            else
                            {
                                $spinState = '"currentScene":"main","multiplier":1';
                                $spinEvent = '';
                                $roundEnded = 'true';
                                $curScene = 'main';
                                $reelSet = 'main';
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                $reels0 = $slotSettings->GetReelStrips($reels['rp'], $postData['slotEvent']);
                                $rpTmp = '[' . $reels0['reel1'][0] . ',' . $reels0['reel2'][0] . ',' . $reels0['reel3'][0] . ',' . $reels0['reel4'][0] . ',' . $reels0['reel5'][0] . '],[' . $reels0['reel1'][1] . ',' . $reels0['reel2'][1] . ',' . $reels0['reel3'][1] . ',' . $reels0['reel4'][1] . ',' . $reels0['reel5'][1] . '],[' . $reels0['reel1'][2] . ',' . $reels0['reel2'][2] . ',' . $reels0['reel3'][2] . ',' . $reels0['reel4'][2] . ',' . $reels0['reel5'][2] . ']';
                                $slotSettings->SetGameData($slotSettings->slotId . 'StartBonusInfo', ',"reels":{"view":[' . $rpTmp . '],"positions":[' . implode(',', $reels['rp']) . ']}');
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'StartBonusWin', $totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount);
                                }
                                $events = '{"id":"freeSpinsStart","amount":' . $slotSettings->slotFreeCount . ',"reels":{"view":[' . $rpTmp . '],"set":"set1","positions":[' . implode(',', $reels['rp']) . ']},"triggeredSceneId":"freeSpins","triggerSymbols":[' . implode(',', $scattersStrArr) . ']}';
                                $spinState = '"currentScene":"freeSpins","multiplier":1,"freeSpinsCount":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"freeSpinsWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"initialFreeSpinWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'StartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') > 0 ) 
                            {
                                $trigR = $slotSettings->GetGameData($slotSettings->slotId . 'StartBonusInfo');
                                $spinState = '"currentScene":"main","multiplier":1,"freeSpinsWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeSpinsCount":0,"initialFreeSpinWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'StartBonusWin') . ',"initialFreeSpinsCount":' . $slotSettings->slotFreeCount . ',"totalFreeSpinsCount":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $events = '{"id":"freeSpinsEnd","reels":{"set":"set1","view":[[12,11,11,11,2],[4,5,4,3,12],[3,4,12,2,3]],"positions":[94,90,115,51,71]}}';
                                $roundEnded = 'true';
                                $curScene = 'freeSpins';
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            }
                            $response = '{"gameSession":"eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uSWQiOiIwIiwiaWQiOiJnYW1lczpjb250ZXh0OjQxOnBsYXllcjE1OTg0MTUzMjEyOTU6c3dfbGU6d2ViIiwiZ2FtZU1vZGUiOiJmdW4iLCJpYXQiOjE1OTg0MTUzNTcsImlzcyI6InNreXdpbmRncm91cCJ9.fvqyTerftnA_IxDJNPkIdqGVEfkkGFpqD45Zx9Cv9iAJ2B-MG-f2wVcWHqFYgRZGzu2dZQVIC3FBXgUQ0IyDig","balance":{"currency":"","amount":' . $slotSettings->GetBalance() . ',"real":{"amount":' . $Balance . '},"bonus":{"amount":0}},"result":{"request":"spin","stake":{"lines":30,"bet":' . $postData['slotBet'] . ',"coin":1},"totalBet":' . ($postData['slotBet'] * $postData['slotLines']) . ',"totalWin":' . $totalWin . ',"scene":"' . $curScene . '","multiplier":1,"state":{' . $spinState . '},"reels":{"set":"set1","positions":[' . implode(',', $reels['rp']) . '],"view":[[' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . '],[' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . '],[' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ']]},"rewards":[' . $winString . '],"events":[' . $events . '],"roundEnded":' . $roundEnded . ',"version":"1.1.1"},"requestId":1,"roundEnded":' . $roundEnded . '}';
                            $response_log = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","lastResponse":' . $response . ',"serverResponse":{"StartBonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'StartBonusWin') . ',"lines":' . $postData['slotLines'] . ',"bet":' . $postData['slotBet'] . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $Balance . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
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
