<?php 
namespace VanguardLTE\Games\VegasHotWD
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
                            $reqCid = '';
                        }
                        else
                        {
                            $reqCid = $postData['cid'];
                        }
                        if( $postData['act'] == '464529TBNS' && $slotSettings->GetGameData('VegasHotWDTotalWin') <= 0 ) 
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
                                $result_tmp[] = '{"a": "LW9XuzrO", "cid": "DRUXHWsV", "id": ' . $reqId . ', "res": {"pv": 3, "ver": 3}, "s": "ok"}';
                                break;
                            case 'nCGuJ1R7Iw98aD7o':
                                $result_tmp[] = '{"a": "4t88et2k", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"aa": "no", "accepted_regulations": "1", "account_id": "2550983", "cbe": "no", "ct": "C", "fb_id": "", "flags": "m[AhT]", "gA": "yes", "hk": "450570", "id": "B481B7993021583C66F38048930AC095BD4FFDA7", "level_changed": "0", "level_up_possible": "0", "mbos": "no", "payin_limit_reached": "0", "sa": "no", "session_id": "3472002", "stamp": "97d367519a7a50acbb124d70a1c03896bdb6fa3181c216da3730fd9782a8e2432228c42904bfe774e66046a287251b38c82d2519e2348226877d5d0b34ee704775a180d65b45c24bb0abacfe19d5f8f3960b9a5efcc65729f89d4175a7adbe1bb4d2637c6e8eb10f9c8c81f11a69009e825d9dab7ab75acd1fa21a883b5122d8", "sv": "43.87", "token": "", "user_id": "0000000", "version": ""}, "s": "ok"}';
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
                                $result_tmp[] = '{"a": "DRUXHWsV", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"manual! lines": "5", "manual! lines! enabled": "0", "max.! stake": "10000", "min.! stake": "1", "min.! treasure": "100", "stake! per! line": "1"}, "s": "ok"}';
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
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusSymbol', -1);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                                }
                                $gameBets = $slotSettings->Bet;
                                for( $i = 0; $i < count($gameBets); $i++ ) 
                                {
                                    $gameBets[$i] = $gameBets[$i] * 5 * 100;
                                }
                                $result_tmp[] = '{"a": "sxYumxhx", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"bank": "0", "coins": "1", "credit": "10000", "difficulty_level_mode": "0", "energy": "1", "flags": "0", "free_rounds": "0", "iv": "3", "lines": "5", "maxstake": "' . ($gameBets[count($gameBets) - 1] * 5) . '", "minstake": "1", "notes": "0", "prize": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "protocol_version": "1", "risk": "1", "skill_games": "0", "skill_record": "0", "stake": "' . $gameBets[0] . '", "stake_list": "' . implode(',', $gameBets) . '", "stake_per_line": "1", "time": "0"}, "s": "ok"}';
                                break;
                            case '1KI735SFY5':
                                $result_tmp[] = '{"a": "bTjXKLkS", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"bank": "0", "credit": "' . round($slotSettings->GetBalance() * 100) . '", "notes": "0", "time": "0"}, "s": "ok"}';
                                break;
                            case 'D3425KPHW1':
                                $lastEvent = $slotSettings->GetHistory();
                                if( $lastEvent != 'NULL' ) 
                                {
                                    $rp1 = implode(',', $lastEvent->serverResponse->reelsSymbols->rp);
                                    $rp2 = '' . $lastEvent->serverResponse->reelsSymbols->reel1[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[0] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[0] . '';
                                    $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[1] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[1] . '');
                                    $rp2 .= (',' . $lastEvent->serverResponse->reelsSymbols->reel1[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel2[2] . ',' . $lastEvent->serverResponse->reelsSymbols->reel3[2] . '');
                                }
                                else
                                {
                                    $rp1 = implode(',', [
                                        rand(0, count($slotSettings->reelStrip1) - 3), 
                                        rand(0, count($slotSettings->reelStrip2) - 3), 
                                        rand(0, count($slotSettings->reelStrip3) - 3)
                                    ]);
                                    $rp_1 = rand(0, count($slotSettings->reelStrip1) - 3);
                                    $rp_2 = rand(0, count($slotSettings->reelStrip2) - 3);
                                    $rp_3 = rand(0, count($slotSettings->reelStrip3) - 3);
                                    $rr1 = $slotSettings->reelStrip1[$rp_1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3];
                                    $rp2 = '' . $rr1 . ',' . $rr2 . ',' . $rr3 . '';
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 1];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 1];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 1];
                                    $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . '');
                                    $rr1 = $slotSettings->reelStrip1[$rp_1 + 2];
                                    $rr2 = $slotSettings->reelStrip2[$rp_2 + 2];
                                    $rr3 = $slotSettings->reelStrip3[$rp_3 + 2];
                                    $rp2 .= (',' . $rr1 . ',' . $rr2 . ',' . $rr3 . '');
                                    $bet = $slotSettings->Bet[0] * 100 * 5;
                                }
                                $result_tmp[] = '{"a": "XtlT6LoA", "cid": "' . $reqCid . '", "id": ' . $reqId . ', "res": {"symb": "' . $rp2 . '"}, "s": "ok"}';
                                break;
                            case 'PTUDQQMKZB':
                                $result_tmp[] = '{"a": "CRc5ya6I", "cid": "Gm4VyA0w", "id": ' . $reqId . ', "res": {"limit_count": "0"}, "s": "ok"}';
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
                                $result_tmp[] = '{"a": "o8W9ap6u", "cid": "gC55xpDm", "id": ' . $reqId . ', "s": "ok"}';
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
                            case 'WXMNPP7H8F':
                                $postData['slotEvent'] = 'bet';
                                $bonusMpl = 1;
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
                                $postData['bet'] = $postData['arg']['stake'] / 100;
                                $postData['lines'] = 5;
                                $lines = $postData['lines'];
                                $betLine = $postData['bet'] / $lines;
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                                $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($postData['bet']);
                                $balance = sprintf('%01.2f', $slotSettings->GetBalance());
                                $slotSettings->SetGameData('VegasHotWDBonusWin', 0);
                                $slotSettings->SetGameData('VegasHotWDFreeGames', 0);
                                $slotSettings->SetGameData('VegasHotWDCurrentFreeGame', 0);
                                $slotSettings->SetGameData('VegasHotWDTotalWin', 0);
                                $slotSettings->SetGameData('VegasHotWDFreeBalance', 0);
                                $slotSettings->SetGameData('VegasHotWDFreeStartWin', 0);
                                $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $postData['bet'], $lines);
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
                                        0
                                    ];
                                    $wild = [''];
                                    $scatter = '';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
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
                                                        $tmpStringWin = '{"Count":3,"Line":' . $k . ',"Win":' . $cWins[$k] . ',"stepWin":' . ($cWins[$k] + $totalWin + $slotSettings->GetGameData('VegasHotWDBonusWin')) . ',"winReel1":[' . ($linesId[$k][0] - 1) . ',"' . $s[0] . '"],"winReel2":[' . ($linesId[$k][1] - 1) . ',"' . $s[1] . '"],"winReel3":[' . ($linesId[$k][2] - 1) . ',"' . $s[2] . '"],"winReel4":["none","none"],"winReel5":["none","none"]}';
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
                                    for( $r = 1; $r <= 3; $r++ ) 
                                    {
                                        $isScat = false;
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter || $reels['reel' . $r][$p] == '0' ) 
                                            {
                                                $scattersCount++;
                                                $scattersStr .= ('"winReel' . $r . '":[' . $p . ',"' . $scatter . '"],');
                                                $isScat = true;
                                            }
                                        }
                                        if( !$isScat ) 
                                        {
                                            break;
                                        }
                                    }
                                    $scattersWin = 0;
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
                                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $postData['bet']) ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $i > 1500 ) 
                                            {
                                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"Bad Reel Strip"}';
                                                exit( $response );
                                            }
                                            if( $totalWin > 0 && $totalWin <= $spinWinLimit && $winType == 'win' ) 
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
                                $slotSettings->SetGameData('VegasHotWDTotalWin', $totalWin);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $winString = implode(',', $lineWins);
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betLine . ',"totalFreeGames":' . $slotSettings->GetGameData('VegasHotWDFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('VegasHotWDCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('VegasHotWDBonusWin') . ',"freeStartWin":' . $slotSettings->GetGameData('VegasHotWDFreeStartWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"bonusInfo":' . $scattersStr . ',"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $symb = $reels['reel1'][0] . ',' . $reels['reel2'][0] . ',' . $reels['reel3'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel2'][1] . ',' . $reels['reel3'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel2'][2] . ',' . $reels['reel3'][2];
                                $slotSettings->SaveLogReport($response, $betLine, $lines, $reportWin, $postData['slotEvent']);
                                $result_tmp[] = '{"a": "l0TVF5xh", "cid": "Gm4VyA0w", "id": ' . $reqId . ', "res": {"bank": "0", "cprize": "0", "credit": "' . round($balance * 100) . '", "flags": "6", "jprize": "0", "prize": "' . round($reportWin * 100) . '", "symb": "' . $symb . '"}, "s": "ok"}';
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
                                $totalWin = $slotSettings->GetGameData('VegasHotWDTotalWin');
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
                                $slotSettings->SetGameData('VegasHotWDTotalWin', $totalWin);
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
