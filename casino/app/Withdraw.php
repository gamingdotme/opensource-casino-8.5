<?php 
namespace VanguardLTE
{
    class Withdraw extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'withdraw_funds';
        protected $fillable = [
            'user_id', 
            'amount', 
            'currency', 
            'wallet', 
            'status', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'user_id');
        }
        public function shop()
        {
            return $this->hasOne('VanguardLTE\Shop', 'id', 'shop_id');
        }
    }

}
