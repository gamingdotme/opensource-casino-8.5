<?php 
namespace VanguardLTE
{
    class OpenShift extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'open_shift';
        protected $fillable = [
            'user_id', 
            'balance', 
            'balance_in', 
            'balance_out', 
            'users', 
            'jpg', 
            'money_in', 
            'money_out', 
            'transfers', 
            'old_banks', 
            'last_banks', 
            'shop_id', 
            'start_date', 
            'end_date'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function getBalanceAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getBalanceInAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getBalanceOutAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getUsersAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getJpgAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getMoneyInAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getMoneyOutAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getOldBanksAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getLastBanksAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function getLastRefundsAttribute($value)
        {
            return number_format($value, 4, '.', '');
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop', 'shop_id');
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'user_id');
        }
        public function get_jpg()
        {
            return JPG::where('shop_id', $this->shop_id)->sum('balance');
        }
        public function get_money()
        {
            return User::where([
                'shop_id' => $this->shop_id, 
                'role_id' => 1
            ])->sum('balance');
        }
        public function banks()
        {
            $GameBank = GameBank::select(\DB::raw('SUM(slots+little+table_bank+bonus) AS balance'))->where('shop_id', $this->shop_id)->first();
            $FishBank = FishBank::select(\DB::raw('fish'))->where('shop_id', $this->shop_id)->first();
            if( $GameBank && $FishBank ) 
            {
                return $GameBank->balance + $FishBank->fish;
            }
            return 0;
        }
        public function refunds()
        {
            return User::where([
                'shop_id' => $this->shop_id, 
                'role_id' => 1
            ])->sum('refunds');
        }
        public function profit()
        {
            $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 = StatGame::where('shop_id', $this->shop_id);
            if( $this->start_date ) 
            {
                $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 = $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->where('date_time', '>=', $this->start_date);
            }
            if( $this->end_date ) 
            {
                $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 = $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->where('date_time', '<=', $this->end_date);
            }
            return $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->sum('in_profit');
        }
    }

}
