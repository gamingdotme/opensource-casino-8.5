<?php 
namespace VanguardLTE
{
    class SMSBonus extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'sms_bonuses';
        protected $fillable = [
            'days', 
            'bonus', 
            'wager', 
            'shop_id'
        ];
        public $timestamps = false;
        public static $values = [
            'days' => [
                5, 
                10, 
                15, 
                20, 
                30, 
                60, 
                90
            ], 
            'bonus' => [
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
                200, 
                300, 
                400, 
                500, 
                1000
            ], 
            'wager' => [
                '1' => 'x1', 
                '2' => 'x2', 
                '3' => 'x3', 
                '4' => 'x4', 
                '5' => 'x5', 
                '10' => 'x10'
            ]
        ];
        public static function boot()
        {
            parent::boot();
        }
    }

}
