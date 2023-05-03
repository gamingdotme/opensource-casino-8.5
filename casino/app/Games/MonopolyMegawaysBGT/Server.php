<?php 
namespace VanguardLTE\Games\MonopolyMegawaysBGT
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
                        if( isset($_GET['command']) && $_GET['command'] == 'gettoken' ) 
                        {
                            exit( '{"rc":1008,"rcMessage":"ogsClientToken: "}' );
                        }
                        $xml = file_get_contents('php://input');
                        $xp0 = explode('<payload>', $xml);
                        if( !isset($xp0[1]) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid request"}';
                            exit( $response );
                        }
                        $xp1 = explode('</payload>', $xp0[1]);
                        $xp2 = explode(';', $xp1[0]);
                        $xmlPayload = [];
                        foreach( $xp2 as $par ) 
                        {
                            $tpar = explode('=', $par);
                            if( isset($tpar[1]) ) 
                            {
                                $tpar[1] = str_replace('&amp', '', $tpar[1]);
                            }
                            else
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid request"}';
                                exit( $response );
                            }
                            $xmlPayload[$tpar[0]] = $tpar[1];
                        }
                        $postData = $xmlPayload;
                        $result_tmp = [];
                        $aid = $postData['MSGID'];
                        $Board = [
                            [
                                'Go', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'cChest', 
                                1
                            ], 
                            [
                                'gHouse', 
                                2
                            ], 
                            [
                                'IncomeTax', 
                                1
                            ], 
                            [
                                'Station', 
                                0
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'Chance', 
                                1
                            ], 
                            [
                                'gHouse', 
                                2
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'Jail', 
                                1
                            ], 
                            [
                                'gHouse', 
                                2
                            ], 
                            [
                                'Util', 
                                0
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'Station', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'cChest', 
                                1
                            ], 
                            [
                                'gHouse', 
                                2
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'FreeParking', 
                                1
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'Chance', 
                                1
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'Station', 
                                2
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'gHouse', 
                                0
                            ], 
                            [
                                'Util', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'GoToJail', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'cChest', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'Station', 
                                3
                            ], 
                            [
                                'Chance', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ], 
                            [
                                'SuperTax', 
                                1
                            ], 
                            [
                                'gHouse', 
                                1
                            ]
                        ];
                        switch( $aid ) 
                        {
                            case 'FREE_GAME':
                                $CurrentLap = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentLap');
                                $BoardPositon = $slotSettings->GetGameData($slotSettings->slotId . 'BoardPositon');
                                $Stations = $slotSettings->GetGameData($slotSettings->slotId . 'Stations');
                                $Utilities = $slotSettings->GetGameData($slotSettings->slotId . 'Utilities');
                                $OPS = $slotSettings->GetGameData($slotSettings->slotId . 'OPS');
                                $OPS0 = $slotSettings->GetGameData($slotSettings->slotId . 'OPS0');
                                $MPS = $slotSettings->GetGameData($slotSettings->slotId . 'MPS');
                                $spins = $slotSettings->GetGameData($slotSettings->slotId . 'spins');
                                $spinsCnt = $slotSettings->GetGameData($slotSettings->slotId . 'spinsCnt');
                                $spinsCnt++;
                                $cSpin = json_decode($spins[$spinsCnt], true);
                                $balanceInCents = round($slotSettings->GetBalance() * 100);
                                $housesIncrease = [
                                    0, 
                                    0, 
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    5, 
                                    6
                                ];
                                $currentHouseIncrease = $housesIncrease[$spinsCnt];
                                if( $cSpin['winningsMoney'] > 0 ) 
                                {
                                    $diceValue = rand(1, 6);
                                    $diceValue = 6;
                                    $OPS0 = $MPS;
                                    array_unshift($OPS, 2);
                                    array_unshift($OPS, $OPS0[0]);
                                    $MPS[0] += $diceValue;
                                    if( $MPS[0] > 39 ) 
                                    {
                                        $MPS[0] = $MPS[0] - 39;
                                        $CurrentLap++;
                                    }
                                    $mtk = '';
                                    if( $Board[$MPS[0]][0] == 'cChest' ) 
                                    {
                                        $additionalStep = rand(1, 6);
                                        $newStep = $MPS[0] + $additionalStep;
                                        if( $newStep > 39 ) 
                                        {
                                            $newStep = $newStep - 39;
                                            $CurrentLap++;
                                        }
                                        $mtk = '#MTK~' . $MPS[0] . ';' . $newStep . '';
                                    }
                                    if( $Board[$MPS[0]][0] == 'Util' ) 
                                    {
                                        $Utilities[$Board[$MPS[0]][1]] = 1;
                                    }
                                    if( $Board[$MPS[0]][0] == 'Station' ) 
                                    {
                                        $Stations[$Board[$MPS[0]][1]] = 1;
                                    }
                                    if( $Board[$MPS[0]][0] == 'gHouse' ) 
                                    {
                                        $Board[$MPS[0]][1] += $currentHouseIncrease;
                                    }
                                    $MPS[2] = $CurrentLap;
                                    $OPS0[2] = $CurrentLap;
                                    $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=FREE_GAME&B=' . $balanceInCents . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&RID=0&NRID=0&BPR=5&RB=6&RS=' . implode('|', $cSpin['newReels']['rp']) . '&TW=' . $cSpin['totalWin'] . '&WC=1|0|1|' . $cSpin['curWinStr'] . '&CW=' . $cSpin['winningsMoney'] . '&NFG=1&FGT=1&TFG=' . ($spinsCnt + 1) . '&CFGG=' . $spinsCnt . '&FGTW=' . $cSpin['totalWin'] . '&IFG=1&FID=0|&MUL=1&SUB=0&GSD=FGP~' . $CurrentLap . '#OPS~' . implode(';', $OPS0) . ';' . implode(';', $OPS) . '#DPR~' . $cSpin['ws'] . $mtk . '#RTW~' . $cSpin['totalWin'] . '#STP~' . $diceValue . '#MPS~' . implode(';', $MPS) . '&Board=' . $Board[$MPS[0]][0] . '&GA=1&AB=' . $balanceInCents . '&FRBAL=0&Board=' . json_encode($Board) . '&SID=Free:udhq58uupa8aubcq7e8efv42nmo&]]></PAYLOAD></GDMRESPONSE>';
                                }
                                else
                                {
                                    $OPS0 = $MPS;
                                    array_unshift($OPS, 1);
                                    array_unshift($OPS, $OPS0[0]);
                                    $MPS[2] = $CurrentLap;
                                    $OPS0[2] = $CurrentLap;
                                    $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=FREE_GAME&B=' . $balanceInCents . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&RID=0&NRID=0&BPR=5&RB=6&RS=' . implode('|', $cSpin['newReels']['rp']) . '&TW=' . $cSpin['totalWin'] . '&WC=0|0|0|&CW=0&NFG=0&TFG=' . $spinsCnt . '&CFGG=' . $spinsCnt . '&FGTW=' . $cSpin['totalWin'] . '&IFG=1&FID=0|&MUL=1&SUB=0&GSD=FGP~' . $CurrentLap . '#OPS~' . implode(';', $OPS0) . ';' . implode(';', $OPS) . '#DPR~' . $cSpin['ws'] . '#RTW~' . $cSpin['totalWin'] . '#MPS~' . implode(';', $MPS) . '&GA=1&AB=' . $balanceInCents . '&FRBAL=0&SID=Free:udhq58uupa8aubcq7e8efv42nmo&]]></PAYLOAD></GDMRESPONSE>';
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentLap', $CurrentLap);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BoardPositon', $BoardPositon);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Stations', $Stations);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Utilities', $Utilities);
                                $slotSettings->SetGameData($slotSettings->slotId . 'spinsCnt', $spinsCnt);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS', $OPS);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS0', $OPS0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'MPS', $MPS);
                                break;
                            case 'INIT':
                                $gameBets = [];
                                for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                                {
                                    $gameBets[] = $slotSettings->Bet[$i] * 100;
                                }
                                $balanceInCents = round($slotSettings->GetBalance() * 100);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BoardPositon', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Stations', [
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Utilities', [
                                    0, 
                                    0
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS', [
                                    39, 
                                    1, 
                                    37, 
                                    1, 
                                    34, 
                                    2, 
                                    32, 
                                    1, 
                                    31, 
                                    1, 
                                    29, 
                                    2, 
                                    27, 
                                    0, 
                                    26, 
                                    1, 
                                    24, 
                                    1, 
                                    23, 
                                    0, 
                                    21, 
                                    0, 
                                    19, 
                                    0, 
                                    18, 
                                    2, 
                                    16, 
                                    1, 
                                    14, 
                                    1, 
                                    13, 
                                    0, 
                                    11, 
                                    2, 
                                    9, 
                                    0, 
                                    8, 
                                    2, 
                                    6, 
                                    1, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS0', [
                                    0, 
                                    0, 
                                    1, 
                                    0
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'MPS', [
                                    0, 
                                    0, 
                                    1, 
                                    0
                                ]);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentLap', 1);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * 100;
                                }
                                else
                                {
                                    $lines = 10;
                                    $bet = $slotSettings->Bet[0] * 100;
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                    $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $freeSpinsStr = '"freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "1", "totalMultiplier": "1" },';
                                }
                                else
                                {
                                    $freeSpinsStr = '';
                                }
                                $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=INIT&B=' . $balanceInCents . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&LIM=1|' . $gameBets[count($gameBets) - 1] . '|&RBM=20|20|20|20|20|20|&BD=' . implode('|', $gameBets) . '&BDD=' . $gameBets[0] . '&RSTM=1;0|&UGB=0&CUR=ISO:EUR|,|.|8364;|L|32;|R&GSD=GSI~1;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|2;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|3;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|4;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|5;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|6;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|8;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|10;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|15;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|16;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|20;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|25;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|30;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|40;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|50;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|60;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|80;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|100;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|150;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1|200;0;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1&GA=0&AB=' . $balanceInCents . '&FRBAL=0&SID=Free:og44di99njt64ut6v3fl9s2l6kn&]]></PAYLOAD></GDMRESPONSE>';
                                break;
                            case 'REELSTRIP':
                                $balanceInCents = round($slotSettings->GetBalance() * 100);
                                $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=REELSTRIP&B=' . $balanceInCents . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&ENC=0&RSIDS=0|2|1|&RC=6&RST_0=0>9;9;3;4;4;7;7;7;3;3;8;8;9;5;5;5;5;10;10;10;4;4;7;7;7;7;4;10;10;10;10;10;10;3;8;8;6;6;8;8;2;4;7;7;1;3;10;3;2;10;6;6;3;4;10;3;7;4;7;3;2;6;9;6;6;10;3;3;5;2;7;6;4;9;7;4;5;7;7;10;3;6;7;9;2;3;10;1;7;3;10;6;1;6;6;4;5;6;3;10;3;7;10;6;5;3;3;10;4;7;3;10;3;2;10;3;7;6;5;4;7;6;8;7;3;6;7;7;6;10;4;5;10;6;4;10;3;10;5;5;8;4;3;10;7;5;10;10;10;6;4;6;7;3;5;6;10;3;9;4;3;6;9;10;5;5;3;6;10|1>7;7;7;8;8;8;8;8;2;2;2;2;3;6;6;6;6;6;6;6;3;8;8;8;8;8;8;8;0;9;9;9;9;9;9;8;10;10;9;10;8;9;4;9;2;3;9;6;8;3;5;9;9;8;5;4;5;9;1;8;5;9;5;8;2;6;6;9;5;8;9;8;5;7;1;8;3;8;5;9;8;9;8;0;9;10;8;5;5;9;3;3;5;6;8;9;3;5;4;9;8;5;10;5;9;6;5;2;8;5;8;8;9;5;9;4;9;10;6;9;8;5;5;9;6;8;9;2;5;3;5;6;9;2;9;6;8;5;2;8;2;5;10;9;5;9;2;2;0;9;4;9;9;8;8;3;8;9;2;5;5;0;6;9;8;5;4;8;9;1;5;5;5;0;8;5;5|2>7;9;2;2;9;6;9;10;7;1;2;7;6;10;7;7;4;9;9;9;6;3;9;8;10;7;7;7;2;6;10;0;8;8;1;7;7;9;10;6;7;8;6;7;0;8;6;7;2;9;9;9;8;4;7;3;6;7;9;8;7;2;7;1;4;7;4;9;6;9;8;4;2;6;4;3;6;7;7;3;9;9;3;8;7;5;9;4;8;10;6;9;4;8;8;6;10;7;10;0;6;9;2;8;7;7;8;8;9;7;4;8;3;2;7;9;9;4;4;7;8;2;4;4;6;4;1;1;2;4;8;9;2;7;9;9;7;4;8;4;2;0;3;7;2;9;6;9;3;3;9;4;6;6;8;5;6;4;2;2;7;0;4;3;9;2;7|3>4;1;10;5;4;7;10;10;5;1;4;10;6;9;4;7;5;4;3;1;7;8;5;7;2;8;8;8;1;7;4;3;3;10;4;2;5;7;1;10;5;7;8;10;5;1;10;5;4;8;4;5;1;4;0;4;7;2;9;1;7;5;2;10;7;4;10;7;10;5;10;10;4;6;7;10;1;4;5;10;10;7;10;2;7;1;10;10;5;10;10;10;10;4;0;7;1;7;5;10;10;7;4;5;10;7;1;2;7;2;7;10;10;9;1;7;7;7;10;10;7;2;10;1;5;10;10;4;10;8;2;10;7;5;7;10;8;7;10;5;8;4;10;5;7;7;7;8;5;7;4;7;10;10;5;1;7;7;2;7;8;7;4;7;10;6;5;7;1;3;10;5;10;2;10;10;7|4>3;6;6;10;9;8;4;1;3;8;2;8;4;5;6;4;1;3;6;4;10;8;5;10;5;3;3;7;8;2;8;7;0;7;6;3;10;8;4;5;8;3;4;8;6;8;6;4;8;8;10;3;4;8;3;6;8;6;10;3;2;4;8;6;3;8;6;0;10;8;4;4;6;4;3;3;2;6;8;4;5;4;10;3;7;0;3;8;4;10;3;8;4;3;7;4;8;6;8;10;8;9;10;8;3;10;8;4;3;5;0;8;4;3;4;4;3;3;8;1;5;6;0;6;3;10;3;4;8;10;2;10;4;3;6;3;4;10;8;10;4;0;5;8;10;4;10;10;8;6;8;10;3;8;9;6;10;6;8;10;4;10;4;8;3;2;5;8;4;3;6;4;10;3;10;8;4|5>7;7;7;8;8;8;8;8;2;2;2;2;3;6;6;6;6;6;6;6;3;8;8;8;8;8;8;8;0;9;9;9;9;9;9;8;10;10;9;10;8;9;4;9;2;3;9;6;8;3;5;9;9;8;5;4;5;9;1;8;5;9;5;8;0;6;6;9;5;8;9;8;5;7;1;8;3;8;5;9;8;9;8;0;9;10;8;5;5;9;3;3;5;6;8;9;1;5;4;9;8;5;10;5;9;6;5;2;8;5;8;8;9;5;9;4;9;10;6;9;8;5;5;9;6;8;9;1;5;3;5;6;9;2;9;6;8;5;2;8;2;5;10;9;5;9;2;2;0;9;4;9;9;8;8;3;8;9;2;5;5;0;6;9;8;5;4;8;9;1;5;5;5;0;8;5;5|&RST_2=0>9;9;4;4;7;7;7;3;3;3;3;3;3;3;8;8;9;5;5;5;5;10;10;10;10;10;10;10;4;4;7;7;7;7;4;10;10;10;10;10;10;10;3;8;8;6;6;8;8;2;4;7;7;1;3;10;3;2;10;6;6;4;10;3;7;4;7;3;2;6;9;6;6;10;3;1;5;2;7;6;4;9;7;4;5;7;7;10;3;6;7;9;2;10;2;7;3;10;6;1;6;6;4;5;6;3;10;3;7;10;6;5;3;3;10;4;7;3;10;3;2;10;3;7;6;5;4;7;6;8;7;3;6;7;7;6;10;4;5;10;6;4;10;3;10;10;5;5;8;4;3;10;7;5;10;10;10;6;4;6;7;3;5;6;10;3;9;4;3;6;9;10;5;5;3;6;10|2>7;9;2;2;9;4;6;6;10;7;0;2;7;9;10;7;7;4;4;4;4;9;8;7;4;4;6;3;4;9;6;4;10;7;7;4;7;2;9;10;0;4;9;9;1;7;7;9;10;8;7;8;6;7;1;8;6;7;2;6;6;9;9;4;7;3;6;7;9;8;7;2;7;0;4;7;4;9;8;9;6;4;2;6;4;3;6;7;7;3;6;9;0;8;7;5;9;4;9;10;6;9;4;8;8;8;10;7;10;0;8;9;2;8;7;7;8;8;6;7;4;8;3;2;7;9;9;4;4;7;6;0;4;4;8;4;1;1;2;4;8;9;2;7;9;9;7;4;6;4;2;9;3;7;2;9;6;9;3;3;9;4;8;8;8;5;6;4;2;2;7;0;4;3;9;2;7|5>4;10;10;8;6;7;8;2;5;8;6;2;9;9;9;4;6;6;6;9;5;9;2;4;6;5;7;3;6;6;7;5;9;7;7;9;2;8;10;6;6;5;6;10;9;6;2;6;9;8;7;3;3;2;9;6;9;9;3;7;7;3;6;9;7;3;7;9;9;1;7;5;6;9;10;2;4;6;0;10;9;7;1;2;4;8;9;6;5;9;8;5;7;10;9;9;10;9;6;7;8;9;6;6;2;9;9;9;6;6;10;10;8;6;9;10;3;6;1;10;6;9;9;7;3;5;7;9;5;6;2;9;1;6;9;5;6;7;6;10;6;7;9;7;7;5;6;6;7;8;7;6;9;5;6;9;10;7;7;7;7;9;9;9;3;7;1;9;2;6;8;7;4;6;2;9;7|&RSC_2=1>20:9;64:0;78:6;96:2;107:0;127:0;128:6;131:5;|3>31:2;94:2;114:2;|4>67:8;85:8;110:1;119:2;122:4;130:3;141:8;|&RST_1=0>9;9;4;4;7;7;7;3;3;3;3;3;3;3;8;8;9;5;5;5;5;10;10;10;10;10;10;10;4;4;7;7;7;7;4;10;10;10;10;10;10;10;3;8;8;6;6;8;8;2;4;7;7;1;3;10;3;2;10;6;6;4;10;3;7;4;7;3;2;6;9;6;6;10;3;1;5;2;7;6;4;9;7;4;5;7;7;10;3;6;7;9;2;10;2;7;3;10;6;3;6;6;4;5;6;3;10;3;7;10;6;5;3;3;10;4;7;3;10;3;2;10;3;7;6;5;4;7;6;8;7;3;6;7;7;6;10;4;5;10;6;4;10;3;10;10;5;5;8;4;3;10;7;5;10;10;10;6;4;6;7;3;5;6;10;3;9;4;3;6;9;10;5;5;3;6;10|2>7;9;0;2;9;4;3;8;10;7;0;2;7;3;10;7;7;4;4;4;4;9;6;1;4;4;6;3;4;9;6;0;10;7;7;4;7;2;8;10;0;4;9;9;2;7;7;9;10;3;7;8;9;7;0;8;6;7;4;9;9;9;0;4;7;3;8;7;9;8;7;2;7;1;4;7;4;9;8;9;9;4;2;6;0;3;6;7;7;3;6;9;0;8;7;3;9;4;5;10;3;9;0;8;8;3;10;7;10;2;9;9;2;8;7;7;8;8;0;7;4;8;3;2;7;8;8;4;4;7;6;0;4;4;9;4;1;1;2;4;6;9;2;7;6;3;7;4;9;4;2;0;3;7;2;9;8;9;3;3;9;4;6;3;8;5;9;4;2;2;7;0;4;3;9;2;7|5>4;10;10;8;6;7;8;2;5;8;6;2;9;9;9;4;6;6;6;9;5;9;2;4;6;5;7;3;6;6;7;5;9;7;7;9;2;8;10;6;6;5;6;10;9;6;2;6;9;8;7;3;3;2;9;6;9;9;3;7;7;3;6;9;7;3;7;9;9;1;7;5;6;9;10;2;4;6;0;10;9;7;1;2;4;8;9;6;5;9;8;5;7;10;9;9;10;9;6;7;8;9;6;6;1;9;9;9;6;6;10;10;8;6;9;10;3;6;1;10;6;9;9;7;3;5;7;9;5;6;2;9;1;6;9;5;6;7;6;10;6;7;9;7;7;5;6;6;7;8;7;6;9;5;6;9;10;7;7;7;7;9;9;9;3;7;1;9;2;6;8;7;4;6;2;9;7|&RSC_1=1>7:0;20:0;37:2;44:10;47:0;58:3;64:9;93:8;95:0;96:2;105:9;107:0;116:0;127:0;128:6;131:5;138:8;139:2;146:9;|3>31:2;94:2;|4>67:8;85:8;110:1;119:2;122:4;130:3;141:8;|&GA=0&AB=' . $balanceInCents . '&FRBAL=0&SID=Free:udhq58uupa8aubcq7e8efv42nmo&]]></PAYLOAD></GDMRESPONSE>';
                                break;
                            case 'BET':
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
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[16] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[17] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[18] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[19] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $lines = 20;
                                $betline = (trim($postData['BPR']) * $lines) / 100;
                                $betlineRaw = trim($postData['BPR']);
                                $allbet = $betline;
                                $postData['slotEvent'] = 'bet';
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $slotSettings->GetBalance() < $allbet && $postData['slotEvent'] == 'bet' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance "}';
                                    exit( $response );
                                }
                                if( $allbet <= 0 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet "}';
                                    exit( $response );
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
                                if( $winType == 'bonus' && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $winType = 'none';
                                }
                                $balanceInCentsStart = round($slotSettings->GetBalance() * 100);
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $CurrentLap = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentLap');
                                    $BoardPositon = $slotSettings->GetGameData($slotSettings->slotId . 'BoardPositon');
                                    $Stations = $slotSettings->GetGameData($slotSettings->slotId . 'Stations');
                                    $Utilities = $slotSettings->GetGameData($slotSettings->slotId . 'Utilities');
                                    $OPS = $slotSettings->GetGameData($slotSettings->slotId . 'OPS');
                                    $OPS0 = $slotSettings->GetGameData($slotSettings->slotId . 'OPS0');
                                    $MPS = $slotSettings->GetGameData($slotSettings->slotId . 'MPS');
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
                                        0
                                    ];
                                    $wild = '0';
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsRaw = $reels;
                                    $ways = $reels['ways'];
                                    $waysCount = 0;
                                    $spins = [];
                                    for( $rs = 0; $rs <= 50; $rs++ ) 
                                    {
                                        if( $rs == 0 ) 
                                        {
                                            $newReels = $reels;
                                            $curSpin = $slotSettings->GetReelsWin($reels, 20, $betline, $betlineRaw, $linesId, $cWins, $rs);
                                        }
                                        else
                                        {
                                            $newReels = $slotSettings->OffsetReels($curSpin['reelsOffset']);
                                            $curSpin = $slotSettings->GetReelsWin($newReels, 20, $betline, $betlineRaw, $linesId, $cWins, $rs);
                                        }
                                        if( $curSpin['totalWin'] <= 0 ) 
                                        {
                                            $reels = $slotSettings->OffsetReels($curSpin['reelsOffset']);
                                            $curWinStr = '"&WC=0|0|0|"';
                                            $spins[] = '{ "symbols": { "line": [ ] }, "lines": { "line":[  ]},"totalWin":' . ($totalWin * 100) . ', "combo": "' . $rs . '", "winnings": "' . (($curSpin['totalWin'] * 100) / $betlineRaw) . '","newReels":' . json_encode($newReels) . ',"ws":"' . implode(';', $reelsRaw['ws']) . '", "winningsMoney": "' . ($curSpin['totalWin'] * 100) . '","curWinStr":' . $curWinStr . ', "money": "' . $balanceInCentsStart . '" }';
                                            break;
                                        }
                                        $curReels = '';
                                        $curReels .= ('"' . $newReels['reel1'][0] . '-' . $newReels['reel2'][0] . '-' . $newReels['reel3'][0] . '-' . $newReels['reel4'][0] . '-' . $newReels['reel5'][0] . '-' . $newReels['reel6'][0] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][1] . '-' . $newReels['reel2'][1] . '-' . $newReels['reel3'][1] . '-' . $newReels['reel4'][1] . '-' . $newReels['reel5'][1] . '-' . $newReels['reel6'][1] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][2] . '-' . $newReels['reel2'][2] . '-' . $newReels['reel3'][2] . '-' . $newReels['reel4'][2] . '-' . $newReels['reel5'][2] . '-' . $newReels['reel6'][2] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][3] . '-' . $newReels['reel2'][3] . '-' . $newReels['reel3'][3] . '-' . $newReels['reel4'][3] . '-' . $newReels['reel5'][3] . '-' . $newReels['reel6'][3] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][4] . '-' . $newReels['reel2'][4] . '-' . $newReels['reel3'][4] . '-' . $newReels['reel4'][4] . '-' . $newReels['reel5'][4] . '-' . $newReels['reel6'][4] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][5] . '-' . $newReels['reel2'][5] . '-' . $newReels['reel3'][5] . '-' . $newReels['reel4'][5] . '-' . $newReels['reel5'][5] . '-' . $newReels['reel6'][5] . '"');
                                        $curReels .= (',"' . $newReels['reel1'][6] . '-' . $newReels['reel2'][6] . '-' . $newReels['reel3'][6] . '-' . $newReels['reel4'][6] . '-' . $newReels['reel5'][6] . '-' . $newReels['reel6'][6] . '"');
                                        $curWinStr = '"&WC=1|0|1|&WS=' . $curSpin['formatedWins'] . '|&WM=' . $curSpin['formatedWins_'] . '|"';
                                        $totalWin += $curSpin['totalWin'];
                                        $spins[] = '{ "symbols": { "line": [ ' . $curReels . ' ] },"totalWin":' . ($totalWin * 100) . ', "lines": { "line":[ ' . implode(',', $curSpin['lineWins']) . ' ]}, "combo": "' . $rs . '", "winnings": "' . (($curSpin['totalWin'] * 100) / $betlineRaw) . '","ws":"' . implode(';', $reelsRaw['ws']) . '","newReels":' . json_encode($newReels) . ', "winningsMoney": "' . ($curSpin['totalWin'] * 100) . '","curWinStr":' . $curWinStr . ', "money": "' . $balanceInCentsStart . '" }';
                                    }
                                    $spinsStr = '';
                                    if( $rs > 0 ) 
                                    {
                                        $spinsStr = ',"spins": { "spin": [' . implode(',', $spins) . '] }';
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scPos = [];
                                    $allScattersWin = 0;
                                    $allScattersWinTempl = [
                                        1, 
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
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3, 
                                        3, 
                                        3, 
                                        4, 
                                        4, 
                                        4, 
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        8, 
                                        8, 
                                        8, 
                                        8, 
                                        10, 
                                        10, 
                                        10, 
                                        15, 
                                        15, 
                                        15, 
                                        20, 
                                        20, 
                                        20, 
                                        25, 
                                        25, 
                                        25, 
                                        30, 
                                        100, 
                                        1000
                                    ];
                                    $allScattersWinArr = [];
                                    $sgwin = 0;
                                    $allScattersWin = 0;
                                    $totalWin += ($scattersWin + $allScattersWin);
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
                                        else
                                        {
                                            if( $totalWin > 0 ) 
                                            {
                                                $diceValue = rand(1, 6);
                                                $diceValue = 1;
                                                $BoardPositon += 1;
                                            }
                                            if( $scattersCount >= 5 && $winType != 'bonus' ) 
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
                                if( $scattersCount >= 5 ) 
                                {
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                    }
                                    $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                }
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reelsRaw) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"spins":[' . implode(',', $spins) . '],"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $curReels = '';
                                $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '-' . $reels['reel6'][0] . '"');
                                $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '-' . $reels['reel6'][1] . '"');
                                $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '-' . $reels['reel6'][2] . '"');
                                $curReels .= (',"' . $reels['reel1'][3] . '-' . $reels['reel2'][3] . '-' . $reels['reel3'][3] . '-' . $reels['reel4'][3] . '-' . $reels['reel5'][3] . '-' . $reels['reel6'][3] . '"');
                                $curReels .= (',"' . $reels['reel1'][4] . '-' . $reels['reel2'][4] . '-' . $reels['reel3'][4] . '-' . $reels['reel4'][4] . '-' . $reels['reel5'][4] . '-' . $reels['reel6'][4] . '"');
                                $curReels .= (',"' . $reels['reel1'][5] . '-' . $reels['reel2'][5] . '-' . $reels['reel3'][5] . '-' . $reels['reel4'][5] . '-' . $reels['reel5'][5] . '-' . $reels['reel6'][5] . '"');
                                $curReels .= (',"' . $reels['reel1'][6] . '-' . $reels['reel2'][6] . '-' . $reels['reel3'][6] . '-' . $reels['reel4'][6] . '-' . $reels['reel5'][6] . '-' . $reels['reel6'][6] . '"');
                                $slotSettings->SetGameData($slotSettings->slotId . 'spins', $spins);
                                $slotSettings->SetGameData($slotSettings->slotId . 'spinsCnt', 0);
                                $isJack = 'false';
                                if( $totalWin > 0 ) 
                                {
                                    $state = 'gamble';
                                }
                                else
                                {
                                    $state = 'idle';
                                }
                                if( !isset($sgwin) ) 
                                {
                                    $fs = 0;
                                }
                                else if( $sgwin > 0 ) 
                                {
                                    $fs = $sgwin;
                                }
                                else
                                {
                                    $fs = 0;
                                }
                                $balanceInCentsEnd = round(($slotSettings->GetBalance() - $allScattersWin) * 100);
                                $gameBets = [];
                                for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                                {
                                    $gameBets[] = $slotSettings->Bet[$i] * 100;
                                }
                                $totalWin -= $allScattersWin;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=BET&B=' . $balanceInCentsStart . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&RID=0&NRID=0&BPR=5&RB=6&RS=' . implode('|', $reelsRaw['rp']) . '|&TW=0&WC=0|0|0|&IFG=0&MUL=1' . $cSpin['curWinStr'] . '&SUB=0&GSD=FGP~1#OPS~4;0;1;0;39;1;37;1;34;2;32;1;31;1;29;2;27;0;26;1;24;1;23;0;21;0;19;0;18;2;16;1;14;1;13;0;11;2;9;0;8;2;6;1;3;2;1;1#DPR~' . implode(';', $reelsRaw['ws']) . '#MPS~4;0;1;0&GA=0&AB=' . $balanceInCentsStart . '&FRBAL=0&SID=Free:og44di99njt64ut6v3fl9s2l6kn&]]></PAYLOAD></GDMRESPONSE>';
                                }
                                else
                                {
                                    $cSpin = json_decode($spins[0], true);
                                    $rtw = '';
                                    if( $totalWin > 0 ) 
                                    {
                                        $diceValue = rand(1, 6);
                                        $diceValue = 6;
                                        $MPS[0] += $diceValue;
                                        if( $MPS[0] > 39 ) 
                                        {
                                            $MPS[0] = $MPS[0] - 39;
                                            $CurrentLap++;
                                        }
                                        $mtk = '';
                                        if( $Board[$MPS[0]][0] == 'cChest' ) 
                                        {
                                            $additionalStep = rand(1, 6);
                                            $newStep = $MPS[0] + $additionalStep;
                                            if( $newStep > 39 ) 
                                            {
                                                $newStep = $newStep - 39;
                                                $CurrentLap++;
                                            }
                                            $mtk = '#MTK~' . $MPS[0] . ';' . $newStep . '';
                                        }
                                        if( $Board[$MPS[0]][0] == 'Util' ) 
                                        {
                                            $Utilities[$Board[$MPS[0]][1]] = 1;
                                        }
                                        if( $Board[$MPS[0]][0] == 'Station' ) 
                                        {
                                            $Stations[$Board[$MPS[0]][1]] = 1;
                                        }
                                        $MPS[2] = $CurrentLap;
                                        $OPS0[2] = $CurrentLap;
                                        $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=BET&B=' . $balanceInCentsStart . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&RID=0&NRID=0&BPR=5&RB=6&RS=' . implode('|', $reelsRaw['rp']) . '&TW=' . $cSpin['winningsMoney'] . '&WC=1|0|1|' . $cSpin['curWinStr'] . '&CW=' . $cSpin['winningsMoney'] . '&NFG=1&FGT=1&TFG=1&CFGG=0&FGTW=0&IFG=0&FID=0|&MUL=1&SUB=0&GSD=FGP~' . $CurrentLap . '#OPS~' . implode(';', $OPS0) . ';' . implode(';', $OPS) . '#DPR~' . implode(';', $reelsRaw['ws']) . $mtk . '#RTW~' . $cSpin['winningsMoney'] . '#STP~' . $diceValue . '#MPS~' . implode(';', $MPS) . '&GA=0&AB=' . $balanceInCentsStart . '&FRBAL=0&Board=' . $Board[$MPS[0]][0] . '|' . $Board[$MPS[0]][1] . '&SID=Free:udhq58uupa8aubcq7e8efv42nmo&]]></PAYLOAD></GDMRESPONSE>';
                                        if( $Board[$MPS[0]][0] == 'cChest' ) 
                                        {
                                            $MPS[0] = $newStep;
                                        }
                                    }
                                    else
                                    {
                                        $MPS[2] = $CurrentLap;
                                        $OPS0[2] = $CurrentLap;
                                        $result_tmp[0] = '<?xml version="1.0" encoding="UTF-8"?><GDMRESPONSE><OGS_RC>0</OGS_RC><SUCCESS>true</SUCCESS><PAYLOAD><![CDATA[&MSGID=BET&B=' . $balanceInCentsStart . '&VER=2.6.54-2.7.3-2.6.3-2.6.1-1-3&RID=0&NRID=0&BPR=5&RB=6&RS=' . implode('|', $reelsRaw['rp']) . '&TW=0&WC=0|0|0|&IFG=0&MUL=1&SUB=0&GSD=FGP~' . $CurrentLap . '#OPS~' . implode(';', $OPS0) . ';' . implode(';', $OPS) . '#DPR~' . implode(';', $reelsRaw['ws']) . '#MPS~' . implode(';', $MPS) . '&GA=0&AB=' . $balanceInCentsStart . '&FRBAL=0&SID=Free:udhq58uupa8aubcq7e8efv42nmo&]]></PAYLOAD></GDMRESPONSE>';
                                    }
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentLap', $CurrentLap);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BoardPositon', $BoardPositon);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Stations', $Stations);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Utilities', $Utilities);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS', $OPS);
                                $slotSettings->SetGameData($slotSettings->slotId . 'OPS0', $OPS0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'MPS', $MPS);
                                break;
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
