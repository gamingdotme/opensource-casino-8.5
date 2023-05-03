<?php 
namespace VanguardLTE
{
    class Payment extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'payments';
        protected $fillable = [
            'user_id', 
            'sum', 
            'currency', 
            'credit_id', 
            'status', 
            'system', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function credit()
        {
            return $this->hasOne('VanguardLTE\Credit', 'id', 'credit_id');
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
