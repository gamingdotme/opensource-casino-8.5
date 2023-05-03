<?php 
namespace VanguardLTE
{
    class Security extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'securities';
        protected $fillable = [
            'type', 
            'item_id', 
            'pay_in', 
            'pay_out', 
            'pay_total', 
            'balance', 
            'bank', 
            'rtp', 
            'count', 
            'view', 
            'shop_id', 
            'created_at', 
            'sms', 
            'block', 
            'category', 
            'win'
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
        public function game()
        {
            return $this->belongsTo('VanguardLTE\Game', 'item_id', 'id');
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'item_id', 'id');
        }
    }

}
