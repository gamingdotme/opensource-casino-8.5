<?php 
namespace VanguardLTE
{
    class JPG extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'jpg';
        protected $fillable = [
            'date_time', 
            'name', 
            'balance', 
            'pay_sum', 
            'start_balance', 
            'percent', 
            'user_id', 
            'shop_id'
        ];
        public static $values = [
            'percent' => [
                '1.00', 
                '0.90', 
                '0.80', 
                '0.70', 
                '0.60', 
                '0.50', 
                '0.40', 
                '0.30', 
                '0.20', 
                '0.10'
            ], 
            'balances' => [
                '', 
                '0', 
                '100', 
                '200', 
                '300', 
                '400', 
                '500', 
                '1000', 
                '2000', 
                '3000', 
                '4000', 
                '5000', 
                '10000'
            ], 
            'start_balance' => [
                '1 - 5', 
                '5 - 10'
            ], 
            'pay_sum' => [
                '50 - 60', 
                '100 - 110', 
                '200 - 210', 
                '300 - 310', 
                '400 - 410', 
                '500 - 510', 
                '1000 - 1010', 
                '2000 - 2010', 
                '3000 - 3010', 
                '4000 - 4010', 
                '5000 - 5010', 
                '10000 - 10010'
            ]
        ];
        public $timestamps = false;
        public $_pay_sum = false;
        public $_start_balance = false;
        public static function boot()
        {
            parent::boot();
            self::saved(function($model)
            {
                JPG::where('id', $model->id)->update(['name' => Lib\Functions::remove_emoji($model->name)]);
            });
            self::updated(function($model)
            {
                event(new Events\Jackpot\JackpotEdited($model));
            });
        }
        public function add_jpg($type, $sum, $check = true)
        {
            $shop = Shop::find($this->shop_id);
            $user = User::where('role_id', 6)->first();
            $old = $this->balance;
            if( !$shop ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_shop')]);
            }
            if( !$sum ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_sum')]);
            }
            if( $type == 'out' && $this->balance < $sum ) 
            {
                return [
                    'success' => false, 
                    'text' => 'Not enough money in the jackpot balance "' . $this->name . '". Only ' . $this->balance
                ];
            }
            $sum = ($type == 'out' ? -1 * $sum : $sum);
            if( $this->balance + $sum < 0 ) 
            {
                return [
                    'success' => false, 
                    'text' => 'Balance < 0'
                ];
            }
            $this->update(['balance' => $this->balance + $sum]);
            if( $user && $this->shop_id > 0 ) 
            {
                Statistic::create([
                    'title' => 'JPG ' . $this->id, 
                    'user_id' => $user->id, 
                    'type' => $type, 
                    'system' => 'jpg', 
                    'sum' => abs($sum), 
                    'old' => $old, 
                    'shop_id' => $shop->id
                ]);
            }
            return ['success' => true];
        }
        public function get_pay_sum()
        {
            if( $this->_pay_sum ) 
            {
                return $this->_pay_sum;
            }
            $_obf_0D1725310217195C0C04125C351E040D1D193822021E11 = explode(' - ', JPG::$values['pay_sum'][$this->pay_sum]);
            $this->_pay_sum = rand($_obf_0D1725310217195C0C04125C351E040D1D193822021E11[0], $_obf_0D1725310217195C0C04125C351E040D1D193822021E11[1]);
            return $this->_pay_sum;
        }
        public function get_start_balance()
        {
            if( $this->_start_balance ) 
            {
                return $this->_start_balance;
            }
            $_obf_0D1725310217195C0C04125C351E040D1D193822021E11 = explode(' - ', JPG::$values['start_balance'][$this->start_balance]);
            $this->_start_balance = rand($_obf_0D1725310217195C0C04125C351E040D1D193822021E11[0], $_obf_0D1725310217195C0C04125C351E040D1D193822021E11[1]);
            return $this->_start_balance;
        }
        public function get_min($field)
        {
            if( in_array($field, [
                'pay_sum', 
                'start_balance'
            ]) ) 
            {
                $_obf_0D1725310217195C0C04125C351E040D1D193822021E11 = explode(' - ', JPG::$values[$field][$this->$field]);
                return $_obf_0D1725310217195C0C04125C351E040D1D193822021E11[0];
            }
            return 0;
        }
        public function getPercent($user_id = false)
        {
            if( !$user_id ) 
            {
                $user_id = auth()->user()->id;
            }
            $user = User::find($user_id);
            if( !$user ) 
            {
                return $this->percent;
            }
            if( $user->count_tournaments > 0 || $user->count_happyhours > 0 || $user->count_refunds > 0 || $user->count_progress > 0 || $user->count_daily_entries > 0 || $user->count_invite > 0 || $user->count_welcomebonus > 0 || $user->count_smsbonus > 0 || $user->count_wheelfortune > 0 ) 
            {
                return 0;
            }
            return $this->percent;
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
    }

}
