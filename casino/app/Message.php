<?php 
namespace VanguardLTE
{
    class Message extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'messages';
        protected $fillable = [
            'user_id', 
            'type', 
            'value', 
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
