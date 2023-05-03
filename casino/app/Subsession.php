<?php 
namespace VanguardLTE
{
    class Subsession extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'subsessions';
        protected $fillable = [
            'user_id', 
            'subsession', 
            'active'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
