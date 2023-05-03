<?php 
namespace VanguardLTE
{
    class ShopUser extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'shops_user';
        protected $fillable = [
            'shop_id', 
            'user_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop', 'shop_id');
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'user_id');
        }
    }

}
