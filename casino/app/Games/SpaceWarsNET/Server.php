<?php 
namespace VanguardLTE\Games\SpaceWarsNET
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
                    if( $postData['action'] == 'respin' ) 
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
                        $lines = 40;
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
                            $slotSettings->SetGameData('SpaceWarsNETBonusWin', 0);
                            $slotSettings->SetGameData('SpaceWarsNETFreeGames', 0);
                            $slotSettings->SetGameData('SpaceWarsNETCurrentFreeGame', 0);
                            $slotSettings->SetGameData('SpaceWarsNETTotalWin', 0);
                            $slotSettings->SetGameData('SpaceWarsNETFreeBalance', 0);
                            if( $lastEvent != 'NULL' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $curReels = '&rs.i0.r.i0.syms=SYM' . $reels->reel1[0] . '%2CSYM' . $reels->reel1[1] . '%2CSYM' . $reels->reel1[2] . '%2CSYM' . $reels->reel1[3] . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels->reel2[0] . '%2CSYM' . $reels->reel2[1] . '%2CSYM' . $reels->reel2[2] . '%2CSYM' . $reels->reel2[3] . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels->reel3[0] . '%2CSYM' . $reels->reel3[1] . '%2CSYM' . $reels->reel3[2] . '%2CSYM' . $reels->reel3[3] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels->reel4[0] . '%2CSYM' . $reels->reel4[1] . '%2CSYM' . $reels->reel4[2] . '%2CSYM' . $reels->reel4[3] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels->reel5[0] . '%2CSYM' . $reels->reel5[1] . '%2CSYM' . $reels->reel5[2] . '%2CSYM' . $reels->reel5[3] . '');
                            }
                            else
                            {
                                $curReels = '&rs.i0.r.i0.syms=SYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '%2CSYM' . rand(1, 12) . '');
                            }
                            for( $d = 0; $d < count($slotSettings->Denominations); $d++ ) 
                            {
                                $slotSettings->Denominations[$d] = $slotSettings->Denominations[$d] * 100;
                            }
                            $result_tmp[] = 'bl.i32.reelset=ALL&bl.i6.coins=1&bl.i17.reelset=ALL&bl.i15.id=15&rs.i0.r.i4.hold=false&bl.i21.id=21&game.win.cents=0&staticsharedurl=&bl.i23.reelset=ALL&bl.i33.coins=1&bl.i10.line=1%2C0%2C1%2C0%2C1&bl.i0.reelset=ALL&bl.i20.coins=1&bl.i18.coins=1&bl.i10.id=10&bl.i3.reelset=ALL&bl.i4.line=3%2C2%2C1%2C2%2C3&bl.i13.coins=1&bl.i26.reelset=ALL&bl.i24.line=0%2C0%2C2%2C0%2C0&bl.i27.id=27&rs.i0.r.i0.syms=SYM8%2CSYM8%2CSYM8%2CSYM2&bl.i2.id=2&bl.i38.line=3%2C0%2C0%2C0%2C3&rs.i0.r.i0.pos=0&bl.i14.reelset=ALL&bl.i38.id=38&bl.i39.coins=1&game.win.coins=0&bl.i28.line=0%2C2%2C0%2C2%2C0&bl.i3.id=3&bl.i22.line=2%2C2%2C0%2C2%2C2&bl.i12.coins=1&bl.i8.reelset=ALL&clientaction=init&rs.i0.r.i2.hold=false&bl.i16.id=16&bl.i37.reelset=ALL&bl.i39.id=39&casinoID=netent&bl.i5.coins=1&bl.i8.id=8&rs.i0.r.i3.pos=0&bl.i33.id=33&bl.i6.line=0%2C1%2C2%2C1%2C0&bl.i22.id=22&bl.i12.line=1%2C2%2C1%2C2%2C1&bl.i0.line=1%2C1%2C1%2C1%2C1&bl.i29.reelset=ALL&bl.i34.line=2%2C1%2C1%2C1%2C2&bl.i31.line=1%2C2%2C2%2C2%2C1&rs.i0.r.i2.syms=SYM12%2CSYM12%2CSYM12%2CSYM5&bl.i34.coins=1&game.win.amount=null&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&bl.i27.coins=1&bl.i34.reelset=ALL&current.rs.i0=basic&bl.i30.reelset=ALL&bl.i1.id=1&bl.i33.line=3%2C2%2C2%2C2%2C3&bl.i25.id=25&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&bl.i31.id=31&bl.i32.line=2%2C3%2C3%2C3%2C2&multiplier=1&bl.i14.id=14&bl.i19.line=0%2C0%2C1%2C0%2C0&bl.i12.reelset=ALL&bl.i2.coins=1&bl.i6.id=6&bl.i21.reelset=ALL&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&bl.i20.id=20&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&bl.i33.reelset=ALL&bl.i5.reelset=ALL&bl.i24.coins=1&bl.i19.coins=1&bl.i32.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&bl.i32.id=32&bl.i14.line=1%2C1%2C0%2C1%2C1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM12%2CSYM12%2CSYM12%2CSYM5&bl.i25.coins=1&rs.i0.r.i2.pos=0&bl.i39.reelset=ALL&bl.i13.line=2%2C3%2C2%2C3%2C2&bl.i24.reelset=ALL&bl.i0.coins=1&bl.i2.reelset=ALL&bl.i31.coins=1&bl.i37.id=37&bl.i26.coins=1&bl.i27.reelset=ALL&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39&bl.i29.line=1%2C3%2C1%2C3%2C1&bl.i23.line=0%2C0%2C3%2C0%2C0&bl.i26.id=26&bl.i15.reelset=ALL&rs.i0.r.i3.hold=false&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&bl.i30.id=30&historybutton=false&bl.i25.line=1%2C1%2C3%2C1%2C1&bl.i5.id=5&gameEventSetters.enabled=false&next.rs=basic&bl.i36.reelset=ALL&rs.i0.r.i1.syms=SYM12%2CSYM12%2CSYM12%2CSYM5&bl.i3.coins=1&bl.i10.coins=1&bl.i18.id=18&bl.i30.coins=1&bl.i39.line=0%2C3%2C3%2C3%2C0&totalwin.coins=0&bl.i5.line=2%2C1%2C0%2C1%2C2&gamestate.current=basic&bl.i28.coins=1&bl.i27.line=2%2C0%2C2%2C0%2C2&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C3%2C2%2C1&bl.i35.id=35&rs.i0.r.i3.syms=SYM1%2CSYM1%2CSYM1%2CSYM1&bl.i16.coins=1&bl.i36.coins=1&bl.i9.coins=1&bl.i30.line=0%2C1%2C1%2C1%2C0&bl.i7.reelset=ALL&isJackpotWin=false&bl.i24.id=24&rs.i0.r.i1.pos=0&bl.i22.coins=1&bl.i29.coins=1&bl.i31.reelset=ALL&bl.i13.id=13&bl.i36.id=36&rs.i0.r.i1.hold=false&bl.i9.line=2%2C1%2C2%2C1%2C2&bl.i35.coins=1&betlevel.standard=1&bl.i10.reelset=ALL&gameover=true&bl.i25.reelset=ALL&bl.i23.coins=1&bl.i11.coins=1&bl.i22.reelset=ALL&bl.i13.reelset=ALL&bl.i0.id=0&nextaction=spin&bl.i15.line=2%2C2%2C1%2C2%2C2&bl.i3.line=3%2C3%2C3%2C3%2C3&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&bl.i37.line=0%2C3%2C0%2C3%2C0&bl.i18.line=1%2C1%2C2%2C1%2C1&bl.i9.id=9&bl.i34.id=34&bl.i17.line=2%2C2%2C3%2C2%2C2&bl.i11.id=11&bl.i37.coins=1&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&bl.i17.coins=1&bl.i28.id=28&bl.i19.reelset=ALL&bl.i11.reelset=ALL&bl.i16.line=3%2C3%2C2%2C3%2C3&rs.i0.id=basic&bl.i38.reelset=ALL&credit=' . $balanceInCents . '&bl.i21.line=3%2C3%2C1%2C3%2C3&bl.i35.line=1%2C0%2C0%2C0%2C1&bl.i1.reelset=ALL&bl.i21.coins=1&bl.i28.reelset=ALL&bl.i1.line=0%2C0%2C0%2C0%2C0&bl.i17.id=17&bl.i16.reelset=ALL&nearwinallowed=true&bl.i8.line=3%2C2%2C3%2C2%2C3&bl.i35.reelset=ALL&bl.i8.coins=1&bl.i23.id=23&bl.i15.coins=1&bl.i36.line=3%2C0%2C3%2C0%2C3&bl.i2.line=2%2C2%2C2%2C2%2C2&totalwin.cents=0&bl.i38.coins=1&rs.i0.r.i0.hold=false&restore=false&bl.i12.id=12&bl.i29.id=29&bl.i4.id=4&rs.i0.r.i4.pos=0&bl.i7.coins=1&bl.i6.reelset=ALL&bl.i20.line=3%2C3%2C0%2C3%2C3&bl.i20.reelset=ALL&wavecount=1&bl.i14.coins=1&bl.i26.line=3%2C1%2C3%2C1%2C3' . $curReels;
                            break;
                        case 'paytable':
                            $result_tmp[] = 'bl.i32.reelset=ALL&pt.i0.comp.i19.symbol=SYM8&bl.i6.coins=1&bl.i17.reelset=ALL&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i0.comp.i32.type=betline&bl.i15.id=15&pt.i0.comp.i29.type=betline&pt.i0.comp.i4.multi=125&pt.i0.comp.i15.symbol=SYM7&pt.i0.comp.i17.symbol=SYM7&pt.i0.comp.i5.freespins=0&pt.i0.comp.i22.multi=20&pt.i0.comp.i23.n=5&bl.i21.id=21&pt.i0.comp.i11.symbol=SYM5&pt.i0.comp.i13.symbol=SYM6&bl.i23.reelset=ALL&bl.i33.coins=1&pt.i0.comp.i15.multi=10&bl.i10.line=1%2C0%2C1%2C0%2C1&bl.i0.reelset=ALL&bl.i20.coins=1&pt.i0.comp.i16.freespins=0&pt.i0.comp.i28.multi=15&bl.i18.coins=1&bl.i10.id=10&pt.i0.comp.i11.n=5&pt.i0.comp.i4.freespins=0&bl.i3.reelset=ALL&bl.i4.line=3%2C2%2C1%2C2%2C3&pt.i0.comp.i30.freespins=0&bl.i13.coins=1&bl.i26.reelset=ALL&bl.i24.line=0%2C0%2C2%2C0%2C0&bl.i27.id=27&pt.i0.comp.i19.n=4&pt.i0.id=basic&pt.i0.comp.i1.type=betline&bl.i2.id=2&bl.i38.line=3%2C0%2C0%2C0%2C3&pt.i0.comp.i2.symbol=SYM2&pt.i0.comp.i4.symbol=SYM3&pt.i0.comp.i20.type=betline&bl.i14.reelset=ALL&pt.i0.comp.i17.freespins=0&bl.i38.id=38&bl.i39.coins=1&pt.i0.comp.i6.symbol=SYM4&pt.i0.comp.i8.symbol=SYM4&pt.i0.comp.i0.symbol=SYM2&pt.i0.comp.i5.n=5&pt.i0.comp.i3.type=betline&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=60&bl.i28.line=0%2C2%2C0%2C2%2C0&bl.i3.id=3&bl.i22.line=2%2C2%2C0%2C2%2C2&pt.i0.comp.i27.multi=2&pt.i0.comp.i9.multi=10&bl.i12.coins=1&pt.i0.comp.i22.symbol=SYM9&pt.i0.comp.i26.symbol=SYM10&pt.i0.comp.i24.n=3&bl.i8.reelset=ALL&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&clientaction=paytable&bl.i16.id=16&bl.i37.reelset=ALL&bl.i39.id=39&bl.i5.coins=1&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=0&bl.i8.id=8&pt.i0.comp.i16.multi=40&pt.i0.comp.i21.multi=4&bl.i33.id=33&pt.i0.comp.i12.n=3&bl.i6.line=0%2C1%2C2%2C1%2C0&bl.i22.id=22&pt.i0.comp.i13.type=betline&bl.i12.line=1%2C2%2C1%2C2%2C1&bl.i0.line=1%2C1%2C1%2C1%2C1&bl.i29.reelset=ALL&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&bl.i34.line=2%2C1%2C1%2C1%2C2&pt.i0.comp.i31.freespins=0&bl.i31.line=1%2C2%2C2%2C2%2C1&pt.i0.comp.i3.multi=20&bl.i34.coins=1&pt.i0.comp.i6.n=3&pt.i0.comp.i21.n=3&bl.i27.coins=1&bl.i34.reelset=ALL&bl.i30.reelset=ALL&pt.i0.comp.i29.n=5&bl.i1.id=1&pt.i0.comp.i27.freespins=0&bl.i33.line=3%2C2%2C2%2C2%2C3&pt.i0.comp.i10.type=betline&bl.i25.id=25&pt.i0.comp.i2.freespins=0&pt.i0.comp.i5.multi=400&pt.i0.comp.i7.n=4&pt.i0.comp.i32.n=5&bl.i31.id=31&bl.i32.line=2%2C3%2C3%2C3%2C2&pt.i0.comp.i11.multi=175&bl.i14.id=14&pt.i0.comp.i7.type=betline&bl.i19.line=0%2C0%2C1%2C0%2C0&bl.i12.reelset=ALL&pt.i0.comp.i17.n=5&bl.i2.coins=1&bl.i6.id=6&bl.i21.reelset=ALL&pt.i0.comp.i29.multi=40&pt.i0.comp.i8.freespins=0&bl.i20.id=20&pt.i0.comp.i8.multi=200&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&pt.i0.comp.i1.freespins=0&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=150&bl.i33.reelset=ALL&bl.i5.reelset=ALL&bl.i24.coins=1&pt.i0.comp.i22.n=4&pt.i0.comp.i28.symbol=SYM11&bl.i19.coins=1&bl.i32.coins=1&bl.i7.id=7&bl.i18.reelset=ALL&pt.i0.comp.i6.multi=15&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&bl.i32.id=32&bl.i14.line=1%2C1%2C0%2C1%2C1&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM9&pt.i0.comp.i21.type=betline&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i0.comp.i2.type=betline&pt.i0.comp.i13.multi=50&bl.i25.coins=1&bl.i39.reelset=ALL&pt.i0.comp.i17.type=betline&bl.i13.line=2%2C3%2C2%2C3%2C2&pt.i0.comp.i30.type=betline&bl.i24.reelset=ALL&bl.i0.coins=1&bl.i2.reelset=ALL&pt.i0.comp.i8.n=5&pt.i0.comp.i10.n=4&bl.i31.coins=1&bl.i37.id=37&pt.i0.comp.i11.type=betline&pt.i0.comp.i18.n=3&pt.i0.comp.i22.freespins=0&bl.i26.coins=1&bl.i27.reelset=ALL&pt.i0.comp.i20.symbol=SYM8&bl.i29.line=1%2C3%2C1%2C3%2C1&pt.i0.comp.i15.freespins=0&pt.i0.comp.i31.symbol=SYM12&bl.i23.line=0%2C0%2C3%2C0%2C0&pt.i0.comp.i27.type=betline&bl.i26.id=26&pt.i0.comp.i28.freespins=0&pt.i0.comp.i0.n=3&pt.i0.comp.i7.symbol=SYM4&bl.i15.reelset=ALL&pt.i0.comp.i0.type=betline&g4mode=false&bl.i11.line=0%2C1%2C0%2C1%2C0&bl.i30.id=30&pt.i0.comp.i25.multi=15&historybutton=false&bl.i25.line=1%2C1%2C3%2C1%2C1&pt.i0.comp.i16.symbol=SYM7&bl.i5.id=5&pt.i0.comp.i1.multi=250&pt.i0.comp.i27.n=3&pt.i0.comp.i18.symbol=SYM8&bl.i36.reelset=ALL&pt.i0.comp.i12.multi=10&pt.i0.comp.i32.multi=40&bl.i3.coins=1&bl.i10.coins=1&pt.i0.comp.i12.symbol=SYM6&pt.i0.comp.i14.symbol=SYM6&bl.i18.id=18&pt.i0.comp.i14.type=betline&bl.i30.coins=1&bl.i39.line=0%2C3%2C3%2C3%2C0&pt.i0.comp.i18.multi=4&bl.i5.line=2%2C1%2C0%2C1%2C2&pt.i0.comp.i7.multi=75&bl.i28.coins=1&pt.i0.comp.i9.n=3&pt.i0.comp.i30.n=3&bl.i27.line=2%2C0%2C2%2C0%2C2&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C3%2C2%2C1&pt.i0.comp.i28.type=betline&bl.i35.id=35&pt.i0.comp.i10.symbol=SYM5&pt.i0.comp.i15.n=3&bl.i16.coins=1&bl.i36.coins=1&bl.i9.coins=1&bl.i30.line=0%2C1%2C1%2C1%2C0&pt.i0.comp.i21.symbol=SYM9&bl.i7.reelset=ALL&pt.i0.comp.i31.type=betline&isJackpotWin=false&bl.i24.id=24&pt.i0.comp.i1.n=4&bl.i22.coins=1&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=60&pt.i0.comp.i20.n=5&pt.i0.comp.i29.symbol=SYM11&pt.i0.comp.i17.multi=125&bl.i29.coins=1&bl.i31.reelset=ALL&bl.i13.id=13&bl.i36.id=36&pt.i0.comp.i25.symbol=SYM10&pt.i0.comp.i26.type=betline&pt.i0.comp.i28.n=4&pt.i0.comp.i9.type=betline&bl.i9.line=2%2C1%2C2%2C1%2C2&pt.i0.comp.i2.multi=1000&pt.i0.comp.i0.freespins=0&bl.i35.coins=1&bl.i10.reelset=ALL&pt.i0.comp.i29.freespins=0&bl.i25.reelset=ALL&pt.i0.comp.i31.n=4&pt.i0.comp.i9.symbol=SYM5&bl.i23.coins=1&bl.i11.coins=1&pt.i0.comp.i16.n=4&bl.i22.reelset=ALL&bl.i13.reelset=ALL&bl.i0.id=0&pt.i0.comp.i16.type=betline&pt.i0.comp.i5.symbol=SYM3&bl.i15.line=2%2C2%2C1%2C2%2C2&bl.i3.line=3%2C3%2C3%2C3%2C3&bl.i19.id=19&bl.i4.reelset=ALL&bl.i4.coins=1&pt.i0.comp.i2.n=5&bl.i37.line=0%2C3%2C0%2C3%2C0&pt.i0.comp.i1.symbol=SYM2&bl.i18.line=1%2C1%2C2%2C1%2C1&bl.i9.id=9&bl.i34.id=34&pt.i0.comp.i19.freespins=0&bl.i17.line=2%2C2%2C3%2C2%2C2&bl.i11.id=11&pt.i0.comp.i6.type=betline&bl.i37.coins=1&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&bl.i17.coins=1&bl.i28.id=28&bl.i19.reelset=ALL&pt.i0.comp.i25.n=4&pt.i0.comp.i9.freespins=0&bl.i11.reelset=ALL&bl.i16.line=3%2C3%2C2%2C3%2C3&bl.i38.reelset=ALL&credit=500000&pt.i0.comp.i5.type=betline&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=40&bl.i21.line=3%2C3%2C1%2C3%2C3&pt.i0.comp.i25.type=betline&bl.i35.line=1%2C0%2C0%2C0%2C1&bl.i1.reelset=ALL&pt.i0.comp.i31.multi=15&pt.i0.comp.i4.type=betline&bl.i21.coins=1&bl.i28.reelset=ALL&pt.i0.comp.i13.freespins=0&pt.i0.comp.i26.freespins=0&bl.i1.line=0%2C0%2C0%2C0%2C0&pt.i0.comp.i13.n=4&pt.i0.comp.i20.freespins=0&pt.i0.comp.i23.type=betline&pt.i0.comp.i30.symbol=SYM12&pt.i0.comp.i32.symbol=SYM12&bl.i17.id=17&bl.i16.reelset=ALL&pt.i0.comp.i3.n=3&pt.i0.comp.i25.freespins=0&bl.i8.line=3%2C2%2C3%2C2%2C3&pt.i0.comp.i24.symbol=SYM10&bl.i35.reelset=ALL&pt.i0.comp.i26.n=5&pt.i0.comp.i27.symbol=SYM11&bl.i8.coins=1&pt.i0.comp.i32.freespins=0&bl.i23.id=23&bl.i15.coins=1&bl.i36.line=3%2C0%2C3%2C0%2C3&pt.i0.comp.i23.multi=50&bl.i2.line=2%2C2%2C2%2C2%2C2&pt.i0.comp.i30.multi=2&bl.i38.coins=1&pt.i0.comp.i18.freespins=0&bl.i12.id=12&bl.i29.id=29&bl.i4.id=4&bl.i7.coins=1&pt.i0.comp.i14.n=5&pt.i0.comp.i0.multi=30&bl.i6.reelset=ALL&pt.i0.comp.i19.multi=20&pt.i0.comp.i3.symbol=SYM3&bl.i20.line=3%2C3%2C0%2C3%2C3&pt.i0.comp.i24.type=betline&bl.i20.reelset=ALL&bl.i14.coins=1&pt.i0.comp.i12.freespins=0&pt.i0.comp.i4.n=4&bl.i26.line=3%2C1%2C3%2C1%2C3&pt.i0.comp.i24.multi=3';
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
                                4, 
                                4, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[4] = [
                                4, 
                                3, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[5] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[6] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[7] = [
                                2, 
                                3, 
                                4, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                4, 
                                3, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[9] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[10] = [
                                2, 
                                1, 
                                2, 
                                1, 
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
                                2, 
                                3, 
                                2, 
                                3, 
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
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[16] = [
                                4, 
                                4, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[17] = [
                                3, 
                                3, 
                                4, 
                                3, 
                                3
                            ];
                            $linesId[18] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[19] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[20] = [
                                4, 
                                4, 
                                1, 
                                4, 
                                4
                            ];
                            $linesId[21] = [
                                4, 
                                4, 
                                2, 
                                4, 
                                4
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
                                1, 
                                4, 
                                1, 
                                1
                            ];
                            $linesId[24] = [
                                1, 
                                1, 
                                3, 
                                1, 
                                1
                            ];
                            $linesId[25] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[26] = [
                                4, 
                                2, 
                                4, 
                                2, 
                                4
                            ];
                            $linesId[27] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[28] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[29] = [
                                2, 
                                4, 
                                2, 
                                4, 
                                2
                            ];
                            $linesId[30] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[31] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[32] = [
                                3, 
                                4, 
                                4, 
                                4, 
                                3
                            ];
                            $linesId[33] = [
                                4, 
                                3, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[34] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[35] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[36] = [
                                4, 
                                1, 
                                4, 
                                1, 
                                4
                            ];
                            $linesId[37] = [
                                1, 
                                4, 
                                1, 
                                4, 
                                1
                            ];
                            $linesId[38] = [
                                4, 
                                1, 
                                1, 
                                1, 
                                4
                            ];
                            $linesId[39] = [
                                1, 
                                4, 
                                4, 
                                4, 
                                1
                            ];
                            $lines = 40;
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                $betline = $postData['bet_betlevel'];
                                $allbet = $betline * $lines;
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetGameData('SpaceWarsNETBonusWin', 0);
                                $slotSettings->SetGameData('SpaceWarsNETFreeGames', 0);
                                $slotSettings->SetGameData('SpaceWarsNETCurrentFreeGame', 0);
                                $slotSettings->SetGameData('SpaceWarsNETTotalWin', 0);
                                $slotSettings->SetGameData('SpaceWarsNETBet', $betline);
                                $slotSettings->SetGameData('SpaceWarsNETFreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $betline = $slotSettings->GetGameData('SpaceWarsNETBet');
                                $allbet = $betline * $lines;
                                $slotSettings->SetGameData('SpaceWarsNETCurrentFreeGame', $slotSettings->GetGameData('SpaceWarsNETCurrentFreeGame') + 1);
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
                                $scatter = '';
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . $tmpWin . '';
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . $tmpWin . '';
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.pos.i4=4%2C' . ($linesId[$k][4] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . $tmpWin . '';
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
                                            $scPos[] = '' . ($r - 1) . ',' . $p . '';
                                        }
                                    }
                                }
                                if( $scattersWin > 0 ) 
                                {
                                    $sgwin = 0;
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $sgwin = $slotSettings->slotFreeCount;
                                    }
                                    $scattersStr = '{"scatterName":' . $scatter . ',"cells":[' . implode(',', $scPos) . '],"winAmount":' . ($scattersWin * 100) . ',"freespins":' . $sgwin . '}';
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
                                $slotSettings->SetGameData('SpaceWarsNETBonusWin', $slotSettings->GetGameData('SpaceWarsNETBonusWin') + $totalWin);
                                $slotSettings->SetGameData('SpaceWarsNETTotalWin', $slotSettings->GetGameData('SpaceWarsNETTotalWin') + $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('SpaceWarsNETTotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetGameData('SpaceWarsNETFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('SpaceWarsNETBonusWin', $totalWin);
                                $slotSettings->SetGameData('SpaceWarsNETFreeGames', $slotSettings->slotFreeCount);
                                $fs = $slotSettings->GetGameData('SpaceWarsNETFreeGames');
                            }
                            /*$newTime = time() - $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimit0');
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit0', time());
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWinLimit', $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWinLimit') - $newTime);
                            $slotSettings->SetGameDataStatic($slotSettings->slotId . 'timeWin', $slotSettings->GetGameDataStatic($slotSettings->slotId . 'timeWin') + ($totalWin * $slotSettings->CurrentDenom));*/
                            $winString = implode('', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('SpaceWarsNETFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('SpaceWarsNETCurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('SpaceWarsNETBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $slotSettings->SetGameData('SpaceWarsNETGambleStep', 5);
                            $hist = $slotSettings->GetGameData('SpaceWarsNETCards');
                            $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '%2CSYM' . $reels['reel1'][3] . '';
                            $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '%2CSYM' . $reels['reel2'][3] . '');
                            $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '%2CSYM' . $reels['reel3'][3] . '');
                            $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '%2CSYM' . $reels['reel4'][3] . '');
                            $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '%2CSYM' . $reels['reel5'][3] . '');
                            $curReels0 = '&rs.i10.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '%2CSYM' . $reels['reel1'][3] . '';
                            $curReels0 .= ('&rs.i10.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '%2CSYM' . $reels['reel2'][3] . '');
                            $curReels0 .= ('&rs.i10.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '%2CSYM' . $reels['reel3'][3] . '');
                            $curReels0 .= ('&rs.i10.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '%2CSYM' . $reels['reel4'][3] . '');
                            $curReels0 .= ('&rs.i10.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '%2CSYM' . $reels['reel5'][3] . '');
                            $isJack = 'false';
                            if( $totalWin > 0 ) 
                            {
                                $state = 'gamble';
                                $gameover = 'false';
                                $curReels .= $curReels0;
                                if( $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $nextaction = 'respin';
                                    $curReels .= ('&next.rs=respin-SYM' . $mainSymAnim . '&rs.i10.id=respin-SYM' . $mainSymAnim . '&symbolwon=SYM' . $mainSymAnim . '');
                                }
                                else
                                {
                                    $nextaction = 'spin';
                                    $gameover = 'true';
                                }
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
                                $totalWin = $slotSettings->GetGameData('SpaceWarsNETBonusWin');
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData('SpaceWarsNETBonusWin') > 0 ) 
                                {
                                }
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $result_tmp[] = 'previous.rs.i0=basic&rs.i10.r.i2.pos=0&g4mode=false&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i4.hold=false&gamestate.history=basic&rs.i10.r.i1.pos=0&rs.i10.r.i2.hold=false&rs.i10.r.i1.hold=false&game.win.cents=' . ($totalWin * 100) . '&rs.i0.id=basic&rs.i10.r.i4.pos=0&totalwin.coins=' . $slotSettings->GetGameData('SpaceWarsNETTotalWin') . '&credit=' . $balanceInCents . '&rs.i10.r.i0.pos=0&gamestate.current=basic&jackpotcurrency=%26%23x20AC%3B&multiplier=1&last.rs=basic&isJackpotWin=false&gamestate.stack=basic&rs.i0.r.i0.pos=24&gamesoundurl=&rs.i10.id=respin-SYM12&rs.i0.r.i1.pos=52&game.win.coins=' . ($totalWin * 100) . '&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&rs.i0.r.i2.pos=131&rs.i10.r.i4.hold=false&totalwin.cents=' . ($slotSettings->GetGameData('SpaceWarsNETTotalWin') * 100) . '&gameover=' . $gameover . '&rs.i0.r.i0.hold=false&rs.i10.r.i3.hold=false&rs.i0.r.i3.pos=82&rs.i10.r.i0.hold=false&rs.i0.r.i4.pos=20&rs.i10.r.i3.pos=0&nextaction=' . $nextaction . '&wavecount=1&rs.i0.r.i3.hold=false&game.win.amount=' . $totalWin . '' . $curReels . $winString;
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
