<?php 
namespace VanguardLTE;
use Illuminate\Support\Facades\Auth;
{
    class Tournament extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tournaments';
        protected $fillable = [
            'name', 
            'start', 
            'end', 
            'type', 
            'bet', 
            'spins', 
            'bots', 
            'bots_time', 
            'bots_step', 
            'bots_limit', 
            'sum_prizes', 
            'games_selected', 
            'repeat_days', 
            'repeat_number', 
            'last_repeat', 
            'image', 
            'description', 
            'status', 
            'wager', 
            'shop_id'
        ];
        public static $values = [
            'type' => [
                'amount_of_bets' => 'Amount of bets', 
                'amount_of_winnings' => 'Amount of winnings', 
                'win_to_bet_ratio' => 'Win to Bet Ratio', 
                'profit' => 'Profit'
            ], 
            'status' => [
                'active' => 'Active', 
                'waiting' => 'Waiting', 
                'completed' => 'Completed'
            ], 
            'bots' => [
                0, 
                1, 
                5, 
                10, 
                25, 
                50, 
                100
            ], 
            'bots_time' => [
                1, 
                5, 
                10, 
                15, 
                30, 
                45, 
                60
            ], 
            'bots_step' => [
                '1 - 5', 
                '5 - 10', 
                '10 - 25', 
                '25 - 50', 
                '50 - 100', 
                '100 - 1000'
            ], 
            'bots_limit' => [
                5, 
                10, 
                50, 
                100, 
                200, 
                300, 
                500, 
                1000, 
                5000, 
                10000, 
                100000
            ], 
            'wager' => [
                '1' => 'x1', 
                '2' => 'x2', 
                '3' => 'x3', 
                '4' => 'x4', 
                '5' => 'x5', 
                '10' => 'x10'
            ], 
            'bet' => [
                0.01, 
                0.02, 
                0.05, 
                0.1, 
                0.2, 
                0.3, 
                0.5, 
                1, 
                2, 
                3, 
                5, 
                10
            ], 
            'spins' => [
                1, 
                5, 
                10, 
                20, 
                30, 
                40, 
                50, 
                100, 
                200, 
                300, 
                400, 
                500, 
                1000
            ], 
            'repeat_days' => [
                1, 
                2, 
                3, 
                4, 
                5, 
                6, 
                7, 
                10, 
                15, 
                30
            ], 
            'repeat_number' => [
                1, 
                2, 
                3, 
                4, 
                5, 
                6, 
                7, 
                10, 
                15, 
                30, 
                40, 
                50, 
                100, 
                1000
            ]
        ];
        public static function boot()
        {
            parent::boot();
            self::saved(function($model)
            {
                Tournament::where('id', $model->id)->update(['name' => Lib\Functions::remove_emoji($model->name)]);
            });
            self::deleting(function($model)
            {
                TournamentPrize::where('tournament_id', $model->id)->delete();
                TournamentCategory::where('tournament_id', $model->id)->delete();
                TournamentGame::where('tournament_id', $model->id)->delete();
                TournamentBot::where('tournament_id', $model->id)->delete();
                TournamentStat::where('tournament_id', $model->id)->delete();
            });
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
        public function prizes()
        {
            return $this->hasMany('VanguardLTE\TournamentPrize');
        }
        public function categories()
        {
            return $this->hasMany('VanguardLTE\TournamentCategory');
        }
        public function games()
        {
            return $this->hasMany('VanguardLTE\TournamentGame');
        }
        public function users()
        {
            return $this->hasMany('VanguardLTE\TournamentBot', 'tournament_id', 'id');
        }
        public function stats()
        {
            return $this->hasMany('VanguardLTE\TournamentStat');
        }
        public function is_waiting()
        {
            return \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($this->start), false) >= 0;
        }
        public function is_completed()
        {
            return \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($this->end), false) <= 0;
        }
        public function my_place($user_id = false)
        {
            $_obf_0D1E2E293B142C1D262E3940223409330105175B0A0922 = 0;
            $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 = 1;
            if( !$user_id ) 
            {
                if( Auth::check() ) 
                {
                    $user_id = auth()->user()->id;
                }
                else
                {
                    $user_id = 0;
                }
            }
            $stats = $this->get_stats(0, 50000);
            if( count($stats) ) 
            {
                foreach( $stats as $stat ) 
                {
                    if( !$stat['is_bot'] && $stat['user_id'] == $user_id ) 
                    {
                        $_obf_0D1E2E293B142C1D262E3940223409330105175B0A0922 = $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422;
                    }
                    $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422++;
                }
            }
            return $_obf_0D1E2E293B142C1D262E3940223409330105175B0A0922;
        }
        public function get_ranking()
        {
            if( $this->stats ) 
            {
                foreach( $this->stats as $stat ) 
                {
                    if( !$stat->is_bot ) 
                    {
                        if( $this->type == 'amount_of_bets' ) 
                        {
                            $stat->update(['points' => $stat->sum_of_bets]);
                        }
                        if( $this->type == 'amount_of_winnings' ) 
                        {
                            $stat->update(['points' => $stat->sum_of_wins]);
                        }
                        if( $this->type == 'win_to_bet_ratio' ) 
                        {
                            $stat->update(['points' => $stat->sum_of_wins / $stat->sum_of_bets]);
                        }
                        if( $this->type == 'profit' ) 
                        {
                            $stat->update(['points' => $stat->sum_of_wins - $stat->sum_of_bets]);
                        }
                    }
                }
            }
            $data = [
                'prized' => [], 
                'other' => []
            ];
            $_obf_0D06103B293F142A1D3023302D022E332B0408101E3E01 = [0];
            if( $this->prizes ) 
            {
                $_obf_0D151F133C2F1D235B13141906382E1D281401022F2A32 = 0;
                foreach( $this->prizes->sortByDesc('prize') as $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 => $prize ) 
                {
                    $prized = false;
                    while( !$prized ) 
                    {
                        $stat = TournamentStat::where('tournament_id', $this->id)->orderBy('points', 'DESC')->skip($_obf_0D151F133C2F1D235B13141906382E1D281401022F2A32)->first();
                        if( !$stat ) 
                        {
                            break;
                        }
                        if( $stat->is_bot ) 
                        {
                            $prized = true;
                            $data['prized'][] = [
                                'stat' => $stat, 
                                'prize' => $prize
                            ];
                            $_obf_0D06103B293F142A1D3023302D022E332B0408101E3E01[] = $stat->user_id;
                            continue;
                        }
                        if( $this->spins <= $stat->spins ) 
                        {
                            $prized = true;
                            $data['prized'][] = [
                                'stat' => $stat, 
                                'prize' => $prize
                            ];
                            $_obf_0D06103B293F142A1D3023302D022E332B0408101E3E01[] = $stat->user_id;
                            continue;
                        }
                        $_obf_0D151F133C2F1D235B13141906382E1D281401022F2A32++;
                    }
                    $_obf_0D151F133C2F1D235B13141906382E1D281401022F2A32++;
                }
            }
            $stats = TournamentStat::whereNotIn('user_id', $_obf_0D06103B293F142A1D3023302D022E332B0408101E3E01)->where('tournament_id', $this->id)->orderBy('points', 'DESC')->get();
            if( $stats ) 
            {
                $data['other'] = $stats;
            }
            return $data;
        }
        public function get_stats($skip = 0, $take = 5, $not_zero = false)
        {
            $_obf_0D05021B072C0A302C39231528025C04342539013E3901 = [];
            $data = [];
            if( $this->is_completed() ) 
            {
                if( $prizes = $this->stats->where('prize_id', '!=', 0)->sortBy('prize_id') ) 
                {
                    foreach( $prizes as $prize ) 
                    {
                        if( $not_zero && $prize->points <= 0 ) 
                        {
                            continue;
                        }
                        $data[] = [
                            'is_bot' => $prize->is_bot, 
                            'user_id' => $prize->user_id, 
                            'username' => $prize->getUsername(), 
                            'points' => number_format($prize->points, 2, '.', ''), 
                            'prize' => ($prize->prize ? number_format($prize->prize->prize, 2, '.', '') : '')
                        ];
                    }
                }
                if( $stats = $this->stats->where('prize_id', '=', 0)->sortByDesc('points') ) 
                {
                    foreach( $stats as $stat ) 
                    {
                        if( $not_zero && $stat->points <= 0 ) 
                        {
                            continue;
                        }
                        $data[] = [
                            'is_bot' => $stat->is_bot, 
                            'user_id' => $stat->user_id, 
                            'username' => $stat->getUsername(), 
                            'points' => number_format($stat->points, 2, '.', ''), 
                            'prize' => ''
                        ];
                    }
                }
            }
            else
            {
                $stats = $this->get_ranking();
                if( isset($stats['prized']) && count($stats['prized']) ) 
                {
                    foreach( $stats['prized'] as $stat ) 
                    {
                        if( $not_zero && $stat['stat']->points <= 0 ) 
                        {
                            continue;
                        }
                        $data[] = [
                            'is_bot' => $stat['stat']->is_bot, 
                            'user_id' => $stat['stat']->user_id, 
                            'username' => $stat['stat']->getUsername(), 
                            'points' => number_format($stat['stat']->points, 2, '.', ''), 
                            'prize' => number_format($stat['prize']->prize, 2, '.', '')
                        ];
                    }
                }
                if( isset($stats['other']) && count($stats['other']) ) 
                {
                    foreach( $stats['other'] as $stat ) 
                    {
                        if( $not_zero && $stat->points <= 0 ) 
                        {
                            continue;
                        }
                        $data[] = [
                            'is_bot' => $stat->is_bot, 
                            'user_id' => $stat->user_id, 
                            'username' => $stat->getUsername(), 
                            'points' => number_format($stat->points, 2, '.', ''), 
                            'prize' => ''
                        ];
                    }
                }
            }
            if( $skip < count($data) ) 
            {
                return array_slice($data, $skip, $take);
            }
            return $_obf_0D05021B072C0A302C39231528025C04342539013E3901;
        }
    }

}
