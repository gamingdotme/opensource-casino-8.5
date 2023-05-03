<?php 
namespace VanguardLTE
{
    class Progress extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'progress';
        protected $fillable = [
            'sum', 
            'type', 
            'spins', 
            'bet', 
            'shop_id', 
            'rating', 
            'bonus', 
            'day', 
            'min', 
            'max', 
            'percent', 
            'min_balance', 
            'wager', 
            'status', 
            'days_active'
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
            'percent' => [
                1, 
                5, 
                10, 
                20, 
                30, 
                40, 
                50
            ], 
            'days_active' => [
                5, 
                10, 
                15, 
                20, 
                30, 
                40, 
                50, 
                100
            ], 
            'day' => [
                'Monday', 
                'Tuesday', 
                'Wednesday', 
                'Thursday', 
                'Friday', 
                'Saturday', 
                'Sunday'
            ]
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function badge()
        {
            if( strlen($this->rating) == 1 ) 
            {
                return '0' . $this->rating;
            }
            else
            {
                return $this->rating;
            }
        }
    }

}
