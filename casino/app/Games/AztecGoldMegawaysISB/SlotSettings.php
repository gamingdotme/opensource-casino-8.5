<?php 
namespace VanguardLTE\Games\AztecGoldMegawaysISB
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
        private $jpgs = null;
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
            $game = \VanguardLTE\Game::where([
                'name' => $this->slotId, 
                'shop_id' => $this->shop_id
            ])->lockForUpdate()->first();
            $this->shop = \VanguardLTE\Shop::find($this->shop_id);
            $this->game = $game;
            $this->MaxWin = $this->shop->max_win;
            $this->increaseRTP = 1;
            $this->Denominations = \VanguardLTE\Game::$values['denomination'];
            $this->CurrentDenom = $this->game->denomination;
            $this->scaleMode = 0;
            $this->numFloat = 0;
            $this->Paytable['SYM_1'] = [
                0, 
                0, 
                0, 
                2, 
                4, 
                8, 
                16
            ];
            $this->Paytable['SYM_2'] = [
                0, 
                0, 
                0, 
                3, 
                5, 
                10, 
                18
            ];
            $this->Paytable['SYM_3'] = [
                0, 
                0, 
                0, 
                3, 
                5, 
                10, 
                20
            ];
            $this->Paytable['SYM_4'] = [
                0, 
                0, 
                0, 
                3, 
                5, 
                12, 
                20
            ];
            $this->Paytable['SYM_5'] = [
                0, 
                0, 
                0, 
                4, 
                8, 
                16, 
                35
            ];
            $this->Paytable['SYM_6'] = [
                0, 
                0, 
                0, 
                4, 
                8, 
                16, 
                35
            ];
            $this->Paytable['SYM_7'] = [
                0, 
                0, 
                0, 
                5, 
                10, 
                20, 
                50
            ];
            $this->Paytable['SYM_8'] = [
                0, 
                0, 
                0, 
                5, 
                10, 
                20, 
                50
            ];
            $this->Paytable['SYM_9'] = [
                0, 
                0, 
                0, 
                10, 
                30, 
                80, 
                200
            ];
            $this->Paytable['SYM_10'] = [
                0, 
                0, 
                10, 
                50, 
                150, 
                400, 
                1000
            ];
            $this->Paytable['SYM_11'] = [
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0
            ];
            $this->Paytable['SYM_12'] = [
                0, 
                0, 
                0, 
                0, 
                0, 
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
            foreach( [
                'reelStripBonus1', 
                'reelStripBonus2', 
                'reelStripBonus3', 
                'reelStripBonus4', 
                'reelStripBonus5', 
                'reelStripBonus6'
            ] as $reelStrip ) 
            {
                if( count($reel->reelsStripBonus[$reelStrip]) ) 
                {
                    $this->$reelStrip = $reel->reelsStripBonus[$reelStrip];
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
            $this->slotBonus = true;
            $this->slotGamble = true;
            $this->slotFastStop = 1;
            $this->slotExitUrl = '/';
            $this->slotWildMpl = 1;
            $this->GambleType = 1;
            $this->slotFreeCount = 8;
            $this->slotFreeMpl = 1;
            $this->slotViewState = ($game->slotViewState == '' ? 'Normal' : $game->slotViewState);
            $this->hideButtons = [];
            $this->jpgs = \VanguardLTE\JPG::where('shop_id', $this->shop_id)->lockForUpdate()->get();
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
                10
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
                12
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
        public function HexFormat($num)
        {
            $str = strlen(dechex($num)) . dechex($num);
            return $str;
        }
        public function FormatReelStrips($pf)
        {
            $resultReelStrips = '';
            foreach( [
                'reelStrip' . $pf . '1', 
                'reelStrip' . $pf . '2', 
                'reelStrip' . $pf . '3', 
                'reelStrip' . $pf . '4', 
                'reelStrip' . $pf . '5', 
                'reelStrip' . $pf . '6'
            ] as $reelStrip ) 
            {
                if( $this->$reelStrip != '' ) 
                {
                    $data = $this->$reelStrip;
                    foreach( $data as &$item ) 
                    {
                        $item = str_replace('"', '', $item);
                        $item = trim($item);
                        $item = dechex($item);
                    }
                    $rLength = dechex(count($data));
                    $resultReelStrips .= (strlen($rLength) . $rLength . implode('', $data));
                }
            }
            return $resultReelStrips;
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
                $tmpLog = json_decode($log->str);
                if( $tmpLog->responseEvent != 'gambleResult' ) 
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
        public function UpdateJackpots($bet)
        {
            $bet = $bet * $this->CurrentDenom;
            $count_balance = $this->count_balance;
            $jsum = [];
            $payJack = 0;
            for( $i = 0; $i < count($this->jpgs); $i++ ) 
            {
                if( $count_balance == 0 || $this->jpgPercentZero ) 
                {
                    $jsum[$i] = $this->jpgs[$i]->balance;
                }
                else if( $count_balance < $bet ) 
                {
                    $jsum[$i] = $count_balance / 100 * $this->jpgs[$i]->percent + $this->jpgs[$i]->balance;
                }
                else
                {
                    $jsum[$i] = $bet / 100 * $this->jpgs[$i]->percent + $this->jpgs[$i]->balance;
                }
                if( $this->jpgs[$i]->get_pay_sum() < $jsum[$i] && $this->jpgs[$i]->get_pay_sum() > 0 ) 
                {
                    $payJack = $this->jpgs[$i]->get_pay_sum() / $this->CurrentDenom;
                    $jsum[$i] = $jsum[$i] - $this->jpgs[$i]->get_pay_sum();
                    $this->SetBalance($this->jpgs[$i]->get_pay_sum() / $this->CurrentDenom);
                    if( $this->jpgs[$i]->get_pay_sum() > 0 ) 
                    {
                        \VanguardLTE\StatGame::create([
                            'user_id' => $this->playerId, 
                            'balance' => $this->Balance * $this->CurrentDenom, 
                            'bet' => 0, 
                            'win' => $this->jpgs[$i]->get_pay_sum(), 
                            'game' => $this->game->name . ' JPG ' . $this->jpgs[$i]->id, 
                            'in_game' => 0, 
                            'in_jpg' => 0, 
                            'in_profit' => 0, 
                            'shop_id' => $this->shop_id, 
                            'date_time' => \Carbon\Carbon::now()
                        ]);
                    }
                }
                $this->jpgs[$i]->balance = $jsum[$i];
                $this->jpgs[$i]->save();
                if( $this->jpgs[$i]->balance < $this->jpgs[$i]->get_min('start_balance') ) 
                {
                    $summ = $this->jpgs[$i]->get_start_balance();
                    if( $summ > 0 ) 
                    {
                        $this->jpgs[$i]->add_jpg('add', $summ);
                    }
                }
            }
            if( $payJack > 0 ) 
            {
                $payJack = sprintf('%01.2f', $payJack);
                $this->Jackpots['jackPay'] = $payJack;
            }
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
            $strLog .= ('{"responseEvent":"error","responseType":"' . $errcode . '","serverResponse":"InternalError"}');
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
            $strLog .= ('{"responseEvent":"error","responseType":"' . $errcode . '","serverResponse":"InternalError"}');
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
                    $currentSpinWinChance = 10;
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
        public function OffsetReels($reels)
        {
            $newReels = [];
            $newReels['reel1'] = [];
            $newReels['reel2'] = [];
            $newReels['reel3'] = [];
            $newReels['reel4'] = [];
            $newReels['reel5'] = [];
            $newReels['reel6'] = [];
            for( $r = 1; $r <= 6; $r++ ) 
            {
                for( $p = 6; $p >= 0; $p-- ) 
                {
                    if( $reels['reel' . $r][$p] != -2 ) 
                    {
                        array_unshift($newReels['reel' . $r], $reels['reel' . $r][$p]);
                    }
                }
            }
            for( $r = 1; $r <= 6; $r++ ) 
            {
                for( $p = count($newReels['reel' . $r]) + 1; $p <= 7; $p++ ) 
                {
                    array_unshift($newReels['reel' . $r], rand(1, 10));
                }
            }
            return $newReels;
        }
        public function GetReelsWin($reels, $lines, $betline, $betlineRaw, $linesId, $cWins, $spinCnt)
        {
            $wild = '11';
            $scatter = '12';
            $totalWin = 0;
            $lineWins = [];
            $bonusMpl = 1;
            $reelsOffset = $reels;
            $tmpStringWin = '';
            $k = 0;
            for( $j = 0; $j < count($this->SymbolGame); $j++ ) 
            {
                $csym = (string)$this->SymbolGame[$j];
                if( $csym == $scatter || !isset($this->Paytable['SYM_' . $csym]) ) 
                {
                }
                else
                {
                    $symCounts = [
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
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
                    $lastReel = 0;
                    for( $r = 1; $r <= 6; $r++ ) 
                    {
                        $isEmpty = true;
                        for( $p = 0; $p <= 6; $p++ ) 
                        {
                            if( $reels['reel' . $r][$p] == $csym || $reels['reel' . $r][$p] == $wild ) 
                            {
                                $isEmpty = false;
                                $symCounts[$r]++;
                                $lastReel = $r;
                            }
                        }
                        if( $isEmpty ) 
                        {
                            break;
                        }
                    }
                    $hl = [
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
                        0, 
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
                    $hlc = 0;
                    for( $p = 0; $p <= 6; $p++ ) 
                    {
                        for( $r = 1; $r <= 6; $r++ ) 
                        {
                            if( ($reels['reel' . $r][$p] == $csym || $reels['reel' . $r][$p] == $wild) && $r <= $lastReel ) 
                            {
                                $hl[$hlc] = 1;
                            }
                            $hlc++;
                        }
                    }
                    if( $symCounts[1] > 0 && $symCounts[2] > 0 ) 
                    {
                        $symWeight = $symCounts[1] * $symCounts[2];
                        $tmpWin = $this->Paytable['SYM_' . $csym][2] * $betline * $symWeight * $bonusMpl;
                        if( $cWins[$k] < $tmpWin ) 
                        {
                            $cWins[$k] = $tmpWin;
                            $tmpStringWin = '{ "ways": "' . $symWeight . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                        }
                    }
                    if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 ) 
                    {
                        $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3];
                        $tmpWin = $this->Paytable['SYM_' . $csym][3] * $betline * $symWeight * $bonusMpl;
                        if( $cWins[$k] < $tmpWin ) 
                        {
                            $cWins[$k] = $tmpWin;
                            $tmpStringWin = '{ "ways": "' . $symWeight . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                        }
                    }
                    if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 && $symCounts[4] > 0 ) 
                    {
                        $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3] * $symCounts[4];
                        $tmpWin = $this->Paytable['SYM_' . $csym][4] * $betline * $symWeight * $bonusMpl;
                        if( $cWins[$k] < $tmpWin ) 
                        {
                            $cWins[$k] = $tmpWin;
                            $tmpStringWin = '{ "ways": "' . $symWeight . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                        }
                    }
                    if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 && $symCounts[4] > 0 && $symCounts[5] > 0 ) 
                    {
                        $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3] * $symCounts[4] * $symCounts[5];
                        $tmpWin = $this->Paytable['SYM_' . $csym][5] * $betline * $symWeight * $bonusMpl;
                        if( $cWins[$k] < $tmpWin ) 
                        {
                            $cWins[$k] = $tmpWin;
                            $tmpStringWin = '{ "ways": "' . $symWeight . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                        }
                    }
                    if( $symCounts[1] > 0 && $symCounts[2] > 0 && $symCounts[3] > 0 && $symCounts[4] > 0 && $symCounts[5] > 0 && $symCounts[6] > 0 ) 
                    {
                        $symWeight = $symCounts[1] * $symCounts[2] * $symCounts[3] * $symCounts[4] * $symCounts[5] * $symCounts[6];
                        $tmpWin = $this->Paytable['SYM_' . $csym][6] * $betline * $symWeight * $bonusMpl;
                        if( $cWins[$k] < $tmpWin ) 
                        {
                            $cWins[$k] = $tmpWin;
                            $tmpStringWin = '{ "ways": "' . $symWeight . '", "initialValue": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "initialValueMoney": "' . ($cWins[$k] * 100) . '", "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "multiplier": "1", "symbolId": "' . $csym . '", "pos": "' . $csym . '", "hl": "' . implode('', $hl) . '", "dir": "0" }';
                        }
                    }
                    if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                    {
                        for( $r = 1; $r <= 6; $r++ ) 
                        {
                            for( $p = 0; $p <= 6; $p++ ) 
                            {
                                if( ($reels['reel' . $r][$p] == $csym || $reels['reel' . $r][$p] == $wild) && $r <= $lastReel ) 
                                {
                                    $reelsOffset['reel' . $r][$p] = -2;
                                }
                            }
                        }
                        array_push($lineWins, $tmpStringWin);
                        $totalWin += $cWins[$k];
                    }
                    $k++;
                }
            }
            return [
                'lineWins' => $lineWins, 
                'reelsOffset' => $reelsOffset, 
                'cWins' => $cWins, 
                'totalWin' => $totalWin
            ];
        }
        public function GetRandomScatterPos($rp)
        {
            $rpResult = [];
            for( $i = 0; $i < count($rp); $i++ ) 
            {
                if( $rp[$i] == '12' ) 
                {
                    if( $i == 0 ) 
                    {
                        array_push($rpResult, $i);
                        array_push($rpResult, $i + 1);
                    }
                    else if( $i == (count($rp) - 1) ) 
                    {
                        array_push($rpResult, $i - 1);
                    }
                    else
                    {
                        array_push($rpResult, $i);
                        array_push($rpResult, $i + 1);
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
            if( $winType != 'bonus' ) 
            {
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
                        $prs[$index + 1] = mt_rand(0, count($this->$reelStrip) - 6);
                    }
                }
            }
            else
            {
                $reelsId = [];
                $scattersCnt = 5;
                $reelsId = [
                    1, 
                    2, 
                    3, 
                    4, 
                    5, 
                    6
                ];
                for( $i = 0; $i < count($reelsId); $i++ ) 
                {
                    $prs[$reelsId[$i]] = $this->GetRandomScatterPos($this->{'reelStrip' . $reelsId[$i]});
                }
            }
            $reel = [
                'rp' => [], 
                'ws' => []
            ];
            $ways = 1;
            foreach( $prs as $index => $value ) 
            {
                $key = $this->{'reelStrip' . $index};
                $cnt = count($key);
                $key[-1] = $key[$cnt - 1];
                $key[$cnt] = $key[0];
                $rpwc = rand(3, 7);
                $ways = $ways * $rpwc;
                for( $pw = 0; $pw < 7; $pw++ ) 
                {
                    if( $pw < $rpwc ) 
                    {
                        $reel['reel' . $index][$pw] = $key[$value + ($pw - 1)];
                    }
                    else
                    {
                        $reel['reel' . $index][$pw] = -1;
                    }
                }
                $reel['rp'][] = $value;
                $reel['ways'] = $ways;
            }
            return $reel;
        }
    }

}
