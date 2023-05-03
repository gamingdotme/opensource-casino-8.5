<?php 
namespace VanguardLTE\Games\SinbadsGoldenVoyagePT
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
                        if( isset($postData['umid']) ) 
                        {
                            $umid = $postData['umid'];
                            if( isset($postData['ID']) ) 
                            {
                                $umid = $postData['ID'];
                            }
                        }
                        else
                        {
                            if( isset($postData['ID']) ) 
                            {
                                $result_tmp[] = '3:::{"ID":18}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['ID']) && $postData['ID'] == '46120' ) 
                        {
                            $fsNum = $slotSettings->GetGameData('SinbadsGoldenVoyagePTCurrentFreeGame');
                            $spins_ = $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeSpins');
                            $logs_ = $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeLogs');
                            $spins = json_decode(trim($spins_[$fsNum]), true);
                            $logs = json_decode(trim($logs_[$fsNum]), true);
                            $totalWin = $spins['currentWin'];
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBalance($totalWin);
                            }
                            $logs['serverResponse']['freeSeq'] = $spins_;
                            $logs['serverResponse']['freeLogSeq'] = $logs_;
                            $slotSettings->SaveLogReport(json_encode($logs), $logs['serverResponse']['slotBet'], $logs['serverResponse']['slotLines'], $totalWin, 'freespin');
                            $result_tmp[] = '3:::{"data":{"gameId":2543168908},"ID":46121,"umid":45}';
                            $slotSettings->SetGameData('SinbadsGoldenVoyagePTCurrentFreeGame', $slotSettings->GetGameData('SinbadsGoldenVoyagePTCurrentFreeGame') + 1);
                        }
                        if( isset($postData['ID']) && ($postData['ID'] == '46583' || $postData['ID'] == '46302') ) 
                        {
                            $result_tmp = [];
                            $postData['spinType'] = 'regular';
                            if( $postData['ID'] == '46302' ) 
                            {
                                $postData['spinType'] = 'free';
                            }
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTBonusWin', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeGames', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTTotalWin', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeBalance', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeStartWin', 0);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTCurrentFreeGame', $slotSettings->GetGameData('SinbadsGoldenVoyagePTCurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $linesId = [];
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $linesId[0] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[1] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[2] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[3] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[4] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[5] = [
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[6] = [
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[7] = [
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[8] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[9] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[10] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[11] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[12] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[15] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[16] = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[17] = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[18] = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[20] = [
                                    3, 
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[21] = [
                                    3, 
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[22] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[23] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[24] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[25] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[26] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[27] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[28] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[29] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[30] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[31] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[32] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[33] = [
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[34] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[35] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[36] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[37] = [
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[38] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[39] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[40] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[41] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[42] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[43] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[44] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[45] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[46] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[47] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[48] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[49] = [
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[50] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[51] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[52] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[53] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[54] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[55] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[56] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[57] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[58] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[59] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[60] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[61] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[62] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[63] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[64] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[65] = [
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[66] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[67] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[68] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[69] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[70] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[71] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[72] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[73] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[74] = [
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[75] = [
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[76] = [
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[77] = [
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[78] = [
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[79] = [
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[80] = [
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[81] = [
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[82] = [
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    2
                                ];
                                $linesId[83] = [
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[84] = [
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[85] = [
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[86] = [
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[87] = [
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[88] = [
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[89] = [
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[90] = [
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[91] = [
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[92] = [
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[93] = [
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[94] = [
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[95] = [
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[96] = [
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[97] = [
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[98] = [
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[99] = [
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[100] = [
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[101] = [
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[102] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[103] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[104] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[105] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[106] = [
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[107] = [
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[108] = [
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[109] = [
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[110] = [
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[111] = [
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[112] = [
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[113] = [
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[114] = [
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[115] = [
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[116] = [
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    4
                                ];
                                $linesId[117] = [
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    5
                                ];
                                $linesId[118] = [
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[119] = [
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[120] = [
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[121] = [
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[122] = [
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[123] = [
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[124] = [
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[125] = [
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[126] = [
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[127] = [
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[128] = [
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[129] = [
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[130] = [
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[131] = [
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[132] = [
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[133] = [
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[134] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[135] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[136] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[137] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[138] = [
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[139] = [
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[140] = [
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[141] = [
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[142] = [
                                    6, 
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    6
                                ];
                                $linesId[143] = [
                                    6, 
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[144] = [
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    4
                                ];
                                $linesId[145] = [
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[146] = [
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    5
                                ];
                                $linesId[147] = [
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    6
                                ];
                                $linesId[148] = [
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[149] = [
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[150] = [
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[151] = [
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[152] = [
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[153] = [
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[154] = [
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[155] = [
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[156] = [
                                    7, 
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    6
                                ];
                                $linesId[157] = [
                                    7, 
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[158] = [
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    5
                                ];
                                $linesId[159] = [
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[160] = [
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    7, 
                                    6
                                ];
                                $linesId[161] = [
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    7, 
                                    7
                                ];
                                $linesId[162] = [
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    6
                                ];
                                $linesId[163] = [
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7
                                ];
                            }
                            $postData['numLines'] = 'L164';
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $postData['bet'] = $slotSettings->GetGameData('SinbadsGoldenVoyagePTBet');
                                $lines = $slotSettings->GetGameData('SinbadsGoldenVoyagePTLines');
                            }
                            else
                            {
                                $postData['bet'] = $postData['stake'];
                                $lines_ = explode('L', $postData['numLines']);
                                $lines = (int)$lines_[1];
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTBet', $postData['bet']);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTLines', $lines);
                            }
                            $betLine = $postData['bet'];
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $betLine ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                                    exit( $response );
                                }
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['bet']);
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $wild = ['10'];
                                $scatter = '12';
                                $cReelIndex = 1;
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $spinInfo = $slotSettings->GetSpinWin($reels, $lines, $betLine, $linesId, $wild, $scatter, $bonusMpl, 'regular');
                                $totalWin = $spinInfo['totalWin'];
                                $lineWins = $spinInfo['lineWins'];
                                $scattersWin = $spinInfo['scattersWin'];
                                $scattersCount = $spinInfo['scattersCount'];
                                $scattersStr = $spinInfo['scattersStr'];
                                $reels2 = $spinInfo['reels'];
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
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
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTBonusWin', $slotSettings->GetGameData('SinbadsGoldenVoyagePTBonusWin') + $totalWin);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTTotalWin', $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTTotalWin', $totalWin);
                            }
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTBonusWin', 0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeGames', $slotSettings->slotFreeCount);
                                $fspins = ['{"results":[' . implode(',', $reels['rp']) . '],"reelset":' . $cReelIndex . '}'];
                                $linesId[0] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[1] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[2] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[3] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[4] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[5] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[6] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[7] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[8] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[9] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[10] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[11] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[12] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[13] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[14] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[15] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[16] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[17] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[18] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[20] = [
                                    2, 
                                    3, 
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
                                    2, 
                                    2
                                ];
                                $linesId[22] = [
                                    2, 
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[23] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[25] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[26] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[27] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[28] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[29] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[30] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[31] = [
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[32] = [
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[33] = [
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[34] = [
                                    2, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[35] = [
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[36] = [
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[37] = [
                                    3, 
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    3
                                ];
                                $linesId[38] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[39] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[40] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[41] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[42] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[43] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[44] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[45] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[46] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[47] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[48] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[49] = [
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[50] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[51] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[52] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[53] = [
                                    3, 
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[54] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[55] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[56] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[57] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[58] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[59] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[60] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[61] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[62] = [
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[63] = [
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[64] = [
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[65] = [
                                    3, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[66] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[67] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[68] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[69] = [
                                    4, 
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[70] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[71] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[72] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[73] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[74] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[75] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[76] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[77] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[78] = [
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[79] = [
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[80] = [
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[81] = [
                                    4, 
                                    4, 
                                    4, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[82] = [
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[83] = [
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[84] = [
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[85] = [
                                    4, 
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[86] = [
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[87] = [
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[88] = [
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[89] = [
                                    4, 
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[90] = [
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[91] = [
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[92] = [
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[93] = [
                                    4, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[94] = [
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[95] = [
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[96] = [
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[97] = [
                                    4, 
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[98] = [
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[99] = [
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[100] = [
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[101] = [
                                    5, 
                                    5, 
                                    4, 
                                    4, 
                                    4, 
                                    5
                                ];
                                $linesId[102] = [
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[103] = [
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[104] = [
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[105] = [
                                    5, 
                                    5, 
                                    4, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[106] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[107] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[108] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[109] = [
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[110] = [
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[111] = [
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[112] = [
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[113] = [
                                    5, 
                                    5, 
                                    5, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[114] = [
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[115] = [
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[116] = [
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[117] = [
                                    5, 
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[118] = [
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[119] = [
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[120] = [
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[121] = [
                                    5, 
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[122] = [
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[123] = [
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[124] = [
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[125] = [
                                    5, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[126] = [
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[127] = [
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[128] = [
                                    5, 
                                    6, 
                                    6, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[129] = [
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    4
                                ];
                                $linesId[130] = [
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    4, 
                                    5
                                ];
                                $linesId[131] = [
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    5
                                ];
                                $linesId[132] = [
                                    6, 
                                    6, 
                                    5, 
                                    5, 
                                    5, 
                                    6
                                ];
                                $linesId[133] = [
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[134] = [
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[135] = [
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[136] = [
                                    6, 
                                    6, 
                                    5, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[137] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[138] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[139] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[140] = [
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[141] = [
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[142] = [
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[143] = [
                                    6, 
                                    6, 
                                    6, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[144] = [
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[145] = [
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[146] = [
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[147] = [
                                    6, 
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[148] = [
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[149] = [
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[150] = [
                                    6, 
                                    7, 
                                    6, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[151] = [
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[152] = [
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[153] = [
                                    6, 
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[154] = [
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    5
                                ];
                                $linesId[155] = [
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    5, 
                                    6
                                ];
                                $linesId[156] = [
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    6
                                ];
                                $linesId[157] = [
                                    7, 
                                    7, 
                                    6, 
                                    6, 
                                    6, 
                                    7
                                ];
                                $linesId[158] = [
                                    7, 
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[159] = [
                                    7, 
                                    7, 
                                    6, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[160] = [
                                    7, 
                                    7, 
                                    6, 
                                    7, 
                                    7, 
                                    7
                                ];
                                $linesId[161] = [
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    6
                                ];
                                $linesId[162] = [
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    6, 
                                    7
                                ];
                                $linesId[163] = [
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7, 
                                    7
                                ];
                                for( $i = 0; $i <= 2000; $i++ ) 
                                {
                                    $totalWin = 0;
                                    $fspins0 = [];
                                    $responseLog = [];
                                    $fullBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                    $slotSettings->SetGameData('SinbadsGoldenVoyagePTBonusWin', 0);
                                    for( $fp = 0; $fp < 7; $fp++ ) 
                                    {
                                        $lineWins = [];
                                        $wild = [
                                            '10', 
                                            '333'
                                        ];
                                        $scatter = '12';
                                        $currentWin = 0;
                                        $cReelIndex = 34;
                                        $reels = $slotSettings->GetReelStrips('', 'freespin');
                                        $spinInfo = $slotSettings->GetSpinWin($reels, $lines, $betLine, $linesId, $wild, $scatter, $bonusMpl, 'freespin');
                                        $currentWin = $spinInfo['totalWin'];
                                        $totalWin = $spinInfo['totalWin'];
                                        $lineWins = $spinInfo['lineWins'];
                                        $scattersWin = $spinInfo['scattersWin'];
                                        $scattersStr = $spinInfo['scattersStr'];
                                        $reels2 = $spinInfo['reels'];
                                        $slotSettings->SetGameData('SinbadsGoldenVoyagePTBonusWin', $slotSettings->GetGameData('SinbadsGoldenVoyagePTBonusWin') + $currentWin);
                                        $fspins0[] = '{"results":[' . implode(',', $reels['rp']) . '],"currentWin":' . $currentWin . ',"reelset":' . $cReelIndex . '}';
                                        $jsSpin = '' . json_encode($reels) . '';
                                        $responseLog[] = '{"responseEvent":"spin","responseType":"freespin","serverResponse":{"bonusMpl":1,"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":7,"currentFreeGames":' . ($fp + 1) . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeStartWin') . ',"totalWin":' . $currentWin . ',"winLines":[],"bonusInfo":"","Jackpots":"","reelsSymbols":' . $jsSpin . '}}';
                                    }
                                    if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $i > 1000 ) 
                                        {
                                            $winType = 'none';
                                        }
                                        if( $i > 1500 ) 
                                        {
                                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                            exit( $response );
                                        }
                                        $minWin = $slotSettings->GetRandomPay();
                                        if( $i > 700 ) 
                                        {
                                            $minWin = 0;
                                        }
                                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $i > 1500 ) 
                                            {
                                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                                exit( $response );
                                            }
                                            if( $totalWin <= $fullBank ) 
                                            {
                                                break;
                                            }
                                        }
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                }
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeSpins', $fspins0);
                                $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeLogs', $responseLog);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            if( $scattersCount >= 3 ) 
                            {
                                $result_tmp[] = '3:::{"data":{"wCapMaxWin":1000000.0,"ww":"' . $scattersCount . '","spins":[' . implode(',', $fspins) . ',' . implode(',', $fspins0) . ']},"ID":46585,"umid":32}';
                            }
                            else
                            {
                                $result_tmp[] = '3:::{"data":{"wCapMaxWin":1000000.0,"spins":[{"results":[' . implode(',', $reels['rp']) . '],"reelset":' . $cReelIndex . '}]},"ID":46585,"umid":32}';
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsSpin2 = '' . json_encode($reels2) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols2":' . $jsSpin2 . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                        }
                        switch( $umid ) 
                        {
                            case '31031':
                                $result_tmp[] = '3:::{"data":{"urlList":[{"urlType":"mobile_login","url":"https://login.loc/register","priority":1},{"urlType":"mobile_support","url":"https://ww2.loc/support","priority":1},{"urlType":"playerprofile","url":"","priority":1},{"urlType":"playerprofile","url":"","priority":10},{"urlType":"gambling_commission","url":"","priority":1},{"urlType":"cashier","url":"","priority":1},{"urlType":"cashier","url":"","priority":1}]},"ID":100}';
                                break;
                            case '10001':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40083,"umid":3}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":4}';
                                $result_tmp[] = '3:::{"data":{"commandId":13218,"params":["0","null"]},"ID":50001,"umid":5}';
                                $result_tmp[] = '3:::{"token":{"secretKey":"","currency":"USD","balance":0,"loginTime":""},"ID":10002,"umid":7}';
                                break;
                            case '40294':
                                $result_tmp[] = '3:::{"nicknameInfo":{"nickname":""},"ID":10022,"umid":8}';
                                $result_tmp[] = '3:::{"data":{"commandId":10713,"params":["0","ba","bj","ct","gc","grel","hb","po","ro","sc","tr"]},"ID":50001,"umid":9}';
                                $result_tmp[] = '3:::{"data":{"commandId":11666,"params":["0","0","0"]},"ID":50001,"umid":11}';
                                $result_tmp[] = '3:::{"data":{"commandId":13981,"params":["0","1"]},"ID":50001,"umid":12}';
                                $result_tmp[] = '3:::{"data":{"commandId":14080,"params":["0","0"]},"ID":50001,"umid":14}';
                                $result_tmp[] = '3:::{"data":{"keyValueCount":5,"elementsPerKey":1,"params":["10","1","11","500","12","1","13","0","14","0"]},"ID":40716,"umid":15}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40083,"umid":16}';
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":' . $balanceInCents . '},"ID":10006,"umid":17}';
                                $result_tmp[] = '3:::{"data":{},"ID":40292,"umid":18}';
                                break;
                            case '10010':
                                $result_tmp[] = '3:::{"data":{"urls":{"casino-cashier-myaccount":[],"regulation_pt_self_exclusion":[],"link_legal_aams":[],"regulation_pt_player_protection":[],"mobile_cashier":[],"mobile_bank":[],"mobile_bonus_terms":[],"mobile_help":[],"link_responsible":[],"cashier":[{"url":"","priority":1},{"url":"","priority":1}],"gambling_commission":[{"url":"","priority":1},{"url":"","priority":1}],"desktop_help":[],"chat_token":[],"mobile_login_error":[],"mobile_error":[],"mobile_login":[{"url":"","priority":1}],"playerprofile":[{"url":"","priority":1},{"url":"","priority":10}],"link_legal_half":[],"ngmdesktop_quick_deposit":[],"external_login_form":[],"mobile_main_promotions":[],"mobile_lobby":[],"mobile_promotion":[],{"url":"","priority":1},{"url":"","priority":10}],"mobile_withdraw":[],"mobile_funds_trans":[],"mobile_quick_deposit":[],"mobile_history":[],"mobile_deposit_limit":[],"minigames_help":[],"link_legal_18":[],"mobile_responsible":[],"mobile_share":[],"mobile_lobby_error":[],"mobile_mobile_comp_points":[],"mobile_support":[{"url":"","priority":1}],"mobile_chat":[],"mobile_logout":[],"mobile_deposit":[],"invite_friend":[]}},"ID":10011,"umid":19}';
                                $result_tmp[] = '3:::{"data":{"brokenGames":[],"windowId":"SuJLru"},"ID":40037,"umid":20}';
                                break;
                            case '46090':
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                    $fsReel = $slotSettings->GetGameData('SinbadsGoldenVoyagePTFreeSpins');
                                    $fsNum = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                    $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                    $spf = [];
                                    $drawId = 2;
                                    for( $i = 0; $i <= 6; $i++ ) 
                                    {
                                        $vl = json_decode($fsReel[$i], true);
                                        $reelset = $vl['reelset'];
                                        $spf[] = '{"drawId":' . $drawId . ',"winLines":{"winlines":[],"feature":{"name":"leviathan","type":"display","detail":{"name":"anticipation","nextReel":1,"reelset":' . $reelset . ',"rsName":"FS11"}},"display":"W,W,W,W,W,W,W;R,R,R,R,D,D;Y,Y,O,O,O,O,O;R,D,D,D,D,O;D,D,D,R,R,R,Y;D,Y,Y,O,O,D","reelset":' . $reelset . ',"rsName":"FS8","runningTotal":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"spinWin":' . $vl['currentWin'] . ',"currentSpins":7,"spins":7,"stops":"' . implode(',', $vl['results']) . '"},"replayInfo":{"foItems":"8,' . implode(',', $vl['results']) . '"}}';
                                        $drawId++;
                                    }
                                    $result_tmp[] = '3:::{"data":{"gameId":2656672142,"version":"C-210416#1638:X-091014#101925","drawStates":[{"drawId":0,"state":"settling","wCapMaxWin":1000000,"winLines":{"winlines":[],"feature":{"name":"leviathan","type":"display","detail":{"name":"anticipation","nextReel":3,"reelset":40,"rsName":"FS10"}},"scatter":{"length":3,"offsets":"7,22,36","payout":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin')) . ',"prize":"3F","spins":7},"display":"Y,Y,O,O,D,D;H,F,F,E,E,E,P;H,P,P,P,P,E;Y,Y,Y,F,F,H,H;S,S,S,S,S,S;H,H,H,H,F,F,E","reelset":0,"rsName":"MainReel0","runningTotal":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"spinWin":0,"currentSpins":7,"spins":7,"stops":"8,25,31,69,84,56"},"replayInfo":{"foItems":"0,8,25,31,69,84,56"},"bet":{"payout":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin')) . ',"pick":"L164","seq":0,"stake":' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . ',"type":"line","won":"true"}},' . implode(',', $spf) . '],"savedStates":[{"attr21":"' . $fsCur . '","seq":0}]},"ID":46580,"umid":25}';
                                }
                                else
                                {
                                    $result_tmp[] = '3:::{"data":{"gameId":2662623726,"version":"C-210416#1638:X-091014#101925","drawStates":[{"drawId":0}]},"ID":46580,"umid":27}';
                                }
                                break;
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"gtsswk","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
                                break;
                            case '40036':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', '');
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $lastEvent->serverResponse->freeStartWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lastEvent->serverResponse->slotLines);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    if( isset($lastEvent->serverResponse->freeSeq) ) 
                                    {
                                        $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeSpins', $lastEvent->serverResponse->freeSeq);
                                        $slotSettings->SetGameData('SinbadsGoldenVoyagePTFreeLogs', $lastEvent->serverResponse->freeLogSeq);
                                        $slotSettings->SetGameData('SinbadsGoldenVoyagePTbonusMpl', $lastEvent->serverResponse->bonusMpl);
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'gtsswk');
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"brokenGames":["' . $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') . '"],"windowId":"SuJLru"},"ID":40037,"umid":22}';
                                break;
                            case '40020':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '40030':
                                $result_tmp[] = '3:::{"data":{"typeBalance":2,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":1,"balanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":0},"ID":40085}';
                                $result_tmp[] = '3:::{"data":{"credit":' . $balanceInCents . ',"windowId":"SuJLru"},"ID":40026,"umid":28}';
                                break;
                            case '48300':
                                $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":30}';
                                $result_tmp[] = '3:::{"data":{"waitingLogins":[],"waitingAlerts":[],"waitingDialogs":[],"waitingDialogMessages":[],"waitingToasterMessages":[]},"ID":48301,"umid":31}';
                                break;
                        }
                        $response = implode('------', $result_tmp);
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
