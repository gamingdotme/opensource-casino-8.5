<?php 
namespace VanguardLTE\Games\GoodLuck40WD
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
                        $result_tmp = [];
                        $reqId = $postData['id'];
                        if( !isset($postData['act']) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"incorrect action"}';
                            exit( $response );
                        }
                        if( !isset($postData['cid']) ) 
                        {
                            $reqCid = 'NxwFWiKl';
                        }
                        else
                        {
                            $reqCid = $postData['cid'];
                        }
                        if( $postData['act'] == '464529TBNS' && $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid gamble state"}';
                            exit( $response );
                        }
                        if( $postData['act'] == 'WXMNPP7H8F' ) 
                        {
                            if( $postData['arg']['stake'] / 100 <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($postData['arg']['stake'] / 100) && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                        }
                        switch( $postData['act'] ) 
                        {
                            case 'setup':
                                $result_tmp[] = '{"a": "ogEFvreY", "cid": "B1L81aKc", "id": ' . $reqId . ', "res": {"pv": 3, "ver": 4}, "s": "ok"}';
                                break;
                            case 'nCGuJ1R7Iw98aD7o':
                                $result_tmp[] = '{"a": "DFeG9RU6", "cid": "BKYzNZNh", "id": ' . $reqId . ', "res": {"aa": "no", "accepted_regulations": "1", "account_id": "4486018", "cbe": "no", "ct": "C", "fb_id": "", "flags": "m[AhT]", "gA": "yes", "hk": "450649", "id": "4B05B2ADB05847C875C19787921A36663AFE8B68", "jackpot_type": "0", "mbos": "no", "payin_limit_reached": "0", "sa": "no", "session_id": "6324339", "stamp": "1d8c0899edfd4adbb803499d72bc21feba42f2fba7be46543cdff772220e10e904948583e26da5a550b4de4df9c05905049b7cf31e215776c14a0cfcb34395650c15ec0a6a9fb50a68ad8751185d4bbad429669debd4c9c7eab07606b1eac650033acd6ed546b391d45899e366a37a9fc1d9f479f8c10b70fa541814a492d3ec", "sv": "43.87", "token": "", "user_id": "4486018", "version": "Wazdan Web Server 3.2 GWT [JPS] git version: f2b6cabff859c316ce7436d94b480fbde678dcdd S: 43.87 T: C Aa:0 As:0 CTRL: SSL F: [IRS]"}, "s": "ok"}';
                                break;
                            case 'TZE8GXY14X':
                                $result_tmp[] = '{"a": "j8Z5KXX1", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"currency": "' . $slotSettings->slotCurrency . '", "currency_display_as": "' . $slotSettings->slotCurrency . '", "currency_format_separ_type": "0", "currency_format_symbol_placement": "1", "point_ratio": "0.01", "screenShotMaxSize": "", "screenShotMinPrize": "", "screenShotType": "", "screenShotUrl": "", "screen_orientation_type": "", "show_currency": "C"}, "s": "ok"}';
                                break;
                            case 'F18RRVF55I':
                                $result_tmp[] = '{"a": "KQDqJMsl", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"autoGameType": "1", "autoPrizeAfter": "120", "capi": "no", "cardEnable": "yes", "connectLoginEnable": "no", "couponHasBank": "no", "creditOnly": "yes", "demoMode": "no", "gA": "yes", "gamblingMaxLevel": "4", "gameConfiguration": "1", "gaming_allowed": "yes", "highScorePeriod": "0", "highScoreValue": "0", "highScoreValuesMaxCount": "0", "highStake": "100", "highStakeBankMode": "0", "higherStake": "50000", "histJackpotCount": "4", "histJackpotRange": "60", "histJackpotTime": "5", "histPrizeCount": "4", "histPrizeRange": "60", "histPrizeStart": "10000", "histPrizeTime": "5", "immediateHighScoreValue": "0", "itGameCredits": "0", "itMaxEnergy": "0", "jackpotDisplayStep": "1000", "jackpotIsBonus": "no", "jackpotName": "0", "jackpotPercent": "10", "maxBank": "0", "maxCouponValue": "0", "maxCredit": "100000", "maxCreditPayin": "10000", "maxCreditStake": "10000", "maxCreditToBlock": "100000", "maxGamblePrize": "0", "maxHopperPayout": "0", "maxNotes": "0", "maxPrize": "0", "maxPrizeToCredit": "no", "maxRiskGames": "0", "maxRiskGamesStake": "0", "maxStake": "10000", "maxTransfer": "0", "minCouponCreateValue": "0", "minCouponUseToPayout": "0", "minGamesToPayout": "20", "minStake": "10", "moneyInsertLimit": "10000", "newCouponEnable": "no", "newCouponEnableOnKiosk": "no", "noCouponPass": "no", "notesType": "0", "oneCouponInGroup": "no", "payoutFromBank": "no", "plusSignsEnabled": "0", "prizeTo": "-1", "quizEnabled": "no", "redemptionEnable": "no", "riskBankInterval": "0", "riskDispersion": "1", "riskEnabled": "yes", "roundSuperGames": "yes", "sasMetersEnabled": "no", "semiRoundSuperGames": "no", "showRecord": "no", "showTrademark": "1", "singleCouponEnable": "no", "skillGames": "0", "skillWheelGames": "no", "specVersion": "0", "stakeFromCreditOnly": "no", "superGameStake": "0", "superGamesAfterLostOnly": "no", "superGamesEnterType": "0", "superGamesFreq": "0", "superGamesMaxPrize": "0", "superGamesProb": "80", "superGamesRandom": "no", "superGamesRandomX2": "no", "superOnJPCounter": "1", "sweepEnable": "no", "sweepEnable2": "no", "transferB2K": "0", "transferB2KMode": "0", "transferEnable": "yes", "veryHighScoreValue": "0", "vltLogo": "yes", "voucherEnable": "no", "wbContinues": "no", "winBank2Credit": "yes", "winProfile": "82%", "winProfileSelect": "1"}, "s": "ok"}';
                                break;
                            case 'P2AB7RC9UU':
                                $result_tmp[] = '{"a": "23cFxJWS", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": ["<config countdown_time=\"15\" currency_factor=\"1\" currency_type=\"KC\" date_format_type=\"1\" demo=\"0\" demo_anims_after=\"17\" disconnected_text_jp=\"Jackpot disconnected\" disconnected_text_jp_show=\"1\" disconnected_text_scroll_game=\"Jackpot disconnected\" disconnected_text_scroll_game_delay=\"5\" disconnected_text_scroll_game_show=\"0\" disconnected_text_scroll_main=\"Jackpot disconnected\" disconnected_text_scroll_main_delay=\"5\" disconnected_text_scroll_main_show=\"1\" effect_delay=\"60\" scroll_text_1=\"\" scroll_text_2=\"\" scroll_text_3=\"\" scroll_text_count=\"5\" scroll_text_delay=\"30\" scroll_text_game_1=\"\" scroll_text_game_2=\"\" scroll_text_game_3=\"\" scroll_text_game_count=\"2\" scroll_text_game_delay=\"10\" scroll_text_game_show=\"1\" scroll_text_show=\"1\" scroll_type=\"1\" show_alter_text_after=\"35\" stake_share=\"0\" /><sub_config currency=\"CP.\" point_ratio=\"1\" show_currency=\"C\" type=\"real\" />"], "s": "ok"}';
                                break;
                            case 'GNNNVGVTKP':
                                $result_tmp[] = '{"a": "pfosYn2N", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"mode": "autorefill"}, "s": "ok"}';
                                break;
                            case 'QE8J5MIKCA':
                                $result_tmp[] = '{"a": "KkfWlJRA", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"difficulty! level! mode": "1", "max.! lines": "2", "stake! per! line": "1"}, "s": "ok"}';
                                break;
                            case 'DVVRWT9K7D':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', $lastEvent->serverResponse->BonusSymbol);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                }
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 10 * 100;
                                }
                                $result_tmp[] = '{"a": "sxYumxhx", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"bank": "0", "coins": "' . $gameBets[0] . '", "credit": "10000", "difficulty_level_mode": "0", "energy": "1", "flags": "0", "free_rounds": "0", "iv": "3", "lines": "40", "maxstake": "' . ($gameBets[count($gameBets) - 1] * 1) . '", "minstake": "1", "notes": "0", "prize": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "protocol_version": "1", "risk": "1", "skill_games": "0", "skill_record": "0", "stake": "' . $gameBets[0] . '", "stake_list": "' . implode(',', $gameBets) . '", "stake_per_line": "1", "time": "0"}, "s": "ok"}';
                                break;
                            case '1KI735SFY5':
                                $result_tmp[] = '{"a": "bTjXKLkS", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"bank": "0", "credit": "' . round($slotSettings->GetBalance() * 100) . '", "notes": "0", "time": "0"}, "s": "ok"}';
                                break;
                            case 'D3425KPHW1':
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', $lastEvent->serverResponse->BonusSymbol);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                    $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                    $rp2 = '' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[0] . '';
                                    $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[1] . '');
                                    $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[2] . '');
                                    $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[3] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[3] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[3] . ',' . $lastEvent->serverResponse->reelsSymbols->reel4[3] . ',' . $lastEvent->serverResponse->reelsSymbols->reel5[3] . '');
                                    $bet = $lastEvent->serverResponse->slotBet * 100 * 10;
                                }
                                else
                                {
                                    $rp1 = implode(',', [
                                        rand(0, count($slotSettings->reelStrip1) - 3), 
                                        rand(0, count($slotSettings->reelStrip2) - 3), 
                                        rand(0, count($slotSettings->reelStrip3) - 3)
                                    ]);
                                    $rp_1 = rand(0, count($slotSettings->reelStrip1) - 4);
                                    $rp_2 = rand(0, count($slotSettings->reelStrip2) - 4);
                                    $rp_3 = rand(0, count($slotSettings->reelStrip3) - 4);
                                    $rp_4 = rand(0, count($slotSettings->reelStrip4) - 4);
                                    $rp_5 = rand(0, count($slotSettings->reelStrip5) - 4);
                                    $rr1 = $slotSettings->reelStrip1[$rp_1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3];
                                    $rr4 = $slotSettings->reelStrip4[$rp_4];
                                    $rr5 = $slotSettings->reelStrip5[$rp_5];
                                    $rp2 = '' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5;
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                    $rr4 = $slotSettings->reelStrip4[$rp_4 + 1];
                                    $rr5 = $slotSettings->reelStrip5[$rp_5 + 1];
                                    $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5);
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                    $rr4 = $slotSettings->reelStrip4[$rp_4 + 2];
                                    $rr5 = $slotSettings->reelStrip5[$rp_5 + 2];
                                    $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5);
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 3];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 3];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 3];
                                    $rr4 = $slotSettings->reelStrip4[$rp_4 + 3];
                                    $rr5 = $slotSettings->reelStrip5[$rp_5 + 3];
                                    $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . ',' . $rr4 . ',' . $rr5);
                                    $bet = $slotSettings->Bet[0] * 100 * 10;
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') == $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                }
                                $result_tmp[] = '{"a": "JvRfPGCY", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"bonus_count": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "bonus_difficulty_level": "0", "bonus_energy": "0", "bonus_nr": "' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . '", "bonus_stake": "' . $bet . '", "bonus_symbol": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') - 1) . '", "max_lines": "40", "stake_of_bonus": "0", "stake_per_line": "1", "symb": "' . $rp2 . '"}, "s": "ok"}';
                                break;
                            case 'PTUDQQMKZB':
                                $result_tmp[] = '{"a": "CRc5ya6I", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "s": "ok"}';
                                break;
                            case 'ping':
                                $result_tmp[] = '{"a": "KuaCagnK", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "s": "ok"}';
                                break;
                            case 'close':
                                exit();
                                break;
                            case 'pause':
                                $result_tmp[] = '{"a": "VfgEano2", "cid": "ewPLsKP1", "id": ' . $reqId . ', "s": "ok"}';
                                break;
                            case 'resume':
                                $result_tmp[] = '{"a": "QKDe0XPD", "cid": "' . $reqCid . '", "id":  ' . $reqId . ', "s": "ok"}';
                                break;
                            case 'continue':
                                $result_tmp[] = '{"a": "QKDe0XPD", "cid": "' . $reqCid . '", "id":  ' . $reqId . ', "s": "ok"}';
                                break;
                            case 'PI1G7D2TDR':
                                $result_tmp[] = '{"a": "dSAbZCRf", "cid": "' . $reqCid . '", "id":  ' . $reqId . ', "res": {"bank": "0", "credit": "' . round($slotSettings->GetBalance() * 100) . '", "notes": "0", "time": "0"}, "s": "ok"}';
                                break;
                            case 'PI1G7D2TDR':
                                $result_tmp[] = '{"a": "aU0MYEP3", "cid": "MNVKfRUv", "id": ' . $reqId . ', "res": {"pv": 3, "ver": 3}, "s": "ok"}';
                                break;
                            case 'WXMNPP7H8F':
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
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
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[5] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[6] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[7] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[8] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[9] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[10] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[11] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[12] = [
                                    2, 
                                    1, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[13] = [
                                    3, 
                                    4, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[14] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[15] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[16] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[17] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[18] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[19] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[21] = [
                                    3, 
                                    3, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[22] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[23] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[24] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[25] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[26] = [
                                    1, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[27] = [
                                    4, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[28] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    3
                                ];
                                $linesId[29] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    2
                                ];
                                $linesId[30] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[31] = [
                                    2, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[32] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[33] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[34] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[35] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[36] = [
                                    1, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[37] = [
                                    4, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[38] = [
                                    2, 
                                    3, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[39] = [
                                    3, 
                                    2, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $lines = 40;
                                $betLine = $postData['arg']['stake'] / 100;
                                $betLine = $betLine / 5;
                                $allbet = $betLine * 5;
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                                {
                                    $postData['slotEvent'] = 'freespin';
                                }
                                if( $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                    $bonusMpl = $slotSettings->slotFreeMpl;
                                }
                                $balance = sprintf('%01.2f', $slotSettings->GetBalance());
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $betLine, $lines);
                                $winType = $winTypeTmp[0];
                                $spinWinLimit = $winTypeTmp[1];
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
                                        0
                                    ];
                                    $wild = ['10'];
                                    $scatter = '11';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $reelsTmp = $reels;
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
                                                $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                                $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                                $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                                $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                                $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                                if( $s[0] == $csym || in_array($s[0], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][1] * $betLine * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '{"Count":1,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('GoodLuck40WDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                        $tmpStringWin = '{"Count":2,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('GoodLuck40WDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":["none","none"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                        $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('GoodLuck40WDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                                        $tmpStringWin = '{"Count":4,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('GoodLuck40WDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":["none","none"]}';
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
                                                        $tmpStringWin = '{"Count":5,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('GoodLuck40WDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":[' . ($linesId[$k][3] - 1) . ',"' . $s[3] . '"],"winReel5":[' . ($linesId[$k][4] - 1) . ',"' . $s[4] . '"]}';
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
                                    $scattersWinB = 0;
                                    $scattersStr = '{';
                                    $scattersCount = 0;
                                    $bSym = $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol');
                                    $bSymCnt = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                                $isScat = true;
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
                                    $totalWin += ($scattersWin + $scattersWinB);
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
                                        else
                                        {
                                            if( $i > 1500 ) 
                                            {
                                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"Bad Reel Strip"}';
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
                                $flag = 0;
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $flag = 6;
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
                                $gameState = '03';
                                if( $scattersCount >= 3 ) 
                                {
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', rand(1, 9));
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                    }
                                }
                                $reels = $reelsTmp;
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $winString = implode(',', $lineWins);
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"BonusSymbol":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') . ',"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel4'][0] . ',' . $reels['reel5'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel4'][1] . ',' . $reels['reel5'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2] . ',' . $reels['reel4'][2] . ',' . $reels['reel5'][2] . ',' . $reels['reel1'][3] . ',' . $reels['reel2'][3] . ',' . $reels['reel3'][3] . ',' . $reels['reel4'][3] . ',' . $reels['reel5'][3];
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                    $flag = 0;
                                    $balance = $slotSettings->GetBalance() - $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                    {
                                        $flag = 6;
                                    }
                                }
                                $result_tmp[] = '{"a": "MmWeiKrZ", "cid": "NxwFWiKl", "id": ' . $reqId . ', "res": {"bank": "0", "bonus_count": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "bonus_difficulty_level": "0", "bonus_energy": "0", "bonus_nr": "' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . '", "bonus_stake": "' . round($allbet * 100) . '", "bonus_symbol": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusSymbol') - 1) . '", "cprize": "0", "credit": "' . round($balance * 100) . '", "flags": "' . $flag . '", "jprize": "0", "prize": "' . round($totalWin * 100) . '", "symb": "' . $symb . '"}, "s": "ok"}';
                                break;
                            case '9MHKS5E2Z2':
                                $result_tmp[] = '{"a": "8PIOHLo2", "cid": "FCj5rHuI", "id": ' . $reqId . ', "res": {"bank": "0", "credit": "' . round($slotSettings->GetBalance() * 100) . '", "flags": "0"}, "s": "ok"}';
                                break;
                            case 'VKYHTCZVTN':
                                $winall = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                if( $winall > 0.01 ) 
                                {
                                    $winall22 = sprintf('%01.2f', $winall / 2);
                                }
                                else
                                {
                                    $winall22 = 0;
                                }
                                $winall = $winall - $winall22;
                                $user_balance = $slotSettings->GetBalance() - $winall;
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $winall);
                                $result_tmp[] = '{"a": "Eh3gR27z", "cid": "8FHZOAu4", "id": ' . $reqId . ', "res": {"bank": "0", "cprize": "0", "credit": "' . round($user_balance * 100) . '", "flags": "12", "prize": "' . round($winall22 * 100) . '", "transfered": "' . round($winall22 * 100) . '", "x": "0"}, "s": "ok"}';
                                break;
                            case 'KZHGWHDL9T':
                                $result_tmp[] = '{"a": "rWJBqTiq", "cid": "FCj5rHuI", "id": ' . $reqId . ', "s": "ok"}';
                                break;
                            case '464529TBNS':
                                $Balance = $slotSettings->GetBalance();
                                $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                                $gambleWin = 0;
                                $statBet = $totalWin;
                                $winState = 0;
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $isGambleWin == 1 ) 
                                {
                                    $gambleState = 'win';
                                    $gambleWin = $totalWin;
                                    $totalWin = $totalWin * 2;
                                    $winState = 1;
                                    $winFlag = '6';
                                }
                                else
                                {
                                    $gambleState = 'lose';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    $winState = 0;
                                    $winFlag = '0';
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $statBet + $gambleWin, 'slotGamble');
                                $result_tmp[] = '{"a": "JtxxRtC8", "cid": "FCj5rHuI", "id": ' . $reqId . ', "res": {"bank": "0", "cprize": "0", "credit": "' . round($afterBalance * 100) . '", "flags": "' . $winFlag . '", "prize": "' . round($totalWin * 100) . '", "win": "' . $winState . '", "x": "0"}, "s": "ok"}';
                                break;
                            case 'W42THTUT96':
                                $result_tmp[] = '{"a": "aTjH2yIT", "cid": "FCj5rHuI", "id": ' . $reqId . ', "res": {"flags": "0"}, "s": "ok"}';
                                break;
                        }
                        $response = implode('------', $result_tmp);
                        $slotSettings->SaveGameData();
                        $slotSettings->SaveGameDataStatic();
                        echo ':::' . $response;
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
