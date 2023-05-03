<?php 
namespace VanguardLTE
{
    class Session extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'sessions';
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
