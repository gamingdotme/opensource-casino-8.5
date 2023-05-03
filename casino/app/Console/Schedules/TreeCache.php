<?php


namespace VanguardLTE\Console\Schedules;


use Illuminate\Support\Facades\Cache;
use VanguardLTE\User;

class TreeCache
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $members = User::where('role_id', '>', 3)->get();
        if(count($members)){
            foreach($members AS $member){
                $tree = [];
                $users = User::where('id', $member->id)->get();
                if( $member->hasRole('admin') ){
                    $users = User::where('role_id', 5)->get();
                }
                if( $member->hasRole(['admin','agent']) ){
                    $tree['agents'] = [];
                } else{
                    $tree['distributors'] = [];
                }
                foreach ($users AS $user){
                    if($user->hasRole('agent')){
                        $tree['agents'][$user->id] = [
                            'href' => route('backend.user.edit', ['user' => $user->id], false),
                            'text' => $user->username ?: trans('app.n_a'),
                            'balance' => $user->balance,
                            'rowspan' => $user->getRowspan(),
                            'distributors' => []
                        ];
                        if( $distributors = $user->getInnerUsers() ){
                            foreach($distributors AS $distributor){
                                $tree['agents'][$user->id]['distributors'][$distributor->id] = $distributor->distributor();
                            }
                        }
                    }
                    if($user->hasRole('distributor')){
                        $tree['distributor'] = $user->distributor();
                    }
                }

                Cache::put('tree:'.$member->id, $tree, 11*60);

            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('TreeCache');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }
    }

}