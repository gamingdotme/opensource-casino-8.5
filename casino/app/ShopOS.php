<?php 
namespace VanguardLTE
{
    class ShopOS extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'shops_os';
        protected $fillable = [
            'shop_id', 
            'os'
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
    }

}
