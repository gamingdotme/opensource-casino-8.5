<?php 
namespace VanguardLTE
{
    class QuickShop extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'quick_shops';
        protected $fillable = ['data'];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
