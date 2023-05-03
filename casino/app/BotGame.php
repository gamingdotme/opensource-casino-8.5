<?php 
namespace VanguardLTE
{
    class BotGame extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'bots_games';
        protected $fillable = [
            'game_id', 
            'device', 
            'login', 
            'bet', 
            'win', 
            'date_time', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function game()
        {
            return $this->hasOne('VanguardLTE\Game', 'id', 'game_id');
        }
    }

}
