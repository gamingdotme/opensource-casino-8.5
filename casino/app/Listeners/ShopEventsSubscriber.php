<?php

namespace VanguardLTE\Listeners;

use VanguardLTE\Activity;
use VanguardLTE\Events\Shop\ShopEdited;
use VanguardLTE\Services\Logging\UserActivity\Logger;

class ShopEventsSubscriber
{

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onShopEdit(ShopEdited $event)
    {
        $shop = $event->getEditedshop();
        $original = $shop->getOriginal();
        $changes = $shop->getChanges();

        $changed = false;

        $text = 'Update Shop ' . $shop->id . ' |';
        $textOriginal = 'Original Shop ' . $shop->id . ' |';

        foreach([
            'name' => 'Title', 'percent' => 'Percent', 'frontend' => 'Frontend',
            'orderby' => 'Order', 'currency' => 'Currency', 'access' => 'Access',
            'country' => 'Country', 'os' => 'OS', 'device' => 'Device', 'is_blocked' => 'Status'] AS $column=>$title){

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

        /*
        foreach(['name', 'percent', 'frontend', 'orderby', 'currency', 'access',
                    'country', 'os', 'device', 'is_blocked',] AS $column){
            switch ($column){
                case 'name':
                    $textOriginal .= ' Title = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Title = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Title = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'percent':
                    $textOriginal .= ' Percent = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Percent = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Percent = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'frontend':
                    $textOriginal .= ' Frontend = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Frontend = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Frontend = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'orderby':
                    $textOriginal .= ' Order = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Order = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Order = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'currency':
                    $textOriginal .= ' Currency = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Currency = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Currency = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'access':
                    $textOriginal .= ' Access = ' . ($original[$column] ? 'Yes' : 'No' ). ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Access = ' .($changes[$column] ? 'Yes' : 'No' ) . ' | ';
                    } else{
                        $text .= ' Access = ' . ($original[$column] ? 'Yes' : 'No' ) . ' | ';
                    }
                    break;
                case 'is_blocked':
                    $textOriginal .= ' Status = ' . ($original[$column] ? 'Unblock' : 'Block' );
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Status = ' . ($changes[$column] ? 'Unblock' : 'Block' );
                    } else{
                        $text .= ' Status = ' . ($original[$column] ? 'Unblock' : 'Block' );
                    }
                    break;
                case 'country':
                    $textOriginal .= ' Country = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Country = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Country = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'os':
                    $textOriginal .= ' OS = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' OS = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' OS = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'device':
                    $textOriginal .= ' Device = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Device = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Device = ' . $original[$column] . ' | ';
                    }
                    break;
            }

        }
        */

        if(!$changed){
            return;
        }

        $this->logger->log($text, $textOriginal, $type = 'system', 'shop', $shop->id);
    }

    public function template($key, $title, $value){
        $text = '';
        if($key == 'access'){
            $text .= ' ' .$title. ' = ' . ($value ? 'Yes' : 'No' ) . ' | ';
        } elseif( $key == 'is_blocked'){
            $text .= ' ' . $title. ' = ' . ($value ? 'Block' : 'Unblock' ) . ' | ';
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
        $class = 'VanguardLTE\Listeners\ShopEventsSubscriber';

        $events->listen(ShopEdited::class, "{$class}@onShopEdit");
    }
}
