<?php 
namespace VanguardLTE
{
    class PaymentSetting extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'payment_settings';
        protected $fillable = [
            'system', 
            'field', 
            'value', 
            'shop_id'
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
