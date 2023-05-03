<?php 
namespace VanguardLTE
{
    class WelcomeBonus extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'welcomebonuses';
        protected $fillable = [
            'pay', 
            'sum', 
            'type', 
            'bonus', 
            'wager'
        ];
        public $timestamps = false;
        public static $values = [
            'bonus' => [
                10, 
                50, 
                100, 
                200, 
                300, 
                400, 
                500, 
                1000, 
                2000, 
                3000, 
                4000, 
                5000
            ], 
            'wager' => [
                '1' => 'x1', 
                '2' => 'x2', 
                '3' => 'x3', 
                '4' => 'x4', 
                '5' => 'x5', 
                '10' => 'x10'
            ], 
            'type' => ['one_pay' => 'One Pay'], 
            'systems' => [
                'handpay', 
                'interkassa', 
                'coinbase', 
                'btcpayserver'
            ]
        ];
        public static function boot()
        {
            parent::boot();
        }
    }

}
