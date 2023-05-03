<?php 
namespace VanguardLTE\Games\FlowersChristmasNET
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
                        $lines = 30;
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
                            $slotSettings->SetGameData('FlowersChristmasNETBonusWin', 0);
                            $slotSettings->SetGameData('FlowersChristmasNETFreeGames', 0);
                            $slotSettings->SetGameData('FlowersChristmasNETCurrentFreeGame', 0);
                            $slotSettings->SetGameData('FlowersChristmasNETTotalWin', 0);
                            $slotSettings->SetGameData('FlowersChristmasNETFreeBalance', 0);
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
                            if( $slotSettings->GetGameData('FlowersChristmasNETCurrentFreeGame') < $slotSettings->GetGameData('FlowersChristmasNETFreeGames') && $slotSettings->GetGameData('FlowersChristmasNETFreeGames') > 0 ) 
                            {
                                $freeState = 'previous.rs.i0=freespin&rs.i1.r.i0.syms=SYM8%2CSYM9%2CSYM11&bl.i6.coins=1&bl.i17.reelset=ALL&rs.i0.nearwin=4%2C2%2C3&bl.i15.id=15&rs.i0.r.i1.attention.i0=1&rs.i0.r.i4.hold=false&gamestate.history=basic%2Cfreespin&rs.i1.r.i2.hold=false&bl.i21.id=21&game.win.cents=300&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i23.reelset=ALL&bl.i10.line=1%2C2%2C1%2C2%2C1&bl.i0.reelset=ALL&bl.i20.coins=1&bl.i18.coins=1&bl.i10.id=10&freespins.initial=10&bl.i3.reelset=ALL&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i13.coins=1&bl.i26.reelset=ALL&bl.i24.line=2%2C0%2C1%2C2%2C0&bl.i27.id=27&rs.i0.r.i0.syms=SYM8%2CSYM9%2CSYM11&bl.i2.id=2&rs.i1.r.i1.pos=68&rs.i0.r.i0.pos=66&bl.i14.reelset=ALL&game.win.coins=60&bl.i28.line=2%2C1%2C0%2C0%2C0&rs.i1.r.i0.hold=false&bl.i3.id=3&bl.i22.line=2%2C2%2C0%2C2%2C2&bl.i12.coins=1&bl.i8.reelset=ALL&clientaction=init&rs.i0.r.i2.hold=false&bl.i16.id=16&casinoID=netent&bl.i5.coins=1&bl.i8.id=8&rs.i0.r.i3.pos=77&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i22.id=22&rs.i1.r.i2.attention.i0=1&bl.i12.line=2%2C1%2C2%2C1%2C2&bl.i0.line=1%2C1%2C1%2C1%2C1&bl.i29.reelset=ALL&rs.i0.r.i2.syms=SYM15%2CSYM10%2CSYM12&game.win.amount=3.00&betlevel.all=1%2C2%2C3%2C4%2C5&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&bl.i27.coins=1&current.rs.i0=freespin&bl.i1.id=1&bl.i25.id=25&rs.i1.r.i4.pos=11&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&multiplier=3&bl.i14.id=14&bl.i19.line=0%2C2%2C2%2C2%2C0&freespins.denomination=5.000&bl.i12.reelset=ALL&bl.i2.coins=1&bl.i6.id=6&bl.i21.reelset=ALL&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&freespins.totalwin.coins=0&freespins.total=10&bl.i20.id=20&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM1%2CSYM1%2CSYM1&gamesoundurl=&bet.betlevel=1&bl.i5.reelset=ALL&bl.i24.coins=1&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&rs.i0.r.i2.attention.i0=2&bl.i14.line=1%2C1%2C2%2C1%2C1&freespins.multiplier=3&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM14%2CSYM1%2CSYM16&bl.i25.coins=1&rs.i0.r.i2.pos=10&bl.i13.line=1%2C1%2C0%2C1%2C1&bl.i24.reelset=ALL&rs.i1.r.i0.pos=45&bl.i0.coins=1&bl.i2.reelset=ALL&rs.i1.r.i4.hold=false&freespins.left=8&bl.i26.coins=1&bl.i27.reelset=ALL&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29&bl.i29.line=1%2C0%2C1%2C2%2C1&bl.i23.line=0%2C2%2C1%2C0%2C2&bl.i26.id=26&bl.i15.reelset=ALL&rs.i0.r.i3.hold=false&bet.denomination=' . ($slotSettings->CurrentDenomination * 100) . '&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&freespins.win.coins=0&historybutton=false&bl.i25.line=1%2C0%2C2%2C0%2C1&bl.i5.id=5&gameEventSetters.enabled=false&next.rs=freespin&rs.i1.r.i3.pos=26&rs.i0.r.i1.syms=SYM11%2CSYM12%2CSYM9&bl.i3.coins=1&bl.i10.coins=1&bl.i18.id=18&rs.i1.r.i3.hold=false&totalwin.coins=60&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=freespin&bl.i28.coins=1&bl.i27.line=0%2C1%2C2%2C2%2C2&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29&rs.i0.r.i3.syms=SYM1%2CSYM3%2CSYM13&rs.i1.r.i1.syms=SYM8%2CSYM9%2CSYM3&bl.i16.coins=1&freespins.win.cents=0&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&bl.i24.id=24&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29&rs.i0.r.i1.pos=9&bl.i22.coins=1&rs.i1.r.i3.syms=SYM3%2CSYM10%2CSYM9&bl.i29.coins=1&bl.i13.id=13&rs.i0.r.i1.hold=false&bl.i9.line=1%2C0%2C1%2C0%2C1&betlevel.standard=1&bl.i10.reelset=ALL&gameover=false&bl.i25.reelset=ALL&bl.i23.coins=1&bl.i11.coins=1&bl.i22.reelset=ALL&bl.i13.reelset=ALL&bl.i0.id=0&nextaction=freespin&bl.i15.line=0%2C1%2C1%2C1%2C0&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&bl.i18.line=2%2C0%2C2%2C0%2C2&freespins.totalwin.cents=0&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&bl.i11.id=11&freespins.betlevel=1&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&bl.i17.coins=1&bl.i28.id=28&bl.i19.reelset=ALL&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&rs.i0.id=basic&credit=' . $balanceInCents . '&bl.i21.line=0%2C0%2C2%2C0%2C0&bl.i1.reelset=ALL&last.rs=freespin&bl.i21.coins=1&bl.i28.reelset=ALL&bl.i1.line=0%2C0%2C0%2C0%2C0&bl.i17.id=17&rs.i1.r.i2.pos=27&bl.i16.reelset=ALL&nearwinallowed=true&bl.i8.line=1%2C0%2C0%2C0%2C1&freespins.wavecount=1&bl.i8.coins=1&bl.i23.id=23&bl.i15.coins=1&bl.i2.line=2%2C2%2C2%2C2%2C2&rs.i1.r.i2.syms=SYM16%2CSYM0%2CSYM15&totalwin.cents=300&rs.i0.r.i0.hold=false&restore=true&rs.i1.id=freespin&bl.i12.id=12&bl.i29.id=29&bl.i4.id=4&rs.i0.r.i4.pos=39&bl.i7.coins=1&bl.i6.reelset=ALL&bl.i20.line=2%2C0%2C0%2C0%2C2&bl.i20.reelset=ALL&wavecount=1&bl.i14.coins=1&rs.i1.r.i1.hold=false&bl.i26.line=1%2C2%2C0%2C2%2C1' . $curReels . $freeState;
                            }
                            $result_tmp[] = 'rs.i1.r.i0.syms=SYM6%2CSYM9%2CSYM11&bl.i6.coins=1&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&bl.i17.reelset=ALL&historybutton=false&bl.i15.id=15&bl.i25.line=1%2C0%2C2%2C0%2C1&rs.i0.r.i4.hold=false&bl.i5.id=5&gameEventSetters.enabled=false&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM9%2CSYM17%2CSYM10&bl.i3.coins=1&bl.i21.id=21&bl.i10.coins=1&bl.i18.id=18&game.win.cents=0&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i23.reelset=ALL&bl.i10.line=1%2C2%2C1%2C2%2C1&bl.i0.reelset=ALL&bl.i20.coins=1&rs.i1.r.i3.hold=false&totalwin.coins=0&bl.i18.coins=1&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=basic&bl.i10.id=10&bl.i28.coins=1&bl.i3.reelset=ALL&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i27.line=0%2C1%2C2%2C2%2C2&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&bl.i13.coins=1&bl.i26.reelset=ALL&bl.i24.line=2%2C0%2C1%2C2%2C0&bl.i27.id=27&rs.i0.r.i0.syms=SYM6%2CSYM8%2CSYM10&rs.i0.r.i3.syms=SYM8%2CSYM6%2CSYM5&rs.i1.r.i1.syms=SYM9%2CSYM17%2CSYM10&bl.i2.id=2&bl.i16.coins=1&rs.i1.r.i1.pos=0&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&rs.i0.r.i0.pos=0&bl.i14.reelset=ALL&bl.i24.id=24&rs.i0.r.i1.pos=0&bl.i22.coins=1&rs.i1.r.i3.syms=SYM8%2CSYM6%2CSYM5&game.win.coins=0&bl.i29.coins=1&bl.i13.id=13&bl.i28.line=2%2C1%2C0%2C0%2C0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&bl.i3.id=3&bl.i22.line=2%2C2%2C0%2C2%2C2&bl.i12.coins=1&bl.i8.reelset=ALL&clientaction=init&bl.i9.line=1%2C0%2C1%2C0%2C1&rs.i0.r.i2.hold=false&bl.i16.id=16&casinoID=netent&betlevel.standard=1&bl.i5.coins=1&bl.i10.reelset=ALL&gameover=true&bl.i25.reelset=ALL&bl.i8.id=8&bl.i23.coins=1&rs.i0.r.i3.pos=0&bl.i11.coins=1&bl.i22.reelset=ALL&bl.i13.reelset=ALL&bl.i0.id=0&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i22.id=22&bl.i12.line=2%2C1%2C2%2C1%2C2&bl.i0.line=1%2C1%2C1%2C1%2C1&nextaction=spin&bl.i15.line=0%2C1%2C1%2C1%2C0&bl.i29.reelset=ALL&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&rs.i0.r.i2.syms=SYM8%2CSYM13%2CSYM11&bl.i18.line=2%2C0%2C2%2C0%2C2&game.win.amount=0&betlevel.all=1%2C2%2C3%2C4%2C5&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&bl.i11.id=11&playercurrency=%26%23x20AC%3B&bl.i27.coins=1&bl.i9.reelset=ALL&bl.i17.coins=1&bl.i28.id=28&bl.i1.id=1&bl.i19.reelset=ALL&bl.i25.id=25&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&rs.i0.id=freespin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=0&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&bl.i21.line=0%2C0%2C2%2C0%2C0&bl.i1.reelset=ALL&multiplier=1&bl.i14.id=14&bl.i19.line=0%2C2%2C2%2C2%2C0&bl.i21.coins=1&bl.i28.reelset=ALL&bl.i12.reelset=ALL&bl.i2.coins=1&bl.i6.id=6&bl.i1.line=0%2C0%2C0%2C0%2C0&bl.i21.reelset=ALL&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&bl.i20.id=20&rs.i1.r.i4.syms=SYM1%2CSYM3%2CSYM14&bl.i17.id=17&gamesoundurl=&rs.i1.r.i2.pos=0&bl.i16.reelset=ALL&nearwinallowed=true&bl.i5.reelset=ALL&bl.i24.coins=1&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&bl.i8.line=1%2C0%2C0%2C0%2C1&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&bl.i14.line=1%2C1%2C2%2C1%2C1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM1%2CSYM1%2CSYM1&bl.i8.coins=1&bl.i23.id=23&bl.i15.coins=1&bl.i25.coins=1&rs.i0.r.i2.pos=0&bl.i2.line=2%2C2%2C2%2C2%2C2&bl.i13.line=1%2C1%2C0%2C1%2C1&rs.i1.r.i2.syms=SYM8%2CSYM13%2CSYM10&bl.i24.reelset=ALL&rs.i1.r.i0.pos=0&totalwin.cents=0&bl.i0.coins=1&bl.i2.reelset=ALL&rs.i0.r.i0.hold=false&restore=false&rs.i1.id=basic&bl.i12.id=12&bl.i29.id=29&rs.i1.r.i4.hold=false&bl.i4.id=4&rs.i0.r.i4.pos=0&bl.i7.coins=1&bl.i26.coins=1&bl.i27.reelset=ALL&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29&bl.i29.line=1%2C0%2C1%2C2%2C1&bl.i6.reelset=ALL&bl.i20.line=2%2C0%2C0%2C0%2C2&bl.i23.line=0%2C2%2C1%2C0%2C2&bl.i20.reelset=ALL&bl.i26.id=26&wavecount=1&bl.i14.coins=1&bl.i15.reelset=ALL&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bl.i26.line=1%2C2%2C0%2C2%2C1' . $curReels . $freeState;
                            break;
                        case 'paytable':
                            $result_tmp[] = 'pt.i0.comp.i46.n=3&pt.i0.comp.i54.symbol=SYM11&bl.i17.reelset=ALL&pt.i1.comp.i47.multi=20&pt.i0.comp.i55.multi=2&bl.i15.id=15&pt.i0.comp.i29.type=betline&pt.i0.comp.i17.symbol=SYM4&pt.i0.comp.i5.freespins=0&pt.i0.comp.i23.n=7&pt.i1.comp.i34.multi=1400&pt.i0.comp.i13.symbol=SYM4&pt.i1.comp.i8.type=betline&pt.i1.comp.i4.n=4&pt.i0.comp.i15.multi=350&bl.i10.line=1%2C2%2C1%2C2%2C1&pt.i1.comp.i27.symbol=SYM6&pt.i1.comp.i60.n=8&pt.i0.comp.i28.multi=25&pt.i1.comp.i43.freespins=0&bl.i18.coins=1&pt.i1.comp.i29.freespins=0&pt.i1.comp.i30.symbol=SYM6&pt.i1.comp.i3.multi=20&pt.i0.comp.i11.n=3&pt.i0.comp.i57.n=5&pt.i1.comp.i23.symbol=SYM5&bl.i4.line=2%2C1%2C0%2C1%2C2&bl.i13.coins=1&bl.i27.id=27&pt.i0.id=basic&pt.i0.comp.i1.type=betline&pt.i1.comp.i60.symbol=SYM0&bl.i2.id=2&pt.i0.comp.i58.type=scatter&pt.i0.comp.i34.n=10&pt.i1.comp.i10.type=betline&pt.i0.comp.i42.multi=1200&pt.i0.comp.i34.type=betline&pt.i0.comp.i4.symbol=SYM3&pt.i1.comp.i5.freespins=0&pt.i1.comp.i8.symbol=SYM3&bl.i14.reelset=ALL&pt.i1.comp.i19.n=3&pt.i1.comp.i52.freespins=0&pt.i0.comp.i17.freespins=0&pt.i0.comp.i50.symbol=SYM10&pt.i0.comp.i8.symbol=SYM3&pt.i0.comp.i58.symbol=SYM0&pt.i0.comp.i0.symbol=SYM1&pt.i0.comp.i47.symbol=SYM9&pt.i1.comp.i36.freespins=0&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=2000&pt.i0.comp.i47.n=4&pt.i1.id=freespin&bl.i3.id=3&bl.i22.line=2%2C2%2C0%2C2%2C2&pt.i1.comp.i34.freespins=0&pt.i1.comp.i34.type=betline&pt.i0.comp.i24.n=8&bl.i8.reelset=ALL&clientaction=paytable&pt.i1.comp.i57.symbol=SYM0&pt.i1.comp.i27.freespins=0&bl.i16.id=16&pt.i0.comp.i50.multi=15&pt.i1.comp.i5.n=5&bl.i5.coins=1&pt.i1.comp.i8.multi=600&pt.i1.comp.i51.type=betline&pt.i1.comp.i42.multi=1200&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=0&pt.i0.comp.i58.n=6&pt.i1.comp.i38.type=betline&pt.i1.comp.i60.type=scatter&pt.i0.comp.i21.multi=120&pt.i1.comp.i13.multi=140&pt.i1.comp.i54.freespins=0&pt.i1.comp.i41.freespins=0&pt.i0.comp.i12.n=4&pt.i0.comp.i35.n=3&pt.i0.comp.i13.type=betline&bl.i0.line=1%2C1%2C1%2C1%2C1&pt.i1.comp.i47.freespins=0&pt.i1.comp.i53.multi=15&pt.i1.comp.i7.freespins=0&pt.i0.comp.i31.freespins=0&pt.i0.comp.i3.multi=20&pt.i0.comp.i51.type=betline&pt.i1.comp.i50.n=4&pt.i1.comp.i22.type=betline&pt.i0.comp.i21.n=5&pt.i0.comp.i42.freespins=0&pt.i1.comp.i6.n=6&pt.i0.comp.i36.symbol=SYM7&pt.i0.comp.i39.symbol=SYM7&pt.i1.comp.i31.type=betline&pt.i1.comp.i50.multi=15&bl.i1.id=1&pt.i0.comp.i44.n=4&pt.i0.comp.i37.type=betline&pt.i0.comp.i10.type=betline&pt.i0.comp.i55.type=scatter&pt.i0.comp.i35.freespins=0&pt.i1.comp.i11.symbol=SYM4&pt.i1.comp.i49.symbol=SYM10&bl.i25.id=25&pt.i1.comp.i46.symbol=SYM9&pt.i1.comp.i46.type=betline&pt.i0.comp.i5.multi=160&pt.i0.comp.i32.n=8&pt.i0.comp.i56.freespins=10&pt.i1.comp.i1.freespins=0&bl.i14.id=14&pt.i1.comp.i16.symbol=SYM4&pt.i1.comp.i23.multi=300&pt.i1.comp.i4.type=betline&pt.i1.comp.i18.multi=1800&bl.i2.coins=1&bl.i21.reelset=ALL&pt.i0.comp.i55.n=3&pt.i1.comp.i26.type=betline&pt.i0.comp.i57.multi=2&pt.i0.comp.i8.multi=600&pt.i0.comp.i34.multi=1400&pt.i0.comp.i49.freespins=0&pt.i1.comp.i51.n=5&pt.i0.comp.i1.freespins=0&bl.i5.reelset=ALL&bl.i24.coins=1&pt.i1.comp.i49.freespins=0&pt.i0.comp.i22.n=6&pt.i0.comp.i28.symbol=SYM6&pt.i0.comp.i45.n=5&pt.i1.comp.i17.type=betline&pt.i1.comp.i0.symbol=SYM1&pt.i1.comp.i7.n=7&pt.i1.comp.i5.multi=160&pt.i1.comp.i39.multi=200&bl.i14.line=1%2C1%2C2%2C1%2C1&pt.i0.comp.i21.type=betline&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=350&pt.i0.comp.i13.multi=140&pt.i1.comp.i45.multi=200&pt.i0.comp.i17.type=betline&bl.i13.line=1%2C1%2C0%2C1%2C1&pt.i0.comp.i30.type=betline&pt.i1.comp.i22.symbol=SYM5&pt.i1.comp.i30.freespins=0&pt.i1.comp.i40.n=8&bl.i24.reelset=ALL&pt.i1.comp.i38.symbol=SYM7&pt.i0.comp.i40.multi=400&pt.i1.comp.i56.freespins=10&bl.i0.coins=1&bl.i2.reelset=ALL&pt.i0.comp.i10.n=10&pt.i0.comp.i33.n=9&pt.i0.comp.i56.n=4&pt.i1.comp.i41.symbol=SYM7&pt.i1.comp.i6.multi=250&pt.i0.comp.i36.multi=20&pt.i1.comp.i19.symbol=SYM5&pt.i0.comp.i22.freespins=0&pt.i1.comp.i52.symbol=SYM11&bl.i26.coins=1&bl.i27.reelset=ALL&pt.i0.comp.i20.symbol=SYM5&pt.i1.comp.i55.type=scatter&bl.i29.line=1%2C0%2C1%2C2%2C1&pt.i0.comp.i15.freespins=0&pt.i0.comp.i31.symbol=SYM6&bl.i23.line=0%2C2%2C1%2C0%2C2&bl.i26.id=26&pt.i0.comp.i28.freespins=0&pt.i0.comp.i0.n=3&pt.i1.comp.i21.multi=120&pt.i1.comp.i52.n=3&pt.i0.comp.i42.symbol=SYM7&pt.i1.comp.i30.type=betline&pt.i1.comp.i50.freespins=0&pt.i0.comp.i46.type=betline&pt.i0.comp.i0.type=betline&pt.i0.comp.i53.symbol=SYM11&pt.i1.comp.i0.multi=250&g4mode=false&pt.i1.comp.i8.n=8&pt.i0.comp.i25.multi=800&pt.i1.comp.i37.multi=80&pt.i0.comp.i38.freespins=0&bl.i25.line=1%2C0%2C2%2C0%2C1&pt.i0.comp.i16.symbol=SYM4&pt.i1.comp.i21.freespins=0&pt.i0.comp.i1.multi=1000&pt.i0.comp.i27.n=3&pt.i0.comp.i53.freespins=0&pt.i1.comp.i9.type=betline&pt.i0.comp.i32.multi=450&pt.i1.comp.i24.multi=500&pt.i1.comp.i44.multi=20&pt.i1.comp.i59.freespins=25&pt.i1.comp.i23.type=betline&pt.i1.comp.i26.n=10&pt.i1.comp.i49.n=3&bl.i18.id=18&pt.i1.comp.i28.symbol=SYM6&pt.i1.comp.i17.multi=900&pt.i0.comp.i18.multi=1800&pt.i0.comp.i33.type=betline&bl.i5.line=0%2C0%2C1%2C0%2C0&bl.i28.coins=1&pt.i1.comp.i33.symbol=SYM6&pt.i1.comp.i35.type=betline&pt.i0.comp.i9.n=9&bl.i27.line=0%2C1%2C2%2C2%2C2&pt.i1.comp.i21.type=betline&bl.i7.line=1%2C2%2C2%2C2%2C1&pt.i0.comp.i28.type=betline&pt.i1.comp.i31.multi=250&pt.i1.comp.i18.type=betline&pt.i1.comp.i58.freespins=20&pt.i0.comp.i10.symbol=SYM3&pt.i0.comp.i38.n=6&pt.i0.comp.i45.type=betline&pt.i0.comp.i15.n=7&pt.i0.comp.i39.freespins=0&pt.i0.comp.i21.symbol=SYM5&bl.i7.reelset=ALL&pt.i0.comp.i31.type=betline&pt.i1.comp.i15.n=7&pt.i1.comp.i38.n=6&isJackpotWin=false&pt.i1.comp.i20.freespins=0&pt.i0.comp.i52.freespins=0&pt.i1.comp.i7.type=betline&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=30&pt.i0.comp.i44.symbol=SYM8&pt.i0.comp.i17.multi=900&pt.i1.comp.i56.type=scatter&bl.i29.coins=1&pt.i1.comp.i25.type=betline&pt.i1.comp.i9.n=9&pt.i0.comp.i28.n=4&bl.i9.line=1%2C0%2C1%2C0%2C1&pt.i1.comp.i39.symbol=SYM7&pt.i0.comp.i2.multi=5000&pt.i1.comp.i27.n=3&pt.i0.comp.i0.freespins=0&pt.i1.comp.i25.multi=800&pt.i0.comp.i33.multi=700&pt.i1.comp.i16.freespins=0&pt.i0.comp.i51.freespins=0&pt.i1.comp.i5.type=betline&pt.i1.comp.i35.symbol=SYM7&bl.i25.reelset=ALL&pt.i1.comp.i24.symbol=SYM5&pt.i0.comp.i37.freespins=0&pt.i1.comp.i50.symbol=SYM10&pt.i1.comp.i13.symbol=SYM4&pt.i1.comp.i17.symbol=SYM4&pt.i0.comp.i54.freespins=0&pt.i0.comp.i16.n=8&pt.i0.comp.i39.n=7&bl.i13.reelset=ALL&bl.i0.id=0&pt.i1.comp.i16.n=8&pt.i0.comp.i5.symbol=SYM3&bl.i15.line=0%2C1%2C1%2C1%2C0&pt.i1.comp.i7.symbol=SYM3&pt.i1.comp.i39.n=7&bl.i19.id=19&pt.i0.comp.i38.type=betline&pt.i0.comp.i35.type=betline&pt.i0.comp.i48.symbol=SYM9&pt.i1.comp.i57.freespins=15&pt.i0.comp.i1.symbol=SYM1&pt.i0.comp.i59.symbol=SYM0&pt.i0.comp.i55.symbol=SYM0&pt.i1.comp.i36.multi=20&pt.i1.comp.i31.freespins=0&bl.i9.id=9&bl.i17.line=0%2C2%2C0%2C2%2C0&pt.i1.comp.i9.freespins=0&pt.i0.comp.i48.n=5&playercurrency=%26%23x20AC%3B&pt.i0.comp.i38.symbol=SYM7&pt.i0.comp.i33.symbol=SYM6&pt.i1.comp.i40.multi=400&bl.i28.id=28&pt.i1.comp.i30.multi=175&bl.i19.reelset=ALL&pt.i0.comp.i25.n=9&pt.i1.comp.i58.type=scatter&pt.i1.comp.i28.n=4&pt.i1.comp.i32.freespins=0&pt.i0.comp.i9.freespins=0&pt.i1.comp.i45.symbol=SYM8&credit=500000&pt.i0.comp.i5.type=betline&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=1600&pt.i0.comp.i25.type=betline&pt.i0.comp.i59.n=7&bl.i1.reelset=ALL&pt.i1.comp.i40.symbol=SYM7&pt.i1.comp.i18.symbol=SYM4&pt.i0.comp.i31.multi=250&pt.i1.comp.i12.symbol=SYM4&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i0.comp.i26.freespins=0&pt.i0.comp.i53.type=betline&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=1000&pt.i1.comp.i51.symbol=SYM10&pt.i0.comp.i36.n=4&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=5&pt.i1.comp.i46.freespins=0&pt.i1.comp.i33.freespins=0&pt.i1.comp.i17.n=9&pt.i0.comp.i23.type=betline&pt.i0.comp.i32.symbol=SYM6&bl.i17.id=17&pt.i0.comp.i43.symbol=SYM8&pt.i1.comp.i17.freespins=0&pt.i1.comp.i26.multi=1600&pt.i0.comp.i43.type=betline&pt.i1.comp.i32.multi=450&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM1&pt.i1.comp.i29.multi=100&pt.i0.comp.i25.freespins=0&pt.i0.comp.i49.n=3&pt.i0.comp.i60.symbol=SYM0&pt.i0.comp.i40.freespins=0&pt.i0.comp.i26.n=10&pt.i0.comp.i27.symbol=SYM6&pt.i1.comp.i56.symbol=SYM0&pt.i1.comp.i45.freespins=0&pt.i1.comp.i29.n=5&pt.i0.comp.i23.multi=300&bl.i2.line=2%2C2%2C2%2C2%2C2&pt.i0.comp.i30.multi=175&pt.i1.comp.i34.symbol=SYM6&pt.i1.comp.i43.type=betline&pt.i1.comp.i60.freespins=30&pt.i1.comp.i28.multi=25&bl.i29.id=29&pt.i1.comp.i33.multi=700&pt.i1.comp.i18.freespins=0&pt.i0.comp.i14.n=6&pt.i0.comp.i37.n=5&pt.i0.comp.i0.multi=250&bl.i6.reelset=ALL&pt.i0.comp.i19.multi=15&bl.i20.line=2%2C0%2C0%2C0%2C2&pt.i1.comp.i18.n=10&pt.i1.comp.i33.type=betline&bl.i20.reelset=ALL&pt.i0.comp.i12.freespins=0&pt.i0.comp.i24.multi=500&pt.i1.comp.i53.type=betline&pt.i0.comp.i19.symbol=SYM5&bl.i6.coins=1&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i0.comp.i32.type=betline&pt.i0.comp.i35.multi=10&pt.i1.comp.i36.type=betline&pt.i0.comp.i4.multi=40&pt.i0.comp.i15.symbol=SYM4&pt.i1.comp.i14.multi=225&pt.i0.comp.i22.multi=200&pt.i1.comp.i54.multi=100&pt.i1.comp.i51.freespins=0&bl.i21.id=21&pt.i1.comp.i19.type=betline&pt.i0.comp.i11.symbol=SYM4&pt.i0.comp.i48.multi=150&pt.i1.comp.i27.multi=10&bl.i23.reelset=ALL&bl.i0.reelset=ALL&bl.i20.coins=1&pt.i0.comp.i16.freespins=0&pt.i1.comp.i6.freespins=0&pt.i1.comp.i29.symbol=SYM6&pt.i1.comp.i22.n=6&pt.i1.comp.i45.n=5&bl.i10.id=10&pt.i0.comp.i4.freespins=0&pt.i1.comp.i25.symbol=SYM5&bl.i3.reelset=ALL&pt.i0.comp.i30.freespins=0&bl.i26.reelset=ALL&bl.i24.line=2%2C0%2C1%2C2%2C0&pt.i1.comp.i24.type=betline&pt.i0.comp.i19.n=3&pt.i1.comp.i57.n=5&pt.i0.comp.i2.symbol=SYM1&pt.i0.comp.i20.type=betline&pt.i1.comp.i48.type=betline&pt.i0.comp.i49.symbol=SYM10&pt.i0.comp.i6.symbol=SYM3&pt.i0.comp.i56.symbol=SYM0&pt.i0.comp.i52.symbol=SYM11&pt.i1.comp.i11.n=3&pt.i1.comp.i34.n=10&pt.i0.comp.i5.n=5&pt.i1.comp.i2.symbol=SYM1&pt.i0.comp.i3.type=betline&pt.i1.comp.i19.multi=15&bl.i28.line=2%2C1%2C0%2C0%2C0&pt.i1.comp.i6.symbol=SYM3&pt.i0.comp.i27.multi=10&pt.i1.comp.i59.multi=4&pt.i0.comp.i9.multi=1000&bl.i12.coins=1&pt.i0.comp.i22.symbol=SYM5&pt.i0.comp.i26.symbol=SYM5&pt.i1.comp.i19.freespins=0&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&pt.i1.comp.i35.multi=10&pt.i1.comp.i46.n=3&pt.i1.comp.i4.freespins=0&pt.i0.comp.i44.type=betline&pt.i0.comp.i43.multi=5&pt.i0.comp.i48.type=betline&pt.i1.comp.i12.type=betline&pt.i1.comp.i57.type=scatter&pt.i1.comp.i36.symbol=SYM7&pt.i1.comp.i21.symbol=SYM5&pt.i1.comp.i23.n=7&pt.i1.comp.i32.symbol=SYM6&bl.i8.id=8&pt.i0.comp.i16.multi=550&pt.i1.comp.i48.multi=150&pt.i1.comp.i37.freespins=0&pt.i1.comp.i43.symbol=SYM8&pt.i1.comp.i41.multi=600&pt.i0.comp.i56.multi=2&pt.i0.comp.i50.n=4&pt.i0.comp.i41.freespins=0&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i22.id=22&pt.i1.comp.i35.n=3&pt.i1.comp.i41.type=betline&bl.i12.line=2%2C1%2C2%2C1%2C2&pt.i1.comp.i9.multi=1000&pt.i1.comp.i58.n=6&bl.i29.reelset=ALL&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=5000&pt.i1.comp.i44.freespins=0&pt.i0.comp.i6.n=6&pt.i1.comp.i12.n=4&pt.i1.comp.i3.type=betline&pt.i1.comp.i10.freespins=0&pt.i1.comp.i55.freespins=0&pt.i0.comp.i57.freespins=15&pt.i1.comp.i28.type=betline&bl.i27.coins=1&pt.i0.comp.i34.symbol=SYM6&pt.i0.comp.i40.type=betline&pt.i1.comp.i45.type=betline&pt.i0.comp.i37.symbol=SYM7&pt.i0.comp.i29.n=5&pt.i1.comp.i20.multi=30&pt.i0.comp.i27.freespins=0&pt.i0.comp.i34.freespins=0&pt.i1.comp.i24.n=8&pt.i1.comp.i47.n=4&pt.i1.comp.i47.symbol=SYM9&pt.i1.comp.i27.type=betline&pt.i1.comp.i48.freespins=0&pt.i1.comp.i2.type=betline&pt.i0.comp.i41.type=betline&pt.i0.comp.i2.freespins=0&pt.i1.comp.i38.multi=150&pt.i0.comp.i7.n=7&pt.i0.comp.i43.freespins=0&pt.i0.comp.i11.multi=15&pt.i0.comp.i36.type=betline&pt.i1.comp.i14.symbol=SYM4&pt.i0.comp.i56.type=scatter&pt.i1.comp.i44.symbol=SYM8&pt.i0.comp.i7.type=betline&pt.i1.comp.i43.multi=5&bl.i19.line=0%2C2%2C2%2C2%2C0&bl.i12.reelset=ALL&pt.i0.comp.i17.n=9&bl.i6.id=6&pt.i1.comp.i55.symbol=SYM0&pt.i0.comp.i29.multi=100&pt.i1.comp.i13.n=5&pt.i1.comp.i36.n=4&pt.i1.comp.i59.n=7&pt.i0.comp.i8.freespins=0&bl.i20.id=20&pt.i1.comp.i4.multi=40&gamesoundurl=&pt.i1.comp.i46.multi=5&pt.i0.comp.i12.type=betline&pt.i0.comp.i36.freespins=0&pt.i0.comp.i14.multi=225&pt.i1.comp.i7.multi=400&pt.i0.comp.i45.symbol=SYM8&bl.i19.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=250&pt.i0.comp.i55.freespins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&pt.i1.comp.i42.freespins=0&pt.i0.comp.i37.multi=80&pt.i0.comp.i60.n=8&pt.i1.comp.i5.symbol=SYM3&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM5&playforfun=false&pt.i1.comp.i25.n=9&pt.i1.comp.i48.n=5&pt.i0.comp.i48.freespins=0&pt.i0.comp.i2.type=betline&pt.i1.comp.i20.type=betline&bl.i25.coins=1&pt.i1.comp.i22.multi=200&pt.i0.comp.i8.n=8&pt.i1.comp.i22.freespins=0&pt.i0.comp.i11.type=betline&pt.i1.comp.i35.freespins=0&pt.i0.comp.i18.n=10&pt.i1.comp.i14.n=6&pt.i1.comp.i16.multi=550&pt.i1.comp.i37.n=5&pt.i1.comp.i15.freespins=0&pt.i0.comp.i27.type=betline&pt.i0.comp.i41.multi=600&pt.i1.comp.i28.freespins=0&pt.i0.comp.i7.symbol=SYM3&pt.i0.comp.i59.multi=4&bl.i15.reelset=ALL&pt.i0.comp.i50.freespins=0&pt.i1.comp.i0.freespins=0&pt.i0.comp.i45.multi=200&pt.i1.comp.i57.multi=2&bl.i11.line=0%2C1%2C0%2C1%2C0&historybutton=false&bl.i5.id=5&pt.i0.comp.i18.symbol=SYM4&pt.i0.comp.i42.n=10&pt.i0.comp.i46.freespins=0&pt.i0.comp.i12.multi=35&pt.i1.comp.i14.freespins=0&bl.i3.coins=1&bl.i10.coins=1&pt.i0.comp.i12.symbol=SYM4&pt.i0.comp.i14.symbol=SYM4&pt.i0.comp.i38.multi=150&pt.i0.comp.i58.multi=2&pt.i1.comp.i13.freespins=0&pt.i0.comp.i45.freespins=0&pt.i0.comp.i59.type=scatter&pt.i1.comp.i40.type=betline&pt.i0.comp.i14.type=betline&pt.i1.comp.i41.n=9&pt.i1.comp.i54.type=betline&pt.i1.comp.i0.n=3&pt.i1.comp.i26.symbol=SYM5&pt.i1.comp.i31.symbol=SYM6&pt.i0.comp.i7.multi=400&pt.i1.comp.i51.multi=125&pt.i0.comp.i30.n=6&jackpotcurrency=%26%23x20AC%3B&pt.i0.comp.i47.type=betline&pt.i0.comp.i50.type=betline&pt.i0.comp.i53.n=4&bl.i16.coins=1&bl.i9.coins=1&pt.i1.comp.i37.type=betline&bl.i24.id=24&pt.i1.comp.i11.multi=15&pt.i1.comp.i30.n=6&pt.i0.comp.i1.n=4&pt.i1.comp.i53.n=4&bl.i22.coins=1&pt.i0.comp.i20.n=4&pt.i0.comp.i29.symbol=SYM6&pt.i1.comp.i3.symbol=SYM3&pt.i1.comp.i50.type=betline&pt.i0.comp.i57.type=scatter&pt.i1.comp.i23.freespins=0&bl.i13.id=13&pt.i0.comp.i25.symbol=SYM5&pt.i0.comp.i26.type=betline&pt.i1.comp.i49.multi=5&pt.i0.comp.i9.type=betline&pt.i1.comp.i58.symbol=SYM0&pt.i0.comp.i43.n=3&pt.i1.comp.i47.type=betline&pt.i1.comp.i16.type=betline&pt.i0.comp.i60.type=scatter&pt.i0.comp.i60.multi=10&pt.i1.comp.i20.symbol=SYM5&bl.i10.reelset=ALL&pt.i1.comp.i12.multi=35&pt.i0.comp.i29.freespins=0&pt.i1.comp.i1.n=4&pt.i1.comp.i42.n=10&pt.i1.comp.i52.multi=5&pt.i1.comp.i11.freespins=0&pt.i0.comp.i31.n=7&pt.i0.comp.i9.symbol=SYM3&bl.i23.coins=1&bl.i11.coins=1&pt.i1.comp.i54.symbol=SYM11&bl.i22.reelset=ALL&pt.i0.comp.i54.n=5&pt.i0.comp.i47.freespins=0&pt.i1.comp.i44.type=betline&pt.i0.comp.i16.type=betline&pt.i0.comp.i39.multi=200&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i4.reelset=ALL&bl.i4.coins=1&pt.i0.comp.i2.n=5&pt.i0.comp.i40.symbol=SYM7&bl.i18.line=2%2C0%2C2%2C0%2C2&pt.i0.comp.i44.freespins=0&pt.i0.comp.i51.symbol=SYM10&pt.i1.comp.i31.n=7&pt.i0.comp.i44.multi=20&pt.i0.comp.i54.type=betline&pt.i1.comp.i54.n=5&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&bl.i11.id=11&pt.i0.comp.i6.type=betline&pt.i1.comp.i2.freespins=0&pt.i0.comp.i35.symbol=SYM7&pt.i1.comp.i25.freespins=0&bl.i9.reelset=ALL&bl.i17.coins=1&pt.i0.comp.i40.n=8&pt.i1.comp.i40.freespins=0&pt.i1.comp.i60.multi=10&pt.i1.comp.i10.multi=2000&pt.i1.comp.i10.symbol=SYM3&pt.i1.comp.i48.symbol=SYM9&bl.i11.reelset=ALL&bl.i16.line=2%2C1%2C1%2C1%2C2&pt.i1.comp.i2.n=5&pt.i1.comp.i20.n=4&pt.i1.comp.i43.n=3&pt.i1.comp.i24.freespins=0&bl.i21.line=0%2C0%2C2%2C0%2C0&pt.i1.comp.i32.type=betline&pt.i0.comp.i39.type=betline&pt.i1.comp.i42.symbol=SYM7&pt.i1.comp.i39.freespins=0&pt.i1.comp.i53.symbol=SYM11&pt.i0.comp.i4.type=betline&pt.i0.comp.i58.freespins=20&bl.i21.coins=1&bl.i28.reelset=ALL&pt.i1.comp.i26.freespins=0&pt.i0.comp.i51.n=5&pt.i1.comp.i1.type=betline&pt.i1.comp.i58.multi=2&pt.i0.comp.i46.multi=5&bl.i1.line=0%2C0%2C0%2C0%2C0&pt.i0.comp.i42.type=betline&pt.i0.comp.i20.freespins=0&pt.i0.comp.i33.freespins=0&pt.i0.comp.i51.multi=125&pt.i1.comp.i29.type=betline&pt.i0.comp.i30.symbol=SYM6&bl.i16.reelset=ALL&pt.i0.comp.i41.symbol=SYM7&pt.i0.comp.i49.multi=5&pt.i0.comp.i54.multi=100&pt.i1.comp.i32.n=8&pt.i1.comp.i55.n=3&pt.i0.comp.i3.n=3&pt.i1.comp.i59.type=scatter&pt.i1.comp.i6.type=betline&pt.i0.comp.i46.symbol=SYM9&pt.i0.comp.i49.type=betline&pt.i1.comp.i4.symbol=SYM3&pt.i1.comp.i38.freespins=0&bl.i8.line=1%2C0%2C0%2C0%2C1&pt.i1.comp.i39.type=betline&pt.i0.comp.i24.symbol=SYM5&pt.i1.comp.i53.freespins=0&pt.i0.comp.i47.multi=20&pt.i0.comp.i41.n=9&pt.i1.comp.i42.type=betline&pt.i1.comp.i59.symbol=SYM0&pt.i0.comp.i59.freespins=25&pt.i1.comp.i55.multi=2&bl.i8.coins=1&pt.i0.comp.i32.freespins=0&bl.i23.id=23&bl.i15.coins=1&pt.i0.comp.i52.type=betline&pt.i0.comp.i53.multi=15&pt.i1.comp.i37.symbol=SYM7&pt.i1.comp.i3.n=3&pt.i1.comp.i21.n=5&pt.i1.comp.i44.n=4&pt.i0.comp.i18.freespins=0&bl.i12.id=12&pt.i1.comp.i15.symbol=SYM4&pt.i1.comp.i49.type=betline&pt.i1.comp.i3.freespins=0&bl.i4.id=4&bl.i7.coins=1&pt.i1.comp.i52.type=betline&pt.i0.comp.i52.n=3&pt.i0.comp.i60.freespins=30&pt.i0.comp.i52.multi=5&pt.i1.comp.i9.symbol=SYM3&pt.i0.comp.i3.symbol=SYM3&pt.i0.comp.i24.type=betline&bl.i14.coins=1&pt.i0.comp.i57.symbol=SYM0&pt.i1.comp.i12.freespins=0&pt.i0.comp.i4.n=4&pt.i1.comp.i10.n=10&pt.i1.comp.i56.n=4&bl.i26.line=1%2C2%2C0%2C2%2C1&pt.i1.comp.i56.multi=2&pt.i1.comp.i33.n=9';
                        case 'initfreespin':
                            $result_tmp[] = 'rs.i1.r.i0.syms=SYM5%2CSYM0%2CSYM6&freespins.betlevel=1&g4mode=false&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i4.hold=false&gamestate.history=basic&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=18&rs.i0.r.i1.syms=SYM5%2CSYM5%2CSYM7&game.win.cents=0&rs.i0.id=freespin&rs.i1.r.i3.hold=false&totalwin.coins=0&credit=497520&rs.i1.r.i4.pos=30&gamestate.current=freespin&freespins.initial=15&jackpotcurrency=%26%23x20AC%3B&multiplier=1&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&rs.i0.r.i0.syms=SYM2%2CSYM7%2CSYM7&freespins.denomination=2.000&rs.i0.r.i3.syms=SYM4%2CSYM4%2CSYM4&rs.i1.r.i1.syms=SYM2%2CSYM3%2CSYM3&rs.i1.r.i1.pos=3&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=15&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&rs.i0.r.i0.pos=3&rs.i1.r.i4.syms=SYM1%2CSYM7%2CSYM7&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&gamesoundurl=&rs.i1.r.i2.pos=15&bet.betlevel=1&rs.i1.nearwin=4%2C3&rs.i0.r.i1.pos=18&rs.i1.r.i3.syms=SYM4%2CSYM0%2CSYM6&game.win.coins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&freespins.wavecount=1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . "&clientaction=initfreespin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM6%2CSYM5%2CSYM5&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM6%2CSYM6%2CSYM0&rs.i1.r.i0.pos=24&totalwin.cents=0&gameover=false&rs.i0.r.i0.hold=false&rs.i1.id=basic&rs.i0.r.i3.pos=3&rs.i1.r.i4.hold=false&freespins.left=15&rs.i0.r.i4.pos=20&rs.i1.r.i2.attention.i0=2&rs.i1.r.i0.attention.i0=1&rs.i1.r.i3.attention.i0=1&nextaction=freespin&wavecount=1&rs.i0.r.i2.syms=SYM3%2CSYM3%2CSYM3&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&game.win.amount=0.00&bet.denomination=2&freespins.totalwin.cents=0\n";
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
                            $linesId[20] = [
                                3, 
                                1, 
                                1, 
                                1, 
                                3
                            ];
                            $linesId[21] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[22] = [
                                3, 
                                3, 
                                1, 
                                3, 
                                3
                            ];
                            $linesId[23] = [
                                1, 
                                3, 
                                2, 
                                1, 
                                3
                            ];
                            $linesId[24] = [
                                3, 
                                1, 
                                2, 
                                3, 
                                1
                            ];
                            $linesId[25] = [
                                2, 
                                1, 
                                3, 
                                1, 
                                2
                            ];
                            $linesId[26] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            $linesId[27] = [
                                1, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[28] = [
                                3, 
                                2, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[29] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $lines = 30;
                            $slotSettings->CurrentDenom = $postData['bet_denomination'];
                            $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
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
                                $slotSettings->SetGameData('FlowersChristmasNETBonusWin', 0);
                                $slotSettings->SetGameData('FlowersChristmasNETFreeGames', 0);
                                $slotSettings->SetGameData('FlowersChristmasNETCurrentFreeGame', 0);
                                $slotSettings->SetGameData('FlowersChristmasNETTotalWin', 0);
                                $slotSettings->SetGameData('FlowersChristmasNETBet', $betline);
                                $slotSettings->SetGameData('FlowersChristmasNETDenom', $postData['bet_denomination']);
                                $slotSettings->SetGameData('FlowersChristmasNETFreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $postData['bet_denomination'] = $slotSettings->GetGameData('FlowersChristmasNETDenom');
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $slotSettings->GetGameData('FlowersChristmasNETBet');
                                $allbet = $betline * $lines;
                                $slotSettings->SetGameData('FlowersChristmasNETCurrentFreeGame', $slotSettings->GetGameData('FlowersChristmasNETCurrentFreeGame') + 1);
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
                                $wild = ['1'];
                                $scatter = '0';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
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
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                $scnt = 2;
                                                if( $csym >= 3 && $csym <= 7 ) 
                                                {
                                                    $dbsym = $csym + 10;
                                                    for( $cs = 0; $cs < 2; $cs++ ) 
                                                    {
                                                        if( $s[$cs] == $dbsym ) 
                                                        {
                                                            $scnt++;
                                                        }
                                                    }
                                                }
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][$scnt] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                    $mainSymAnim = $csym;
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $scnt = 3;
                                                if( $csym >= 3 && $csym <= 7 ) 
                                                {
                                                    $dbsym = $csym + 10;
                                                    for( $cs = 0; $cs < 3; $cs++ ) 
                                                    {
                                                        if( $s[$cs] == $dbsym ) 
                                                        {
                                                            $scnt++;
                                                        }
                                                    }
                                                }
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][$scnt] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                    $mainSymAnim = $csym;
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $scnt = 4;
                                                if( $csym >= 3 && $csym <= 7 ) 
                                                {
                                                    $dbsym = $csym + 10;
                                                    for( $cs = 0; $cs < 4; $cs++ ) 
                                                    {
                                                        if( $s[$cs] == $dbsym ) 
                                                        {
                                                            $scnt++;
                                                        }
                                                    }
                                                }
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][$scnt] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
                                                    $mainSymAnim = $csym;
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                $scnt = 5;
                                                if( $csym >= 3 && $csym <= 7 ) 
                                                {
                                                    $dbsym = $csym + 10;
                                                    for( $cs = 0; $cs < 5; $cs++ ) 
                                                    {
                                                        if( $s[$cs] == $dbsym ) 
                                                        {
                                                            $scnt++;
                                                        }
                                                    }
                                                }
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][$scnt] * $betline * $mpl * $bonusMpl;
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
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                        }
                                    }
                                }
                                if( $scattersCount >= 4 ) 
                                {
                                    $scattersStr = '&ws.i0.types.i0.freespins=' . $slotSettings->slotFreeCount[$scattersCount] . '&ws.i0.reelset=basic&ws.i0.betline=null&ws.i0.types.i0.wintype=freespins&ws.i0.direction=none' . implode('', $scPos);
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
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $allbet) ) 
                                    {
                                    }
                                    else if( $scattersCount >= 4 && $winType != 'bonus' ) 
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
                            $freeState = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                            $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                            $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                            $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                            $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('FlowersChristmasNETBonusWin', $slotSettings->GetGameData('FlowersChristmasNETBonusWin') + $totalWin);
                                $slotSettings->SetGameData('FlowersChristmasNETTotalWin', $slotSettings->GetGameData('FlowersChristmasNETTotalWin') + $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('FlowersChristmasNETTotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $scattersCount >= 4 ) 
                            {
                                $slotSettings->SetGameData('FlowersChristmasNETFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('FlowersChristmasNETBonusWin', $totalWin);
                                $slotSettings->SetGameData('FlowersChristmasNETFreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                $fs = $slotSettings->GetGameData('FlowersChristmasNETFreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=freespin&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cfreespin&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=freespin&freespins.initial=' . $fs . '&freespins.win.coins=0&freespins.betlevel=' . $slotSettings->GetGameData('FlowersChristmasNETBet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
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
                            $slotSettings->SetGameData('FlowersChristmasNETGambleStep', 5);
                            $hist = $slotSettings->GetGameData('FlowersChristmasNETCards');
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
                                $totalWin = $slotSettings->GetGameData('FlowersChristmasNETBonusWin');
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $nextaction = 'spin';
                                    $stack = 'basic';
                                    $gamestate = 'basic';
                                }
                                else
                                {
                                    $gamestate = 'freespin';
                                    $nextaction = 'freespin';
                                    $stack = 'basic%2Cfreespin';
                                }
                                $fs = $slotSettings->GetGameData('FlowersChristmasNETFreeGames');
                                $fsl = $slotSettings->GetGameData('FlowersChristmasNETFreeGames') - $slotSettings->GetGameData('FlowersChristmasNETCurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData('FlowersChristmasNETBet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('FlowersChristmasNETFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('FlowersChristmasNETCurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('FlowersChristmasNETBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $result_tmp[] = 'rs.i0.r.i1.pos=18&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&rs.i0.r.i1.hold=false&rs.i0.r.i4.hold=false&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=47&rs.i0.id=basic&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gamestate.current=basic&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=4&rs.i0.r.i4.pos=5&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=7&wavecount=1&gamesoundurl=&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $curReels . $winString;
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
