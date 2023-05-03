<?php 
namespace VanguardLTE
{
    class Invite extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'invites';
        protected $fillable = [
            'message', 
            'type', 
            'sum', 
            'sum_ref', 
            'min_amount', 
            'waiting_time', 
            'wager', 
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
            'type' => [
                'one_pay' => 'One Pay', 
                'sum_pay' => 'Sum Pay'
            ]
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
    }

}
