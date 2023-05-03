<?php 
namespace VanguardLTE\Games\RouletteClassicPTM
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
        public $licenseDK = null;
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
            $this->increaseRTP = rand(0, 1);
            $this->CurrentDenom = $this->game->denomination;
            $this->scaleMode = 0;
            $this->numFloat = 0;
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
            $this->slotWildMpl = 2;
            $this->GambleType = 1;
            $this->slotFreeCount = 15;
            $this->slotFreeMpl = 3;
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
                10, 
                11, 
                12, 
                13, 
                14, 
                15
            ];
            $this->gameLine = [];
            $this->Bet = explode(',', 'minBet=0.1,maxBet=10,minBetTable=0.1,maxBetTable=10,minBetStraight=0.1,maxBetStraight=10,minBetFiftyFifty=0.1,maxBetFiftyFifty=10,minBetColumnAndDozen=0.1,maxBetColumnAndDozen=10');
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
                11
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
        public function GetNumbersByField($fieldName)
        {
            $betPlaces = "[\n                {\"id\":   1, \"type\": \"straight\", \"mask_color\": \"#cc3333\", \"pockets\":[0]},\n                {\"id\":  31, \"type\": \"straight\", \"mask_color\": \"#3333ff\", \"pockets\":[1]},\n                {\"id\":  77, \"type\": \"straight\", \"mask_color\": \"#3399ff\", \"pockets\":[2]},\n                {\"id\": 123, \"type\": \"straight\", \"mask_color\": \"#33ffff\", \"pockets\":[3]},\n                {\"id\":  33, \"type\": \"straight\", \"mask_color\": \"#9933ff\", \"pockets\":[4]},\n                {\"id\":  79, \"type\": \"straight\", \"mask_color\": \"#9999ff\", \"pockets\":[5]},\n                {\"id\": 125, \"type\": \"straight\", \"mask_color\": \"#99ffff\", \"pockets\":[6]},\n                {\"id\":  35, \"type\": \"straight\", \"mask_color\": \"#0033cc\", \"pockets\":[7]},\n                {\"id\":  81, \"type\": \"straight\", \"mask_color\": \"#0099cc\", \"pockets\":[8]},\n                {\"id\": 127, \"type\": \"straight\", \"mask_color\": \"#00ffcc\", \"pockets\":[9]},\n                {\"id\":  37, \"type\": \"straight\", \"mask_color\": \"#6633cc\", \"pockets\":[10]},\n                {\"id\":  83, \"type\": \"straight\", \"mask_color\": \"#6699cc\", \"pockets\":[11]},\n                {\"id\": 129, \"type\": \"straight\", \"mask_color\": \"#66ffcc\", \"pockets\":[12]},\n                {\"id\":  39, \"type\": \"straight\", \"mask_color\": \"#cc33cc\", \"pockets\":[13]},\n                {\"id\":  85, \"type\": \"straight\", \"mask_color\": \"#cc99cc\", \"pockets\":[14]},\n                {\"id\": 131, \"type\": \"straight\", \"mask_color\": \"#ccffcc\", \"pockets\":[15]},\n                {\"id\":  41, \"type\": \"straight\", \"mask_color\": \"#003399\", \"pockets\":[16]},\n                {\"id\":  87, \"type\": \"straight\", \"mask_color\": \"#009999\", \"pockets\":[17]},\n                {\"id\": 133, \"type\": \"straight\", \"mask_color\": \"#00ff99\", \"pockets\":[18]},\n                {\"id\":  43, \"type\": \"straight\", \"mask_color\": \"#663399\", \"pockets\":[19]},\n                {\"id\":  89, \"type\": \"straight\", \"mask_color\": \"#669999\", \"pockets\":[20]},\n                {\"id\": 135, \"type\": \"straight\", \"mask_color\": \"#66ff99\", \"pockets\":[21]},\n                {\"id\":  45, \"type\": \"straight\", \"mask_color\": \"#cc3399\", \"pockets\":[22]},\n                {\"id\":  91, \"type\": \"straight\", \"mask_color\": \"#cc9999\", \"pockets\":[23]},\n                {\"id\": 137, \"type\": \"straight\", \"mask_color\": \"#ccff99\", \"pockets\":[24]},\n                {\"id\":  47, \"type\": \"straight\", \"mask_color\": \"#003366\", \"pockets\":[25]},\n                {\"id\":  93, \"type\": \"straight\", \"mask_color\": \"#009966\", \"pockets\":[26]},\n                {\"id\": 139, \"type\": \"straight\", \"mask_color\": \"#00ff66\", \"pockets\":[27]},\n                {\"id\":  49, \"type\": \"straight\", \"mask_color\": \"#663366\", \"pockets\":[28]},\n                {\"id\":  95, \"type\": \"straight\", \"mask_color\": \"#669966\", \"pockets\":[29]},\n                {\"id\": 141, \"type\": \"straight\", \"mask_color\": \"#66ff66\", \"pockets\":[30]},\n                {\"id\":  51, \"type\": \"straight\", \"mask_color\": \"#cc3366\", \"pockets\":[31]},\n                {\"id\":  97, \"type\": \"straight\", \"mask_color\": \"#cc9966\", \"pockets\":[32]},\n                {\"id\": 143, \"type\": \"straight\", \"mask_color\": \"#ccff66\", \"pockets\":[33]},\n                {\"id\":  53, \"type\": \"straight\", \"mask_color\": \"#003333\", \"pockets\":[34]},\n                {\"id\":  99, \"type\": \"straight\", \"mask_color\": \"#009933\", \"pockets\":[35]},\n                {\"id\": 145, \"type\": \"straight\", \"mask_color\": \"#00ff33\", \"pockets\":[36]},\n\n                {\"id\":   3, \"type\": \"split\", \"mask_color\": \"#0033ff\", \"pockets\": [0, 1]},\n                {\"id\":   5, \"type\": \"split\", \"mask_color\": \"#0099ff\", \"pockets\": [0, 2]},\n                {\"id\":   7, \"type\": \"split\", \"mask_color\": \"#00ffff\", \"pockets\": [0, 3]},\n                {\"id\":  54, \"type\": \"split\", \"mask_color\": \"#3366ff\", \"pockets\": [1, 2]},\n                {\"id\":  32, \"type\": \"split\", \"mask_color\": \"#6633ff\", \"pockets\": [1, 4]},\n                {\"id\":  78, \"type\": \"split\", \"mask_color\": \"#6699ff\", \"pockets\": [2, 5]},\n                {\"id\": 100, \"type\": \"split\", \"mask_color\": \"#33ccff\", \"pockets\": [2, 3]},\n                {\"id\": 124, \"type\": \"split\", \"mask_color\": \"#66ffff\", \"pockets\": [3, 6]},\n                {\"id\":  34, \"type\": \"split\", \"mask_color\": \"#cc33ff\", \"pockets\": [4, 7]},\n                {\"id\":  56, \"type\": \"split\", \"mask_color\": \"#9966ff\", \"pockets\": [4, 5]},\n                {\"id\":  80, \"type\": \"split\", \"mask_color\": \"#cc99ff\", \"pockets\": [5, 8]},\n                {\"id\": 102, \"type\": \"split\", \"mask_color\": \"#99ccff\", \"pockets\": [5, 6]},\n                {\"id\": 126, \"type\": \"split\", \"mask_color\": \"#ccffff\", \"pockets\": [6, 9]},\n                {\"id\":  36, \"type\": \"split\", \"mask_color\": \"#3333cc\", \"pockets\": [7, 10]},\n                {\"id\":  58, \"type\": \"split\", \"mask_color\": \"#0066cc\", \"pockets\": [7, 8]},\n                {\"id\":  82, \"type\": \"split\", \"mask_color\": \"#3399cc\", \"pockets\": [8, 11]},\n                {\"id\": 104, \"type\": \"split\", \"mask_color\": \"#00cccc\", \"pockets\": [8, 9]},\n                {\"id\": 128, \"type\": \"split\", \"mask_color\": \"#33ffcc\", \"pockets\": [9, 12]},\n                {\"id\":  38, \"type\": \"split\", \"mask_color\": \"#9933cc\", \"pockets\": [10, 13]},\n                {\"id\":  60, \"type\": \"split\", \"mask_color\": \"#6666cc\", \"pockets\": [10, 11]},\n                {\"id\":  84, \"type\": \"split\", \"mask_color\": \"#9999cc\", \"pockets\": [11, 14]},\n                {\"id\": 106, \"type\": \"split\", \"mask_color\": \"#66cccc\", \"pockets\": [11, 12]},\n                {\"id\": 130, \"type\": \"split\", \"mask_color\": \"#99ffcc\", \"pockets\": [12, 15]},\n                {\"id\":  40, \"type\": \"split\", \"mask_color\": \"#ff33cc\", \"pockets\": [13, 16]},\n                {\"id\":  62, \"type\": \"split\", \"mask_color\": \"#cc66cc\", \"pockets\": [13, 14]},\n                {\"id\":  86, \"type\": \"split\", \"mask_color\": \"#ff99cc\", \"pockets\": [14, 17]},\n                {\"id\": 108, \"type\": \"split\", \"mask_color\": \"#cccccc\", \"pockets\": [14, 15]},\n                {\"id\": 132, \"type\": \"split\", \"mask_color\": \"#ffffcc\", \"pockets\": [15, 18]},\n                {\"id\":  42, \"type\": \"split\", \"mask_color\": \"#333399\", \"pockets\": [16, 19]},\n                {\"id\":  64, \"type\": \"split\", \"mask_color\": \"#006699\", \"pockets\": [16, 17]},\n                {\"id\":  88, \"type\": \"split\", \"mask_color\": \"#339999\", \"pockets\": [17, 20]},\n                {\"id\": 110, \"type\": \"split\", \"mask_color\": \"#00cc99\", \"pockets\": [17, 18]},\n                {\"id\": 134, \"type\": \"split\", \"mask_color\": \"#33ff99\", \"pockets\": [18, 21]},\n                {\"id\":  44, \"type\": \"split\", \"mask_color\": \"#993399\", \"pockets\": [19, 22]},\n                {\"id\":  66, \"type\": \"split\", \"mask_color\": \"#666699\", \"pockets\": [19, 20]},\n                {\"id\":  90, \"type\": \"split\", \"mask_color\": \"#999999\", \"pockets\": [20, 23]},\n                {\"id\": 112, \"type\": \"split\", \"mask_color\": \"#66cc99\", \"pockets\": [20, 21]},\n                {\"id\": 136, \"type\": \"split\", \"mask_color\": \"#99ff99\", \"pockets\": [21, 24]},\n                {\"id\":  46, \"type\": \"split\", \"mask_color\": \"#ff3399\", \"pockets\": [22, 25]},\n                {\"id\":  68, \"type\": \"split\", \"mask_color\": \"#cc6699\", \"pockets\": [22, 23]},\n                {\"id\":  92, \"type\": \"split\", \"mask_color\": \"#ff9999\", \"pockets\": [23, 26]},\n                {\"id\": 114, \"type\": \"split\", \"mask_color\": \"#cccc99\", \"pockets\": [23, 24]},\n                {\"id\": 138, \"type\": \"split\", \"mask_color\": \"#ffff99\", \"pockets\": [24, 27]},\n                {\"id\":  48, \"type\": \"split\", \"mask_color\": \"#333366\", \"pockets\": [25, 28]},\n                {\"id\":  70, \"type\": \"split\", \"mask_color\": \"#006666\", \"pockets\": [25, 26]},\n                {\"id\":  94, \"type\": \"split\", \"mask_color\": \"#339966\", \"pockets\": [26, 29]},\n                {\"id\": 116, \"type\": \"split\", \"mask_color\": \"#00cc66\", \"pockets\": [26, 27]},\n                {\"id\": 140, \"type\": \"split\", \"mask_color\": \"#33ff66\", \"pockets\": [27, 30]},\n                {\"id\":  50, \"type\": \"split\", \"mask_color\": \"#993366\", \"pockets\": [28, 31]},\n                {\"id\":  72, \"type\": \"split\", \"mask_color\": \"#666666\", \"pockets\": [28, 29]},\n                {\"id\":  96, \"type\": \"split\", \"mask_color\": \"#999966\", \"pockets\": [29, 32]},\n                {\"id\": 118, \"type\": \"split\", \"mask_color\": \"#66cc66\", \"pockets\": [29, 30]},\n                {\"id\": 142, \"type\": \"split\", \"mask_color\": \"#99ff66\", \"pockets\": [30, 33]},\n                {\"id\":  52, \"type\": \"split\", \"mask_color\": \"#ff3366\", \"pockets\": [31, 34]},\n                {\"id\":  74, \"type\": \"split\", \"mask_color\": \"#cc6666\", \"pockets\": [31, 32]},\n                {\"id\":  98, \"type\": \"split\", \"mask_color\": \"#ff9966\", \"pockets\": [32, 35]},\n                {\"id\": 120, \"type\": \"split\", \"mask_color\": \"#cccc66\", \"pockets\": [32, 33]},\n                {\"id\": 144, \"type\": \"split\", \"mask_color\": \"#ffff66\", \"pockets\": [33, 36]},\n                {\"id\":  76, \"type\": \"split\", \"mask_color\": \"#006633\", \"pockets\": [34, 35]},\n                {\"id\": 122, \"type\": \"split\", \"mask_color\": \"#00cc33\", \"pockets\": [35, 36]},\n\n                {\"id\":  8,  \"type\": \"street\", \"mask_color\": \"#3300ff\", \"pockets\": [1, 2, 3]},\n                {\"id\":  10, \"type\": \"street\", \"mask_color\": \"#9900ff\", \"pockets\": [4, 5, 6]},\n                {\"id\":  12, \"type\": \"street\", \"mask_color\": \"#0000cc\", \"pockets\": [7, 8, 9]},\n                {\"id\":  14, \"type\": \"street\", \"mask_color\": \"#6600cc\", \"pockets\": [10, 11, 12]},\n                {\"id\":  16, \"type\": \"street\", \"mask_color\": \"#cc00cc\", \"pockets\": [13, 14, 15]},\n                {\"id\":  18, \"type\": \"street\", \"mask_color\": \"#000099\", \"pockets\": [16, 17, 18]},\n                {\"id\":  20, \"type\": \"street\", \"mask_color\": \"#660099\", \"pockets\": [19, 20, 21]},\n                {\"id\":  22, \"type\": \"street\", \"mask_color\": \"#cc0099\", \"pockets\": [22, 23, 24]},\n                {\"id\":  24, \"type\": \"street\", \"mask_color\": \"#000066\", \"pockets\": [25, 26, 27]},\n                {\"id\":  26, \"type\": \"street\", \"mask_color\": \"#660066\", \"pockets\": [28, 29, 30]},\n                {\"id\":  28, \"type\": \"street\", \"mask_color\": \"#cc0066\", \"pockets\": [31, 32, 33]},\n                {\"id\":  30, \"type\": \"street\", \"mask_color\": \"#000033\", \"pockets\": [34, 35, 36]},\n                {\"id\":   4, \"type\": \"street\", \"mask_color\": \"#0066ff\", \"pockets\": [0, 1, 2]},\n                {\"id\":   6, \"type\": \"street\", \"mask_color\": \"#00ccff\", \"pockets\": [0, 2, 3]},\n\n                {\"id\":  55, \"type\": \"corner\", \"mask_color\": \"#6666ff\", \"pockets\": [2, 5, 4, 1]},\n                {\"id\": 101, \"type\": \"corner\", \"mask_color\": \"#66ccff\", \"pockets\": [3, 6, 5, 2]},\n                {\"id\":  57, \"type\": \"corner\", \"mask_color\": \"#cc66ff\", \"pockets\": [5, 8, 7, 4]},\n                {\"id\": 103, \"type\": \"corner\", \"mask_color\": \"#ccccff\", \"pockets\": [6, 9, 8, 5]},\n                {\"id\":  59, \"type\": \"corner\", \"mask_color\": \"#3366cc\", \"pockets\": [8, 11, 10, 7]},\n                {\"id\": 105, \"type\": \"corner\", \"mask_color\": \"#33cccc\", \"pockets\": [9, 12, 11, 8]},\n                {\"id\":  61, \"type\": \"corner\", \"mask_color\": \"#9966cc\", \"pockets\": [11, 14, 13, 10]},\n                {\"id\": 107, \"type\": \"corner\", \"mask_color\": \"#99cccc\", \"pockets\": [12, 15, 14, 11]},\n                {\"id\":  63, \"type\": \"corner\", \"mask_color\": \"#ff66cc\", \"pockets\": [14, 17, 16, 13]},\n                {\"id\": 109, \"type\": \"corner\", \"mask_color\": \"#ffcccc\", \"pockets\": [15, 18, 17, 14]},\n                {\"id\":  65, \"type\": \"corner\", \"mask_color\": \"#336699\", \"pockets\": [17, 20, 19, 16]},\n                {\"id\": 111, \"type\": \"corner\", \"mask_color\": \"#33cc99\", \"pockets\": [18, 21, 20, 17]},\n                {\"id\":  67, \"type\": \"corner\", \"mask_color\": \"#996699\", \"pockets\": [20, 23, 22, 19]},\n                {\"id\": 113, \"type\": \"corner\", \"mask_color\": \"#99cc99\", \"pockets\": [21, 24, 23, 20]},\n                {\"id\":  69, \"type\": \"corner\", \"mask_color\": \"#ff6699\", \"pockets\": [23, 26, 25, 22]},\n                {\"id\": 115, \"type\": \"corner\", \"mask_color\": \"#ffcc99\", \"pockets\": [24, 27, 26, 23]},\n                {\"id\":  71, \"type\": \"corner\", \"mask_color\": \"#336666\", \"pockets\": [26, 29, 28, 25]},\n                {\"id\": 117, \"type\": \"corner\", \"mask_color\": \"#33cc66\", \"pockets\": [27, 30, 29, 26]},\n                {\"id\":  73, \"type\": \"corner\", \"mask_color\": \"#996666\", \"pockets\": [29, 32, 31, 28]},\n                {\"id\": 119, \"type\": \"corner\", \"mask_color\": \"#99cc66\", \"pockets\": [30, 33, 32, 29]},\n                {\"id\":  75, \"type\": \"corner\", \"mask_color\": \"#ff6666\", \"pockets\": [32, 35, 34, 31]},\n                {\"id\": 121, \"type\": \"corner\", \"mask_color\": \"#ffcc66\", \"pockets\": [33, 36, 35, 32]},\n\n                {\"id\":   2, \"type\": \"four\", \"mask_color\": \"#0000ff\", \"pockets\": [0, 1, 2, 3]},\n\n                {\"id\":   9, \"type\": \"line\", \"mask_color\": \"#6600ff\", \"pockets\": [1, 2, 3, 4, 5, 6]},\n                {\"id\":  11, \"type\": \"line\", \"mask_color\": \"#cc00ff\", \"pockets\": [4, 5, 6, 7, 8, 9]},\n                {\"id\":  13, \"type\": \"line\", \"mask_color\": \"#3300cc\", \"pockets\": [7, 8, 9, 10, 11, 12]},\n                {\"id\":  15, \"type\": \"line\", \"mask_color\": \"#9900cc\", \"pockets\": [10, 11, 12, 13, 14, 15]},\n                {\"id\":  17, \"type\": \"line\", \"mask_color\": \"#ff00cc\", \"pockets\": [13, 14, 15, 16, 17, 18]},\n                {\"id\":  19, \"type\": \"line\", \"mask_color\": \"#330099\", \"pockets\": [16, 17, 18, 19, 20, 21]},\n                {\"id\":  21, \"type\": \"line\", \"mask_color\": \"#990099\", \"pockets\": [19, 20, 21, 22, 23, 24]},\n                {\"id\":  23, \"type\": \"line\", \"mask_color\": \"#ff0099\", \"pockets\": [22, 23, 24, 25, 26, 27]},\n                {\"id\":  25, \"type\": \"line\", \"mask_color\": \"#330066\", \"pockets\": [25, 26, 27, 28, 29, 30]},\n                {\"id\":  27, \"type\": \"line\", \"mask_color\": \"#990066\", \"pockets\": [28, 29, 30, 31, 32, 33]},\n                {\"id\":  29, \"type\": \"line\", \"mask_color\": \"#ff0066\", \"pockets\": [31, 32, 33, 34, 35, 36]},\n\n                {\"id\": 146, \"type\": \"column\",   \"mask_color\": \"#339933\", \"pockets\": [1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34]},\n                {\"id\": 147, \"type\": \"column\",   \"mask_color\": \"#33cc33\", \"pockets\": [2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35]},\n                {\"id\": 148, \"type\": \"column\",   \"mask_color\": \"#33ff33\", \"pockets\": [3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36]},\n                {\"id\": 149, \"type\": \"twelve\",   \"mask_color\": \"#336633\", \"pockets\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]},\n                {\"id\": 150, \"type\": \"twelve\",   \"mask_color\": \"#330033\", \"pockets\": [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]},\n                {\"id\": 151, \"type\": \"twelve\",   \"mask_color\": \"#666633\", \"pockets\": [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]},\n                {\"id\": 153, \"type\": \"low\",      \"mask_color\": \"#333333\", \"pockets\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18]},\n                {\"id\": 152, \"type\": \"high\",     \"mask_color\": \"#660033\", \"pockets\": [19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]},\n\n                {\"id\": 156, \"type\": \"even\", \"mask_color\": \"#66ff33\",\n                    \"pockets\": [2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36],\n                    \"stroke_ids\": [77, 33, 125, 81, 37, 129, 85, 41, 133, 89, 45, 137, 93, 49, 141, 97, 53, 145]\n                },\n\n                {\"id\": 157, \"type\": \"odd\", \"mask_color\": \"#663333\",\n                    \"pockets\": [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29, 31, 33, 35],\n                    \"stroke_ids\": [31, 123, 79, 35, 127, 83, 39, 131, 87,  43, 135, 91, 47, 139, 95, 51, 143, 99]\n                },\n\n                {\"id\": 155, \"type\": \"red\", \"mask_color\": \"#66cc33\",\n                    \"pockets\": [1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36],\n                    \"stroke_ids\": [31, 123, 79, 35, 128, 85, 134, 42, 91, 47, 140,  97, 53, 145]\n                },   \n\n                {\"id\": 154, \"type\": \"black\", \"mask_color\": \"#669933\",\n                    \"pockets\": [2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35],\n                    \"stroke_ids\": [77, 33, 125, 82, 38, 131, 88, 45, 137, 94, 50, 143, 99]\n                }\n            ]";
            $placesArr = json_decode($betPlaces);
            foreach( $placesArr as $vl ) 
            {
                if( $vl->id == $fieldName ) 
                {
                    return [
                        $vl->type, 
                        $vl->pockets
                    ];
                }
            }
        }
        public function GetHistory()
        {
            return 'NULL';
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
            else if( $slotState == 'slotGamble' ) 
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
        public function GetSpinSettings($bet, $lines)
        {
            $pref = '';
            $garantType = 'bet';
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
                if( $rp[$i] == '12' ) 
                {
                    if( isset($rp[$i + 1]) && isset($rp[$i - 1]) ) 
                    {
                        array_push($rpResult, $i);
                    }
                    if( isset($rp[$i - 1]) && isset($rp[$i - 2]) ) 
                    {
                        array_push($rpResult, $i - 1);
                    }
                    if( isset($rp[$i + 1]) && isset($rp[$i + 2]) ) 
                    {
                        array_push($rpResult, $i + 1);
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
            if( $slotEvent == 'freespin' ) 
            {
                $reel = new GameReel();
                $fArr = $reel->reelsStripBonus;
                foreach( [
                    'reelStrip1', 
                    'reelStrip2', 
                    'reelStrip3', 
                    'reelStrip4', 
                    'reelStrip5', 
                    'reelStrip6'
                ] as $reelStrip ) 
                {
                    $curReel = array_shift($fArr);
                    if( count($curReel) ) 
                    {
                        $this->$reelStrip = $curReel;
                    }
                }
            }
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
                        $prs[$index + 1] = mt_rand(0, count($this->$reelStrip) - 3);
                    }
                }
            }
            else
            {
                $reelsId = [];
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
                        $prs[$index + 1] = $this->GetRandomScatterPos($this->$reelStrip);
                        $reelsId[] = $index + 1;
                    }
                }
                $scattersCnt = rand(3, count($reelsId));
                shuffle($reelsId);
                for( $i = 0; $i < count($reelsId); $i++ ) 
                {
                    if( $i < $scattersCnt ) 
                    {
                        $prs[$reelsId[$i]] = $this->GetRandomScatterPos($this->{'reelStrip' . $reelsId[$i]});
                    }
                    else
                    {
                        $prs[$reelsId[$i]] = rand(0, count($this->{'reelStrip' . $reelsId[$i]}) - 3);
                    }
                }
            }
            $reel = [
                'rp' => []
            ];
            foreach( $prs as $index => $value ) 
            {
                $key = $this->{'reelStrip' . $index};
                $key[-1] = $key[count($key) - 1];
                $reel['reel' . $index][0] = $key[$value - 1];
                $reel['reel' . $index][1] = $key[$value];
                $reel['reel' . $index][2] = $key[$value + 1];
                $reel['reel' . $index][3] = '';
                $reel['rp'][] = $value;
            }
            return $reel;
        }
    }

}
