<?php 
namespace VanguardLTE\Games\Lucky7BS
{
    class SlotSettings
    {
        public $playerId = null;
        public $splitScreen = null;
        public $reelStrip1 = null;
        public $reelStrip2 = null;
        public $reelStrip3 = null;
        public $reelStrip4 = null;
        public $reelStrip5 = null;
        public $reelStrip6 = null;
        public $reelStripBonus1 = null;
        public $reelStripBonus2 = null;
        public $reelStripBonus3 = null;
        public $reelStripBonus4 = null;
        public $reelStripBonus5 = null;
        public $reelStripBonus6 = null;
        public $slotId = '';
        public $slotDBId = '';
        public $Line = null;
        public $scaleMode = null;
        public $numFloat = null;
        public $gameLine = null;
        public $Bet = null;
        public $isBonusStart = null;
        public $Balance = null;
        public $SymbolGame = null;
        public $GambleType = null;
        public $lastEvent = null;
        public $Jackpots = [];
        public $keyController = null;
        public $slotViewState = null;
        public $hideButtons = null;
        public $slotReelsConfig = null;
        public $slotFreeCount = null;
        public $slotFreeMpl = null;
        public $slotWildMpl = null;
        public $slotExitUrl = null;
        public $slotBonus = null;
        public $slotBonusType = null;
        public $slotScatterType = null;
        public $slotGamble = null;
        public $Paytable = [];
        public $slotSounds = [];
        public $jpgs = null;
        private $Bank = null;
        private $Percent = null;
        private $WinLine = null;
        private $WinGamble = null;
        private $Bonus = null;
        private $shop_id = null;
        public $currency = null;
        public $user = null;
        public $game = null;
        public $shop = null;
        public $jpgPercentZero = false;
        public $count_balance = null;
        public function __construct($sid, $playerId)
        {
            $this->slotId = $sid;
            $this->playerId = $playerId;
            $user = \VanguardLTE\User::lockForUpdate()->find($this->playerId);
            $this->user = $user;
            $this->shop_id = $user->shop_id;
            $gamebank = \VanguardLTE\GameBank::where(['shop_id' => $this->shop_id])->lockForUpdate()->get();
            $this->username = $user->username;
            $game = \VanguardLTE\Game::where([
                'name' => $this->slotId, 
                'shop_id' => $this->shop_id
            ])->lockForUpdate()->first();
            $this->shop = \VanguardLTE\Shop::find($this->shop_id);
            $this->game = $game;
            $this->MaxWin = $this->shop->max_win;
            $this->increaseRTP = 1;
            $this->CurrentDenomGame = $this->game->denomination;
            $this->Denominations = \VanguardLTE\Game::$values['denomination'];
            $this->CurrentDenom = 1;
            $this->scaleMode = 0;
            $this->numFloat = 0;
            $this->Paytable['SYM_0'] = [
                0, 
                0, 
                0, 
                150, 
                0, 
                0
            ];
            $this->Paytable['SYM_1'] = [
                0, 
                0, 
                0, 
                50, 
                0, 
                0
            ];
            $this->Paytable['SYM_2'] = [
                0, 
                0, 
                0, 
                1000, 
                0, 
                0
            ];
            $this->Paytable['SYM_3'] = [
                0, 
                0, 
                0, 
                100, 
                0, 
                0
            ];
            $this->Paytable['SYM_4'] = [
                0, 
                0, 
                0, 
                250, 
                0, 
                0
            ];
            $this->Paytable['SYM_5'] = [
                0, 
                0, 
                0, 
                20, 
                0, 
                0
            ];
            $reel = new GameReel();
            foreach( [
                'reelStrip1', 
                'reelStrip2', 
                'reelStrip3', 
                'reelStrip4', 
                'reelStrip5', 
                'reelStrip6'
            ] as $reelStrip ) 
            {
                if( count($reel->reelsStrip[$reelStrip]) ) 
                {
                    $this->$reelStrip = $reel->reelsStrip[$reelStrip];
                }
            }
            $this->keyController = [
                '13' => 'uiButtonSpin,uiButtonSkip', 
                '49' => 'uiButtonInfo', 
                '50' => 'uiButtonCollect', 
                '51' => 'uiButtonExit2', 
                '52' => 'uiButtonLinesMinus', 
                '53' => 'uiButtonLinesPlus', 
                '54' => 'uiButtonBetMinus', 
                '55' => 'uiButtonBetPlus', 
                '56' => 'uiButtonGamble', 
                '57' => 'uiButtonRed', 
                '48' => 'uiButtonBlack', 
                '189' => 'uiButtonAuto', 
                '187' => 'uiButtonSpin'
            ];
            $this->slotReelsConfig = [
                [
                    425, 
                    142, 
                    3
                ], 
                [
                    669, 
                    142, 
                    3
                ], 
                [
                    913, 
                    142, 
                    3
                ], 
                [
                    1157, 
                    142, 
                    3
                ], 
                [
                    1401, 
                    142, 
                    3
                ]
            ];
            $this->slotBonusType = 1;
            $this->slotScatterType = 0;
            $this->splitScreen = false;
            $this->slotBonus = false;
            $this->slotGamble = true;
            $this->slotFastStop = 1;
            $this->slotExitUrl = '/';
            $this->slotWildMpl = 1;
            $this->GambleType = 1;
            $this->slotFreeCount = [
                0, 
                0, 
                0, 
                8, 
                12, 
                20
            ];
            $this->slotFreeMpl = 2;
            $this->slotViewState = ($game->slotViewState == '' ? 'Normal' : $game->slotViewState);
            $this->hideButtons = [];
            $this->jpgs = \VanguardLTE\JPG::where('shop_id', $this->shop_id)->lockForUpdate()->get();
            $this->slotJackPercent = [];
            $this->slotJackpot = [];
            for( $jp = 0; $jp < 4; $jp++ ) 
            {
                if( $this->jpgs[$jp]->balance != '' ) 
                {
                    $this->slotJackpot[$jp] = sprintf('%01.4f', $this->jpgs[$jp]->balance);
                    $this->slotJackpot[$jp] = substr($this->slotJackpot[$jp], 0, strlen($this->slotJackpot[$jp]) - 2);
                    $this->slotJackPercent[] = $this->jpgs[$jp]->percent;
                }
            }
            $this->Line = [
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
                11, 
                12, 
                13, 
                14, 
                15
            ];
            $this->gameLine = [
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
                11, 
                12, 
                13, 
                14, 
                15
            ];
            $this->Bet = explode(',', $game->bet);
            $this->Balance = $user->balance;
            $this->SymbolGame = [
                '0', 
                '1', 
                '2', 
                '3', 
                '4', 
                '5', 
                '6', 
                '7', 
                '8', 
                '9', 
                '10'
            ];
            $this->Bank = $game->get_gamebank();
            $this->Percent = $this->shop->percent;
            $this->WinGamble = $game->rezerv;
            $this->slotDBId = $game->id;
            $this->slotCurrency = $user->shop->currency;
            $this->count_balance = $user->count_balance;
            if( $user->address > 0 && $user->count_balance == 0 ) 
            {
                $this->Percent = 0;
                $this->jpgPercentZero = true;
            }
            else if( $user->count_balance == 0 ) 
            {
                $this->Percent = 100;
            }
            if( !isset($this->user->session) || strlen($this->user->session) <= 0 ) 
            {
                $this->user->session = serialize([]);
            }
            $this->gameData = unserialize($this->user->session);
            if( count($this->gameData) > 0 ) 
            {
                foreach( $this->gameData as $key => $vl ) 
                {
                    if( $vl['timelife'] <= time() ) 
                    {
                        unset($this->gameData[$key]);
                    }
                }
            }
            if( !isset($this->game->advanced) || strlen($this->game->advanced) <= 0 ) 
            {
                $this->game->advanced = serialize([]);
            }
            $this->gameDataStatic = unserialize($this->game->advanced);
            if( count($this->gameDataStatic) > 0 ) 
            {
                foreach( $this->gameDataStatic as $key => $vl ) 
                {
                    if( $vl['timelife'] <= time() ) 
                    {
                        unset($this->gameDataStatic[$key]);
                    }
                }
            }
        }
        public function is_active()
        {
            if( $this->game && $this->shop && $this->user && (!$this->game->view || $this->shop->is_blocked || $this->user->is_blocked || $this->user->status == \VanguardLTE\Support\Enum\UserStatus::BANNED) ) 
            {
                \VanguardLTE\Session::where('user_id', $this->user->id)->delete();
                $this->user->update(['remember_token' => null]);
                return false;
            }
            if( !$this->game->view ) 
            {
                return false;
            }
            if( $this->shop->is_blocked ) 
            {
                return false;
            }
            if( $this->user->is_blocked ) 
            {
                return false;
            }
            if( $this->user->status == \VanguardLTE\Support\Enum\UserStatus::BANNED ) 
            {
                return false;
            }
            return true;
        }
        public function SetGameData($key, $value)
        {
            $timeLife = 86400;
            $this->gameData[$key] = [
                'timelife' => time() + $timeLife, 
                'payload' => $value
            ];
        }
        public function GetGameData($key)
        {
            if( isset($this->gameData[$key]) ) 
            {
                return $this->gameData[$key]['payload'];
            }
            else
            {
                return 0;
            }
        }
        public function FormatFloat($num)
        {
            $str0 = explode('.', $num);
            if( isset($str0[1]) ) 
            {
                if( strlen($str0[1]) > 4 ) 
                {
                    return round($num * 100) / 100;
                }
                else if( strlen($str0[1]) > 2 ) 
                {
                    return floor($num * 100) / 100;
                }
                else
                {
                    return $num;
                }
            }
            else
            {
                return $num;
            }
        }
        public function SaveGameData()
        {
            $this->user->session = serialize($this->gameData);
            $this->user->save();
        }
        public function CheckBonusWin()
        {
            $allRateCnt = 0;
            $allRate = 0;
            foreach( $this->Paytable as $vl ) 
            {
                foreach( $vl as $vl2 ) 
                {
                    if( $vl2 > 0 ) 
                    {
                        $allRateCnt++;
                        $allRate += $vl2;
                        break;
                    }
                }
            }
            return $allRate / $allRateCnt;
        }
        public function GetRandomPay()
        {
            $allRate = [];
            foreach( $this->Paytable as $vl ) 
            {
                foreach( $vl as $vl2 ) 
                {
                    if( $vl2 > 0 ) 
                    {
                        $allRate[] = $vl2;
                    }
                }
            }
            shuffle($allRate);
            if( $this->game->stat_in < ($this->game->stat_out + ($allRate[0] * $this->AllBet)) ) 
            {
                $allRate[0] = 0;
            }
            return $allRate[0];
        }
        public function HasGameDataStatic($key)
        {
            if( isset($this->gameDataStatic[$key]) ) 
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        public function SaveGameDataStatic()
        {
            $this->game->advanced = serialize($this->gameDataStatic);
            $this->game->save();
            $this->game->refresh();
        }
        public function SetGameDataStatic($key, $value)
        {
            $timeLife = 86400;
            $this->gameDataStatic[$key] = [
                'timelife' => time() + $timeLife, 
                'payload' => $value
            ];
        }
        public function GetGameDataStatic($key)
        {
            if( isset($this->gameDataStatic[$key]) ) 
            {
                return $this->gameDataStatic[$key]['payload'];
            }
            else
            {
                return 0;
            }
        }
        public function HasGameData($key)
        {
            if( isset($this->gameData[$key]) ) 
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        public function GetHistory()
        {
            $history = \VanguardLTE\GameLog::whereRaw('game_id=? and user_id=? ORDER BY id DESC LIMIT 10', [
                $this->slotDBId, 
                $this->playerId
            ])->get();
            $this->lastEvent = 'NULL';
            foreach( $history as $log ) 
            {
                if( $log->str == 'NONE' ) 
                {
                    return 'NULL';
                }
                $tmpLog = json_decode($log->str);
                if( $tmpLog->responseEvent != 'gambleResult' && $tmpLog->responseEvent != 'jackpot' ) 
                {
                    $this->lastEvent = $log->str;
                    break;
                }
            }
            if( isset($tmpLog) ) 
            {
                return $tmpLog;
            }
            else
            {
                return 'NULL';
            }
        }
        public function ClearJackpot($jid)
        {
            $this->jpgs[$jid]->balance = sprintf('%01.4f', 0);
            $this->jpgs[$jid]->save();
        }
        public function UpdateJackpots($bet)
        {
            $bet = $bet * $this->CurrentDenom;
            $count_balance = $this->count_balance;
            $slotJackSum = [];
            $game = $this->game;
            $isJackPay = 0;
            $isJackId = 0;
            for( $jp = 1; $jp <= 4; $jp++ ) 
            {
                if( $this->jpgs[$jp - 1]->balance != '' ) 
                {
                    $this->slotJackpot[$jp - 1] = number_format($this->jpgs[$jp - 1]->balance, 4, '.', '');
                    if( $count_balance == 0 || $this->jpgPercentZero ) 
                    {
                        $slotJackSum[$jp - 1] = $this->slotJackpot[$jp - 1];
                    }
                    else if( $count_balance < $bet ) 
                    {
                        $slotJackSum[$jp - 1] = number_format($count_balance / 100 * $this->slotJackPercent[$jp - 1] + $this->slotJackpot[$jp - 1], 4, '.', '');
                    }
                    else
                    {
                        $slotJackSum[$jp - 1] = number_format($bet / 100 * $this->slotJackPercent[$jp - 1] + $this->slotJackpot[$jp - 1], 4, '.', '');
                    }
                    $leftOver = $slotJackSum[$jp - 1];
                    if( $this->jpgs[$jp - 1]->get_pay_sum() <= $this->slotJackpot[$jp - 1] && !$isJackPay && $this->jpgs[$jp - 1]->get_pay_sum() > 0 ) 
                    {
                        if( $this->jpgs[$jp - 1]->user_id && $this->jpgs[$jp - 1]->user_id != $this->user->id ) 
                        {
                        }
                        else
                        {
                            $isJackPay = 1;
                            $isJackId = $jp - 1;
                        }
                    }
                    $this->slotJackpot[$jp - 1] = sprintf('%01.2f', $slotJackSum[$jp - 1]);
                    $this->jpgs[$jp - 1]->balance = sprintf('%01.4f', $leftOver);
                    $this->jpgs[$jp - 1]->save();
                }
            }
            $game->save();
            for( $jp = 0; $jp < 4; $jp++ ) 
            {
                if( $this->jpgs[$jp]->balance != '' && $this->jpgs[$jp]->balance < $this->jpgs[$jp]->get_min('start_balance') ) 
                {
                    $summ = $this->jpgs[$jp]->get_start_balance() - $this->jpgs[$jp]->balance;
                    if( $summ > 0 ) 
                    {
                        $this->jpgs[$jp]->add_jpg('add', $summ);
                    }
                }
            }
            return [
                'isJackPay' => $isJackPay, 
                'isJackId' => $isJackId
            ];
        }
        public function GetBank($slotState = '')
        {
            if( $this->isBonusStart || $slotState == 'bonus' || $slotState == 'freespin' || $slotState == 'respin' ) 
            {
                $slotState = 'bonus';
            }
            else
            {
                $slotState = '';
            }
            $game = $this->game;
            $this->Bank = $game->get_gamebank($slotState);
            return $this->Bank / $this->CurrentDenom;
        }
        public function GetPercent()
        {
            return $this->Percent;
        }
        public function GetCountBalanceUser()
        {
            return $this->user->count_balance;
        }
        public function InternalErrorSilent($errcode)
        {
            $strLog = '';
            $strLog .= "\n";
            $strLog .= ('{"responseEvent":"error","responseType":"' . $errcode . '","serverResponse":"InternalError","request":' . json_encode($_REQUEST) . ',"requestRaw":' . file_get_contents('php://input') . '}');
            $strLog .= "\n";
            $strLog .= ' ############################################### ';
            $strLog .= "\n";
            $slg = '';
            if( file_exists(storage_path('logs/') . $this->slotId . 'Internal.log') ) 
            {
                $slg = file_get_contents(storage_path('logs/') . $this->slotId . 'Internal.log');
            }
            file_put_contents(storage_path('logs/') . $this->slotId . 'Internal.log', $slg . $strLog);
        }
        public function InternalError($errcode)
        {
            $strLog = '';
            $strLog .= "\n";
            $strLog .= ('{"responseEvent":"error","responseType":"' . $errcode . '","serverResponse":"InternalError","request":' . json_encode($_REQUEST) . ',"requestRaw":' . file_get_contents('php://input') . '}');
            $strLog .= "\n";
            $strLog .= ' ############################################### ';
            $strLog .= "\n";
            $slg = '';
            if( file_exists(storage_path('logs/') . $this->slotId . 'Internal.log') ) 
            {
                $slg = file_get_contents(storage_path('logs/') . $this->slotId . 'Internal.log');
            }
            file_put_contents(storage_path('logs/') . $this->slotId . 'Internal.log', $slg . $strLog);
            exit( '' );
        }
        public function SetBank($slotState = '', $sum, $slotEvent = '')
        {
            if( $this->isBonusStart || $slotState == 'bonus' || $slotState == 'freespin' || $slotState == 'respin' ) 
            {
                $slotState = 'bonus';
            }
            else
            {
                $slotState = '';
            }
            if( $this->GetBank($slotState) + $sum < 0 ) 
            {
                $this->InternalError('Bank_   ' . $sum . '  CurrentBank_ ' . $this->GetBank($slotState) . ' CurrentState_ ' . $slotState . ' Trigger_ ' . ($this->GetBank($slotState) + $sum));
            }
            $sum = $sum * $this->CurrentDenom;
            $game = $this->game;
            $bankBonusSum = 0;
            if( $sum > 0 && $slotEvent == 'bet' ) 
            {
                $this->toGameBanks = 0;
                $this->toSlotJackBanks = 0;
                $this->toSysJackBanks = 0;
                $this->betProfit = 0;
                $prc = $this->GetPercent();
                $prc_b = 10;
                if( $prc <= $prc_b ) 
                {
                    $prc_b = 0;
                }
                $count_balance = $this->count_balance;
                $gameBet = $sum / $this->GetPercent() * 100;
                if( $count_balance < $gameBet && $count_balance > 0 ) 
                {
                    $firstBid = $count_balance;
                    $secondBid = $gameBet - $firstBid;
                    if( isset($this->betRemains0) ) 
                    {
                        $secondBid = $this->betRemains0;
                    }
                    $bankSum = $firstBid / 100 * $this->GetPercent();
                    $sum = $bankSum + $secondBid;
                    $bankBonusSum = $firstBid / 100 * $prc_b;
                }
                else if( $count_balance > 0 ) 
                {
                    $bankBonusSum = $gameBet / 100 * $prc_b;
                }
                for( $i = 0; $i < count($this->jpgs); $i++ ) 
                {
                    if( !$this->jpgPercentZero ) 
                    {
                        if( $count_balance < $gameBet && $count_balance > 0 ) 
                        {
                            $this->toSlotJackBanks += ($count_balance / 100 * $this->jpgs[$i]->percent);
                        }
                        else if( $count_balance > 0 ) 
                        {
                            $this->toSlotJackBanks += ($gameBet / 100 * $this->jpgs[$i]->percent);
                        }
                    }
                }
                $this->toGameBanks = $sum;
                $this->betProfit = $gameBet - $this->toGameBanks - $this->toSlotJackBanks - $this->toSysJackBanks;
            }
            if( $sum > 0 ) 
            {
                $this->toGameBanks = $sum;
            }
            if( $bankBonusSum > 0 ) 
            {
                $sum -= $bankBonusSum;
                $game->set_gamebank($bankBonusSum, 'inc', 'bonus');
            }
            if( $sum == 0 && $slotEvent == 'bet' && isset($this->betRemains) ) 
            {
                $sum = $this->betRemains;
            }
            $game->set_gamebank($sum, 'inc', $slotState);
            $game->save();
            return $game;
        }
        public function SetBalance($sum, $slotEvent = '')
        {
            if( $this->GetBalance() + $sum < 0 ) 
            {
                $this->InternalError('Balance_   ' . $sum);
            }
            $sum = $sum * $this->CurrentDenom;
            if( $sum < 0 && $slotEvent == 'bet' ) 
            {
                $user = $this->user;
                if( $user->count_balance == 0 ) 
                {
                    $remains = [];
                    $this->betRemains = 0;
                    $sm = abs($sum);
                    if( $user->address < $sm && $user->address > 0 ) 
                    {
                        $remains[] = $sm - $user->address;
                    }
                    for( $i = 0; $i < count($remains); $i++ ) 
                    {
                        if( $this->betRemains < $remains[$i] ) 
                        {
                            $this->betRemains = $remains[$i];
                        }
                    }
                }
                if( $user->count_balance > 0 && $user->count_balance < abs($sum) ) 
                {
                    $remains0 = [];
                    $sm = abs($sum);
                    $tmpSum = $sm - $user->count_balance;
                    $this->betRemains0 = $tmpSum;
                    if( $user->address > 0 ) 
                    {
                        $this->betRemains0 = 0;
                        if( $user->address < $tmpSum && $user->address > 0 ) 
                        {
                            $remains0[] = $tmpSum - $user->address;
                        }
                        for( $i = 0; $i < count($remains0); $i++ ) 
                        {
                            if( $this->betRemains0 < $remains0[$i] ) 
                            {
                                $this->betRemains0 = $remains0[$i];
                            }
                        }
                    }
                }
                $sum0 = abs($sum);
                if( $user->count_balance == 0 ) 
                {
                    $sm = abs($sum);
                    if( $user->address < $sm && $user->address > 0 ) 
                    {
                        $user->address = 0;
                    }
                    else if( $user->address > 0 ) 
                    {
                        $user->address -= $sm;
                    }
                }
                else if( $user->count_balance > 0 && $user->count_balance < $sum0 ) 
                {
                    $sm = $sum0 - $user->count_balance;
                    if( $user->address < $sm && $user->address > 0 ) 
                    {
                        $user->address = 0;
                    }
                    else if( $user->address > 0 ) 
                    {
                        $user->address -= $sm;
                    }
                }
                $this->user->count_balance = $this->user->updateCountBalance($sum, $this->count_balance);
                $this->user->count_balance = $this->FormatFloat($this->user->count_balance);
            }
            $this->user->increment('balance', $sum);
            $this->user->balance = $this->FormatFloat($this->user->balance);
            $this->user->save();
            return $this->user;
        }
        public function GetBalance()
        {
            $user = $this->user;
            $this->Balance = $user->balance / $this->CurrentDenom;
            return $this->Balance;
        }
        public function SaveLogReport($spinSymbols, $bet, $lines, $win, $slotState)
        {
            $reportName = $this->slotId . ' ' . $slotState;
            if( $slotState == 'freespin' ) 
            {
                $reportName = $this->slotId . ' FG';
            }
            else if( $slotState == 'bet' ) 
            {
                $reportName = $this->slotId . '';
            }
            else if( $slotState == 'slotGamble' ) 
            {
                $reportName = $this->slotId . ' DG';
            }
            $game = $this->game;
            if( $slotState == 'bet' ) 
            {
                $this->user->update_level('bet', $bet * $this->CurrentDenom);
            }
            if( $slotState != 'freespin' ) 
            {
                $game->increment('stat_in', $bet * $this->CurrentDenom);
            }
            $game->increment('stat_out', $win * $this->CurrentDenom);
            $game->tournament_stat($slotState, $this->user->id, $bet * $this->CurrentDenom, $win * $this->CurrentDenom);
            $this->user->update(['last_bid' => \Carbon\Carbon::now()]);
            if( !isset($this->betProfit) ) 
            {
                $this->betProfit = 0;
                $this->toGameBanks = 0;
                $this->toSlotJackBanks = 0;
                $this->toSysJackBanks = 0;
            }
            if( !isset($this->toGameBanks) ) 
            {
                $this->toGameBanks = 0;
            }
            $this->game->increment('bids');
            $this->game->refresh();
            $gamebank = \VanguardLTE\GameBank::where(['shop_id' => $game->shop_id])->first();
            if( $gamebank ) 
            {
                list($slotsBank, $bonusBank, $fishBank, $tableBank, $littleBank) = \VanguardLTE\Lib\Banker::get_all_banks($game->shop_id);
            }
            else
            {
                $slotsBank = $game->get_gamebank('', 'slots');
                $bonusBank = $game->get_gamebank('bonus', 'bonus');
                $fishBank = $game->get_gamebank('', 'fish');
                $tableBank = $game->get_gamebank('', 'table_bank');
                $littleBank = $game->get_gamebank('', 'little');
            }
            $totalBank = $slotsBank + $bonusBank + $fishBank + $tableBank + $littleBank;
            \VanguardLTE\GameLog::create([
                'game_id' => $this->slotDBId, 
                'user_id' => $this->playerId, 
                'ip' => $_SERVER['REMOTE_ADDR'], 
                'str' => $spinSymbols, 
                'shop_id' => $this->shop_id
            ]);
            \VanguardLTE\StatGame::create([
                'user_id' => $this->playerId, 
                'balance' => $this->Balance * $this->CurrentDenom, 
                'bet' => $bet * $this->CurrentDenom, 
                'win' => $win * $this->CurrentDenom, 
                'game' => $reportName, 
                'in_game' => $this->toGameBanks, 
                'in_jpg' => $this->toSlotJackBanks, 
                'in_profit' => $this->betProfit, 
                'denomination' => $this->CurrentDenom, 
                'shop_id' => $this->shop_id, 
                'slots_bank' => (double)$slotsBank, 
                'bonus_bank' => (double)$bonusBank, 
                'fish_bank' => (double)$fishBank, 
                'table_bank' => (double)$tableBank, 
                'little_bank' => (double)$littleBank, 
                'total_bank' => (double)$totalBank, 
                'date_time' => \Carbon\Carbon::now()
            ]);
        }
        public function GetSpinSettings($garantType = 'bet', $bet, $lines)
        {
            $curField = 10;
            switch( $lines ) 
            {
                case 10:
                    $curField = 10;
                    break;
                case 9:
                case 8:
                    $curField = 9;
                    break;
                case 7:
                case 6:
                    $curField = 7;
                    break;
                case 5:
                case 4:
                    $curField = 5;
                    break;
                case 3:
                case 2:
                    $curField = 3;
                    break;
                case 1:
                    $curField = 1;
                    break;
                default:
                    $curField = 10;
                    break;
            }
            if( $garantType != 'bet' ) 
            {
                $pref = '_bonus';
            }
            else
            {
                $pref = '';
            }
            $this->AllBet = $bet * $lines;
            $linesPercentConfigSpin = $this->game->get_lines_percent_config('spin');
            $linesPercentConfigBonus = $this->game->get_lines_percent_config('bonus');
            $currentPercent = $this->shop->percent;
            $currentSpinWinChance = 0;
            $currentBonusWinChance = 0;
            $percentLevel = '';
            foreach( $linesPercentConfigSpin['line' . $curField . $pref] as $k => $v ) 
            {
                $l = explode('_', $k);
                $l0 = $l[0];
                $l1 = $l[1];
                if( $l0 <= $currentPercent && $currentPercent <= $l1 ) 
                {
                    $percentLevel = $k;
                    break;
                }
            }
            $currentSpinWinChance = $linesPercentConfigSpin['line' . $curField . $pref][$percentLevel];
            $currentBonusWinChance = $linesPercentConfigBonus['line' . $curField . $pref][$percentLevel];
            $RtpControlCount = 200;
            if( !$this->HasGameDataStatic('SpinWinLimit') ) 
            {
                $this->SetGameDataStatic('SpinWinLimit', 0);
            }
            if( !$this->HasGameDataStatic('RtpControlCount') ) 
            {
                $this->SetGameDataStatic('RtpControlCount', $RtpControlCount);
            }
            if( $this->game->stat_in > 0 ) 
            {
                $rtpRange = $this->game->stat_out / $this->game->stat_in * 100;
            }
            else
            {
                $rtpRange = 0;
            }
            if( $this->GetGameDataStatic('RtpControlCount') == 0 ) 
            {
                if( $currentPercent + rand(1, 2) < $rtpRange && $this->GetGameDataStatic('SpinWinLimit') <= 0 ) 
                {
                    $this->SetGameDataStatic('SpinWinLimit', rand(25, 50));
                }
                if( $pref == '' && $this->GetGameDataStatic('SpinWinLimit') > 0 ) 
                {
                    $currentBonusWinChance = 5000;
                    $currentSpinWinChance = 20;
                    $this->MaxWin = rand(1, 5);
                    if( $rtpRange < ($currentPercent - 1) ) 
                    {
                        $this->SetGameDataStatic('SpinWinLimit', 0);
                        $this->SetGameDataStatic('RtpControlCount', $this->GetGameDataStatic('RtpControlCount') - 1);
                    }
                }
            }
            else if( $this->GetGameDataStatic('RtpControlCount') < 0 ) 
            {
                if( $currentPercent + rand(1, 2) < $rtpRange && $this->GetGameDataStatic('SpinWinLimit') <= 0 ) 
                {
                    $this->SetGameDataStatic('SpinWinLimit', rand(25, 50));
                }
                $this->SetGameDataStatic('RtpControlCount', $this->GetGameDataStatic('RtpControlCount') - 1);
                if( $pref == '' && $this->GetGameDataStatic('SpinWinLimit') > 0 ) 
                {
                    $currentBonusWinChance = 5000;
                    $currentSpinWinChance = 20;
                    $this->MaxWin = rand(1, 5);
                    if( $rtpRange < ($currentPercent - 1) ) 
                    {
                        $this->SetGameDataStatic('SpinWinLimit', 0);
                    }
                }
                if( $this->GetGameDataStatic('RtpControlCount') < (-1 * $RtpControlCount) && $currentPercent - 1 <= $rtpRange && $rtpRange <= ($currentPercent + 2) ) 
                {
                    $this->SetGameDataStatic('RtpControlCount', $RtpControlCount);
                }
            }
            else
            {
                $this->SetGameDataStatic('RtpControlCount', $this->GetGameDataStatic('RtpControlCount') - 1);
            }
            $bonusWin = rand(1, $currentBonusWinChance);
            $spinWin = rand(1, $currentSpinWinChance);
            $return = [
                'none', 
                0
            ];
            if( $bonusWin == 1 && $this->slotBonus ) 
            {
                $this->isBonusStart = true;
                $garantType = 'bonus';
                $winLimit = $this->GetBank($garantType);
                $return = [
                    'bonus', 
                    $winLimit
                ];
                if( $this->game->stat_in < ($this->CheckBonusWin() * $bet + $this->game->stat_out) || $winLimit < ($this->CheckBonusWin() * $bet) ) 
                {
                    $return = [
                        'none', 
                        0
                    ];
                }
            }
            else if( $spinWin == 1 ) 
            {
                $winLimit = $this->GetBank($garantType);
                $return = [
                    'win', 
                    $winLimit
                ];
            }
            if( $garantType == 'bet' && $this->GetBalance() <= (2 / $this->CurrentDenom) ) 
            {
                $randomPush = rand(1, 10);
                if( $randomPush == 1 ) 
                {
                    $winLimit = $this->GetBank('');
                    $return = [
                        'win', 
                        $winLimit
                    ];
                }
            }
            return $return;
        }
        public function GetRandomScatterPos($rp)
        {
            $rpResult = [];
            for( $i = 0; $i < count($rp); $i++ ) 
            {
                if( $rp[$i] == '14' ) 
                {
                    if( isset($rp[$i + 1]) && isset($rp[$i - 1]) ) 
                    {
                        array_push($rpResult, $i);
                    }
                    if( isset($rp[$i + 1]) && isset($rp[$i + 2]) ) 
                    {
                        array_push($rpResult, $i + 1);
                    }
                    if( isset($rp[$i - 1]) && isset($rp[$i - 2]) ) 
                    {
                        array_push($rpResult, $i - 1);
                    }
                }
            }
            shuffle($rpResult);
            if( !isset($rpResult[0]) ) 
            {
                $rpResult[0] = rand(2, count($rp) - 3);
            }
            return $rpResult[0];
        }
        public function GetGambleSettings()
        {
            $spinWin = rand(1, $this->WinGamble);
            return $spinWin;
        }
        public function GetReelStrips($winType, $slotEvent)
        {
            $game = $this->game;
            $prs = [];
            foreach( [
                'reelStrip1', 
                'reelStrip2', 
                'reelStrip3', 
                'reelStrip4', 
                'reelStrip5', 
                'reelStrip6'
            ] as $index => $reelStrip ) 
            {
                if( is_array($this->$reelStrip) && count($this->$reelStrip) > 0 ) 
                {
                    $prs[$index + 1] = mt_rand(0, count($this->$reelStrip) - 1);
                }
            }
            $reel = [
                'rp' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            foreach( $prs as $index => $value ) 
            {
                $key = $this->{'reelStrip' . $index};
                $cnt = count($key);
                $key[-1] = $key[$cnt - 1];
                $key[$cnt] = $key[0];
                $reel['reel' . $index][0] = '';
                if( $key[$value] == -1 ) 
                {
                    $reel['rp'][$index] = rand(0, 8) . '.5';
                }
                else
                {
                    $reel['rp'][$index] = $key[$value] . '.0';
                }
                $reel['reel' . $index][1] = $key[$value];
                $reel['reel' . $index][2] = '';
                $reel['reel' . $index][3] = '';
            }
            return $reel;
        }
    }

}
