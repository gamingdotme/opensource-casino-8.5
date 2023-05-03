<?php 
namespace VanguardLTE
{
    class SMS extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'sms';
        protected $fillable = [
            'message', 
            'type', 
            'message_id', 
            'user_id', 
            'status', 
            'shop_id', 
            'new_user_id', 
            'payed'
        ];
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'user_id');
        }
        public function new_user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'new_user_id');
        }
    }

}
