<?php 
namespace VanguardLTE\Games\ForestNymphCT
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
                        if( !isset($_POST['request']) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"Invalid request state"}';
                            exit( $response );
                        }
                        $postData = json_decode(trim($_POST['request']), true);
                        $postData = $postData[0];
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        $aid = '';
                        if( isset($postData['type']) && $postData['command'] == 'continue' && $postData['type'] == 'can_play_red_black_double' && isset($postData['continue_params']['double_key']) ) 
                        {
                            $postData['command'] = 'gamble';
                        }
                        if( $postData['command'] == 'gamble' && $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid gamble state"}';
                            exit( $response );
                        }
                        $responseData = json_decode(trim(file_get_contents(dirname(__FILE__) . '/response_template.json')), true);
                        $responseDataOrig = $responseData;
                        $aid = (string)$postData['command'];
                        switch( $aid ) 
                        {
                            case 'create_session':
                                $slotSettings->SetGameData('ForestNymphCTCards', [
                                    19, 
                                    44, 
                                    0, 
                                    37, 
                                    8, 
                                    28, 
                                    33
                                ]);
                                for( $i = 0; $i < count($slotSettings->Denominations); $i++ ) 
                                {
                                    $responseData['create_session'][0]['enabled_denoms'][] = (object)[
                                        'label' => '' . sprintf('%01.2f', $slotSettings->Denominations[$i]), 
                                        'id' => '' . sprintf('%01.2f', $slotSettings->Denominations[$i]), 
                                        'viewLabel' => '' . $slotSettings->CurrentDenom, 
                                        'value' => $slotSettings->Denominations[$i], 
                                        'cents' => $slotSettings->Denominations[$i] * 100
                                    ];
                                }
                                $responseData['create_session'][0]['account_status']['currency'] = $slotSettings->slotCurrency;
                                $responseData['create_session'][0]['account_status']['currency_code'] = $slotSettings->slotCurrency;
                                $result_tmp[] = json_encode($responseData['create_session']);
                                break;
                            case 'continue':
                                $gameBets = $slotSettings->Bet;
                                $responseData['continue'][0]['account_status']['credit'] = round($balanceInCents);
                                $result_tmp[] = json_encode($responseData['continue']);
                                break;
                            case 'init_game':
                                $gameBets = $slotSettings->Bet;
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData('ForestNymphCTBonusWin', 0);
                                $slotSettings->SetGameData('ForestNymphCTFreeGames', 0);
                                $slotSettings->SetGameData('ForestNymphCTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('ForestNymphCTCurrentFreeGame0', 0);
                                $slotSettings->SetGameData('ForestNymphCTTotalWin', 0);
                                $slotSettings->SetGameData('ForestNymphCTFreeBalance', 0);
                                $responseData['init_game'][0]['game_config']['game_denoms'] = [];
                                for( $i = 0; $i < count($slotSettings->Denominations); $i++ ) 
                                {
                                    $responseData['init_game'][0]['game_config']['game_denoms'][sprintf('%01.2f', $slotSettings->Denominations[$i])] = '' . sprintf('%01.2f', $slotSettings->Denominations[$i]);
                                }
                                $responseData['init_game'][0]['game_config']['game_denoms'] = (object)$responseData['init_game'][0]['game_config']['game_denoms'];
                                $responseData['init_game'][0]['account_status']['credit'] = round($balanceInCents);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame0', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Results', $lastEvent->serverResponse->result);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Reels', $lastEvent->serverResponse->reelsSymbols);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lastEvent->serverResponse->slotLines);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $lastEvent->serverResponse->slotBet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Denom', $lastEvent->serverResponse->slotDenom);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $responseData['init_game'][0]['game_config']['init_screen'][0] = [
                                        '' . $reels->reel1[0], 
                                        '' . $reels->reel2[0], 
                                        '' . $reels->reel3[0], 
                                        '' . $reels->reel4[0], 
                                        '' . $reels->reel5[0]
                                    ];
                                    $responseData['init_game'][0]['game_config']['init_screen'][1] = [
                                        '' . $reels->reel1[1], 
                                        '' . $reels->reel2[1], 
                                        '' . $reels->reel3[1], 
                                        '' . $reels->reel4[1], 
                                        '' . $reels->reel5[1]
                                    ];
                                    $responseData['init_game'][0]['game_config']['init_screen'][2] = [
                                        '' . $reels->reel1[2], 
                                        '' . $reels->reel2[2], 
                                        '' . $reels->reel3[2], 
                                        '' . $reels->reel4[2], 
                                        '' . $reels->reel5[2]
                                    ];
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet;
                                    $denom = $lastEvent->serverResponse->slotDenom;
                                    if( $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame') < $slotSettings->GetGameData('ForestNymphCTFreeGames') && $slotSettings->GetGameData('ForestNymphCTFreeGames') > 0 ) 
                                    {
                                        $responseData['init_game'][0]['game_config']['init_screen_subgame_label'] = 'free';
                                        $responseData['init_game'][0]['game_config']['main_subgame_label'] = 'free';
                                        $responseData['init_game'][0]['freeround_limit'] = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                        $responseData['init_game'][0]['freeround_play'] = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame0');
                                        $responseData['init_game'][0]['freeround_bet_per_line_cents'] = $bet * $denom * 100;
                                        $responseData['init_game'][0]['freeround_lines'] = $lines;
                                        $denomIndex = 0;
                                        for( $d = 0; $d < count($slotSettings->Denominations); $d++ ) 
                                        {
                                            if( $slotSettings->Denominations[$d] == $slotSettings->GetGameData($slotSettings->slotId . 'Denom') ) 
                                            {
                                                $denomIndex = $d;
                                            }
                                        }
                                        $responseData['init_game'][0]['account_status']['credit'] = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                                        $responseData['init_game'][0]['resume_game'] = (object)[
                                            'extra_bet' => 0, 
                                            'line_bet' => $bet / $denom, 
                                            'lines' => $lines, 
                                            'denomIndex' => 0, 
                                            'currDenom' => [
                                                'cents' => $denom * 100, 
                                                'value' => sprintf('%01.2f', $denom), 
                                                'label' => '' . sprintf('%01.2f', $denom), 
                                                'id' => '' . sprintf('%01.2f', $denom)
                                            ], 
                                            'last_result' => $lastEvent->serverResponse->result, 
                                            'account_status_after_bet' => $responseData['init_game'][0]['account_status'], 
                                            'game_config' => $responseData['init_game'][0]['game_config'], 
                                            'server_denom' => [
                                                'cents' => $denom * 100, 
                                                'value' => sprintf('%01.2f', $denom), 
                                                'label' => '' . sprintf('%01.2f', $denom), 
                                                'id' => '' . sprintf('%01.2f', $denom)
                                            ], 
                                            'game_denom' => '' . sprintf('%01.2f', $denom), 
                                            'enabled_denoms' => [
                                                'cents' => $denom * 100, 
                                                'value' => sprintf('%01.2f', $denom), 
                                                'label' => '' . sprintf('%01.2f', $denom), 
                                                'id' => '' . sprintf('%01.2f', $denom)
                                            ], 
                                            'resume_subgame_index' => $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame0') + 1
                                        ];
                                        $responseData['init_game'][0]['resume'] = true;
                                    }
                                }
                                else
                                {
                                    $lines = 15;
                                    $bet = $slotSettings->Bet[0];
                                    $denom = $slotSettings->Denominations[0];
                                }
                                $responseData['init_game'][0]['game_config']['line_step_bet_numbers'] = $gameBets;
                                $responseData['init_game'][0]['game_config']['line_step_bet_numbers_quick'] = $gameBets;
                                $responseData['init_game'][0]['game_config']['line_max_bet'] = $gameBets[count($gameBets) - 1];
                                $responseData['init_game'][0]['game_config']['line_min_bet'] = $gameBets[0];
                                $result_tmp[0] = '[{"freeround_limit":30,"status":{"status":"ok"},"game_config":{"app_name":"Forest Nymph","line_step_bet_numbers_quick":["0.01"," 0.02"," 0.05"," 0.10"," 0.20"],"allow_client_demo_request":0,"paytables":{"114":{"paytable_scatters":{"1":{"5":{"factor":"100","bonus_type":"free_games","free_games_extra_bet_only":0,"bonus_value":15,"ordering":44},"2":{"free_games_extra_bet_only":0,"ordering":41,"bonus_value":0,"factor":"2","bonus_type":"none"},"4":{"factor":"10","bonus_type":"free_games","bonus_value":15,"free_games_extra_bet_only":0,"ordering":43},"3":{"ordering":42,"free_games_extra_bet_only":0,"bonus_value":15,"bonus_type":"free_games","factor":"5"}}},"paytable":{"7":{"4":{"ordering":21,"factor":"50"},"3":{"factor":"10","ordering":10},"5":{"factor":"125","ordering":30}},"2":{"2":{"ordering":1,"factor":"2"},"5":{"factor":"100","ordering":25},"4":{"ordering":16,"factor":"25"},"3":{"ordering":4,"factor":"5"}},"12":{"5":{"ordering":38,"factor":"750"},"2":{"factor":"2","ordering":3},"4":{"ordering":32,"factor":"125"},"3":{"ordering":15,"factor":"25"}},"9":{"3":{"ordering":12,"factor":"15"},"4":{"factor":"75","ordering":23},"5":{"factor":"250","ordering":35}},"3":{"3":{"factor":"5","ordering":5},"4":{"ordering":17,"factor":"25"},"5":{"ordering":26,"factor":"100"}},"4":{"3":{"factor":"5","ordering":6},"4":{"factor":"25","ordering":18},"5":{"ordering":27,"factor":"100"}},"8":{"3":{"ordering":11,"factor":"15"},"4":{"factor":"75","ordering":22},"5":{"ordering":34,"factor":"250"}},"5":{"4":{"factor":"25","ordering":19},"3":{"factor":"5","ordering":7},"5":{"factor":"100","ordering":28}},"13":{"4":{"ordering":39,"factor":"1500"},"3":{"ordering":33,"factor":"250"},"5":{"ordering":40,"factor":"10000"},"2":{"ordering":8,"factor":"10"}},"11":{"3":{"ordering":14,"factor":"25"},"4":{"ordering":31,"factor":"125"},"5":{"ordering":37,"factor":"750"},"2":{"factor":"2","ordering":2}},"6":{"5":{"factor":"125","ordering":29},"3":{"ordering":9,"factor":"10"},"4":{"ordering":20,"factor":"50"}},"10":{"4":{"factor":"100","ordering":24},"3":{"ordering":13,"factor":"20"},"5":{"factor":"400","ordering":36}}},"paytable_wilds":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"multi":0,"expanding":0,"factor":"2"}}},"115":{"paytable_wilds":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"factor":"2","multi":0,"expanding":0}},"paytable_scatters":{"1":{"2":{"ordering":41,"free_games_extra_bet_only":0,"bonus_value":0,"factor":"6","bonus_type":"none"},"5":{"factor":"300","bonus_type":"free_games","ordering":44,"free_games_extra_bet_only":0,"bonus_value":15},"4":{"factor":"30","bonus_type":"free_games","free_games_extra_bet_only":0,"bonus_value":15,"ordering":43},"3":{"ordering":42,"free_games_extra_bet_only":0,"bonus_value":15,"bonus_type":"free_games","factor":"15"}}},"paytable":{"11":{"4":{"ordering":31,"factor":"375"},"3":{"factor":"75","ordering":14},"2":{"factor":"6","ordering":2},"5":{"factor":"2250","ordering":37}},"8":{"3":{"factor":"45","ordering":11},"4":{"factor":"225","ordering":22},"5":{"ordering":34,"factor":"750"}},"13":{"2":{"ordering":8,"factor":"30"},"5":{"ordering":40,"factor":"10000"},"4":{"ordering":39,"factor":"4500"},"3":{"ordering":33,"factor":"750"}},"5":{"5":{"factor":"300","ordering":28},"4":{"factor":"75","ordering":19},"3":{"ordering":7,"factor":"15"}},"10":{"3":{"ordering":13,"factor":"60"},"4":{"factor":"300","ordering":24},"5":{"ordering":36,"factor":"1200"}},"6":{"5":{"factor":"375","ordering":29},"4":{"ordering":20,"factor":"150"},"3":{"ordering":9,"factor":"30"}},"2":{"2":{"ordering":1,"factor":"6"},"5":{"ordering":25,"factor":"300"},"3":{"ordering":4,"factor":"15"},"4":{"factor":"75","ordering":16}},"7":{"5":{"ordering":30,"factor":"375"},"3":{"factor":"30","ordering":10},"4":{"factor":"150","ordering":21}},"9":{"3":{"ordering":12,"factor":"45"},"4":{"ordering":23,"factor":"225"},"5":{"factor":"750","ordering":35}},"3":{"3":{"factor":"15","ordering":5},"4":{"factor":"75","ordering":17},"5":{"ordering":26,"factor":"300"}},"4":{"4":{"factor":"75","ordering":18},"3":{"factor":"15","ordering":6},"5":{"ordering":27,"factor":"300"}},"12":{"4":{"factor":"375","ordering":32},"3":{"factor":"75","ordering":15},"2":{"ordering":3,"factor":"6"},"5":{"factor":"2250","ordering":38}}}}},"win_limit":0,"skill_stop_enabled":1,"subgames_labels":{"main":{"subgame_id":"114","symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]},"free":{"subgame_id":"115","symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]}},"autoplay":1,"last_game_recall_enabled":0,"celebration":0,"lines_step_numbers":[3,5,7,10,15],"lines_min_number":1,"game_denoms":{"1.00":"1.00"},"line_max_bet":" 0.20","init_screen_subgame_label":"free","game_type":null,"reel_rotate_mintime":0,"autoplay_steps":[0,5,10,25,50,100,500,1000],"line_step_bet_numbers_quick_increment":"steps_only","line_step_bet_numbers":["0.01"," 0.02"," 0.05"," 0.10"," 0.20"],"lines_max_number":15,"reel_anim_real":0,"extra_bet_enabled":false,"result_math_real":"","lines":[["1","1","1","1",1],["0","0","0","0",0],["2","2","2","2",2],["0","1","2",1,0],["2","1","0","1",2],["0","0","1",2,2],["2","2","2","0",0],["1","0","1","2",1],["1","2","1","0",1],["0","1",0,1,0],["2","1","2",1,2],["1","1","0",0,0],["1","1","2","2",2],["0","0","1","0",0],["2","2","1",2,2]],"paytable_scatters_old":{"1":{"3":{"bonus_type":"free_games","factor":"5","bonus_value":15},"4":{"bonus_value":15,"factor":"10","bonus_type":"free_games"},"2":{"bonus_value":0,"factor":"2","bonus_type":"none"},"5":{"bonus_type":"free_games","factor":"100","bonus_value":15}}},"columns":5,"init_screen":[["12","4","3","2","2"],["4","2","2","5","4"],["2","3","4","4","5"]],"main_subgame_label":"free","directory_name":"ForestNimph","game_win_check_type":"lines","demo":null,"main_subgame_reels_set":"114","lines_mode":"15","double":{"double_enable":1,"double_limit":50,"double_type":"rb","double_enter_limit":10},"rows":3,"reels_sets":{"114":{"reels":[[2,4,10,2,4,11,9,2,6,9,2,5,9,8,2,4,8,2,6,1,2,5,10,8,4,5,11,9,6,4,13,9,7,4,13,3,7,4,10,8,11,10,5,3,12,6,4,12,3,4,12,11,6,12,10,9,12,9,5,4,2,4,6,11],[6,4,5,2,7,3,4,2,7,5,3,6,5,3,4,5,3,7,4,3,5,7,13,4,5,11,7,8,4,3,7,6,9,10,2,3,7,4,3,1,4,5,3,9,4,3,9,6,4,10,3,11,9,12,4,8,13,7,10,12,6,8,4,12],[2,13,3,2,7,3,2,7,1,2,6,8,2,7,6,2,7,8,2,7,8,2,6,7,2,6,4,5,2,13,5,6,11,2,8,5,3,12,2,8,13,3,6,2,9,5,3,10,5,3,8,5,9,3,5,8,4,5,4,3,2,5,3,4],[13,7,4,8,5,2,4,5,13,9,5,4,4,9,2,8,4,2,1,6,2,1,4,6,1,2,5,6,7,8,9,13,8,4,13,4,6,8,3,9,8,4,6,8,11,9,7,1,12,9,3,11,9,1,3,6,13,8,3,6,9,11,7,10],[3,2,4,9,2,12,4,3,1,2,4,7,3,4,3,6,7,5,8,3,13,8,2,6,7,5,8,7,9,1,8,9,11,8,4,9,4,8,5,9,8,6,9,7,8,9,7,4,5,9,8,7,9,8,3,7,9,6,7,5,9,8,10,9]],"symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]},"115":{"symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"],"reels":[[2,7,9,10,4,6,11,4,2,11,5,2,11,2,5,4,6,9,12,4,2,8,3,10,6,2,1,3,4,5,8,2,11,7,12,5,7,6,12,9,3,2,4,5,11,2,4,13,6,2,12,4,1,8,2,6,1,5,2,12,4,5,12,2],[5,11,4,2,3,4,2,4,3,12,4,2,12,4,3,2,4,12,5,2,3,12,5,6,11,4,5,2,3,6,7,11,5,12,4,5,3,4,7,4,11,7,4,2,6,4,12,11,4,12,2,9,4,13,3,6,1,8,10,5,2,1,6,7],[2,3,2,6,4,3,2,12,5,2,7,5,2,4,7,1,4,2,5,3,2,4,5,7,8,3,5,7,3,2,10,4,2,3,2,6,3,2,5,6,3,13,5,6,2,7,3,9,2,3,5,2,4,5,7,2,4,5,7,2,3,2,4,11],[3,7,5,4,7,3,4,7,5,3,4,6,3,5,1,4,4,6,9,13,2,6,3,2,4,6,2,4,3,2,3,1,7,2,5,4,3,12,4,1,5,3,2,4,2,9,5,3,2,5,4,6,10,8,3,5,4,6,2,4,3,2,3,11],[2,4,5,3,6,5,3,4,2,4,6,2,7,4,2,3,6,1,2,7,5,3,6,4,5,7,6,3,5,4,3,12,4,4,3,10,4,5,3,2,8,6,4,2,13,5,3,4,6,4,7,3,4,9,6,1,5,7,6,2,5,4,3,11]]}},"license":"","lines_per_credit":1,"lines_step_numbers_quick":[1,5,7,9,15],"paytable_wilds_old":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"multi":0,"expanding":0,"factor":"2"}},"line_min_bet":"0.01","paytable_old":{"10":{"5":"400","3":"20","4":"100"},"6":{"3":"10","4":"50","5":"125"},"5":{"5":"100","3":"5","4":"25"},"13":{"4":"1500","3":"250","2":"10","5":"10000"},"11":{"2":"2","5":"750","3":"25","4":"125"},"8":{"5":"250","3":"15","4":"75"},"4":{"5":"100","3":"5","4":"25"},"3":{"5":"100","3":"5","4":"25"},"9":{"4":"75","3":"15","5":"250"},"12":{"2":"2","5":"750","4":"125","3":"25"},"2":{"5":"100","2":"2","4":"25","3":"5"},"7":{"5":"125","3":"10","4":"50"}}},"server_version":{"game_protocol_version":1.1999999999999999555910790149937383830547332763671875,"rgs_core_checksum":"SHA-256:c13945d17b3e67a0d88a1180aacb111af201fff5aed08de8f09ce75d025f9b2e; SHA-1:63c2daa370ac51314173f56502b34227f2462146; ","icasino_checksum":"SHA-256:5f70b3868f17f15d92befd2b1c8a783f4f06a13784e8271c674338b5dbfc15a3; SHA-1:4510abec79390732c388654effeedb0f95e2b416; ","rgs_protocol_version":"1.1","game_checksum":"SHA-256:a22f54fe25f90aa280901bb9aaac34157b0948c26a871c13b4e0623ce2af7aa9; SHA-1:3b4c1281ee7c00e53ce04062ba5147845bf779ff; "},"account_status":{"freeround_limit":0,"support_cashout":false,"currency_code":"EUR","thousands_separator":",","digits_after_decimal_separator":2,"credit":4734,"rgs_balance":0,"support_buyin":false,"currency_sign":"","seamless_credit":1,"currency":"EUR","decimal_separator":"."},"_req_sequence_number":12,"_req_game_name":"ForestNimph","freeround_lines":15,"_req_command":"init_game","_req_session_id":"7546d45a45a22fce1e3536eb51f0e88b","server_curr_timestamp":{"timestamp_epoch":1568707565.03125,"timestamp_http":"2019\/09\/17 08:06:05","timestamp":"2019-09-17 08:06:05.031247+03"},"resume":true,"show_win":"last_win","finish_game_command":"fast","freeround_bet_per_line_cents":1,"freeround_play":8,"resume_game":{"extra_bet":0,"line_bet":0.01000000000000000020816681711721685132943093776702880859375,"lines":15,"denomIndex":0,"currDenom":{"cents":100,"value":"1.00","label":"1.00","id":"1.00"},"last_result":[{"increase_factor_bonus":null,"reels_dir":["down","down","down","down","down"],"subgame_label":"main","step_number":0,"lines_win":[],"stop_indexes":[[21,22,23],[40,41,42],[6,7,8],[46,47,48],[8,9,10]],"bonus":null,"subgame_label_game_total":1,"type_descr":"SPIN","win":"75","result_reels":[["5","4","2","7","1"],["10","5","7","1","2"],["8","3","1","12","4"]],"free_game_total":0,"total_bet":15,"start_subgame_timestamp":"2019-09-22 11:06:48.249832+03","expanded_wilds":[],"msgs":[],"add_wilds":0,"reels_stop_timing":[1,1,1,1,1],"stop_reels_pos":[[21,40,6,46,8],[22,41,7,47,9],[23,42,8,48,10]],"end_subgame_timestamp":null,"scatters_win":[{"positions":[[2,2],[0,3],[1,4]],"increase_factor_bonus":[],"bonus_type":"free_games","wilds":null,"wild_exists":null,"bonus_value":"15","msgs":null,"factor":"5","symbols_positions":{"1":{"positions":[[2,2],[0,3],[1,4]]}},"symbol":"1","subgame_win_factor":1,"count":"3","win":"0.75","paytable_factor":5}],"subgame_bonus":[],"reels_sets_name":"114","free_games":15,"total_win":75,"stop_reels":[["5","10","8"],["4","5","3"],["2","7","1"],["7","1","12"],["1","2","4"]],"type":"slot_subgame","request_params":{"game_denom":"0.01","lines":"15","total_win_limit":null,"total_bet":15,"extra_bet":0,"demo":null,"line_bet":"1"},"skip_reels_anim":false,"real_reels_in_use":false,"reels_start_timing":[1,1,1,1,1],"is_last":false,"is_free":false,"subgame_factor":"1","free_game_number":0,"reels_anim_time":1,"wait_for_button":false,"subgame_label_game_number":1},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":1,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":1,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["4","5","11"],["5","2","1"],["7","3","9"],["2","5","4"],["6","4","5"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["4","5","7","2","6"],["5","2","3","5","4"],["11","1","9","4","5"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":2,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":2,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["7","6","12"],["6","11","4"],["2","3","2"],["3","5","4"],["3","5","4"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["7","6","2","3","3"],["6","11","3","5","5"],["12","4","2","4","4"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":3,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":3,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["7","12","5"],["1","8","10"],["13","5","6"],["3","2","3"],["4","3","10"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["7","1","13","3","4"],["12","8","5","2","3"],["5","10","6","3","10"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":4,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":4,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["6","9","12"],["12","4","5"],["2","7","5"],["3","2","4"],["5","3","2"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["6","12","2","3","5"],["9","4","7","2","3"],["12","5","5","4","2"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":5,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":5,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["7","9","10"],["12","4","5"],["7","8","3"],["4","7","5"],["6","4","2"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["7","12","7","4","6"],["9","4","8","7","4"],["10","5","3","5","2"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":6,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":6,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["4","6","9"],["12","2","9"],["9","2","3"],["3","5","1"],["6","4","2"]],"reels_sets_name":"115","total_win":"1.50","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["4","12","9","3","6"],["6","2","2","5","4"],["9","9","3","1","2"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":15,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":7,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":7,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["9","12","4"],["11","4","5"],["4","7","1"],["1","7","2"],["6","1","5"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["9","11","4","1","6"],["12","4","7","7","1"],["4","5","1","2","5"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[{"factor":5,"msgs":null,"wilds":[],"symbol_count":"3","positions":[0,1,2],"symbols":["4","4","4"],"paytable_factor":5,"win":"0.15","symbol":"4","subgame_win_factor":1,"line_number":4}],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.90","winType ":"bonus"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":8,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":8,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["6","12","9"],["4","5","3"],["2","3","2"],["2","6","3"],["6","1","5"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["6","4","2","2","6"],["12","5","3","6","1"],["9","3","2","3","5"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":9,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":9,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["5","4","6"],["4","2","4"],["8","3","5"],["5","1","4"],["6","4","2"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["5","4","8","5","6"],["4","2","3","1","4"],["6","4","5","4","2"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":10,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":10,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["8","3","10"],["4","5","2"],["2","3","5"],["2","4","6"],["3","6","4"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["8","4","2","2","3"],["3","5","3","4","6"],["10","2","5","6","4"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":11,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":11,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["10","4","6"],["5","2","3"],["2","3","2"],["6","3","5"],["5","7","6"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["10","5","2","6","5"],["4","2","3","3","7"],["6","3","2","5","6"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":12,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":12,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["4","6","11"],["2","12","4"],["3","2","12"],["4","6","3"],["7","6","2"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["4","2","3","4","7"],["6","12","2","6","6"],["11","4","12","3","2"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":13,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":13,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["12","4","5"],["2","6","4"],["13","5","6"],["7","2","5"],["2","3","6"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["12","2","13","7","2"],["4","6","5","2","3"],["5","4","6","5","6"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":14,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":14,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["5","2","11"],["4","5","3"],["11","2","3"],["2","4","3"],["4","2","4"]],"reels_sets_name":"115","total_win":"2.40","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["5","4","11","2","4"],["2","5","2","4","2"],["11","3","3","3","4"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.00","winType ":"none"},{"is_last":false,"is_free":true,"reels_start_timing":[1,1,1,1,1],"real_reels_in_use":false,"free_game_number":15,"subgame_factor":"3","reels_anim_time":1,"subgame_label_game_number":15,"wait_for_button":false,"end_subgame_timestamp":null,"stop_reels_pos":[[10,44,34,40,0],[11,45,35,41,1],[12,46,36,42,2]],"subgame_bonus":[],"scatters_win":[],"type":"slot_subgame","stop_reels":[["12","4","2"],["4","2","3"],["3","2","4"],["2","5","4"],["2","4","5"]],"reels_sets_name":"115","total_win":"2.52","free_games":0,"skip_reels_anim":false,"request_params":{"total_bet":0.1499999999999999944488848768742172978818416595458984375,"extra_bet":0,"demo":null,"line_bet":"0.01","game_denom":1,"lines":15,"total_win_limit":null},"result_reels":[["12","4","3","2","2"],["4","2","2","5","4"],["2","3","4","4","5"]],"expanded_wilds":[],"start_subgame_timestamp":null,"total_bet":15,"free_game_total":30,"add_wilds":0,"msgs":[],"reels_stop_timing":[1,1,1,1,1],"step_number":1,"subgame_label":"free","reels_dir":["down","down","down","down","down"],"increase_factor_bonus":null,"stop_indexes":[[10,11,12],[44,45,46],[34,35,36],[40,41,42],[0,1,2]],"lines_win":[{"factor":2,"msgs":null,"wilds":[],"symbol_count":"2","positions":[0,1],"symbols":["2","2"],"paytable_factor":2,"win":"0.06","symbol":"2","subgame_win_factor":1,"line_number":4},{"factor":2,"msgs":null,"wilds":[],"symbol_count":"2","positions":[0,1],"symbols":["2","2"],"paytable_factor":2,"win":"0.06","symbol":"2","subgame_win_factor":1,"line_number":10}],"type_descr":"FREE GAME","subgame_label_game_total":15,"bonus":null,"win":"0.12","winType ":"win"}],"account_status_after_bet":{"freeround_limit":0,"support_cashout":false,"currency_code":"EUR","thousands_separator":",","digits_after_decimal_separator":2,"credit":4734,"rgs_balance":0,"support_buyin":false,"currency_sign":"","seamless_credit":1,"currency":"EUR","decimal_separator":"."},"game_config":{"app_name":"Forest Nymph","line_step_bet_numbers_quick":[2,4,6,10,20],"allow_client_demo_request":0,"paytables":{"114":{"paytable_scatters":{"1":{"5":{"factor":"100","bonus_type":"free_games","free_games_extra_bet_only":0,"bonus_value":15,"ordering":44},"2":{"free_games_extra_bet_only":0,"ordering":41,"bonus_value":0,"factor":"2","bonus_type":"none"},"4":{"factor":"10","bonus_type":"free_games","bonus_value":15,"free_games_extra_bet_only":0,"ordering":43},"3":{"ordering":42,"free_games_extra_bet_only":0,"bonus_value":15,"bonus_type":"free_games","factor":"5"}}},"paytable":{"7":{"4":{"ordering":21,"factor":"50"},"3":{"factor":"10","ordering":10},"5":{"factor":"125","ordering":30}},"2":{"2":{"ordering":1,"factor":"2"},"5":{"factor":"100","ordering":25},"4":{"ordering":16,"factor":"25"},"3":{"ordering":4,"factor":"5"}},"12":{"5":{"ordering":38,"factor":"750"},"2":{"factor":"2","ordering":3},"4":{"ordering":32,"factor":"125"},"3":{"ordering":15,"factor":"25"}},"9":{"3":{"ordering":12,"factor":"15"},"4":{"factor":"75","ordering":23},"5":{"factor":"250","ordering":35}},"3":{"3":{"factor":"5","ordering":5},"4":{"ordering":17,"factor":"25"},"5":{"ordering":26,"factor":"100"}},"4":{"3":{"factor":"5","ordering":6},"4":{"factor":"25","ordering":18},"5":{"ordering":27,"factor":"100"}},"8":{"3":{"ordering":11,"factor":"15"},"4":{"factor":"75","ordering":22},"5":{"ordering":34,"factor":"250"}},"5":{"4":{"factor":"25","ordering":19},"3":{"factor":"5","ordering":7},"5":{"factor":"100","ordering":28}},"13":{"4":{"ordering":39,"factor":"1500"},"3":{"ordering":33,"factor":"250"},"5":{"ordering":40,"factor":"10000"},"2":{"ordering":8,"factor":"10"}},"11":{"3":{"ordering":14,"factor":"25"},"4":{"ordering":31,"factor":"125"},"5":{"ordering":37,"factor":"750"},"2":{"factor":"2","ordering":2}},"6":{"5":{"factor":"125","ordering":29},"3":{"ordering":9,"factor":"10"},"4":{"ordering":20,"factor":"50"}},"10":{"4":{"factor":"100","ordering":24},"3":{"ordering":13,"factor":"20"},"5":{"factor":"400","ordering":36}}},"paytable_wilds":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"multi":0,"expanding":0,"factor":"2"}}},"115":{"paytable_wilds":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"factor":"2","multi":0,"expanding":0}},"paytable_scatters":{"1":{"2":{"ordering":41,"free_games_extra_bet_only":0,"bonus_value":0,"factor":"6","bonus_type":"none"},"5":{"factor":"300","bonus_type":"free_games","ordering":44,"free_games_extra_bet_only":0,"bonus_value":15},"4":{"factor":"30","bonus_type":"free_games","free_games_extra_bet_only":0,"bonus_value":15,"ordering":43},"3":{"ordering":42,"free_games_extra_bet_only":0,"bonus_value":15,"bonus_type":"free_games","factor":"15"}}},"paytable":{"11":{"4":{"ordering":31,"factor":"375"},"3":{"factor":"75","ordering":14},"2":{"factor":"6","ordering":2},"5":{"factor":"2250","ordering":37}},"8":{"3":{"factor":"45","ordering":11},"4":{"factor":"225","ordering":22},"5":{"ordering":34,"factor":"750"}},"13":{"2":{"ordering":8,"factor":"30"},"5":{"ordering":40,"factor":"10000"},"4":{"ordering":39,"factor":"4500"},"3":{"ordering":33,"factor":"750"}},"5":{"5":{"factor":"300","ordering":28},"4":{"factor":"75","ordering":19},"3":{"ordering":7,"factor":"15"}},"10":{"3":{"ordering":13,"factor":"60"},"4":{"factor":"300","ordering":24},"5":{"ordering":36,"factor":"1200"}},"6":{"5":{"factor":"375","ordering":29},"4":{"ordering":20,"factor":"150"},"3":{"ordering":9,"factor":"30"}},"2":{"2":{"ordering":1,"factor":"6"},"5":{"ordering":25,"factor":"300"},"3":{"ordering":4,"factor":"15"},"4":{"factor":"75","ordering":16}},"7":{"5":{"ordering":30,"factor":"375"},"3":{"factor":"30","ordering":10},"4":{"factor":"150","ordering":21}},"9":{"3":{"ordering":12,"factor":"45"},"4":{"ordering":23,"factor":"225"},"5":{"factor":"750","ordering":35}},"3":{"3":{"factor":"15","ordering":5},"4":{"factor":"75","ordering":17},"5":{"ordering":26,"factor":"300"}},"4":{"4":{"factor":"75","ordering":18},"3":{"factor":"15","ordering":6},"5":{"ordering":27,"factor":"300"}},"12":{"4":{"factor":"375","ordering":32},"3":{"factor":"75","ordering":15},"2":{"ordering":3,"factor":"6"},"5":{"factor":"2250","ordering":38}}}}},"win_limit":0,"skill_stop_enabled":1,"subgames_labels":{"main":{"subgame_id":"114","symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]},"free":{"subgame_id":"115","symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]}},"autoplay":1,"last_game_recall_enabled":0,"celebration":0,"lines_step_numbers":[3,5,7,10,15],"lines_min_number":1,"game_denoms":{"1.00":"1.00"},"line_max_bet":30,"init_screen_subgame_label":"free","game_type":null,"reel_rotate_mintime":0,"autoplay_steps":[0,5,10,25,50,100,500,1000],"line_step_bet_numbers_quick_increment":"steps_only","line_step_bet_numbers":[2,4,6,10,20],"lines_max_number":15,"reel_anim_real":0,"extra_bet_enabled":false,"result_math_real":"","lines":[["1","1","1","1",1],["0","0","0","0",0],["2","2","2","2",2],["0","1","2",1,0],["2","1","0","1",2],["0","0","1",2,2],["2","2","2","0",0],["1","0","1","2",1],["1","2","1","0",1],["0","1",0,1,0],["2","1","2",1,2],["1","1","0",0,0],["1","1","2","2",2],["0","0","1","0",0],["2","2","1",2,2]],"paytable_scatters_old":{"1":{"3":{"bonus_type":"free_games","factor":"5","bonus_value":15},"4":{"bonus_value":15,"factor":"10","bonus_type":"free_games"},"2":{"bonus_value":0,"factor":"2","bonus_type":"none"},"5":{"bonus_type":"free_games","factor":"100","bonus_value":15}}},"columns":5,"init_screen":[["12","4","3","2","2"],["4","2","2","5","4"],["2","3","4","4","5"]],"main_subgame_label":"free","directory_name":"ForestNimph","game_win_check_type":"lines","demo":null,"main_subgame_reels_set":"114","lines_mode":"15","double":{"double_enable":1,"double_limit":50,"double_type":"rb","double_enter_limit":10},"rows":3,"reels_sets":{"114":{"reels":[[2,4,10,2,4,11,9,2,6,9,2,5,9,8,2,4,8,2,6,1,2,5,10,8,4,5,11,9,6,4,13,9,7,4,13,3,7,4,10,8,11,10,5,3,12,6,4,12,3,4,12,11,6,12,10,9,12,9,5,4,2,4,6,11],[6,4,5,2,7,3,4,2,7,5,3,6,5,3,4,5,3,7,4,3,5,7,13,4,5,11,7,8,4,3,7,6,9,10,2,3,7,4,3,1,4,5,3,9,4,3,9,6,4,10,3,11,9,12,4,8,13,7,10,12,6,8,4,12],[2,13,3,2,7,3,2,7,1,2,6,8,2,7,6,2,7,8,2,7,8,2,6,7,2,6,4,5,2,13,5,6,11,2,8,5,3,12,2,8,13,3,6,2,9,5,3,10,5,3,8,5,9,3,5,8,4,5,4,3,2,5,3,4],[13,7,4,8,5,2,4,5,13,9,5,4,4,9,2,8,4,2,1,6,2,1,4,6,1,2,5,6,7,8,9,13,8,4,13,4,6,8,3,9,8,4,6,8,11,9,7,1,12,9,3,11,9,1,3,6,13,8,3,6,9,11,7,10],[3,2,4,9,2,12,4,3,1,2,4,7,3,4,3,6,7,5,8,3,13,8,2,6,7,5,8,7,9,1,8,9,11,8,4,9,4,8,5,9,8,6,9,7,8,9,7,4,5,9,8,7,9,8,3,7,9,6,7,5,9,8,10,9]],"symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"]},"115":{"symbols":["1","2","3","4","5","6","7","8","9","10","11","12","13"],"reels":[[2,7,9,10,4,6,11,4,2,11,5,2,11,2,5,4,6,9,12,4,2,8,3,10,6,2,1,3,4,5,8,2,11,7,12,5,7,6,12,9,3,2,4,5,11,2,4,13,6,2,12,4,1,8,2,6,1,5,2,12,4,5,12,2],[5,11,4,2,3,4,2,4,3,12,4,2,12,4,3,2,4,12,5,2,3,12,5,6,11,4,5,2,3,6,7,11,5,12,4,5,3,4,7,4,11,7,4,2,6,4,12,11,4,12,2,9,4,13,3,6,1,8,10,5,2,1,6,7],[2,3,2,6,4,3,2,12,5,2,7,5,2,4,7,1,4,2,5,3,2,4,5,7,8,3,5,7,3,2,10,4,2,3,2,6,3,2,5,6,3,13,5,6,2,7,3,9,2,3,5,2,4,5,7,2,4,5,7,2,3,2,4,11],[3,7,5,4,7,3,4,7,5,3,4,6,3,5,1,4,4,6,9,13,2,6,3,2,4,6,2,4,3,2,3,1,7,2,5,4,3,12,4,1,5,3,2,4,2,9,5,3,2,5,4,6,10,8,3,5,4,6,2,4,3,2,3,11],[2,4,5,3,6,5,3,4,2,4,6,2,7,4,2,3,6,1,2,7,5,3,6,4,5,7,6,3,5,4,3,12,4,4,3,10,4,5,3,2,8,6,4,2,13,5,3,4,6,4,7,3,4,9,6,1,5,7,6,2,5,4,3,11]]}},"license":"","lines_per_credit":1,"lines_step_numbers_quick":[1,5,7,9,15],"paytable_wilds_old":{"13":{"exclude":{"13":{"count":1},"1":{"count":1}},"multi":0,"expanding":0,"factor":"2"}},"line_min_bet":1,"paytable_old":{"10":{"5":"400","3":"20","4":"100"},"6":{"3":"10","4":"50","5":"125"},"5":{"5":"100","3":"5","4":"25"},"13":{"4":"1500","3":"250","2":"10","5":"10000"},"11":{"2":"2","5":"750","3":"25","4":"125"},"8":{"5":"250","3":"15","4":"75"},"4":{"5":"100","3":"5","4":"25"},"3":{"5":"100","3":"5","4":"25"},"9":{"4":"75","3":"15","5":"250"},"12":{"2":"2","5":"750","4":"125","3":"25"},"2":{"5":"100","2":"2","4":"25","3":"5"},"7":{"5":"125","3":"10","4":"50"}}},"server_denom":{"cents":100,"value":"1.00","label":"1.00","id":"1.00"},"game_denom":"1.00","enabled_denoms":{"cents":100,"value":"1.00","label":"1.00","id":"1.00"},"resume_subgame_index":9}}]';
                                break;
                            case 'poll':
                                $gameBets = $slotSettings->Bet;
                                if( $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame') < $slotSettings->GetGameData('ForestNymphCTFreeGames') && $slotSettings->GetGameData('ForestNymphCTFreeGames') > 0 ) 
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                }
                                else
                                {
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance() - $slotSettings->GetGameData('ForestNymphCTTotalWin')) * 100;
                                }
                                $responseData['poll'][0]['account_status']['credit'] = (string)round($balanceInCents);
                                $responseData['poll'][0]['account_status']['updated_credit'] = (string)round($balanceInCents);
                                $result_tmp[] = json_encode($responseData['poll']);
                                break;
                            case 'game_finished':
                                $slotSettings->SetGameData('ForestNymphCTBonusWin', 0);
                                $slotSettings->SetGameData('ForestNymphCTTotalWin', 0);
                                $responseData['game_finished'][0]['freeround_play'] = $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0');
                                $responseData['game_finished'][0]['account_status']['credit'] = round($balanceInCents);
                                $responseData['game_finished'][0]['account_status']['rgs_balance'] = round($balanceInCents);
                                $result_tmp[] = json_encode($responseData['game_finished']);
                                break;
                            case 'subgame_finished':
                                if( $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0') < $slotSettings->GetGameData('ForestNymphCTFreeGames') && $slotSettings->GetGameData('ForestNymphCTFreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame0', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame0') + 1);
                                    $result = $slotSettings->GetGameData($slotSettings->slotId . 'Results');
                                    $lines = $slotSettings->GetGameData($slotSettings->slotId . 'Lines');
                                    $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                    $denom = $slotSettings->GetGameData($slotSettings->slotId . 'Denom');
                                    $cr = (array)$result[$slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame0')];
                                    $totalWin = $cr['total_win'] * $denom;
                                    $reportWin = $cr['win'] * $denom;
                                    $totalWin = sprintf('%01.2f', $totalWin);
                                    $reportWin = sprintf('%01.2f', $reportWin);
                                    $response = '{"responseEvent":"spin","responseType":"freespin","serverResponse":{"result":' . json_encode($result) . ',"slotDenom":' . $slotSettings->GetGameData($slotSettings->slotId . 'Denom') . ',"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('ForestNymphCTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0') . ',"Balance":' . $slotSettings->GetGameData('ForestNymphCTFreeBalance') . ',"afterBalance":' . $slotSettings->GetGameData('ForestNymphCTFreeBalance') . ',"bonusWin":' . $slotSettings->GetGameData('ForestNymphCTBonusWin') . ',"totalWin":' . $reportWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":' . json_encode($slotSettings->GetGameData($slotSettings->slotId . 'Reels')) . '}}';
                                    if( $slotSettings->GetGameData('ForestNymphCTFreeGames') <= $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0') ) 
                                    {
                                        if( $reportWin > 0 ) 
                                        {
                                            $slotSettings->SetBalance($reportWin);
                                        }
                                        $slotSettings->SaveLogReport($response, 0, $lines, $reportWin, 'freespin');
                                    }
                                    else
                                    {
                                        if( $reportWin > 0 ) 
                                        {
                                            $slotSettings->SetBalance($reportWin);
                                        }
                                        $slotSettings->SaveLogReport($response, 0, $lines, $reportWin, 'freespin');
                                    }
                                    $balanceInCents = $slotSettings->GetGameData('ForestNymphCTFreeBalance');
                                    $responseData['subgame_finished'][0]['account_status']['credit'] = round($balanceInCents);
                                    $responseData['subgame_finished'][0]['freeround_play'] = $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0');
                                    $responseData['subgame_finished'][1]['freeround_play'] = $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0');
                                    $responseData['subgame_finished'][0]['account_status']['rgs_balance'] = round($balanceInCents);
                                    $responseData['subgame_finished'][1]['account_status']['credit'] = round($balanceInCents);
                                    $responseData['subgame_finished'][1]['account_status']['rgs_balance'] = round($balanceInCents);
                                }
                                else
                                {
                                    $responseData['subgame_finished'][0]['account_status']['credit'] = round($balanceInCents);
                                    $responseData['subgame_finished'][0]['account_status']['rgs_balance'] = round($balanceInCents);
                                    $responseData['subgame_finished'][1]['account_status']['credit'] = round($balanceInCents);
                                    $responseData['subgame_finished'][1]['account_status']['rgs_balance'] = round($balanceInCents);
                                }
                                $result_tmp[] = json_encode($responseData['subgame_finished']);
                                break;
                            case 'play':
                                if( !isset($postData['lines']) || !isset($postData['line_bet']) || !isset($postData['game_denom']) ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                $denom = $postData['game_denom'];
                                $lines = $postData['lines'];
                                $betline = sprintf('%01.2f', $postData['line_bet'] * $denom);
                                $allbet = $betline * $lines;
                                $postData['slotEvent'] = 'bet';
                                if( $lines <= 0 || $betline <= 0.0001 || $denom <= 0.0001 || $denom > 1 || $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                $spinLoopLimit = 1;
                                for( $spinLoop = 0; $spinLoop < $spinLoopLimit; $spinLoop++ ) 
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
                                        3, 
                                        3
                                    ];
                                    $linesId[6] = [
                                        3, 
                                        3, 
                                        3, 
                                        1, 
                                        1
                                    ];
                                    $linesId[7] = [
                                        2, 
                                        1, 
                                        2, 
                                        3, 
                                        2
                                    ];
                                    $linesId[8] = [
                                        2, 
                                        3, 
                                        2, 
                                        1, 
                                        2
                                    ];
                                    $linesId[9] = [
                                        1, 
                                        2, 
                                        1, 
                                        2, 
                                        1
                                    ];
                                    $linesId[10] = [
                                        3, 
                                        2, 
                                        3, 
                                        2, 
                                        3
                                    ];
                                    $linesId[11] = [
                                        2, 
                                        2, 
                                        1, 
                                        1, 
                                        1
                                    ];
                                    $linesId[12] = [
                                        2, 
                                        2, 
                                        3, 
                                        3, 
                                        3
                                    ];
                                    $linesId[13] = [
                                        1, 
                                        1, 
                                        2, 
                                        1, 
                                        1
                                    ];
                                    $linesId[14] = [
                                        3, 
                                        3, 
                                        2, 
                                        3, 
                                        3
                                    ];
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
                                        $slotSettings->SetGameData('ForestNymphCTBonusWin', 0);
                                        $slotSettings->SetGameData('ForestNymphCTFreeGames', 0);
                                        $slotSettings->SetGameData('ForestNymphCTCurrentFreeGame', 0);
                                        $slotSettings->SetGameData('ForestNymphCTCurrentFreeGame0', 0);
                                        $slotSettings->SetGameData('ForestNymphCTTotalWin', 0);
                                        $slotSettings->SetGameData('ForestNymphCTFreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Denom', $denom);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Lines', $lines);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                        $bonusMpl = 1;
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('ForestNymphCTCurrentFreeGame', $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame') + 1);
                                        $bonusMpl = $slotSettings->slotFreeMpl;
                                    }
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                    $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                                    $winType = $winTypeTmp[0];
                                    $spinWinLimit = $winTypeTmp[1];
                                    if( $postData['slotEvent'] == 'freespin' && $winType == 'bonus' ) 
                                    {
                                        $winType = 'none';
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
                                            0
                                        ];
                                        $wild = ['13'];
                                        $scatter = '1';
                                        $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
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
                                                            $fc = $slotSettings->Paytable['SYM_' . $csym][2];
                                                            $tmpStringWin = (object)[
                                                                'factor' => $fc, 
                                                                'msgs' => null, 
                                                                'wilds' => [], 
                                                                'symbol_count' => '2', 
                                                                'positions' => [
                                                                    0, 
                                                                    1
                                                                ], 
                                                                'symbols' => [
                                                                    '' . $csym, 
                                                                    '' . $csym
                                                                ], 
                                                                'paytable_factor' => $fc, 
                                                                'win' => sprintf('%01.2f', sprintf('%01.2f', $tmpWin / $denom)), 
                                                                'symbol' => '' . $csym, 
                                                                'subgame_win_factor' => 1, 
                                                                'line_number' => $k
                                                            ];
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
                                                            $fc = $slotSettings->Paytable['SYM_' . $csym][3];
                                                            $tmpStringWin = (object)[
                                                                'factor' => $fc, 
                                                                'msgs' => null, 
                                                                'wilds' => [], 
                                                                'symbol_count' => '3', 
                                                                'positions' => [
                                                                    0, 
                                                                    1, 
                                                                    2
                                                                ], 
                                                                'symbols' => [
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym
                                                                ], 
                                                                'paytable_factor' => $fc, 
                                                                'win' => sprintf('%01.2f', sprintf('%01.2f', $tmpWin / $denom)), 
                                                                'symbol' => '' . $csym, 
                                                                'subgame_win_factor' => 1, 
                                                                'line_number' => $k
                                                            ];
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
                                                            $fc = $slotSettings->Paytable['SYM_' . $csym][4];
                                                            $tmpStringWin = (object)[
                                                                'factor' => $fc, 
                                                                'msgs' => null, 
                                                                'wilds' => [], 
                                                                'symbol_count' => '4', 
                                                                'positions' => [
                                                                    0, 
                                                                    1, 
                                                                    2, 
                                                                    3
                                                                ], 
                                                                'symbols' => [
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym
                                                                ], 
                                                                'paytable_factor' => $fc, 
                                                                'win' => sprintf('%01.2f', sprintf('%01.2f', $tmpWin / $denom)), 
                                                                'symbol' => '' . $csym, 
                                                                'subgame_win_factor' => 1, 
                                                                'line_number' => $k
                                                            ];
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
                                                            $fc = $slotSettings->Paytable['SYM_' . $csym][5];
                                                            $cWins[$k] = $tmpWin;
                                                            $tmpStringWin = (object)[
                                                                'factor' => $fc, 
                                                                'msgs' => null, 
                                                                'wilds' => [], 
                                                                'symbol_count' => '5', 
                                                                'positions' => [
                                                                    0, 
                                                                    1, 
                                                                    2, 
                                                                    3, 
                                                                    4
                                                                ], 
                                                                'symbols' => [
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym, 
                                                                    '' . $csym
                                                                ], 
                                                                'paytable_factor' => $fc, 
                                                                'win' => sprintf('%01.2f', sprintf('%01.2f', $tmpWin / $denom)), 
                                                                'symbol' => '' . $csym, 
                                                                'subgame_win_factor' => 1, 
                                                                'line_number' => $k
                                                            ];
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
                                            for( $p = 0; $p <= 2; $p++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == $scatter ) 
                                                {
                                                    $scattersCount++;
                                                    $scPos[] = [
                                                        $p, 
                                                        $r - 1
                                                    ];
                                                }
                                            }
                                        }
                                        $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet;
                                        if( $scattersCount >= 2 ) 
                                        {
                                            if( $scattersCount >= 3 ) 
                                            {
                                                $scattersStr = [
                                                    [
                                                        'positions' => $scPos, 
                                                        'increase_factor_bonus' => [], 
                                                        'bonus_type' => 'free_games', 
                                                        'wilds' => null, 
                                                        'wild_exists' => null, 
                                                        'bonus_value' => '' . $slotSettings->slotFreeCount, 
                                                        'msgs' => null, 
                                                        'factor' => '' . $slotSettings->Paytable['SYM_' . $scatter][$scattersCount], 
                                                        'symbols_positions' => [
                                                            1 => ['positions' => $scPos]
                                                        ], 
                                                        'symbol' => '1', 
                                                        'subgame_win_factor' => 1, 
                                                        'count' => '' . $scattersCount, 
                                                        'win' => '' . ($scattersWin / $denom), 
                                                        'paytable_factor' => $slotSettings->Paytable['SYM_' . $scatter][$scattersCount]
                                                    ]
                                                ];
                                            }
                                            else
                                            {
                                                $scattersStr = [
                                                    [
                                                        'symbol' => '1', 
                                                        'bonus_value' => 0, 
                                                        'msgs' => null, 
                                                        'paytable_factor' => 2, 
                                                        'subgame_win_factor' => 1, 
                                                        'count' => '2', 
                                                        'positions' => $scPos, 
                                                        'win' => '' . ($scattersWin / $denom), 
                                                        'increase_factor_bonus' => [], 
                                                        'wild_exists' => null, 
                                                        'factor' => '2', 
                                                        'wilds' => null, 
                                                        'symbols_positions' => [
                                                            1 => ['positions' => $scPos]
                                                        ]
                                                    ]
                                                ];
                                            }
                                        }
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
                                    $totalWin = sprintf('%01.2f', $totalWin);
                                    if( $totalWin > 0 ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        if( $postData['slotEvent'] != 'freespin' ) 
                                        {
                                            $slotSettings->SetBalance($totalWin);
                                        }
                                        if( $postData['slotEvent'] != 'freespin' ) 
                                        {
                                            $responseData['play'][0]['result'][1] = (object)[
                                                'continue_params' => [
                                                    [
                                                        'label' => 'double_betpart', 
                                                        'values' => [
                                                            'skip', 
                                                            'half', 
                                                            'all'
                                                        ]
                                                    ], 
                                                    [
                                                        'label' => 'double_key', 
                                                        'values' => [
                                                            'red', 
                                                            'black', 
                                                            'diamond', 
                                                            'heart', 
                                                            'spade', 
                                                            'clubs'
                                                        ]
                                                    ]
                                                ], 
                                                'type' => 'can_play_red_black_double', 
                                                'wait_for_continue' => true, 
                                                'type_descr' => 'SELECT DOUBLE', 
                                                'start_subgame_timestamp' => null, 
                                                'step_number' => 1, 
                                                'double_cards_history' => [], 
                                                'end_subgame_timestamp' => null
                                            ];
                                        }
                                    }
                                    $reportWin = $totalWin;
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $slotSettings->SetGameData('ForestNymphCTBonusWin', $slotSettings->GetGameData('ForestNymphCTBonusWin') + $totalWin);
                                        $slotSettings->SetGameData('ForestNymphCTTotalWin', $slotSettings->GetGameData('ForestNymphCTTotalWin') + $totalWin);
                                        $balanceInCents = $slotSettings->GetGameData('ForestNymphCTFreeBalance');
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('ForestNymphCTTotalWin', $totalWin);
                                    }
                                    $fs = 0;
                                    if( $scattersCount >= 2 ) 
                                    {
                                        if( $scattersCount >= 3 ) 
                                        {
                                            if( $slotSettings->GetGameData('ForestNymphCTFreeGames') > 0 ) 
                                            {
                                                $slotSettings->SetGameData('ForestNymphCTFreeGames', $slotSettings->GetGameData('ForestNymphCTFreeGames') + $slotSettings->slotFreeCount);
                                            }
                                            else
                                            {
                                                $slotSettings->SetGameData('ForestNymphCTFreeStartWin', $totalWin);
                                                $slotSettings->SetGameData('ForestNymphCTBonusWin', $totalWin);
                                                $slotSettings->SetGameData('ForestNymphCTFreeGames', $slotSettings->slotFreeCount);
                                            }
                                            $fs = $slotSettings->GetGameData('ForestNymphCTFreeGames');
                                            $responseData['play'][0]['result'][0] = (array)$responseData['free_result'];
                                            $responseData['play'][0]['result'][0]['scatters_win'] = $scattersStr;
                                            $responseData['play'][0]['result'][$spinLoop]['free_game_total'] = 0;
                                            $responseData['play'][0]['result'][$spinLoop]['subgame_label_game_number'] = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                            $responseData['play'][0]['result'][$spinLoop]['free_games'] = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                            $responseData['play'][0]['result'][$spinLoop]['is_last'] = false;
                                            $responseData['play'][0]['result'][$spinLoop]['is_free'] = true;
                                        }
                                        else
                                        {
                                            $responseData['play'][0]['result'][0]['scatters_win'] = $scattersStr;
                                        }
                                    }
                                    $winString = json_encode($lineWins);
                                    $jsSpin = '' . json_encode($reels) . '';
                                    $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'Reels', $reels);
                                    $winstring = '';
                                    $slotSettings->SetGameData('ForestNymphCTGambleStep', 5);
                                    $hist = $slotSettings->GetGameData('ForestNymphCTCards');
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $state = 'freespin';
                                        $responseData['play'][0]['result'][$spinLoop] = $responseData['free_spin_result'];
                                        $responseData['play'][0]['result'][$spinLoop]['type_descr'] = 'FREE GAME';
                                        $responseData['play'][0]['result'][$spinLoop]['free_game_total'] = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                        $responseData['play'][0]['result'][$spinLoop]['subgame_label_game_number'] = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                        $responseData['play'][0]['result'][$spinLoop]['free_game_number'] = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                        $responseData['play'][0]['result'][$spinLoop]['free_games'] = 0;
                                        $responseData['play'][0]['result'][$spinLoop]['is_free'] = true;
                                        $responseData['play'][0]['result'][$spinLoop]['is_last'] = false;
                                        $responseData['play'][0]['result'][$spinLoop]['subgame_label'] = 'free';
                                        if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData('ForestNymphCTBonusWin') > 0 ) 
                                        {
                                            $responseData['play'][0]['result'][$spinLoop]['is_free'] = true;
                                            $responseData['play'][0]['result'][$spinLoop]['is_last'] = true;
                                            $postData['slotEvent'] = 'bet';
                                            $reportWin = 0;
                                        }
                                    }
                                    $responseData['play'][0]['account_status']['credit'] = round($balanceInCents);
                                    $responseData['play'][0]['account_status']['rgs_balance'] = round($balanceInCents);
                                    $responseData['play'][0]['result'][$spinLoop]['stop_reels'][0] = $reels['reel1'];
                                    $responseData['play'][0]['result'][$spinLoop]['stop_reels'][1] = $reels['reel2'];
                                    $responseData['play'][0]['result'][$spinLoop]['stop_reels'][2] = $reels['reel3'];
                                    $responseData['play'][0]['result'][$spinLoop]['stop_reels'][3] = $reels['reel4'];
                                    $responseData['play'][0]['result'][$spinLoop]['stop_reels'][4] = $reels['reel5'];
                                    $responseData['play'][0]['result'][$spinLoop]['result_reels'][0] = [
                                        $reels['reel1'][0], 
                                        $reels['reel2'][0], 
                                        $reels['reel3'][0], 
                                        $reels['reel4'][0], 
                                        $reels['reel5'][0]
                                    ];
                                    $responseData['play'][0]['result'][$spinLoop]['result_reels'][1] = [
                                        $reels['reel1'][1], 
                                        $reels['reel2'][1], 
                                        $reels['reel3'][1], 
                                        $reels['reel4'][1], 
                                        $reels['reel5'][1]
                                    ];
                                    $responseData['play'][0]['result'][$spinLoop]['result_reels'][2] = [
                                        $reels['reel1'][2], 
                                        $reels['reel2'][2], 
                                        $reels['reel3'][2], 
                                        $reels['reel4'][2], 
                                        $reels['reel5'][2]
                                    ];
                                    $responseData['play'][0]['result'][$spinLoop]['request_params']['line_bet'] = $betline;
                                    $responseData['play'][0]['result'][$spinLoop]['request_params']['game_denom'] = 1;
                                    $responseData['play'][0]['result'][$spinLoop]['request_params']['lines'] = $lines;
                                    $responseData['play'][0]['result'][$spinLoop]['request_params']['total_bet'] = $allbet;
                                    $responseData['play'][0]['result'][$spinLoop]['lines_win'] = $lineWins;
                                    $responseData['play'][0]['result'][$spinLoop]['winType '] = $winType;
                                    $responseData['play'][0]['result'][$spinLoop]['win'] = sprintf('%01.2f', $totalWin / $denom);
                                    $responseData['play'][0]['result'][$spinLoop]['total_win'] = sprintf('%01.2f', $slotSettings->GetGameData('ForestNymphCTTotalWin') / $denom);
                                    $responseData['play'][0]['result'][$spinLoop]['total_win'] = sprintf('%01.2f', $responseData['play'][0]['result'][$spinLoop]['total_win']);
                                    if( $spinLoop == 0 && $winType == 'bonus' ) 
                                    {
                                        $postData['slotEvent'] = 'freespin';
                                        $spinLoopLimit = $slotSettings->slotFreeCount + 1;
                                    }
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'Results', $responseData['play'][0]['result']);
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"result":' . json_encode($responseData['play'][0]['result']) . ',"slotDenom":' . $denom . ',"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('ForestNymphCTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('ForestNymphCTCurrentFreeGame0') . ',"Balance":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance') . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('ForestNymphCTBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $result_tmp[] = json_encode($responseData['play']);
                                break;
                            case 'gamble':
                                $Balance = $slotSettings->GetBalance();
                                $denom = $slotSettings->GetGameData($slotSettings->slotId . 'Denom');
                                if( $postData['continue_params']['double_key'] != 'red' && $postData['continue_params']['double_key'] != 'black' ) 
                                {
                                    $isGambleWin = rand(1, $slotSettings->GetGambleSettings() * 2);
                                }
                                else
                                {
                                    $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                }
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData('ForestNymphCTTotalWin');
                                $gambleWin = 0;
                                $statBet = $totalWin;
                                if( $postData['continue_params']['double_betpart'] == 'half' ) 
                                {
                                    $totalWin = sprintf('%01.2f', $totalWin / 2);
                                    $statBet = $totalWin;
                                }
                                $hist = $slotSettings->GetGameData('ForestNymphCTCards');
                                $slotSettings->SetGameData('ForestNymphCTGambleStep', $slotSettings->GetGameData('ForestNymphCTGambleStep') - 1);
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $isGambleWin == 1 ) 
                                {
                                    $gambleState = 'gamble';
                                    $responseData['gamble'][0]['result'][0]['double_result'] = 'win';
                                    if( $postData['continue_params']['double_key'] != 'red' && $postData['continue_params']['double_key'] != 'black' ) 
                                    {
                                        $gambleWin = $totalWin * 2;
                                        $totalWin = $totalWin * 4;
                                        if( $postData['continue_params']['double_key'] == 'diamond' ) 
                                        {
                                            $dealerCard = rand(0, 12);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'heart' ) 
                                        {
                                            $dealerCard = rand(13, 25);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'spade' ) 
                                        {
                                            $dealerCard = rand(26, 38);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'clubs' ) 
                                        {
                                            $dealerCard = rand(39, 51);
                                        }
                                    }
                                    else
                                    {
                                        $gambleWin = $totalWin;
                                        $totalWin = $totalWin * 2;
                                        if( $postData['continue_params']['double_key'] == 'red' ) 
                                        {
                                            $dealerCard = rand(0, 25);
                                        }
                                        else
                                        {
                                            $dealerCard = rand(26, 51);
                                        }
                                    }
                                    array_pop($hist);
                                    array_unshift($hist, $dealerCard);
                                    $responseData['gamble'][0]['result'][1] = (object)[
                                        'type' => 'can_play_red_black_double', 
                                        'continue_params' => [
                                            [
                                                'values' => [
                                                    'skip', 
                                                    'half', 
                                                    'all'
                                                ], 
                                                'label' => 'double_betpart'
                                            ], 
                                            [
                                                'values' => [
                                                    'red', 
                                                    'black', 
                                                    'diamond', 
                                                    'heart', 
                                                    'spade', 
                                                    'clubs'
                                                ], 
                                                'label' => 'double_key'
                                            ]
                                        ], 
                                        'double_cards_history' => $hist, 
                                        'start_subgame_timestamp' => null, 
                                        'step_number' => 3, 
                                        'end_subgame_timestamp' => null, 
                                        'wait_for_continue' => true, 
                                        'type_descr' => 'SELECT DOUBLE'
                                    ];
                                }
                                else
                                {
                                    $gambleState = 'idle';
                                    $responseData['gamble'][0]['result'][0]['double_result'] = 'lost';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    $slotSettings->SetGameData('ForestNymphCTGambleStep', 0);
                                    if( $postData['continue_params']['double_key'] != 'red' && $postData['continue_params']['double_key'] != 'black' ) 
                                    {
                                        if( $postData['continue_params']['double_key'] == 'diamond' ) 
                                        {
                                            $dealerCard = rand(13, 51);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'heart' ) 
                                        {
                                            $dealerCard = rand(26, 51);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'spade' ) 
                                        {
                                            $dealerCard = rand(0, 26);
                                        }
                                        if( $postData['continue_params']['double_key'] == 'clubs' ) 
                                        {
                                            $dealerCard = rand(0, 38);
                                        }
                                    }
                                    else if( $postData['continue_params']['double_key'] == 'red' ) 
                                    {
                                        $dealerCard = rand(26, 51);
                                    }
                                    else
                                    {
                                        $dealerCard = rand(0, 25);
                                    }
                                    array_pop($hist);
                                    array_unshift($hist, $dealerCard);
                                }
                                $responseData['gamble'][0]['result'][0]['request_params']['double_key'] = $postData['continue_params']['double_key'];
                                $responseData['gamble'][0]['result'][0]['double_win'] = sprintf('%01.2f', $totalWin / $denom);
                                $responseData['gamble'][0]['result'][0]['win'] = sprintf('%01.2f', $totalWin / $denom);
                                $responseData['gamble'][0]['result'][0]['double_cards_history'] = $hist;
                                $slotSettings->SetGameData('ForestNymphCTTotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, 'slotGamble');
                                $slotSettings->SetGameData('ForestNymphCTCards', $hist);
                                $result_tmp[] = json_encode($responseData['gamble']);
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
