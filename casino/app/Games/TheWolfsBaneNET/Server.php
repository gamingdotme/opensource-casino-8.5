<?php 
namespace VanguardLTE\Games\TheWolfsBaneNET
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
                    if( $postData['action'] == 'initbonus' ) 
                    {
                        $postData['slotEvent'] = 'initbonus';
                    }
                    if( $postData['action'] == 'bonusaction' ) 
                    {
                        $postData['slotEvent'] = 'bonusaction';
                    }
                    if( $postData['action'] == 'endbonus' ) 
                    {
                        $postData['slotEvent'] = 'endbonus';
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
                        if( !isset($postData['bet_betlevel']) ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"bet","serverResponse":"invalid bet request"}';
                            exit( $response );
                        }
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
                            $freeState = '';
                            if( $lastEvent != 'NULL' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $slotSettings->SetGameData($slotSettings->slotId . 'ReelsType', $lastEvent->serverResponse->ReelsType);
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
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $freeState = 'previous.rs.i0=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&rs.i1.r.i0.syms=SYM6%2CSYM5%2CSYM4&bl.i6.coins=1&rs.i0.r.i0.overlay.i0.pos=21&rs.i0.r.i4.hold=false&gamestate.history=basic%2Cfreespin&rs.i1.r.i2.hold=false&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . "\t&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&freespins.initial=10&rs.i0.r.i0.overlay.i0.with=SYM1&bl.i3.reelset=ALL&bl.i4.line=2%2C1%2C0%2C1%2C2&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM1%2CSYM3%2CSYM6&bl.i2.id=2&rs.i1.r.i1.pos=78&rs.i3.r.i4.pos=26&rs.i0.r.i0.pos=19&rs.i2.r.i3.pos=4&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=1&rs.i2.id=freespin_expanding&game.win.coins=" . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&rs.i1.r.i0.hold=false&bl.i3.id=3&ws.i1.reelset=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&bl.i8.reelset=ALL&clientaction=init&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM4%2CSYM6%2CSYM3&casinoID=netent&bl.i5.coins=1&rs.i3.r.i2.hold=false&bl.i8.id=8&rs.i0.r.i3.pos=26&rs.i4.r.i0.syms=SYM6%2CSYM9%2CSYM7&bl.i6.line=2%2C2%2C1%2C2%2C2&rs.i1.r.i2.attention.i0=1&bl.i0.line=1%2C1%2C1%2C1%2C1&rs.i4.r.i2.pos=2&rs.i0.r.i2.syms=SYM7%2CSYM1%2CSYM9&game.win.amount=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&betlevel.all=1&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&ws.i2.pos.i1=1%2C2&rs.i2.r.i0.pos=1&current.rs.i0=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&ws.i2.pos.i0=2%2C2&ws.i0.reelset=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&ws.i2.pos.i2=0%2C2&bl.i1.id=1&rs.i3.r.i2.syms=SYM8%2CSYM10%2CSYM5&rs.i1.r.i4.pos=54&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&rs.i3.id=freespin_multiplier&multiplier=1&freespins.denomination=5.000&bl.i2.coins=1&bl.i6.id=6&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&freespins.totalwin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&ws.i0.direction=left_to_right&freespins.total=' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM6%2CSYM7%2CSYM0&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&bet.betlevel=1&rs.i4.r.i2.hold=false&bl.i5.reelset=ALL&rs.i4.r.i1.syms=SYM3%2CSYM5%2CSYM4&bl.i7.id=7&rs.i2.r.i4.pos=26&rs.i3.r.i0.syms=SYM10%2CSYM6%2CSYM9&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&rs.i4.r.i1.hold=false&ws.i2.types.i0.coins=5&rs.i3.r.i2.pos=2&ws.i2.sym=SYM9&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM7%2CSYM6%2CSYM0&rs.i0.r.i2.pos=1&ws.i1.betline=5&rs.i1.r.i0.pos=75&bl.i0.coins=1&ws.i2.types.i0.wintype=coins&rs.i2.r.i0.syms=SYM7%2CSYM9%2CSYM10&bl.i2.reelset=ALL&rs.i3.r.i1.syms=SYM9%2CSYM3%2CSYM5&rs.i1.r.i4.hold=false&freespins.left=' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM9%2CSYM10%2CSYM8&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&rs.i1.r.i1.attention.i0=2&rs.i3.r.i0.hold=false&rs.i0.r.i3.hold=false&bet.denomination=' . ($slotSettings->GetGameData($slotSettings->slotId . 'GameDenom') * 100) . '&rs.i4.id=freespin_spreading&rs.i2.r.i1.hold=false&gameServerVersion=1.0.0&g4mode=false&freespins.win.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&historybutton=false&ws.i2.direction=left_to_right&bl.i5.id=5&gameEventSetters.enabled=false&next.rs=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&ws.i2.types.i0.cents=25&rs.i1.r.i3.pos=18&rs.i0.r.i1.syms=SYM9%2CSYM6%2CSYM1&bl.i3.coins=1&ws.i1.types.i0.coins=30&rs.i2.r.i1.pos=6&rs.i4.r.i4.pos=5&ws.i0.betline=6&rs.i1.r.i3.hold=false&totalwin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=freespin&rs.i4.r.i0.pos=3&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&rs.i3.r.i1.hold=false&rs.i0.r.i3.syms=SYM1%2CSYM6%2CSYM5&rs.i1.r.i1.syms=SYM7%2CSYM9%2CSYM0&freespins.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . "\t&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&rs.i2.r.i3.hold=false&ws.i2.reelset=" . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&rs.i0.r.i1.pos=53&rs.i4.r.i4.syms=SYM10%2CSYM8%2CSYM6&rs.i1.r.i3.syms=SYM3%2CSYM8%2CSYM7&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM10%2CSYM3%2CSYM6&ws.i1.types.i0.wintype=coins&bl.i9.line=1%2C0%2C1%2C0%2C1&ws.i1.sym=SYM9&betlevel.standard=1&ws.i1.types.i0.cents=150&gameover=false&rs.i3.r.i3.pos=15&ws.i1.direction=left_to_right&bl.i0.id=0&nextaction=freespin&bl.i3.line=0%2C1%2C2%2C1%2C0&rs.i1.r.i4.attention.i0=2&bl.i4.reelset=ALL&bl.i4.coins=1&freespins.totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . "\t&bl.i9.id=9&ws.i2.betline=2&ws.i0.pos.i3=3%2C2&freespins.betlevel=1&ws.i0.pos.i2=2%2C1&ws.i1.pos.i3=2%2C1&rs.i4.r.i3.pos=4&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&rs.i4.r.i4.hold=false&ws.i1.pos.i0=0%2C0&ws.i1.pos.i1=3%2C0&ws.i1.pos.i2=1%2C0&ws.i0.pos.i1=0%2C2&ws.i0.pos.i0=1%2C2&rs.i2.r.i4.syms=SYM4%2CSYM3%2CSYM6&rs.i4.r.i3.hold=false&rs.i0.id=" . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&credit=' . $balanceInCents . '&ws.i0.types.i0.coins=60&bl.i1.reelset=ALL&rs.i2.r.i2.pos=3&last.rs=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&bl.i1.line=0%2C0%2C0%2C0%2C0&ws.i0.sym=SYM5&rs.i2.r.i2.syms=SYM4%2CSYM7%2CSYM3&rs.i1.r.i2.pos=75&rs.i3.r.i3.syms=SYM6%2CSYM7%2CSYM4&rs.i1.nearwin=4%2C3&ws.i0.types.i0.wintype=coins&rs.i3.r.i4.hold=false&rs.i0.r.i0.overlay.i0.row=2&nearwinallowed=true&bl.i8.line=1%2C0%2C0%2C0%2C1&freespins.wavecount=1&rs.i3.r.i3.hold=false&bl.i8.coins=1&bl.i2.line=2%2C2%2C2%2C2%2C2&rs.i1.r.i2.syms=SYM5%2CSYM0%2CSYM6&totalwin.cents=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM8%2CSYM6%2CSYM10&restore=true&rs.i1.id=basic&rs.i3.r.i4.syms=SYM5%2CSYM10%2CSYM9&bl.i4.id=4&rs.i0.r.i4.pos=53&bl.i7.coins=1&ws.i0.types.i0.cents=300&bl.i6.reelset=ALL&rs.i3.r.i0.pos=3&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false';
                            }
                            $result_tmp[] = 'rs.i4.id=basic&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM7%2CSYM9%2CSYM10&bl.i6.coins=1&gameServerVersion=1.0.0&g4mode=false&historybutton=false&rs.i0.r.i4.hold=false&bl.i5.id=5&gameEventSetters.enabled=false&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=4&rs.i0.r.i1.syms=SYM5%2CSYM10%2CSYM3&bl.i3.coins=1&rs.i2.r.i1.pos=45&game.win.cents=0&rs.i4.r.i4.pos=5&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&rs.i1.r.i3.hold=false&totalwin.coins=0&bl.i5.line=0%2C0%2C1%2C0%2C0&gamestate.current=basic&bl.i3.reelset=ALL&rs.i4.r.i0.pos=3&bl.i4.line=2%2C1%2C0%2C1%2C2&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=1%2C2%2C2%2C2%2C1&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM7%2CSYM9%2CSYM10&rs.i0.r.i3.syms=SYM4%2CSYM9%2CSYM8&rs.i1.r.i1.syms=SYM10%2CSYM3%2CSYM6&bl.i2.id=2&rs.i1.r.i1.pos=6&rs.i3.r.i4.pos=26&bl.i9.coins=1&bl.i7.reelset=ALL&isJackpotWin=false&rs.i0.r.i0.pos=4&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=81&rs.i0.r.i1.pos=39&rs.i4.r.i4.syms=SYM10%2CSYM8%2CSYM6&rs.i1.r.i3.syms=SYM8%2CSYM6%2CSYM10&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=1&rs.i2.id=basic&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&bl.i3.id=3&rs.i2.r.i1.syms=SYM9%2CSYM5%2CSYM8&bl.i8.reelset=ALL&clientaction=init&bl.i9.line=1%2C0%2C1%2C0%2C1&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM4%2CSYM6%2CSYM3&casinoID=netent&betlevel.standard=1&bl.i5.coins=1&rs.i3.r.i2.hold=false&gameover=true&rs.i3.r.i3.pos=15&bl.i8.id=8&rs.i0.r.i3.pos=12&rs.i4.r.i0.syms=SYM6%2CSYM9%2CSYM7&bl.i0.id=0&bl.i6.line=2%2C2%2C1%2C2%2C2&bl.i0.line=1%2C1%2C1%2C1%2C1&nextaction=spin&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i4.reelset=ALL&rs.i4.r.i2.pos=2&bl.i4.coins=1&rs.i0.r.i2.syms=SYM8%2CSYM6%2CSYM9&game.win.amount=0&betlevel.all=1&bl.i9.id=9&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&rs.i4.r.i3.pos=4&playercurrency=%26%23x20AC%3B&bl.i9.reelset=ALL&rs.i2.r.i0.pos=3&rs.i4.r.i4.hold=false&bl.i1.id=1&rs.i2.r.i4.syms=SYM0%2CSYM9%2CSYM7&rs.i3.r.i2.syms=SYM8%2CSYM10%2CSYM5&rs.i4.r.i3.hold=false&rs.i0.id=freespin_regular&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=26&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&rs.i3.id=freespin_multiplier&bl.i1.reelset=ALL&multiplier=1&rs.i2.r.i2.pos=81&bl.i2.coins=1&bl.i6.id=6&bl.i1.line=0%2C0%2C0%2C0%2C0&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&rs.i1.r.i4.syms=SYM4%2CSYM3%2CSYM6&rs.i2.r.i2.syms=SYM2%2CSYM3%2CSYM10&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=3&rs.i3.r.i3.syms=SYM6%2CSYM7%2CSYM4&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&nearwinallowed=true&bl.i5.reelset=ALL&rs.i4.r.i1.syms=SYM3%2CSYM5%2CSYM4&bl.i7.id=7&rs.i2.r.i4.pos=1&bl.i8.line=1%2C0%2C0%2C0%2C1&rs.i3.r.i0.syms=SYM10%2CSYM6%2CSYM9&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&rs.i4.r.i1.hold=false&rs.i3.r.i2.pos=2&rs.i3.r.i3.hold=false&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM5%2CSYM8%2CSYM7&bl.i8.coins=1&rs.i0.r.i2.pos=25&bl.i2.line=2%2C2%2C2%2C2%2C2&rs.i1.r.i2.syms=SYM4%2CSYM7%2CSYM3&rs.i1.r.i0.pos=1&totalwin.cents=0&bl.i0.coins=1&rs.i2.r.i0.syms=SYM1%2CSYM8%2CSYM7&bl.i2.reelset=ALL&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM6%2CSYM4%2CSYM10&restore=false&rs.i1.id=freespin_expanding&rs.i3.r.i4.syms=SYM5%2CSYM10%2CSYM9&rs.i3.r.i1.syms=SYM9%2CSYM3%2CSYM5&rs.i1.r.i4.hold=false&bl.i4.id=4&rs.i0.r.i4.pos=10&bl.i7.coins=1&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM9%2CSYM10%2CSYM8&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&bl.i6.reelset=ALL&rs.i3.r.i0.pos=3&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false' . $curReels . $freeState;
                            break;
                        case 'paytable':
                            $result_tmp[] = 'pt.i0.comp.i17.symbol=SYM8&pt.i0.comp.i5.freespins=0&pt.i0.comp.i23.n=5&pt.i1.comp.i34.multi=80&pt.i0.comp.i13.symbol=SYM7&pt.i1.comp.i8.type=betline&pt.i1.comp.i4.n=4&pt.i0.comp.i15.multi=10&pt.i1.comp.i27.symbol=SYM13&pt.i1.comp.i29.freespins=0&pt.i1.comp.i30.symbol=SYM11&pt.i1.comp.i3.multi=20&pt.i0.comp.i11.n=5&pt.i1.comp.i23.symbol=SYM10&bl.i4.line=2%2C1%2C0%2C1%2C2&pt.i0.id=basic&pt.i0.comp.i1.type=betline&bl.i2.id=2&pt.i1.comp.i10.type=betline&pt.i0.comp.i4.symbol=SYM4&pt.i1.comp.i5.freespins=0&pt.i1.comp.i8.symbol=SYM5&pt.i1.comp.i19.n=4&pt.i0.comp.i17.freespins=0&pt.i0.comp.i8.symbol=SYM5&pt.i0.comp.i0.symbol=SYM3&pt.i1.comp.i36.freespins=0&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=60&pt.i1.id=freespin&bl.i3.id=3&pt.i1.comp.i34.freespins=0&pt.i1.comp.i34.type=betline&pt.i0.comp.i24.n=3&bl.i8.reelset=ALL&clientaction=paytable&pt.i1.comp.i27.freespins=0&pt.i1.comp.i5.n=5&bl.i5.coins=1&pt.i1.comp.i8.multi=200&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=10&pt.i1.comp.i38.type=betline&pt.i0.comp.i21.multi=5&pt.i1.comp.i13.multi=50&pt.i0.comp.i12.n=3&pt.i0.comp.i13.type=betline&bl.i0.line=1%2C1%2C1%2C1%2C1&pt.i1.comp.i7.freespins=0&pt.i0.comp.i3.multi=20&pt.i1.comp.i22.type=betline&pt.i0.comp.i21.n=3&pt.i1.comp.i6.n=3&pt.i1.comp.i31.type=betline&bl.i1.id=1&pt.i0.comp.i10.type=betline&pt.i1.comp.i11.symbol=SYM6&pt.i0.comp.i5.multi=300&pt.i1.comp.i1.freespins=0&pt.i1.comp.i16.symbol=SYM8&pt.i1.comp.i23.multi=80&pt.i1.comp.i4.type=betline&pt.i1.comp.i18.multi=5&bl.i2.coins=1&pt.i1.comp.i26.type=scatter&pt.i0.comp.i8.multi=200&pt.i0.comp.i1.freespins=0&bl.i5.reelset=ALL&pt.i0.comp.i22.n=4&pt.i1.comp.i17.type=betline&pt.i1.comp.i0.symbol=SYM3&pt.i1.comp.i7.n=4&pt.i1.comp.i5.multi=300&pt.i0.comp.i21.type=betline&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=10&pt.i0.comp.i13.multi=50&pt.i0.comp.i17.type=betline&pt.i1.comp.i22.symbol=SYM10&pt.i1.comp.i30.freespins=0&pt.i1.comp.i38.symbol=SYM1&bl.i0.coins=1&bl.i2.reelset=ALL&pt.i0.comp.i10.n=4&pt.i1.comp.i6.multi=15&pt.i1.comp.i19.symbol=SYM9&pt.i0.comp.i22.freespins=0&pt.i0.comp.i20.symbol=SYM9&pt.i0.comp.i15.freespins=0&pt.i0.comp.i0.n=3&pt.i1.comp.i21.multi=5&pt.i1.comp.i30.type=betline&pt.i0.comp.i0.type=betline&pt.i1.comp.i0.multi=25&g4mode=false&pt.i1.comp.i8.n=5&pt.i0.comp.i25.multi=0&pt.i1.comp.i37.multi=80&pt.i0.comp.i16.symbol=SYM8&pt.i1.comp.i21.freespins=0&pt.i0.comp.i1.multi=80&pt.i0.comp.i27.n=3&pt.i1.comp.i9.type=betline&pt.i1.comp.i24.multi=0&pt.i1.comp.i23.type=betline&pt.i1.comp.i26.n=5&pt.i1.comp.i28.symbol=SYM13&pt.i1.comp.i17.multi=100&pt.i0.comp.i18.multi=5&bl.i5.line=0%2C0%2C1%2C0%2C0&pt.i1.comp.i33.symbol=SYM12&pt.i1.comp.i35.type=betline&pt.i0.comp.i9.n=3&pt.i1.comp.i21.type=betline&bl.i7.line=1%2C2%2C2%2C2%2C1&pt.i1.comp.i31.multi=80&pt.i1.comp.i18.type=betline&pt.i0.comp.i10.symbol=SYM6&pt.i0.comp.i15.n=3&pt.i0.comp.i21.symbol=SYM10&bl.i7.reelset=ALL&pt.i1.comp.i15.n=3&pt.i1.comp.i38.n=5&isJackpotWin=false&pt.i1.comp.i20.freespins=0&pt.i1.comp.i7.type=betline&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=80&pt.i0.comp.i17.multi=100&pt.i1.comp.i25.type=scatter&pt.i1.comp.i9.n=3&bl.i9.line=1%2C0%2C1%2C0%2C1&pt.i0.comp.i2.multi=500&pt.i1.comp.i27.n=3&pt.i0.comp.i0.freespins=0&pt.i1.comp.i25.multi=0&pt.i1.comp.i16.freespins=0&pt.i1.comp.i5.type=betline&pt.i1.comp.i35.symbol=SYM12&pt.i1.comp.i24.symbol=SYM0&pt.i1.comp.i13.symbol=SYM7&pt.i1.comp.i17.symbol=SYM8&pt.i0.comp.i16.n=4&bl.i0.id=0&pt.i1.comp.i16.n=4&pt.i0.comp.i5.symbol=SYM4&pt.i1.comp.i7.symbol=SYM5&pt.i0.comp.i1.symbol=SYM3&pt.i1.comp.i36.multi=25&pt.i1.comp.i31.freespins=0&bl.i9.id=9&pt.i1.comp.i9.freespins=0&playercurrency=%26%23x20AC%3B&pt.i1.comp.i30.multi=25&pt.i0.comp.i25.n=4&pt.i1.comp.i28.n=4&pt.i1.comp.i32.freespins=0&pt.i0.comp.i9.freespins=0&credit=500000&pt.i0.comp.i5.type=betline&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=0&pt.i0.comp.i25.type=scatter&bl.i1.reelset=ALL&pt.i1.comp.i18.symbol=SYM9&pt.i1.comp.i12.symbol=SYM7&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i0.comp.i26.freespins=30&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=80&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=4&pt.i1.comp.i33.freespins=0&pt.i1.comp.i17.n=5&pt.i0.comp.i23.type=betline&pt.i1.comp.i17.freespins=0&pt.i1.comp.i26.multi=0&pt.i1.comp.i32.multi=500&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM3&pt.i1.comp.i29.multi=500&pt.i0.comp.i25.freespins=20&pt.i0.comp.i26.n=5&pt.i0.comp.i27.symbol=SYM2&pt.i1.comp.i29.n=5&pt.i0.comp.i23.multi=80&bl.i2.line=2%2C2%2C2%2C2%2C2&pt.i1.comp.i34.symbol=SYM12&pt.i1.comp.i28.multi=80&pt.i1.comp.i33.multi=25&pt.i1.comp.i18.freespins=0&pt.i0.comp.i14.n=5&pt.i0.comp.i0.multi=25&bl.i6.reelset=ALL&pt.i0.comp.i19.multi=30&pt.i1.comp.i18.n=3&pt.i1.comp.i33.type=betline&pt.i0.comp.i12.freespins=0&pt.i0.comp.i24.multi=0&pt.i0.comp.i19.symbol=SYM9&bl.i6.coins=1&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i1.comp.i36.type=betline&pt.i0.comp.i4.multi=70&pt.i0.comp.i15.symbol=SYM8&pt.i1.comp.i14.multi=100&pt.i0.comp.i22.multi=30&pt.i1.comp.i19.type=betline&pt.i0.comp.i11.symbol=SYM6&pt.i1.comp.i27.multi=25&bl.i0.reelset=ALL&pt.i0.comp.i16.freespins=0&pt.i1.comp.i6.freespins=0&pt.i1.comp.i29.symbol=SYM13&pt.i1.comp.i22.n=4&pt.i0.comp.i4.freespins=0&pt.i1.comp.i25.symbol=SYM0&bl.i3.reelset=ALL&pt.i1.comp.i24.type=scatter&pt.i0.comp.i19.n=4&pt.i0.comp.i2.symbol=SYM3&pt.i0.comp.i20.type=betline&pt.i0.comp.i6.symbol=SYM5&pt.i1.comp.i11.n=5&pt.i1.comp.i34.n=4&pt.i0.comp.i5.n=5&pt.i1.comp.i2.symbol=SYM3&pt.i0.comp.i3.type=betline&pt.i1.comp.i19.multi=30&pt.i1.comp.i6.symbol=SYM5&pt.i0.comp.i27.multi=0&pt.i0.comp.i9.multi=15&pt.i0.comp.i22.symbol=SYM10&pt.i0.comp.i26.symbol=SYM0&pt.i1.comp.i19.freespins=0&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&pt.i1.comp.i35.multi=500&pt.i1.comp.i4.freespins=0&pt.i1.comp.i12.type=betline&pt.i1.comp.i36.symbol=SYM1&pt.i1.comp.i21.symbol=SYM10&pt.i1.comp.i23.n=5&pt.i1.comp.i32.symbol=SYM11&bl.i8.id=8&pt.i0.comp.i16.multi=50&pt.i1.comp.i37.freespins=0&bl.i6.line=2%2C2%2C1%2C2%2C2&pt.i1.comp.i35.n=5&pt.i1.comp.i9.multi=15&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=500&pt.i0.comp.i6.n=3&pt.i1.comp.i12.n=3&pt.i1.comp.i3.type=betline&pt.i1.comp.i10.freespins=0&pt.i1.comp.i28.type=betline&pt.i1.comp.i20.multi=80&pt.i0.comp.i27.freespins=0&pt.i1.comp.i24.n=3&pt.i1.comp.i27.type=betline&pt.i1.comp.i2.type=betline&pt.i0.comp.i2.freespins=0&pt.i1.comp.i38.multi=500&pt.i0.comp.i7.n=4&pt.i0.comp.i11.multi=200&pt.i1.comp.i14.symbol=SYM7&pt.i0.comp.i7.type=betline&pt.i0.comp.i17.n=5&bl.i6.id=6&pt.i1.comp.i13.n=4&pt.i1.comp.i36.n=3&pt.i0.comp.i8.freespins=0&pt.i1.comp.i4.multi=70&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=100&pt.i1.comp.i7.multi=60&bl.i7.id=7&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=15&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=1&pt.i1.comp.i5.symbol=SYM4&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM10&playforfun=false&pt.i1.comp.i25.n=4&pt.i0.comp.i2.type=betline&pt.i1.comp.i20.type=betline&pt.i1.comp.i22.multi=30&pt.i0.comp.i8.n=5&pt.i1.comp.i22.freespins=0&pt.i0.comp.i11.type=betline&pt.i1.comp.i35.freespins=0&pt.i0.comp.i18.n=3&pt.i1.comp.i14.n=5&pt.i1.comp.i16.multi=50&pt.i1.comp.i37.n=4&pt.i1.comp.i15.freespins=0&pt.i0.comp.i27.type=bonus&pt.i1.comp.i28.freespins=0&pt.i0.comp.i7.symbol=SYM5&pt.i1.comp.i0.freespins=0&gameServerVersion=1.0.0&historybutton=false&bl.i5.id=5&pt.i0.comp.i18.symbol=SYM9&pt.i0.comp.i12.multi=10&pt.i1.comp.i14.freespins=0&bl.i3.coins=1&pt.i0.comp.i12.symbol=SYM7&pt.i0.comp.i14.symbol=SYM7&pt.i1.comp.i13.freespins=0&pt.i0.comp.i14.type=betline&pt.i1.comp.i0.n=3&pt.i1.comp.i26.symbol=SYM0&pt.i1.comp.i31.symbol=SYM11&pt.i0.comp.i7.multi=60&jackpotcurrency=%26%23x20AC%3B&bl.i9.coins=1&pt.i1.comp.i37.type=betline&pt.i1.comp.i11.multi=200&pt.i1.comp.i30.n=3&pt.i0.comp.i1.n=4&pt.i0.comp.i20.n=5&pt.i1.comp.i3.symbol=SYM4&pt.i1.comp.i23.freespins=0&pt.i0.comp.i25.symbol=SYM0&pt.i0.comp.i26.type=scatter&pt.i0.comp.i9.type=betline&pt.i1.comp.i16.type=betline&pt.i1.comp.i20.symbol=SYM9&pt.i1.comp.i12.multi=10&pt.i1.comp.i1.n=4&pt.i1.comp.i11.freespins=0&pt.i0.comp.i9.symbol=SYM6&pt.i0.comp.i16.type=betline&bl.i3.line=0%2C1%2C2%2C1%2C0&bl.i4.reelset=ALL&bl.i4.coins=1&pt.i0.comp.i2.n=5&pt.i1.comp.i31.n=4&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&pt.i0.comp.i6.type=betline&pt.i1.comp.i2.freespins=0&pt.i1.comp.i25.freespins=20&bl.i9.reelset=ALL&pt.i1.comp.i10.multi=60&pt.i1.comp.i10.symbol=SYM6&pt.i1.comp.i2.n=5&pt.i1.comp.i20.n=5&pt.i1.comp.i24.freespins=10&pt.i1.comp.i32.type=betline&pt.i0.comp.i4.type=betline&pt.i1.comp.i26.freespins=30&pt.i1.comp.i1.type=betline&bl.i1.line=0%2C0%2C0%2C0%2C0&pt.i0.comp.i20.freespins=0&pt.i1.comp.i29.type=betline&pt.i1.comp.i32.n=5&pt.i0.comp.i3.n=3&pt.i1.comp.i6.type=betline&pt.i1.comp.i4.symbol=SYM4&pt.i1.comp.i38.freespins=0&bl.i8.line=1%2C0%2C0%2C0%2C1&pt.i0.comp.i24.symbol=SYM0&bl.i8.coins=1&pt.i1.comp.i37.symbol=SYM1&pt.i1.comp.i3.n=3&pt.i1.comp.i21.n=3&pt.i0.comp.i18.freespins=0&pt.i1.comp.i15.symbol=SYM8&pt.i1.comp.i3.freespins=0&bl.i4.id=4&bl.i7.coins=1&pt.i1.comp.i9.symbol=SYM6&pt.i0.comp.i3.symbol=SYM4&pt.i0.comp.i24.type=scatter&pt.i1.comp.i12.freespins=0&pt.i0.comp.i4.n=4&pt.i1.comp.i10.n=4&pt.i1.comp.i33.n=3';
                        case 'initfreespin':
                            $result_tmp[] = 'rs.i4.id=freespin_spreading&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM7%2CSYM9%2CSYM10&gameServerVersion=1.0.0&g4mode=false&freespins.win.coins=0&historybutton=false&rs.i0.r.i4.hold=false&next.rs=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&gamestate.history=basic&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=4&rs.i0.r.i1.syms=SYM5%2CSYM10%2CSYM3&rs.i2.r.i1.pos=25&game.win.cents=0&rs.i4.r.i4.pos=5&rs.i1.r.i3.hold=false&totalwin.coins=0&gamestate.current=freespin&freespins.initial=10&rs.i4.r.i0.pos=3&jackpotcurrency=%26%23x20AC%3B&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM7%2CSYM9%2CSYM10&rs.i0.r.i3.syms=SYM4%2CSYM9%2CSYM8&rs.i1.r.i1.syms=SYM10%2CSYM3%2CSYM6&rs.i1.r.i1.pos=6&rs.i3.r.i4.pos=26&freespins.win.cents=0&isJackpotWin=false&rs.i0.r.i0.pos=4&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=20&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9&rs.i0.r.i1.pos=39&rs.i4.r.i4.syms=SYM10%2CSYM8%2CSYM6&rs.i1.r.i3.syms=SYM8%2CSYM6%2CSYM10&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=1&rs.i2.id=basic&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM0%2CSYM6%2CSYM9&clientaction=initfreespin&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM4%2CSYM6%2CSYM3&rs.i2.r.i3.attention.i0=1&rs.i3.r.i2.hold=false&gameover=false&rs.i3.r.i3.pos=15&rs.i0.r.i3.pos=12&rs.i2.r.i1.attention.i0=0&rs.i4.r.i0.syms=SYM6%2CSYM9%2CSYM7&nextaction=freespin&rs.i2.nearwin=4%2C3&rs.i4.r.i2.pos=2&rs.i0.r.i2.syms=SYM8%2CSYM6%2CSYM9&game.win.amount=0.00&freespins.totalwin.cents=0&freespins.betlevel=1&rs.i4.r.i3.pos=4&playercurrency=%26%23x20AC%3B&rs.i2.r.i0.pos=74&rs.i4.r.i4.hold=false&current.rs.i0=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&rs.i2.r.i4.syms=SYM7%2CSYM10%2CSYM6&rs.i3.r.i2.syms=SYM8%2CSYM10%2CSYM5&rs.i4.r.i3.hold=false&rs.i0.id=freespin_regular&credit=498550&rs.i1.r.i4.pos=26&rs.i3.id=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&multiplier=1&rs.i2.r.i2.pos=3&freespins.denomination=5.000&freespins.totalwin.coins=0&freespins.total=10&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM4%2CSYM3%2CSYM6&rs.i2.r.i2.syms=SYM0%2CSYM9%2CSYM8&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=3&rs.i3.r.i3.syms=SYM6%2CSYM7%2CSYM4&bet.betlevel=1&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i4.r.i1.syms=SYM3%2CSYM5%2CSYM4&rs.i2.r.i4.pos=52&rs.i3.r.i0.syms=SYM10%2CSYM6%2CSYM9&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i4.r.i1.hold=false&freespins.wavecount=1&rs.i3.r.i2.pos=2&rs.i3.r.i3.hold=false&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM5%2CSYM8%2CSYM7&rs.i0.r.i2.pos=25&rs.i1.r.i2.syms=SYM4%2CSYM7%2CSYM3&rs.i1.r.i0.pos=1&totalwin.cents=0&rs.i2.r.i0.syms=SYM3%2CSYM6%2CSYM5&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM7%2CSYM0%2CSYM9&rs.i1.id=freespin_expanding&rs.i3.r.i4.syms=SYM5%2CSYM10%2CSYM9&rs.i3.r.i1.syms=SYM9%2CSYM3%2CSYM5&rs.i1.r.i4.hold=false&freespins.left=10&rs.i0.r.i4.pos=10&rs.i2.r.i2.attention.i0=0&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM9%2CSYM10%2CSYM8&rs.i3.r.i0.pos=3&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bet.denomination=5';
                            break;
                        case 'initbonus':
                            $pickWinMpl = $slotSettings->GetGameData($slotSettings->slotId . 'pickWinMpl');
                            $pickWin = $slotSettings->GetGameData($slotSettings->slotId . 'pickWin');
                            $reels = $slotSettings->GetGameData($slotSettings->slotId . 'pickReels');
                            $bsymPos = [];
                            $bsc = 0;
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == 2 ) 
                                    {
                                        $bsymPos[$bsc] = $p;
                                        $bsc++;
                                    }
                                }
                            }
                            $result_tmp[] = 'bonusitem.2.state=not_picked&gameServerVersion=1.0.0&g4mode=false&game.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&bonusitem.1.win=unknown&bonusitem.2.reel=4&bonusitem.1.row=' . $bsymPos[1] . '&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=initbonus&game.win.cents=0&bonusitem.1.reel=2&bonusitem.2.row=' . $bsymPos[2] . '&bonusitem.0.state=not_picked&totalwin.coins=0&bonusitem.2.win=unknown&credit=' . $balanceInCents . '&totalwin.cents=0&gamestate.current=bonus&gameover=false&totalbonuswin.coins=0&jackpotcurrency=%26%23x20AC%3B&multiplier=1&bonusitem.1.state=not_picked&bonusgame.coinvalue=0.05&gamestate.bonusid=wildwildwestbonusgame&isJackpotWin=false&gamestate.stack=basic%2Cbonus&bonuswin.cents=0&totalbonuswin.cents=0&nextaction=bonusaction&wavecount=1&nextactiontype=pickbonus&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&bonusitem.0.reel=0&bonusitem.0.row=' . $bsymPos[0] . '&game.win.amount=0&bonusitem.0.win=unknown&bonuswin.coins=0';
                            break;
                        case 'endbonus':
                            $result_tmp[] = 'gameServerVersion=1.0.0&g4mode=false&game.win.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&current.rs.i0=basic&next.rs=basic&gamestate.history=basic%2Cbonus&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=endbonus&game.win.cents=' . ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') * $slotSettings->CurrentDenomination * 100) . '&totalwin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') * $slotSettings->CurrentDenomination * 100) . '&gamestate.current=basic&gameover=true&jackpotcurrency=%26%23x20AC%3B&multiplier=1&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&wavecount=1&gamesoundurl=&game.win.amount=' . ($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') * $slotSettings->CurrentDenomination * 100) . '';
                            break;
                        case 'bonusaction':
                            $pickWinMpl = $slotSettings->GetGameData($slotSettings->slotId . 'pickWinMpl');
                            $pickWin = $slotSettings->GetGameData($slotSettings->slotId . 'pickWin');
                            $reels = $slotSettings->GetGameData($slotSettings->slotId . 'pickReels');
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'pickBet');
                            $picked = explode(',', $postData['wildwildwest_bonus_pick']);
                            if( $picked[0] == 4 ) 
                            {
                                $picked[0] = 2;
                            }
                            else if( $picked[0] == 2 ) 
                            {
                                $picked[0] = 1;
                            }
                            $bsymPos = [];
                            $bsymWins = [
                                rand(5, 50) * $allbet, 
                                rand(5, 50) * $allbet, 
                                rand(5, 50) * $allbet
                            ];
                            $bsymPicked = [
                                'not_picked', 
                                'not_picked', 
                                'not_picked'
                            ];
                            $bsymWinsStr = [
                                '&bonusitem.0.win=' . $bsymWins[0] . '&bonusitem.0.state=not_picked', 
                                '&bonusitem.1.win=' . $bsymWins[1] . '&bonusitem.1.state=not_picked', 
                                '&bonusitem.2.win=' . $bsymWins[2] . '&bonusitem.2.state=not_picked'
                            ];
                            $bsymWinsStr[$picked[0]] = '&bonusitem.' . $picked[0] . '.win=' . $pickWin . '&bonusitem.' . $picked[0] . '.state=picked';
                            for( $r = 1; $r <= 5; $r++ ) 
                            {
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    if( $reels['reel' . $r][$p] == 2 ) 
                                    {
                                        $bsymPos[] = $p;
                                    }
                                }
                            }
                            $result_tmp[] = 'bonusitem.2.state=not_picked&gameServerVersion=1.0.0&g4mode=false&game.win.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&bonusitem.1.win=150&bonusitem.2.reel=4&bonusitem.1.row=' . $bsymPos[1] . '&gamestate.history=basic%2Cbonus&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=bonusaction&game.win.cents=750&bonusitem.1.reel=2&bonusitem.2.row=' . $bsymPos[2] . '&bonusitem.0.state=not_picked&totalwin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&bonusitem.2.win=250&credit=' . $balanceInCents . '&totalwin.cents=750&gamestate.current=bonus&gameover=false&totalbonuswin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&jackpotcurrency=%26%23x20AC%3B&multiplier=1&bonusitem.1.state=picked&bonusgame.coinvalue=0.05&gamestate.bonusid=wildwildwestbonusgame&isJackpotWin=false&gamestate.stack=basic%2Cbonus&bonuswin.cents=750&totalbonuswin.cents=750&nextaction=endbonus&wavecount=1&gamesoundurl=&bonusitem.0.reel=0&bonusitem.0.row=' . $bsymPos[0] . '&game.win.amount=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '&bonusgameover=true&bonusitem.0.win=50&bonuswin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') . '' . implode('', $bsymWinsStr);
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
                            $lines = 10;
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Denom', $postData['bet_denomination']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $slotSettings->SetGameData($slotSettings->slotId . 'ReelsType', 'basic');
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $postData['bet_denomination'] = $slotSettings->GetGameData($slotSettings->slotId . 'Denom');
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $slotSettings->GetGameData($slotSettings->slotId . 'Bet');
                                $allbet = $betline * $lines;
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
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
                                    '1', 
                                    '11', 
                                    '12', 
                                    '13'
                                ];
                                $scatter = '0';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent'], $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType'));
                                $reelsTmp = $reels;
                                $featureStr = '';
                                $randomwildsactive = false;
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $randomwildsactive = true;
                                }
                                $slotSettings->slotFreeMpl = 1;
                                if( $randomwildsactive ) 
                                {
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') == 'freespin_spreading' ) 
                                    {
                                        $wsym = 'SYM13';
                                        $wsym0 = '13';
                                        $spreadingWildsArr = [];
                                        $rReel = rand(2, 4);
                                        $rRow = rand(0, 2);
                                        $reels['reel' . $rReel][$rRow] = '13';
                                        $reelsTmp['reel' . $rReel][$rRow] = '13';
                                        for( $r = 2; $r <= 4; $r++ ) 
                                        {
                                            if( $reels['reel' . $r][0] == '13' || $reels['reel' . $r][1] == '13' || $reels['reel' . $r][2] == '13' ) 
                                            {
                                                if( $reels['reel' . $r][0] == '13' ) 
                                                {
                                                    $startSpeadngSym = [
                                                        $r, 
                                                        0
                                                    ];
                                                    $spreadingWildsArr = [
                                                        [
                                                            $r, 
                                                            1
                                                        ], 
                                                        [
                                                            $r + 1, 
                                                            0
                                                        ], 
                                                        [
                                                            $r - 1, 
                                                            0
                                                        ]
                                                    ];
                                                }
                                                if( $reels['reel' . $r][1] == '13' ) 
                                                {
                                                    $startSpeadngSym = [
                                                        $r, 
                                                        1
                                                    ];
                                                    $spreadingWildsArr = [
                                                        [
                                                            $r, 
                                                            0
                                                        ], 
                                                        [
                                                            $r, 
                                                            2
                                                        ], 
                                                        [
                                                            $r + 1, 
                                                            1
                                                        ], 
                                                        [
                                                            $r - 1, 
                                                            1
                                                        ]
                                                    ];
                                                }
                                                if( $reels['reel' . $r][2] == '13' ) 
                                                {
                                                    $startSpeadngSym = [
                                                        $r, 
                                                        2
                                                    ];
                                                    $spreadingWildsArr = [
                                                        [
                                                            $r, 
                                                            1
                                                        ], 
                                                        [
                                                            $r + 1, 
                                                            2
                                                        ], 
                                                        [
                                                            $r - 1, 
                                                            2
                                                        ]
                                                    ];
                                                }
                                                break;
                                            }
                                        }
                                        $symArr = [];
                                        $spreadCnt = rand(2, 3);
                                        shuffle($spreadingWildsArr);
                                        for( $ii = 0; $ii < $spreadCnt; $ii++ ) 
                                        {
                                            if( isset($spreadingWildsArr[$ii]) ) 
                                            {
                                                $symArr[$ii] = $spreadingWildsArr[$ii];
                                            }
                                        }
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') == 'freespin_regular' ) 
                                    {
                                        $wsym = 'SYM1';
                                        $wsym0 = '1';
                                        $spreadingWildsArr = [
                                            [
                                                1, 
                                                0
                                            ], 
                                            [
                                                1, 
                                                1
                                            ], 
                                            [
                                                1, 
                                                2
                                            ], 
                                            [
                                                2, 
                                                0
                                            ], 
                                            [
                                                2, 
                                                1
                                            ], 
                                            [
                                                2, 
                                                2
                                            ], 
                                            [
                                                3, 
                                                0
                                            ], 
                                            [
                                                3, 
                                                1
                                            ], 
                                            [
                                                3, 
                                                2
                                            ], 
                                            [
                                                4, 
                                                0
                                            ], 
                                            [
                                                4, 
                                                1
                                            ], 
                                            [
                                                4, 
                                                2
                                            ], 
                                            [
                                                5, 
                                                0
                                            ], 
                                            [
                                                5, 
                                                1
                                            ], 
                                            [
                                                5, 
                                                2
                                            ]
                                        ];
                                        $symArr = [];
                                        $spreadCnt = rand(1, 5);
                                        shuffle($spreadingWildsArr);
                                        for( $jj = 0; $jj < $spreadCnt; $jj++ ) 
                                        {
                                            $symArr[$jj] = $spreadingWildsArr[$jj];
                                        }
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') == 'freespin_expanding' ) 
                                    {
                                        $wsym = 'SYM11';
                                        $wsym0 = '11';
                                        $spreadingWildsArr = [
                                            [
                                                1, 
                                                1
                                            ], 
                                            [
                                                2, 
                                                1
                                            ], 
                                            [
                                                3, 
                                                1
                                            ], 
                                            [
                                                4, 
                                                1
                                            ], 
                                            [
                                                5, 
                                                1
                                            ]
                                        ];
                                        $symArr = [];
                                        $spreadCnt = rand(1, 2);
                                        shuffle($spreadingWildsArr);
                                        for( $r = 0; $r < $spreadCnt; $r++ ) 
                                        {
                                            $sew = $spreadingWildsArr[$r];
                                            $symArr[] = [
                                                $sew[0], 
                                                1
                                            ];
                                            $symArr[] = [
                                                $sew[0], 
                                                0
                                            ];
                                            $symArr[] = [
                                                $sew[0], 
                                                2
                                            ];
                                        }
                                    }
                                    if( $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') == 'freespin_multiplier' ) 
                                    {
                                        $spreadingWildsArr = [
                                            [
                                                1, 
                                                0
                                            ], 
                                            [
                                                1, 
                                                1
                                            ], 
                                            [
                                                1, 
                                                2
                                            ], 
                                            [
                                                2, 
                                                0
                                            ], 
                                            [
                                                2, 
                                                1
                                            ], 
                                            [
                                                2, 
                                                2
                                            ], 
                                            [
                                                3, 
                                                0
                                            ], 
                                            [
                                                3, 
                                                1
                                            ], 
                                            [
                                                3, 
                                                2
                                            ], 
                                            [
                                                4, 
                                                0
                                            ], 
                                            [
                                                4, 
                                                1
                                            ], 
                                            [
                                                4, 
                                                2
                                            ], 
                                            [
                                                5, 
                                                0
                                            ], 
                                            [
                                                5, 
                                                1
                                            ], 
                                            [
                                                5, 
                                                2
                                            ]
                                        ];
                                        $symArr = [];
                                        $spreadCnt = rand(1, 5);
                                        shuffle($spreadingWildsArr);
                                        for( $r = 0; $r < $spreadCnt; $r++ ) 
                                        {
                                            $symArr[$r] = $spreadingWildsArr[$r];
                                        }
                                        $wsym = 'SYM12';
                                        $wsym0 = '12';
                                        $slotSettings->slotFreeMpl = 2;
                                    }
                                    $ps = [
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ];
                                    $ps_ = 0;
                                    $ps_0 = 0;
                                    foreach( $symArr as $sw ) 
                                    {
                                        $reels['reel' . $sw[0]][$sw[1]] = $wsym0;
                                        $featureStr .= ('&rs.i0.r.i' . ($sw[0] - 1) . '.overlay.i' . $ps[$sw[0]] . '.row=' . $sw[1] . '&rs.i0.r.i' . ($sw[0] - 1) . '.overlay.i' . $ps[$sw[0]] . '.with=' . $wsym . '&rs.i0.r.i' . ($sw[0] - 1) . '.overlay.i' . $ps[$sw[0]] . '.pos=1');
                                        if( $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') == 'freespin_spreading' ) 
                                        {
                                            $featureStr .= ('&spread.from.i' . $ps_0 . '=' . ($startSpeadngSym[0] - 1) . '%2C' . $startSpeadngSym[1]);
                                            $featureStr .= ('&spread.to.i' . $ps_0 . '=' . ($sw[0] - 1) . '%2C' . $sw[1]);
                                        }
                                        $ps_0++;
                                        $ps[$sw[0]]++;
                                        $ps[0]++;
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.types.i0.multipliers=1&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.multipliers=1&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
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
                                                    $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $tmpWin . '&ws.i' . $winLineCount . '.pos.i0=0%2C' . ($linesId[$k][0] - 1) . '&ws.i' . $winLineCount . '.pos.i1=1%2C' . ($linesId[$k][1] - 1) . '&ws.i' . $winLineCount . '.pos.i2=2%2C' . ($linesId[$k][2] - 1) . '&ws.i' . $winLineCount . '.pos.i3=3%2C' . ($linesId[$k][3] - 1) . '&ws.i' . $winLineCount . '.pos.i4=4%2C' . ($linesId[$k][4] - 1) . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.types.i0.multipliers=1&ws.i' . $winLineCount . '.betline=' . $k . '&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($tmpWin * $slotSettings->CurrentDenomination * 100) . '';
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
                                $attStr = '';
                                $nearwin = [];
                                $scattersWin = 0;
                                $pickWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scattersCount2 = 0;
                                $scPos = [];
                                $scPos2 = [];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                        }
                                        if( $reels['reel' . $r][$p] == 2 ) 
                                        {
                                            $scattersCount2++;
                                            $scPos2[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                        }
                                    }
                                }
                                if( $scattersCount >= 3 ) 
                                {
                                    $scattersStr = '&ws.i0.types.i0.freespins=' . $slotSettings->slotFreeCount[$scattersCount] . '&ws.i0.reelset=basic&ws.i0.betline=null&ws.i0.types.i0.wintype=freespins&ws.i0.direction=none' . implode('', $scPos);
                                }
                                if( $scattersCount2 >= 3 ) 
                                {
                                    $pickWinMpl = rand(5, 50);
                                    $pickWin = $pickWinMpl * $allbet;
                                    $slotSettings->SetGameData($slotSettings->slotId . 'pickWinMpl', $pickWinMpl);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'pickWin', $pickWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'pickReels', $reels);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'pickBet', $allbet);
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '2' && $r > 3 ) 
                                            {
                                                $attStr .= ('&rs.i0.r.i' . ($r - 1) . '.attention.i0=' . $p . '');
                                                $nearwin[] = $r - 1;
                                                break;
                                            }
                                        }
                                    }
                                    $scattersStr = '&rs.i0.nearwin=' . implode('%2C', $nearwin) . '&gamestate.current=bonus&ws.i0.sym=SYM2&ws.i0.direction=none&gamestate.stack=basic%2Cbonus&ws.i0.types.i0.wintype=bonusgame&ws.i0.types.i0.bonusid=wildwildwestbonusgame' . implode('', $scPos) . $attStr;
                                }
                                $totalWin += ($scattersWin + $pickWin);
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
                                    else if( $scattersCount2 >= 3 && $winType != 'bonus' ) 
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
                            $freeState = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $reels = $reelsTmp;
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
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                $rsSets = [
                                    'freespin_expanding', 
                                    'freespin_multiplier', 
                                    'freespin_regular', 
                                    'freespin_spreading'
                                ];
                                $slotSettings->SetGameData($slotSettings->slotId . 'ReelsType', $rsSets[rand(0, 3)]);
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=freespin&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cfreespin&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=freespin&freespins.initial=' . $fs . '&freespins.win.coins=0&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.nearwin=' . implode('%2C', $nearwin) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $attStr;
                                $curReels .= $freeState;
                            }
                            $attStr = '';
                            $nearwin = [];
                            $nearwinCnt = 0;
                            if( $scattersCount >= 2 ) 
                            {
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $nearwinCnt >= 2 && $p == 0 ) 
                                        {
                                            $nearwin[] = $r - 1;
                                        }
                                        if( $reels['reel' . $r][$p] == '0' ) 
                                        {
                                            $attStr .= ('&rs.i0.r.i' . ($r - 1) . '.attention.i0=' . $p . '');
                                            $nearwinCnt++;
                                        }
                                    }
                                }
                                $attStr .= ('&rs.i0.nearwin=' . implode('%2C', $nearwin));
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
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') > 0 ) 
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
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $fsl = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"GameDenom":' . $slotSettings->GetGameData($slotSettings->slotId . 'GameDenom') . ',"ReelsType":"' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '","freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $result_tmp[] = 'rs.i0.r.i1.pos=11&gameServerVersion=1.0.0&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&rs.i0.r.i1.hold=false&current.rs.i0=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&rs.i0.r.i4.hold=false&next.rs=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i1.syms=SYM3%2CSYM9%2CSYM6&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM5%2CSYM3%2CSYM6&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=62&rs.i0.id=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=0&gamestate.current=' . $slotSettings->GetGameData($slotSettings->slotId . 'ReelsType') . '&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&freespins.multiplier=1&freespins.wavecount=1&multiplier=1&rs.i0.r.i3.pos=23&rs.i0.r.i4.pos=29&rs.i0.r.i0.syms=SYM4%2CSYM7%2CSYM9&rs.i0.r.i3.syms=SYM7%2CSYM8%2CSYM9&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=30&wavecount=1&gamesoundurl=&rs.i0.r.i2.syms=SYM4%2CSYM10%2CSYM7&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $curReels . $winString . $scattersStr . $featureStr . $attStr;
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
