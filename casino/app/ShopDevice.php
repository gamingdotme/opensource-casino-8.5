<?php 
namespace VanguardLTE
{
    class ShopDevice extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'shops_devices';
        protected $fillable = [
            'shop_id', 
            'device'
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
