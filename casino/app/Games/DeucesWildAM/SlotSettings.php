<?php 
namespace VanguardLTE\Games\DeucesWildAM
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
        public $lastEvent = null;
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
            if( $game->stat_in > 0 ) 
            {
                $this->currentRTP = $game->stat_out / $game->stat_in * 100;
            }
            else
            {
                $this->currentRTP = 0;
            }
            $this->increaseRTP = 1;
            $this->CurrentDenom = $this->game->denomination;
            $this->slotExitUrl = '/';
            $this->Bet = explode(',', $game->bet);
            if( $this->Bet[0] < 0.01 ) 
            {
                foreach( $this->Bet as &$bt ) 
                {
                    $bt = $bt * 10;
                }
            }
            $this->Bet = array_slice($this->Bet, 0, 5);
            $this->Balance = $user->balance;
            $this->jpgs = \VanguardLTE\JPG::where('shop_id', $this->shop_id)->lockForUpdate()->get();
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
            $i = 0;
            while( $i < count($this->jpgs) ) 
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
                    if( $this->jpgs[$i]->user_id && $this->jpgs[$i]->user_id != $this->user->id ) 
                    {
                    }
                    else
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
                    $i++;
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
        public function GetCombination($cards, $suits, $tipMode)
        {
            $cardsS = $cards;
            $suitsS = $suits;
            $wild = 2;
            sort($cardsS, SORT_NUMERIC);
            sort($suitsS, SORT_NUMERIC);
            $Paytable = [
                0, 
                1, 
                2, 
                2, 
                3, 
                5, 
                9, 
                15, 
                25, 
                200, 
                250
            ];
            $cardsAmount = [
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
                0, 
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
            for( $i = 2; $i <= 14; $i++ ) 
            {
                for( $j = 0; $j < 5; $j++ ) 
                {
                    if( $cardsS[$j] == $i ) 
                    {
                        $cardsAmount[$i]++;
                    }
                }
            }
            $combinations = [];
            $combinations[0] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[1] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[2] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[3] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[4] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[5] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[6] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[7] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[8] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[9] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            $combinations[10] = [
                'amount' => 0, 
                'cards' => [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ]
            ];
            for( $i = 3; $i <= 14; $i++ ) 
            {
                if( $cardsAmount[$i] + $cardsAmount[$wild] == 3 ) 
                {
                    $combinations[1]['amount'] += 1;
                    $combinations[1]['cards'][0] = $i;
                    $combinations[1]['cards'][1] = 2;
                }
                else if( $cardsAmount[$i] + $cardsAmount[$wild] == 4 ) 
                {
                    $combinations[5]['amount'] = 1;
                    $combinations[5]['cards'][0] = $i;
                    $combinations[5]['cards'][1] = 2;
                }
                else
                {
                    $straightCount = 0;
                    for( $s_c = 0; $s_c < 5; $s_c++ ) 
                    {
                        if( $cardsAmount[$i + $s_c] == 1 ) 
                        {
                            $straightCount += 1;
                        }
                    }
                    if( $cardsAmount[$wild] + $straightCount == 5 ) 
                    {
                        $combinations[2]['amount'] = 1;
                        $combinations[2]['cards'] = $cards;
                    }
                }
            }
            for( $i = 3; $i <= 14; $i++ ) 
            {
                if( $combinations[1]['amount'] == 1 && $cardsAmount[$i] - $cardsAmount[$wild] == 2 ) 
                {
                    $combinations[4]['amount'] = 1;
                    $combinations[4]['cards'][0] = $combinations[1]['cards'][0];
                    $combinations[4]['cards'][1] = $combinations[1]['cards'][1];
                    $combinations[4]['cards'][2] = $i;
                    $combinations[4]['cards'][3] = $i;
                    $combinations[4]['cards'][4] = $i;
                    break;
                }
            }
            if( $combinations[1]['amount'] == 2 ) 
            {
                $combinations[4]['amount'] = 1;
                $combinations[4]['cards'] = $cards;
            }
            $cSuits = [
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
            for( $ss = 0; $ss < 5; $ss++ ) 
            {
                $cSuits[$suits[$ss]]++;
            }
            for( $cl = 0; $cl <= 3; $cl++ ) 
            {
                if( $cSuits[$cl] + $cardsAmount[2] >= 5 ) 
                {
                    $combinations[3]['amount'] = 1;
                    $combinations[3]['cards'] = $cards;
                }
            }
            if( $combinations[2]['amount'] == 1 && $combinations[3]['amount'] == 1 ) 
            {
                $combinations[6]['amount'] = 1;
                $combinations[6]['cards'] = $combinations[2]['cards'];
            }
            if( $combinations[6]['amount'] == 1 && $combinations[6]['cards'][4] == 14 ) 
            {
                $combinations[10]['amount'] = 1;
                $combinations[10]['cards'] = $combinations[5]['cards'];
            }
            $pay = 0;
            $holds = [
                0, 
                0, 
                0, 
                0, 
                0
            ];
            $rang = 0;
            for( $i = count($combinations) - 1; $i >= 1; $i-- ) 
            {
                if( $combinations[$i]['amount'] >= 1 ) 
                {
                    $pay = $Paytable[$i];
                    $rang = $i;
                    break;
                }
            }
            $hcards = $combinations[$rang]['cards'];
            for( $i = 0; $i < 5; $i++ ) 
            {
                if( in_array($cards[$i], $hcards) ) 
                {
                    $holds[$i] = $cards[$i];
                }
            }
            if( $tipMode && $pay == 0 ) 
            {
                $holds = [
                    0, 
                    0, 
                    0, 
                    0, 
                    0
                ];
                $hcCount = 0;
                $scCount = 0;
                $is2Pair = 0;
                for( $i = 3; $i <= 14; $i++ ) 
                {
                    if( $cardsAmount[$i] + $cardsAmount[$wild] == 2 ) 
                    {
                        $combinations[1]['amount'] = 1;
                        $combinations[1]['cards'][0] = $i;
                        $combinations[1]['cards'][1] = 2;
                    }
                    else
                    {
                        $straightCount = 0;
                        $scCount = 0;
                        $tCrds = [
                            0, 
                            0, 
                            0, 
                            0, 
                            0
                        ];
                        for( $s_c = 0; $s_c < 5; $s_c++ ) 
                        {
                            if( $cardsAmount[$i + $s_c] == 1 ) 
                            {
                                $straightCount += 1;
                                $tCrds[$scCount] = $i + $s_c;
                                $scCount++;
                            }
                        }
                        if( $cardsAmount[$wild] + $straightCount == 4 ) 
                        {
                            $combinations[2]['amount'] = 1;
                            $combinations[2]['cards'] = $tCrds;
                            $combinations[2]['cards'][$scCount] = 2;
                        }
                    }
                }
                $fcArr = [
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
                $csuit = -1;
                for( $fc = 0; $fc < 5; $fc++ ) 
                {
                    $fcArr[$suits[$fc]]++;
                }
                if( $fcArr[0] + $cardsAmount[$wild] >= 4 ) 
                {
                    $csuit = 0;
                }
                if( $fcArr[1] + $cardsAmount[$wild] >= 4 ) 
                {
                    $csuit = 1;
                }
                if( $fcArr[2] + $cardsAmount[$wild] >= 4 ) 
                {
                    $csuit = 2;
                }
                if( $fcArr[3] + $cardsAmount[$wild] >= 4 ) 
                {
                    $csuit = 3;
                }
                if( $csuit >= 0 ) 
                {
                    $combinations[3]['amount'] = 1;
                    $ss_ = 0;
                    for( $ss = 0; $ss < 5; $ss++ ) 
                    {
                        if( $csuit == $suits[$ss] || $cards[$ss] == 2 ) 
                        {
                            $combinations[3]['cards'][$ss_] = $cards[$ss];
                            $ss_++;
                        }
                    }
                }
                $rangTip = 0;
                for( $i = count($combinations) - 1; $i >= 1; $i-- ) 
                {
                    if( $combinations[$i]['amount'] == 1 ) 
                    {
                        $rangTip = $i;
                        break;
                    }
                }
                $hcards = $combinations[$rangTip]['cards'];
                $trCards = [
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
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
                for( $i = 0; $i < 5; $i++ ) 
                {
                    if( $rangTip == 3 ) 
                    {
                        if( $trCards[$cards[$i]] == 0 && in_array($cards[$i], $hcards) || $cards[$i] == $wild ) 
                        {
                            $trCards[$cards[$i]]++;
                            $holds[$i] = $cards[$i];
                        }
                    }
                    else if( in_array($cards[$i], $hcards) ) 
                    {
                        $holds[$i] = $cards[$i];
                    }
                }
            }
            return [
                $pay, 
                $rang, 
                $holds
            ];
        }
        public function HexFormat($num)
        {
            $str = strlen(dechex($num)) . dechex($num);
            return $str;
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
                $prc_b = 0;
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
            else if( $slotState == 'double' ) 
            {
                $reportName = $this->slotId . ' DG';
            }
            $game = $this->game;
            if( $slotState == 'bet' ) 
            {
                $this->user->update_level('bet', $bet * $lines * $this->CurrentDenom);
            }
            if( $slotState != 'freespin' ) 
            {
                $game->increment('stat_in', $bet * $lines * $this->CurrentDenom);
            }
            $game->increment('stat_out', $win * $this->CurrentDenom);
            $game->tournament_stat($slotState, $this->user->id, $bet * $lines * $this->CurrentDenom, $win * $this->CurrentDenom);
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
                'bet' => $bet * $lines * $this->CurrentDenom, 
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
        public function GetGambleSettings()
        {
            $spinWin = rand(1, $this->WinGamble);
            return $spinWin;
        }
    }

}
