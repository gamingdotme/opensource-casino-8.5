<?php 
namespace VanguardLTE
{
    class Notification extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'notifications';
        protected $fillable = [
            'notification', 
            'user_id', 
            'data'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
    }

}
