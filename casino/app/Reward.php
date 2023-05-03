<?php 
namespace VanguardLTE
{
    class Reward extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'rewards';
        protected $fillable = [
            'user_id', 
            'referral_id', 
            'payed', 
            'sum', 
            'ref_sum', 
            'activated', 
            'user_received', 
            'referral_received', 
            'until', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'user_id');
        }
        public function referral()
        {
            return $this->belongsTo('VanguardLTE\User', 'referral_id');
        }
    }

}
