<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Notification;
use VanguardLTE\Notifications\NewAnswer;
use VanguardLTE\Notifications\NewTicket;
use VanguardLTE\TicketAnswer;

class Notifications
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);

        $notifications = Notification::get();
        if($notifications){
            foreach($notifications AS $notification){
                try{
                    $title = "VanguardLTE\Notifications\\".$notification->notification;
                    if($notification->user){
                        if( $notification->notification == 'NewAnswer'){
                            $answer = TicketAnswer::find($notification->data);
                            $notification->user->notify(new NewAnswer($answer, $answer->ticket ));
                        } elseif( $notification->notification == 'NewTicket'){
                            $notification->user->notify(new NewTicket($notification->data));
                        } else{
                            $notification->user->notify(new $title($notification->data));
                        }
                        $notification->delete();
                    }
                }
                catch(\Exception $e){ // Using a generic exception
                    //Info($e->getMessage());
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('Notifications');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }
}