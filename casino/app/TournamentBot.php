<?php 
namespace VanguardLTE
{
    class TournamentBot extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournament_bots';
        protected $fillable = [
            'tournament_id', 
            'username'
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
