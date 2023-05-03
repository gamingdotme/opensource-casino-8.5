<?php

namespace VanguardLTE\Listeners;

use VanguardLTE\Events\HappyHours\HappyHourEdited;
use VanguardLTE\Events\HappyHours\NewHappyHour;
use VanguardLTE\Events\HappyHours\DeleteHappyHour;
use VanguardLTE\Services\Logging\UserActivity\Logger;

use VanguardLTE\HappyHour;

class HappyHourEventsSubscriber
{
    /**
     * @var UserActivityLogger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onNewHappyHour(NewHappyHour $event)
    {
        $happyhour = $event->getNewHappyHour();

        //$this->logger->log('New HappyHour / ' . $happyhour->id . ', Shop ' . $happyhour->shop_id, $type = 'system', 'happyhour', $happyhour->id);
    }

    public function onHappyHourEdited(HappyHourEdited $event)
    {
        $happyhour = $event->getEditedHappyHour();
        $original = $happyhour->getOriginal();
        $changes = $happyhour->getChanges();

        $text = 'Update HH ' . $happyhour->id . ' | ';
        $textOriginal = 'Update HH ' . $happyhour->id . ' | ';

        foreach(['multiplier' => 'Multiplier', 'wager' => 'Wager', 'time' => 'Time',
                    'status' => 'Status'] AS $column=>$title){

            if( isset($original[$column]) ){
                $textOriginal .= $this->template($column, $title, $original[$column]);
            } else{
                $textOriginal .= ' ' .$title. ' =  | ';
            }
            if( isset($changes[$column]) ){
                $changed = true;
                $text .= $this->template($column, $title, $changes[$column]);
            } else{
                if( isset($original[$column]) ){
                    $text .= $this->template($column, $title, $original[$column]);
                } else{
                    $text .= ' ' .$title. ' =  | ';
                }
            }
        }

        if(!$changed){
            return;
        }

        if(!$changed){
            return;
        }


        $this->logger->log($text, $textOriginal, $type = 'system', 'happyhour', $happyhour->id);
    }

    public function onDeleteHappyHour(DeleteHappyHour $event)
    {
        $happyhour = $event->getDeleteHappyHour();
        //$this->logger->log('Delete HappyHour / ' . $happyhour->id . ', Shop ' . $happyhour->shop_id, $type = 'system', 'happyhour', $happyhour->id);
    }

    public function template($key, $title, $value){
        $text = '';
        if($key == 'time'){
            $text .= ' ' .$title. ' = ' . (HappyHour::$values['time'][$value]) . ' | ';
        } else {
            $text .= ' ' . $title. ' = ' . $value . ' | ';
        }
        return $text;
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $class = 'VanguardLTE\Listeners\HappyHourEventsSubscriber';

        $events->listen(NewHappyHour::class, "{$class}@onNewHappyHour");
        $events->listen(HappyHourEdited::class, "{$class}@onHappyHourEdited");
        $events->listen(DeleteHappyHour::class, "{$class}@onDeleteHappyHour");
    }
}
