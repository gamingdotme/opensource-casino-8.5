<?php 
namespace VanguardLTE
{
    class StatGame extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'stat_game';
        protected $fillable = [
            'date_time', 
            'user_id', 
            'balance', 
            'bet', 
            'win', 
            'game', 
            'denomination', 
            'in_game', 
            'in_jpg', 
            'in_profit', 
            'game_bank', 
            'jack_balance', 
            'shop_id', 
            'slots_bank', 
            'bonus_bank', 
            'fish_bank', 
            'table_bank', 
            'little_bank', 
            'total_bank'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function getSlotsBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getBonusBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getFishBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getTableBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getLittleBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getTotalBankAttribute($value)
        {
            return number_format($value, 2, '.', ' ');
        }
        public function getBalanceAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function getBetAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function getWinAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function getInGameAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function getInJpgAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function getInProfitAttribute($value)
        {
            return number_format($value, 4, '.', ' ');
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'user_id');
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
        public function game_item()
        {
            return $this->hasOne('VanguardLTE\Game', 'name', 'game');
        }
        public function name_ico()
        {
            return explode(' ', $this->game)[0];
        }
    }

}
