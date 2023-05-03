<?php 
namespace VanguardLTE\Games\FourBeautiesKA
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
                        $postData = json_decode(trim(file_get_contents('php://input')), true);
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        $aid = '';
                        if( $postData['command'] == 'bet' && $postData['bet']['gameCommand'] == 'collect' ) 
                        {
                            $postData['command'] = 'collect';
                        }
                        $aid = (string)$postData['command'];
                        switch( $aid ) 
                        {
                            case 'startGame':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '' . rand(0, 6) . ',' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . ',' . rand(0, 6);
                                    $curReels = '';
                                    $curReels .= ($reels->reel1[3] . ',' . $reels->reel2[3] . ',' . $reels->reel3[3] . ',' . $reels->reel4[3] . ',' . $reels->reel5[3]);
                                    $curReels .= (',' . $reels->reel1[2] . ',' . $reels->reel2[2] . ',' . $reels->reel3[2] . ',' . $reels->reel4[2] . ',' . $reels->reel5[2]);
                                    $curReels .= (',' . $reels->reel1[1] . ',' . $reels->reel2[1] . ',' . $reels->reel3[1] . ',' . $reels->reel4[1] . ',' . $reels->reel5[1]);
                                    $curReels .= (',' . $reels->reel1[0] . ',' . $reels->reel2[0] . ',' . $reels->reel3[0] . ',' . $reels->reel4[0] . ',' . $reels->reel5[0]);
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * 100;
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                    }
                                }
                                else
                                {
                                    $curReels = '6,0,10,5,6,9,10,6,6,7,10,5,12,5,5,9,8,6,6,8';
                                    $lines = 100;
                                    $bet = $slotSettings->Bet[0] * 100;
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $acb = 1;
                                    $fsr = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $fs = 'true';
                                    $rd = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $tfw = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100;
                                }
                                else
                                {
                                    $rd = 0;
                                    $fs = 'false';
                                    $acb = 0;
                                    $fsr = 0;
                                    $tfw = 0;
                                }
                                $result_tmp[0] = '{"sgr":{"gn":"FourBeauties","lsd":{"sid":' . rand(0, 9999999) . ',"tid":"94d99c342e25434e90d1a99791fc6087","sel":' . $lines . ',"cps":1,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":' . $fs . ',"twg":' . ($lines * $bet) . ',"swm":0,"sw":0,"swu":0,"tw":0,"fsw":0,"fsr":' . $fsr . ',"tfw":' . $tfw . ',"st":[' . $curReels . '],"swi":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"snm":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"ssm":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"sm":[-1,0,0,0,0],"acb":0,"rf":0,"as":[{"asi":397758614,"st":[' . $curReels . '],"swm":0,"sw":0,"swu":0,"fsw":0,"tw":0}],"sp":0,"cr":"USD","rd":0,"pbb":0,"obb":0,"mb":false,"pwa":0},"drs":[[' . implode(',', $slotSettings->reelStrip1) . '],[' . implode(',', $slotSettings->reelStrip2) . '],[' . implode(',', $slotSettings->reelStrip3) . '],[' . implode(',', $slotSettings->reelStrip4) . '],[' . implode(',', $slotSettings->reelStrip5) . ']],"pt":[[0,0,0,0,0],[0,0,50,80,180],[0,0,50,80,180],[0,0,50,80,180],[0,0,50,80,180],[0,0,40,80,150],[0,0,30,60,100],[0,0,20,50,80],[0,0,10,40,60],[0,0,5,20,40],[0,0,5,10,20],[0,0,0,0,0],[0,0,3,5,8]],"cps":[1,2,3,4,5,7,8,10,15,20,25,30,50,75,100],"e":false,"ec":0,"cc":""},"un":"accessKey|USD|653892710","bl":' . $balanceInCents . ',"gn":"FourBeauties","lgn":"Four Beauties","gv":0,"fs":' . $fs . ',"si":"a7f63927af504335a033d62277db7c1b","dn":[0.01,0.05,0.1,0.25,0.5,1.0,2.0],"cs":"$","cd":2,"cp":false,"gs":",","ds":".","ase":[100],"gm":0,"mi":-1,"cud":0.01,"cup":[' . implode(',', $gameBets) . '],"mm":0,"e":false,"ec":0,"cc":"EN"}';
                                break;
                            case 'ping':
                                $result_tmp[0] = '{"v":' . $balanceInCents . ',"e":false,"ec":0,"cc":"EN"}';
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
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[5] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[6] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[7] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[8] = [
                                    1, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[9] = [
                                    2, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[10] = [
                                    3, 
                                    2, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[11] = [
                                    4, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[12] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[13] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[14] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[15] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    1
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
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[19] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[20] = [
                                    1, 
                                    2, 
                                    4, 
                                    2, 
                                    1
                                ];
                                $linesId[21] = [
                                    4, 
                                    3, 
                                    1, 
                                    3, 
                                    4
                                ];
                                $linesId[22] = [
                                    1, 
                                    3, 
                                    4, 
                                    3, 
                                    1
                                ];
                                $linesId[23] = [
                                    4, 
                                    2, 
                                    1, 
                                    2, 
                                    4
                                ];
                                $linesId[24] = [
                                    1, 
                                    1, 
                                    4, 
                                    1, 
                                    4
                                ];
                                $linesId[25] = [
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[26] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[27] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[28] = [
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[29] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[30] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[31] = [
                                    1, 
                                    1, 
                                    1, 
                                    3, 
                                    3
                                ];
                                $linesId[32] = [
                                    2, 
                                    2, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $linesId[33] = [
                                    3, 
                                    3, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[34] = [
                                    4, 
                                    4, 
                                    4, 
                                    2, 
                                    2
                                ];
                                $linesId[35] = [
                                    1, 
                                    1, 
                                    1, 
                                    4, 
                                    4
                                ];
                                $linesId[36] = [
                                    4, 
                                    4, 
                                    4, 
                                    1, 
                                    1
                                ];
                                $linesId[37] = [
                                    1, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[38] = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[39] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[40] = [
                                    2, 
                                    2, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[41] = [
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[42] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[43] = [
                                    1, 
                                    1, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[44] = [
                                    2, 
                                    2, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[45] = [
                                    3, 
                                    3, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[46] = [
                                    4, 
                                    4, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[47] = [
                                    1, 
                                    1, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[48] = [
                                    4, 
                                    4, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[49] = [
                                    4, 
                                    4, 
                                    1, 
                                    4, 
                                    1
                                ];
                                $linesId[50] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[51] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[52] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[53] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[54] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[55] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[56] = [
                                    1, 
                                    3, 
                                    1, 
                                    3, 
                                    1
                                ];
                                $linesId[57] = [
                                    2, 
                                    4, 
                                    2, 
                                    4, 
                                    2
                                ];
                                $linesId[58] = [
                                    3, 
                                    1, 
                                    3, 
                                    1, 
                                    3
                                ];
                                $linesId[59] = [
                                    4, 
                                    2, 
                                    4, 
                                    2, 
                                    4
                                ];
                                $linesId[60] = [
                                    1, 
                                    4, 
                                    1, 
                                    4, 
                                    1
                                ];
                                $linesId[61] = [
                                    4, 
                                    1, 
                                    4, 
                                    1, 
                                    4
                                ];
                                $linesId[62] = [
                                    2, 
                                    1, 
                                    3, 
                                    1, 
                                    2
                                ];
                                $linesId[63] = [
                                    2, 
                                    1, 
                                    4, 
                                    1, 
                                    2
                                ];
                                $linesId[64] = [
                                    3, 
                                    1, 
                                    2, 
                                    1, 
                                    3
                                ];
                                $linesId[65] = [
                                    3, 
                                    1, 
                                    4, 
                                    1, 
                                    3
                                ];
                                $linesId[66] = [
                                    3, 
                                    4, 
                                    2, 
                                    4, 
                                    3
                                ];
                                $linesId[67] = [
                                    3, 
                                    4, 
                                    1, 
                                    4, 
                                    3
                                ];
                                $linesId[68] = [
                                    2, 
                                    4, 
                                    3, 
                                    4, 
                                    2
                                ];
                                $linesId[69] = [
                                    2, 
                                    4, 
                                    1, 
                                    4, 
                                    2
                                ];
                                $linesId[70] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[71] = [
                                    3, 
                                    2, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[72] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[73] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[74] = [
                                    1, 
                                    4, 
                                    1, 
                                    4, 
                                    4
                                ];
                                $linesId[75] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[76] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[77] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[78] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[79] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[80] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[81] = [
                                    1, 
                                    3, 
                                    3, 
                                    3, 
                                    1
                                ];
                                $linesId[82] = [
                                    2, 
                                    4, 
                                    4, 
                                    4, 
                                    2
                                ];
                                $linesId[83] = [
                                    3, 
                                    1, 
                                    1, 
                                    1, 
                                    3
                                ];
                                $linesId[84] = [
                                    4, 
                                    2, 
                                    2, 
                                    2, 
                                    4
                                ];
                                $linesId[85] = [
                                    1, 
                                    4, 
                                    4, 
                                    4, 
                                    1
                                ];
                                $linesId[86] = [
                                    4, 
                                    1, 
                                    1, 
                                    1, 
                                    4
                                ];
                                $linesId[87] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[88] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[89] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[90] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[91] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[92] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[93] = [
                                    1, 
                                    1, 
                                    3, 
                                    1, 
                                    1
                                ];
                                $linesId[94] = [
                                    2, 
                                    2, 
                                    4, 
                                    2, 
                                    2
                                ];
                                $linesId[95] = [
                                    3, 
                                    3, 
                                    1, 
                                    3, 
                                    3
                                ];
                                $linesId[96] = [
                                    4, 
                                    4, 
                                    2, 
                                    4, 
                                    4
                                ];
                                $linesId[97] = [
                                    1, 
                                    1, 
                                    4, 
                                    1, 
                                    1
                                ];
                                $linesId[98] = [
                                    4, 
                                    4, 
                                    1, 
                                    4, 
                                    4
                                ];
                                $linesId[99] = [
                                    4, 
                                    1, 
                                    4, 
                                    1, 
                                    1
                                ];
                                $lines = $postData['sel'];
                                $betline = $postData['cps'] / 100;
                                $allbet = $betline * $lines;
                                $postData['slotEvent'] = 'bet';
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $allbet <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
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
                                    $slotSettings->UpdateJackpots($allbet);
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
                                    $cWins = [];
                                    $cWinsCount = [];
                                    $cWinsMpl = [];
                                    $wild = ['0'];
                                    $scatter = '12';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    for( $k = 0; $k < $lines; $k++ ) 
                                    {
                                        $cWins[$k] = 0;
                                        $cWinsCount[$k] = 1;
                                        $cWinsMpl[$k] = 1;
                                    }
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
                                                        $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                        $cWinsCount[$k] = 3;
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
                                                        $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                        $cWinsCount[$k] = 7;
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
                                                        $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                        $cWinsCount[$k] = 15;
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
                                                        $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . ',4,' . ($linesId[$k][4] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                        $cWinsCount[$k] = 31;
                                                    }
                                                }
                                            }
                                        }
                                        if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                        {
                                            $cWinsMpl[$k] = $bonusMpl;
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$k];
                                            $cWins[$k] = $cWins[$k] / $bonusMpl;
                                        }
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scattersCount2 = 0;
                                    $scPos = [];
                                    $scRPos = [
                                        0, 
                                        1, 
                                        2, 
                                        4, 
                                        8, 
                                        16
                                    ];
                                    $swm_ = [
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    for( $p = 3; $p >= 0; $p-- ) 
                                    {
                                        if( $reels['reel1'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 1;
                                        }
                                        if( $reels['reel2'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 2;
                                        }
                                        if( $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 4;
                                        }
                                        if( $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 8;
                                        }
                                        if( $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 16;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 3;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 5;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 6;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 9;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 10;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 12;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 17;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 18;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 20;
                                        }
                                        if( $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 24;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 7;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 11;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 13;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 14;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 19;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 21;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 22;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 25;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 26;
                                        }
                                        if( $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 28;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 27;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 29;
                                        }
                                        if( $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 30;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 23;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 15;
                                        }
                                        if( $reels['reel1'][$p] == $scatter && $reels['reel2'][$p] == $scatter && $reels['reel3'][$p] == $scatter && $reels['reel4'][$p] == $scatter && $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $swm_[$p] = 31;
                                        }
                                        if( $reels['reel1'][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                        if( $reels['reel2'][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                        if( $reels['reel3'][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                        if( $reels['reel4'][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                        if( $reels['reel5'][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                        }
                                        if( $reels['reel1'][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                        if( $reels['reel2'][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                        if( $reels['reel3'][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                        if( $reels['reel4'][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                        if( $reels['reel5'][$p] == '11' ) 
                                        {
                                            $scattersCount2++;
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    $totalWin += $scattersWin;
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
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
                                        else if( $scattersCount >= 3 && $scattersCount2 >= 3 ) 
                                        {
                                        }
                                        else if( ($scattersCount >= 3 || $scattersCount2 >= 3) && $winType != 'bonus' ) 
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
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                }
                                $fs = 0;
                                $swu = 0;
                                $swm = 0;
                                if( $scattersCount >= 3 ) 
                                {
                                    $swm = $swm_[3] + (32 * $swm_[2]) + (1024 * $swm_[1]) + (32768 * $swm_[0]);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $swu = 1;
                                }
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $curReels = '';
                                $curReels .= ($reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . ',' . $reels['reel4'][3] . ',' . $reels['reel5'][3]);
                                $curReels .= (',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2]);
                                $curReels .= (',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1]);
                                $curReels .= (',' . $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0]);
                                $acb = 0;
                                $fs_ = 'false';
                                if( $winType == 'bonus' ) 
                                {
                                    $acb = 1;
                                }
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $fs_ = 'true';
                                    $acb = 1;
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin'));
                                    }
                                }
                                for( $ww = 0; $ww < count($cWins); $ww++ ) 
                                {
                                    $cWinsStr[$ww] = $cWins[$ww] * 100;
                                }
                                $balanceInCents2 = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $pickBonusData = '';
                                if( $scattersCount2 >= 3 ) 
                                {
                                    $acb = 2;
                                    $pickBonusData = ',"bpd":{"c":false,"s":false,"lt":0,"np":1,"msb":1,"w":0,"lpw":0,"nfw":0,"m":0,"mk":0,"m2":0,"m3":0,"csw":0}';
                                    $PickBonusWins = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3, 
                                        4, 
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        10, 
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
                                        50, 
                                        50, 
                                        100, 
                                        100
                                    ];
                                    $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    for( $bs = 0; $bs <= 2000; $bs++ ) 
                                    {
                                        $totalPickWin = 0;
                                        $PickBonusValue = [
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        shuffle($PickBonusWins);
                                        for( $bc = 0; $bc < 1; $bc++ ) 
                                        {
                                            $curPickWin = $PickBonusWins[$bc] * $betline;
                                            $totalPickWin += $curPickWin;
                                            $PickBonusValue[$bc] = $curPickWin;
                                        }
                                        if( $totalPickWin <= $bank ) 
                                        {
                                            break;
                                        }
                                    }
                                    if( $totalPickWin > 0 ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalPickWin);
                                        $slotSettings->SetBalance($totalPickWin);
                                    }
                                    $PickBonusResult = [];
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusCount', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusValue', $PickBonusValue);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusResult', $PickBonusResult);
                                    $slotSettings->SaveLogReport($response, 0, 0, $totalPickWin, $postData['slotEvent']);
                                }
                                $result_tmp[0] = '{"sid":' . rand(0, 9999999) . ',"md":{"sid":' . rand(0, 9999999) . ',"tid":"0e809033ce77432e8dcee275ec2fca7b","sel":100,"cps":1,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":' . $fs_ . ',"twg":' . ($lines * $betline * 100) . ',"swm":' . $swm . ',"sw":' . ($scattersWin * 100) . ',"swu":' . $swu . ',"tw":' . ($totalWin * 100) . ',"fsw":' . $fs . ',"fsr":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"tfw":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"st":[' . $curReels . '],"swi":[' . implode(',', $cWinsStr) . '],"snm":[' . implode(',', $cWinsMpl) . '],"ssm":[' . implode(',', $cWinsCount) . ']' . $pickBonusData . ',"sm":[-1,0,0,0,0],"acb":' . $acb . ',"rf":0,"as":[{"asi":397766512,"st":[' . $curReels . '],"swm":' . $swm . ',"sw":' . ($scattersWin * 100) . ',"swu":' . $swu . ',"fsw":' . $fs . ',"tw":' . ($totalWin * 100) . '}],"sp":2,"cr":"USD","sessionId":"6e864f28050d4e34b605c6ed05aa3d92","rd":0,"pbb":' . $balanceInCents . ',"obb":' . $balanceInCents2 . ',"mb":false,"pwa":0,"pac":{}},"pcr":' . $balanceInCents . ',"cr":' . $balanceInCents2 . ',"xp":0,"lvl":{"lvl":1,"xp":0,"bc":0,"cps":5,"sc":200,"wb":0},"dl":0,"cps":[1,2,3,4,5,7,8,10,15,20,25,30,50,75,100],"e":false,"ec":0,"cc":"EN"}';
                                $PickAnswer = '{"md":{"sid":2371159497,"tid":"6e60cdc944754acba254b058afafb17d","sel":' . $lines . ',"cps":5,"dn":0.01,"nd":0.01,"ncps":0,"atb":0,"v":true,"fs":false,"twg":' . (25 * $betline * 100) . ',"swm":' . $swm . ',"sw":' . ($scattersWin * 100) . ',"swu":' . $swu . ',"tw":' . ($totalWin * 100) . ',"fsw":' . $fs . ',"fsr":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"tfw":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"st":[' . $curReels . '],"swi":[' . implode(',', $cWinsStr) . '],"snm":[' . implode(',', $cWinsMpl) . '],"ssm":[' . implode(',', $cWinsCount) . '],"bpd":{"c":false,"s":true,"lt":0,"np":1,"msb":1,"w":0,"lpw":0,"nfw":0,"m":0,"mk":3,"m2":0,"m3":0,"rv":[-1,-1,-1,-1,-1],"csw":0},"acb":2,"rf":0,"as":[],"sp":40,"cr":"USD","sessionId":"20685ede9e4a4c91ab030dec86cca651","rd":0,"pbb":' . $balanceInCents . ',"obb":10002743,"mb":false,"pwa":0,"pac":{}},"p":0,"cr":10002743,"cps":[1,2,3,5,10,15,20,25,30,50,75,100,150,250,500],"e":false,"ec":0,"cc":"EN"}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickAnswer', $PickAnswer);
                                break;
                            case 'bonusPick':
                                $PickBonusCount = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusCount');
                                $PickBonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusWin');
                                $PickBonusValue = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusValue');
                                $PickBonusResult = $slotSettings->GetGameData($slotSettings->slotId . 'PickBonusResult');
                                $PickAnswer = json_decode($slotSettings->GetGameData($slotSettings->slotId . 'PickAnswer'), true);
                                $CurWin = $PickBonusValue[$PickBonusCount] * 100;
                                $PickBonusWin += $CurWin;
                                $PickBonusResult[$PickBonusCount] = $CurWin;
                                $c = false;
                                if( $PickBonusCount == 0 ) 
                                {
                                    $c = true;
                                    $pbw = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3, 
                                        4, 
                                        4, 
                                        4, 
                                        5, 
                                        5, 
                                        5, 
                                        5, 
                                        10, 
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
                                        50, 
                                        50, 
                                        100, 
                                        100
                                    ];
                                    shuffle($pbw);
                                }
                                $mk = [
                                    1, 
                                    3, 
                                    7, 
                                    15
                                ];
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                $PickAnswer['md']['obb'] = $balanceInCents;
                                $PickAnswer['cr'] = $balanceInCents;
                                $PickAnswer['md']['bpd']['mk'] = $mk[$PickBonusCount];
                                $PickAnswer['md']['bpd']['c'] = $c;
                                $PickAnswer['md']['bpd']['s'] = true;
                                $PickAnswer['md']['bpd']['w'] = $PickBonusWin;
                                $PickAnswer['md']['bpd']['lpw'] = $CurWin;
                                $PickAnswer['md']['bpd']['rv'] = $PickBonusResult;
                                $result_tmp[0] = json_encode($PickAnswer);
                                $PickBonusCount++;
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusCount', $PickBonusCount);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusWin', $PickBonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusValue', $PickBonusValue);
                                $slotSettings->SetGameData($slotSettings->slotId . 'PickBonusResult', $PickBonusResult);
                                break;
                            case 'updatePlayerInfo':
                                $result_tmp[0] = '{"e":false,"ec":0,"cc":"EN"}';
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
