<?php 
namespace VanguardLTE
{
    class TournamentGame extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournament_games';
        protected $fillable = [
            'tournament_id', 
            'game_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function tournament()
        {
            return $this->belongsTo('VanguardLTE\Tournament');
        }
        public function game()
        {
            return $this->belongsTo('VanguardLTE\Game');
        }
    }

}
