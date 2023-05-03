<?php 
namespace VanguardLTE\Games\MedusaMonstersPTM
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
                            if( isset($postData['ID']) && $postData['ID'] == 40041 ) 
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            else if( isset($postData['ID']) ) 
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
                            }
                            $umid = 0;
                        }
                        if( isset($postData['spinType']) ) 
                        {
                            $result_tmp = [];
                            if( $postData['spinType'] == 'regular' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
                                $slotSettings->SetGameData('MedusaMonstersPTMBonusWin', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeGames', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMCurrentFreeGame', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMTotalWin', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeBalance', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeStartWin', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeMpl', $slotSettings->slotFreeMpl);
                                $slotSettings->SetGameData('MedusaMonstersPTMIncreaseMpl', 0);
                                $stickySymbol = rand(1, 3);
                                $stickySymbolArr = [
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1, 
                                    -1
                                ];
                                $slotSettings->SetGameData('MedusaMonstersPTMstickySymbol', $stickySymbol);
                                $slotSettings->SetGameData('MedusaMonstersPTMstickySymbolArr', $stickySymbolArr);
                                $slotSettings->SetGameData('MedusaMonstersPTMwildRow', 6);
                            }
                            else if( $postData['spinType'] == 'free' ) 
                            {
                                $umid = '0';
                                $postData['slotEvent'] = 'freespin';
                                $slotSettings->SetGameData('MedusaMonstersPTMCurrentFreeGame', $slotSettings->GetGameData('MedusaMonstersPTMCurrentFreeGame') + 1);
                                $stickySymbol = $slotSettings->GetGameData('MedusaMonstersPTMstickySymbol');
                                $stickySymbolArr = $slotSettings->GetGameData('MedusaMonstersPTMstickySymbolArr');
                                $bonusMpl = 1;
                            }
                            $linesId = [];
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
                            $postData['bet'] = $postData['bet'] / 100;
                            for( $i = 0; $i < count($postData['lines']); $i++ ) 
                            {
                                if( $postData['lines'][$i] > 0 ) 
                                {
                                    $lines = $i + 1;
                                }
                                else
                                {
                                    break;
                                }
                            }
                            $betLine = $postData['bet'];
                            if( $postData['slotEvent'] == 'bet' || $postData['slotEvent'] == 'freespin' || $postData['slotEvent'] == 'respin' ) 
                            {
                                if( $lines <= 0 || $betLine <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < ($lines * $betLine) ) 
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
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($postData['bet']);
                                if( is_array($jackState) ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                }
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $randomBonusIdRand = 1;
                                $winType = 'bonus';
                            }
                            else
                            {
                                $randomBonusIdRand = 0;
                            }
                            $winType2 = '';
                            if( $randomBonusIdRand == 1 && $winType == 'bonus' ) 
                            {
                                $winType2 = 'bonus2';
                                $winType = 'none';
                            }
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $cWins = [];
                                for( $cw = 0; $cw < 165; $cw++ ) 
                                {
                                    $cWins[$cw] = 0;
                                }
                                $wild = ['0'];
                                $scatter = '11';
                                if( $winType == 'bonus' ) 
                                {
                                    $cReelIndex = 0;
                                    $reels = $slotSettings->GetReelStrips('bonus', $cReelIndex, $postData['slotEvent']);
                                }
                                else
                                {
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $cReelIndex = rand(0, 11);
                                    }
                                    else
                                    {
                                        $cReelIndex = rand(0, 16);
                                    }
                                    $reels = $slotSettings->GetReelStrips0($cReelIndex, $postData['slotEvent']);
                                }
                                $reelsTmp = $reels;
                                for( $r = 1; $r <= 6; $r++ ) 
                                {
                                    for( $p = 0; $p <= 6; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == '12' ) 
                                        {
                                            $reels['reel' . $r][$p] = '1';
                                        }
                                        if( $reels['reel' . $r][$p] == '13' ) 
                                        {
                                            $reels['reel' . $r][$p] = '2';
                                        }
                                        if( $reels['reel' . $r][$p] == '14' ) 
                                        {
                                            $reels['reel' . $r][$p] = '3';
                                        }
                                        if( $reels['reel' . $r][$p] == '15' ) 
                                        {
                                            $reels['reel' . $r][$p] = '0';
                                        }
                                        if( $reels['reel' . $r][$p] == '0' ) 
                                        {
                                            $reels['reel' . $r][0] = '0';
                                            $reels['reel' . $r][1] = '0';
                                            $reels['reel' . $r][2] = '0';
                                            $reels['reel' . $r][3] = '0';
                                            $reels['reel' . $r][4] = '0';
                                            $reels['reel' . $r][5] = '0';
                                            $reels['reel' . $r][6] = '0';
                                            break;
                                        }
                                    }
                                }
                                if( $postData['slotEvent'] == 'freespin' && $slotSettings->GetGameData('MedusaMonstersPTMwildRow') >= 1 ) 
                                {
                                    $wr = $slotSettings->GetGameData('MedusaMonstersPTMwildRow');
                                    $reels['reel' . $wr][0] = '0';
                                    $reels['reel' . $wr][1] = '0';
                                    $reels['reel' . $wr][2] = '0';
                                    $reels['reel' . $wr][3] = '0';
                                    $reels['reel' . $wr][4] = '0';
                                    $reels['reel' . $wr][5] = '0';
                                    $reels['reel' . $wr][6] = '0';
                                    $stickyResultArr = [];
                                    $sr = 0;
                                    for( $r = 1; $r <= 6; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 6; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == 0 ) 
                                            {
                                                $stickySymbolArr[$sr] = -1;
                                            }
                                            else if( $reels['reel' . $r][$p] == $stickySymbol ) 
                                            {
                                                $stickySymbolArr[$sr] = $stickySymbol;
                                            }
                                            else if( $stickySymbolArr[$sr] != -1 ) 
                                            {
                                                $reels['reel' . $r][$p] = $stickySymbol;
                                            }
                                            if( $stickySymbolArr[$sr] != -1 ) 
                                            {
                                                $stickyResultArr[] = $sr;
                                            }
                                            $sr++;
                                        }
                                    }
                                }
                                for( $k = 0; $k < $lines; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = $slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $s = [];
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 2];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 2];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 2];
                                            $s[5] = $reels['reel6'][$linesId[$k][5] - 1];
                                            if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                            {
                                                $mpl = 1;
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) && ($s[5] == $csym || in_array($s[5], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) && in_array($s[5], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) || in_array($s[5], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betLine * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('MedusaMonstersPTMBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                for( $r = 1; $r <= 6; $r++ ) 
                                {
                                    for( $p = 0; $p <= 6; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                            break;
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betLine * $lines * $bonusMpl;
                                if( $scattersCount >= 3 && $slotSettings->slotBonus ) 
                                {
                                    $scattersStr .= '"scattersType":"bonus",';
                                }
                                else if( $scattersWin > 0 ) 
                                {
                                    $scattersStr .= '"scattersType":"win",';
                                }
                                else
                                {
                                    $scattersStr .= '"scattersType":"none",';
                                }
                                $scattersStr .= ('"scattersWin":' . $scattersWin . '}');
                                $totalWin += $scattersWin;
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
                                        if( $postData['slotEvent'] == 'freespin' && $totalWin <= $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) ) 
                                        {
                                            break;
                                        }
                                        if( $scattersCount >= 3 && ($winType != 'bonus' || $postData['slotEvent'] == 'freespin') ) 
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
                                $slotSettings->SetGameData('MedusaMonstersPTMBonusWin', $slotSettings->GetGameData('MedusaMonstersPTMBonusWin') + $totalWin);
                                $slotSettings->SetGameData('MedusaMonstersPTMTotalWin', $totalWin);
                                $slotSettings->SetGameData('MedusaMonstersPTMstickySymbol', $stickySymbol);
                                $slotSettings->SetGameData('MedusaMonstersPTMwildRow', $slotSettings->GetGameData('MedusaMonstersPTMwildRow') - 1);
                            }
                            else
                            {
                                $slotSettings->SetGameData('MedusaMonstersPTMTotalWin', $totalWin);
                            }
                            $slotSettings->SetGameData('MedusaMonstersPTMBonusStart', false);
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('MedusaMonstersPTMBonusStart', true);
                                $slotSettings->SetGameData('MedusaMonstersPTMBonusStep', 0);
                                $wildReels = [];
                                $slotSettings->SetGameData('MedusaMonstersPTMwildReels', $wildReels);
                                $slotSettings->SetGameData('MedusaMonstersPTMIncreaseMpl', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('MedusaMonstersPTMBonusWin', 0);
                                $slotSettings->SetGameData('MedusaMonstersPTMFreeGames', $slotSettings->slotFreeCount);
                            }
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            $spinState = 'REGULAR';
                            if( $postData['spinType'] == 'free' ) 
                            {
                                $spinState = 'FREE';
                            }
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winString = implode(',', $lineWins);
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"stickySymbolArr":[' . implode(',', $stickySymbolArr) . '],"wildRow":' . $slotSettings->GetGameData('MedusaMonstersPTMwildRow') . ',"stickySymbol":' . $stickySymbol . ',"IncreaseMpl":1,"linesArr":[' . implode(',', $postData['lines']) . '],"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('MedusaMonstersPTMFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('MedusaMonstersPTMCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('MedusaMonstersPTMBonusWin') . ',"FreeMpl":' . $slotSettings->GetGameData('MedusaMonstersPTMFreeMpl') . ',"freeStartWin":' . $slotSettings->GetGameData('MedusaMonstersPTMFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                            $result_tmp[] = '3:::{"data":{"spinType":"' . $spinState . '","ww":' . $totalWin . ',"wt":"' . $winType . '","reelset":' . $cReelIndex . ',"ewp":[0],"ssp":[],"credit":' . $balanceInCents . ',"results":[' . implode(',', $reels['rp']) . '],"windowId":"iwBwxw"},"ID":49242,"umid":48}';
                            if( $winType2 == 'bonus2' && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $winsArr = [
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
                                    3, 
                                    3, 
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $jids = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $jid = 0;
                                $JackWinID = $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID') + 1;
                                shuffle($winsArr);
                                for( $i = 0; $i < 20; $i++ ) 
                                {
                                    if( $jids[$winsArr[$i]] == 2 ) 
                                    {
                                        $winsArr[$i] = $JackWinID;
                                    }
                                    $jids[$winsArr[$i]]++;
                                    if( $jids[$winsArr[$i]] >= 3 ) 
                                    {
                                        $jid = $winsArr[$i];
                                        break;
                                    }
                                }
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                                $slotSettings->SetBalance($slotSettings->slotJackpot[$jid - 1]);
                                $jackWin = $slotSettings->slotJackpot[$jid - 1] * 100;
                                $jWin = $slotSettings->slotJackpot[$jid - 1];
                                $result_tmp[] = '3:::{"data":{"startTime":1,"winningLevel":' . $jid . ',"totalWin":' . $jackWin . ',"reelInfo":[' . implode(',', $winsArr) . '],"windowId":"33zr6v"},"ID":40071,"umid":40}';
                                $slotSettings->SaveLogReport($response, $betLine, $lines, $jWin, 'JPG');
                            }
                            else
                            {
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":60}';
                            }
                            $result_tmp[] = '3:::{"data":{"windowId":"Hr1cOy"},"ID":40072,"umid":44}';
                            $result_tmp[] = '3:::{"data":{"typeBalance":0,"currency":"' . $slotSettings->slotCurrency . '","balanceInCents":' . $balanceInCents . ',"deltaBalanceInCents":1},"ID":40085}';
                            $result_tmp[] = '3:::{"balanceInfo":{"clientType":"casino","totalBalance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","balanceChange":0},"ID":10006,"umid":45}';
                            $response = implode('------', $result_tmp);
                            $slotSettings->SaveGameData();
                            $slotSettings->SaveGameDataStatic();
                            echo $response;
                        }
                        switch( $umid ) 
                        {
                            case '49244':
                                $stickySymbol = $slotSettings->GetGameData('MedusaMonstersPTMstickySymbol');
                                $result_tmp[] = '3:::{"data":{"stickySymbolName":"THEME_' . $stickySymbol . '","windowId":"gEpIWw"},"ID":49245,"umid":80}';
                                break;
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
                                $result_tmp[] = '3:::{"data":{"jackpotUpdates":{"mrj":[{"coinSize":400,"jackpot":' . ($slotSettings->slotJackpot[3] * 100) . '},{"coinSize":300,"jackpot":' . ($slotSettings->slotJackpot[2] * 100) . '},{"coinSize":200,"jackpot":' . ($slotSettings->slotJackpot[1] * 100) . '},{"coinSize":100,"jackpot":' . ($slotSettings->slotJackpot[0] * 100) . '}]}},"ID":40042,"umid":10}';
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
                            case '40066':
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 100;
                                }
                                $result_tmp[] = '3:::{"data":{"funNoticeGames":0,"funNoticePayouts":0,"gameGroup":"aogmm","minBet":0,"maxBet":0,"minPosBet":0,"maxPosBet":50000,"coinSizes":[' . implode(',', $gameBets) . ']},"ID":40025,"umid":21}';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeMpl', $lastEvent->serverResponse->FreeMpl);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'LinesArr', $lastEvent->serverResponse->linesArr);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'IncreaseMpl', $lastEvent->serverResponse->IncreaseMpl);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'wildRow', $lastEvent->serverResponse->wildRow);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'stickySymbol', $lastEvent->serverResponse->stickySymbol);
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'brokenGames', 'aogmm');
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
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'brokenGames') != '' ) 
                                {
                                    $result_tmp[] = '3:::{"data":{"freeSpinsData":{"numFreeSpins":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . ',"coinsize":' . ($slotSettings->GetGameData($slotSettings->slotId . 'Bet') * 100) . ',"rows":[' . implode(',', $slotSettings->GetGameData($slotSettings->slotId . 'LinesArr')) . '],"gamewin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') * 100) . ',"freespinwin":' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . ',"freespinTriggerReels":[51,87,71,68,78,17],"coins":0,"multiplier":1,"mode":1,"startBonus":1},"bonusGameName":"bonus_freespins_aogmm","mgReelIdx":7,"medusaReelIdx":' . (6 - $slotSettings->GetGameData('MedusaMonstersPTMwildRow')) . ',"stickySymbolName":"THEME_' . $slotSettings->GetGameData('MedusaMonstersPTMstickySymbol') . '","stickyPositions":[],"jackpotWin":0,"reelinfo":[51,26,56,43,65,84],"windowId":"g5ywOU"},"ID":49247,"umid":30}';
                                }
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
