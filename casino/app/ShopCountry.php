<?php 
namespace VanguardLTE
{
    class ShopCountry extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'shops_countries';
        protected $fillable = [
            'shop_id', 
            'country'
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
