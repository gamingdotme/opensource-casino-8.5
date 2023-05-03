<?php


namespace VanguardLTE\Console\Schedules;


use Illuminate\Support\Facades\Cache;
use VanguardLTE\User;

class HierarchyUsersCache
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $users = [];
        $users_all = [];

        $members = User::orderBy('role_id', 'DESC')->get();
        if(count($members)){
            foreach($members AS $member){

                Cache::put('usersShops:'.$member->id, array_merge([0], $member->shops_array(true)), 10*60 );

                $memberShops = Cache::get('usersShops:'.$member->id);

                //$memberShops = array_merge([0], $member->shops_array(true));

                foreach ($memberShops AS $shop_id){

                    if(!isset($users[$member->id][$shop_id])){
                        $users[$member->id][$shop_id] = [];
                    }
                    if(!isset($users_all[$member->id])){
                        $users_all[$member->id] = [];
                    }

                    if( $member->parent_id ){
                        $users[$member->parent_id][$shop_id][] = $member->id;
                        $users_all[$member->parent_id][$member->id] = $member->id;
                    }

                    if($shop_id > 0){
                        foreach($users_all AS $user_id=>$inner){
                            foreach($inner AS $inner_id){
                                if( $inner_id == $member->parent_id && in_array($shop_id, cache('usersShops:'.$inner_id))){
                                    $users[$user_id][$shop_id][] = $member->id;
                                    $users_all[$user_id][$member->id] = $member->id;
                                }
                            }
                        }
                    }

                }


            }
        }

        if(count($members)) {
            foreach ($members AS $member) {
                if( isset($users[$member->id])){
                    foreach($users[$member->id] AS $shop_id=>$items){
                        Cache::put('hierarchyUsers:'.$member->id.':'.$shop_id, array_merge($items, [$member->id]), 11*60);
                    }
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('HierarchyUsersCache');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}