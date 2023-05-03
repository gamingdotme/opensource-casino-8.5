<?php


namespace VanguardLTE\Console\Schedules;


use VanguardLTE\Game;
use VanguardLTE\Task;

class RemoveGamesWithoutFolder
{

    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        $task = Task::where(['finished' => 0, 'category' => 'game', 'action' => 'clear' ])->first();
        if($task){
            $task->update(['finished' => 1]);
            $games = [];
            $folders = scandir( app_path() . '/Games' );
            if( count($folders) ){
                foreach ($folders AS $folder){
                    if($folder != '.' && $folder != '..'){
                        $games[] = $folder;
                    }
                }
            }
            if(count($games)){
                $allGames = Game::whereNotIn('name', $games)->where('shop_id', 0)->pluck('id');

                if(count($allGames)){
                    Game::destroy($allGames);
                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('RemoveGamesWithoutFolder');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}