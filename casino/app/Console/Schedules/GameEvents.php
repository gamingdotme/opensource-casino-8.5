<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Game;
use VanguardLTE\Services\Logging\UserActivity\Activity;
use VanguardLTE\Task;

class GameEvents
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $tasks = Task::where(['finished' => 0, 'category' => 'game', 'action' => 'delete' ])->take(50)->get();
        if(count($tasks)){
            foreach($tasks As $task){
                $task->update(['finished' => 1]);
                Game::destroy($task->item_id);
            }
        }

        $tasks = Task::where(['finished' => 0, 'category' => 'event', 'action' => 'GameEdited' ])->take(50)->get();
        if(count($tasks)){
            foreach($tasks As $task){
                $task->update(['finished' => 1]);
                $games = explode(',', $task->item_id);
                if(count($games)){
                    $games = Game::whereIn('id', $games)->get();
                    foreach($games AS $game){
                        Activity::create([
                            'description' => 'Update Game / ' . $game->name . ' / ' . $task->details,
                            'user_id' => $task->user_id,
                            'ip_address' => $task->ip_address,
                            'user_agent' => $task->user_agent,
                        ]);
                    }
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('GameEvents');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}