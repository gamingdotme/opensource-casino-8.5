<?php 
namespace VanguardLTE
{
    class Credit extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'credits';
        protected $fillable = [
            'credit', 
            'price'
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
