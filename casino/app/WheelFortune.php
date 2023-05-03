<?php 
namespace VanguardLTE
{
    class WheelFortune extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'wheelfortune';
        protected $fillable = [
            'wh1_1', 
            'wh1_2', 
            'wh1_3', 
            'wh1_4', 
            'wh1_5', 
            'wh1_6', 
            'wh1_7', 
            'wh2_1', 
            'wh2_2', 
            'wh2_3', 
            'wh2_4', 
            'wh2_5', 
            'wh2_6', 
            'wh2_7', 
            'wh2_8', 
            'wh3_1', 
            'wh3_2', 
            'wh3_3', 
            'wh3_4', 
            'wh3_5', 
            'wh3_6', 
            'wh3_7', 
            'wh3_8', 
            'wh3_9', 
            'wh3_10', 
            'wh3_11', 
            'wh3_12', 
            'wh3_13', 
            'wh3_14', 
            'wh3_15', 
            'wh3_16', 
            'wager', 
            'status', 
            'shop_id'
        ];
        public static $values = [
            'wager' => [
                '1' => 'x1', 
                '2' => 'x2', 
                '3' => 'x3', 
                '4' => 'x4', 
                '5' => 'x5', 
                '10' => 'x10'
            ], 
            'wh1' => [
                0, 
                1, 
                2, 
                3, 
                5, 
                10, 
                20, 
                30, 
                40, 
                50, 
                60, 
                70, 
                80, 
                90, 
                100, 
                'WH'
            ], 
            'wh2' => [
                0, 
                1, 
                2, 
                3, 
                5, 
                10, 
                20, 
                30, 
                40, 
                50, 
                60, 
                70, 
                80, 
                90, 
                100
            ]
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function get_data()
        {
            $data = [
                'wh1' => [], 
                'wh2' => [], 
                'wh3' => []
            ];
            for( $i = 1; $i <= 7; $i++ ) 
            {
                $data['wh1'][] = WheelFortune::$values['wh1'][$this->{'wh1_' . $i}];
            }
            for( $i = 1; $i <= 8; $i++ ) 
            {
                $data['wh2'][] = WheelFortune::$values['wh1'][$this->{'wh2_' . $i}];
            }
            for( $i = 1; $i <= 16; $i++ ) 
            {
                $data['wh3'][] = WheelFortune::$values['wh2'][$this->{'wh3_' . $i}];
            }
            return $data;
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
    }

}
