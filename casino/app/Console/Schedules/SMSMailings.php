<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use VanguardLTE\Lib\SMS_sender;
use VanguardLTE\SMSMailing;

class SMSMailings
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $sms_mailings = SMSMailing::where('date_start', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->whereHas('sms_messages', function ($query)  {
                $query->where('sent', 0);
            })->get();
        if($sms_mailings){
            foreach ($sms_mailings AS $sms_mailing){
                SMS_sender::mailing($sms_mailing);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('SMSMailings');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}