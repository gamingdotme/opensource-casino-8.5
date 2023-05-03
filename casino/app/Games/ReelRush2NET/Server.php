<?php 
namespace VanguardLTE\Games\ReelRush2NET
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
                    $postData['freeMode'] = '';
                    if( $postData['action'] == 'freespin' ) 
                    {
                        $postData['slotEvent'] = 'freespin';
                        $postData['action'] = 'spin';
                    }
                    if( $postData['action'] == 'superfreespin' ) 
                    {
                        $postData['slotEvent'] = 'freespin';
                        $postData['action'] = 'spin';
                        $postData['freeMode'] = 'superfreespin';
                    }
                    if( $postData['action'] == 'respin' ) 
                    {
                        $postData['slotEvent'] = 'respin';
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
                    if( $postData['action'] == 'purchasestars' ) 
                    {
                        $postData['slotEvent'] = 'purchasestars';
                    }
                    if( $postData['action'] == 'gamble' ) 
                    {
                        $postData['slotEvent'] = 'gamble';
                    }
                    if( $postData['action'] == 'initfreespin' ) 
                    {
                        $postData['slotEvent'] = 'initfreespin';
                    }
                    if( $postData['action'] == 'startfreespins' ) 
                    {
                        $postData['slotEvent'] = 'startfreespins';
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
                            $slotSettings->SetGameData($slotSettings->slotId . 'Stars', 0);
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
                                $freeState = 'rs.i1.r.i0.syms=SYM5%2CSYM5%2CSYM9&ws.i6.sym=SYM10&rs.i0.r.i4.hold=false&gamestate.history=basic%2Cstart_freespins%2Cfreespin&ws.i4.betline=0&rs.i1.r.i2.hold=false&ws.i5.types.i0.cents=30&ws.i9.direction=left_to_right&game.win.cents=1530&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&ws.i9.reelset=freespin3&freespins.initial=0&ws.i8.types.i0.wintype=coins&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM4%2CSYM4%2CSYM5%2CSYM5%2CSYM9&ws.i6.reelset=freespin3&rs.i1.r.i1.pos=46&rs.i3.r.i4.pos=0&rs.i6.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i0.r.i0.pos=0&rs.i2.r.i3.pos=50&rs.i5.r.i0.pos=25&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&ws.i6.types.i0.cents=30&rs.i2.id=freespin2&rs.i4.r.i2.overlay.i0.row=0&rs.i6.r.i1.pos=0&game.win.coins=306&rs.i1.r.i0.hold=false&ws.i1.reelset=freespin3&clientaction=init&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM10%2CSYM10%2CSYM4%2CSYM4%2CSYM10&casinoID=netent&rs.i3.r.i2.hold=false&ws.i5.types.i0.coins=6&rs.i5.r.i1.syms=SYM13%2CSYM13%2CSYM1%2CSYM9%2CSYM9&rs.i0.r.i3.pos=0&rs.i4.r.i0.syms=SYM10%2CSYM10%2CSYM3%2CSYM3%2CSYM4&rs.i5.r.i3.pos=7&ws.i3.sym=SYM4&bl.i0.line=0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4&ws.i3.types.i0.coins=25&ws.i4.types.i0.wintype=coins&rs.i4.r.i2.pos=39&rs.i0.r.i2.syms=SYM3%2CSYM9%2CSYM9%2CSYM12%2CSYM12&game.win.amount=15.30&betlevel.all=' . implode('%2C', $slotSettings->Denominations) . '&ws.i7.betline=0&rs.i5.r.i2.hold=false&denomination.all=1%2C2%2C5%2C10%2C20&ws.i7.sym=SYM10&ws.i2.pos.i1=0%2C4&ws.i6.direction=left_to_right&rs.i2.r.i0.pos=15&ws.i2.pos.i0=1%2C1&ws.i0.reelset=freespin3&ws.i2.pos.i3=3%2C2&ws.i4.pos.i3=2%2C0&ws.i5.types.i0.wintype=coins&ws.i2.pos.i2=2%2C0&ws.i4.pos.i2=3%2C0&ws.i4.pos.i1=1%2C4&ws.i4.pos.i0=0%2C0&ws.i7.types.i0.wintype=coins&rs.i3.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&rs.i1.r.i4.pos=28&denomination.standard=5&rs.i3.id=superFreespin2&multiplier=1&ws.i8.reelset=freespin3&freespins.denomination=5.000&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&ws.i6.types.i0.coins=6&freespins.totalwin.coins=144&ws.i0.direction=left_to_right&freespins.total=0&gamestate.stack=basic%2Cfreespin&rs.i6.r.i2.pos=0&ws.i6.betline=0&rs.i1.r.i4.syms=SYM6&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i5.r.i2.syms=SYM12%2CSYM12%2CSYM1%2CSYM4%2CSYM4&rs.i5.r.i3.hold=false&bet.betlevel=1&rs.i4.r.i2.hold=false&rs.i4.r.i1.syms=SYM4%2CSYM4%2CSYM13%2CSYM13%2CSYM10&lastreelsetid=freespin3&rs.i2.r.i4.pos=33&rs.i3.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i4.r.i1.hold=false&ws.i2.types.i0.coins=25&rs.i3.r.i2.pos=0&ws.i2.sym=SYM4&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM5%2CSYM5%2CSYM13%2CSYM13%2CSYM3&rs.i0.r.i2.pos=0&ws.i9.types.i0.coins=6&rs.i6.r.i3.pos=0&ws.i1.betline=0&rs.i1.r.i0.pos=2&rs.i6.r.i3.hold=false&bl.i0.coins=20&ws.i2.types.i0.wintype=coins&rs.i2.r.i0.syms=SYM11%2CSYM9%2CSYM9%2CSYM13%2CSYM13&ws.i9.types.i0.cents=30&rs.i3.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i1.r.i4.hold=false&freespins.left=5&casinoconfiguration.FEATURE_SUPER_TOKENS_BUY_ENABLED=TRUE&ws.i9.betline=0&rs.i4.r.i1.pos=33&ws.i7.pos.i0=0%2C1&rs.i4.r.i2.syms=SYM13%2CSYM11%2CSYM11%2CSYM12%2CSYM12&bl.standard=0&ws.i3.reelset=freespin3&ws.i7.pos.i3=2%2C0&ws.i7.pos.i2=3%2C0&rs.i5.r.i3.syms=SYM5%2CSYM5%2CSYM9%2CSYM9%2CSYM8&ws.i7.pos.i1=1%2C4&rs.i3.r.i0.hold=false&rs.i6.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&ws.i5.direction=left_to_right&rs.i0.r.i3.hold=false&bet.denomination=5&rs.i5.r.i4.pos=64&rs.i4.id=freespin3&rs.i2.r.i1.hold=false&ws.i6.types.i0.wintype=coins&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=136&stars.unscaled=655&historybutton=false&ws.i2.direction=left_to_right&gameEventSetters.enabled=false&next.rs=freespin&ws.i8.types.i0.coins=6&ws.i2.types.i0.cents=125&rs.i1.r.i3.pos=15&rs.i0.r.i1.syms=SYM13%2CSYM1%2CSYM12%2CSYM10%2CSYM10&ws.i1.types.i0.coins=25&rs.i2.r.i1.pos=8&rs.i4.r.i4.pos=52&ws.i0.betline=0&rs.i1.r.i3.hold=false&totalwin.coins=306&rs.i5.r.i4.syms=SYM7%2CSYM13%2CSYM13&gamestate.current=freespin&rs.i4.r.i0.pos=4&ws.i3.direction=left_to_right&jackpotcurrency=%26%23x20AC%3B&bet.betlines=0&rs.i3.r.i1.hold=false&rs.i0.r.i3.syms=SYM11%2CSYM10%2CSYM10%2CSYM8%2CSYM8&rs.i1.r.i1.syms=SYM4%2CSYM4%2CSYM12&freespins.win.cents=680&isJackpotWin=false&rs.i6.r.i4.hold=false&ws.i8.betline=0&ws.i8.sym=SYM10&rs.i2.r.i3.hold=false&ws.i2.reelset=freespin3&freespins.betlines=0&rs.i4.r.i2.overlay.i0.with=SYM1&ws.i4.direction=left_to_right&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM6%2CSYM11%2CSYM11%2CSYM13%2CSYM13&rs.i1.r.i3.syms=SYM6%2CSYM13%2CSYM13&ws.i3.betline=0&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM4%2CSYM4%2CSYM12%2CSYM12%2CSYM10&ws.i1.types.i0.wintype=coins&openedpositions.thisspin=0&ws.i1.sym=SYM4&betlevel.standard=1&ws.i1.types.i0.cents=125&rs.i6.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&gameover=false&rs.i3.r.i3.pos=0&rs.i5.id=basic&rs.i6.r.i4.pos=0&ws.i9.pos.i0=0%2C1&ws.i8.types.i0.cents=30&ws.i9.pos.i2=1%2C4&rs.i5.r.i1.hold=false&ws.i1.direction=left_to_right&ws.i9.pos.i1=3%2C4&rs.i5.r.i4.hold=false&rs.i6.r.i2.hold=false&bl.i0.id=0&ws.i5.pos.i0=0%2C0&ws.i5.pos.i1=1%2C4&ws.i5.pos.i2=2%2C0&nextaction=freespin&ws.i5.pos.i3=3%2C1&ws.i9.pos.i3=2%2C0&ws.i5.reelset=freespin3&freespins.totalwin.cents=720&ws.i2.betline=0&ws.i0.pos.i3=3%2C2&freespins.betlevel=1&ws.i0.pos.i2=1%2C0&ws.i1.pos.i3=1%2C0&rs.i4.r.i3.pos=55&playercurrency=%26%23x20AC%3B&ws.i3.pos.i3=2%2C0&rs.i4.r.i4.hold=false&ws.i1.pos.i0=3%2C3&ws.i1.pos.i1=0%2C4&ws.i3.pos.i1=3%2C3&ws.i1.pos.i2=2%2C0&ws.i3.pos.i2=0%2C4&ws.i0.pos.i1=2%2C0&rs.i5.r.i0.syms=SYM12%2CSYM11%2CSYM11%2CSYM6%2CSYM6&ws.i0.pos.i0=0%2C4&ws.i3.pos.i0=1%2C1&rs.i2.r.i4.syms=SYM10%2CSYM10%2CSYM8%2CSYM8%2CSYM12&rs.i4.r.i3.hold=false&ws.i5.sym=SYM10&rs.i6.r.i0.hold=false&rs.i0.id=freespin&credit=' . $balanceInCents . '&ws.i0.types.i0.coins=25&rs.i2.r.i2.pos=24&ws.i9.sym=SYM10&last.rs=freespin&ws.i8.pos.i0=0%2C1&ws.i8.pos.i1=1%2C4&ws.i3.types.i0.wintype=coins&rs.i5.r.i1.pos=26&ws.i4.types.i0.cents=30&ws.i7.direction=left_to_right&openedpositions.total=12&ws.i0.sym=SYM4&ws.i6.pos.i2=1%2C4&rs.i6.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&ws.i6.pos.i3=2%2C0&ws.i6.pos.i0=0%2C0&casinoconfiguration.FEATURE_SUPER_TOKENS_GAMBLE_ENABLED=true&ws.i6.pos.i1=3%2C4&ws.i8.pos.i2=2%2C0&ws.i8.pos.i3=3%2C1&rs.i6.r.i1.hold=false&rs.i2.r.i2.syms=SYM9%2CSYM13%2CSYM13%2CSYM7%2CSYM7&rs.i1.r.i2.pos=38&rs.i3.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&ws.i0.types.i0.wintype=coins&ws.i4.reelset=freespin3&rs.i3.r.i4.hold=false&rs.i5.r.i0.hold=false&nearwinallowed=true&ws.i3.types.i0.cents=125&ws.i7.types.i0.coins=6&ws.i5.betline=0&rs.i6.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&freespins.wavecount=1&rs.i3.r.i3.hold=false&stars.total=131&rs.i6.r.i0.pos=0&rs.i1.r.i2.syms=SYM10%2CSYM1%2CSYM11%2CSYM11%2CSYM9&rs.i6.id=superFreespin&totalwin.cents=1530&rs.i5.r.i2.pos=3&ws.i7.reelset=freespin3&rs.i0.r.i0.hold=false&ws.i4.sym=SYM10&rs.i2.r.i3.syms=SYM13%2CSYM6%2CSYM6%2CSYM11%2CSYM11&restore=true&rs.i1.id=basic2&rs.i3.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i0.r.i4.pos=0&ws.i4.types.i0.coins=6&ws.i0.types.i0.cents=125&rs.i3.r.i0.pos=0&ws.i8.direction=left_to_right&rs.i2.r.i2.hold=false&ws.i9.types.i0.wintype=coins&wavecount=1&rs.i4.r.i2.overlay.i0.pos=39&ws.i7.types.i0.cents=30&rs.i1.r.i1.hold=false' . $freeState;
                            }
                            $result_tmp[] = 'rs.i4.id=freespin3&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM3%2CSYM3%2CSYM5%2CSYM5%2CSYM11&gameServerVersion=1.21.0&g4mode=false&stars.unscaled=0&historybutton=false&rs.i0.r.i4.hold=false&gameEventSetters.enabled=false&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i2.r.i1.pos=0&game.win.cents=0&rs.i4.r.i4.pos=0&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&rs.i1.r.i3.hold=false&totalwin.coins=0&rs.i5.r.i4.syms=SYM4%2CSYM4%2CSYM9%2CSYM9%2CSYM6&gamestate.current=basic&rs.i4.r.i0.pos=0&jackpotcurrency=%26%23x20AC%3B&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&rs.i0.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i1.r.i1.syms=SYM5%2CSYM5%2CSYM1%2CSYM6%2CSYM6&rs.i1.r.i1.pos=0&rs.i3.r.i4.pos=0&rs.i6.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&isJackpotWin=false&rs.i6.r.i4.hold=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=0&rs.i5.r.i0.pos=0&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i1.r.i3.syms=SYM10%2CSYM8%2CSYM8%2CSYM12%2CSYM12&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=freespin2&rs.i6.r.i1.pos=0&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&clientaction=init&openedpositions.thisspin=0&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&casinoID=netent&betlevel.standard=1&rs.i3.r.i2.hold=false&rs.i6.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&gameover=true&rs.i3.r.i3.pos=0&rs.i5.id=basic&rs.i5.r.i1.syms=SYM5%2CSYM5%2CSYM1%2CSYM6%2CSYM6&rs.i0.r.i3.pos=0&rs.i6.r.i4.pos=0&rs.i5.r.i1.hold=false&rs.i4.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&rs.i5.r.i4.hold=false&rs.i6.r.i2.hold=false&rs.i5.r.i3.pos=0&bl.i0.id=0&bl.i0.line=0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4&nextaction=spin&rs.i4.r.i2.pos=0&rs.i0.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&game.win.amount=0&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&rs.i5.r.i2.hold=false&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&rs.i4.r.i3.pos=0&playercurrency=%26%23x20AC%3B&rs.i2.r.i0.pos=0&rs.i4.r.i4.hold=false&rs.i5.r.i0.syms=SYM3%2CSYM3%2CSYM5%2CSYM5%2CSYM11&rs.i2.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i3.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&rs.i4.r.i3.hold=false&rs.i6.r.i0.hold=false&rs.i0.id=freespin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=0&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&rs.i3.id=superFreespin2&multiplier=1&rs.i2.r.i2.pos=0&rs.i5.r.i1.pos=0&openedpositions.total=0&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&rs.i6.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&casinoconfiguration.FEATURE_SUPER_TOKENS_GAMBLE_ENABLED=true&rs.i6.r.i2.pos=0&rs.i1.r.i4.syms=SYM4%2CSYM4%2CSYM9%2CSYM9%2CSYM6&rs.i6.r.i1.hold=false&rs.i2.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=0&rs.i5.r.i2.syms=SYM6%2CSYM6%2CSYM13%2CSYM13%2CSYM11&rs.i3.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i5.r.i3.hold=false&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i5.r.i0.hold=false&nearwinallowed=true&rs.i4.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i4.r.i1.hold=false&rs.i6.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i3.r.i2.pos=0&rs.i3.r.i3.hold=false&stars.total=0&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i6.r.i0.pos=0&rs.i0.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM6%2CSYM6%2CSYM13%2CSYM13%2CSYM11&rs.i6.r.i3.pos=0&rs.i1.r.i0.pos=0&rs.i6.id=superFreespin&totalwin.cents=0&rs.i6.r.i3.hold=false&bl.i0.coins=20&rs.i2.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&rs.i5.r.i2.pos=0&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&restore=false&rs.i1.id=basic2&rs.i3.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i3.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i1.r.i4.hold=false&casinoconfiguration.FEATURE_SUPER_TOKENS_BUY_ENABLED=TRUE&rs.i0.r.i4.pos=0&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&bl.standard=0&rs.i3.r.i0.pos=0&rs.i5.r.i3.syms=SYM10%2CSYM8%2CSYM8%2CSYM12%2CSYM12&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&wavecount=1&rs.i6.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&rs.i5.r.i4.pos=0';
                            break;
                        case 'paytable':
                            $result_tmp[] = 'pt.i0.comp.i19.symbol=SYM8&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i0.comp.i32.type=betline&pt.i0.comp.i35.multi=10&pt.i0.comp.i29.type=betline&pt.i0.comp.i4.multi=50&pt.i0.comp.i15.symbol=SYM7&pt.i0.comp.i17.symbol=SYM7&pt.i0.comp.i5.freespins=0&pt.i1.comp.i14.multi=30&pt.i0.comp.i22.multi=6&pt.i0.comp.i23.n=5&pt.i1.comp.i34.multi=5&pt.i1.comp.i19.type=betline&pt.i0.comp.i11.symbol=SYM5&pt.i0.comp.i13.symbol=SYM6&pt.i1.comp.i8.type=betline&pt.i1.comp.i4.n=4&pt.i1.comp.i27.multi=1&pt.i0.comp.i15.multi=5&pt.i1.comp.i27.symbol=SYM11&bl.i0.reelset=ALL&pt.i0.comp.i16.freespins=0&pt.i0.comp.i28.multi=5&pt.i1.comp.i6.freespins=0&pt.i1.comp.i29.symbol=SYM11&pt.i1.comp.i29.freespins=0&pt.i1.comp.i22.n=4&pt.i1.comp.i30.symbol=SYM12&pt.i1.comp.i3.multi=10&pt.i0.comp.i11.n=5&pt.i0.comp.i4.freespins=0&pt.i1.comp.i23.symbol=SYM9&pt.i1.comp.i25.symbol=SYM10&pt.i0.comp.i30.freespins=0&pt.i1.comp.i24.type=betline&pt.i0.comp.i19.n=4&pt.i0.id=basic&pt.i0.comp.i1.type=betline&pt.i0.comp.i34.n=4&pt.i1.comp.i10.type=betline&pt.i0.comp.i34.type=betline&pt.i0.comp.i2.symbol=SYM1&pt.i0.comp.i4.symbol=SYM3&pt.i1.comp.i5.freespins=0&pt.i0.comp.i20.type=betline&pt.i1.comp.i8.symbol=SYM4&pt.i1.comp.i19.n=4&pt.i0.comp.i17.freespins=0&pt.i0.comp.i6.symbol=SYM4&pt.i0.comp.i8.symbol=SYM4&pt.i0.comp.i0.symbol=SYM1&pt.i1.comp.i11.n=5&pt.i1.comp.i34.n=4&pt.i0.comp.i5.n=5&pt.i1.comp.i2.symbol=SYM1&pt.i0.comp.i3.type=betline&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=15&pt.i1.id=freespin&pt.i1.comp.i19.multi=10&pt.i1.comp.i6.symbol=SYM4&pt.i1.comp.i34.freespins=0&pt.i0.comp.i27.multi=1&pt.i0.comp.i9.multi=7&pt.i0.comp.i22.symbol=SYM9&pt.i0.comp.i26.symbol=SYM10&pt.i1.comp.i19.freespins=0&pt.i1.comp.i34.type=betline&pt.i0.comp.i24.n=3&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&clientaction=paytable&pt.i1.comp.i35.multi=10&pt.i1.comp.i27.freespins=0&pt.i1.comp.i4.freespins=0&pt.i1.comp.i12.type=betline&pt.i1.comp.i5.n=5&pt.i1.comp.i8.multi=100&pt.i1.comp.i21.symbol=SYM9&pt.i1.comp.i23.n=5&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=0&pt.i1.comp.i32.symbol=SYM12&pt.i0.comp.i16.multi=10&pt.i0.comp.i21.multi=1&pt.i1.comp.i13.multi=15&pt.i0.comp.i12.n=3&pt.i0.comp.i35.n=5&pt.i0.comp.i13.type=betline&pt.i1.comp.i35.n=5&pt.i1.comp.i9.multi=7&bl.i0.line=0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4%2C0%2F1%2F2%2F3%2F4&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=200&pt.i1.comp.i7.freespins=0&pt.i0.comp.i31.freespins=0&pt.i0.comp.i3.multi=10&pt.i0.comp.i6.n=3&pt.i1.comp.i22.type=betline&pt.i1.comp.i12.n=3&pt.i1.comp.i3.type=betline&pt.i0.comp.i21.n=3&pt.i1.comp.i10.freespins=0&pt.i1.comp.i28.type=betline&pt.i0.comp.i34.symbol=SYM13&pt.i1.comp.i6.n=3&pt.i0.comp.i29.n=5&pt.i1.comp.i31.type=betline&pt.i1.comp.i20.multi=20&pt.i0.comp.i27.freespins=0&pt.i0.comp.i34.freespins=0&pt.i1.comp.i24.n=3&pt.i0.comp.i10.type=betline&pt.i0.comp.i35.freespins=0&pt.i1.comp.i11.symbol=SYM5&pt.i1.comp.i27.type=betline&pt.i1.comp.i2.type=betline&pt.i0.comp.i2.freespins=0&pt.i0.comp.i5.multi=200&pt.i0.comp.i7.n=4&pt.i0.comp.i32.n=5&pt.i1.comp.i1.freespins=0&pt.i0.comp.i11.multi=30&pt.i1.comp.i14.symbol=SYM6&pt.i1.comp.i16.symbol=SYM7&pt.i1.comp.i23.multi=12&pt.i0.comp.i7.type=betline&pt.i1.comp.i4.type=betline&pt.i0.comp.i17.n=5&pt.i1.comp.i18.multi=5&pt.i0.comp.i29.multi=10&pt.i1.comp.i13.n=4&pt.i0.comp.i8.freespins=0&pt.i1.comp.i26.type=betline&pt.i1.comp.i4.multi=50&pt.i0.comp.i8.multi=100&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&pt.i0.comp.i34.multi=5&pt.i0.comp.i1.freespins=0&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=30&pt.i1.comp.i7.multi=25&pt.i0.comp.i22.n=4&pt.i0.comp.i28.symbol=SYM11&pt.i1.comp.i17.type=betline&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=8&pt.i1.comp.i0.symbol=SYM1&playercurrencyiso=' . $slotSettings->slotCurrency . '&pt.i1.comp.i7.n=4&pt.i1.comp.i5.multi=200&pt.i1.comp.i5.symbol=SYM3&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM9&pt.i0.comp.i21.type=betline&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i1.comp.i25.n=4&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=5&pt.i0.comp.i2.type=betline&pt.i0.comp.i13.multi=15&pt.i1.comp.i20.type=betline&pt.i0.comp.i17.type=betline&pt.i0.comp.i30.type=betline&pt.i1.comp.i22.symbol=SYM9&pt.i1.comp.i30.freespins=0&pt.i1.comp.i22.multi=6&bl.i0.coins=20&pt.i0.comp.i8.n=5&pt.i0.comp.i10.n=4&pt.i0.comp.i33.n=3&pt.i1.comp.i6.multi=8&pt.i1.comp.i22.freespins=0&pt.i0.comp.i11.type=betline&pt.i1.comp.i19.symbol=SYM8&pt.i1.comp.i35.freespins=0&pt.i0.comp.i18.n=3&pt.i0.comp.i22.freespins=0&pt.i0.comp.i20.symbol=SYM8&pt.i0.comp.i15.freespins=0&pt.i1.comp.i14.n=5&pt.i1.comp.i16.multi=10&pt.i0.comp.i31.symbol=SYM12&pt.i1.comp.i15.freespins=0&pt.i0.comp.i27.type=betline&pt.i1.comp.i28.freespins=0&pt.i0.comp.i28.freespins=0&pt.i0.comp.i0.n=3&pt.i0.comp.i7.symbol=SYM4&pt.i1.comp.i21.multi=1&pt.i1.comp.i30.type=betline&pt.i1.comp.i0.freespins=0&pt.i0.comp.i0.type=betline&pt.i1.comp.i0.multi=10&gameServerVersion=1.21.0&g4mode=false&pt.i1.comp.i8.n=5&pt.i0.comp.i25.multi=6&historybutton=false&pt.i0.comp.i16.symbol=SYM7&pt.i1.comp.i21.freespins=0&pt.i0.comp.i1.multi=50&pt.i0.comp.i27.n=3&pt.i0.comp.i18.symbol=SYM8&pt.i1.comp.i9.type=betline&pt.i0.comp.i12.multi=7&pt.i0.comp.i32.multi=10&pt.i1.comp.i24.multi=1&pt.i1.comp.i14.freespins=0&pt.i1.comp.i23.type=betline&pt.i1.comp.i26.n=5&pt.i0.comp.i12.symbol=SYM6&pt.i0.comp.i14.symbol=SYM6&pt.i1.comp.i13.freespins=0&pt.i1.comp.i28.symbol=SYM11&pt.i0.comp.i14.type=betline&pt.i1.comp.i17.multi=20&pt.i0.comp.i18.multi=5&pt.i1.comp.i0.n=3&pt.i1.comp.i26.symbol=SYM10&pt.i0.comp.i33.type=betline&pt.i1.comp.i31.symbol=SYM12&pt.i0.comp.i7.multi=25&pt.i1.comp.i33.symbol=SYM13&pt.i1.comp.i35.type=betline&pt.i0.comp.i9.n=3&pt.i0.comp.i30.n=3&pt.i1.comp.i21.type=betline&jackpotcurrency=%26%23x20AC%3B&pt.i0.comp.i28.type=betline&pt.i1.comp.i31.multi=5&pt.i1.comp.i18.type=betline&pt.i0.comp.i10.symbol=SYM5&pt.i0.comp.i15.n=3&pt.i0.comp.i21.symbol=SYM9&pt.i0.comp.i31.type=betline&pt.i1.comp.i15.n=3&isJackpotWin=false&pt.i1.comp.i20.freespins=0&pt.i1.comp.i7.type=betline&pt.i1.comp.i11.multi=30&pt.i1.comp.i30.n=3&pt.i0.comp.i1.n=4&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=20&pt.i0.comp.i20.n=5&pt.i0.comp.i29.symbol=SYM11&pt.i1.comp.i3.symbol=SYM3&pt.i0.comp.i17.multi=20&pt.i1.comp.i23.freespins=0&pt.i1.comp.i25.type=betline&pt.i1.comp.i9.n=3&pt.i0.comp.i25.symbol=SYM10&pt.i0.comp.i26.type=betline&pt.i0.comp.i28.n=4&pt.i0.comp.i9.type=betline&pt.i0.comp.i2.multi=200&pt.i1.comp.i27.n=3&pt.i0.comp.i0.freespins=0&pt.i1.comp.i16.type=betline&pt.i1.comp.i25.multi=6&pt.i0.comp.i33.multi=1&pt.i1.comp.i16.freespins=0&pt.i1.comp.i20.symbol=SYM8&pt.i1.comp.i12.multi=7&pt.i0.comp.i29.freespins=0&pt.i1.comp.i1.n=4&pt.i1.comp.i5.type=betline&pt.i1.comp.i35.symbol=SYM13&pt.i1.comp.i11.freespins=0&pt.i1.comp.i24.symbol=SYM10&pt.i0.comp.i31.n=4&pt.i0.comp.i9.symbol=SYM5&pt.i1.comp.i13.symbol=SYM6&pt.i1.comp.i17.symbol=SYM7&pt.i0.comp.i16.n=4&bl.i0.id=0&pt.i0.comp.i16.type=betline&pt.i1.comp.i16.n=4&pt.i0.comp.i5.symbol=SYM3&pt.i1.comp.i7.symbol=SYM4&pt.i0.comp.i2.n=5&pt.i0.comp.i35.type=betline&pt.i0.comp.i1.symbol=SYM1&pt.i1.comp.i31.n=4&pt.i1.comp.i31.freespins=0&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&pt.i0.comp.i6.type=betline&pt.i1.comp.i9.freespins=0&pt.i1.comp.i2.freespins=0&playercurrency=%26%23x20AC%3B&pt.i0.comp.i35.symbol=SYM13&pt.i1.comp.i25.freespins=0&pt.i0.comp.i33.symbol=SYM13&pt.i1.comp.i30.multi=1&pt.i0.comp.i25.n=4&pt.i1.comp.i10.multi=15&pt.i1.comp.i10.symbol=SYM5&pt.i1.comp.i28.n=4&pt.i1.comp.i32.freespins=0&pt.i0.comp.i9.freespins=0&pt.i1.comp.i2.n=5&pt.i1.comp.i20.n=5&credit=500000&pt.i0.comp.i5.type=betline&pt.i1.comp.i24.freespins=0&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=12&pt.i0.comp.i25.type=betline&pt.i1.comp.i32.type=betline&pt.i1.comp.i18.symbol=SYM8&pt.i0.comp.i31.multi=5&pt.i1.comp.i12.symbol=SYM6&pt.i0.comp.i4.type=betline&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i1.comp.i26.freespins=0&pt.i0.comp.i26.freespins=0&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=50&pt.i1.comp.i1.type=betline&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=4&pt.i0.comp.i20.freespins=0&pt.i1.comp.i33.freespins=0&pt.i0.comp.i33.freespins=0&pt.i1.comp.i17.n=5&pt.i0.comp.i23.type=betline&pt.i1.comp.i29.type=betline&pt.i0.comp.i30.symbol=SYM12&pt.i0.comp.i32.symbol=SYM12&pt.i1.comp.i32.n=5&pt.i0.comp.i3.n=3&pt.i1.comp.i17.freespins=0&pt.i1.comp.i26.multi=12&pt.i1.comp.i32.multi=10&pt.i1.comp.i6.type=betline&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM1&pt.i1.comp.i29.multi=10&pt.i0.comp.i25.freespins=0&pt.i1.comp.i4.symbol=SYM3&pt.i0.comp.i24.symbol=SYM10&pt.i0.comp.i26.n=5&pt.i0.comp.i27.symbol=SYM11&pt.i0.comp.i32.freespins=0&pt.i1.comp.i29.n=5&pt.i0.comp.i23.multi=12&pt.i1.comp.i3.n=3&pt.i0.comp.i30.multi=1&pt.i1.comp.i21.n=3&pt.i1.comp.i34.symbol=SYM13&pt.i1.comp.i28.multi=5&pt.i0.comp.i18.freespins=0&pt.i1.comp.i33.multi=1&pt.i1.comp.i15.symbol=SYM7&pt.i1.comp.i18.freespins=0&pt.i1.comp.i3.freespins=0&pt.i0.comp.i14.n=5&pt.i0.comp.i0.multi=10&pt.i1.comp.i9.symbol=SYM5&pt.i0.comp.i19.multi=10&pt.i0.comp.i3.symbol=SYM3&pt.i0.comp.i24.type=betline&pt.i1.comp.i18.n=3&pt.i1.comp.i33.type=betline&pt.i1.comp.i12.freespins=0&pt.i0.comp.i12.freespins=0&pt.i0.comp.i4.n=4&pt.i1.comp.i10.n=4&pt.i0.comp.i24.multi=1&pt.i1.comp.i33.n=3';
                            break;
                        case 'purchasestars':
                            $starAmountArr = [
                                400, 
                                1000, 
                                2000
                            ];
                            $starPriceArr = [
                                6, 
                                15, 
                                29.5
                            ];
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                            $starAmount = $starAmountArr[$postData['starbuy_amount']];
                            $starPrice = $starPriceArr[$postData['starbuy_amount']] * $allbet;
                            if( $starPrice <= $slotSettings->GetBalance() ) 
                            {
                                $slotSettings->SetBalance(-1 * $starPrice, 'bet');
                                $bankSum = $starPrice / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->UpdateJackpots($starPrice);
                                $slotSettings->SaveLogReport('', $starPrice, 1, 0, 'BUY');
                            }
                            else
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $Stars = $slotSettings->GetGameData($slotSettings->slotId . 'Stars');
                            $Stars += $starAmount;
                            if( $Stars > 2000 ) 
                            {
                                $Stars = 2000;
                            }
                            $GambleChance = sprintf('%01.2f', $Stars / 20);
                            if( $Stars >= 2000 ) 
                            {
                                $result_tmp[0] = 'freespins.betlevel=1&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&stars.unscaled=455&historybutton=false&rs.i0.r.i4.hold=false&next.rs=superFreespin&gamestate.history=basic%2Cstart_freespins&rs.i0.r.i1.syms=SYM13%2CSYM1%2CSYM12%2CSYM10%2CSYM10&game.win.cents=1470&rs.i0.id=freespin&totalwin.coins=0&credit=' . $balanceInCents . '&gamestate.current=super_freespin&freespins.initial=0&jackpotcurrency=%26%23x20AC%3B&multiplier=1&last.rs=basic&rs.i0.r.i0.syms=SYM4%2CSYM4%2CSYM5%2CSYM5%2CSYM9&freespins.denomination=5.000&superfreespins.multiplier.increase=0&rs.i0.r.i3.syms=SYM11%2CSYM10%2CSYM10%2CSYM8%2CSYM8&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=0&isJackpotWin=false&gamestate.stack=basic%2Csuper_freespin&rs.i0.r.i0.pos=0&freespins.betlines=0&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i1.pos=0&game.win.coins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&freespins.wavecount=1&stars.total=91&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=purchasestars&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM5%2CSYM5%2CSYM13%2CSYM13%2CSYM3&stars.frompositions=2%2C2&rs.i0.r.i2.pos=0&totalwin.cents=1470&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=0&freespins.left=8&rs.i0.r.i4.pos=0&superfreespins.multiplier.final=1&nextaction=superfreespin&wavecount=1&superfreespins.multiplier.active=1&rs.i0.r.i2.syms=SYM3%2CSYM9%2CSYM9%2CSYM12%2CSYM12&rs.i0.r.i3.hold=false&game.win.amount=14.70&freespins.totalwin.cents=0';
                                $Stars = 0;
                            }
                            else
                            {
                                $result_tmp[0] = 'rs.i0.r.i1.pos=0&legalactions=startfreespins%2Cgamble%2Cpurchasestars&gameServerVersion=1.21.0&g4mode=false&game.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&stars.unscaled=5220&historybutton=false&rs.i0.r.i1.hold=false&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic%2Cstart_freespins&stars.total=' . $Stars . '&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=purchasestars&rs.i0.r.i1.syms=SYM13%2CSYM1%2CSYM12%2CSYM10%2CSYM10&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM5%2CSYM5%2CSYM13%2CSYM13%2CSYM3&stars.frompositions=3%2C1%2C3%2C2&gamble.chance=' . $GambleChance . '&game.win.cents=0&rs.i0.r.i2.pos=0&rs.i0.id=freespin&totalwin.coins=0&credit=' . $balanceInCents . '&totalwin.cents=0&gameover=false&gamestate.current=start_freespins&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=0&last.rs=basic&rs.i0.r.i4.pos=0&rs.i0.r.i0.syms=SYM4%2CSYM4%2CSYM5%2CSYM5%2CSYM9&rs.i0.r.i3.syms=SYM11%2CSYM10%2CSYM10%2CSYM8%2CSYM8&isJackpotWin=false&gamestate.stack=basic%2Cstart_freespins&nextaction=startfreespins&rs.i0.r.i0.pos=0&wavecount=1&gamesoundurl=&rs.i0.r.i2.syms=SYM3%2CSYM9%2CSYM9%2CSYM12%2CSYM12&rs.i0.r.i3.hold=false&game.win.amount=0';
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'Stars', $Stars);
                            break;
                        case 'startfreespins':
                            $result_tmp[] = 'freespins.betlevel=1&freespintype=freespin&gameServerVersion=1.21.0&g4mode=false&game.win.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&stars.unscaled=3510&historybutton=false&next.rs=freespin&freespins.wavecount=1&gamestate.history=basic%2Cstart_freespins&stars.total=702&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=startfreespins&game.win.cents=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&totalwin.coins=' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . '&credit=' . $balanceInCents . '&totalwin.cents=425&gameover=false&gamestate.current=freespin&freespins.initial=0&jackpotcurrency=%26%23x20AC%3B&multiplier=1&freespins.left=8&last.rs=basic&freespins.denomination=' . $slotSettings->CurrentDenomination . '&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=0&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&nextaction=freespin&wavecount=1&freespins.betlines=0&gamesoundurl=&game.win.amount=4.25&freespins.totalwin.cents=0';
                            break;
                        case 'gamble':
                            $chanceArr = [];
                            $Stars = $slotSettings->GetGameData($slotSettings->slotId . 'Stars');
                            $GambleChance = $Stars / 20;
                            for( $i = 1; $i <= 100; $i++ ) 
                            {
                                if( $i < $GambleChance ) 
                                {
                                    $chanceArr[$i] = 1;
                                }
                                else
                                {
                                    $chanceArr[$i] = 0;
                                }
                            }
                            shuffle($chanceArr);
                            $gambleWin = $chanceArr[0];
                            if( $gambleWin ) 
                            {
                                $result_tmp[] = 'freespins.betlevel=1&gameServerVersion=1.21.0&g4mode=false&game.win.coins=0&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&stars.unscaled=0&historybutton=false&next.rs=superFreespin&freespins.wavecount=1&gamestate.history=basic%2Cstart_freespins&freespins.multiplier=1&stars.total=0&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=gamble&stars.frompositions=1%2C1&game.win.cents=0&totalwin.coins=0&credit=' . $balanceInCents . '&totalwin.cents=0&gameover=false&gamestate.current=super_freespin&freespins.initial=0&jackpotcurrency=%26%23x20AC%3B&multiplier=1&freespins.left=8&last.rs=basic&freespins.denomination=5.000&superfreespins.multiplier.increase=0&superfreespins.multiplier.final=1&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=0&isJackpotWin=false&gamestate.stack=basic%2Csuper_freespin&nextaction=superfreespin&wavecount=1&freespins.betlines=0&superfreespins.multiplier.active=1&gamesoundurl=&gamble.win=true&game.win.amount=0&freespins.totalwin.cents=0';
                            }
                            else
                            {
                                $result_tmp[] = 'freespins.betlevel=1&gameServerVersion=1.21.0&g4mode=false&game.win.coins=0&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&stars.unscaled=0&historybutton=false&next.rs=freespin&freespins.wavecount=1&gamestate.history=basic%2Cstart_freespins&freespins.multiplier=1&stars.total=0&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=gamble&stars.frompositions=3%2C1%2C3%2C2&game.win.cents=0&totalwin.coins=0&credit=' . $balanceInCents . '&totalwin.cents=0&gameover=false&gamestate.current=freespin&freespins.initial=0&jackpotcurrency=%26%23x20AC%3B&multiplier=1&freespins.left=8&last.rs=basic&freespins.denomination=5.000&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=0&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&nextaction=freespin&wavecount=1&freespins.betlines=0&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&gamble.win=false&game.win.amount=0&freespins.totalwin.cents=0';
                            }
                            $Stars = 0;
                            $slotSettings->SetGameData($slotSettings->slotId . 'Stars', $Stars);
                            break;
                        case 'initfreespin':
                            $result_tmp[] = 'rs.i4.id=freespin3&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM11%2CSYM11%2CSYM12&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=0&historybutton=false&rs.i0.r.i4.hold=false&next.rs=freespin&gamestate.history=basic%2Cstart_freespins&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=3&rs.i0.r.i1.syms=SYM13%2CSYM1%2CSYM12%2CSYM10%2CSYM10&rs.i2.r.i1.pos=0&game.win.cents=550&rs.i4.r.i4.pos=0&rs.i1.r.i3.hold=false&totalwin.coins=110&rs.i5.r.i4.syms=SYM3%2CSYM3%2CSYM5&gamestate.current=freespin&freespins.initial=0&rs.i4.r.i0.pos=0&jackpotcurrency=%26%23x20AC%3B&bet.betlines=0&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM4%2CSYM4%2CSYM5%2CSYM5%2CSYM9&rs.i0.r.i3.syms=SYM11%2CSYM10%2CSYM10%2CSYM8%2CSYM8&rs.i1.r.i1.syms=SYM12%2CSYM12%2CSYM8&rs.i1.r.i1.pos=42&rs.i3.r.i4.pos=0&freespins.win.cents=0&rs.i6.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&isJackpotWin=false&rs.i6.r.i4.hold=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=0&freespins.betlines=0&rs.i5.r.i0.pos=50&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i1.r.i3.syms=SYM13%2CSYM3%2CSYM3&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=freespin2&rs.i6.r.i1.pos=0&game.win.coins=110&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&rs.i2.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&clientaction=initfreespin&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i3.r.i2.hold=false&rs.i6.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&gameover=false&rs.i3.r.i3.pos=0&rs.i5.id=basic&rs.i5.r.i1.syms=SYM6%2CSYM6%2CSYM10%2CSYM10%2CSYM1&rs.i0.r.i3.pos=0&rs.i6.r.i4.pos=0&rs.i5.r.i1.hold=false&rs.i4.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&rs.i5.r.i4.hold=false&rs.i6.r.i2.hold=false&rs.i5.r.i3.pos=58&nextaction=freespin&rs.i4.r.i2.pos=0&rs.i0.r.i2.syms=SYM3%2CSYM9%2CSYM9%2CSYM12%2CSYM12&game.win.amount=5.50&freespins.totalwin.cents=0&rs.i5.r.i2.hold=false&freespins.betlevel=1&rs.i4.r.i3.pos=0&playercurrency=%26%23x20AC%3B&rs.i2.r.i0.pos=0&rs.i4.r.i4.hold=false&rs.i5.r.i0.syms=SYM12%2CSYM12%2CSYM8%2CSYM8%2CSYM4&rs.i2.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i3.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&rs.i4.r.i3.hold=false&rs.i6.r.i0.hold=false&rs.i0.id=freespin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=46&rs.i3.id=superFreespin2&multiplier=1&rs.i2.r.i2.pos=0&last.rs=basic&freespins.denomination=5.000&rs.i5.r.i1.pos=19&rs.i6.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&freespins.totalwin.coins=0&freespins.total=0&gamestate.stack=basic%2Cfreespin&rs.i6.r.i2.pos=0&rs.i1.r.i4.syms=SYM12%2CSYM10%2CSYM10&rs.i6.r.i1.hold=false&rs.i2.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=35&rs.i5.r.i2.syms=SYM1%2CSYM3%2CSYM3%2CSYM9%2CSYM9&rs.i3.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i5.r.i3.hold=false&bet.betlevel=1&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i5.r.i0.hold=false&rs.i4.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i4.r.i1.hold=false&rs.i6.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&freespins.wavecount=1&rs.i3.r.i2.pos=0&rs.i3.r.i3.hold=false&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i6.r.i0.pos=0&rs.i0.r.i4.syms=SYM5%2CSYM5%2CSYM13%2CSYM13%2CSYM3&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM13%2CSYM13%2CSYM10%2CSYM10%2CSYM1&rs.i6.r.i3.pos=0&rs.i1.r.i0.pos=40&rs.i6.id=superFreespin&totalwin.cents=550&rs.i6.r.i3.hold=false&rs.i2.r.i0.syms=SYM6%2CSYM7%2CSYM3%2CSYM5%2CSYM5&rs.i5.r.i2.pos=51&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM12%2CSYM12%2CSYM4%2CSYM11%2CSYM10&rs.i1.id=basic2&rs.i3.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i3.r.i1.syms=SYM13%2CSYM4%2CSYM6%2CSYM13%2CSYM13&rs.i1.r.i4.hold=false&freespins.left=8&rs.i0.r.i4.pos=0&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM5%2CSYM11%2CSYM9%2CSYM7%2CSYM8&rs.i3.r.i0.pos=0&rs.i5.r.i3.syms=SYM4%2CSYM4%2CSYM10%2CSYM10%2CSYM9&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&wavecount=1&rs.i6.r.i4.syms=SYM12%2CSYM9%2CSYM5%2CSYM6%2CSYM11&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bet.denomination=5&rs.i5.r.i4.pos=4';
                            break;
                        case 'spin':
                            $lines = 20;
                            $slotSettings->CurrentDenom = $postData['bet_denomination'];
                            $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                            if( $postData['slotEvent'] != 'freespin' && $postData['slotEvent'] != 'respin' ) 
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
                                $slotSettings->SetGameData($slotSettings->slotId . 'SuperMpl', 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Bet', $betline);
                                $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'Denom', $postData['bet_denomination']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
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
                                    0
                                ];
                                $wild = '1';
                                $scatter = '0';
                                $linesId0 = [];
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $reelsTmp = $reels;
                                $Stars = $slotSettings->GetGameData($slotSettings->slotId . 'Stars');
                                $featureStr = '';
                                $featuresArr = [
                                    'BreakOpen', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'SymbolUpgrade', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'ManyBonusStars', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'SymbolMultiplier', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'None', 
                                    'RandomWilds', 
                                    'SecondChance'
                                ];
                                shuffle($featuresArr);
                                $featuresActived = [
                                    $featuresArr[0], 
                                    $featuresArr[1], 
                                    $featuresArr[2]
                                ];
                                $featuresCnt = 0;
                                if( $winType == 'bonus' ) 
                                {
                                    $featuresActived = [
                                        'BreakOpen', 
                                        'None', 
                                        'None'
                                    ];
                                }
                                if( in_array('SymbolUpgrade', $featuresActived) ) 
                                {
                                    $featureStr = $slotSettings->SymbolUpgrade($reels, $featuresCnt);
                                    $featuresCnt++;
                                }
                                $advancedMultiplier = 1;
                                $advancedSymMultiplier = -1;
                                if( in_array('SymbolMultiplier', $featuresActived) ) 
                                {
                                    $tmpl = [
                                        5, 
                                        10, 
                                        15, 
                                        20
                                    ];
                                    shuffle($tmpl);
                                    $advancedMultiplier = $tmpl[0];
                                    $advancedSymMultiplier = rand(3, 13);
                                    $featureStr = '&features.i' . $featuresCnt . '.data.sym=SYM' . $advancedSymMultiplier . '&features.i' . $featuresCnt . '.data.multiplier=' . $advancedMultiplier . '&features.i' . $featuresCnt . '.type=SymbolMultiplier';
                                    $featuresCnt++;
                                }
                                if( in_array('ManyBonusStars', $featuresActived) ) 
                                {
                                    $tmpl = [
                                        3, 
                                        15
                                    ];
                                    shuffle($tmpl);
                                    $curName = 'ManyBonusStars';
                                    if( $tmpl[0] == 3 ) 
                                    {
                                        $curName = 'FewBonusStars';
                                    }
                                    $CurrentStars = $tmpl[0];
                                    $Stars += $CurrentStars;
                                    $featureStr = '&features.i' . $featuresCnt . '.type=' . $curName . '&features.i' . $featuresCnt . '.data.stars=' . $CurrentStars;
                                    $featuresCnt++;
                                }
                                if( in_array('RandomWilds', $featuresActived) ) 
                                {
                                    $featureStr = $slotSettings->RandomWilds($reels, $featuresCnt);
                                    $featuresCnt++;
                                }
                                $SecondChance = false;
                                if( in_array('SecondChance', $featuresActived) && $winType == 'none' && $postData['slotEvent'] != 'freespin' ) 
                                {
                                    $SecondChance = true;
                                    $featureStr = '&features.i' . $featuresCnt . '.type=SecondChance&features.i' . $featuresCnt . '.data.active=true';
                                    $featuresCnt++;
                                }
                                $BreakOpen = false;
                                $BreakOpenCnt = 2;
                                if( in_array('BreakOpen', $featuresActived) && $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') == 0 ) 
                                {
                                    $BreakOpen = true;
                                    if( $winType == 'bonus' ) 
                                    {
                                        $BreakOpenCnt = 12;
                                    }
                                    $openPositionsArr = [
                                        0, 
                                        2, 
                                        4, 
                                        6, 
                                        8, 
                                        10, 
                                        12, 
                                        10, 
                                        10, 
                                        10
                                    ];
                                    $op1 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId') + 1];
                                    $featureStr = '&features.i' . $featuresCnt . '.type=BreakOpen&features.i' . $featuresCnt . '.data.count=' . $BreakOpenCnt;
                                    $featuresCnt++;
                                }
                                $winLineCount = 0;
                                $tmpStringWin = '';
                                $wildsMplArr = [];
                                for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                {
                                    $csym = $slotSettings->SymbolGame[$j];
                                    if( $csym == $scatter ) 
                                    {
                                    }
                                    else
                                    {
                                        $waysCountArr = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $waysCount = 1;
                                        $wayPos = [];
                                        $RespinId = $slotSettings->GetGameData($slotSettings->slotId . 'RespinId');
                                        if( $BreakOpen ) 
                                        {
                                            $RespinId = $RespinId + 1;
                                        }
                                        if( $RespinId > 5 ) 
                                        {
                                            $RespinId = 5;
                                        }
                                        $waysLimit = [];
                                        $waysLimit[0] = [
                                            [2], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2]
                                        ];
                                        $waysLimit[1] = [
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [2]
                                        ];
                                        $waysLimit[2] = [
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ]
                                        ];
                                        $waysLimit[3] = [
                                            [
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ]
                                        ];
                                        $waysLimit[4] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                1, 
                                                2, 
                                                3
                                            ]
                                        ];
                                        $waysLimit[5] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3, 
                                                4
                                            ]
                                        ];
                                        $symPosConvert = [
                                            0, 
                                            1, 
                                            2, 
                                            3, 
                                            4
                                        ];
                                        $wildsMpl = 0;
                                        $aMpl = 1;
                                        $wscnt = 0;
                                        if( $advancedSymMultiplier == $csym ) 
                                        {
                                            $aMpl = $advancedMultiplier;
                                        }
                                        for( $rws = 1; $rws <= 5; $rws++ ) 
                                        {
                                            $curWays = $waysLimit[$RespinId][$rws - 1];
                                            foreach( $curWays as $cwsIndex => $cws ) 
                                            {
                                                if( $reels['reel' . $rws][$cws] == $csym || $reels['reel' . $rws][$cws] == $wild ) 
                                                {
                                                    $waysCountArr[$rws]++;
                                                    $wayPos[] = '&ws.i' . $winLineCount . '.pos.i' . $wscnt . '=' . ($rws - 1) . '%2C' . $cwsIndex;
                                                    $wscnt++;
                                                }
                                            }
                                            if( $waysCountArr[$rws] <= 0 ) 
                                            {
                                                break;
                                            }
                                            $waysCount = $waysCountArr[$rws] * $waysCount;
                                        }
                                        $wReelSet = 'basic';
                                        $superMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'SuperMpl');
                                        if( $postData['slotEvent'] == 'freespin' && $postData['freeMode'] == 'superfreespin' ) 
                                        {
                                            $wReelSet = 'superFreespin';
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $waysCount * $bonusMpl * $aMpl * $superMultiplier;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=' . $wReelSet . '&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=0&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $waysCount * $bonusMpl * $aMpl * $superMultiplier;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=' . $wReelSet . '&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=0&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 && $waysCountArr[5] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $waysCount * $bonusMpl * $aMpl * $superMultiplier;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=' . $wReelSet . '&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=0&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
                                        }
                                        if( $cWins[$j] > 0 && $tmpStringWin != '' ) 
                                        {
                                            array_push($lineWins, $tmpStringWin);
                                            $totalWin += $cWins[$j];
                                            $winLineCount++;
                                        }
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
                                if( $scattersCount >= 3 ) 
                                {
                                    $scattersStr = '&ws.i0.types.i0.freespins=' . $slotSettings->slotFreeCount[$scattersCount] . '&ws.i0.reelset=basic&ws.i0.betline=null&ws.i0.types.i0.wintype=freespins&ws.i0.direction=none' . implode('', $scPos);
                                }
                                $totalWin += $scattersWin;
                                $spinWin = $totalWin;
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
                            $freeState = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $reels = $reelsTmp;
                            $curReels = ' &rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '%2CSYM' . $reels['reel1'][3] . '%2CSYM' . $reels['reel1'][4] . '';
                            $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '%2CSYM' . $reels['reel2'][3] . '%2CSYM' . $reels['reel2'][4] . '');
                            $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '%2CSYM' . $reels['reel3'][3] . '%2CSYM' . $reels['reel3'][4] . '');
                            $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '%2CSYM' . $reels['reel4'][3] . '%2CSYM' . $reels['reel4'][4] . '');
                            $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '%2CSYM' . $reels['reel5'][3] . '%2CSYM' . $reels['reel5'][4] . '');
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
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $op1 = 12;
                                $op0 = 0;
                            }
                            else if( $SecondChance ) 
                            {
                                $state = 'gamble';
                                $gameover = 'false';
                                $nextaction = 'respin';
                                $gameover = 'true';
                                if( $BreakOpen ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') + 1);
                                }
                                $openPositionsArr = [
                                    0, 
                                    2, 
                                    4, 
                                    6, 
                                    8, 
                                    10, 
                                    12, 
                                    10, 
                                    10, 
                                    10
                                ];
                                $op1 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId')];
                                $op0 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId')];
                            }
                            else if( $totalWin > 0 ) 
                            {
                                $state = 'gamble';
                                $gameover = 'false';
                                $nextaction = 'respin';
                                $gameover = 'true';
                                if( $BreakOpen ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') + 1);
                                }
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') + 1);
                                $openPositionsArr = [
                                    0, 
                                    2, 
                                    4, 
                                    6, 
                                    8, 
                                    10, 
                                    12, 
                                    10, 
                                    10, 
                                    10
                                ];
                                $op1 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId')];
                                $op0 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId')];
                            }
                            else
                            {
                                if( $BreakOpen ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') + 1);
                                }
                                $openPositionsArr = [
                                    0, 
                                    2, 
                                    4, 
                                    6, 
                                    8, 
                                    10, 
                                    12, 
                                    10, 
                                    10, 
                                    10
                                ];
                                $op1 = $openPositionsArr[$slotSettings->GetGameData($slotSettings->slotId . 'RespinId')];
                                $op0 = 0;
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', 0);
                                $state = 'idle';
                                $gameover = 'true';
                                $nextaction = 'spin';
                            }
                            $gameover = 'true';
                            if( $BreakOpenCnt == 12 ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'RespinId', 5);
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'RespinId') >= 5 && $postData['slotEvent'] != 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 8);
                                $op0 = 0;
                                $op1 = 0;
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cstart_freespins&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=start_freespins&freespins.initial=' . $fs . '&freespins.win.coins=0&legalactions=startfreespins%2Cgamble&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&nextaction=startfreespins&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $nextaction = 'spin';
                                    $stack = 'basic';
                                    $gamestate = 'basic';
                                    $nextrs = 'basic';
                                }
                                else
                                {
                                    $gamestate = 'freespin';
                                    $nextaction = 'freespin';
                                    $stack = 'basic%2Cfreespin';
                                    $nextrs = 'freespin';
                                    if( $postData['freeMode'] == 'superfreespin' ) 
                                    {
                                        $gamestate = 'super_freespin';
                                        $nextaction = 'superfreespin';
                                        $stack = 'basic%2Cfreespin';
                                        $nextrs = 'superFreespin';
                                    }
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $fsl = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '&next.rs=' . $nextrs;
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            if( $postData['slotEvent'] == 'respin' ) 
                            {
                                $postData['slotEvent'] = 'BG2';
                            }
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $slotSettings->SetGameData($slotSettings->slotId . 'Stars', $Stars);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            if( $postData['freeMode'] == 'superfreespin' ) 
                            {
                                $totalWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $superMultiplier = $slotSettings->GetGameData($slotSettings->slotId . 'SuperMpl');
                                $superMultiplierNew = $slotSettings->GetGameData($slotSettings->slotId . 'SuperMpl');
                                $superMultiplierInc = 0;
                                if( $spinWin > 0 ) 
                                {
                                    $superMultiplierInc = 1;
                                    $superMultiplierNew += 1;
                                    $slotSettings->SetGameData($slotSettings->slotId . 'SuperMpl', $superMultiplierNew);
                                }
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $result_tmp[0] = 'freespins.betlevel=1&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&stars.unscaled=' . $Stars . '&historybutton=false&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic%2Cstart_freespins%2Csuper_freespin&rs.i0.r.i1.syms=SYM4%2CSYM4%2CSYM8%2CSYM8%2CSYM12&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.id=freespin2&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&gamestate.current=basic&freespins.initial=0&features.i2.type=None&jackpotcurrency=%26%23x20AC%3B&multiplier=1&last.rs=superFreespin&rs.i0.r.i0.syms=SYM6%2CSYM11%2CSYM11%2CSYM9%2CSYM9&freespins.denomination=' . $slotSettings->CurrentDenomination . '&superfreespins.multiplier.increase=' . $superMultiplierInc . '&rs.i0.r.i3.syms=SYM10%2CSYM10%2CSYM11%2CSYM11%2CSYM12&openedpositions.total=12&freespins.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=0&isJackpotWin=false&gamestate.stack=basic&rs.i0.r.i0.pos=13&freespins.betlines=0&gamesoundurl=&rs.i0.r.i1.pos=18&game.win.coins=' . $totalWin . '&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&freespins.wavecount=1&freespins.multiplier=1&stars.total=' . $Stars . '&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=superfreespin&openedpositions.thisspin=0&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM7%2CSYM13%2CSYM13%2CSYM12%2CSYM12&features.i0.type=None&rs.i0.r.i2.pos=16&features.i1.type=None&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=true&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=18&freespins.left=0&rs.i0.r.i4.pos=42&superfreespins.multiplier.final=' . $superMultiplierNew . '&nextaction=spin&wavecount=1&superfreespins.multiplier.active=' . $superMultiplier . '&rs.i0.r.i2.syms=SYM11%2CSYM9%2CSYM9%2CSYM10%2CSYM10&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '&freespins.totalwin.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '' . $curReels . $winString;
                                }
                                else
                                {
                                    $result_tmp[0] = 'freespins.betlevel=1&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&stars.unscaled=' . $Stars . '&historybutton=false&rs.i0.r.i4.hold=false&next.rs=superFreespin&gamestate.history=basic%2Cstart_freespins%2Csuper_freespin&rs.i0.r.i1.syms=SYM10%2CSYM6%2CSYM6%2CSYM10%2CSYM10&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.id=freespin2&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&gamestate.current=super_freespin&freespins.initial=0&features.i2.type=None&jackpotcurrency=%26%23x20AC%3B&multiplier=1&last.rs=superFreespin&rs.i0.r.i0.syms=SYM5%2CSYM9%2CSYM9%2CSYM11%2CSYM11&freespins.denomination=' . $slotSettings->CurrentDenomination . '&superfreespins.multiplier.increase=' . $superMultiplierInc . '&rs.i0.r.i3.syms=SYM11%2CSYM11%2CSYM7%2CSYM7%2CSYM5&openedpositions.total=12&freespins.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=0&isJackpotWin=false&gamestate.stack=basic%2Csuper_freespin&rs.i0.r.i0.pos=3&freespins.betlines=0&gamesoundurl=&rs.i0.r.i1.pos=13&game.win.coins=' . $totalWin . '&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&freespins.wavecount=1&freespins.multiplier=1&stars.total=' . $Stars . '&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=superfreespin&openedpositions.thisspin=0&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM12%2CSYM12%2CSYM5%2CSYM5%2CSYM10&features.i0.type=None&rs.i0.r.i2.pos=45&features.i1.type=None&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=33&freespins.left=4&rs.i0.r.i4.pos=12&superfreespins.multiplier.final=' . $superMultiplierNew . '&nextaction=superfreespin&wavecount=1&superfreespins.multiplier.active=' . $superMultiplier . '&rs.i0.r.i2.syms=SYM7%2CSYM7%2CSYM5%2CSYM5%2CSYM12&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin * $slotSettings->CurrentDenomination) . '&freespins.totalwin.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '' . $curReels . $winString . $featureStr;
                                }
                            }
                            else
                            {
                                $result_tmp[0] = 'rs.i0.r.i1.pos=22&gameServerVersion=1.21.0&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&stars.unscaled=' . $Stars . '&historybutton=false&rs.i0.r.i1.hold=false&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic&stars.total=6&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i1.syms=SYM12%2CSYM12%2CSYM10&openedpositions.thisspin=' . $op0 . '&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM9&features.i0.type=None&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=5&features.i1.type=None&rs.i0.id=basic2&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=true&gamestate.current=basic&rs.i0.r.i0.hold=false&features.i2.type=None&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=46&rs.i0.r.i4.pos=61&rs.i0.r.i0.syms=SYM9&rs.i0.r.i3.syms=SYM6%2CSYM9%2CSYM9&openedpositions.total=' . $op1 . '&isJackpotWin=false&gamestate.stack=basic&nextaction=' . $nextaction . '&rs.i0.r.i0.pos=11&wavecount=1&gamesoundurl=&rs.i0.r.i2.syms=SYM12%2CSYM12%2CSYM4%2CSYM4%2CSYM10&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $curReels . $winString . $featureStr;
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
