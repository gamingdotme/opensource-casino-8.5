<?php 
namespace VanguardLTE
{
    class ProgressUser extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'progress_users';
        protected $fillable = [
            'sum', 
            'rating', 
            'spins', 
            'user_id', 
            'progress_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
