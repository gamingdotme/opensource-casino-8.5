<?php 
namespace VanguardLTE
{
    class TournamentCategory extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournament_categories';
        protected $fillable = [
            'tournament_id', 
            'category_id'
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
