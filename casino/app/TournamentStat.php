<?php 
namespace VanguardLTE
{
    class TournamentStat extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournament_stats';
        protected $fillable = [
            'tournament_id', 
            'user_id', 
            'username', 
            'is_bot', 
            'sum_of_bets', 
            'sum_of_wins', 
            'points', 
            'spins', 
            'prize_id'
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
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
        public function bot()
        {
            return $this->belongsTo('VanguardLTE\TournamentBot', 'user_id', 'id');
        }
        public function prize()
        {
            return $this->belongsTo('VanguardLTE\TournamentPrize');
        }
        public function getUsername()
        {
            $username = '';
            if( $this->username != '' ) 
            {
                return $this->username;
            }
            if( $this->is_bot ) 
            {
                $username = $this->bot->username;
            }
            else
            {
                $username = $this->user->username;
            }
            if( $username != '' ) 
            {
                for( $_obf_0D0D152E23100D0A032D292F022E10330B280A5B1E3201 = 0; $_obf_0D0D152E23100D0A032D292F022E10330B280A5B1E3201 < strlen($username); $_obf_0D0D152E23100D0A032D292F022E10330B280A5B1E3201++ ) 
                {
                    $_obf_0D1A1A330F1206221B050C07273C212B250D2C2C140611 = rand(0, 1);
                    if( $_obf_0D1A1A330F1206221B050C07273C212B250D2C2C140611 ) 
                    {
                        $username[$_obf_0D0D152E23100D0A032D292F022E10330B280A5B1E3201] = '*';
                    }
                }
            }
            $this->update(['username' => $username]);
            return $username;
        }
    }

}
