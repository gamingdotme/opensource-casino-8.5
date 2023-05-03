<?php 
namespace VanguardLTE\Games\BingoAM
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
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
                    $betsArr = $slotSettings->Bet;
                    $betString = '';
                    for( $b = 0; $b < count($betsArr); $b++ ) 
                    {
                        $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                        $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                    }
                    $minBets = '';
                    $maxBets = '';
                    $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                    $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1])) . dechex($betsArr[count($betsArr) - 1]));
                    $betsLength = count($betsArr);
                    $betsLength = dechex($betsLength);
                    if( strlen($betsLength) <= 1 ) 
                    {
                        $betsLength = '0' . $betsLength;
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
                            $minBets = '';
                            $maxBets = '';
                            $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                            $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1])) . dechex($betsArr[count($betsArr) - 1]));
                            $betsLength = count($betsArr);
                            $betsLength = dechex($betsLength);
                            if( strlen($betsLength) <= 1 ) 
                            {
                                $betsLength = '0' . $betsLength;
                            }
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response = '00001037d010' . $minBets . $maxBets . '616e360111010101010101010101010101010101010101010101010101010101010101010' . implode(',', $betsArr) . '#';
                            break;
                        case 'A/u250':
                            $betsArr = $slotSettings->Bet;
                            $betString = '';
                            for( $b = 0; $b < count($betsArr); $b++ ) 
                            {
                                $betsArr[$b] = (double)$betsArr[$b] * $floatBet;
                                $betString .= (dechex(strlen(dechex($betsArr[$b]))) . dechex($betsArr[$b]));
                            }
                            $minBets = '';
                            $maxBets = '';
                            $minBets .= (strlen(dechex($betsArr[0])) . dechex($betsArr[0]));
                            $maxBets .= (strlen(dechex($betsArr[count($betsArr) - 1])) . dechex($betsArr[count($betsArr) - 1]));
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response = '100010' . $balanceFormated . '10' . $minBets . $maxBets . '616e360111010101010101010101010101010101010101010101010101010101010101010#';
                            break;
                        case 'A/u274':
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $response_collect = $slotSettings->GetGameData('finish_answer');
                            $balanceFormated = $slotSettings->HexFormat(round($slotSettings->GetBalance() * $floatBet));
                            $response = $response_collect;
                            break;
                        case 'A/u271':
                            $betsArr = $slotSettings->Bet;
                            $gBet = $betsArr[$tmpPar[1]];
                            $allbet = $gBet;
                            $slotSettings->SetGameData('bingo_bet', $gBet);
                            $slotSettings->SetGameData('bingo_bet_h', $tmpPar[1]);
                            $postData['bet'] = $allbet;
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
                            if( !isset($postData['slotEvent']) ) 
                            {
                                $postData['slotEvent'] = 'bet';
                            }
                            $bankSum = $postData['bet'] / 100 * $slotSettings->GetPercent();
                            $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                            $slotSettings->UpdateJackpots($postData['bet']);
                            $slotSettings->SetBalance(-1 * $postData['bet'], $postData['slotEvent']);
                            $user_balance = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                            $slotSettings->SetGameData('bingo_balance', $user_balance);
                            $hexLogin = dechex(11111111);
                            $loginId = strlen($hexLogin) . $hexLogin;
                            $str_balance = str_replace('.', '', $user_balance . '');
                            $hexBalance = dechex($str_balance - 0);
                            $rtnBalance = strlen($hexBalance) . $hexBalance;
                            $rtnReels = '';
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $gloop = 1; $gloop <= 2000; $gloop++ ) 
                            {
                                $winall = 0;
                                $ballsArr = [];
                                $userBalls = explode(':', $tmpPar[2]);
                                $ballMatch = 0;
                                $ballSelected = 0;
                                for( $i = 0; $i <= 9; $i++ ) 
                                {
                                    if( $userBalls[$i] > 0 ) 
                                    {
                                        $ballSelected++;
                                    }
                                }
                                for( $i = 1; $i <= 80; $i++ ) 
                                {
                                    $ballsArr[$i - 1] = $i;
                                }
                                shuffle($ballsArr);
                                $ballsStr = '';
                                for( $i = 0; $i <= 19; $i++ ) 
                                {
                                    for( $j = 0; $j < $ballSelected; $j++ ) 
                                    {
                                        if( $ballsArr[$i] == $userBalls[$j] ) 
                                        {
                                            $ballMatch++;
                                        }
                                    }
                                    $hexBall = dechex($ballsArr[$i]);
                                    $ballsStr .= (dechex(strlen($hexBall)) . $hexBall);
                                }
                                $ballsArr0 = [];
                                for( $i = 20; $i < 80; $i++ ) 
                                {
                                    $ballsArr0[] = $ballsArr[$i];
                                }
                                $slotSettings->SetGameData('bingo_match', $ballMatch);
                                $slotSettings->SetGameData('bingo_selected', $ballSelected);
                                $slotSettings->SetGameData('bingo_balls0', $ballsArr0);
                                $slotSettings->SetGameData('bingo_balls', $ballsArr);
                                $slotSettings->SetGameData('bingo_uballs', $userBalls);
                                $winall = $slotSettings->Paytable[$ballMatch][$ballSelected] * $allbet;
                                if( $winall <= $bank ) 
                                {
                                    break;
                                }
                            }
                            if( $winall > 0 ) 
                            {
                                $slotSettings->SetBalance($winall);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $winall * -1);
                            }
                            $slotSettings->SetGameData('bingo_winall', $winall);
                            $str_winall = str_replace('.', '', $winall * $floatBet . '');
                            $str_winall = $str_winall - 0;
                            $winall_h = dechex($str_winall);
                            $loginId = '10';
                            $bet_h = dechex($tmpPar[1]);
                            $bet_h = strlen($bet_h) . $bet_h;
                            if( $winall > 0 ) 
                            {
                                $user_balance = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                                $hexLogin = dechex(11111111);
                                $loginId = strlen($hexLogin) . $hexLogin;
                                $str_balance = str_replace('.', '', $user_balance . '');
                                $hexBalance = dechex($str_balance - 0);
                                $rtnBalance = strlen($hexBalance) . $hexBalance;
                            }
                            $response = '103010' . $rtnBalance . strlen($winall_h) . $winall_h . $minBets . $maxBets . '616e360' . $bet_h . '1021121a21b21c22422522622e22f' . $ballsStr . '#' . $ballMatch . '-' . $ballSelected;
                            $user_balance = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                            $hexLogin = dechex(11111111);
                            $loginId = strlen($hexLogin) . $hexLogin;
                            $str_balance = str_replace('.', '', $user_balance . '');
                            $hexBalance = dechex($str_balance - 0);
                            $rtnBalance = strlen($hexBalance) . $hexBalance;
                            $slotSettings->SetGameData('finish_answer', '104010' . $rtnBalance . strlen($winall_h) . $winall_h . '1537d0616e360' . $bet_h . '1021121a21b21c22422522622e22f' . $ballsStr);
                            $slotSettings->SaveLogReport($response, $gBet, 1, $winall, 'bet');
                            break;
                        case 'A/u272':
                            $betsArr = $slotSettings->Bet;
                            $gBet = $slotSettings->GetGameData('bingo_bet');
                            $allbet = $gBet;
                            $bet_h = dechex($slotSettings->GetGameData('bingo_bet_h'));
                            $bet_h = strlen($bet_h) . $bet_h;
                            $winallOld = $slotSettings->GetGameData('bingo_winall');
                            if( $winallOld > 0 ) 
                            {
                                $slotSettings->SetBalance(-1 * $winallOld);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $winallOld);
                            }
                            $user_balance = $slotSettings->GetGameData('bingo_balance');
                            $hexLogin = dechex(11111111);
                            $loginId = strlen($hexLogin) . $hexLogin;
                            $str_balance = str_replace('.', '', $user_balance . '');
                            $hexBalance = dechex($str_balance - 0);
                            $rtnBalance = strlen($hexBalance) . $hexBalance;
                            $rtnReels = '';
                            $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                            for( $gloop = 1; $gloop <= 2000; $gloop++ ) 
                            {
                                $winall = 0;
                                $ballMatch = 0;
                                $ballSelected = $slotSettings->GetGameData('bingo_selected');
                                $ballsArr = $slotSettings->GetGameData('bingo_balls');
                                $ballsArr0 = $slotSettings->GetGameData('bingo_balls0');
                                $userBalls = $slotSettings->GetGameData('bingo_uballs');
                                $ballsStr = '';
                                shuffle($ballsArr0);
                                for( $i = 0; $i <= 21; $i++ ) 
                                {
                                    if( $i >= 20 ) 
                                    {
                                        $ballsArr[$i] = $ballsArr0[$i - 20];
                                    }
                                    for( $j = 0; $j < $ballSelected; $j++ ) 
                                    {
                                        if( $ballsArr[$i] == $userBalls[$j] ) 
                                        {
                                            $ballMatch++;
                                        }
                                    }
                                    $hexBall = dechex($ballsArr[$i]);
                                    $ballsStr .= (dechex(strlen($hexBall)) . $hexBall);
                                }
                                $winall = $slotSettings->Paytable[$ballMatch][$ballSelected] * $allbet;
                                if( $winall <= $bank ) 
                                {
                                    break;
                                }
                            }
                            if( $winall > 0 ) 
                            {
                                $slotSettings->SetBalance($winall);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $winall * -1);
                            }
                            $slotSettings->SetGameData('bingo_winall', $winall);
                            $str_winall = str_replace('.', '', $winall * $floatBet . '');
                            $str_winall = $str_winall - 0;
                            $winall_h = dechex($str_winall);
                            $loginId = '10';
                            $user_balance = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                            if( $winall > 0 ) 
                            {
                                $hexLogin = dechex(11111111);
                                $loginId = strlen($hexLogin) . $hexLogin;
                                $str_balance = str_replace('.', '', $user_balance . '');
                                $hexBalance = dechex($str_balance - 0);
                                $rtnBalance = strlen($hexBalance) . $hexBalance;
                            }
                            $response = '102010' . $rtnBalance . strlen($winall_h) . $winall_h . $minBets . $maxBets . '616e360' . $bet_h . '1021121a21b21c22422522622e22f' . $ballsStr . '#' . $ballMatch . '-' . $ballSelected;
                            $user_balance = sprintf('%01.2f', $slotSettings->GetBalance()) * $floatBet;
                            $hexLogin = dechex(11111111);
                            $loginId = strlen($hexLogin) . $hexLogin;
                            $str_balance = str_replace('.', '', $user_balance . '');
                            $hexBalance = dechex($str_balance - 0);
                            $rtnBalance = strlen($hexBalance) . $hexBalance;
                            $slotSettings->SetGameData('finish_answer', '104010' . $rtnBalance . strlen($winall_h) . $winall_h . '1537d0616e360' . $bet_h . '1021121a21b21c22422522622e22f' . $ballsStr);
                            $slotSettings->SaveLogReport($response, $winallOld, 1, $winall, ' BG');
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
