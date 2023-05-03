<?php 
namespace VanguardLTE
{
    class Transaction extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'transactions';
        protected $fillable = [
            'user_id', 
            'payeer_id', 
            'system', 
            'value', 
            'type', 
            'summ', 
            'status', 
            'shop_id'
        ];
        public static function boot()
        {
            parent::boot();
        }
        public function admin()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'payeer_id');
        }
        public function user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'user_id');
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
    }

}
