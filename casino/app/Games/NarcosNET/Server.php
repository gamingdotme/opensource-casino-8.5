<?php 
namespace VanguardLTE\Games\NarcosNET
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
                    if( $postData['action'] == 'respin' ) 
                    {
                        $postData['slotEvent'] = 'respin';
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
                            $slotSettings->SetGameData($slotSettings->slotId . 'WalkingWild', []);
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
                                $freeState = 'rs.i4.id=basicwalkingwild&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM8%2CSYM3%2CSYM7&rs.i2.r.i4.overlay.i0.pos=42&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=0&historybutton=false&rs.i0.r.i4.hold=false&gameEventSetters.enabled=false&next.rs=freespin&gamestate.history=basic%2Cfreespin&rs.i0.r.i14.syms=SYM30&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM30&rs.i0.r.i5.hold=false&rs.i0.r.i7.pos=0&rs.i2.r.i1.pos=53&game.win.cents=300&rs.i4.r.i4.pos=65&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&rs.i1.r.i3.hold=false&totalwin.coins=60&gamestate.current=freespin&freespins.initial=10&rs.i4.r.i0.pos=2&rs.i0.r.i12.syms=SYM30&jackpotcurrency=%26%23x20AC%3B&rs.i4.r.i0.overlay.i0.row=1&bet.betlines=243&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM30&rs.i0.r.i3.syms=SYM30&rs.i1.r.i1.syms=SYM3%2CSYM9%2CSYM12&rs.i1.r.i1.pos=0&rs.i3.r.i4.pos=0&freespins.win.cents=0&isJackpotWin=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=49&freespins.betlines=243&rs.i0.r.i9.pos=0&rs.i2.r.i4.overlay.i0.type=transform&rs.i4.r.i2.attention.i0=1&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM5%2CSYM0%2CSYM7&rs.i1.r.i3.syms=SYM3%2CSYM9%2CSYM12&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=freespin&game.win.coins=60&rs.i1.r.i0.hold=false&denomination.last=0.05&rs.i0.r.i5.syms=SYM30&rs.i0.r.i1.hold=false&rs.i0.r.i13.pos=0&rs.i0.r.i13.hold=false&rs.i2.r.i1.syms=SYM12%2CSYM8%2CSYM7&rs.i0.r.i7.hold=false&rs.i2.r.i4.overlay.i0.with=SYM1&clientaction=init&rs.i0.r.i8.hold=false&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM4%2CSYM10%2CSYM9&casinoID=netent&betlevel.standard=1&rs.i3.r.i2.hold=false&gameover=false&rs.i3.r.i3.pos=60&rs.i0.r.i3.pos=0&rs.i4.r.i0.syms=SYM0%2CSYM7%2CSYM11&rs.i0.r.i11.pos=0&bl.i0.id=243&rs.i0.r.i10.syms=SYM30&rs.i0.r.i13.syms=SYM30&bl.i0.line=0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2&nextaction=freespin&rs.i0.r.i5.pos=0&rs.i4.r.i2.pos=32&rs.i0.r.i2.syms=SYM30&game.win.amount=3.00&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&freespins.totalwin.cents=300&denomination.all=1%2C2%2C5%2C10%2C20%2C50%2C100%2C200&freespins.betlevel=1&rs.i0.r.i6.pos=0&rs.i4.r.i3.pos=51&playercurrency=%26%23x20AC%3B&rs.i0.r.i10.hold=false&rs.i2.r.i0.pos=51&rs.i4.r.i4.hold=false&rs.i4.r.i0.overlay.i0.with=SYM1&rs.i0.r.i8.syms=SYM30&rs.i2.r.i4.syms=SYM6%2CSYM10%2CSYM9&betlevel.last=1&rs.i3.r.i2.syms=SYM4%2CSYM10%2CSYM9&rs.i4.r.i3.hold=false&rs.i0.id=respin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=0&rs.i0.r.i7.syms=SYM30&denomination.standard=5&rs.i0.r.i6.syms=SYM30&rs.i3.id=basic&rs.i4.r.i0.overlay.i0.pos=3&rs.i0.r.i12.hold=false&multiplier=1&rs.i2.r.i2.pos=25&rs.i0.r.i9.syms=SYM30&last.rs=freespin&freespins.denomination=5.000&rs.i0.r.i8.pos=0&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&freespins.totalwin.coins=60&freespins.total=10&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM8%2CSYM3%2CSYM7&rs.i4.r.i0.attention.i0=0&rs.i2.r.i2.syms=SYM10%2CSYM11%2CSYM12&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=0&rs.i2.r.i4.overlay.i0.row=0&rs.i3.r.i3.syms=SYM1%2CSYM10%2CSYM2&rs.i4.r.i4.attention.i0=1&bet.betlevel=1&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i0.r.i14.pos=0&nearwinallowed=true&rs.i4.r.i1.syms=SYM12%2CSYM5%2CSYM9&rs.i2.r.i4.pos=42&rs.i3.r.i0.syms=SYM11%2CSYM7%2CSYM10&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i11.syms=SYM30&rs.i4.r.i1.hold=false&freespins.wavecount=1&rs.i3.r.i2.pos=131&rs.i3.r.i3.hold=false&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM30&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM8%2CSYM3%2CSYM7&rs.i1.r.i0.pos=0&totalwin.cents=300&bl.i0.coins=20&rs.i0.r.i12.pos=0&rs.i2.r.i0.syms=SYM5%2CSYM8%2CSYM11&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM10%2CSYM8%2CSYM4&restore=true&rs.i1.id=freespinwalkingwild&rs.i3.r.i4.syms=SYM3%2CSYM10%2CSYM0&rs.i0.r.i6.hold=false&rs.i3.r.i1.syms=SYM6%2CSYM12%2CSYM4&rs.i1.r.i4.hold=false&freespins.left=7&rs.i0.r.i4.pos=0&rs.i0.r.i9.hold=false&rs.i4.r.i1.pos=17&rs.i4.r.i2.syms=SYM11%2CSYM0%2CSYM6&bl.standard=243&rs.i0.r.i10.pos=0&rs.i0.r.i14.hold=false&rs.i0.r.i11.hold=false&rs.i3.r.i0.pos=0&rs.i3.r.i0.hold=false&rs.i4.nearwin=4&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bet.denomination=5';
                            }
                            $result_tmp[] = 'rs.i4.id=basic&rs.i2.r.i1.hold=false&rs.i2.r.i13.pos=0&rs.i1.r.i0.syms=SYM12%2CSYM2%2CSYM9&gameServerVersion=1.21.0&g4mode=false&historybutton=false&rs.i0.r.i4.hold=false&gameEventSetters.enabled=false&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM6%2CSYM12%2CSYM8&rs.i2.r.i1.pos=0&game.win.cents=0&rs.i4.r.i4.pos=0&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i0.reelset=ALL&rs.i1.r.i3.hold=false&rs.i2.r.i11.pos=0&totalwin.coins=0&gamestate.current=basic&rs.i4.r.i0.pos=0&jackpotcurrency=%26%23x20AC%3B&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM3%2CSYM11%2CSYM12&rs.i0.r.i3.syms=SYM6%2CSYM12%2CSYM8&rs.i1.r.i1.syms=SYM12%2CSYM7%2CSYM2&rs.i1.r.i1.pos=0&rs.i2.r.i10.hold=false&rs.i3.r.i4.pos=0&rs.i2.r.i8.syms=SYM30&isJackpotWin=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=0&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM3%2CSYM10%2CSYM0&rs.i1.r.i3.syms=SYM3%2CSYM9%2CSYM11&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=respin&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&rs.i2.r.i5.pos=0&rs.i2.r.i7.syms=SYM30&rs.i2.r.i1.syms=SYM30&clientaction=init&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM1%2CSYM10%2CSYM2&casinoID=netent&betlevel.standard=1&rs.i3.r.i2.hold=false&rs.i2.r.i10.syms=SYM30&gameover=true&rs.i3.r.i3.pos=0&rs.i2.r.i7.pos=0&rs.i0.r.i3.pos=0&rs.i4.r.i0.syms=SYM11%2CSYM7%2CSYM10&bl.i0.id=243&bl.i0.line=0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2&nextaction=spin&rs.i2.r.i14.pos=0&rs.i2.r.i12.hold=false&rs.i4.r.i2.pos=131&rs.i0.r.i2.syms=SYM3%2CSYM11%2CSYM12&game.win.amount=0&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&rs.i2.r.i12.syms=SYM30&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&rs.i2.r.i9.pos=0&rs.i4.r.i3.pos=60&playercurrency=%26%23x20AC%3B&rs.i2.r.i7.hold=false&rs.i2.r.i0.pos=0&rs.i4.r.i4.hold=false&rs.i2.r.i4.syms=SYM30&rs.i3.r.i2.syms=SYM8%2CSYM3%2CSYM7&rs.i2.r.i12.pos=0&rs.i4.r.i3.hold=false&rs.i2.r.i13.syms=SYM30&rs.i0.id=freespin&credit=' . $balanceInCents . '&rs.i1.r.i4.pos=0&rs.i2.r.i14.hold=false&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&rs.i2.r.i13.hold=false&rs.i3.id=freespinwalkingwild&multiplier=1&rs.i2.r.i2.pos=0&rs.i2.r.i10.pos=0&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&rs.i2.r.i5.syms=SYM30&rs.i2.r.i6.hold=false&rs.i1.r.i4.syms=SYM12%2CSYM10%2CSYM0&rs.i2.r.i2.syms=SYM30&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=0&rs.i3.r.i3.syms=SYM3%2CSYM9%2CSYM12&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&nearwinallowed=true&rs.i2.r.i9.hold=false&rs.i4.r.i1.syms=SYM6%2CSYM12%2CSYM4&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM8%2CSYM3%2CSYM7&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i4.r.i1.hold=false&rs.i3.r.i2.pos=0&rs.i3.r.i3.hold=false&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM3%2CSYM11%2CSYM12&rs.i2.r.i11.hold=false&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM12%2CSYM11%2CSYM0&rs.i2.r.i6.pos=0&rs.i1.r.i0.pos=0&totalwin.cents=0&bl.i0.coins=20&rs.i2.r.i0.syms=SYM30&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM30&restore=false&rs.i1.id=basicwalkingwild&rs.i2.r.i6.syms=SYM30&rs.i3.r.i4.syms=SYM8%2CSYM3%2CSYM7&rs.i3.r.i1.syms=SYM3%2CSYM9%2CSYM12&rs.i1.r.i4.hold=false&rs.i2.r.i8.hold=false&rs.i0.r.i4.pos=0&rs.i2.r.i9.syms=SYM30&rs.i4.r.i1.pos=0&rs.i4.r.i2.syms=SYM4%2CSYM10%2CSYM9&rs.i2.r.i14.syms=SYM30&rs.i2.r.i5.hold=false&bl.standard=243&rs.i3.r.i0.pos=0&rs.i2.r.i8.pos=0&rs.i3.r.i0.hold=false&rs.i2.r.i2.hold=false&rs.i2.r.i11.syms=SYM30&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false';
                            break;
                        case 'paytable':
                            $result_tmp[] = 'pt.i0.comp.i19.symbol=SYM8&pt.i0.comp.i15.type=betline&pt.i0.comp.i23.freespins=0&pt.i0.comp.i32.type=betline&pt.i0.comp.i35.multi=0&pt.i0.comp.i29.type=betline&pt.i0.comp.i4.multi=80&pt.i0.comp.i15.symbol=SYM7&pt.i0.comp.i17.symbol=SYM7&pt.i0.comp.i5.freespins=0&pt.i1.comp.i14.multi=250&pt.i0.comp.i22.multi=15&pt.i0.comp.i23.n=5&pt.i1.comp.i19.type=betline&pt.i0.comp.i11.symbol=SYM5&pt.i0.comp.i13.symbol=SYM6&pt.i1.comp.i8.type=betline&pt.i1.comp.i4.n=4&pt.i1.comp.i27.multi=5&pt.i0.comp.i15.multi=10&pt.i1.comp.i27.symbol=SYM11&bl.i0.reelset=ALL&pt.i0.comp.i16.freespins=0&pt.i0.comp.i28.multi=10&pt.i1.comp.i6.freespins=0&pt.i1.comp.i29.symbol=SYM11&pt.i1.comp.i29.freespins=0&pt.i1.comp.i22.n=4&pt.i1.comp.i30.symbol=SYM12&pt.i1.comp.i3.multi=20&pt.i0.comp.i11.n=5&pt.i0.comp.i4.freespins=0&pt.i1.comp.i23.symbol=SYM9&pt.i1.comp.i25.symbol=SYM10&pt.i0.comp.i30.freespins=0&pt.i1.comp.i24.type=betline&pt.i0.comp.i19.n=4&pt.i0.id=basic&pt.i0.comp.i1.type=betline&pt.i0.comp.i34.n=4&pt.i1.comp.i10.type=betline&pt.i0.comp.i34.type=scatter&pt.i0.comp.i2.symbol=SYM1&pt.i0.comp.i4.symbol=SYM3&pt.i1.comp.i5.freespins=0&pt.i0.comp.i20.type=betline&pt.i1.comp.i8.symbol=SYM4&pt.i1.comp.i19.n=4&pt.i0.comp.i17.freespins=0&pt.i0.comp.i6.symbol=SYM4&pt.i0.comp.i8.symbol=SYM4&pt.i0.comp.i0.symbol=SYM1&pt.i1.comp.i11.n=5&pt.i0.comp.i5.n=5&pt.i1.comp.i2.symbol=SYM1&pt.i0.comp.i3.type=betline&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=60&pt.i1.id=freespin&pt.i1.comp.i19.multi=30&pt.i1.comp.i6.symbol=SYM4&pt.i0.comp.i27.multi=5&pt.i0.comp.i9.multi=15&pt.i0.comp.i22.symbol=SYM9&pt.i0.comp.i26.symbol=SYM10&pt.i1.comp.i19.freespins=0&pt.i0.comp.i24.n=3&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=0&clientaction=paytable&pt.i1.comp.i27.freespins=0&pt.i1.comp.i4.freespins=0&pt.i1.comp.i12.type=betline&pt.i1.comp.i5.n=5&pt.i1.comp.i8.multi=300&pt.i1.comp.i21.symbol=SYM9&pt.i1.comp.i23.n=5&pt.i0.comp.i22.type=betline&pt.i0.comp.i24.freespins=0&pt.i1.comp.i32.symbol=SYM12&pt.i0.comp.i16.multi=30&pt.i0.comp.i21.multi=5&pt.i1.comp.i13.multi=60&pt.i0.comp.i12.n=3&pt.i0.comp.i35.n=5&pt.i0.comp.i13.type=betline&pt.i1.comp.i9.multi=15&bl.i0.line=0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2%2C0%2F1%2F2&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=300&pt.i1.comp.i7.freespins=0&pt.i0.comp.i31.freespins=0&pt.i0.comp.i3.multi=20&pt.i0.comp.i6.n=3&pt.i1.comp.i22.type=betline&pt.i1.comp.i12.n=3&pt.i1.comp.i3.type=betline&pt.i0.comp.i21.n=3&pt.i1.comp.i10.freespins=0&pt.i1.comp.i28.type=betline&pt.i0.comp.i34.symbol=SYM0&pt.i1.comp.i6.n=3&pt.i0.comp.i29.n=5&pt.i1.comp.i31.type=betline&pt.i1.comp.i20.multi=120&pt.i0.comp.i27.freespins=0&pt.i0.comp.i34.freespins=10&pt.i1.comp.i24.n=3&pt.i0.comp.i10.type=betline&pt.i0.comp.i35.freespins=10&pt.i1.comp.i11.symbol=SYM5&pt.i1.comp.i27.type=betline&pt.i1.comp.i2.type=betline&pt.i0.comp.i2.freespins=0&pt.i0.comp.i5.multi=300&pt.i0.comp.i7.n=4&pt.i0.comp.i32.n=5&pt.i1.comp.i1.freespins=0&pt.i0.comp.i11.multi=250&pt.i1.comp.i14.symbol=SYM6&pt.i1.comp.i16.symbol=SYM7&pt.i1.comp.i23.multi=60&pt.i0.comp.i7.type=betline&pt.i1.comp.i4.type=betline&pt.i0.comp.i17.n=5&pt.i1.comp.i18.multi=10&pt.i0.comp.i29.multi=40&pt.i1.comp.i13.n=4&pt.i0.comp.i8.freespins=0&pt.i1.comp.i26.type=betline&pt.i1.comp.i4.multi=80&pt.i0.comp.i8.multi=300&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&pt.i0.comp.i34.multi=0&pt.i0.comp.i1.freespins=0&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=250&pt.i1.comp.i7.multi=80&pt.i0.comp.i22.n=4&pt.i0.comp.i28.symbol=SYM11&pt.i1.comp.i17.type=betline&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=20&pt.i1.comp.i0.symbol=SYM1&playercurrencyiso=' . $slotSettings->slotCurrency . '&pt.i1.comp.i7.n=4&pt.i1.comp.i5.multi=300&pt.i1.comp.i5.symbol=SYM3&pt.i0.comp.i18.type=betline&pt.i0.comp.i23.symbol=SYM9&pt.i0.comp.i21.type=betline&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&pt.i1.comp.i25.n=4&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=10&pt.i0.comp.i2.type=betline&pt.i0.comp.i13.multi=60&pt.i1.comp.i20.type=betline&pt.i0.comp.i17.type=betline&pt.i0.comp.i30.type=betline&pt.i1.comp.i22.symbol=SYM9&pt.i1.comp.i30.freespins=0&pt.i1.comp.i22.multi=15&bl.i0.coins=20&pt.i0.comp.i8.n=5&pt.i0.comp.i10.n=4&pt.i0.comp.i33.n=3&pt.i1.comp.i6.multi=20&pt.i1.comp.i22.freespins=0&pt.i0.comp.i11.type=betline&pt.i1.comp.i19.symbol=SYM8&pt.i0.comp.i18.n=3&pt.i0.comp.i22.freespins=0&pt.i0.comp.i20.symbol=SYM8&pt.i0.comp.i15.freespins=0&pt.i1.comp.i14.n=5&pt.i1.comp.i16.multi=30&pt.i0.comp.i31.symbol=SYM12&pt.i1.comp.i15.freespins=0&pt.i0.comp.i27.type=betline&pt.i1.comp.i28.freespins=0&pt.i0.comp.i28.freespins=0&pt.i0.comp.i0.n=3&pt.i0.comp.i7.symbol=SYM4&pt.i1.comp.i21.multi=5&pt.i1.comp.i30.type=betline&pt.i1.comp.i0.freespins=0&pt.i0.comp.i0.type=betline&pt.i1.comp.i0.multi=20&gameServerVersion=1.21.0&g4mode=false&pt.i1.comp.i8.n=5&pt.i0.comp.i25.multi=15&historybutton=false&pt.i0.comp.i16.symbol=SYM7&pt.i1.comp.i21.freespins=0&pt.i0.comp.i1.multi=80&pt.i0.comp.i27.n=3&pt.i0.comp.i18.symbol=SYM8&pt.i1.comp.i9.type=betline&pt.i0.comp.i12.multi=15&pt.i0.comp.i32.multi=40&pt.i1.comp.i24.multi=5&pt.i1.comp.i14.freespins=0&pt.i1.comp.i23.type=betline&pt.i1.comp.i26.n=5&pt.i0.comp.i12.symbol=SYM6&pt.i0.comp.i14.symbol=SYM6&pt.i1.comp.i13.freespins=0&pt.i1.comp.i28.symbol=SYM11&pt.i0.comp.i14.type=betline&pt.i1.comp.i17.multi=120&pt.i0.comp.i18.multi=10&pt.i1.comp.i0.n=3&pt.i1.comp.i26.symbol=SYM10&pt.i0.comp.i33.type=scatter&pt.i1.comp.i31.symbol=SYM12&pt.i0.comp.i7.multi=80&pt.i0.comp.i9.n=3&pt.i0.comp.i30.n=3&pt.i1.comp.i21.type=betline&jackpotcurrency=%26%23x20AC%3B&pt.i0.comp.i28.type=betline&pt.i1.comp.i31.multi=10&pt.i1.comp.i18.type=betline&pt.i0.comp.i10.symbol=SYM5&pt.i0.comp.i15.n=3&pt.i0.comp.i21.symbol=SYM9&pt.i0.comp.i31.type=betline&pt.i1.comp.i15.n=3&isJackpotWin=false&pt.i1.comp.i20.freespins=0&pt.i1.comp.i7.type=betline&pt.i1.comp.i11.multi=250&pt.i1.comp.i30.n=3&pt.i0.comp.i1.n=4&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=120&pt.i0.comp.i20.n=5&pt.i0.comp.i29.symbol=SYM11&pt.i1.comp.i3.symbol=SYM3&pt.i0.comp.i17.multi=120&pt.i1.comp.i23.freespins=0&pt.i1.comp.i25.type=betline&pt.i1.comp.i9.n=3&pt.i0.comp.i25.symbol=SYM10&pt.i0.comp.i26.type=betline&pt.i0.comp.i28.n=4&pt.i0.comp.i9.type=betline&pt.i0.comp.i2.multi=300&pt.i1.comp.i27.n=3&pt.i0.comp.i0.freespins=0&pt.i1.comp.i16.type=betline&pt.i1.comp.i25.multi=15&pt.i0.comp.i33.multi=0&pt.i1.comp.i16.freespins=0&pt.i1.comp.i20.symbol=SYM8&pt.i1.comp.i12.multi=15&pt.i0.comp.i29.freespins=0&pt.i1.comp.i1.n=4&pt.i1.comp.i5.type=betline&pt.i1.comp.i11.freespins=0&pt.i1.comp.i24.symbol=SYM10&pt.i0.comp.i31.n=4&pt.i0.comp.i9.symbol=SYM5&pt.i1.comp.i13.symbol=SYM6&pt.i1.comp.i17.symbol=SYM7&pt.i0.comp.i16.n=4&bl.i0.id=243&pt.i0.comp.i16.type=betline&pt.i1.comp.i16.n=4&pt.i0.comp.i5.symbol=SYM3&pt.i1.comp.i7.symbol=SYM4&pt.i0.comp.i2.n=5&pt.i0.comp.i35.type=scatter&pt.i0.comp.i1.symbol=SYM1&pt.i1.comp.i31.n=4&pt.i1.comp.i31.freespins=0&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&pt.i0.comp.i6.type=betline&pt.i1.comp.i9.freespins=0&pt.i1.comp.i2.freespins=0&playercurrency=%26%23x20AC%3B&pt.i0.comp.i35.symbol=SYM0&pt.i1.comp.i25.freespins=0&pt.i0.comp.i33.symbol=SYM0&pt.i1.comp.i30.multi=5&pt.i0.comp.i25.n=4&pt.i1.comp.i10.multi=60&pt.i1.comp.i10.symbol=SYM5&pt.i1.comp.i28.n=4&pt.i1.comp.i32.freespins=0&pt.i0.comp.i9.freespins=0&pt.i1.comp.i2.n=5&pt.i1.comp.i20.n=5&credit=500000&pt.i0.comp.i5.type=betline&pt.i1.comp.i24.freespins=0&pt.i0.comp.i11.freespins=0&pt.i0.comp.i26.multi=60&pt.i0.comp.i25.type=betline&pt.i1.comp.i32.type=betline&pt.i1.comp.i18.symbol=SYM8&pt.i0.comp.i31.multi=10&pt.i1.comp.i12.symbol=SYM6&pt.i0.comp.i4.type=betline&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i1.comp.i26.freespins=0&pt.i0.comp.i26.freespins=0&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=80&pt.i1.comp.i1.type=betline&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=4&pt.i0.comp.i20.freespins=0&pt.i0.comp.i33.freespins=10&pt.i1.comp.i17.n=5&pt.i0.comp.i23.type=betline&pt.i1.comp.i29.type=betline&pt.i0.comp.i30.symbol=SYM12&pt.i0.comp.i32.symbol=SYM12&pt.i1.comp.i32.n=5&pt.i0.comp.i3.n=3&pt.i1.comp.i17.freespins=0&pt.i1.comp.i26.multi=60&pt.i1.comp.i32.multi=40&pt.i1.comp.i6.type=betline&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM1&pt.i1.comp.i29.multi=40&pt.i0.comp.i25.freespins=0&pt.i1.comp.i4.symbol=SYM3&pt.i0.comp.i24.symbol=SYM10&pt.i0.comp.i26.n=5&pt.i0.comp.i27.symbol=SYM11&pt.i0.comp.i32.freespins=0&pt.i1.comp.i29.n=5&pt.i0.comp.i23.multi=60&pt.i1.comp.i3.n=3&pt.i0.comp.i30.multi=5&pt.i1.comp.i21.n=3&pt.i1.comp.i28.multi=10&pt.i0.comp.i18.freespins=0&pt.i1.comp.i15.symbol=SYM7&pt.i1.comp.i18.freespins=0&pt.i1.comp.i3.freespins=0&pt.i0.comp.i14.n=5&pt.i0.comp.i0.multi=20&pt.i1.comp.i9.symbol=SYM5&pt.i0.comp.i19.multi=30&pt.i0.comp.i3.symbol=SYM3&pt.i0.comp.i24.type=betline&pt.i1.comp.i18.n=3&pt.i1.comp.i12.freespins=0&pt.i0.comp.i12.freespins=0&pt.i0.comp.i4.n=4&pt.i1.comp.i10.n=4&pt.i0.comp.i24.multi=5';
                            break;
                        case 'initfreespin':
                            $result_tmp[] = 'rs.i4.id=basicwalkingwild&rs.i2.r.i1.hold=false&rs.i1.r.i0.syms=SYM8%2CSYM3%2CSYM7&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=0&historybutton=false&rs.i0.r.i4.hold=false&next.rs=freespin&gamestate.history=basic&rs.i0.r.i14.syms=SYM30&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM30&rs.i0.r.i5.hold=false&rs.i0.r.i7.pos=0&rs.i2.r.i1.pos=0&game.win.cents=0&rs.i4.r.i4.pos=65&rs.i1.r.i3.hold=false&totalwin.coins=0&gamestate.current=freespin&freespins.initial=10&rs.i4.r.i0.pos=2&rs.i0.r.i12.syms=SYM30&jackpotcurrency=%26%23x20AC%3B&rs.i4.r.i0.overlay.i0.row=1&bet.betlines=243&rs.i3.r.i1.hold=false&rs.i2.r.i0.hold=false&rs.i0.r.i0.syms=SYM30&rs.i0.r.i3.syms=SYM30&rs.i1.r.i1.syms=SYM3%2CSYM9%2CSYM12&rs.i1.r.i1.pos=0&rs.i3.r.i4.pos=0&freespins.win.cents=0&isJackpotWin=false&rs.i0.r.i0.pos=0&rs.i2.r.i3.hold=false&rs.i2.r.i3.pos=0&freespins.betlines=243&rs.i0.r.i9.pos=0&rs.i4.r.i2.attention.i0=1&rs.i0.r.i1.pos=0&rs.i4.r.i4.syms=SYM5%2CSYM0%2CSYM7&rs.i1.r.i3.syms=SYM3%2CSYM9%2CSYM12&rs.i2.r.i4.hold=false&rs.i3.r.i1.pos=0&rs.i2.id=freespin&game.win.coins=0&rs.i1.r.i0.hold=false&rs.i0.r.i5.syms=SYM30&rs.i0.r.i1.hold=false&rs.i0.r.i13.pos=0&rs.i0.r.i13.hold=false&rs.i2.r.i1.syms=SYM6%2CSYM12%2CSYM8&rs.i0.r.i7.hold=false&clientaction=initfreespin&rs.i0.r.i8.hold=false&rs.i4.r.i0.hold=false&rs.i0.r.i2.hold=false&rs.i4.r.i3.syms=SYM4%2CSYM10%2CSYM9&rs.i3.r.i2.hold=false&gameover=false&rs.i3.r.i3.pos=60&rs.i0.r.i3.pos=0&rs.i4.r.i0.syms=SYM0%2CSYM7%2CSYM11&rs.i0.r.i11.pos=0&rs.i0.r.i10.syms=SYM30&rs.i0.r.i13.syms=SYM30&nextaction=freespin&rs.i0.r.i5.pos=0&rs.i4.r.i2.pos=32&rs.i0.r.i2.syms=SYM30&game.win.amount=0.00&freespins.totalwin.cents=0&freespins.betlevel=1&rs.i0.r.i6.pos=0&rs.i4.r.i3.pos=51&playercurrency=%26%23x20AC%3B&rs.i0.r.i10.hold=false&rs.i2.r.i0.pos=0&rs.i4.r.i4.hold=false&rs.i4.r.i0.overlay.i0.with=SYM1&rs.i0.r.i8.syms=SYM30&rs.i2.r.i4.syms=SYM3%2CSYM11%2CSYM12&rs.i3.r.i2.syms=SYM4%2CSYM10%2CSYM9&rs.i4.r.i3.hold=false&rs.i0.id=respin&credit=500525&rs.i1.r.i4.pos=0&rs.i0.r.i7.syms=SYM30&rs.i0.r.i6.syms=SYM30&rs.i3.id=basic&rs.i4.r.i0.overlay.i0.pos=3&rs.i0.r.i12.hold=false&multiplier=1&rs.i2.r.i2.pos=0&rs.i0.r.i9.syms=SYM30&freespins.denomination=5.000&rs.i0.r.i8.pos=0&freespins.totalwin.coins=0&freespins.total=10&gamestate.stack=basic%2Cfreespin&rs.i1.r.i4.syms=SYM8%2CSYM3%2CSYM7&rs.i4.r.i0.attention.i0=0&rs.i2.r.i2.syms=SYM3%2CSYM11%2CSYM12&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i1.r.i2.pos=0&rs.i3.r.i3.syms=SYM1%2CSYM10%2CSYM2&rs.i4.r.i4.attention.i0=1&bet.betlevel=1&rs.i3.r.i4.hold=false&rs.i4.r.i2.hold=false&rs.i0.r.i14.pos=0&rs.i4.r.i1.syms=SYM12%2CSYM5%2CSYM9&rs.i2.r.i4.pos=0&rs.i3.r.i0.syms=SYM11%2CSYM7%2CSYM10&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i11.syms=SYM30&rs.i4.r.i1.hold=false&freespins.wavecount=1&rs.i3.r.i2.pos=131&rs.i3.r.i3.hold=false&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM30&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM8%2CSYM3%2CSYM7&rs.i1.r.i0.pos=0&totalwin.cents=0&rs.i0.r.i12.pos=0&rs.i2.r.i0.syms=SYM3%2CSYM11%2CSYM12&rs.i0.r.i0.hold=false&rs.i2.r.i3.syms=SYM6%2CSYM12%2CSYM8&rs.i1.id=freespinwalkingwild&rs.i3.r.i4.syms=SYM3%2CSYM10%2CSYM0&rs.i0.r.i6.hold=false&rs.i3.r.i1.syms=SYM6%2CSYM12%2CSYM4&rs.i1.r.i4.hold=false&freespins.left=10&rs.i0.r.i4.pos=0&rs.i0.r.i9.hold=false&rs.i4.r.i1.pos=17&rs.i4.r.i2.syms=SYM11%2CSYM0%2CSYM6&rs.i0.r.i10.pos=0&rs.i0.r.i14.hold=false&rs.i0.r.i11.hold=false&rs.i3.r.i0.pos=0&rs.i3.r.i0.hold=false&rs.i4.nearwin=4&rs.i2.r.i2.hold=false&wavecount=1&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&bet.denomination=5';
                            break;
                        case 'respin':
                            $reelStrips = [];
                            $reelStrips[0] = [
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30'
                            ];
                            $reelStrips[1] = [
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30'
                            ];
                            $reelStrips[2] = [
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30'
                            ];
                            $reelStrips[3] = [
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '2', 
                                '30'
                            ];
                            $reelStrips[4] = [
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '2', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30', 
                                '30'
                            ];
                            $ClusterSpinCount = $slotSettings->GetGameData($slotSettings->slotId . 'ClusterSpinCount');
                            $clusterAllWinOld = $slotSettings->GetGameData($slotSettings->slotId . 'clusterAllWin');
                            $clusterAllWin = $slotSettings->GetGameData($slotSettings->slotId . 'clusterAllWin');
                            $clusterSymAllWins = $slotSettings->GetGameData($slotSettings->slotId . 'clusterSymAllWins');
                            $allbet = $slotSettings->GetGameData($slotSettings->slotId . 'AllBet');
                            $clusterSymWinsArr = $slotSettings->GetGameData($slotSettings->slotId . 'clusterSymWinsArr');
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $clusterAllWin);
                            $slotSettings->SetBalance(-1 * $clusterAllWin);
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $bLoop = 0; $bLoop <= 500; $bLoop++ ) 
                            {
                                $reels_c = $slotSettings->GetGameData($slotSettings->slotId . 'clusterReels');
                                $clusterSymStr = '';
                                $clusterAllWin = 0;
                                $curReels = '';
                                $reels = [];
                                $symcnt = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    $reels['reel' . $r] = [];
                                    $randPos = rand(1, count($reelStrips[$r - 1]) - 3);
                                    $reels['reel' . $r][0] = $reelStrips[$r - 1][$randPos - 1];
                                    $reels['reel' . $r][1] = $reelStrips[$r - 1][$randPos];
                                    $reels['reel' . $r][2] = $reelStrips[$r - 1][$randPos + 1];
                                }
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels_c['reel' . $r][$p] != '2c' && $reels_c['reel' . $r][$p] != '2' ) 
                                        {
                                            $reels_c['reel' . $r][$p] = $reels['reel' . $r][$p];
                                        }
                                    }
                                }
                                $reels_c = $slotSettings->GetCluster($reels_c);
                                $reels_c = $slotSettings->GetCluster($reels_c);
                                $reels_c = $slotSettings->GetCluster($reels_c);
                                $reels_c = $slotSettings->GetCluster($reels_c);
                                $symcnt = 0;
                                $symcnt0 = 0;
                                $nearwin = [];
                                $holds = '';
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels_c['reel' . $r][$p] == '2c' || $reels_c['reel' . $r][$p] == '2' ) 
                                        {
                                            $holds .= ('&rs.i0.r.i' . $symcnt0 . '.hold=true');
                                        }
                                        else
                                        {
                                            $holds .= ('&rs.i0.r.i' . $symcnt0 . '.hold=false');
                                        }
                                        if( $reels_c['reel' . $r][$p] == '2c' ) 
                                        {
                                            if( !isset($clusterSymAllWins[$symcnt]) ) 
                                            {
                                                $cwin = $clusterSymWinsArr[$r][$p] * $allbet;
                                                $clusterAllWin += $cwin;
                                                $clusterSymAllWins[] = $cwin;
                                            }
                                            else
                                            {
                                                $cwin = $clusterSymWinsArr[$r][$p] * $allbet;
                                                $clusterAllWin += $cwin;
                                            }
                                            $clusterSymStr .= ('&lockup.cluster.i0.sym.i' . $symcnt . '.value=' . $cwin);
                                            $clusterSymStr .= ('&lockup.cluster.i0.sym.i' . $symcnt . '.pos=' . ($r - 1) . '%2C' . $p);
                                            $symcnt++;
                                            $curReels .= ('&rs.i0.r.i' . $symcnt0 . '.syms=SYM2');
                                        }
                                        else
                                        {
                                            $curReels .= ('&rs.i0.r.i' . $symcnt0 . '.syms=SYM' . $reels_c['reel' . $r][$p]);
                                        }
                                        $symcnt0++;
                                    }
                                }
                                if( $clusterAllWin <= $bank ) 
                                {
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $clusterAllWin);
                                    $slotSettings->SetBalance($clusterAllWin);
                                    break;
                                }
                            }
                            if( $clusterAllWinOld < $clusterAllWin ) 
                            {
                                $ClusterSpinCount = 3;
                            }
                            else
                            {
                                $ClusterSpinCount--;
                            }
                            $slotSettings->SetGameData($slotSettings->slotId . 'clusterAllWin', $clusterAllWin);
                            $slotSettings->SetGameData($slotSettings->slotId . 'clusterSymAllWins', $clusterSymAllWins);
                            $slotSettings->SetGameData($slotSettings->slotId . 'clusterReels', $reels_c);
                            $slotSettings->SetGameData($slotSettings->slotId . 'ClusterSpinCount', $ClusterSpinCount);
                            if( $ClusterSpinCount <= 0 ) 
                            {
                                $clusterSymStr .= ('&lockup.deltawin.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.win.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.deltawin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&lockup.win.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&totalwin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&game.win.coins=' . $clusterAllWin);
                                $symcnt0 = 0;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        $clusterSymStr .= ('&rs.i0.r.i' . $symcnt0 . '.hold=false');
                                        $symcnt0++;
                                    }
                                }
                                $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                                $result_tmp[0] = 'rs.i0.r.i6.pos=0&gameServerVersion=1.21.0&g4mode=false&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i10.hold=false&rs.i0.r.i4.hold=false&ws.i0.reelset=respin&next.rs=basic&rs.i0.r.i8.syms=SYM2&gamestate.history=basic%2Crespin&lockup.cluster.i0.sym.i1.value=60&rs.i0.r.i14.syms=SYM30&lockup.deltawin.cents=0&rs.i0.r.i1.syms=SYM30&rs.i0.r.i5.hold=false&rs.i0.r.i7.pos=8&lockup.respins.left=0&game.win.cents=900&ws.i0.betline=null&rs.i0.id=respin&totalwin.coins=180&credit=' . $balanceInCents . '&gamestate.current=basic&rs.i0.r.i7.syms=SYM30&ws.i0.types.i0.coins=180&rs.i0.r.i6.syms=SYM30&rs.i0.r.i12.syms=SYM30&rs.i0.r.i12.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&rs.i0.r.i9.syms=SYM30&last.rs=respin&rs.i0.r.i0.syms=SYM2&rs.i0.r.i3.syms=SYM30&rs.i0.r.i8.pos=0&ws.i0.sym=SYM2&ws.i0.direction=left_to_right&lockup.win.cents=900&isJackpotWin=false&gamestate.stack=basic&rs.i0.r.i0.pos=10&lockup.cluster.i0.sym.i0.value=100&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i9.pos=1&ws.i0.types.i0.wintype=coins&rs.i0.r.i14.pos=2&rs.i0.r.i1.pos=6&game.win.coins=180&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i5.syms=SYM2&rs.i0.r.i1.hold=false&rs.i0.r.i13.pos=9&rs.i0.r.i13.hold=false&lockup.cluster.i0.sym.i2.pos=3%2C2&rs.i0.r.i11.syms=SYM2&lockup.deltawin.coins=0&rs.i0.r.i7.hold=false&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=respin&rs.i0.r.i8.hold=false&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM30&lockup.cluster.i0.sym.i0.pos=1%2C2&rs.i0.r.i2.pos=4&totalwin.cents=900&gameover=true&rs.i0.r.i12.pos=8&rs.i0.r.i0.hold=false&rs.i0.r.i6.hold=false&rs.i0.r.i3.pos=5&rs.i0.r.i4.pos=13&lockup.cluster.i0.sym.i2.value=20&rs.i0.r.i9.hold=false&lockup.win.coins=180&rs.i0.r.i11.pos=0&ws.i0.types.i0.cents=900&rs.i0.r.i10.syms=SYM30&rs.i0.r.i10.pos=11&rs.i0.r.i14.hold=false&rs.i0.r.i11.hold=false&rs.i0.r.i13.syms=SYM30&nextaction=spin&rs.i0.r.i5.pos=0&wavecount=1&rs.i0.r.i2.syms=SYM30&lockup.cluster.i0.sym.i1.pos=2%2C2&rs.i0.r.i3.hold=false&game.win.amount=9.00' . $curReels . $clusterSymStr;
                            }
                            else
                            {
                                $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'StaticBalance');
                                $clusterSymStr .= ('&lockup.deltawin.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.win.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.deltawin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&lockup.win.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&totalwin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&game.win.coins=' . $clusterAllWin);
                                $result_tmp[0] = 'gameServerVersion=1.21.0&g4mode=false&historybutton=false&rs.i0.r.i4.hold=false&next.rs=respin&gamestate.history=basic%2Crespin&rs.i0.r.i14.syms=&lockup.deltawin.cents=1500&rs.i0.r.i1.syms=SYM30&rs.i0.r.i5.hold=false&rs.i0.r.i7.pos=0&game.win.cents=175&totalwin.coins=35&gamestate.current=respin&rs.i0.r.i12.syms=SYM30&jackpotcurrency=%26%23x20AC%3B&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&rs.i0.r.i0.syms=SYM30&rs.i0.r.i3.syms=SYM30&isJackpotWin=false&rs.i0.r.i0.pos=0&rs.i0.r.i9.pos=0&rs.i0.r.i1.pos=5&game.win.coins=35&rs.i0.r.i5.syms=SYM30&rs.i0.r.i1.hold=false&rs.i0.r.i13.pos=0&rs.i0.r.i13.hold=false&rs.i0.r.i7.hold=false&clientaction=respin&rs.i0.r.i8.hold=false&rs.i0.r.i2.hold=false&gameover=false&rs.i0.r.i3.pos=13&lockup.win.coins=435&rs.i0.r.i11.pos=11&rs.i0.r.i10.syms=SYM2&rs.i0.r.i13.syms=SYM2&nextaction=respin&rs.i0.r.i5.pos=10&rs.i0.r.i2.syms=SYM30&game.win.amount=1.75&rs.i0.r.i6.pos=2&playercurrency=%26%23x20AC%3B&rs.i0.r.i10.hold=false&rs.i0.r.i8.syms=SYM30&lockup.respins.left=' . $ClusterSpinCount . '&rs.i0.id=respin&credit=' . $balanceInCents . '&rs.i0.r.i7.syms=SYM2&rs.i0.r.i6.syms=SYM30&rs.i0.r.i12.hold=false&multiplier=1&rs.i0.r.i9.syms=SYM30&last.rs=respin&rs.i0.r.i8.pos=2&lockup.win.cents=2175&gamestate.stack=basic%2Crespin&gamesoundurl=&rs.i0.r.i14.pos=10&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i11.syms=SYM30&lockup.deltawin.coins=300&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i4.syms=SYM30&rs.i0.r.i2.pos=5&totalwin.cents=175&rs.i0.r.i12.pos=2&rs.i0.r.i0.hold=false&rs.i0.r.i6.hold=false&rs.i0.r.i4.pos=10&rs.i0.r.i9.hold=false&rs.i0.r.i10.pos=0&rs.i0.r.i14.hold=false&rs.i0.r.i11.hold=false&wavecount=1' . $curReels . $clusterSymStr . $holds;
                            }
                            $response = $slotSettings->GetGameData($slotSettings->slotId . 'LastResponse');
                            $slotSettings->SaveLogReport($response, 0, 1, $clusterAllWin - $clusterAllWinOld, 'FG2');
                            break;
                        case 'spin':
                            $lines = 20;
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
                                $WalkingWild = $slotSettings->GetGameData($slotSettings->slotId . 'WalkingWild');
                                $featureStr = '';
                                if( !is_array($WalkingWild) ) 
                                {
                                    $WalkingWild = [];
                                }
                                foreach( $WalkingWild as $ww ) 
                                {
                                    $reels['reel' . $ww[0]][$ww[1]] = '1';
                                }
                                $randomwildsactive = false;
                                if( rand(1, 15) == 1 && $postData['slotEvent'] != 'freespin' && $winType != 'bonus' ) 
                                {
                                    $randomwildsactive = true;
                                }
                                if( rand(1, 2) == 1 && $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $randomwildsactive = true;
                                }
                                if( $randomwildsactive ) 
                                {
                                    $wildReelsArr = [
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5
                                    ];
                                    shuffle($wildReelsArr);
                                    $featureStr = '&feature.randomwilds.active=true';
                                    if( $postData['slotEvent'] == 'freespin' ) 
                                    {
                                        $featureStr = '&feature.randomwilds.active=true&next.rs=freespinwalkingwild';
                                    }
                                    $randomwildspArr = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] <= 6 && $reels['reel' . $r][$p] >= 3 && rand(1, 2) == 1 ) 
                                            {
                                                $reels['reel' . $r][$p] = '1';
                                                $featureStr .= ('&rs.i0.r.i' . ($r - 1) . '.overlay.i' . $p . '.row=' . $p . '&rs.i0.r.i' . ($r - 1) . '.overlay.i' . $p . '.with=SYM1&rs.i0.r.i' . ($r - 1) . '.overlay.i' . $p . '.pos=1&rs.i0.r.i' . ($r - 1) . '.overlay.i' . $p . '.type=transform');
                                                $randomwildspArr[] = ($r - 1) . '%3A' . $p . '';
                                            }
                                        }
                                    }
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
                                        $waysLimit = [];
                                        $waysLimit[20] = [
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ], 
                                            [
                                                0, 
                                                1, 
                                                2, 
                                                3
                                            ]
                                        ];
                                        $symPosConvert = [
                                            0, 
                                            1, 
                                            2
                                        ];
                                        $wildsMpl = 0;
                                        $wscnt = 0;
                                        for( $rws = 1; $rws <= 5; $rws++ ) 
                                        {
                                            $curWays = $waysLimit[20][$rws - 1];
                                            foreach( $curWays as $cws ) 
                                            {
                                                if( $reels['reel' . $rws][$cws] == $csym || $reels['reel' . $rws][$cws] == $wild ) 
                                                {
                                                    $waysCountArr[$rws]++;
                                                    $wayPos[] = '&ws.i' . $winLineCount . '.pos.i' . $wscnt . '=' . ($rws - 1) . '%2C' . $symPosConvert[$cws];
                                                    $wscnt++;
                                                }
                                            }
                                            if( $waysCountArr[$rws] <= 0 ) 
                                            {
                                                break;
                                            }
                                            $waysCount = $waysCountArr[$rws] * $waysCount;
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $waysCount * $bonusMpl;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=243&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $waysCount * $bonusMpl;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=243&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
                                        }
                                        if( $waysCountArr[1] > 0 && $waysCountArr[2] > 0 && $waysCountArr[3] > 0 && $waysCountArr[4] > 0 && $waysCountArr[5] > 0 ) 
                                        {
                                            $cWins[$j] = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $waysCount * $bonusMpl;
                                            $tmpStringWin = '&ws.i' . $winLineCount . '.reelset=basic&ws.i' . $winLineCount . '.types.i0.coins=' . $cWins[$j] . '&ws.i' . $winLineCount . '.types.i0.wintype=coins&ws.i' . $winLineCount . '.betline=243&ws.i' . $winLineCount . '.sym=SYM' . $csym . '&ws.i' . $winLineCount . '.direction=left_to_right&ws.i' . $winLineCount . '.types.i0.cents=' . ($cWins[$j] * $slotSettings->CurrentDenomination * 100) . '' . implode('', $wayPos);
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
                                $scattersCount2 = 0;
                                $WalkingWildTmp = [];
                                $WalkingWildStr = [];
                                $scPos = [];
                                $scat2Row = -1;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                        }
                                        if( $reels['reel' . $r][$p] == '2' && ($scat2Row == -1 || $scat2Row == $p) ) 
                                        {
                                            $scattersCount2++;
                                            $scPos[] = '&ws.i0.pos.i' . ($r - 1) . '=' . ($r - 1) . '%2C' . $p . '';
                                            $scat2Row = $p;
                                        }
                                        if( $totalWin > 0 && $reels['reel' . $r][$p] == '1' && $r > 1 ) 
                                        {
                                            $WalkingWildTmp[] = [
                                                $r - 1, 
                                                $p
                                            ];
                                        }
                                        if( $reels['reel' . $r][$p] == '1' && $totalWin > 0 ) 
                                        {
                                            $WalkingWildStr[] = '1';
                                        }
                                        else
                                        {
                                            $WalkingWildStr[] = '0';
                                        }
                                    }
                                }
                                $WalkingWild = $WalkingWildTmp;
                                if( count($WalkingWildTmp) > 1 ) 
                                {
                                }
                                else
                                {
                                    if( $scattersCount >= 3 ) 
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
                                        else if( $scattersCount2 >= 3 && $spinWinLimit < ($scattersCount2 * 5 * $allbet) && $winType == 'bonus' ) 
                                        {
                                            $winType = 'none';
                                        }
                                        else if( ($scattersCount >= 3 || $scattersCount2 >= 3) && $winType != 'bonus' ) 
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
                            $freeState = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            $reels = $reelsTmp;
                            $curReels = ' &rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
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
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=freespin&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cfreespin&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=freespin&freespins.initial=' . $fs . '&freespins.win.coins=0&rs.i0.nearwin=4&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&next.rs=freespin&rs.i0.r.i2.attention.i0=1&rs.i0.r.i0.attention.i0=0&rs.i0.r.i4.attention.i0=1&rs.i0.nearwin=4&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
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
                                        if( $nearwinCnt >= 2 && $p == 0 && ($r == 1 || $r == 3 || $r == 5) ) 
                                        {
                                            $nearwin[] = $r - 1;
                                        }
                                        if( $reels['reel' . $r][$p] == '0' && $r < 5 ) 
                                        {
                                            $nearwinCnt++;
                                        }
                                    }
                                }
                                if( $nearwinCnt >= 2 ) 
                                {
                                    $attStr = '&rs.i0.nearwin=' . implode('%2C', $nearwin);
                                }
                            }
                            else if( $scattersCount2 >= 2 ) 
                            {
                                $nrp = -1;
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $nearwinCnt >= 2 && $p == 0 ) 
                                        {
                                            $nearwin[] = $r - 1;
                                        }
                                        if( $reels['reel' . $r][$p] == '2' && $r < 5 && ($nrp == -1 || $nrp == $p) ) 
                                        {
                                            $nrp = $p;
                                            $nearwinCnt++;
                                        }
                                    }
                                }
                                if( $nearwinCnt >= 2 ) 
                                {
                                    $attStr = '&rs.i0.nearwin=' . implode('%2C', $nearwin);
                                }
                            }
                            $clusterStr = '';
                            if( $scattersCount2 >= 3 ) 
                            {
                                $clusterStr = 'gameServerVersion=1.21.0&g4mode=false&playercurrency=%26%23x20AC%3B&rs.i0.nearwin=2%2C3%2C4&historybutton=false&rs.i0.r.i4.hold=false&next.rs=respin&gamestate.history=basic&lockup.cluster.i0.sym.i1.value=60&lockup.deltawin.cents=800&rs.i0.r.i1.syms=SYM12%2CSYM2%2CSYM6&lockup.respins.left=3&game.win.cents=0&rs.i0.id=basicwalkingwild&totalwin.coins=0&credit=500625&gamestate.current=respin&jackpotcurrency=%26%23x20AC%3B&multiplier=1&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&rs.i0.r.i0.syms=SYM11%2CSYM2%2CSYM12&rs.i0.r.i3.syms=SYM8%2CSYM7%2CSYM10&rs.i0.r.i1.overlay.i0.row=0&lockup.win.cents=800&isJackpotWin=false&gamestate.stack=basic%2Crespin&rs.i0.r.i0.pos=77&lockup.cluster.i0.sym.i0.value=60&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i1.pos=21&game.win.coins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&lockup.cluster.i0.sym.i2.pos=2%2C1&rs.i0.r.i1.overlay.i0.pos=21&lockup.deltawin.coins=160&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM10%2CSYM2%2CSYM4&lockup.cluster.i0.sym.i0.pos=4%2C1&rs.i0.r.i2.pos=76&totalwin.cents=0&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=67&rs.i0.r.i4.pos=44&lockup.cluster.i0.sym.i2.value=40&lockup.win.coins=160&nextaction=respin&wavecount=1&rs.i0.r.i1.overlay.i0.with=SYM1&rs.i0.r.i2.syms=SYM9%2CSYM0%2CSYM4&lockup.cluster.i0.sym.i1.pos=3%2C1&rs.i0.r.i3.hold=false&game.win.amount=0' . $attStr;
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
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') ) 
                                {
                                    $freewalking = '0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0';
                                    $nextaction = 'spin';
                                    $stack = 'basic';
                                    $gamestate = 'basic';
                                }
                                else
                                {
                                    $freewalking = '0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0';
                                    $gamestate = 'freespin';
                                    $nextaction = 'freespin';
                                    $stack = 'basic%2Cfreespin';
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $fsl = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $slotSettings->SetGameData($slotSettings->slotId . 'LastResponse', $response);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $slotSettings->SetGameData($slotSettings->slotId . 'WalkingWild', $WalkingWild);
                            if( $scattersCount2 >= 3 ) 
                            {
                                $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
                                $curReels .= ('&rs.i0.r.i0.pos' . $reels['rp'][0]);
                                $curReels .= ('&rs.i0.r.i1.pos' . $reels['rp'][1]);
                                $curReels .= ('&rs.i0.r.i2.pos' . $reels['rp'][2]);
                                $curReels .= ('&rs.i0.r.i3.pos' . $reels['rp'][3]);
                                $curReels .= ('&rs.i0.r.i4.pos' . $reels['rp'][4]);
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                for( $bLoop = 0; $bLoop <= 500; $bLoop++ ) 
                                {
                                    $clusterSymStr = '';
                                    $clusterAllWin = 0;
                                    $WinsArr = [
                                        1, 
                                        1, 
                                        1, 
                                        1, 
                                        2, 
                                        2, 
                                        2, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9, 
                                        10, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        1, 
                                        2, 
                                        3, 
                                        4, 
                                        5, 
                                        6, 
                                        7, 
                                        8, 
                                        9, 
                                        10
                                    ];
                                    $clusterSymWinsArr = [
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        [], 
                                        []
                                    ];
                                    $clusterSymAllWins = [];
                                    $reels_c = $slotSettings->GetCluster($reels);
                                    $symcnt = 0;
                                    $symcnt0 = 0;
                                    $nearwin = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            shuffle($WinsArr);
                                            $clusterSymWinsArr[$r][$p] = $WinsArr[0];
                                            if( $reels_c['reel' . $r][$p] == '2c' ) 
                                            {
                                                $cwin = $clusterSymWinsArr[$r][$p] * $allbet;
                                                $clusterAllWin += $cwin;
                                                $clusterSymAllWins[$symcnt0] = $cwin;
                                                $clusterSymStr .= ('&lockup.cluster.i0.sym.i' . $symcnt . '.value=' . $cwin);
                                                $clusterSymStr .= ('&lockup.cluster.i0.sym.i' . $symcnt . '.pos=' . ($r - 1) . '%2C' . $p);
                                                $symcnt++;
                                                $symcnt0++;
                                            }
                                        }
                                    }
                                    if( $clusterAllWin <= $bank ) 
                                    {
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $clusterAllWin);
                                        $slotSettings->SetBalance($clusterAllWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'clusterSymWinsArr', $clusterSymWinsArr);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'clusterAllWin', $clusterAllWin);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'clusterSymAllWins', $clusterSymAllWins);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'clusterReels', $reels_c);
                                        $slotSettings->SetGameData($slotSettings->slotId . 'AllBet', $allbet);
                                        break;
                                    }
                                }
                                $clusterSymStr .= ('&lockup.deltawin.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.win.cents=' . ($clusterAllWin * $slotSettings->CurrentDenomination * 100));
                                $clusterSymStr .= ('&lockup.deltawin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&lockup.win.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&totalwin.coins=' . $clusterAllWin);
                                $clusterSymStr .= ('&game.win.coins=' . $clusterAllWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'StaticBalance', $balanceInCents);
                                $slotSettings->SetGameData($slotSettings->slotId . 'ClusterSpinCount', 3);
                                $result_tmp[0] = 'gameServerVersion=1.21.0&g4mode=false&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i4.hold=false&next.rs=respin&gamestate.history=basic&lockup.deltawin.cents=800&rs.i0.r.i1.syms=SYM12%2CSYM2%2CSYM6&lockup.respins.left=3&game.win.cents=0&rs.i0.id=basicwalkingwild&totalwin.coins=0&credit=' . $balanceInCents . '&gamestate.current=respin&jackpotcurrency=%26%23x20AC%3B&multiplier=1&walkingwilds.pos=' . implode('%2C', $WalkingWildStr) . '&rs.i0.r.i0.syms=SYM11%2CSYM2%2CSYM12&rs.i0.r.i3.syms=SYM8%2CSYM7%2CSYM10&lockup.win.cents=800&isJackpotWin=false&gamestate.stack=basic%2Crespin&rs.i0.r.i0.pos=77&gamesoundurl=&rs.i0.r.i1.pos=21&game.win.coins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&lockup.deltawin.coins=160&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM10%2CSYM2%2CSYM4&totalwin.cents=0&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=67&rs.i0.r.i4.pos=44&lockup.win.coins=160&nextaction=respin&wavecount=1&rs.i0.r.i2.syms=SYM9%2CSYM0%2CSYM4&rs.i0.r.i3.hold=false&game.win.amount=0' . $curReels . $clusterSymStr . $attStr;
                                $slotSettings->SaveLogReport($response, $allbet, 1, $clusterAllWin, 'FG2');
                            }
                            else if( $scattersCount >= 3 ) 
                            {
                                $curReels = ' &rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
                                $result_tmp[0] = 'freespins.betlevel=1&ws.i0.pos.i2=2%2C1&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&rs.i0.nearwin=4&historybutton=false&rs.i0.r.i4.hold=false&ws.i0.types.i0.freespins=10&ws.i0.reelset=basicwalkingwild&next.rs=freespin&gamestate.history=basic&ws.i0.pos.i1=4%2C1&ws.i0.pos.i0=0%2C0&rs.i0.r.i1.syms=SYM12%2CSYM5%2CSYM9&game.win.cents=0&ws.i0.betline=null&rs.i0.id=basicwalkingwild&totalwin.coins=0&credit=' . $balanceInCents . '&gamestate.current=freespin&freespins.initial=10&jackpotcurrency=%26%23x20AC%3B&multiplier=1&walkingwilds.pos=' . implode('%2C', $WalkingWildStr) . '&freespins.denomination=5.000&rs.i0.r.i0.syms=SYM0%2CSYM7%2CSYM11&rs.i0.r.i3.syms=SYM4%2CSYM10%2CSYM9&freespins.win.cents=0&ws.i0.sym=SYM0&freespins.totalwin.coins=0&freespins.total=10&ws.i0.direction=none&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&rs.i0.r.i0.pos=2&freespins.betlines=243&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&ws.i0.types.i0.wintype=freespins&rs.i0.r.i1.pos=17&game.win.coins=0&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&rs.i0.r.i2.attention.i0=1&freespins.wavecount=1&rs.i0.r.i4.attention.i0=1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM5%2CSYM0%2CSYM7&rs.i0.r.i2.pos=32&rs.i0.r.i0.attention.i0=0&totalwin.cents=0&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=51&freespins.left=10&rs.i0.r.i4.pos=65&nextaction=freespin&wavecount=1&ws.i0.types.i0.multipliers=1&rs.i0.r.i2.syms=SYM11%2CSYM0%2CSYM6&rs.i0.r.i3.hold=false&game.win.amount=0.00&freespins.totalwin.cents=0' . $curReels . $attStr;
                            }
                            else if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $curReels = ' &rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                                $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '');
                                $totalWin2 = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $result_tmp[0] = 'freespins.betlevel=' . $slotSettings->GetGameData($slotSettings->slotId . 'Bet') . '&gameServerVersion=1.21.0&g4mode=false&freespins.win.coins=' . $totalWin2 . '&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i4.hold=false&next.rs=freespin&gamestate.history=basic%2Cfreespin&rs.i0.r.i1.syms=SYM1%2CSYM10%2CSYM8&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.id=freespin&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&jackpotcurrency=%26%23x20AC%3B&multiplier=1&walkingwilds.pos=0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0%2C0&last.rs=freespin&freespins.denomination=5.000&rs.i0.r.i0.syms=SYM6%2CSYM8%2CSYM9&rs.i0.r.i3.syms=SYM6%2CSYM12%2CSYM8&freespins.win.cents=' . ($totalWin2 * $slotSettings->CurrentDenomination * 100) . '&freespins.totalwin.coins=' . $totalWin2 . '&freespins.total=' . $fs . '&isJackpotWin=false&gamestate.stack=' . $stack . '&rs.i0.r.i0.pos=3&freespins.betlines=243&gamesoundurl=https%3A%2F%2Fstatic.casinomodule.com%2F&rs.i0.r.i1.pos=6&game.win.coins=' . $totalWin . '&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i0.r.i1.hold=false&freespins.wavecount=1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=freespin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM8%2CSYM11%2CSYM9&rs.i0.r.i2.pos=0&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=false&rs.i0.r.i0.hold=false&rs.i0.r.i3.pos=0&freespins.left=' . $fsl . '&rs.i0.r.i4.pos=52&freespinwalkingwilds=' . implode('%2C', $WalkingWildStr) . '&nextaction=' . $nextaction . '&wavecount=1&rs.i0.r.i2.syms=SYM3%2CSYM11%2CSYM12&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin2 / $slotSettings->CurrentDenomination) . '&freespins.totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '' . $curReels . $winString . $attStr;
                            }
                            else
                            {
                                $result_tmp[0] = '&rs.i0.r.i1.pos=16&gameServerVersion=1.21.0&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&freespinwalkingwilds=' . implode('%2C', $WalkingWildStr) . '&rs.i0.r.i1.hold=false&rs.i0.r.i4.hold=false&next.rs=basic&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i1.syms=SYM1%2CSYM12%2CSYM11&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM7%2CSYM12%2CSYM2&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=10&rs.i0.id=basic&totalwin.coins=0&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gameover=true&gamestate.current=basic&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=17&walkingwilds.pos=' . implode('%2C', $WalkingWildStr) . '&rs.i0.r.i4.pos=83&rs.i0.r.i0.syms=SYM2%2CSYM7%2CSYM6&rs.i0.r.i3.syms=SYM2%2CSYM11%2CSYM7&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=105&wavecount=1&gamesoundurl=&rs.i0.r.i2.syms=SYM9%2CSYM4%2CSYM0&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $curReels . $winString . $featureStr . $attStr;
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
