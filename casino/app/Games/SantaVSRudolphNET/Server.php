<?php 
namespace VanguardLTE\Games\SantaVSRudolphNET
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
                    $postData = $_GET;
                    $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                    $result_tmp = [];
                    $aid = '';
                    $postData['slotEvent'] = 'bet';
                    if( $postData['action'] == 'freespin' ) 
                    {
                        $postData['slotEvent'] = 'freespin';
                        $postData['action'] = 'spin';
                    }
                    if( $postData['action'] == 'respin' ) 
                    {
                        $fsl = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                        if( $fsl > 0 ) 
                        {
                            $postData['slotEvent'] = 'freespin';
                        }
                        else
                        {
                            $postData['slotEvent'] = 'respin';
                        }
                        $postData['action'] = 'spin';
                    }
                    if( $postData['action'] == 'init' || $postData['action'] == 'reloadbalance' ) 
                    {
                        $postData['action'] = 'init';
                        $postData['slotEvent'] = 'init';
                    }
                    if( $postData['action'] == 'paytable' ) 
                    {
                        $postData['slotEvent'] = 'paytable';
                    }
                    if( $postData['action'] == 'initfreespin' ) 
                    {
                        $postData['slotEvent'] = 'initfreespin';
                    }
                    if( isset($postData['bet_denomination']) && $postData['bet_denomination'] >= 1 ) 
                    {
                        $postData['bet_denomination'] = $postData['bet_denomination'] / 100;
                        $slotSettings->CurrentDenom = $postData['bet_denomination'];
                        $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                        $slotSettings->SetGameData($slotSettings->slotId . 'GameDenom', $postData['bet_denomination']);
                    }
                    else if( $slotSettings->HasGameData($slotSettings->slotId . 'GameDenom') ) 
                    {
                        $postData['bet_denomination'] = $slotSettings->GetGameData($slotSettings->slotId . 'GameDenom');
                        $slotSettings->CurrentDenom = $postData['bet_denomination'];
                        $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                    }
                    $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                    if( $postData['slotEvent'] == 'bet' ) 
                    {
                        $lines = 20;
                        $betline = $postData['bet_betlevel'];
                        if( $lines <= 0 || $betline <= 0.0001 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetBalance() < ($lines * $betline) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                            exit( $response );
                        }
                    }
                    if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['slotEvent'] == 'freespin' ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                        exit( $response );
                    }
                    $aid = (string)$postData['action'];
                    switch( $aid ) 
                    {
                        case 'init':
                            $gameBets = $slotSettings->Bet;
                            $lastEvent = $slotSettings->GetHistory();
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'WildsWalk', [
                                'Santa' => [], 
                                'Rudolph' => []
                            ]);
                            $freeState = '';
                            if( $lastEvent != 'NULL' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $freeState = $lastEvent->serverResponse->freeState;
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $curReels = '&rs.i0.r.i0.syms=SYM' . $reels->reel1[0] . '%2CSYM' . $reels->reel1[1] . '%2CSYM' . $reels->reel1[2] . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels->reel2[0] . '%2CSYM' . $reels->reel2[1] . '%2CSYM' . $reels->reel2[2] . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels->reel3[0] . '%2CSYM' . $reels->reel3[1] . '%2CSYM' . $reels->reel3[2] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels->reel4[0] . '%2CSYM' . $reels->reel4[1] . '%2CSYM' . $reels->reel4[2] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels->reel5[0] . '%2CSYM' . $reels->reel5[1] . '%2CSYM' . $reels->reel5[2] . '');
                                $curReels .= ('&rs.i1.r.i0.syms=SYM' . $reels->reel1[0] . '%2CSYM' . $reels->reel1[1] . '%2CSYM' . $reels->reel1[2] . '');
                                $curReels .= ('&rs.i1.r.i1.syms=SYM' . $reels->reel2[0] . '%2CSYM' . $reels->reel2[1] . '%2CSYM' . $reels->reel2[2] . '');
                                $curReels .= ('&rs.i1.r.i2.syms=SYM' . $reels->reel3[0] . '%2CSYM' . $reels->reel3[1] . '%2CSYM' . $reels->reel3[2] . '');
                                $curReels .= ('&rs.i1.r.i3.syms=SYM' . $reels->reel4[0] . '%2CSYM' . $reels->reel4[1] . '%2CSYM' . $reels->reel4[2] . '');
                                $curReels .= ('&rs.i1.r.i4.syms=SYM' . $reels->reel5[0] . '%2CSYM' . $reels->reel5[1] . '%2CSYM' . $reels->reel5[2] . '');
                                $curReels .= ('&rs.i0.r.i0.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i0.r.i1.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i0.r.i2.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i0.r.i3.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i0.r.i4.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i1.r.i0.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i1.r.i1.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i1.r.i2.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i1.r.i3.pos=' . $reels->rp[0]);
                                $curReels .= ('&rs.i1.r.i4.pos=' . $reels->rp[0]);
                            }
                            else
                            {
                                $curReels = '&rs.i0.r.i0.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i0.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i0.r.i1.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i0.r.i2.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i0.r.i3.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i0.r.i4.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i1.r.i0.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i1.r.i1.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i1.r.i2.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i1.r.i3.pos=' . rand(1, 10));
                                $curReels .= ('&rs.i1.r.i4.pos=' . rand(1, 10));
                            }
                            for( $d = 0; $d < count($slotSettings->Denominations); $d++ ) 
                            {
                                $slotSettings->Denominations[$d] = $slotSettings->Denominations[$d] * 100;
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                            {
                                $freeState = 'rs.i1.r.i0.syms=SYM2%2CSYM5%2CSYM5&bl.i6.coins=1&bl.i17.reelset=ALL&rs.i0.nearwin=4&bl.i15.id=15&rs.i0.r.i4.hold=false&gamestate.history=basic%2Cfreespin&rs.i1.r.i2.hold=false&game.win.cents=176&rs.i1.r.i1.overlay.i2.pos=61&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i10.line=1%2C2%2C1%2C2%2C1&bl.i0.reelset=ALL&bl.i18.coins=1&bl.i10.id=10&freespins.initial=15&bl.i3.reelset=ALL&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i13.coins=1&rs.i0.r.i0.syms=SYM5%2CSYM0%2CSYM6&bl.i2.id=2&rs.i1.r.i1.pos=59&rs.i0.r.i0.pos=24&bl.i14.reelset=ALL&game.win.coins=88&rs.i1.r.i0.hold=false&bl.i3.id=3&ws.i1.reelset=freespin&bl.i12.coins=1&bl.i8.reelset=ALL&clientaction=init&rs.i0.r.i2.hold=false&bl.i16.id=16&casinoID=netent&bl.i5.coins=1&rs.i1.r.i1.overlay.i1.row=1&bl.i8.id=8&rs.i0.r.i3.pos=17&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i12.line=2%2C1%2C2%2C1%2C2&bl.i0.line=1%2C1%2C1%2C1%2C1&rs.i0.r.i2.syms=SYM7%2CSYM6%2CSYM6&rs.i1.r.i1.overlay.i1.with=SYM1_FS&game.win.amount=1.76&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&ws.i0.reelset=freespin&bl.i1.id=1&rs.i0.r.i3.attention.i0=2&rs.i1.r.i1.overlay.i0.with=SYM1_FS&rs.i1.r.i4.pos=39&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&multiplier=1&bl.i14.id=14&bl.i19.line=0%2C2%2C2%2C2%2C0&freespins.denomination=2.000&bl.i12.reelset=ALL&bl.i2.coins=1&bl.i6.id=6&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&freespins.totalwin.coins=80&ws.i0.direction=left_to_right&freespins.total=15&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM5%2CSYM4%2CSYM4&gamesoundurl=&bet.betlevel=1&bl.i5.reelset=ALL&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&bl.i14.line=1%2C1%2C2%2C1%2C1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM5%2CSYM5%2CSYM0&rs.i0.r.i2.pos=48&bl.i13.line=1%2C1%2C0%2C1%2C1&ws.i1.betline=19&rs.i1.r.i0.pos=20&bl.i0.coins=1&bl.i2.reelset=ALL&rs.i1.r.i1.overlay.i2.row=2&rs.i1.r.i4.hold=false&freespins.left=14&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&bl.i15.reelset=ALL&rs.i0.r.i3.hold=false&bet.denomination=' . ($slotSettings->CurrentDenomination * 100) . '&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&freespins.win.coins=80&historybutton=false&bl.i5.id=5&gameEventSetters.enabled=false&rs.i1.r.i3.pos=27&rs.i0.r.i1.syms=SYM5%2CSYM1%2CSYM3&bl.i3.coins=1&ws.i1.types.i0.coins=40&bl.i10.coins=1&bl.i18.id=18&ws.i0.betline=3&rs.i1.r.i3.hold=false&totalwin.coins=88&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=freespin&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&rs.i0.r.i3.syms=SYM4%2CSYM4%2CSYM0&rs.i1.r.i1.syms=SYM7%2CSYM1_FS%2CSYM5&bl.i16.coins=1&freespins.win.cents=160&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&rs.i1.r.i1.overlay.i0.pos=59&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&rs.i0.r.i1.pos=61&rs.i1.r.i3.syms=SYM3%2CSYM3%2CSYM6&bl.i13.id=13&rs.i0.r.i1.hold=false&ws.i1.types.i0.wintype=coins&bl.i9.line=1%2C0%2C1%2C0%2C1&ws.i1.sym=SYM2&betlevel.standard=1&bl.i10.reelset=ALL&ws.i1.types.i0.cents=80&gameover=false&bl.i11.coins=1&ws.i1.direction=left_to_right&bl.i13.reelset=ALL&bl.i0.id=0&nextaction=freespin&bl.i15.line=0%2C1%2C1%2C1%2C0&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&bl.i18.line=2%2C0%2C2%2C0%2C2&freespins.totalwin.cents=160&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&bl.i11.id=11&freespins.betlevel=1&ws.i0.pos.i2=2%2C2&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&bl.i17.coins=1&ws.i1.pos.i0=0%2C0&ws.i1.pos.i1=2%2C2&ws.i1.pos.i2=1%2C2&ws.i0.pos.i1=1%2C1&bl.i19.reelset=ALL&ws.i0.pos.i0=0%2C0&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&rs.i0.id=basic&credit=' . $balanceInCents . '&ws.i0.types.i0.coins=40&bl.i1.reelset=ALL&rs.i1.r.i1.overlay.i1.pos=60&rs.i1.r.i1.overlay.i2.with=SYM1_FS&bl.i1.line=0%2C0%2C0%2C0%2C0&ws.i0.sym=SYM2&bl.i17.id=17&rs.i1.r.i2.pos=1&bl.i16.reelset=ALL&ws.i0.types.i0.wintype=coins&nearwinallowed=true&bl.i8.line=1%2C0%2C0%2C0%2C1&rs.i1.r.i1.overlay.i0.row=0&freespins.wavecount=1&rs.i0.r.i4.attention.i0=2&bl.i8.coins=1&bl.i15.coins=1&bl.i2.line=2%2C2%2C2%2C2%2C2&rs.i0.r.i0.attention.i0=1&rs.i1.r.i2.syms=SYM3%2CSYM3%2CSYM2&totalwin.cents=176&rs.i0.r.i0.hold=false&restore=true&rs.i1.id=freespin&bl.i12.id=12&bl.i4.id=4&rs.i0.r.i4.pos=10&bl.i7.coins=1&ws.i0.types.i0.cents=80&bl.i6.reelset=ALL&wavecount=1&bl.i14.coins=1&rs.i1.r.i1.hold=false' . $freeState;
                            }
                            $result_tmp[] = 'rs.i1.r.i0.syms=SYM6%2CSYM9%2CSYM5&bl.i6.coins=1&bl.i17.reelset=ALL&bl.i15.id=15&rs.i0.r.i4.hold=false&rs.i1.r.i2.hold=false&game.win.cents=0&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i10.line=1%2C2%2C1%2C2%2C1&bl.i0.reelset=ALL&bl.i18.coins=1&bl.i10.id=10&bl.i3.reelset=ALL&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i13.coins=1&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM11%2CSYM3%2CSYM8&bl.i2.id=2&rs.i1.r.i1.pos=0&rs.i3.r.i4.pos=0&rs.i0.r.i0.pos=0&bl.i14.reelset=ALL&rs.i2.r.i3.pos=0&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=superspin&game.win.coins=0&rs.i1.r.i0.hold=false&bl.i3.id=3&bl.i12.coins=1&bl.i8.reelset=ALL&clientaction=init&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM5%2CSYM9%2CSYM11&bl.i16.id=16&casinoID=netent&bl.i5.coins=1&rs.i3.r.i2.hold=false&bl.i8.id=8&rs.i0.r.i3.pos=0&rs.i4.r.i0.syms=SYM11%2CSYM10%2CSYM5&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i12.line=2%2C1%2C2%2C1%2C2&bl.i0.line=1%2C1%2C1%2C1%2C1&rs.i4.r.i2.pos=0&rs.i0.r.i2.syms=SYM8%2CSYM9%2CSYM5&game.win.amount=0&betlevel.all=1&denomination.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C12%2C15%2C20%2C25%2C35%2C50%2C75%2C100%2C125%2C150%2C200%2C250%2C375%2C500%2C750%2C1000%2C1500%2C2000&rs.i2.r.i0.pos=0&bl.i1.id=1&rs.i3.r.i2.syms=SYM6%2CSYM12%2CSYM8&rs.i1.r.i4.pos=0&denomination.standard=5&rs.i3.id=basic&multiplier=1&bl.i14.id=14&bl.i19.line=0%2C2%2C2%2C2%2C0&bl.i12.reelset=ALL&bl.i2.coins=1&bl.i6.id=6&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&rs.i1.r.i4.syms=SYM10%2CSYM7%2CSYM9&gamesoundurl=&rs.i4.r.i2.hold=false&bl.i5.reelset=ALL&rs.i4.r.i1.syms=SYM5%2CSYM9%2CSYM12&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM8%2CSYM11%2CSYM5&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&rs.i4.r.i1.hold=false&rs.i3.r.i2.pos=0&bl.i14.line=1%2C1%2C2%2C1%2C1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM9%2CSYM3%2CSYM6&rs.i0.r.i2.pos=0&bl.i13.line=1%2C1%2C0%2C1%2C1&rs.i1.r.i0.pos=0&bl.i0.coins=1&rs.i2.r.i0.syms=SYM6%2CSYM9%2CSYM5&bl.i2.reelset=ALL&rs.i3.r.i1.syms=SYM6%2CSYM10%2CSYM7&rs.i1.r.i4.hold=false&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM3%2CSYM12%2CSYM6&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&rs.i3.r.i0.hold=false&bl.i15.reelset=ALL&rs.i0.r.i3.hold=false&rs.i4.id=basic_respin&rs.i2.r.i1.hold=false&gameServerVersion=1.5.0&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&historybutton=false&bl.i5.id=5&gameEventSetters.enabled=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM9%2CSYM3%2CSYM12&bl.i3.coins=1&bl.i10.coins=1&bl.i18.id=18&rs.i2.r.i1.pos=0&rs.i4.r.i4.pos=0&rs.i1.r.i3.hold=false&totalwin.coins=0&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=basic&rs.i4.r.i0.pos=0&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&rs.i3.r.i1.hold=false&rs.i0.r.i3.syms=SYM5%2CSYM3%2CSYM11&rs.i1.r.i1.syms=SYM6%2CSYM9%2CSYM3&bl.i16.coins=1&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&rs.i2.r.i3.hold=false&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM10%2CSYM6%2CSYM7&rs.i1.r.i3.syms=SYM12%2CSYM8%2CSYM10&bl.i13.id=13&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM11%2CSYM8%2CSYM10&bl.i9.line=1%2C0%2C1%2C0%2C1&betlevel.standard=1&bl.i10.reelset=ALL&gameover=true&rs.i3.r.i3.pos=0&bl.i11.coins=1&bl.i13.reelset=ALL&bl.i0.id=0&nextaction=spin&bl.i15.line=0%2C1%2C1%2C1%2C0&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&bl.i18.line=2%2C0%2C2%2C0%2C2&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&bl.i11.id=11&rs.i4.r.i3.pos=0&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&rs.i4.r.i4.hold=false&bl.i17.coins=1&bl.i19.reelset=ALL&rs.i2.r.i4.syms=SYM12%2CSYM7%2CSYM11&rs.i4.r.i3.hold=false&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&rs.i0.id=freespin_respin&credit=' . $balanceInCents . '&bl.i1.reelset=ALL&rs.i2.r.i2.pos=0&bl.i1.line=0%2C0%2C0%2C0%2C0&bl.i17.id=17&rs.i2.r.i2.syms=SYM11%2CSYM10%2CSYM9&rs.i1.r.i2.pos=0&bl.i16.reelset=ALL&rs.i3.r.i3.syms=SYM7%2CSYM8%2CSYM5&rs.i3.r.i4.hold=false&nearwinallowed=true&bl.i8.line=1%2C0%2C0%2C0%2C1&rs.i3.r.i3.hold=false&bl.i8.coins=1&bl.i15.coins=1&bl.i2.line=2%2C2%2C2%2C2%2C2&rs.i1.r.i2.syms=SYM5%2CSYM12%2CSYM8&totalwin.cents=0&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM11%2CSYM10%2CSYM5&restore=false&rs.i1.id=freespin&rs.i3.r.i4.syms=SYM10%2CSYM9%2CSYM6&bl.i12.id=12&bl.i4.id=4&rs.i0.r.i4.pos=0&bl.i7.coins=1&bl.i6.reelset=ALL&rs.i3.r.i0.pos=0&rs.i2.r.i2.hold=false&wavecount=1&bl.i14.coins=1&rs.i1.r.i1.hold=false';
                            break;
                        case 'paytable':
                            $result_tmp[] = 'bl.i17.reelset=ALL&bl.i15.id=15&pt.i0.comp.i29.type=betline&pt.i0.comp.i17.symbol=SYM8&pt.i0.comp.i5.freespins=0&pt.i0.comp.i23.n=5&pt.i0.comp.i13.symbol=SYM7&pt.i1.comp.i8.type=betline&pt.i1.comp.i4.n=4&pt.i0.comp.i15.multi=4&bl.i10.line=1%2C2%2C1%2C2%2C1&pt.i1.comp.i27.symbol=SYM12&pt.i0.comp.i28.multi=15&bl.i18.coins=1&pt.i1.comp.i29.freespins=0&pt.i1.comp.i3.multi=15&pt.i0.comp.i11.n=5&pt.i1.comp.i23.symbol=SYM10&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i13.coins=1&pt.i0.id=basic&pt.i0.comp.i1.type=betline&bl.i2.id=2&pt.i1.comp.i10.type=betline&pt.i0.comp.i4.symbol=SYM4&pt.i1.comp.i5.freespins=0&pt.i1.comp.i8.symbol=SYM5&bl.i14.reelset=ALL&pt.i1.comp.i19.n=4&pt.i0.comp.i17.freespins=0&pt.i0.comp.i8.symbol=SYM5&pt.i0.comp.i0.symbol=SYM3&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=60&pt.i1.id=freespin&bl.i3.id=3&pt.i0.comp.i24.n=3&bl.i8.reelset=ALL&clientaction=paytable&pt.i1.comp.i27.freespins=0&bl.i16.id=16&pt.i1.comp.i5.n=5&bl.i5.coins=1&pt.i1.comp.i8.multi=300&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=0&pt.i0.comp.i21.multi=3&pt.i1.comp.i13.multi=50&pt.i0.comp.i12.n=3&pt.i0.comp.i13.type=betline&bl.i0.line=1%2C1%2C1%2C1%2C1&pt.i1.comp.i7.freespins=0&pt.i0.comp.i3.multi=15&pt.i1.comp.i22.type=betline&pt.i0.comp.i21.n=3&pt.i1.comp.i6.n=3&bl.i1.id=1&pt.i0.comp.i10.type=betline&pt.i1.comp.i11.symbol=SYM6&pt.i0.comp.i5.multi=400&pt.i1.comp.i1.freespins=0&bl.i14.id=14&pt.i1.comp.i16.symbol=SYM8&pt.i1.comp.i23.multi=60&pt.i1.comp.i4.type=betline&pt.i1.comp.i18.multi=3&bl.i2.coins=1&pt.i1.comp.i26.type=betline&pt.i0.comp.i8.multi=300&pt.i0.comp.i1.freespins=0&bl.i5.reelset=ALL&pt.i0.comp.i22.n=4&pt.i0.comp.i28.symbol=SYM12&pt.i1.comp.i17.type=betline&pt.i1.comp.i0.symbol=SYM3&pt.i1.comp.i7.n=4&pt.i1.comp.i5.multi=400&bl.i14.line=1%2C1%2C2%2C1%2C1&pt.i0.comp.i21.type=betline&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=4&pt.i0.comp.i13.multi=50&pt.i0.comp.i17.type=betline&bl.i13.line=1%2C1%2C0%2C1%2C1&pt.i1.comp.i22.symbol=SYM10&bl.i0.coins=1&bl.i2.reelset=ALL&pt.i0.comp.i10.n=4&pt.i1.comp.i6.multi=10&pt.i1.comp.i19.symbol=SYM9&pt.i0.comp.i22.freespins=0&pt.i0.comp.i20.symbol=SYM9&pt.i0.comp.i15.freespins=0&pt.i0.comp.i28.freespins=0&pt.i0.comp.i0.n=3&pt.i1.comp.i21.multi=3&pt.i0.comp.i0.type=betline&pt.i1.comp.i0.multi=20&g4mode=false&pt.i1.comp.i8.n=5&pt.i0.comp.i25.multi=15&pt.i0.comp.i16.symbol=SYM8&pt.i1.comp.i21.freespins=0&pt.i0.comp.i1.multi=150&pt.i0.comp.i27.n=3&pt.i1.comp.i9.type=betline&pt.i1.comp.i24.multi=2&pt.i1.comp.i23.type=betline&pt.i1.comp.i26.n=5&bl.i18.id=18&pt.i1.comp.i28.symbol=SYM12&pt.i1.comp.i17.multi=100&pt.i0.comp.i18.multi=3&bl.i5.line=0%2C0%2C1%2C0%2C0&pt.i0.comp.i9.n=3&pt.i1.comp.i21.type=betline&bl.i7.line=1%2C2%2C2%2C2%2C1&pt.i0.comp.i28.type=betline&pt.i1.comp.i18.type=betline&pt.i0.comp.i10.symbol=SYM6&pt.i0.comp.i15.n=3&pt.i0.comp.i21.symbol=SYM10&bl.i7.reelset=ALL&pt.i1.comp.i15.n=3&isJackpotWin=false&pt.i1.comp.i20.freespins=0&pt.i1.comp.i7.type=betline&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=75&pt.i0.comp.i17.multi=100&pt.i1.comp.i25.type=betline&pt.i1.comp.i9.n=3&pt.i0.comp.i28.n=4&bl.i9.line=1%2C0%2C1%2C0%2C1&pt.i0.comp.i2.multi=500&pt.i1.comp.i27.n=3&pt.i0.comp.i0.freespins=0&pt.i1.comp.i25.multi=15&pt.i1.comp.i16.freespins=0&pt.i1.comp.i5.type=betline&pt.i1.comp.i24.symbol=SYM11&pt.i1.comp.i13.symbol=SYM7&pt.i1.comp.i17.symbol=SYM8&pt.i0.comp.i16.n=4&bl.i13.reelset=ALL&bl.i0.id=0&pt.i1.comp.i16.n=4&pt.i0.comp.i5.symbol=SYM4&bl.i15.line=0%2C1%2C1%2C1%2C0&pt.i1.comp.i7.symbol=SYM5&bl.i19.id=19&pt.i0.comp.i1.symbol=SYM3&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&pt.i1.comp.i9.freespins=0&playercurrency=%26%23x20AC%3B&bl.i19.reelset=ALL&pt.i0.comp.i25.n=4&pt.i1.comp.i28.n=4&pt.i0.comp.i9.freespins=0&credit=' . $balanceInCents . '&pt.i0.comp.i5.type=betline&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=60&pt.i0.comp.i25.type=betline&bl.i1.reelset=ALL&pt.i1.comp.i18.symbol=SYM9&pt.i1.comp.i12.symbol=SYM7&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i0.comp.i26.freespins=0&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=150&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=4&pt.i1.comp.i17.n=5&pt.i0.comp.i23.type=betline&bl.i17.id=17&pt.i1.comp.i17.freespins=0&pt.i1.comp.i26.multi=60&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM3&pt.i1.comp.i29.multi=60&pt.i0.comp.i25.freespins=0&pt.i0.comp.i26.n=5&pt.i0.comp.i27.symbol=SYM12&pt.i1.comp.i29.n=5&pt.i0.comp.i23.multi=60&bl.i2.line=2%2C2%2C2%2C2%2C2&pt.i1.comp.i28.multi=15&pt.i1.comp.i18.freespins=0&pt.i0.comp.i14.n=5&pt.i0.comp.i0.multi=20&bl.i6.reelset=ALL&pt.i0.comp.i19.multi=25&pt.i1.comp.i18.n=3&pt.i0.comp.i12.freespins=0&pt.i0.comp.i24.multi=2&pt.i0.comp.i19.symbol=SYM9&bl.i6.coins=1&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i0.comp.i4.multi=100&pt.i0.comp.i15.symbol=SYM8&pt.i1.comp.i14.multi=150&pt.i0.comp.i22.multi=20&pt.i1.comp.i19.type=betline&pt.i0.comp.i11.symbol=SYM6&pt.i1.comp.i27.multi=2&bl.i0.reelset=ALL&pt.i0.comp.i16.freespins=0&pt.i1.comp.i6.freespins=0&pt.i1.comp.i29.symbol=SYM12&pt.i1.comp.i22.n=4&bl.i10.id=10&pt.i0.comp.i4.freespins=0&pt.i1.comp.i25.symbol=SYM11&bl.i3.reelset=ALL&pt.i1.comp.i24.type=betline&pt.i0.comp.i19.n=4&pt.i0.comp.i2.symbol=SYM3&pt.i0.comp.i20.type=betline&pt.i0.comp.i6.symbol=SYM5&pt.i1.comp.i11.n=5&pt.i0.comp.i5.n=5&pt.i1.comp.i2.symbol=SYM3&pt.i0.comp.i3.type=betline&pt.i1.comp.i19.multi=25&pt.i1.comp.i6.symbol=SYM5&pt.i0.comp.i27.multi=2&pt.i0.comp.i9.multi=5&bl.i12.coins=1&pt.i0.comp.i22.symbol=SYM10&pt.i0.comp.i26.symbol=SYM11&pt.i1.comp.i19.freespins=0&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&pt.i1.comp.i4.freespins=0&pt.i1.comp.i12.type=betline&pt.i1.comp.i21.symbol=SYM10&pt.i1.comp.i23.n=5&bl.i8.id=8&pt.i0.comp.i16.multi=30&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i12.line=2%2C1%2C2%2C1%2C2&pt.i1.comp.i9.multi=5&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=500&pt.i0.comp.i6.n=3&pt.i1.comp.i12.n=3&pt.i1.comp.i3.type=betline&pt.i1.comp.i10.freespins=0&pt.i1.comp.i28.type=betline&pt.i0.comp.i29.n=5&pt.i1.comp.i20.multi=75&pt.i0.comp.i27.freespins=0&pt.i1.comp.i24.n=3&pt.i1.comp.i27.type=betline&pt.i1.comp.i2.type=betline&pt.i0.comp.i2.freespins=0&pt.i0.comp.i7.n=4&pt.i0.comp.i11.multi=200&pt.i1.comp.i14.symbol=SYM7&pt.i0.comp.i7.type=betline&bl.i19.line=0%2C2%2C2%2C2%2C0&bl.i12.reelset=ALL&pt.i0.comp.i17.n=5&bl.i6.id=6&pt.i0.comp.i29.multi=60&pt.i1.comp.i13.n=4&pt.i0.comp.i8.freespins=0&pt.i1.comp.i4.multi=100&gamesoundurl=&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=150&pt.i1.comp.i7.multi=80&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=10&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&pt.i1.comp.i5.symbol=SYM4&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM10&playforfun=false&pt.i1.comp.i25.n=4&pt.i0.comp.i2.type=betline&pt.i1.comp.i20.type=betline&pt.i1.comp.i22.multi=20&pt.i0.comp.i8.n=5&pt.i1.comp.i22.freespins=0&pt.i0.comp.i11.type=betline&pt.i0.comp.i18.n=3&pt.i1.comp.i14.n=5&pt.i1.comp.i16.multi=30&pt.i1.comp.i15.freespins=0&pt.i0.comp.i27.type=betline&pt.i1.comp.i28.freespins=0&pt.i0.comp.i7.symbol=SYM5&bl.i15.reelset=ALL&pt.i1.comp.i0.freespins=0&gameServerVersion=1.5.0&bl.i11.line=0%2C1%2C0%2C1%2C0&historybutton=false&bl.i5.id=5&pt.i0.comp.i18.symbol=SYM9&pt.i0.comp.i12.multi=5&pt.i1.comp.i14.freespins=0&bl.i3.coins=1&bl.i10.coins=1&pt.i0.comp.i12.symbol=SYM7&pt.i0.comp.i14.symbol=SYM7&pt.i1.comp.i13.freespins=0&pt.i0.comp.i14.type=betline&pt.i1.comp.i0.n=3&pt.i1.comp.i26.symbol=SYM11&pt.i0.comp.i7.multi=80&jackpotcurrency=%26%23x20AC%3B&bl.i16.coins=1&bl.i9.coins=1&pt.i1.comp.i11.multi=200&pt.i0.comp.i1.n=4&pt.i0.comp.i20.n=5&pt.i0.comp.i29.symbol=SYM12&pt.i1.comp.i3.symbol=SYM4&pt.i1.comp.i23.freespins=0&bl.i13.id=13&pt.i0.comp.i25.symbol=SYM11&pt.i0.comp.i26.type=betline&pt.i0.comp.i9.type=betline&pt.i1.comp.i16.type=betline&pt.i1.comp.i20.symbol=SYM9&bl.i10.reelset=ALL&pt.i1.comp.i12.multi=5&pt.i0.comp.i29.freespins=0&pt.i1.comp.i1.n=4&pt.i1.comp.i11.freespins=0&pt.i0.comp.i9.symbol=SYM6&bl.i11.coins=1&pt.i0.comp.i16.type=betline&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i4.reelset=ALL&bl.i4.coins=1&pt.i0.comp.i2.n=5&bl.i18.line=2%2C0%2C2%2C0%2C2&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&bl.i11.id=11&pt.i0.comp.i6.type=betline&pt.i1.comp.i2.freespins=0&pt.i1.comp.i25.freespins=0&bl.i9.reelset=ALL&bl.i17.coins=1&pt.i1.comp.i10.multi=60&pt.i1.comp.i10.symbol=SYM6&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&pt.i1.comp.i2.n=5&pt.i1.comp.i20.n=5&pt.i1.comp.i24.freespins=0&pt.i0.comp.i4.type=betline&pt.i1.comp.i26.freespins=0&pt.i1.comp.i1.type=betline&bl.i1.line=0%2C0%2C0%2C0%2C0&pt.i0.comp.i20.freespins=0&pt.i1.comp.i29.type=betline&bl.i16.reelset=ALL&pt.i0.comp.i3.n=3&pt.i1.comp.i6.type=betline&pt.i1.comp.i4.symbol=SYM4&bl.i8.line=1%2C0%2C0%2C0%2C1&pt.i0.comp.i24.symbol=SYM11&bl.i8.coins=1&bl.i15.coins=1&pt.i1.comp.i3.n=3&pt.i1.comp.i21.n=3&pt.i0.comp.i18.freespins=0&bl.i12.id=12&pt.i1.comp.i15.symbol=SYM8&pt.i1.comp.i3.freespins=0&bl.i4.id=4&bl.i7.coins=1&pt.i1.comp.i9.symbol=SYM6&pt.i0.comp.i3.symbol=SYM4&pt.i0.comp.i24.type=betline&bl.i14.coins=1&pt.i1.comp.i12.freespins=0&pt.i0.comp.i4.n=4&pt.i1.comp.i10.n=4';
                        case 'initfreespin':
                            $result_tmp[] = 'previous.rs.i0=basic_respin&rs.i4.id=basic_respin&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM6%2CSYM9%2CSYM5&gameServerVersion=1.5.0&g4mode=false&freespins.win.coins=0&historybutton=false&rs.i0.r.i4.hold=false&next.rs=freespin&gamestate.history=basic&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM9%2CSYM3%2CSYM12&rs.i2.r.i1.pos=0&game.win.cents=170&haswonbonusgame=false&rs.i4.r.i4.pos=33&rs.i1.r.i3.hold=false&totalwin.coins=0&gamestate.current=freespin&freespins.initial=10&rs.i4.r.i0.pos=24&jackpotcurrency=%26%23x20AC%3B&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i3.r.i4.overlay.i0.pos=137&rs.i0.r.i0.syms=SYM11%2CSYM3%2CSYM8&rs.i0.r.i3.syms=SYM5%2CSYM3%2CSYM11&rs.i1.r.i1.syms=SYM6%2CSYM9%2CSYM3&rs.i1.r.i1.pos=0&rs.i3.r.i4.pos=135&freespins.win.cents=0&isJackpotWin=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=0&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&cjpUrl=&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM12%2CSYM7%2CSYM3&rs.i1.r.i3.syms=SYM12%2CSYM8%2CSYM10&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=44&rs.i2.id=superspin&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&rs.i3.r.i4.overlay.i0.with=SYM16&rs.i2.r.i1.syms=SYM11%2CSYM8%2CSYM10&clientaction=initfreespin&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM11%2CSYM7%2CSYM8&rs.i3.r.i2.hold=false&gameover=false&rs.i3.r.i3.pos=46&rs.i0.r.i3.pos=0&rs.i4.r.i1.overlay.i0.with=SYM17&rs.i4.r.i1.overlay.i0.pos=11&rs.i4.r.i0.syms=SYM3%2CSYM5%2CSYM11&nextaction=freespin&rs.i4.r.i2.pos=2&rs.i0.r.i2.syms=SYM8%2CSYM9%2CSYM5&haswonsuperspins=false&game.win.amount=0&freespins.totalwin.cents=0&freespins.betlevel=1&rs.i4.r.i3.pos=12&playercurrency=%26%23x20AC%3B&rs.i2.r.i0.pos=0&rs.i4.r.i4.hold=false&current.rs.i0=freespin&rs.i2.r.i4.syms=SYM12%2CSYM7%2CSYM11&rs.i4.r.i1.overlay.i0.row=2&rs.i3.r.i2.syms=SYM8%2CSYM3%2CSYM9&rs.i4.r.i3.hold=false&rs.i3.r.i4.overlay.i0.row=2&rs.i0.id=freespin_respin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=0&rs.i3.id=basic&multiplier=1&rs.i2.r.i2.pos=0&last.rs=basic_respin&freespins.denomination=' . $slotSettings->CurrentDenomination . '&freespins.totalwin.coins=0&freespins.total=10&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM10%2CSYM7%2CSYM9&rs.i2.r.i2.syms=SYM11%2CSYM10%2CSYM9&gamesoundurl=&rs.i1.r.i2.pos=0&rs.i3.r.i3.syms=SYM5%2CSYM11%2CSYM7&bet.betlevel=1&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i4.r.i1.syms=SYM8%2CSYM10%2CSYM6&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM10%2CSYM5%2CSYM11&playercurrencyiso=' . $slotSettings->slotCurrency . '&featurewildcount.bonusgame=0&featurewildcount.superspins=0&rs.i4.r.i1.hold=false&freespins.wavecount=1&rs.i3.r.i2.pos=2&rs.i3.r.i3.hold=false&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM9%2CSYM3%2CSYM6&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM5%2CSYM12%2CSYM8&rs.i1.r.i0.pos=0&totalwin.cents=170&rs.i2.r.i0.syms=SYM6%2CSYM9%2CSYM5&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM11%2CSYM10%2CSYM5&rs.i1.id=freespin&rs.i3.r.i4.syms=SYM5%2CSYM10%2CSYM16&rs.i3.r.i1.syms=SYM4%2CSYM9%2CSYM10&rs.i1.r.i4.hold=false&freespins.left=10&rs.i0.r.i4.pos=0&rs.i4.r.i1.pos=9&rs.i4.r.i2.syms=SYM6%2CSYM5%2CSYM9&rs.i3.r.i0.pos=46&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bet.denomination=' . $slotSettings->CurrentDenomination;
                            break;
                        case 'spin':
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
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $lines = 20;
                            $isRespin = $slotSettings->GetGameData($slotSettings->slotId . 'IsRespin');
                            if( $postData['slotEvent'] != 'freespin' && $postData['slotEvent'] != 'respin' ) 
                            {
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $postData['bet_betlevel'];
                                $allbet = $betline * $lines;
                                $slotSettings->UpdateJackpots($allbet);
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'IsRespin', false);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Denom', $postData['bet_denomination']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $slotSettings->SetGameData($slotSettings->slotId . 'SantaScore', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RudolphScore', 0);
                                $bonusMpl = 1;
                            }
                            else if( $postData['slotEvent'] == 'respin' ) 
                            {
                                $postData['bet_denomination'] = $slotSettings->GetGameData($slotSettings->slotId . 'Denom');
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                $allbet = $betline * $lines;
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            else
                            {
                                $postData['bet_denomination'] = $slotSettings->GetGameData($slotSettings->slotId . 'Denom');
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                $allbet = $betline * $lines;
                                if( !$isRespin ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                }
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            /*if( !$slotSettings->HasGameDataStatic($slotSettings->slotId . 'timeWinLimit') || $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimit') <= 0 ) 
                            {
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimitNum', rand(0, count($slotSettings->winLimitsArr) - 1));
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit0', time());
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit', $slotSettings->winLimitsArr[$slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimitNum')][0]);
                                $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWin', 0);
                            }*/
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            if( $winType == 'bonus' && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $winType = 'win';
                            }
                            $jackRandom = rand(1, 500);
                            $mainSymAnim = '';
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
                                    0
                                ];
                                $wild = [
                                    '15', 
                                    '16'
                                ];
                                $scatter = '13';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $reelsTmp = $reels;
                                $isBonusStart = false;
                                $SantaScore = $slotSettings->GetGameData($slotSettings->slotId . 'SantaScore');
                                $RudolphScore = $slotSettings->GetGameData($slotSettings->slotId . 'RudolphScore');
                                $WildsWalk = $slotSettings->GetGameData($slotSettings->slotId . 'WildsWalk');
                                $walkingWildsStr = '';
                                foreach( $WildsWalk['Santa'] as $key => $wwalk ) 
                                {
                                    $WildsWalk['Santa'][$key][0]--;
                                    if( $WildsWalk['Santa'][$key][0] < 1 ) 
                                    {
                                        unset($WildsWalk['Santa'][$key]);
                                        $SantaScore++;
                                    }
                                }
                                foreach( $WildsWalk['Rudolph'] as $key => $wwalk ) 
                                {
                                    $WildsWalk['Rudolph'][$key][0]++;
                                    if( $WildsWalk['Rudolph'][$key][0] > 5 ) 
                                    {
                                        unset($WildsWalk['Rudolph'][$key]);
                                        $RudolphScore++;
                                    }
                                }
                                $bChecckedReels = [];
                                $bChecckedReels[0] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $bChecckedReels[1] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $bChecckedReels[2] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $bChecckedReels[3] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $bChecckedReels[4] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                $bChecckedReels[5] = [
                                    0, 
                                    0, 
                                    0
                                ];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p < 3; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == '16' ) 
                                        {
                                            $WildsWalk['Santa'][] = [
                                                $r, 
                                                $p
                                            ];
                                        }
                                        if( $reels['reel' . $r][$p] == '15' ) 
                                        {
                                            $WildsWalk['Rudolph'][] = [
                                                $r, 
                                                $p
                                            ];
                                        }
                                    }
                                }
                                if( count($WildsWalk['Santa']) > 1 || count($WildsWalk['Rudolph']) > 1 ) 
                                {
                                }
                                else
                                {
                                    foreach( $WildsWalk['Santa'] as $key => $wwalk ) 
                                    {
                                        $cwild = $WildsWalk['Santa'][$key];
                                        $reels['reel' . $cwild[0]][$cwild[1]] = '16';
                                        $bChecckedReels[$cwild[0]][$cwild[1]]++;
                                    }
                                    foreach( $WildsWalk['Rudolph'] as $key => $wwalk ) 
                                    {
                                        $cwild = $WildsWalk['Rudolph'][$key];
                                        $reels['reel' . $cwild[0]][$cwild[1]] = '15';
                                        $bChecckedReels[$cwild[0]][$cwild[1]]++;
                                    }
                                    $wwcnt = 0;
                                    foreach( $WildsWalk['Rudolph'] as $key => $wwalk ) 
                                    {
                                        $wwalk[0] -= 1;
                                        $walkingWildsStr .= ('&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.wildtype=NORMAL&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.row=' . $wwalk[1] . '&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.with=SYM15&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.pos=39');
                                        $wwcnt++;
                                    }
                                    foreach( $WildsWalk['Santa'] as $key => $wwalk ) 
                                    {
                                        $wwalk[0] -= 1;
                                        $walkingWildsStr .= ('&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.wildtype=NORMAL&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.row=' . $wwalk[1] . '&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.with=SYM16&rs.i0.r.i' . $wwalk[0] . '.overlay.i' . $wwcnt . '.pos=39');
                                        $wwcnt++;
                                    }
                                    $bRowStarter = 0;
                                    $bReelStarter = 0;
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p < 3; $p++ ) 
                                        {
                                            if( $bChecckedReels[$r][$p] >= 2 ) 
                                            {
                                                $isBonusStart = true;
                                                $bRowStarter = $p;
                                                $bReelStarter = $r - 1;
                                                break;
                                            }
                                        }
                                    }
                                    $winLineCount = 0;
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
                                                        $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                        $mainSymAnim = $csym;
                                                    }
                                                }
                                                if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                                {
                                                    $mpl = 1;
                                                    if( in_array($s[4], $wild) && in_array($s[3], $wild) && in_array($s[2], $wild) ) 
                                                    {
                                                        $mpl = 1;
                                                    }
                                                    else if( in_array($s[4], $wild) || in_array($s[3], $wild) || in_array($s[2], $wild) ) 
                                                    {
                                                        $mpl = $slotSettings->slotWildMpl;
                                                    }
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=4%2C' . ($linesId[$k][4] - 1) . '&ws.i' . $winLineCount . '.pos.i1=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=right_to_left&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                        $mainSymAnim = $csym;
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
                                                        $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                        $mainSymAnim = $csym;
                                                    }
                                                }
                                                if( ($s[4] == $csym || in_array($s[4], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                                {
                                                    $mpl = 1;
                                                    if( in_array($s[4], $wild) && in_array($s[3], $wild) && in_array($s[2], $wild) && in_array($s[1], $wild) ) 
                                                    {
                                                        $mpl = 1;
                                                    }
                                                    else if( in_array($s[4], $wild) || in_array($s[3], $wild) || in_array($s[2], $wild) || in_array($s[1], $wild) ) 
                                                    {
                                                        $mpl = $slotSettings->slotWildMpl;
                                                    }
                                                    $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                    if( $cWins[$k] < $tmpWin ) 
                                                    {
                                                        $cWins[$k] = $tmpWin;
                                                        $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=4%2C' . ($linesId[$k][4] - 1) . '&ws.i' . $winLineCount . '.pos.i1=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=right_to_left&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                        $mainSymAnim = $csym;
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
                                                        $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.pos.i4=4%2C' . ($linesId[$k][4] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                        $mainSymAnim = $csym;
                                                    }
                                                }
                                            }
                                        }
                                        if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                        {
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$k];
                                            $winLineCount++;
                                        }
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scPos = [];
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $scattersStr = '&ws.i0.types.i0.freespins=' . $slotSettings->slotFreeCount . '&ws.i0.reelset=basic&ws.i0.betline=null&ws.i0.types.i0.wintype=freespins&ws.i0.direction=none' . implode('', $scPos);
                                    }
                                    $totalWin += $scattersWin;
                                    if( $i > 1000 ) 
                                    {
                                        $winType = 'none';
                                    }
                                    if( $i > 1500 ) 
                                    {
                                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                        exit( $response );
                                    }
                                    if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                    {
                                    }
                                    else
                                    {
                                        if( $slotSettings->MaxWin < ($slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWin') + ($totalWin * $slotSettings->CurrentDenom)) ) 
                                        {
                                            $winType = 'none';
                                        }
                                        $minWin = $slotSettings->GetRandomPay();
                                        if( $i > 1000 ) 
                                        {
                                            $minWin = 0;
                                        }
                                        if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $betline) ) 
                                        {
                                        }
                                        else
                                        {
                                            if( $i > 900 && $totalWin <= $spinWinLimit ) 
                                            {
                                                break;
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
                            }
                            $freeState = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'WildsWalk', $WildsWalk);
                            $reportWin = $totalWin;
                            $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                            $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                            $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                            $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                            $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
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
                            $scattersCount = 0;
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == $scatter ) 
                                    {
                                        $scattersCount++;
                                    }
                                }
                            }
                            if( $isBonusStart ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=freespin&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cfreespin&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=freespin&freespins.initial=' . $fs . '&freespins.win.coins=0&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            /*$newTime = time() - $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimit0');
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit0', time());
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit', $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimit') - $newTime);
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWin', $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWin') + ($totalWin * $slotSettings->CurrentDenom));*/
                            $winString = implode('', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $winstring = '';
                            $slotSettings->SetGameData($slotSettings->slotId . 'GambleStep', 5);
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $isJack = 'false';
                            if( $totalWin > 0 ) 
                            {
                                $state = 'gamble';
                                $gameover = 'false';
                                $nextaction = 'spin';
                                $gameover = 'true';
                            }
                            else
                            {
                                $state = 'idle';
                                $gameover = 'true';
                                $nextaction = 'spin';
                            }
                            $gameover = 'true';
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $slotSettings->SetGameData($slotSettings->slotId . 'SantaScore', $SantaScore);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RudolphScore', $RudolphScore);
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && count($WildsWalk['Santa']) <= 0 && count($WildsWalk['Rudolph']) <= 0 ) 
                                {
                                    $nextaction = 'spin';
                                    $stack = 'basic';
                                    $gamestate = 'basic';
                                    $slotSettings->SetGameData($slotSettings->slotId . 'WildsWalk', [
                                        'Santa' => [], 
                                        'Rudolph' => []
                                    ]);
                                }
                                else
                                {
                                    $gamestate = 'freespin';
                                    $nextaction = 'freespin';
                                    $stack = 'basic%2Cfreespin';
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $fsl = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            if( !$isRespin && (count($WildsWalk['Santa']) > 0 || count($WildsWalk['Rudolph']) > 0) ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'IsRespin', true);
                                $walkingWildsStr .= '&nextaction=respin';
                            }
                            else if( $isRespin && (count($WildsWalk['Santa']) > 0 || count($WildsWalk['Rudolph']) > 0) ) 
                            {
                                $walkingWildsStr .= '&nextaction=respin&clientaction=respin';
                            }
                            else if( $isRespin && count($WildsWalk['Santa']) <= 0 && count($WildsWalk['Rudolph']) <= 0 ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'IsRespin', false);
                            }
                            if( $isBonusStart ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'WildsWalk', [
                                    'Santa' => [], 
                                    'Rudolph' => []
                                ]);
                                $result_tmp[0] = 'previous.rs.i0=basic_respin&freespins.betlevel=1&gameServerVersion=1.5.0&g4mode=false&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&historybutton=false&current.rs.i0=freespin&rs.i0.r.i4.hold=false&next.rs=freespin&gamestate.history=basic&rs.i0.r.i1.syms=SYM8%2CSYM10%2CSYM6&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&haswonbonusgame=false&rs.i0.id=basic_respin&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&gamestate.current=freespin&freespins.initial=10&jackpotcurrency=%26%23x20AC%3B&multiplier=1&last.rs=basic_respin&freespins.denomination=' . $slotSettings->CurrentDenomination . '&rs.i0.r.i0.syms=SYM3%2CSYM5%2CSYM11&rs.i0.r.i3.syms=SYM11%2CSYM7%2CSYM8&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=10&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&rs.i0.r.i0.pos=24&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&gamesoundurl=&cjpUrl=&rs.i0.r.i1.pos=9&game.win.coins=' . $totalWin . '&playercurrencyiso=' . $slotSettings->slotCurrency . '&featurewildcount.bonusgame=0&rs.i0.r.i1.hold=false&featurewildcount.superspins=0&freespins.wavecount=1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=respin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM12%2CSYM7%2CSYM3&rs.i0.r.i2.pos=2&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=12&freespins.left=10&rs.i0.r.i4.pos=33&nextaction=freespin&wavecount=1&rs.i0.r.i2.syms=SYM6%2CSYM5%2CSYM9&haswonsuperspins=false&rs.i0.r.i3.hold=false&game.win.amount=1.70&rs.i0.r.i' . $bReelStarter . '.overlay.i0.pos=11&rs.i0.r.i' . $bReelStarter . '.overlay.i0.with=SYM17&rs.i0.r.i' . $bReelStarter . '.overlay.i0.row=' . $bRowStarter . '&freespins.totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '' . $curReels . $winString;
                            }
                            else
                            {
                                $result_tmp[0] = 'previous.rs.i0=basic&rs.i0.r.i1.pos=102&gameServerVersion=1.0.0&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&rs.i0.r.i1.hold=false&current.rs.i0=basic&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i1.syms=SYM6%2CSYM8%2CSYM6&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM9%2CSYM0%2CSYM8&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=122&rs.i0.id=basic&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gamestate.current=basic&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=84&last.rs=basic&rs.i0.r.i4.pos=219&rs.i0.r.i0.syms=SYM10%2CSYM5%2CSYM12&rs.i0.r.i3.syms=SYM9%2CSYM11%2CSYM10&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=10&wavecount=1&gamesoundurl=&jab.collects=null&rs.i0.r.i2.syms=SYM12%2CSYM6%2CSYM7&rs.i0.r.i3.hold=false&haswonsuperspins=false&featurewildcount.bonusgame=' . $SantaScore . '&featurewildcount.superspins=' . $RudolphScore . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '&cjpUrl=' . $curReels . $winString . $walkingWildsStr;
                            }
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
