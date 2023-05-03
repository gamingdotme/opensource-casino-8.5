<?php

namespace VanguardLTE\Listeners;

use VanguardLTE\Activity;
use VanguardLTE\Events\Jackpot\NewJackpot;
use VanguardLTE\Events\Jackpot\JackpotEdited;
use VanguardLTE\Events\Jackpot\DeleteJackpot;
use VanguardLTE\Events\User\UserEventContract;
use VanguardLTE\JPG;
use VanguardLTE\Services\Logging\UserActivity\Logger;

class JackpotEventsSubscriber
{
    /**
     * @var UserActivityLogger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onNewJackpot(NewJackpot $event)
    {
        $jackpot = $event->getNewJackpot();

        //$this->logger->log('New Jackpot / ' . $jackpot->name . ', Shop ' . $jackpot->shop_id, $type = 'system', 'jackpot', $jackpot->id);
    }

    public function onJackpotEdited(JackpotEdited $event)
    {
        $jackpot = $event->getEditedJackpot();
        $original = $jackpot->getOriginal();
        $changes = $jackpot->getChanges();

        $changed = false;

        $text = 'Update ' . $jackpot->name . ' | ';
        $textOriginal = 'Update ' . $jackpot->name . ' | ';

        foreach(['name' => 'Name', 'start_balance' => 'Start Balance', 'pay_sum' => 'Trigger',
                    'percent' => 'Percent', 'view' => 'Status'] AS $column=>$title){

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


        $this->logger->log($text, $textOriginal, $type = 'system', 'jackpot', $jackpot->id);
    }

    public function onDeleteJackpot(DeleteJackpot $event)
    {
        $jackpot = $event->getDeleteJackpot();

        //$this->logger->log('Delete Jackpot / ' . $jackpot->name . ', Shop ' . $jackpot->shop_id, $type = 'system', 'jackpot', $jackpot->id);
    }

    public function template($key, $title, $value){
        $text = '';
        if($key == 'view'){
            $text .= ' ' .$title. ' = ' . ($value ? 'Active' : 'Disabled' ) . ' | ';
        } elseif($key == 'start_balance'){
            $text .= ' ' .$title. ' = ' . (JPG::$values['start_balance'][$value] ? 'Active' : 'Disabled' ) . ' | ';
        } elseif($key == 'pay_sum'){
            $text .= ' ' .$title. ' = ' . (JPG::$values['pay_sum'][$value] ? 'Active' : 'Disabled' ) . ' | ';
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
        $class = 'VanguardLTE\Listeners\JackpotEventsSubscriber';

        $events->listen(NewJackpot::class, "{$class}@onNewJackpot");
        $events->listen(JackpotEdited::class, "{$class}@onJackpotEdited");
        $events->listen(DeleteJackpot::class, "{$class}@onDeleteJackpot");
    }
}
