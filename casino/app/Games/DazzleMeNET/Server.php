<?php 
namespace VanguardLTE\Games\DazzleMeNET
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
                    if( $postData['slotEvent'] == 'bet' && !isset($postData['bet_betlevel']) ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"invalid bet state"}';
                        exit( $response );
                    }
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
                            $slotSettings->SetGameData('DazzleMeNETBonusWin', 0);
                            $slotSettings->SetGameData('DazzleMeNETFreeGames', 0);
                            $slotSettings->SetGameData('DazzleMeNETCurrentFreeGame', 0);
                            $slotSettings->SetGameData('DazzleMeNETTotalWin', 0);
                            $slotSettings->SetGameData('DazzleMeNETFreeBalance', 0);
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
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels->reel3[0] . '%2CSYM' . $reels->reel3[1] . '%2CSYM' . $reels->reel3[2] . '%2CSYM' . $reels->reel3[3] . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels->reel4[0] . '%2CSYM' . $reels->reel4[1] . '%2CSYM' . $reels->reel4[2] . '%2CSYM' . $reels->reel4[3] . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels->reel5[0] . '%2CSYM' . $reels->reel5[1] . '%2CSYM' . $reels->reel5[2] . '%2CSYM' . $reels->reel5[3] . '%2CSYM' . $reels->reel5[4] . '');
                                $curReels .= ('&rs.i1.r.i0.syms=SYM' . $reels->reel1[0] . '%2CSYM' . $reels->reel1[1] . '%2CSYM' . $reels->reel1[2] . '');
                                $curReels .= ('&rs.i1.r.i1.syms=SYM' . $reels->reel2[0] . '%2CSYM' . $reels->reel2[1] . '%2CSYM' . $reels->reel2[2] . '');
                                $curReels .= ('&rs.i1.r.i2.syms=SYM' . $reels->reel3[0] . '%2CSYM' . $reels->reel3[1] . '%2CSYM' . $reels->reel3[2] . '%2CSYM' . $reels->reel3[3] . '');
                                $curReels .= ('&rs.i1.r.i3.syms=SYM' . $reels->reel4[0] . '%2CSYM' . $reels->reel4[1] . '%2CSYM' . $reels->reel4[2] . '%2CSYM' . $reels->reel4[3] . '');
                                $curReels .= ('&rs.i1.r.i4.syms=SYM' . $reels->reel5[0] . '%2CSYM' . $reels->reel5[1] . '%2CSYM' . $reels->reel5[2] . '%2CSYM' . $reels->reel5[3] . '%2CSYM' . $reels->reel5[4] . '');
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
                                $curReels .= ('&rs.i0.r.i2.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i3.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
                                $curReels .= ('&rs.i0.r.i4.syms=SYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '%2CSYM' . rand(1, 7) . '');
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
                            if( $slotSettings->GetGameData('DazzleMeNETCurrentFreeGame') < $slotSettings->GetGameData('DazzleMeNETFreeGames') && $slotSettings->GetGameData('DazzleMeNETFreeGames') > 0 ) 
                            {
                                $freeState = 'rs.i1.r.i0.syms=SYM7%2CSYM7%2CSYM7&bl.i17.reelset=ALL&bl.i15.id=15&ws.i53.types.i0.cents=40&ws.i6.sym=SYM7&ws.i48.direction=left_to_right&rs.i0.r.i4.hold=false&ws.i63.reelset=freespin&rs.i1.r.i2.hold=false&ws.i5.types.i0.cents=40&ws.i50.types.i0.wintype=coins&ws.i11.betline=11&ws.i23.types.i0.cents=40&ws.i16.reelset=freespin&game.win.cents=3160&bl.i50.id=50&ws.i28.types.i0.wintype=coins&bl.i55.line=2%2C1%2C1%2C0%2C1&ws.i62.pos.i1=2%2C2&ws.i62.pos.i0=1%2C1&ws.i62.pos.i3=4%2C2&ws.i62.pos.i2=0%2C2&bl.i18.coins=0&bl.i4.line=0%2C0%2C1%2C0%2C0&bl.i13.coins=0&bl.i62.id=62&ws.i13.reelset=freespin&ws.i61.betline=61&ws.i70.types.i0.wintype=coins&ws.i23.types.i0.coins=20&bl.i27.id=27&ws.i75.betline=75&bl.i2.id=2&bl.i38.line=1%2C1%2C2%2C1%2C1&linkedreels=0%3A1%2C2%3A3&bl.i50.reelset=ALL&bl.i14.reelset=ALL&ws.i57.direction=left_to_right&bl.i39.coins=0&bl.i64.reelset=ALL&ws.i66.reelset=freespin&game.win.coins=1580&ws.i19.reelset=freespin&bl.i22.line=1%2C0%2C0%2C0%2C0&ws.i65.types.i0.wintype=coins&ws.i16.types.i0.coins=20&bl.i8.reelset=ALL&ws.i46.types.i0.coins=20&bl.i67.reelset=ALL&ws.i59.pos.i4=4%2C3&ws.i72.betline=72&ws.i73.pos.i3=3%2C2&ws.i73.pos.i2=0%2C2&ws.i39.direction=left_to_right&ws.i73.pos.i1=2%2C3&ws.i53.types.i0.coins=20&ws.i66.direction=left_to_right&ws.i73.pos.i0=1%2C2&bl.i39.id=39&ws.i31.sym=SYM7&bl.i5.coins=0&ws.i51.pos.i0=0%2C1&ws.i59.pos.i1=0%2C2&ws.i5.types.i0.coins=20&ws.i51.pos.i1=1%2C2&ws.i59.pos.i0=1%2C1&ws.i51.pos.i2=2%2C3&ws.i59.pos.i3=3%2C2&ws.i51.pos.i3=3%2C2&ws.i59.pos.i2=2%2C1&ws.i43.pos.i4=0%2C1&ws.i51.pos.i4=4%2C3&ws.i32.pos.i0=1%2C1&ws.i43.pos.i3=4%2C4&ws.i18.pos.i3=4%2C2&ws.i32.pos.i1=0%2C1&ws.i18.pos.i4=3%2C2&ws.i32.pos.i2=4%2C0&ws.i18.pos.i1=1%2C1&ws.i32.pos.i3=3%2C0&ws.i43.pos.i0=1%2C1&ws.i18.pos.i2=2%2C2&ws.i32.pos.i4=2%2C1&ws.i62.pos.i4=3%2C2&ws.i43.pos.i2=3%2C3&rs.i0.r.i3.pos=108&ws.i16.sym=SYM7&ws.i18.pos.i0=0%2C0&ws.i43.pos.i1=2%2C2&ws.i21.pos.i1=1%2C1&ws.i29.pos.i3=1%2C0&ws.i21.pos.i2=2%2C2&ws.i29.pos.i4=2%2C1&ws.i21.pos.i0=0%2C0&ws.i73.pos.i4=4%2C3&ws.i10.direction=left_to_right&ws.i29.pos.i0=0%2C1&ws.i21.pos.i3=3%2C3&ws.i29.pos.i1=3%2C1&ws.i21.pos.i4=4%2C4&ws.i29.pos.i2=4%2C2&ws.i10.pos.i4=2%2C1&bl.i72.line=2%2C2%2C3%2C2%2C2&ws.i10.pos.i3=3%2C0&ws.i10.pos.i0=0%2C0&ws.i69.reelset=freespin&ws.i64.betline=64&ws.i10.pos.i2=4%2C0&ws.i10.pos.i1=1%2C1&ws.i3.types.i0.coins=20&bl.i31.line=1%2C0%2C1%2C2%2C3&rs.i0.r.i2.syms=SYM4%2CSYM0%2CSYM7%2CSYM7&ws.i14.betline=14&ws.i71.direction=left_to_right&bl.i34.coins=0&ws.i74.reelset=freespin&game.win.amount=31.60&ws.i22.betline=22&bl.i47.coins=0&ws.i7.sym=SYM7&ws.i67.betline=67&bl.i75.reelset=ALL&ws.i25.types.i0.cents=40&bl.i47.line=1%2C2%2C2%2C2%2C3&ws.i10.types.i0.wintype=coins&ws.i53.types.i0.wintype=coins&ws.i69.sym=SYM7&bl.i25.id=25&ws.i52.reelset=freespin&ws.i53.direction=left_to_right&ws.i45.types.i0.wintype=coins&ws.i30.types.i0.cents=40&ws.i8.reelset=freespin&ws.i13.pos.i0=0%2C0&ws.i13.pos.i1=1%2C1&ws.i27.reelset=freespin&ws.i13.pos.i2=3%2C1&ws.i13.pos.i3=4%2C2&ws.i13.pos.i4=2%2C1&ws.i48.types.i0.coins=20&ws.i55.types.i0.cents=40&ws.i71.reelset=freespin&ws.i18.types.i0.coins=20&freespins.totalwin.coins=1520&ws.i0.direction=left_to_right&bl.i72.id=72&gamestate.stack=basic%2Cfreespin&ws.i6.betline=6&ws.i60.types.i0.cents=40&ws.i44.direction=left_to_right&bl.i5.reelset=ALL&ws.i48.types.i0.wintype=coins&bl.i59.id=59&ws.i13.types.i0.wintype=coins&ws.i53.betline=53&ws.i25.betline=25&bl.i49.id=49&bl.i61.reelset=ALL&bl.i14.line=0%2C1%2C1%2C2%2C2&ws.i32.direction=left_to_right&bl.i55.coins=0&rs.i0.r.i2.pos=157&bl.i39.reelset=ALL&bl.i13.line=0%2C1%2C1%2C1%2C2&bl.i0.coins=20&ws.i2.types.i0.wintype=coins&bl.i2.reelset=ALL&bl.i37.id=37&ws.i18.types.i0.cents=40&ws.i48.types.i0.cents=40&bl.i60.id=60&ws.i9.betline=9&ws.i32.sym=SYM7&bl.i26.coins=0&bl.i29.line=1%2C0%2C1%2C1%2C2&ws.i15.sym=SYM7&ws.i28.betline=28&ws.i55.reelset=freespin&bl.i23.line=1%2C0%2C0%2C0%2C1&ws.i50.betline=50&ws.i10.reelset=freespin&ws.i30.types.i0.coins=20&ws.i60.types.i0.coins=20&ws.i41.direction=left_to_right&ws.i6.types.i0.wintype=coins&bl.i50.coins=0&bl.i30.id=30&bl.i73.line=2%2C2%2C3%2C2%2C3&ws.i74.types.i0.cents=40&ws.i65.pos.i0=1%2C1&ws.i65.pos.i2=3%2C3&ws.i65.pos.i1=2%2C2&ws.i65.pos.i4=0%2C2&ws.i26.direction=left_to_right&ws.i65.pos.i3=4%2C4&bl.i43.coins=0&ws.i0.betline=0&rs.i1.r.i3.hold=false&bl.i5.line=0%2C0%2C1%2C0%2C1&ws.i26.pos.i4=2%2C1&ws.i30.betline=30&ws.i16.types.i0.wintype=coins&ws.i3.direction=left_to_right&ws.i40.direction=left_to_right&ws.i70.pos.i4=4%2C3&ws.i70.pos.i3=0%2C2&ws.i37.pos.i4=4%2C3&ws.i44.reelset=freespin&ws.i70.pos.i2=1%2C2&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&ws.i41.sym=SYM7&ws.i70.pos.i1=3%2C3&ws.i37.pos.i2=2%2C1&ws.i14.types.i0.cents=40&ws.i37.pos.i3=3%2C2&ws.i37.pos.i0=1%2C1&ws.i37.pos.i1=0%2C1&ws.i26.pos.i0=0%2C1&ws.i26.pos.i1=4%2C0&ws.i26.pos.i2=3%2C0&ws.i26.pos.i3=1%2C0&ws.i27.types.i0.cents=40&ws.i57.types.i0.cents=40&bl.i30.line=1%2C0%2C1%2C2%2C2&ws.i44.types.i0.cents=40&bl.i45.reelset=ALL&ws.i8.sym=SYM7&ws.i2.reelset=freespin&ws.i4.direction=left_to_right&ws.i73.types.i0.wintype=coins&bl.i31.reelset=ALL&ws.i14.sym=SYM7&ws.i69.direction=left_to_right&ws.i21.reelset=freespin&ws.i57.types.i0.wintype=coins&ws.i32.reelset=freespin&bl.i40.line=1%2C1%2C2%2C2%2C2&bl.i35.coins=0&bl.i42.id=42&ws.i54.pos.i2=4%2C0&ws.i54.pos.i1=0%2C2&ws.i54.pos.i0=1%2C1&betlevel.standard=1&ws.i49.reelset=freespin&ws.i54.pos.i4=2%2C1&gameover=false&ws.i54.pos.i3=3%2C0&bl.i25.reelset=ALL&ws.i44.types.i0.coins=20&ws.i33.sym=SYM7&ws.i51.types.i0.cents=40&bl.i51.coins=0&ws.i57.types.i0.coins=20&ws.i47.betline=47&bl.i64.id=64&ws.i21.types.i0.cents=40&ws.i27.types.i0.coins=20&ws.i14.types.i0.coins=20&ws.i74.types.i0.coins=20&bl.i0.id=0&ws.i5.pos.i0=0%2C0&ws.i5.pos.i1=3%2C0&ws.i5.pos.i2=4%2C1&ws.i5.pos.i3=1%2C0&ws.i59.betline=59&bl.i15.line=0%2C1%2C1%2C2%2C3&ws.i5.pos.i4=2%2C1&bl.i19.id=19&bl.i37.line=1%2C1%2C1%2C2%2C3&bl.i9.id=9&ws.i0.pos.i4=1%2C0&ws.i0.pos.i3=2%2C0&freespins.betlevel=1&ws.i0.pos.i2=3%2C0&ws.i0.pos.i1=4%2C0&ws.i0.pos.i0=0%2C0&ws.i34.sym=SYM7&ws.i48.pos.i3=1%2C2&ws.i48.pos.i2=0%2C1&ws.i48.pos.i1=3%2C3&ws.i48.pos.i0=2%2C2&bl.i40.id=40&ws.i33.reelset=freespin&ws.i48.pos.i4=4%2C3&bl.i63.line=2%2C1%2C2%2C2%2C3&ws.i17.betline=17&ws.i15.pos.i2=2%2C1&ws.i15.pos.i3=3%2C2&ws.i15.pos.i0=0%2C0&ws.i9.sym=SYM7&ws.i15.pos.i1=1%2C1&ws.i72.types.i0.cents=40&ws.i15.pos.i4=4%2C3&ws.i48.betline=48&ws.i3.types.i0.wintype=coins&ws.i42.types.i0.cents=40&ws.i6.pos.i2=3%2C1&ws.i6.pos.i3=1%2C0&ws.i51.types.i0.coins=20&ws.i6.pos.i0=0%2C0&ws.i6.pos.i1=4%2C1&bl.i65.line=2%2C1%2C2%2C3%2C4&ws.i21.types.i0.coins=20&ws.i6.pos.i4=2%2C1&bl.i17.id=17&ws.i3.types.i0.cents=40&bl.i45.line=1%2C2%2C2%2C1%2C2&ws.i38.reelset=freespin&freespins.wavecount=1&bl.i42.coins=0&ws.i22.types.i0.wintype=coins&bl.i44.reelset=ALL&ws.i40.sym=SYM7&ws.i75.pos.i3=2%2C3&ws.i42.betline=42&ws.i75.pos.i2=1%2C2&ws.i75.pos.i4=0%2C2&ws.i75.pos.i1=4%2C4&ws.i36.betline=36&ws.i75.pos.i0=3%2C3&bl.i52.id=52&ws.i70.pos.i0=2%2C2&ws.i13.sym=SYM7&bl.i38.coins=0&bl.i56.reelset=ALL&ws.i41.types.i0.wintype=coins&bl.i29.id=29&rs.i0.r.i4.pos=51&ws.i27.direction=left_to_right&ws.i14.direction=left_to_right&ws.i25.types.i0.wintype=coins&bl.i20.reelset=ALL&ws.i9.types.i0.wintype=coins&bl.i74.id=74&rs.i1.r.i1.hold=false&ws.i27.sym=SYM7&bl.i6.coins=0&ws.i69.types.i0.wintype=coins&ws.i11.direction=left_to_right&ws.i56.direction=left_to_right&ws.i22.reelset=freespin&ws.i44.types.i0.wintype=coins&ws.i29.types.i0.coins=20&bl.i73.coins=0&ws.i70.types.i0.coins=20&bl.i44.id=44&ws.i37.betline=37&bl.i23.reelset=ALL&ws.i64.pos.i4=4%2C3&ws.i64.pos.i3=0%2C2&bl.i0.reelset=ALL&bl.i20.coins=0&ws.i64.pos.i2=3%2C3&ws.i35.sym=SYM7&ws.i60.pos.i1=2%2C2&ws.i64.pos.i1=2%2C2&ws.i71.types.i0.cents=40&bl.i74.line=2%2C2%2C3%2C3%2C3&ws.i9.reelset=freespin&ws.i60.pos.i0=1%2C1&ws.i64.pos.i0=1%2C1&ws.i11.types.i0.cents=40&ws.i69.betline=69&bl.i10.id=10&bl.i56.id=56&ws.i24.types.i0.wintype=coins&bl.i3.reelset=ALL&ws.i65.types.i0.coins=20&bl.i26.reelset=ALL&bl.i24.line=1%2C0%2C0%2C1%2C1&ws.i10.types.i0.coins=20&rs.i0.r.i0.syms=SYM5%2CSYM7%2CSYM7&bl.i41.line=1%2C1%2C2%2C2%2C3&ws.i12.sym=SYM7&ws.i6.reelset=freespin&ws.i54.reelset=freespin&ws.i34.betline=34&ws.i64.types.i0.coins=20&bl.i57.line=2%2C1%2C1%2C1%2C2&ws.i63.direction=left_to_right&bl.i59.coins=0&rs.i0.r.i0.pos=166&ws.i34.types.i0.wintype=coins&ws.i23.betline=23&bl.i28.line=1%2C0%2C1%2C1%2C1&bl.i52.coins=0&ws.i10.types.i0.cents=40&ws.i12.types.i0.cents=40&ws.i70.types.i0.cents=40&ws.i36.types.i0.cents=40&rs.i0.r.i2.hold=false&ws.i13.direction=left_to_right&ws.i71.types.i0.wintype=coins&casinoID=netent&ws.i49.types.i0.wintype=coins&bl.i55.reelset=ALL&bl.i8.id=8&ws.i64.types.i0.cents=40&bl.i58.reelset=ALL&ws.i61.direction=left_to_right&bl.i22.id=22&ws.i3.sym=SYM7&ws.i20.sym=SYM7&ws.i57.reelset=freespin&bl.i50.line=1%2C2%2C3%2C2%2C2&bl.i57.coins=0&ws.i37.types.i0.cents=40&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&ws.i63.types.i0.coins=20&ws.i12.types.i0.coins=20&bl.i48.line=1%2C2%2C2%2C3%2C3&ws.i2.pos.i4=1%2C0&bl.i27.coins=0&bl.i34.reelset=ALL&ws.i2.pos.i1=4%2C1&ws.i29.direction=left_to_right&ws.i2.pos.i0=0%2C0&ws.i2.pos.i3=3%2C1&ws.i5.types.i0.wintype=coins&ws.i2.pos.i2=2%2C0&ws.i72.types.i0.coins=20&ws.i36.sym=SYM7&rs.i0.r.i3.attention.i0=0&ws.i26.sym=SYM7&ws.i46.reelset=freespin&ws.i18.reelset=freespin&ws.i19.types.i0.wintype=coins&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&ws.i29.types.i0.wintype=coins&ws.i19.pos.i4=4%2C3&ws.i19.pos.i3=3%2C2&ws.i19.pos.i2=2%2C2&ws.i19.pos.i1=1%2C1&ws.i19.pos.i0=0%2C0&bl.i12.reelset=ALL&bl.i66.id=66&bl.i6.id=6&ws.i58.betline=58&bl.i20.id=20&bl.i66.reelset=ALL&ws.i13.betline=13&ws.i31.direction=left_to_right&gamesoundurl=&ws.i38.direction=left_to_right&ws.i11.sym=SYM7&ws.i71.types.i0.coins=20&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=0&bl.i32.id=32&bl.i70.line=2%2C2%2C2%2C3%2C3&ws.i2.sym=SYM7&ws.i21.sym=SYM7&ws.i29.types.i0.cents=40&playforfun=false&ws.i37.types.i0.coins=20&ws.i11.types.i0.coins=20&ws.i71.pos.i0=2%2C2&bl.i25.coins=0&ws.i63.types.i0.cents=40&ws.i71.pos.i1=3%2C3&bl.i69.reelset=ALL&ws.i1.betline=1&ws.i58.direction=left_to_right&bl.i71.line=2%2C2%2C2%2C3%2C4&ws.i67.types.i0.wintype=coins&ws.i60.pos.i4=3%2C1&ws.i43.reelset=freespin&ws.i60.pos.i3=4%2C1&bl.i70.reelset=ALL&ws.i60.pos.i2=0%2C2&ws.i59.types.i0.wintype=coins&bl.i54.id=54&freespins.left=7&ws.i71.pos.i2=4%2C4&ws.i71.pos.i3=1%2C2&ws.i71.pos.i4=0%2C2&ws.i61.types.i0.wintype=coins&bl.i54.coins=0&ws.i56.types.i0.coins=20&bl.i61.line=2%2C1%2C2%2C1%2C2&ws.i62.betline=62&bl.i15.reelset=ALL&ws.i65.direction=left_to_right&bl.i70.coins=0&ws.i35.reelset=freespin&bl.i11.line=0%2C1%2C1%2C0%2C1&ws.i38.types.i0.coins=20&ws.i62.types.i0.cents=40&ws.i52.types.i0.wintype=coins&gameEventSetters.enabled=false&bl.i36.reelset=ALL&ws.i67.pos.i4=4%2C2&bl.i3.coins=0&ws.i10.sym=SYM7&ws.i67.pos.i3=3%2C1&ws.i1.types.i0.coins=20&ws.i53.sym=SYM7&ws.i67.pos.i2=0%2C2&ws.i67.pos.i1=1%2C2&ws.i56.betline=56&ws.i67.pos.i0=2%2C2&ws.i62.types.i0.wintype=coins&gamestate.current=freespin&ws.i29.reelset=freespin&ws.i73.reelset=freespin&jackpotcurrency=%26%23x20AC%3B&ws.i68.sym=SYM7&ws.i28.pos.i0=0%2C1&ws.i34.direction=left_to_right&ws.i28.pos.i1=4%2C1&ws.i25.sym=SYM7&ws.i28.pos.i4=2%2C1&rs.i0.r.i3.syms=SYM0%2CSYM8%2CSYM8%2CSYM8&bl.i54.line=2%2C1%2C1%2C0%2C0&ws.i28.pos.i2=3%2C1&bl.i56.coins=0&ws.i28.pos.i3=1%2C0&bl.i9.coins=0&ws.i42.types.i0.wintype=coins&ws.i8.betline=8&ws.i38.types.i0.cents=40&bl.i24.id=24&ws.i39.types.i0.cents=40&ws.i15.betline=15&ws.i37.sym=SYM7&rs.i0.r.i1.pos=86&bl.i22.coins=0&rs.i1.r.i3.syms=SYM7%2CSYM7%2CSYM7%2CSYM7&ws.i65.sym=SYM7&bl.i36.id=36&ws.i3.betline=3&ws.i55.types.i0.coins=20&bl.i75.coins=0&ws.i51.betline=51&ws.i10.betline=10&bl.i44.line=1%2C2%2C2%2C1%2C1&bl.i72.reelset=ALL&bl.i42.reelset=ALL&ws.i40.pos.i4=3%2C2&ws.i45.types.i0.cents=40&bl.i10.reelset=ALL&ws.i1.types.i0.cents=40&ws.i22.sym=SYM7&bl.i58.id=58&ws.i36.direction=left_to_right&bl.i23.coins=0&ws.i41.reelset=freespin&ws.i9.pos.i0=0%2C0&ws.i61.types.i0.coins=20&ws.i9.pos.i2=2%2C1&ws.i9.pos.i1=1%2C0&ws.i9.pos.i4=4%2C3&ws.i33.direction=left_to_right&ws.i9.pos.i3=3%2C2&ws.i24.reelset=freespin&bl.i4.coins=0&ws.i50.sym=SYM7&bl.i18.line=0%2C1%2C2%2C2%2C2&bl.i34.id=34&ws.i52.sym=SYM7&ws.i39.betline=39&bl.i64.line=2%2C1%2C2%2C3%2C3&ws.i46.types.i0.cents=40&ws.i26.betline=26&ws.i5.sym=SYM7&ws.i45.pos.i0=2%2C2&ws.i45.pos.i1=0%2C1&ws.i54.types.i0.coins=20&ws.i45.pos.i2=1%2C2&ws.i45.pos.i3=3%2C1&ws.i45.pos.i4=4%2C2&ws.i65.reelset=freespin&ws.i17.pos.i3=3%2C1&bl.i21.line=0%2C1%2C2%2C3%2C4&ws.i0.types.i0.coins=20&ws.i17.pos.i2=2%2C2&ws.i17.pos.i4=4%2C2&ws.i40.pos.i2=0%2C1&ws.i40.pos.i3=4%2C2&ws.i17.pos.i1=1%2C1&ws.i40.pos.i0=1%2C1&ws.i17.pos.i0=0%2C0&ws.i40.pos.i1=2%2C2&bl.i21.coins=0&bl.i28.reelset=ALL&ws.i38.sym=SYM7&bl.i1.line=0%2C0%2C0%2C0%2C1&ws.i30.reelset=freespin&ws.i61.types.i0.cents=40&ws.i68.types.i0.wintype=coins&ws.i67.sym=SYM7&ws.i24.sym=SYM7&ws.i4.reelset=freespin&ws.i11.reelset=freespin&bl.i47.reelset=ALL&bl.i8.line=0%2C0%2C1%2C2%2C2&ws.i14.types.i0.wintype=coins&ws.i62.direction=left_to_right&bl.i72.coins=0&ws.i39.types.i0.wintype=coins&ws.i21.betline=21&ws.i66.sym=SYM7&ws.i23.sym=SYM7&ws.i35.direction=left_to_right&bl.i46.id=46&ws.i39.sym=SYM7&ws.i39.pos.i0=1%2C1&ws.i56.pos.i0=1%2C1&ws.i39.pos.i1=2%2C2&ws.i39.pos.i2=0%2C1&bl.i8.coins=0&ws.i39.pos.i3=3%2C1&ws.i39.pos.i4=4%2C2&ws.i70.betline=70&ws.i56.pos.i2=4%2C1&ws.i54.types.i0.cents=40&ws.i56.pos.i1=0%2C2&ws.i62.types.i0.coins=20&ws.i56.pos.i4=2%2C1&ws.i56.pos.i3=3%2C1&ws.i34.pos.i3=3%2C1&ws.i34.pos.i4=2%2C1&ws.i23.pos.i0=0%2C1&ws.i34.pos.i1=0%2C1&ws.i4.sym=SYM7&ws.i34.pos.i2=4%2C1&ws.i59.reelset=freespin&restore=true&ws.i45.betline=45&rs.i1.id=freespin&bl.i12.id=12&bl.i53.reelset=ALL&ws.i33.types.i0.wintype=coins&bl.i4.id=4&bl.i7.coins=0&bl.i71.coins=0&ws.i23.pos.i2=4%2C1&ws.i23.pos.i1=3%2C0&ws.i34.pos.i0=1%2C1&ws.i0.types.i0.cents=40&ws.i23.pos.i4=1%2C0&ws.i23.pos.i3=2%2C0&bl.i68.id=68&ws.i51.sym=SYM7&ws.i60.reelset=freespin&ws.i12.pos.i4=2%2C1&ws.i12.pos.i3=3%2C1&ws.i12.pos.i2=4%2C1&ws.i12.pos.i1=1%2C1&ws.i39.types.i0.coins=20&ws.i12.pos.i0=0%2C0&bl.i32.reelset=ALL&bl.i49.reelset=ALL&rs.i0.nearwin=4&gamestate.history=basic%2Cfreespin&ws.i18.types.i0.wintype=coins&ws.i31.reelset=freespin&ws.i64.direction=left_to_right&ws.i66.pos.i2=0%2C2&ws.i66.pos.i3=4%2C1&ws.i66.pos.i0=2%2C2&bl.i73.id=73&ws.i66.pos.i1=1%2C2&bl.i53.coins=0&ws.i9.direction=left_to_right&ws.i66.pos.i4=3%2C1&bl.i10.line=0%2C1%2C1%2C0%2C0&bl.i40.coins=0&ws.i48.reelset=freespin&bl.i60.line=2%2C1%2C2%2C1%2C1&freespins.initial=8&ws.i71.sym=SYM7&ws.i52.types.i0.coins=20&ws.i22.types.i0.coins=20&ws.i55.sym=SYM7&bl.i43.line=1%2C1%2C2%2C3%2C4&ws.i47.types.i0.coins=20&bl.i38.id=38&ws.i60.types.i0.wintype=coins&ws.i12.direction=left_to_right&ws.i17.types.i0.coins=20&ws.i46.betline=46&bl.i59.line=2%2C1%2C1%2C2%2C3&ws.i6.types.i0.cents=40&bl.i61.id=61&bl.i3.id=3&ws.i1.reelset=freespin&clientaction=init&ws.i23.types.i0.wintype=coins&bl.i45.coins=0&bl.i16.id=16&ws.i40.types.i0.wintype=coins&ws.i55.pos.i4=2%2C1&ws.i58.types.i0.wintype=coins&ws.i55.pos.i0=1%2C1&bl.i58.coins=0&ws.i55.pos.i1=0%2C2&ws.i55.pos.i2=3%2C0&ws.i40.betline=40&ws.i55.pos.i3=4%2C1&ws.i25.pos.i2=3%2C1&ws.i25.pos.i1=2%2C0&ws.i25.pos.i0=0%2C1&ws.i46.sym=SYM7&ws.i14.pos.i0=0%2C0&ws.i36.pos.i4=3%2C2&ws.i36.pos.i3=2%2C1&ws.i14.pos.i2=4%2C2&ws.i14.pos.i1=1%2C1&ws.i14.pos.i4=3%2C2&ws.i36.pos.i0=1%2C1&ws.i14.pos.i3=2%2C1&ws.i45.reelset=freespin&ws.i36.pos.i2=4%2C2&ws.i36.pos.i1=0%2C1&ws.i20.types.i0.wintype=coins&ws.i25.pos.i4=1%2C0&ws.i25.pos.i3=4%2C2&ws.i43.betline=43&ws.i17.types.i0.cents=40&ws.i59.direction=left_to_right&bl.i0.line=0%2C0%2C0%2C0%2C0&ws.i46.direction=left_to_right&bl.i34.line=1%2C1%2C1%2C1%2C1&bl.i46.reelset=ALL&ws.i4.types.i0.wintype=coins&ws.i47.types.i0.cents=40&ws.i38.types.i0.wintype=coins&bl.i74.coins=0&ws.i63.sym=SYM7&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&ws.i60.direction=left_to_right&ws.i43.types.i0.wintype=coins&ws.i37.direction=left_to_right&ws.i4.pos.i4=2%2C1&ws.i4.pos.i3=1%2C0&ws.i4.pos.i2=3%2C0&bl.i1.id=1&ws.i4.pos.i1=4%2C0&ws.i4.pos.i0=0%2C0&ws.i35.types.i0.wintype=coins&ws.i37.reelset=freespin&ws.i7.types.i0.wintype=coins&ws.i35.betline=35&bl.i43.reelset=ALL&ws.i70.sym=SYM7&bl.i48.id=48&bl.i51.line=1%2C2%2C3%2C2%2C3&ws.i15.types.i0.coins=20&ws.i19.types.i0.coins=20&ws.i45.types.i0.coins=20&rs.i1.r.i4.pos=190&ws.i75.types.i0.coins=20&bl.i61.coins=0&ws.i44.pos.i4=3%2C1&ws.i44.pos.i3=4%2C1&bl.i40.reelset=ALL&ws.i44.pos.i2=1%2C2&ws.i54.sym=SYM7&bl.i14.id=14&bl.i52.line=1%2C2%2C3%2C3%2C3&bl.i57.reelset=ALL&ws.i44.pos.i1=0%2C1&ws.i44.pos.i0=2%2C2&bl.i2.coins=0&bl.i21.reelset=ALL&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&ws.i6.types.i0.coins=20&rs.i1.r.i4.syms=SYM7%2CSYM7%2CSYM7%2CSYM7%2CSYM7&ws.i32.betline=32&bl.i24.coins=0&bl.i32.coins=0&ws.i73.direction=left_to_right&bl.i67.line=2%2C2%2C2%2C1%2C2&ws.i2.types.i0.coins=20&ws.i72.types.i0.wintype=coins&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&bl.i71.id=71&ws.i74.pos.i1=1%2C2&ws.i74.pos.i2=2%2C3&ws.i74.pos.i3=0%2C2&ws.i74.pos.i4=4%2C3&ws.i74.pos.i0=3%2C3&bl.i24.reelset=ALL&ws.i75.types.i0.wintype=coins&bl.i58.line=2%2C1%2C1%2C2%2C2&ws.i34.reelset=freespin&ws.i7.pos.i0=0%2C0&ws.i47.sym=SYM7&bl.i27.reelset=ALL&ws.i49.betline=49&ws.i7.pos.i4=2%2C1&ws.i64.sym=SYM7&ws.i7.pos.i3=1%2C0&ws.i7.pos.i2=4%2C2&ws.i7.pos.i1=3%2C1&bl.i26.id=26&ws.i22.types.i0.cents=40&ws.i52.types.i0.cents=40&ws.i5.direction=left_to_right&bl.i42.line=1%2C1%2C2%2C3%2C3&ws.i50.reelset=freespin&ws.i24.betline=24&g4mode=false&freespins.win.coins=1520&ws.i67.reelset=freespin&bl.i25.line=1%2C0%2C0%2C1%2C2&ws.i2.direction=left_to_right&ws.i2.types.i0.cents=40&ws.i50.types.i0.cents=40&bl.i18.id=18&bl.i68.reelset=ALL&ws.i20.types.i0.cents=40&ws.i69.pos.i4=4%2C3&ws.i69.pos.i3=3%2C2&ws.i63.pos.i3=3%2C2&ws.i69.pos.i0=2%2C2&ws.i63.pos.i4=4%2C3&ws.i63.pos.i1=2%2C2&ws.i69.pos.i2=0%2C2&ws.i63.pos.i2=0%2C2&ws.i69.pos.i1=1%2C2&ws.i63.pos.i0=1%2C1&ws.i61.sym=SYM7&ws.i56.types.i0.cents=40&bl.i28.coins=0&bl.i27.line=1%2C0%2C1%2C0%2C1&bl.i7.line=0%2C0%2C1%2C1%2C2&ws.i31.pos.i4=4%2C3&ws.i20.pos.i1=1%2C1&ws.i31.pos.i3=3%2C2&ws.i20.pos.i0=0%2C0&ws.i31.pos.i2=2%2C1&ws.i20.pos.i3=3%2C3&ws.i31.pos.i1=1%2C0&ws.i20.pos.i2=2%2C2&ws.i31.pos.i0=0%2C1&bl.i36.coins=0&ws.i20.pos.i4=4%2C3&freespins.win.cents=3040&bl.i7.reelset=ALL&bl.i68.line=2%2C2%2C2%2C2%2C2&isJackpotWin=false&ws.i65.betline=65&bl.i41.id=41&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&ws.i26.types.i0.cents=40&ws.i25.direction=left_to_right&bl.i63.reelset=ALL&ws.i20.types.i0.coins=20&bl.i29.coins=0&ws.i50.types.i0.coins=20&ws.i57.sym=SYM7&rs.i0.r.i1.hold=false&ws.i43.direction=left_to_right&ws.i16.direction=left_to_right&ws.i62.reelset=freespin&ws.i31.types.i0.wintype=coins&bl.i75.line=2%2C2%2C3%2C3%2C4&ws.i58.pos.i3=2%2C1&bl.i9.line=0%2C0%2C1%2C2%2C3&ws.i58.pos.i4=3%2C2&ws.i66.types.i0.wintype=coins&ws.i15.types.i0.wintype=coins&ws.i1.sym=SYM7&ws.i48.sym=SYM7&bl.i75.id=75&ws.i32.types.i0.wintype=coins&ws.i49.types.i0.coins=20&ws.i58.pos.i0=1%2C1&ws.i58.pos.i1=0%2C2&ws.i58.pos.i2=4%2C2&ws.i29.sym=SYM7&ws.i18.betline=18&ws.i1.direction=left_to_right&ws.i70.reelset=freespin&bl.i13.reelset=ALL&ws.i60.betline=60&nextaction=freespin&bl.i51.reelset=ALL&ws.i12.types.i0.wintype=coins&bl.i53.id=53&ws.i51.types.i0.wintype=coins&bl.i17.line=0%2C1%2C2%2C1%2C2&bl.i62.reelset=ALL&bl.i37.coins=0&ws.i1.pos.i3=2%2C0&playercurrency=%26%23x20AC%3B&ws.i1.pos.i4=1%2C0&bl.i28.id=28&ws.i1.pos.i0=0%2C0&bl.i63.id=63&ws.i1.pos.i1=3%2C0&ws.i1.pos.i2=4%2C1&ws.i30.direction=left_to_right&bl.i19.reelset=ALL&ws.i67.direction=left_to_right&ws.i49.types.i0.cents=40&ws.i47.pos.i1=0%2C1&ws.i73.betline=73&ws.i19.types.i0.cents=40&ws.i47.pos.i2=1%2C2&ws.i47.pos.i0=2%2C2&ws.i66.types.i0.coins=20&ws.i47.pos.i3=3%2C2&ws.i47.pos.i4=4%2C3&bl.i38.reelset=ALL&credit=' . $balanceInCents . '&ws.i20.reelset=freespin&ws.i54.betline=54&ws.i58.types.i0.cents=40&ws.i28.types.i0.cents=40&ws.i42.pos.i2=3%2C3&ws.i42.pos.i3=0%2C1&ws.i43.types.i0.cents=40&bl.i35.line=1%2C1%2C1%2C1%2C2&ws.i42.pos.i4=4%2C3&bl.i41.coins=0&ws.i36.types.i0.coins=20&bl.i1.reelset=ALL&ws.i42.pos.i0=1%2C1&ws.i42.pos.i1=2%2C2&ws.i28.direction=left_to_right&ws.i63.types.i0.wintype=coins&ws.i4.types.i0.cents=40&ws.i15.direction=left_to_right&ws.i0.sym=SYM7&bl.i51.id=51&ws.i75.reelset=freespin&ws.i73.types.i0.cents=40&ws.i56.reelset=freespin&ws.i0.types.i0.wintype=coins&ws.i13.types.i0.cents=40&ws.i47.types.i0.wintype=coins&nearwinallowed=true&bl.i44.coins=0&ws.i29.betline=29&ws.i68.reelset=freespin&ws.i5.betline=5&ws.i66.betline=66&ws.i49.sym=SYM7&bl.i74.reelset=ALL&ws.i58.types.i0.coins=20&ws.i70.direction=left_to_right&ws.i56.sym=SYM7&bl.i2.line=0%2C0%2C0%2C1%2C1&ws.i53.pos.i0=3%2C3&ws.i53.pos.i1=4%2C4&rs.i1.r.i2.syms=SYM7%2CSYM7%2CSYM7%2CSYM7&ws.i28.types.i0.coins=20&ws.i53.pos.i4=2%2C3&ws.i51.reelset=freespin&ws.i53.pos.i2=0%2C1&ws.i53.pos.i3=1%2C2&ws.i55.direction=left_to_right&ws.i7.reelset=freespin&ws.i68.direction=left_to_right&ws.i35.types.i0.cents=40&ws.i4.types.i0.coins=20&ws.i12.betline=12&ws.i14.reelset=freespin&ws.i62.sym=SYM7&ws.i42.direction=left_to_right&bl.i6.reelset=ALL&ws.i13.types.i0.coins=20&bl.i20.line=0%2C1%2C2%2C3%2C3&ws.i28.sym=SYM7&ws.i65.types.i0.cents=40&wavecount=1&ws.i26.reelset=freespin&ws.i73.types.i0.coins=20&ws.i43.types.i0.coins=20&bl.i60.coins=0&ws.i19.sym=SYM7&ws.i43.sym=SYM7&ws.i59.types.i0.coins=20&bl.i67.id=67&ws.i52.betline=52&ws.i4.betline=4&ws.i20.betline=20&bl.i21.id=21&bl.i73.reelset=ALL&ws.i40.types.i0.coins=20&ws.i68.pos.i2=0%2C2&ws.i68.pos.i3=4%2C2&ws.i68.pos.i4=3%2C2&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i33.coins=0&ws.i68.pos.i0=2%2C2&ws.i68.pos.i1=1%2C2&ws.i72.direction=left_to_right&bl.i41.reelset=ALL&ws.i41.types.i0.cents=40&ws.i8.types.i0.wintype=coins&ws.i40.reelset=freespin&ws.i47.direction=left_to_right&ws.i35.types.i0.coins=20&rs.i1.r.i1.pos=161&ws.i39.reelset=freespin&ws.i34.types.i0.coins=20&ws.i25.reelset=freespin&ws.i36.reelset=freespin&ws.i54.direction=left_to_right&bl.i53.line=1%2C2%2C3%2C3%2C4&bl.i55.id=55&rs.i1.r.i0.hold=false&ws.i66.types.i0.cents=40&bl.i62.line=2%2C1%2C2%2C2%2C2&bl.i12.coins=0&ws.i40.types.i0.cents=40&ws.i74.types.i0.wintype=coins&bl.i37.reelset=ALL&ws.i55.betline=55&ws.i74.sym=SYM7&ws.i28.reelset=freespin&ws.i74.direction=left_to_right&ws.i59.sym=SYM7&bl.i69.line=2%2C2%2C2%2C2%2C3&ws.i54.types.i0.wintype=coins&bl.i33.id=33&ws.i49.direction=left_to_right&bl.i46.coins=0&bl.i6.line=0%2C0%2C1%2C1%2C1&bl.i12.line=0%2C1%2C1%2C1%2C1&bl.i29.reelset=ALL&ws.i34.types.i0.cents=40&ws.i20.direction=left_to_right&ws.i31.betline=31&ws.i7.betline=7&ws.i67.types.i0.cents=40&ws.i27.types.i0.wintype=coins&ws.i6.direction=left_to_right&bl.i30.reelset=ALL&ws.i0.reelset=freespin&ws.i42.types.i0.coins=20&ws.i60.sym=SYM7&ws.i33.types.i0.coins=20&ws.i49.pos.i1=3%2C3&ws.i49.pos.i2=4%2C4&bl.i33.line=1%2C1%2C1%2C0%2C1&ws.i41.betline=41&ws.i49.pos.i3=0%2C1&ws.i49.pos.i4=1%2C2&ws.i46.pos.i1=0%2C1&ws.i46.pos.i0=2%2C2&ws.i49.pos.i0=2%2C2&ws.i46.pos.i3=4%2C2&ws.i46.pos.i2=1%2C2&ws.i46.pos.i4=3%2C2&ws.i45.direction=left_to_right&bl.i31.id=31&bl.i32.line=1%2C1%2C1%2C0%2C0&multiplier=1&ws.i44.betline=44&bl.i19.line=0%2C1%2C2%2C2%2C3&bl.i49.line=1%2C2%2C2%2C3%2C4&ws.i15.reelset=freespin&freespins.denomination=' . ($slotSettings->CurrentDenomination * 100) . '&ws.i37.types.i0.wintype=coins&bl.i52.reelset=ALL&ws.i61.reelset=freespin&freespins.total=8&ws.i11.pos.i4=2%2C1&ws.i11.pos.i2=3%2C0&ws.i11.pos.i3=4%2C1&ws.i11.pos.i0=0%2C0&ws.i11.pos.i1=1%2C1&ws.i42.sym=SYM7&bet.betlevel=1&bl.i33.reelset=ALL&bl.i48.reelset=ALL&bl.i19.coins=0&bl.i7.id=7&bl.i18.reelset=ALL&ws.i56.types.i0.wintype=coins&ws.i24.direction=left_to_right&ws.i64.reelset=freespin&ws.i59.types.i0.cents=40&ws.i67.types.i0.coins=20&rs.i0.r.i2.attention.i0=1&bl.i65.id=65&freespins.multiplier=1&ws.i57.pos.i1=0%2C2&ws.i57.pos.i0=1%2C1&rs.i0.r.i4.syms=SYM8%2CSYM0%2CSYM5%2CSYM5%2CSYM5&ws.i19.betline=19&ws.i33.types.i0.cents=40&ws.i9.types.i0.coins=20&ws.i52.pos.i0=3%2C3&ws.i41.types.i0.coins=20&ws.i52.pos.i2=1%2C2&bl.i48.coins=0&ws.i52.pos.i1=0%2C1&ws.i57.pos.i4=2%2C1&rs.i1.r.i0.pos=161&ws.i52.pos.i4=4%2C3&ws.i57.pos.i3=4%2C2&ws.i52.pos.i3=2%2C3&ws.i57.pos.i2=3%2C1&ws.i16.betline=16&ws.i16.pos.i1=1%2C1&ws.i41.pos.i0=1%2C1&ws.i16.pos.i2=2%2C2&ws.i16.pos.i3=4%2C1&ws.i17.direction=left_to_right&ws.i16.pos.i4=3%2C1&bl.i31.coins=0&ws.i41.pos.i4=4%2C3&ws.i9.types.i0.cents=40&ws.i41.pos.i3=3%2C2&ws.i41.pos.i2=0%2C1&ws.i16.pos.i0=0%2C0&ws.i41.pos.i1=2%2C2&rs.i1.r.i4.hold=false&ws.i30.pos.i2=1%2C0&ws.i38.pos.i4=3%2C1&ws.i74.betline=74&ws.i30.pos.i1=4%2C2&ws.i38.pos.i3=4%2C1&ws.i27.pos.i0=0%2C1&ws.i30.pos.i4=3%2C2&ws.i38.pos.i2=0%2C1&ws.i30.pos.i3=2%2C1&ws.i38.pos.i1=2%2C2&ws.i27.pos.i2=4%2C1&ws.i38.pos.i0=1%2C1&ws.i27.pos.i1=3%2C0&ws.i27.pos.i4=2%2C1&ws.i30.pos.i0=0%2C1&ws.i27.pos.i3=1%2C0&ws.i75.sym=SYM7&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&ws.i3.reelset=freespin&ws.i58.sym=SYM7&bl.i43.id=43&ws.i26.types.i0.coins=20&bl.i49.coins=0&ws.i64.types.i0.wintype=coins&rs.i0.r.i3.hold=false&ws.i52.direction=left_to_right&bet.denomination=' . ($slotSettings->CurrentDenomination * 100) . '&ws.i68.types.i0.coins=20&ws.i32.types.i0.cents=40&bl.i56.line=2%2C1%2C1%2C1%2C1&historybutton=false&bl.i60.reelset=ALL&ws.i26.types.i0.wintype=coins&bl.i5.id=5&ws.i8.types.i0.coins=20&rs.i1.r.i3.pos=243&rs.i0.r.i1.syms=SYM6%2CSYM6%2CSYM7&ws.i36.types.i0.wintype=coins&bl.i10.coins=0&bl.i63.coins=0&ws.i17.sym=SYM7&ws.i61.pos.i0=1%2C1&ws.i61.pos.i1=2%2C2&bl.i30.coins=0&bl.i39.line=1%2C1%2C2%2C1%2C2&ws.i61.pos.i2=0%2C2&totalwin.coins=1580&ws.i71.betline=71&ws.i18.direction=left_to_right&ws.i11.types.i0.wintype=coins&ws.i24.pos.i0=0%2C1&ws.i33.pos.i1=0%2C1&ws.i24.pos.i1=4%2C1&ws.i33.pos.i0=1%2C1&ws.i35.pos.i4=2%2C1&ws.i33.pos.i3=4%2C1&ws.i35.pos.i3=4%2C2&ws.i69.types.i0.cents=40&ws.i33.pos.i2=3%2C0&ws.i35.pos.i2=3%2C1&ws.i61.pos.i3=3%2C1&ws.i19.direction=left_to_right&ws.i33.pos.i4=2%2C1&ws.i61.pos.i4=4%2C2&bl.i35.id=35&ws.i58.reelset=freespin&bl.i54.reelset=ALL&ws.i22.pos.i3=2%2C0&ws.i22.pos.i2=3%2C0&ws.i22.pos.i1=4%2C0&ws.i72.pos.i3=4%2C2&ws.i22.pos.i0=0%2C1&ws.i72.pos.i4=3%2C2&ws.i24.pos.i4=1%2C0&ws.i35.pos.i1=0%2C1&rs.i1.r.i1.syms=SYM7%2CSYM7%2CSYM7&ws.i35.pos.i0=1%2C1&bl.i16.coins=0&ws.i24.pos.i2=2%2C0&ws.i22.pos.i4=1%2C0&ws.i24.pos.i3=3%2C1&ws.i27.betline=27&ws.i50.direction=left_to_right&bl.i59.reelset=ALL&ws.i68.types.i0.cents=40&ws.i21.types.i0.wintype=coins&ws.i25.types.i0.coins=20&ws.i75.types.i0.cents=40&bl.i13.id=13&bl.i62.coins=0&ws.i1.types.i0.wintype=coins&bl.i69.id=69&ws.i51.direction=left_to_right&ws.i53.reelset=freespin&ws.i72.pos.i1=2%2C3&ws.i72.pos.i2=0%2C2&ws.i72.pos.i0=1%2C2&bl.i68.coins=0&ws.i15.types.i0.cents=40&ws.i68.betline=68&ws.i38.betline=38&bl.i66.line=2%2C2%2C2%2C1%2C1&ws.i12.reelset=freespin&ws.i50.pos.i1=1%2C2&ws.i50.pos.i2=2%2C3&ws.i23.direction=left_to_right&ws.i50.pos.i0=0%2C1&bl.i11.coins=0&ws.i8.types.i0.cents=40&ws.i46.types.i0.wintype=coins&bl.i22.reelset=ALL&ws.i31.types.i0.coins=20&bl.i70.id=70&bl.i47.id=47&bl.i69.coins=0&bl.i3.line=0%2C0%2C0%2C1%2C2&ws.i5.reelset=freespin&bl.i4.reelset=ALL&freespins.totalwin.cents=3040&ws.i2.betline=2&bl.i11.id=11&bl.i57.id=57&ws.i16.types.i0.cents=40&ws.i44.sym=SYM7&bl.i67.coins=0&bl.i9.reelset=ALL&ws.i3.pos.i3=4%2C2&bl.i17.coins=0&ws.i3.pos.i4=1%2C0&ws.i3.pos.i1=2%2C0&ws.i75.direction=left_to_right&ws.i3.pos.i2=3%2C1&ws.i3.pos.i0=0%2C0&ws.i22.direction=left_to_right&ws.i24.types.i0.coins=20&ws.i18.sym=SYM7&bl.i11.reelset=ALL&bl.i16.line=0%2C1%2C2%2C1%2C1&rs.i0.id=basic&ws.i50.pos.i3=4%2C2&ws.i50.pos.i4=3%2C2&ws.i42.reelset=freespin&bl.i71.reelset=ALL&ws.i21.direction=left_to_right&ws.i8.pos.i0=0%2C0&ws.i8.pos.i1=4%2C2&ws.i7.direction=left_to_right&bl.i46.line=1%2C2%2C2%2C2%2C2&ws.i31.types.i0.cents=40&bl.i45.id=45&ws.i8.pos.i2=1%2C0&ws.i8.pos.i3=2%2C1&ws.i8.pos.i4=3%2C2&ws.i55.types.i0.wintype=coins&rs.i1.r.i2.pos=243&bl.i16.reelset=ALL&ws.i72.sym=SYM7&bl.i64.coins=0&ws.i63.betline=63&ws.i7.types.i0.coins=20&ws.i24.types.i0.cents=40&ws.i30.types.i0.wintype=coins&ws.i30.sym=SYM7&bl.i65.coins=0&ws.i73.sym=SYM7&bl.i35.reelset=ALL&rs.i0.r.i4.attention.i0=1&ws.i23.reelset=freespin&ws.i57.betline=57&ws.i47.reelset=freespin&ws.i32.types.i0.coins=20&bl.i23.id=23&bl.i15.coins=0&bl.i36.line=1%2C1%2C1%2C2%2C2&totalwin.cents=3160&ws.i17.reelset=freespin&rs.i0.r.i0.hold=false&ws.i72.reelset=freespin&bl.i66.coins=0&ws.i45.sym=SYM7&ws.i8.direction=left_to_right&ws.i17.types.i0.wintype=coins&ws.i33.betline=33&ws.i69.types.i0.coins=20&bl.i14.coins=0&bl.i65.reelset=ALL&ws.i7.types.i0.cents=40&bl.i26.line=1%2C0%2C1%2C0%2C0' . $freeState;
                            }
                            $result_tmp[] = 'bl.i32.reelset=ALL&bl.i49.reelset=ALL&bl.i60.coins=0&rs.i1.r.i0.syms=SYM5%2CSYM5%2CSYM5&bl.i6.coins=0&bl.i17.reelset=ALL&bl.i15.id=15&rs.i0.r.i4.hold=false&bl.i67.id=67&rs.i1.r.i2.hold=false&bl.i73.coins=0&bl.i21.id=21&bl.i73.id=73&bl.i73.reelset=ALL&bl.i53.coins=0&game.win.cents=0&bl.i44.id=44&bl.i50.id=50&bl.i55.line=2%2C1%2C1%2C0%2C1&staticsharedurl=https%3A%2F%2Fstatic-shared.casinomodule.com%2Fgameclient_html%2Fdevicedetection%2Fcurrent&bl.i23.reelset=ALL&bl.i33.coins=0&bl.i10.line=0%2C1%2C1%2C0%2C0&bl.i0.reelset=ALL&bl.i20.coins=0&bl.i40.coins=0&bl.i18.coins=0&bl.i74.line=2%2C2%2C3%2C3%2C3&bl.i41.reelset=ALL&bl.i10.id=10&bl.i60.line=2%2C1%2C2%2C1%2C1&bl.i56.id=56&bl.i3.reelset=ALL&bl.i4.line=0%2C0%2C1%2C0%2C0&bl.i13.coins=0&bl.i26.reelset=ALL&bl.i62.id=62&bl.i24.line=1%2C0%2C0%2C1%2C1&bl.i27.id=27&rs.i0.r.i0.syms=SYM5%2CSYM5%2CSYM3&bl.i41.line=1%2C1%2C2%2C2%2C3&bl.i43.line=1%2C1%2C2%2C3%2C4&bl.i2.id=2&rs.i1.r.i1.pos=0&bl.i38.line=1%2C1%2C2%2C1%2C1&bl.i50.reelset=ALL&bl.i57.line=2%2C1%2C1%2C1%2C2&bl.i59.coins=0&rs.i0.r.i0.pos=0&bl.i14.reelset=ALL&bl.i38.id=38&bl.i39.coins=0&bl.i64.reelset=ALL&bl.i59.line=2%2C1%2C1%2C2%2C3&game.win.coins=0&bl.i53.line=1%2C2%2C3%2C3%2C4&bl.i55.id=55&bl.i61.id=61&bl.i28.line=1%2C0%2C1%2C1%2C1&rs.i1.r.i0.hold=false&bl.i3.id=3&bl.i22.line=1%2C0%2C0%2C0%2C0&bl.i52.coins=0&bl.i62.line=2%2C1%2C2%2C2%2C2&bl.i12.coins=0&bl.i8.reelset=ALL&clientaction=init&bl.i67.reelset=ALL&rs.i0.r.i2.hold=false&bl.i45.coins=0&bl.i16.id=16&bl.i37.reelset=ALL&bl.i39.id=39&casinoID=netent&bl.i5.coins=0&bl.i58.coins=0&bl.i55.reelset=ALL&bl.i8.id=8&bl.i69.line=2%2C2%2C2%2C2%2C3&rs.i0.r.i3.pos=0&bl.i33.id=33&bl.i58.reelset=ALL&bl.i46.coins=0&bl.i6.line=0%2C0%2C1%2C1%2C1&bl.i22.id=22&bl.i72.line=2%2C2%2C3%2C2%2C2&bl.i12.line=0%2C1%2C1%2C1%2C1&bl.i0.line=0%2C0%2C0%2C0%2C0&bl.i29.reelset=ALL&bl.i34.line=1%2C1%2C1%2C1%2C1&bl.i46.reelset=ALL&bl.i31.line=1%2C0%2C1%2C2%2C3&rs.i0.r.i2.syms=SYM4%2CSYM4%2CSYM4%2CSYM7&bl.i34.coins=0&bl.i74.coins=0&game.win.amount=0&betlevel.all=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10&bl.i50.line=1%2C2%2C3%2C2%2C2&bl.i57.coins=0&denomination.all=' . implode('%2C', $slotSettings->Denominations) . '&bl.i48.line=1%2C2%2C2%2C3%2C3&bl.i27.coins=0&bl.i47.coins=0&bl.i34.reelset=ALL&bl.i30.reelset=ALL&bl.i1.id=1&bl.i75.reelset=ALL&bl.i33.line=1%2C1%2C1%2C0%2C1&bl.i43.reelset=ALL&bl.i47.line=1%2C2%2C2%2C2%2C3&bl.i48.id=48&bl.i51.line=1%2C2%2C3%2C2%2C3&bl.i25.id=25&rs.i1.r.i4.pos=0&denomination.standard=' . ($slotSettings->CurrentDenomination * 100) . '&bl.i61.coins=0&bl.i31.id=31&bl.i32.line=1%2C1%2C1%2C0%2C0&bl.i40.reelset=ALL&multiplier=1&bl.i14.id=14&bl.i52.line=1%2C2%2C3%2C3%2C3&bl.i57.reelset=ALL&bl.i19.line=0%2C1%2C2%2C2%2C3&bl.i49.line=1%2C2%2C2%2C3%2C4&bl.i12.reelset=ALL&bl.i66.id=66&bl.i2.coins=0&bl.i6.id=6&bl.i52.reelset=ALL&bl.i21.reelset=ALL&autoplay=10%2C25%2C50%2C75%2C100%2C250%2C500%2C750%2C1000&bl.i20.id=20&bl.i72.id=72&bl.i66.reelset=ALL&rs.i1.r.i4.syms=SYM6%2CSYM6%2CSYM6%2CSYM6%2CSYM6&gamesoundurl=&bl.i33.reelset=ALL&bl.i5.reelset=ALL&bl.i24.coins=0&bl.i48.reelset=ALL&bl.i19.coins=0&bl.i32.coins=0&bl.i59.id=59&bl.i7.id=7&bl.i18.reelset=ALL&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=0&bl.i32.id=32&bl.i67.line=2%2C2%2C2%2C1%2C2&bl.i49.id=49&bl.i65.id=65&bl.i61.reelset=ALL&bl.i14.line=0%2C1%2C1%2C2%2C2&bl.i70.line=2%2C2%2C2%2C3%2C3&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&bl.i71.id=71&rs.i0.r.i4.syms=SYM6%2CSYM6%2CSYM6%2CSYM6%2CSYM6&bl.i55.coins=0&bl.i25.coins=0&rs.i0.r.i2.pos=0&bl.i39.reelset=ALL&bl.i13.line=0%2C1%2C1%2C1%2C2&bl.i69.reelset=ALL&bl.i24.reelset=ALL&bl.i48.coins=0&bl.i71.line=2%2C2%2C2%2C3%2C4&rs.i1.r.i0.pos=0&bl.i58.line=2%2C1%2C1%2C2%2C2&bl.i0.coins=20&bl.i2.reelset=ALL&bl.i70.reelset=ALL&bl.i31.coins=0&bl.i37.id=37&bl.i54.id=54&bl.i60.id=60&rs.i1.r.i4.hold=false&bl.i26.coins=0&bl.i27.reelset=ALL&bl.standard=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&bl.i29.line=1%2C0%2C1%2C1%2C2&bl.i54.coins=0&bl.i43.id=43&bl.i23.line=1%2C0%2C0%2C0%2C1&bl.i26.id=26&bl.i49.coins=0&bl.i61.line=2%2C1%2C2%2C1%2C2&bl.i15.reelset=ALL&rs.i0.r.i3.hold=false&bl.i42.line=1%2C1%2C2%2C3%2C3&bl.i70.coins=0&g4mode=false&bl.i11.line=0%2C1%2C1%2C0%2C1&bl.i50.coins=0&bl.i30.id=30&bl.i56.line=2%2C1%2C1%2C1%2C1&historybutton=false&bl.i25.line=1%2C0%2C0%2C1%2C2&bl.i60.reelset=ALL&bl.i73.line=2%2C2%2C3%2C2%2C3&bl.i5.id=5&gameEventSetters.enabled=false&bl.i36.reelset=ALL&rs.i1.r.i3.pos=0&rs.i0.r.i1.syms=SYM8%2CSYM8%2CSYM5&bl.i3.coins=0&bl.i10.coins=0&bl.i18.id=18&bl.i68.reelset=ALL&bl.i63.coins=0&bl.i43.coins=0&bl.i30.coins=0&bl.i39.line=1%2C1%2C2%2C1%2C2&rs.i1.r.i3.hold=false&totalwin.coins=0&bl.i5.line=0%2C0%2C1%2C0%2C1&gamestate.current=basic&bl.i28.coins=0&bl.i27.line=1%2C0%2C1%2C0%2C1&jackpotcurrency=%26%23x20AC%3B&bl.i7.line=0%2C0%2C1%2C1%2C2&bl.i35.id=35&bl.i54.reelset=ALL&rs.i0.r.i3.syms=SYM4%2CSYM4%2CSYM4%2CSYM4&rs.i1.r.i1.syms=SYM7%2CSYM7%2CSYM7&bl.i16.coins=0&bl.i54.line=2%2C1%2C1%2C0%2C0&bl.i36.coins=0&bl.i56.coins=0&bl.i9.coins=0&bl.i30.line=1%2C0%2C1%2C2%2C2&bl.i7.reelset=ALL&bl.i68.line=2%2C2%2C2%2C2%2C2&isJackpotWin=false&bl.i59.reelset=ALL&bl.i45.reelset=ALL&bl.i24.id=24&bl.i41.id=41&rs.i0.r.i1.pos=0&bl.i22.coins=0&rs.i1.r.i3.syms=SYM8%2CSYM8%2CSYM8%2CSYM8&bl.i63.reelset=ALL&bl.i29.coins=0&bl.i31.reelset=ALL&bl.i13.id=13&bl.i36.id=36&bl.i75.coins=0&bl.i62.coins=0&rs.i0.r.i1.hold=false&bl.i75.line=2%2C2%2C3%2C3%2C4&bl.i9.line=0%2C0%2C1%2C2%2C3&bl.i69.id=69&bl.i40.line=1%2C1%2C2%2C2%2C2&bl.i35.coins=0&bl.i42.id=42&bl.i44.line=1%2C2%2C2%2C1%2C1&bl.i68.coins=0&bl.i72.reelset=ALL&bl.i42.reelset=ALL&bl.i75.id=75&betlevel.standard=1&bl.i10.reelset=ALL&bl.i66.line=2%2C2%2C2%2C1%2C1&gameover=true&bl.i25.reelset=ALL&bl.i58.id=58&bl.i51.coins=0&bl.i23.coins=0&bl.i11.coins=0&bl.i64.id=64&bl.i22.reelset=ALL&bl.i13.reelset=ALL&bl.i0.id=0&bl.i70.id=70&bl.i47.id=47&nextaction=spin&bl.i15.line=0%2C1%2C1%2C2%2C3&bl.i69.coins=0&bl.i3.line=0%2C0%2C0%2C1%2C2&bl.i19.id=19&bl.i51.reelset=ALL&bl.i4.reelset=ALL&bl.i53.id=53&bl.i4.coins=0&bl.i37.line=1%2C1%2C1%2C2%2C3&bl.i18.line=0%2C1%2C2%2C2%2C2&bl.i9.id=9&bl.i34.id=34&bl.i17.line=0%2C1%2C2%2C1%2C2&bl.i62.reelset=ALL&bl.i11.id=11&bl.i57.id=57&bl.i37.coins=0&playercurrency=%26%23x20AC%3B&bl.i67.coins=0&bl.i9.reelset=ALL&bl.i17.coins=0&bl.i28.id=28&bl.i64.line=2%2C1%2C2%2C3%2C3&bl.i63.id=63&bl.i19.reelset=ALL&bl.i40.id=40&bl.i11.reelset=ALL&bl.i16.line=0%2C1%2C2%2C1%2C1&rs.i0.id=freespin&bl.i38.reelset=ALL&credit=' . $balanceInCents . '&bl.i21.line=0%2C1%2C2%2C3%2C4&bl.i35.line=1%2C1%2C1%2C1%2C2&bl.i63.line=2%2C1%2C2%2C2%2C3&bl.i41.coins=0&bl.i1.reelset=ALL&bl.i71.reelset=ALL&bl.i21.coins=0&bl.i28.reelset=ALL&bl.i1.line=0%2C0%2C0%2C0%2C1&bl.i46.line=1%2C2%2C2%2C2%2C2&bl.i45.id=45&bl.i65.line=2%2C1%2C2%2C3%2C4&bl.i51.id=51&bl.i17.id=17&rs.i1.r.i2.pos=0&bl.i16.reelset=ALL&bl.i64.coins=0&nearwinallowed=true&bl.i44.coins=0&bl.i47.reelset=ALL&bl.i45.line=1%2C2%2C2%2C1%2C2&bl.i8.line=0%2C0%2C1%2C2%2C2&bl.i65.coins=0&bl.i35.reelset=ALL&bl.i72.coins=0&bl.i42.coins=0&bl.i44.reelset=ALL&bl.i46.id=46&bl.i74.reelset=ALL&bl.i8.coins=0&bl.i23.id=23&bl.i15.coins=0&bl.i36.line=1%2C1%2C1%2C2%2C2&bl.i2.line=0%2C0%2C0%2C1%2C1&bl.i52.id=52&rs.i1.r.i2.syms=SYM0%2CSYM7%2CSYM7%2CSYM7&totalwin.cents=0&bl.i38.coins=0&bl.i56.reelset=ALL&rs.i0.r.i0.hold=false&restore=false&rs.i1.id=basic&bl.i12.id=12&bl.i29.id=29&bl.i53.reelset=ALL&bl.i4.id=4&rs.i0.r.i4.pos=0&bl.i7.coins=0&bl.i71.coins=0&bl.i66.coins=0&bl.i6.reelset=ALL&bl.i68.id=68&bl.i20.line=0%2C1%2C2%2C3%2C3&bl.i20.reelset=ALL&wavecount=1&bl.i14.coins=0&bl.i65.reelset=ALL&bl.i74.id=74&rs.i1.r.i1.hold=false&bl.i26.line=1%2C0%2C1%2C0%2C0' . $curReels . $freeState;
                            break;
                        case 'paytable':
                            $result_tmp[] = 'bl.i32.reelset=ALL&bl.i49.reelset=ALL&bl.i17.reelset=ALL&bl.i15.id=15&pt.i0.comp.i17.symbol=SYM8&pt.i0.comp.i5.freespins=0&bl.i73.id=73&pt.i0.comp.i13.symbol=SYM6&bl.i53.coins=0&pt.i1.comp.i8.type=betline&bl.i50.id=50&bl.i55.line=2%2C1%2C1%2C0%2C1&pt.i1.comp.i4.n=2&pt.i0.comp.i15.multi=5&bl.i10.line=0%2C1%2C1%2C0%2C0&bl.i40.coins=0&bl.i18.coins=0&pt.i1.comp.i3.multi=200&bl.i60.line=2%2C1%2C2%2C1%2C1&pt.i0.comp.i11.n=3&bl.i4.line=0%2C0%2C1%2C0%2C0&bl.i13.coins=0&bl.i62.id=62&bl.i27.id=27&bl.i43.line=1%2C1%2C2%2C3%2C4&pt.i0.id=basic&pt.i0.comp.i1.type=betline&bl.i2.id=2&bl.i38.line=1%2C1%2C2%2C1%2C1&pt.i1.comp.i10.type=betline&bl.i50.reelset=ALL&pt.i0.comp.i4.symbol=SYM4&pt.i1.comp.i5.freespins=0&pt.i1.comp.i8.symbol=SYM5&bl.i14.reelset=ALL&pt.i1.comp.i19.n=5&pt.i0.comp.i17.freespins=0&bl.i38.id=38&bl.i39.coins=0&pt.i0.comp.i8.symbol=SYM5&pt.i0.comp.i0.symbol=SYM3&bl.i64.reelset=ALL&bl.i59.line=2%2C1%2C1%2C2%2C3&pt.i0.comp.i3.freespins=0&pt.i0.comp.i10.multi=30&pt.i1.id=freespin&bl.i61.id=61&bl.i3.id=3&bl.i22.line=1%2C0%2C0%2C0%2C0&bl.i8.reelset=ALL&clientaction=paytable&bl.i67.reelset=ALL&bl.i45.coins=0&bl.i16.id=16&bl.i39.id=39&pt.i1.comp.i5.n=3&bl.i5.coins=0&pt.i1.comp.i8.multi=4&bl.i58.coins=0&pt.i0.comp.i22.type=scatter&pt.i0.comp.i21.multi=0&pt.i1.comp.i13.multi=30&pt.i0.comp.i12.n=4&pt.i0.comp.i13.type=betline&bl.i72.line=2%2C2%2C3%2C2%2C2&bl.i0.line=0%2C0%2C0%2C0%2C0&pt.i1.comp.i7.freespins=0&bl.i34.line=1%2C1%2C1%2C1%2C1&bl.i46.reelset=ALL&bl.i31.line=1%2C0%2C1%2C2%2C3&pt.i0.comp.i3.multi=200&bl.i34.coins=0&bl.i74.coins=0&pt.i0.comp.i21.n=4&bl.i47.coins=0&pt.i1.comp.i6.n=4&bl.i1.id=1&bl.i75.reelset=ALL&bl.i43.reelset=ALL&bl.i47.line=1%2C2%2C2%2C2%2C3&bl.i48.id=48&bl.i51.line=1%2C2%2C3%2C2%2C3&pt.i0.comp.i10.type=betline&pt.i1.comp.i11.symbol=SYM6&bl.i25.id=25&pt.i0.comp.i5.multi=8&pt.i1.comp.i1.freespins=0&bl.i61.coins=0&bl.i40.reelset=ALL&bl.i14.id=14&bl.i52.line=1%2C2%2C3%2C3%2C3&bl.i57.reelset=ALL&pt.i1.comp.i16.symbol=SYM7&pt.i1.comp.i4.type=betline&pt.i1.comp.i18.multi=5&bl.i2.coins=0&bl.i21.reelset=ALL&bl.i72.id=72&pt.i0.comp.i8.multi=4&pt.i0.comp.i1.freespins=0&bl.i5.reelset=ALL&bl.i24.coins=0&pt.i0.comp.i22.n=5&bl.i32.coins=0&bl.i59.id=59&pt.i1.comp.i17.type=betline&pt.i1.comp.i0.symbol=SYM3&pt.i1.comp.i7.n=5&bl.i67.line=2%2C2%2C2%2C1%2C2&pt.i1.comp.i5.multi=8&bl.i49.id=49&bl.i61.reelset=ALL&bl.i14.line=0%2C1%2C1%2C2%2C2&pt.i0.comp.i21.type=scatter&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&bl.i71.id=71&pt.i0.comp.i8.type=betline&pt.i0.comp.i7.freespins=0&pt.i1.comp.i15.multi=5&bl.i55.coins=0&pt.i0.comp.i13.multi=30&bl.i39.reelset=ALL&pt.i0.comp.i17.type=betline&bl.i13.line=0%2C1%2C1%2C1%2C2&bl.i24.reelset=ALL&bl.i58.line=2%2C1%2C1%2C2%2C2&bl.i0.coins=20&bl.i2.reelset=ALL&pt.i0.comp.i10.n=5&pt.i1.comp.i6.multi=15&bl.i37.id=37&bl.i60.id=60&pt.i1.comp.i19.symbol=SYM8&pt.i0.comp.i22.freespins=16&bl.i26.coins=0&bl.i27.reelset=ALL&pt.i0.comp.i20.symbol=SYM0&bl.i29.line=1%2C0%2C1%2C1%2C2&pt.i0.comp.i15.freespins=0&bl.i23.line=1%2C0%2C0%2C0%2C1&bl.i26.id=26&pt.i0.comp.i0.n=2&bl.i42.line=1%2C1%2C2%2C3%2C3&pt.i0.comp.i0.type=betline&pt.i1.comp.i0.multi=1&g4mode=false&bl.i50.coins=0&pt.i1.comp.i8.n=3&bl.i30.id=30&bl.i25.line=1%2C0%2C0%2C1%2C2&pt.i0.comp.i16.symbol=SYM7&bl.i73.line=2%2C2%2C3%2C2%2C3&pt.i0.comp.i1.multi=12&pt.i1.comp.i9.type=betline&bl.i18.id=18&bl.i68.reelset=ALL&bl.i43.coins=0&pt.i1.comp.i17.multi=3&pt.i0.comp.i18.multi=5&bl.i5.line=0%2C0%2C1%2C0%2C1&bl.i28.coins=0&pt.i0.comp.i9.n=4&bl.i27.line=1%2C0%2C1%2C0%2C1&bl.i7.line=0%2C0%2C1%2C1%2C2&pt.i1.comp.i18.type=betline&pt.i0.comp.i10.symbol=SYM5&pt.i0.comp.i15.n=4&bl.i36.coins=0&bl.i30.line=1%2C0%2C1%2C2%2C2&pt.i0.comp.i21.symbol=SYM0&bl.i7.reelset=ALL&bl.i68.line=2%2C2%2C2%2C2%2C2&pt.i1.comp.i15.n=4&isJackpotWin=false&bl.i45.reelset=ALL&bl.i41.id=41&pt.i1.comp.i7.type=betline&pt.i0.comp.i10.freespins=0&pt.i0.comp.i20.multi=0&bl.i63.reelset=ALL&pt.i0.comp.i17.multi=3&bl.i29.coins=0&bl.i31.reelset=ALL&pt.i1.comp.i9.n=4&bl.i75.line=2%2C2%2C3%2C3%2C4&bl.i9.line=0%2C0%2C1%2C2%2C3&pt.i0.comp.i2.multi=30&pt.i0.comp.i0.freespins=0&bl.i40.line=1%2C1%2C2%2C2%2C2&bl.i35.coins=0&bl.i42.id=42&pt.i1.comp.i16.freespins=0&bl.i75.id=75&pt.i1.comp.i5.type=betline&bl.i25.reelset=ALL&bl.i51.coins=0&pt.i1.comp.i13.symbol=SYM6&pt.i1.comp.i17.symbol=SYM8&bl.i64.id=64&pt.i0.comp.i16.n=5&bl.i13.reelset=ALL&bl.i0.id=0&pt.i1.comp.i16.n=5&pt.i0.comp.i5.symbol=SYM4&bl.i15.line=0%2C1%2C1%2C2%2C3&pt.i1.comp.i7.symbol=SYM4&bl.i19.id=19&bl.i51.reelset=ALL&bl.i53.id=53&bl.i37.line=1%2C1%2C1%2C2%2C3&pt.i0.comp.i1.symbol=SYM3&bl.i9.id=9&bl.i17.line=0%2C1%2C2%2C1%2C2&bl.i62.reelset=ALL&pt.i1.comp.i9.freespins=0&bl.i37.coins=0&playercurrency=%26%23x20AC%3B&bl.i28.id=28&bl.i63.id=63&bl.i19.reelset=ALL&pt.i0.comp.i9.freespins=0&bl.i40.id=40&bl.i38.reelset=ALL&credit=500000&pt.i0.comp.i5.type=betline&pt.i0.comp.i11.freespins=0&bl.i35.line=1%2C1%2C1%2C1%2C2&bl.i63.line=2%2C1%2C2%2C2%2C3&bl.i41.coins=0&bl.i1.reelset=ALL&pt.i1.comp.i18.symbol=SYM8&pt.i1.comp.i12.symbol=SYM6&pt.i0.comp.i13.freespins=0&pt.i1.comp.i15.type=betline&pt.i1.comp.i13.type=betline&pt.i1.comp.i1.multi=12&pt.i1.comp.i8.freespins=0&pt.i0.comp.i13.n=5&pt.i1.comp.i17.n=3&bl.i65.line=2%2C1%2C2%2C3%2C4&bl.i51.id=51&bl.i17.id=17&pt.i1.comp.i17.freespins=0&bl.i44.coins=0&pt.i1.comp.i0.type=betline&pt.i1.comp.i1.symbol=SYM3&bl.i45.line=1%2C2%2C2%2C1%2C2&bl.i42.coins=0&bl.i44.reelset=ALL&bl.i74.reelset=ALL&bl.i2.line=0%2C0%2C0%2C1%2C1&bl.i52.id=52&bl.i38.coins=0&bl.i56.reelset=ALL&bl.i29.id=29&pt.i1.comp.i18.freespins=0&pt.i0.comp.i14.n=3&pt.i0.comp.i0.multi=1&bl.i6.reelset=ALL&pt.i0.comp.i19.multi=20&bl.i20.line=0%2C1%2C2%2C3%2C3&pt.i1.comp.i18.n=4&bl.i20.reelset=ALL&pt.i0.comp.i12.freespins=0&bl.i74.id=74&bl.i60.coins=0&pt.i0.comp.i19.symbol=SYM8&bl.i6.coins=0&pt.i0.comp.i15.type=betline&pt.i0.comp.i4.multi=1&pt.i0.comp.i15.symbol=SYM7&bl.i67.id=67&pt.i1.comp.i14.multi=3&pt.i0.comp.i22.multi=0&bl.i73.coins=0&bl.i21.id=21&pt.i1.comp.i19.type=betline&bl.i73.reelset=ALL&pt.i0.comp.i11.symbol=SYM6&bl.i44.id=44&bl.i23.reelset=ALL&bl.i33.coins=0&bl.i0.reelset=ALL&bl.i20.coins=0&pt.i0.comp.i16.freespins=0&bl.i74.line=2%2C2%2C3%2C3%2C3&pt.i1.comp.i6.freespins=0&bl.i41.reelset=ALL&bl.i10.id=10&bl.i56.id=56&pt.i0.comp.i4.freespins=0&bl.i3.reelset=ALL&bl.i26.reelset=ALL&bl.i24.line=1%2C0%2C0%2C1%2C1&pt.i0.comp.i19.n=5&bl.i41.line=1%2C1%2C2%2C2%2C3&pt.i0.comp.i2.symbol=SYM3&bl.i57.line=2%2C1%2C1%2C1%2C2&pt.i0.comp.i20.type=scatter&bl.i59.coins=0&pt.i0.comp.i6.symbol=SYM4&pt.i1.comp.i11.n=3&pt.i0.comp.i5.n=3&pt.i1.comp.i2.symbol=SYM3&pt.i0.comp.i3.type=betline&pt.i1.comp.i19.multi=20&bl.i53.line=1%2C2%2C3%2C3%2C4&bl.i55.id=55&bl.i28.line=1%2C0%2C1%2C1%2C1&pt.i1.comp.i6.symbol=SYM4&bl.i52.coins=0&bl.i62.line=2%2C1%2C2%2C2%2C2&pt.i0.comp.i9.multi=8&bl.i12.coins=0&pt.i0.comp.i22.symbol=SYM0&pt.i1.comp.i19.freespins=0&pt.i0.comp.i14.freespins=0&pt.i0.comp.i21.freespins=12&pt.i1.comp.i4.freespins=0&bl.i37.reelset=ALL&pt.i1.comp.i12.type=betline&bl.i55.reelset=ALL&bl.i8.id=8&pt.i0.comp.i16.multi=20&bl.i69.line=2%2C2%2C2%2C2%2C3&bl.i33.id=33&bl.i58.reelset=ALL&bl.i46.coins=0&bl.i6.line=0%2C0%2C1%2C1%2C1&bl.i22.id=22&bl.i12.line=0%2C1%2C1%2C1%2C1&pt.i1.comp.i9.multi=8&bl.i29.reelset=ALL&pt.i0.comp.i19.type=betline&pt.i0.comp.i6.freespins=0&pt.i1.comp.i2.multi=30&pt.i0.comp.i6.n=4&bl.i50.line=1%2C2%2C3%2C2%2C2&pt.i1.comp.i12.n=4&bl.i57.coins=0&pt.i1.comp.i3.type=betline&pt.i1.comp.i10.freespins=0&bl.i48.line=1%2C2%2C2%2C3%2C3&bl.i27.coins=0&bl.i34.reelset=ALL&bl.i30.reelset=ALL&bl.i33.line=1%2C1%2C1%2C0%2C1&pt.i1.comp.i2.type=betline&pt.i0.comp.i2.freespins=0&pt.i0.comp.i7.n=5&bl.i31.id=31&bl.i32.line=1%2C1%2C1%2C0%2C0&pt.i0.comp.i11.multi=4&pt.i1.comp.i14.symbol=SYM7&pt.i0.comp.i7.type=betline&bl.i19.line=0%2C1%2C2%2C2%2C3&bl.i49.line=1%2C2%2C2%2C3%2C4&bl.i12.reelset=ALL&bl.i66.id=66&pt.i0.comp.i17.n=3&bl.i6.id=6&bl.i52.reelset=ALL&pt.i1.comp.i13.n=5&pt.i0.comp.i8.freespins=0&bl.i20.id=20&bl.i66.reelset=ALL&pt.i1.comp.i4.multi=1&gamesoundurl=&pt.i0.comp.i12.type=betline&pt.i0.comp.i14.multi=3&pt.i1.comp.i7.multi=100&bl.i33.reelset=ALL&bl.i48.reelset=ALL&bl.i19.coins=0&bl.i7.id=7&bl.i18.reelset=ALL&pt.i1.comp.i11.type=betline&pt.i0.comp.i6.multi=15&playercurrencyiso=' . $slotSettings->slotCurrency . '&bl.i1.coins=0&bl.i32.id=32&pt.i1.comp.i5.symbol=SYM4&bl.i65.id=65&bl.i70.line=2%2C2%2C2%2C3%2C3&pt.i0.comp.i18.type=betline&playforfun=false&pt.i0.comp.i2.type=betline&bl.i25.coins=0&bl.i69.reelset=ALL&bl.i48.coins=0&bl.i71.line=2%2C2%2C2%2C3%2C4&pt.i0.comp.i8.n=3&bl.i70.reelset=ALL&bl.i31.coins=0&bl.i54.id=54&pt.i0.comp.i11.type=betline&pt.i0.comp.i18.n=4&bl.i54.coins=0&pt.i1.comp.i14.n=3&pt.i1.comp.i16.multi=20&bl.i43.id=43&pt.i1.comp.i15.freespins=0&bl.i49.coins=0&pt.i0.comp.i7.symbol=SYM4&bl.i61.line=2%2C1%2C2%2C1%2C2&bl.i15.reelset=ALL&pt.i1.comp.i0.freespins=0&bl.i70.coins=0&bl.i11.line=0%2C1%2C1%2C0%2C1&bl.i56.line=2%2C1%2C1%2C1%2C1&historybutton=false&bl.i60.reelset=ALL&bl.i5.id=5&pt.i0.comp.i18.symbol=SYM8&bl.i36.reelset=ALL&pt.i0.comp.i12.multi=8&pt.i1.comp.i14.freespins=0&bl.i3.coins=0&bl.i10.coins=0&pt.i0.comp.i12.symbol=SYM6&pt.i0.comp.i14.symbol=SYM7&pt.i1.comp.i13.freespins=0&bl.i63.coins=0&pt.i0.comp.i14.type=betline&bl.i30.coins=0&bl.i39.line=1%2C1%2C2%2C1%2C2&pt.i1.comp.i0.n=2&pt.i0.comp.i7.multi=100&jackpotcurrency=%26%23x20AC%3B&bl.i35.id=35&bl.i54.reelset=ALL&bl.i16.coins=0&bl.i54.line=2%2C1%2C1%2C0%2C0&bl.i56.coins=0&bl.i9.coins=0&bl.i59.reelset=ALL&bl.i24.id=24&pt.i1.comp.i11.multi=4&pt.i0.comp.i1.n=3&bl.i22.coins=0&pt.i0.comp.i20.n=3&pt.i1.comp.i3.symbol=SYM3&bl.i13.id=13&bl.i36.id=36&bl.i75.coins=0&bl.i62.coins=0&pt.i0.comp.i9.type=betline&bl.i69.id=69&pt.i1.comp.i16.type=betline&bl.i44.line=1%2C2%2C2%2C1%2C1&bl.i68.coins=0&bl.i72.reelset=ALL&bl.i42.reelset=ALL&bl.i10.reelset=ALL&pt.i1.comp.i12.multi=8&bl.i66.line=2%2C2%2C2%2C1%2C1&pt.i1.comp.i1.n=3&pt.i1.comp.i11.freespins=0&bl.i58.id=58&pt.i0.comp.i9.symbol=SYM5&bl.i23.coins=0&bl.i11.coins=0&bl.i22.reelset=ALL&bl.i70.id=70&bl.i47.id=47&pt.i0.comp.i16.type=betline&bl.i69.coins=0&bl.i3.line=0%2C0%2C0%2C1%2C2&bl.i4.reelset=ALL&bl.i4.coins=0&pt.i0.comp.i2.n=4&bl.i18.line=0%2C1%2C2%2C2%2C2&bl.i34.id=34&pt.i0.comp.i19.freespins=0&pt.i1.comp.i14.type=betline&bl.i11.id=11&pt.i0.comp.i6.type=betline&bl.i57.id=57&pt.i1.comp.i2.freespins=0&bl.i67.coins=0&bl.i9.reelset=ALL&bl.i17.coins=0&bl.i64.line=2%2C1%2C2%2C3%2C3&pt.i1.comp.i10.multi=30&pt.i1.comp.i10.symbol=SYM5&bl.i11.reelset=ALL&bl.i16.line=0%2C1%2C2%2C1%2C1&pt.i1.comp.i2.n=4&bl.i21.line=0%2C1%2C2%2C3%2C4&bl.i71.reelset=ALL&pt.i0.comp.i4.type=betline&bl.i21.coins=0&bl.i28.reelset=ALL&pt.i1.comp.i1.type=betline&bl.i1.line=0%2C0%2C0%2C0%2C1&bl.i46.line=1%2C2%2C2%2C2%2C2&bl.i45.id=45&pt.i0.comp.i20.freespins=8&bl.i16.reelset=ALL&bl.i64.coins=0&pt.i0.comp.i3.n=5&bl.i47.reelset=ALL&pt.i1.comp.i6.type=betline&pt.i1.comp.i4.symbol=SYM4&bl.i8.line=0%2C0%2C1%2C2%2C2&bl.i65.coins=0&bl.i35.reelset=ALL&bl.i72.coins=0&bl.i46.id=46&bl.i8.coins=0&bl.i23.id=23&bl.i15.coins=0&bl.i36.line=1%2C1%2C1%2C2%2C2&pt.i1.comp.i3.n=5&pt.i0.comp.i18.freespins=0&bl.i12.id=12&pt.i1.comp.i15.symbol=SYM7&bl.i53.reelset=ALL&pt.i1.comp.i3.freespins=0&bl.i4.id=4&bl.i7.coins=0&bl.i71.coins=0&bl.i66.coins=0&pt.i1.comp.i9.symbol=SYM5&bl.i68.id=68&pt.i0.comp.i3.symbol=SYM3&bl.i14.coins=0&bl.i65.reelset=ALL&pt.i1.comp.i12.freespins=0&pt.i0.comp.i4.n=2&pt.i1.comp.i10.n=5&bl.i26.line=1%2C0%2C1%2C0%2C0';
                        case 'initfreespin':
                            $result_tmp[] = 'rs.i1.r.i0.syms=SYM5%2CSYM7%2CSYM7&freespins.betlevel=1&g4mode=false&freespins.win.coins=0&playercurrency=%26%23x20AC%3B&historybutton=false&rs.i0.r.i4.hold=false&gamestate.history=basic&rs.i1.r.i2.hold=false&rs.i1.r.i3.pos=108&rs.i0.r.i1.syms=SYM8%2CSYM8%2CSYM5&game.win.cents=120&rs.i0.id=freespin&rs.i1.r.i3.hold=false&totalwin.coins=60&credit=498036&rs.i1.r.i4.pos=51&gamestate.current=freespin&freespins.initial=8&jackpotcurrency=%26%23x20AC%3B&multiplier=1&bet.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&rs.i0.r.i0.syms=SYM5%2CSYM5%2CSYM3&freespins.denomination=2.000&rs.i0.r.i3.syms=SYM4%2CSYM4%2CSYM4%2CSYM4&rs.i1.r.i1.syms=SYM6%2CSYM6%2CSYM7&rs.i1.r.i1.pos=86&freespins.win.cents=0&freespins.totalwin.coins=0&freespins.total=8&isJackpotWin=false&gamestate.stack=basic%2Cfreespin&rs.i0.r.i0.pos=0&rs.i1.r.i4.syms=SYM8%2CSYM0%2CSYM5%2CSYM5%2CSYM5&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20%2C21%2C22%2C23%2C24%2C25%2C26%2C27%2C28%2C29%2C30%2C31%2C32%2C33%2C34%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C44%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C52%2C53%2C54%2C55%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75&gamesoundurl=&rs.i1.r.i2.pos=157&bet.betlevel=1&rs.i1.nearwin=4&rs.i0.r.i1.pos=0&rs.i1.r.i3.syms=SYM0%2CSYM8%2CSYM8%2CSYM8&game.win.coins=60&playercurrencyiso=' . $slotSettings->slotCurrency . '&rs.i1.r.i0.hold=false&rs.i0.r.i1.hold=false&freespins.wavecount=1&freespins.multiplier=1&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . "&clientaction=initfreespin&rs.i0.r.i2.hold=false&rs.i0.r.i4.syms=SYM6%2CSYM6%2CSYM6%2CSYM6%2CSYM6&rs.i0.r.i2.pos=0&rs.i1.r.i2.syms=SYM4%2CSYM0%2CSYM7%2CSYM7&rs.i1.r.i0.pos=166&totalwin.cents=120&gameover=false&rs.i0.r.i0.hold=false&rs.i1.id=basic&rs.i0.r.i3.pos=0&rs.i1.r.i4.hold=false&freespins.left=8&rs.i0.r.i4.pos=0&rs.i1.r.i2.attention.i0=1&rs.i1.r.i3.attention.i0=0&nextaction=freespin&wavecount=1&rs.i1.r.i4.attention.i0=1&rs.i0.r.i2.syms=SYM4%2CSYM4%2CSYM4%2CSYM7&rs.i1.r.i1.hold=false&rs.i0.r.i3.hold=false&game.win.amount=1.20&bet.denomination=2&freespins.totalwin.cents=0\n";
                            break;
                        case 'spin':
                            $linesId = [];
                            $linesId[0] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[2] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[3] = [
                                1, 
                                1, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[4] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[5] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[6] = [
                                1, 
                                1, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[7] = [
                                1, 
                                1, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[8] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[9] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[10] = [
                                1, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[12] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[13] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[14] = [
                                1, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[15] = [
                                1, 
                                2, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[16] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[17] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[18] = [
                                1, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                2, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[20] = [
                                1, 
                                2, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[21] = [
                                1, 
                                2, 
                                3, 
                                4, 
                                5
                            ];
                            $linesId[22] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[23] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[24] = [
                                2, 
                                1, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[25] = [
                                2, 
                                1, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[26] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[27] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[28] = [
                                2, 
                                1, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[29] = [
                                2, 
                                1, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[30] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[31] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[32] = [
                                2, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[33] = [
                                2, 
                                2, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[34] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[35] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[36] = [
                                2, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[37] = [
                                2, 
                                2, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[38] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[39] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[40] = [
                                2, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[41] = [
                                2, 
                                2, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[42] = [
                                2, 
                                2, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[43] = [
                                2, 
                                2, 
                                3, 
                                4, 
                                5
                            ];
                            $linesId[44] = [
                                2, 
                                3, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[45] = [
                                2, 
                                3, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[46] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[47] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[48] = [
                                2, 
                                3, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[49] = [
                                2, 
                                3, 
                                3, 
                                4, 
                                5
                            ];
                            $linesId[50] = [
                                2, 
                                3, 
                                4, 
                                3, 
                                3
                            ];
                            $linesId[51] = [
                                2, 
                                3, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[52] = [
                                2, 
                                3, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[53] = [
                                2, 
                                3, 
                                4, 
                                4, 
                                5
                            ];
                            $linesId[54] = [
                                3, 
                                2, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[55] = [
                                3, 
                                2, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[56] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[57] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[58] = [
                                3, 
                                2, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[59] = [
                                3, 
                                2, 
                                2, 
                                3, 
                                4
                            ];
                            $linesId[60] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[61] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[62] = [
                                3, 
                                2, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[63] = [
                                3, 
                                2, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[64] = [
                                3, 
                                2, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[65] = [
                                3, 
                                2, 
                                3, 
                                4, 
                                5
                            ];
                            $linesId[66] = [
                                3, 
                                3, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[67] = [
                                3, 
                                3, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[68] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[69] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                4
                            ];
                            $linesId[70] = [
                                3, 
                                3, 
                                3, 
                                4, 
                                4
                            ];
                            $linesId[71] = [
                                3, 
                                3, 
                                3, 
                                4, 
                                5
                            ];
                            $linesId[72] = [
                                3, 
                                3, 
                                4, 
                                3, 
                                3
                            ];
                            $linesId[73] = [
                                3, 
                                3, 
                                4, 
                                3, 
                                4
                            ];
                            $linesId[74] = [
                                3, 
                                3, 
                                4, 
                                4, 
                                4
                            ];
                            $linesId[75] = [
                                3, 
                                3, 
                                4, 
                                4, 
                                5
                            ];
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
                                $slotSettings->SetGameData('DazzleMeNETBonusWin', 0);
                                $slotSettings->SetGameData('DazzleMeNETFreeGames', 0);
                                $slotSettings->SetGameData('DazzleMeNETCurrentFreeGame', 0);
                                $slotSettings->SetGameData('DazzleMeNETTotalWin', 0);
                                $slotSettings->SetGameData('DazzleMeNETBet', $betline);
                                $slotSettings->SetGameData('DazzleMeNETDenom', $postData['bet_denomination']);
                                $slotSettings->SetGameData('DazzleMeNETFreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $postData['bet_denomination'] = $slotSettings->GetGameData('DazzleMeNETDenom');
                                $slotSettings->CurrentDenom = $postData['bet_denomination'];
                                $slotSettings->CurrentDenomination = $postData['bet_denomination'];
                                $betline = $slotSettings->GetGameData('DazzleMeNETBet');
                                $allbet = $betline * $lines;
                                $slotSettings->SetGameData('DazzleMeNETCurrentFreeGame', $slotSettings->GetGameData('DazzleMeNETCurrentFreeGame') + 1);
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
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
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
                                $tmpReels = $reels;
                                $randWild = rand(1, 50);
                                $isWild = false;
                                if( $randWild == 1 ) 
                                {
                                    $rr = rand(1, 2);
                                    if( $rr == 1 ) 
                                    {
                                        $reels['reel3'][0] = '1';
                                        $reels['reel3'][1] = '1';
                                        $reels['reel3'][2] = '1';
                                        $reels['reel3'][3] = '1';
                                    }
                                    if( $rr == 2 ) 
                                    {
                                        $reels['reel4'][0] = '1';
                                        $reels['reel3'][1] = '1';
                                        $reels['reel3'][2] = '1';
                                        $reels['reel3'][3] = '1';
                                    }
                                    $isWild = true;
                                }
                                $winLineCount = 0;
                                for( $k = 0; $k < 76; $k++ ) 
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
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 4; $p++ ) 
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
                            $freeState = '';
                            $wildStr = '';
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                                if( $isWild ) 
                                {
                                    if( $rr = 1 ) 
                                    {
                                        $wildStr = '&rs.i0.r.i2.overlay.i3.row=3&rs.i0.r.i2.overlay.i2.row=2&rs.i0.r.i2.overlay.i3.pos=176&rs.i0.r.i2.overlay.i1.row=1&rs.i0.r.i2.overlay.i2.with=SYM1&rs.i0.r.i2.overlay.i2.pos=175&rs.i0.r.i2.overlay.i0.row=0&rs.i0.r.i2.overlay.i1.pos=174&rs.i0.r.i2.overlay.i0.with=SYM1&rs.i0.r.i2.overlay.i1.with=SYM1&rs.i0.r.i2.overlay.i3.with=SYM1&rs.i0.r.i2.overlay.i0.pos=173';
                                    }
                                    if( $rr = 2 ) 
                                    {
                                        $wildStr = '&rs.i0.r.i3.overlay.i3.row=3&rs.i0.r.i3.overlay.i2.row=2&rs.i0.r.i3.overlay.i3.pos=176&rs.i0.r.i3.overlay.i1.row=1&rs.i0.r.i3.overlay.i2.with=SYM1&rs.i0.r.i3.overlay.i2.pos=175&rs.i0.r.i3.overlay.i0.row=0&rs.i0.r.i3.overlay.i1.pos=174&rs.i0.r.i3.overlay.i0.with=SYM1&rs.i0.r.i3.overlay.i1.with=SYM1&rs.i0.r.i3.overlay.i3.with=SYM1&rs.i0.r.i3.overlay.i0.pos=173';
                                    }
                                }
                            }
                            $reels = $tmpReels;
                            $reportWin = $totalWin;
                            $curReels = '&rs.i0.r.i0.syms=SYM' . $reels['reel1'][0] . '%2CSYM' . $reels['reel1'][1] . '%2CSYM' . $reels['reel1'][2] . '';
                            $curReels .= ('&rs.i0.r.i1.syms=SYM' . $reels['reel2'][0] . '%2CSYM' . $reels['reel2'][1] . '%2CSYM' . $reels['reel2'][2] . '');
                            $curReels .= ('&rs.i0.r.i2.syms=SYM' . $reels['reel3'][0] . '%2CSYM' . $reels['reel3'][1] . '%2CSYM' . $reels['reel3'][2] . '%2CSYM' . $reels['reel3'][3] . '');
                            $curReels .= ('&rs.i0.r.i3.syms=SYM' . $reels['reel4'][0] . '%2CSYM' . $reels['reel4'][1] . '%2CSYM' . $reels['reel4'][2] . '%2CSYM' . $reels['reel4'][3] . '');
                            $curReels .= ('&rs.i0.r.i4.syms=SYM' . $reels['reel5'][0] . '%2CSYM' . $reels['reel5'][1] . '%2CSYM' . $reels['reel5'][2] . '%2CSYM' . $reels['reel5'][3] . '%2CSYM' . $reels['reel5'][4] . '');
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData('DazzleMeNETBonusWin', $slotSettings->GetGameData('DazzleMeNETBonusWin') + $totalWin);
                                $slotSettings->SetGameData('DazzleMeNETTotalWin', $slotSettings->GetGameData('DazzleMeNETTotalWin') + $totalWin);
                            }
                            else
                            {
                                $slotSettings->SetGameData('DazzleMeNETTotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $scattersCount >= 3 ) 
                            {
                                $slotSettings->SetGameData('DazzleMeNETFreeStartWin', $totalWin);
                                $slotSettings->SetGameData('DazzleMeNETBonusWin', $totalWin);
                                $slotSettings->SetGameData('DazzleMeNETFreeGames', $slotSettings->slotFreeCount[$scattersCount]);
                                $fs = $slotSettings->GetGameData('DazzleMeNETFreeGames');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=freespin&freespins.left=' . $fs . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=basic%2Cfreespin&freespins.totalwin.coins=0&freespins.total=' . $fs . '&freespins.win.cents=0&gamestate.current=freespin&freespins.initial=' . $fs . '&freespins.win.coins=0&freespins.betlevel=' . $slotSettings->GetGameData('DazzleMeNETBet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
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
                            $slotSettings->SetGameData('DazzleMeNETGambleStep', 5);
                            $hist = $slotSettings->GetGameData('DazzleMeNETCards');
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
                                $totalWin = $slotSettings->GetGameData('DazzleMeNETBonusWin');
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData('DazzleMeNETBonusWin') > 0 ) 
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
                                $fs = $slotSettings->GetGameData('DazzleMeNETFreeGames');
                                $fsl = $slotSettings->GetGameData('DazzleMeNETFreeGames') - $slotSettings->GetGameData('DazzleMeNETCurrentFreeGame');
                                $freeState = '&freespins.betlines=0%2C1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19&freespins.totalwin.cents=0&nextaction=' . $nextaction . '&freespins.left=' . $fsl . '&freespins.wavecount=1&freespins.multiplier=1&gamestate.stack=' . $stack . '&freespins.totalwin.coins=' . $totalWin . '&freespins.total=' . $fs . '&freespins.win.cents=' . ($totalWin / $slotSettings->CurrentDenomination * 100) . '&gamestate.current=' . $gamestate . '&freespins.initial=' . $fs . '&freespins.win.coins=' . $totalWin . '&freespins.betlevel=' . $slotSettings->GetGameData('DazzleMeNETBet') . '&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '';
                                $curReels .= $freeState;
                            }
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"freeState":"' . $freeState . '","slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('DazzleMeNETFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('DazzleMeNETCurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData('DazzleMeNETBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $balanceInCents = round($slotSettings->GetBalance() * $slotSettings->CurrentDenom * 100);
                            $result_tmp[] = 'rs.i0.r.i1.pos=18&g4mode=false&game.win.coins=' . $totalWin . '&playercurrency=%26%23x20AC%3B&playercurrencyiso=' . $slotSettings->slotCurrency . '&historybutton=false&rs.i0.r.i1.hold=false&rs.i0.r.i4.hold=false&gamestate.history=basic&playforfun=false&jackpotcurrencyiso=' . $slotSettings->slotCurrency . '&clientaction=spin&rs.i0.r.i2.hold=false&game.win.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&rs.i0.r.i2.pos=47&rs.i0.id=basic&totalwin.coins=' . $totalWin . '&credit=' . $balanceInCents . '&totalwin.cents=' . ($totalWin * $slotSettings->CurrentDenomination * 100) . '&gamestate.current=basic&gameover=true&rs.i0.r.i0.hold=false&jackpotcurrency=%26%23x20AC%3B&multiplier=1&rs.i0.r.i3.pos=4&rs.i0.r.i4.pos=5&isJackpotWin=false&gamestate.stack=basic&nextaction=spin&rs.i0.r.i0.pos=7&wavecount=1&gamesoundurl=&rs.i0.r.i3.hold=false&game.win.amount=' . ($totalWin / $slotSettings->CurrentDenomination) . '' . $curReels . $winString . $wildStr;
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
