<?php 
namespace VanguardLTE
{
    class FishBank extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'fish_bank';
        protected $fillable = [
            'fish', 
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
                if( $model->shop->shop_limit < $model->fish ) 
                {
                    $out = $model->fish - $model->shop->shop_limit;
                    if( $limit < $out ) 
                    {
                        $out = $limit;
                        if( $min_limit < $model->fish ) 
                        {
                            $out = $model->fish - $min_limit;
                            $out = intval($out / 10) * 10;
                        }
                        Statistic::create([
                            'title' => 'Fish', 
                            'user_id' => 1, 
                            'type' => 'out', 
                            'sum' => $out, 
                            'system' => 'bank', 
                            'old' => $model->fish, 
                            'shop_id' => $model->shop_id, 
                            'created_at' => \Carbon\Carbon::now()->addMinute()->format('Y-m-d H:i:s')
                        ]);
                        FishBank::where('id', $model->id)->update(['fish' => $model->fish - $out]);
                    }
                }
            });
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
    }

}
