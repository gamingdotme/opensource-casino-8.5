<?php 
namespace VanguardLTE
{
    class SMSBonusItem extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'sms_bonus_items';
        protected $fillable = [
            'user_id', 
            'days', 
            'sms_bonus_id', 
            'bonus', 
            'status', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
