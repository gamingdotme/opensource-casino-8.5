<?php

namespace VanguardLTE\Listeners;

use VanguardLTE\Activity;
use VanguardLTE\Category;
use VanguardLTE\Events\Game\NewGame;
use VanguardLTE\Events\Game\GameEdited;
use VanguardLTE\Events\Game\DeleteGame;
use VanguardLTE\Events\User\UserEventContract;
use VanguardLTE\JPG;
use VanguardLTE\Services\Logging\UserActivity\Logger;

class GameEventsSubscriber
{
    /**
     * @var UserActivityLogger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onNewGame(NewGame $event)
    {
        $game = $event->getNewGame();

        //$this->logger->log('New Game / ' . $game->name . ', Shop ' . $game->shop_id, $type = 'system', 'game', $game->id);

    }

    public function onGameEdited(GameEdited $event)
    {
        $game = $event->getEditedGame();
        $original = $game->getOriginal();
        $changes = $game->getChanges();

        $changed = false;

        $text = 'Update Game ' . $game->id . ' | ';
        $textOriginal = 'Update Game ' . $game->id . ' | ';

        foreach(['title', 'category_temp', 'bet', 'denomination', 'gamebank', 'jpg_id', 'label', 'view'] AS $column){
            switch ($column){
                case 'title':
                    $textOriginal .= ' Title = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Title = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Title = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'bet':
                    $textOriginal .= ' Bet = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Bet = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Bet = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'category_temp':
                    $categories = [];
                    if($original[$column]){
                        $categories = Category::whereIn('id', explode(',', $original[$column]))->pluck('title');
                        if( count($categories)){
                            $categories = $categories->toArray();
                        }
                    }
                    $textOriginal .= ' Categories = ' . (count($categories) ? implode(',', $categories) : '') . ' | ';
                    if( isset($changes[$column]) ){
                        $categories_changes = [];
                        if($changes[$column]){
                            $categories_changes = Category::whereIn('id', explode(',', $changes[$column]))->pluck('title');
                            if( count($categories_changes)){
                                $categories_changes = $categories_changes->toArray();
                            }
                        }
                        $changed = true;
                        $text .= ' Categories = ' . (count($categories_changes) ? implode(',', $categories_changes) : '') . ' | ';
                    } else{
                        $text .= ' Categories = ' . (count($categories) ? implode(',', $categories) : '') . ' | ';
                    }
                    break;
                case 'denomination':
                    $textOriginal .= ' Denomination = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Denomination = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Denomination = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'gamebank':
                    $textOriginal .= ' Bank = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Bank = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Bank = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'jpg_id':
                    $jpg = '';
                    if($original[$column]){
                        $jackpot = JPG::find($original[$column]);
                        if($jackpot){
                            $jpg = $jackpot->name;
                        }
                    }
                    $textOriginal .= ' JP Game = ' . $jpg . ' | ';
                    if( array_key_exists($column, $changes) ){
                        $jpg_orig = '';
                        $jackpot = JPG::find($changes[$column]);
                        if($jackpot){
                            $jpg_orig = $jackpot->name;
                        }
                        $changed = true;
                        $text .= ' JP Game = ' . $jpg_orig . ' | ';
                    } else{
                        $text .= ' JP Game = ' . $jpg . ' | ';
                    }
                    break;
                case 'label':
                    $textOriginal .= ' Label = ' . $original[$column] . ' | ';
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' Label = ' . $changes[$column] . ' | ';
                    } else{
                        $text .= ' Label = ' . $original[$column] . ' | ';
                    }
                    break;
                case 'view':
                    $textOriginal .= ' View = ' . ($original[$column] ? 'Active' : 'Disabled' );
                    if( isset($changes[$column]) ){
                        $changed = true;
                        $text .= ' View = ' . ($original[$column] ? 'Active' : 'Disabled' );
                    } else{
                        $text .= ' View = ' . ($original[$column] ? 'Active' : 'Disabled' ) ;
                    }
                    break;
            }

        }

        if(!$changed){
            return;
        }

        

        $this->logger->log($text, $textOriginal, $type = 'system', 'game', $game->id, $game->shop_id);
    }

    public function onDeleteGame(DeleteGame $event)
    {
        $game = $event->getDeleteGame();
        //$this->logger->log('Delete Game / ' . $game->name . ', Shop ' . $game->shop_id, $type = 'system', 'game', $game->id);
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $class = 'VanguardLTE\Listeners\GameEventsSubscriber';

        $events->listen(NewGame::class, "{$class}@onNewGame");
        $events->listen(GameEdited::class, "{$class}@onGameEdited");
        $events->listen(DeleteGame::class, "{$class}@onDeleteGame");
    }
}
