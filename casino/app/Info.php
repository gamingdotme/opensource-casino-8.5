<?php 
namespace VanguardLTE
{
    class Info extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'info';
        protected $fillable = [
            'title', 
            'text', 
            'roles', 
            'user_id', 
            'days'
        ];
        public static $values = [
            'days' => [
                1, 
                2, 
                3, 
                4, 
                5, 
                6, 
                7
            ]
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
            self::deleting(function($model)
            {
            });
        }
        public function user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'user_id');
        }
    }

}
