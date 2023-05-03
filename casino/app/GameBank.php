<?php 
namespace VanguardLTE
{
    class GameBank extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'game_bank';
        protected $fillable = [
            'slots', 
            'little', 
            'table_bank', 
            'bonus', 
            'temp_rtp', 
            'shop_id'
        ];
        public static $values = [
            'banks' => [
                '', 
                '0', 
                '10', 
                '20', 
                '50', 
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
                '10000', 
                '50000', 
                '100000'
            ]
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
            self::updated(function($model)
            {
                $limit = intval($model->shop->shop_limit * 0.1);
                $min_limit = $model->shop->shop_limit - $limit;
                foreach( [
                    'slots', 
                    'little', 
                    'table_bank', 
                    'bonus'
                ] as $bank ) 
                {
                    if( $model->shop_id > 0 && $model->shop->shop_limit < $model->$bank ) 
                    {
                        $out = $model->$bank - $model->shop->shop_limit;
                        if( $out > 20 ) 
                        {
                            $out = $limit;
                            if( $min_limit < $model->$bank ) 
                            {
                                $out = $model->$bank - $min_limit;
                                $out = intval($out / 10) * 10;
                            }
                            $type = ($bank == 'table_bank' ? 'table' : $bank);
                            Statistic::create([
                                'title' => ucfirst($type), 
                                'user_id' => 1, 
                                'type' => 'out', 
                                'sum' => $out, 
                                'system' => 'bank', 
                                'old' => $model->$bank, 
                                'shop_id' => $model->shop_id, 
                                'created_at' => \Carbon\Carbon::now()->addMinute()->format('Y-m-d H:i:s')
                            ]);
                            GameBank::where('id', $model->id)->update([$bank => $model->$bank - $out]);
                        }
                    }
                }
            });
        }
        public function getFishAttribute()
        {
            $fish = FishBank::where('shop_id', $this->shop_id)->first();
            if( $fish ) 
            {
                return $fish->fish;
            }
            return 0;
        }
        public function setFishAttribute($value)
        {
            $fish = FishBank::where('shop_id', $this->shop_id)->first();
            if( $fish ) 
            {
                $fish->fish = $value;
            }
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
        public function total()
        {
            return $this->slots + $this->little + $this->table_bank + $this->fish + $this->bonus;
        }
        public function get_rtp()
        {
            $_obf_0D08401A5B3F3113162A323C32271801221F3E35351532 = 0;
            $out = 0;
            $_obf_0D08401A5B3F3113162A323C32271801221F3E35351532 = Game::where('shop_id', $this->shop_id)->sum('stat_in');
            $out = Game::where('shop_id', $this->shop_id)->sum('stat_out');
            return ($_obf_0D08401A5B3F3113162A323C32271801221F3E35351532 > 0 ? number_format($out / $_obf_0D08401A5B3F3113162A323C32271801221F3E35351532 * 100, 2) : 0);
        }
    }

}
