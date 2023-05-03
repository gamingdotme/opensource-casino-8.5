<?php 
namespace VanguardLTE\Games\PrimalHuntBS
{
    set_time_limit(5);
    use DB;
    use Auth;
    class Server
    {
        public function get($request, $game)
        {
            function get_($request, $game)
            {
                DB::transaction(function() use ($request, $game)
                {
                    try
                    {
                        $userId = Auth::id();
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
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$_POST['CMD'];
                        switch( $aid ) 
                        {
                            case 'ENTER':
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . ',' . $reels->reel1[3];
                                    $curReels .= ('|' . $reels->reel2[0] . ',' . $reels->reel2[1] . ',' . $reels->reel2[2] . ',' . $reels->reel2[3]);
                                    $curReels .= ('|' . $reels->reel3[0] . ',' . $reels->reel3[1] . ',' . $reels->reel3[2] . ',' . $reels->reel3[3]);
                                    $curReels .= ('|' . $reels->reel4[0] . ',' . $reels->reel4[1] . ',' . $reels->reel4[2] . ',' . $reels->reel4[3]);
                                    $curReels .= ('|' . $reels->reel5[0] . ',' . $reels->reel5[1] . ',' . $reels->reel5[2] . ',' . $reels->reel5[3]);
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet;
                                }
                                else
                                {
                                    $tr = (object)[
                                        'reel1' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel2' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel3' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel4' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ], 
                                        'reel5' => [
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6), 
                                            rand(1, 6)
                                        ]
                                    ];
                                    $bet = 0;
                                    $lines = 10;
                                    $curReels = '' . $tr->reel1[0] . ',' . $tr->reel1[1] . ',' . $tr->reel1[2] . ',' . $tr->reel1[3];
                                    $curReels .= ('|' . $tr->reel2[0] . ',' . $tr->reel2[1] . ',' . $tr->reel2[2] . ',' . $tr->reel2[3]);
                                    $curReels .= ('|' . $tr->reel3[0] . ',' . $tr->reel3[1] . ',' . $tr->reel3[2] . ',' . $tr->reel3[3]);
                                    $curReels .= ('|' . $tr->reel4[0] . ',' . $tr->reel4[1] . ',' . $tr->reel4[2] . ',' . $tr->reel4[3]);
                                    $curReels .= ('|' . $tr->reel5[0] . ',' . $tr->reel5[1] . ',' . $tr->reel5[2] . ',' . $tr->reel5[3]);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Betline', $bet);
                                $allbet = $slotSettings->Bet[$bet] * $lines;
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $betline = $slotSettings->Bet[$bet];
                                    $bwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $allbet);
                                    $fwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') / $allbet);
                                    $freeLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $result_tmp[] = 'RESULT=OK&BALANCE=' . $balanceInCents . '&game_mode=free&server_type=AP&LASTHAND=YES&FREESPIN=' . $freeLeft . '&STATE=FREESPIN&IBETVALUES=' . implode('', $slotSettings->Bet) . '&FREESPINPAYOUT=' . $bwCoin . '&LASTSTOPREEL=' . $curReels . '&COMPLEXWIN=' . $fwCoin . '&LASTBET=' . $bet . '|10|1&AUTOPLAY_VALUES=5|10|15|20|25|30|40|50|100&DEFCOIN=' . $bet . '&POSSIBLE_LINES=10&DEFAULTNUMLINES=10&DEFAULTBETPERLINE=1&FSCOUNT=8|12|20&IPAYOUT4=25|15|10|8|7|7|5|3|3|3|2&IPAYOUT3=5|8|7|5|4|4|3|2|2|2|1&IPAYOUT5=100|50|25|15|12|12|8|5|5|5|4&SCATTERPAYS=8|16|96&IPAYOUT2=1|0|0|0|0|0|0|0|0|0|0&BONUSBALANCE=0&GID=833&TIME=' . time() . '&END=0';
                                }
                                else
                                {
                                    $result_tmp[] = 'RESULT=OK&BALANCE=' . $balanceInCents . '&game_mode=free&server_type=AP&LASTHAND=NO&AUTOPLAY_VALUES=5|10|15|20|25|30|40|50|100&IBETVALUES=' . implode('', $slotSettings->Bet) . '&DEFCOIN=' . $bet . '&POSSIBLE_LINES=10&DEFAULTNUMLINES=10&DEFAULTBETPERLINE=1&FSCOUNT=8|12|20&IPAYOUT4=25|15|10|8|7|7|5|3|3|3|2&IPAYOUT3=5|8|7|5|4|4|3|2|2|2|1&IPAYOUT5=100|50|25|15|12|12|8|5|5|5|4&SCATTERPAYS=8|16|96&IPAYOUT2=1|0|0|0|0|0|0|0|0|0|0&INITIAL_STOPREEL=' . $curReels . '&BONUSBALANCE=0&GID=833&TIME=' . time() . '&END=0';
                                }
                                break;
                            case 'DOBONUS':
                            case 'PLACEBET':
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
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[5] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[6] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[7] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[8] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[9] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[10] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[11] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[12] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[13] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[14] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
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
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[18] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[19] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[20] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[21] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[22] = [
                                    1, 
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[23] = [
                                    4, 
                                    4, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $linesId[24] = [
                                    2, 
                                    2, 
                                    4, 
                                    2, 
                                    2
                                ];
                                $linesId[25] = [
                                    3, 
                                    3, 
                                    1, 
                                    3, 
                                    3
                                ];
                                $linesId[26] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[27] = [
                                    2, 
                                    4, 
                                    2, 
                                    4, 
                                    2
                                ];
                                $linesId[28] = [
                                    1, 
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[29] = [
                                    4, 
                                    2, 
                                    4, 
                                    2, 
                                    4
                                ];
                                $linesId[30] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $linesId[31] = [
                                    2, 
                                    4, 
                                    4, 
                                    4, 
                                    2
                                ];
                                $linesId[32] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[33] = [
                                    4, 
                                    2, 
                                    2, 
                                    2, 
                                    4
                                ];
                                $linesId[34] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[35] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[36] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[37] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[38] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[39] = [
                                    3, 
                                    4, 
                                    2, 
                                    4, 
                                    3
                                ];
                                $linesId[40] = [
                                    3, 
                                    2, 
                                    4, 
                                    2, 
                                    3
                                ];
                                $linesId[41] = [
                                    2, 
                                    3, 
                                    1, 
                                    3, 
                                    2
                                ];
                                $linesId[42] = [
                                    1, 
                                    3, 
                                    2, 
                                    3, 
                                    1
                                ];
                                $linesId[43] = [
                                    4, 
                                    2, 
                                    3, 
                                    2, 
                                    4
                                ];
                                $linesId[44] = [
                                    2, 
                                    4, 
                                    3, 
                                    4, 
                                    2
                                ];
                                $linesId[45] = [
                                    3, 
                                    1, 
                                    2, 
                                    1, 
                                    3
                                ];
                                $linesId[46] = [
                                    1, 
                                    4, 
                                    1, 
                                    4, 
                                    1
                                ];
                                $linesId[47] = [
                                    4, 
                                    1, 
                                    4, 
                                    1, 
                                    4
                                ];
                                $linesId[48] = [
                                    1, 
                                    1, 
                                    4, 
                                    1, 
                                    1
                                ];
                                $linesId[49] = [
                                    4, 
                                    4, 
                                    1, 
                                    4, 
                                    4
                                ];
                                $linesId[50] = [
                                    4, 
                                    1, 
                                    1, 
                                    1, 
                                    4
                                ];
                                $linesId[51] = [
                                    1, 
                                    4, 
                                    4, 
                                    4, 
                                    1
                                ];
                                $linesId[52] = [
                                    1, 
                                    2, 
                                    4, 
                                    2, 
                                    1
                                ];
                                $linesId[53] = [
                                    4, 
                                    3, 
                                    1, 
                                    3, 
                                    4
                                ];
                                $linesId[54] = [
                                    4, 
                                    2, 
                                    1, 
                                    2, 
                                    4
                                ];
                                $linesId[55] = [
                                    1, 
                                    3, 
                                    4, 
                                    3, 
                                    1
                                ];
                                $linesId[56] = [
                                    2, 
                                    1, 
                                    4, 
                                    1, 
                                    2
                                ];
                                $linesId[57] = [
                                    3, 
                                    4, 
                                    1, 
                                    4, 
                                    3
                                ];
                                $linesId[58] = [
                                    1, 
                                    4, 
                                    3, 
                                    4, 
                                    1
                                ];
                                $linesId[59] = [
                                    4, 
                                    1, 
                                    2, 
                                    1, 
                                    4
                                ];
                                $linesId[60] = [
                                    1, 
                                    4, 
                                    2, 
                                    4, 
                                    1
                                ];
                                $linesId[61] = [
                                    4, 
                                    1, 
                                    3, 
                                    1, 
                                    4
                                ];
                                $linesId[62] = [
                                    3, 
                                    1, 
                                    4, 
                                    1, 
                                    3
                                ];
                                $linesId[63] = [
                                    2, 
                                    4, 
                                    1, 
                                    4, 
                                    2
                                ];
                                $linesId[64] = [
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[65] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[66] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[67] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[68] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[69] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[70] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[71] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[72] = [
                                    2, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[73] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[74] = [
                                    2, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[75] = [
                                    3, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[76] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[77] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[78] = [
                                    2, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[79] = [
                                    3, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                if( $aid == 'DOBONUS' ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $be = $slotSettings->GetGameData($slotSettings->slotId . 'Betline');
                                    $betline = $slotSettings->Bet[$be];
                                }
                                else
                                {
                                    $tmpInputData = explode('|', $_POST['BET']);
                                    $lines = (int)$tmpInputData[1];
                                    $betline = $slotSettings->Bet[$tmpInputData[0]];
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Betline', $tmpInputData[0]);
                                    $postData['slotEvent'] = 'bet';
                                }
                                $allbet = $betline * $lines;
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                if( $postData['slotEvent'] == 'bet' ) 
                                {
                                    if( $lines <= 0 || $betline <= 0.0001 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->GetBalance() < $allbet ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bonus state"}';
                                        exit( $response );
                                    }
                                }
                                if( $postData['slotEvent'] != 'freespin' ) 
                                {
                                    if( !isset($postData['slotEvent']) ) 
                                    {
                                        $postData['slotEvent'] = 'bet';
                                    }
                                    $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                    $jackState = $slotSettings->UpdateJackpots($allbet);
                                    if( is_array($jackState) ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                    }
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
                                $symViewPosition = [
                                    [
                                        0, 
                                        4, 
                                        8, 
                                        12, 
                                        16
                                    ], 
                                    [
                                        1, 
                                        5, 
                                        9, 
                                        13, 
                                        17
                                    ], 
                                    [
                                        2, 
                                        6, 
                                        10, 
                                        14, 
                                        18
                                    ], 
                                    [
                                        3, 
                                        7, 
                                        11, 
                                        15, 
                                        19
                                    ]
                                ];
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
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $wild = [
                                        '11', 
                                        '12', 
                                        '13'
                                    ];
                                    $scatter = '14';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    for( $k = 0; $k < 80; $k++ ) 
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
                                                $svp = [];
                                                $svp[0] = $symViewPosition[$linesId[$k][0] - 1][0];
                                                $svp[1] = $symViewPosition[$linesId[$k][1] - 1][1];
                                                $svp[2] = $symViewPosition[$linesId[$k][2] - 1][2];
                                                $svp[3] = $symViewPosition[$linesId[$k][3] - 1][3];
                                                $svp[4] = $symViewPosition[$linesId[$k][4] - 1][4];
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '' . $csym . '-' . $slotSettings->Paytable['SYM_' . $csym][2] . '-' . ($k + 1) . '-' . $svp[0] . ',' . $svp[1];
                                                    }
                                                }
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '' . $csym . '-' . $slotSettings->Paytable['SYM_' . $csym][3] . '-' . ($k + 1) . '-' . $svp[0] . ',' . $svp[1] . ',' . $svp[2];
                                                    }
                                                }
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '' . $csym . '-' . $slotSettings->Paytable['SYM_' . $csym][4] . '-' . ($k + 1) . '-' . $svp[0] . ',' . $svp[1] . ',' . $svp[2] . ',' . $svp[3];
                                                    }
                                                }
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
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '' . $csym . '-' . $slotSettings->Paytable['SYM_' . $csym][5] . '-' . ($k + 1) . '-' . $svp[0] . ',' . $svp[1] . ',' . $svp[2] . ',' . $svp[3] . ',' . $svp[4];
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
                                    $scattersCount = 0;
                                    $scPos = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scPos[] = '' . ($r - 1) . ',' . $p . '';
                                            }
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    $sgwin = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $scattersStr = '';
                                    }
                                    $totalWin += $scattersWin;
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
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
                                        else if( $scattersCount >= 3 && $winType != 'bonus' ) 
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
                                    $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                }
                                $fs = 0;
                                $freeDataStr = '&STATE=MAIN&COMPLEXWIN=' . round($totalWin / $allbet);
                                if( $scattersCount >= 3 ) 
                                {
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    $bwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $allbet);
                                    $fwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') / $allbet);
                                    $freeLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $freeDataStr = '&FSADD=' . $slotSettings->slotFreeCount[$scattersCount] . '&FREESPIN=' . $freeLeft . '&SCATTERWIN=' . round($scattersWin / $allbet) . '&FREESPINPAYOUT=' . $bwCoin . '&STATE=FREESPIN';
                                    $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                }
                                $curReels = $reels['reel1'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel1'][3];
                                $curReels .= ('|' . $reels['reel2'][0] . ',' . $reels['reel2'][1] . ',' . $reels['reel2'][2] . ',' . $reels['reel2'][3]);
                                $curReels .= ('|' . $reels['reel3'][0] . ',' . $reels['reel3'][1] . ',' . $reels['reel3'][2] . ',' . $reels['reel3'][3]);
                                $curReels .= ('|' . $reels['reel4'][0] . ',' . $reels['reel4'][1] . ',' . $reels['reel4'][2] . ',' . $reels['reel4'][3]);
                                $curReels .= ('|' . $reels['reel5'][0] . ',' . $reels['reel5'][1] . ',' . $reels['reel5'][2] . ',' . $reels['reel5'][3]);
                                $winString = '';
                                if( count($lineWins) > 0 ) 
                                {
                                    $winString = '&PAYOUTS=' . implode('|', $lineWins);
                                }
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . '' . $slotSettings->GetGameData($slotSettings->slotId . 'FirstSpin') . ',"slotBet":' . $slotSettings->GetGameData($slotSettings->slotId . 'Betline') . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . ',"winLines":["' . $winString . '"],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                                if( $postData['slotEvent'] == 'freespin' && $winType != 'bonus' ) 
                                {
                                    $bwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') / $allbet);
                                    $fwCoin = round($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') / $allbet);
                                    $freeLeft = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                    {
                                        $freeDataStr = '&FREESPINPAYOUT=' . $bwCoin . '&STATE=MAIN&FSADD=0&COMPLEXWIN=' . $fwCoin . '&FREESPIN=0';
                                    }
                                    else
                                    {
                                        $freeDataStr = '&FREESPINPAYOUT=' . $bwCoin . '&STATE=FREESPIN&FSADD=0&COMPLEXWIN=' . $fwCoin . '&FREESPIN=' . $freeLeft;
                                    }
                                }
                                $result_tmp[0] = 'RESULT=OK&BALANCE=' . $balanceInCents . '&STOPREEL=' . $curReels . '&BONUSBALANCE=0&GID=833&TIME=' . time() . '&END=0' . $winString . $freeDataStr;
                                break;
                        }
                        $response = implode('------:::', $result_tmp);
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
