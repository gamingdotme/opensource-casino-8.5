<?php 
namespace VanguardLTE
{
    class TournamentPrize extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournament_prizes';
        protected $fillable = [
            'tournament_id', 
            'prize'
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
    }

}
