<?php 
namespace VanguardLTE
{
    class OpenShiftTemp extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'open_shift_temp';
        protected $fillable = [
            'field', 
            'type', 
            'value', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
