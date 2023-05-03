<?php 
namespace VanguardLTE\Games\RouletteRoyalAM
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
                    $floatBet = 100;
                    $response = '';
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                    $gameData = [];
                    $tmpPar = explode(',', $postData['gameData']);
                    $gameData['slotEvent'] = $tmpPar[0];
                    if( $gameData['slotEvent'] == 'A/u251' || $gameData['slotEvent'] == 'A/u256' ) 
                    {
                        if( $gameData['slotEvent'] == 'A/u256' && $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') <= $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') > 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid bonus state"}';
                            exit( $response );
                        }
                        if( $slotSettings->GetBalance() < ($tmpPar[1] * $slotSettings->Bet[$tmpPar[2]]) && $gameData['slotEvent'] == 'A/u251' ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid balance"}';
                            exit( $response );
                        }
                        if( !isset($slotSettings->Bet[$tmpPar[2]]) || $tmpPar[1] <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid bet/lines"}';
                            exit( $response );
                        }
                    }
                    if( $gameData['slotEvent'] == 'A/u257' && $slotSettings->GetGameData($slotSettings->slotId . 'DoubleWin') <= 0 ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"' . $gameData['slotEvent'] . '","serverResponse":"invalid gamble state"}';
                        exit( $response );
                    }
                    if( $gameData['slotEvent'] == 'A/u256' ) 
                    {
                        $postData['spinType'] = 'free';
                        $gameData['slotEvent'] = 'A/u251';
                    }
                    else
                    {
                        $postData['spinType'] = 'regular';
                    }
                    switch( $gameData['slotEvent'] ) 
                    {
                        case 'A/u350':
                            $winall = $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin');
                            if( !is_numeric($winall) ) 
                            {
                                $winall = 0;
                            }
                            $balance = $slotSettings->GetBalance() - $winall;
                            $response = 'UPDATE#' . (sprintf('%01.2f', $balance) * $floatBet);
                            break;
                        case 'A/u25':
                            $slotSettings->SetGameData($slotSettings->slotId . 'Cards', [
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00', 
                                '00'
                            ]);
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', 0);
                            $betsArr = $slotSettings->Bet;
                            $betString = '';
                            for( $b = 0; $b < count($betsArr); $b++ ) 
                            {
                                $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                                $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                            }
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $HalfBetLimit = 1000;
                            $TableBetLimit = 10000;
                            $SixBetLimit = 100;
                            $TwelveBetLimit = 100;
                            $StraightBetLimit = 10;
                            $SplitBetLimit = 10;
                            $SquareBetLimit = 10;
                            $LineBetLimit = 10;
                            $TableBetLimitMin = 1;
                            $NumHistory = '';
                            $NumHistoryArr = [];
                            for( $i = 1; $i <= 16; $i++ ) 
                            {
                                $num = $slotSettings->HexFormat(rand(0, 36));
                                $NumHistoryArr[] = $num;
                                $NumHistory .= $num;
                            }
                            if( $slotSettings->HasGameData('RouletteRoyalAMHistory') ) 
                            {
                                $NumHistory = implode('', $slotSettings->GetGameData('RouletteRoyalAMHistory'));
                            }
                            else
                            {
                                $slotSettings->SetGameData('RouletteRoyalAMHistory', $NumHistoryArr);
                            }
                            $response = '005010' . $balanceFormated . '108ffffffff10' . $betString . $slotSettings->HexFormat($TableBetLimit * 100) . $slotSettings->HexFormat($StraightBetLimit * 100) . $slotSettings->HexFormat($SplitBetLimit * 100) . $slotSettings->HexFormat($LineBetLimit * 100) . $slotSettings->HexFormat($SquareBetLimit * 100) . $slotSettings->HexFormat($SixBetLimit * 100) . $slotSettings->HexFormat($TwelveBetLimit * 100) . $slotSettings->HexFormat($HalfBetLimit * 100) . $slotSettings->HexFormat($TableBetLimitMin * 100) . '1010101010101010' . $NumHistory . '10101010101010101010101010101010101010101010101010101010101010101010101010null#';
                            break;
                        case 'A/u250':
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response = '100010' . $balanceFormated . '108ffffffff10null';
                            break;
                        case 'A/u291':
                            $tmpPar = explode('A/u291,', $postData['gameData']);
                            $paysArr = [];
                            $paysArr['straight'] = 36;
                            $paysArr['split'] = 18;
                            $paysArr['street'] = 12;
                            $paysArr['corner'] = 9;
                            $paysArr['line'] = 6;
                            $paysArr['column'] = 3;
                            $paysArr['twelve'] = 3;
                            $paysArr['low'] = 2;
                            $paysArr['high'] = 2;
                            $paysArr['red'] = 2;
                            $paysArr['black'] = 2;
                            $paysArr['odd'] = 2;
                            $paysArr['even'] = 2;
                            $bsArr = [];
                            $bsArr['zero'] = 0;
                            $bsArr['straight'] = 0;
                            $bsArr['split'] = 0;
                            $bsArr['street'] = 0;
                            $bsArr['corner'] = 0;
                            $bsArr['line'] = 0;
                            $bsArr['column'] = 0;
                            $bsArr['twelve'] = 0;
                            $bsArr['low'] = 0;
                            $bsArr['high'] = 0;
                            $bsArr['red'] = 0;
                            $bsArr['black'] = 0;
                            $bsArr['odd'] = 0;
                            $bsArr['even'] = 0;
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $randNumber = rand(0, 36);
                                $wins = [];
                                $totalWin = 0;
                                $allbet = 0;
                                $postData['bets'] = explode('|', $tmpPar[1]);
                                foreach( $postData['bets'] as $key => $vl ) 
                                {
                                    $vl = explode('$', $vl);
                                    if( !isset($vl[1]) ) 
                                    {
                                        continue;
                                    }
                                    $allbet += ($vl[1] / 100);
                                    $curNums = $slotSettings->GetNumbersByField($vl[0]);
                                    if( $curNums[0] == 'straight' && $curNums[1][0] == '0' ) 
                                    {
                                        $bsArr['zero'] = 1;
                                    }
                                    else
                                    {
                                        $bsArr[$curNums[0]]++;
                                    }
                                    if( in_array($randNumber, $curNums[1]) ) 
                                    {
                                        $curWin = $paysArr[$curNums[0]] * ($vl[1] / 100);
                                        $totalWin += $curWin;
                                    }
                                }
                                if( $allbet <= 0.0001 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                $slotSettings->UpdateJackpots($allbet);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                if( $bsArr['zero'] > 0 && ($bsArr['red'] > 0 && $bsArr['black'] > 0 || $bsArr['odd'] > 0 && $bsArr['even'] > 0 || $bsArr['high'] > 0 && $bsArr['low'] > 0 || $bsArr['twelve'] >= 3 || $bsArr['column'] >= 3 || $bsArr['straight'] >= 36) ) 
                                {
                                    $bankSum = $allbet / 100 * 100;
                                }
                                if( $totalWin <= ($bank + $bankSum) ) 
                                {
                                    break;
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"Bad Reel Strip"}';
                                    exit( $response );
                                }
                            }
                            $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                            if( $bsArr['zero'] > 0 && ($bsArr['red'] > 0 && $bsArr['black'] > 0 || $bsArr['odd'] > 0 && $bsArr['even'] > 0 || $bsArr['high'] > 0 && $bsArr['low'] > 0 || $bsArr['twelve'] >= 3 || $bsArr['column'] >= 3 || $bsArr['straight'] >= 36) ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $allbet);
                                $slotSettings->SetBalance(-1 * $allbet, 'bet');
                            }
                            else
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                $slotSettings->SetBalance(-1 * $allbet, 'bet');
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $NumHistoryArr = $slotSettings->GetGameData('RouletteRoyalAMHistory');
                            array_shift($NumHistoryArr);
                            array_push($NumHistoryArr, $slotSettings->HexFormat($randNumber));
                            $slotSettings->SetGameData('RouletteRoyalAMHistory', $NumHistoryArr);
                            $response = '105010' . $balanceFormated . $slotSettings->HexFormat(round($totalWin * 100)) . $slotSettings->HexFormat($randNumber) . '10' . $tmpPar[1];
                            $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                            break;
                    }
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
