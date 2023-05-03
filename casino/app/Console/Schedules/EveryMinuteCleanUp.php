<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use VanguardLTE\User;

class EveryMinuteCleanUp
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $users = User::where('phone_verified', 0)->where('sms_token', '!=' ,'')->get();
        if( $users){
            foreach ($users as $user) {
                $now = Carbon::now();
                $times = $now->diffInSeconds(Carbon::parse($user->sms_token_date), false);
                if( $times <= 0 ){
                    $user ->update([
                        'phone' => '',
                        'phone_verified' => 0,
                        'sms_token' => '',
                    ]);
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('EveryMinuteCleanUp');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}