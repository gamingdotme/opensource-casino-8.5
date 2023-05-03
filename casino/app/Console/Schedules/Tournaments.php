<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use VanguardLTE\Message;
use VanguardLTE\Tournament;
use VanguardLTE\TournamentStat;
use VanguardLTE\User;

class Tournaments {


    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }

    public function __invoke(){

        $start = microtime(true);

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $statuses = Tournament::where('status','!=','completed')->get();
        if(count($statuses)){
            foreach($statuses As $tournament){
                if( $tournament->is_waiting() ){
                    $tournament->update(['status' => 'waiting']);
                } else if( $tournament->is_completed() ){
                    //$tournament->update(['status' => 'completed']);
                } else {
                    $tournament->update(['status' => 'active']);
                }
            }
        }

        $tournaments = Tournament::where('start', '<=', $now)
            ->where('end', '>=', $now)->get();

        if(count($tournaments)){
            foreach($tournaments As $tournament){
                if($tournament->users){
                    foreach($tournament->users AS $bot){
                        $stat = TournamentStat::where(['tournament_id' => $tournament->id, 'user_id' => $bot->id, 'is_bot' => 1])->first();
                        if(!$stat){
                            TournamentStat::create(['tournament_id' => $tournament->id, 'user_id' => $bot->id, 'is_bot' => 1]);
                        }
                    }
                }
                $times = Carbon::now()->diffInMinutes(Carbon::parse($tournament->start), false);
                if( $times != 0 && $times % $tournament->bots_time == 0 ){
                    $steps = explode(' - ', Tournament::$values['bots_step'][$tournament->bots_step]);
                    if($tournament->users){
                        foreach($tournament->users AS $bot){
                            $stat = TournamentStat::where(['tournament_id' => $tournament->id, 'user_id' => $bot->id, 'is_bot' => 1])->first();
                            if(!$stat){
                                $stat = TournamentStat::create(['tournament_id' => $tournament->id, 'user_id' => $bot->id, 'is_bot' => 1]);
                            }
                            if( $stat->points >= $tournament->bots_limit ){
                                continue;
                            }
                            $step = rand($steps[0], $steps[1]);
                            $stat->increment('points', $step);
                        }
                    }
                }
            }
        }

        // second step

        $endeds = Tournament::where('status','!=','completed')->where('end', '<=', $now)->get();
        if(count($endeds)){
            foreach($endeds As $tournament){

                if($tournament->stats){
                    $stats = $tournament->get_ranking();
                    if( isset($stats['prized']) && count($stats['prized'])){
                        foreach ($stats['prized'] AS $stat){
                            $stat['stat']->update(['prize_id' => $stat['prize']->id]);
                            if( !$stat['stat']->is_bot ){
                                $payeer = User::find($stat['stat']->user->parent_id);
                                $stat['stat']->user->addBalance('add', $stat['prize']->prize, $payeer, false, 'tournament', false, $tournament);
                                Message::create(['user_id' => $stat['stat']->user_id, 'type' => 'tournaments', 'value' => $stat['prize']->prize, 'shop_id' => $tournament->shop_id]);
                            }
                        }
                    }

                }
                $tournament->update(['status' => 'completed']);


                // copy tournament
                if($tournament->repeat_days && $tournament->repeat_number){

                    //for($d=1; $d<=$tournament->repeat_number; $d++){

                    $start = Carbon::parse($tournament->start)->addDays($tournament->repeat_days);
                    $end = Carbon::parse($tournament->end)->addDays($tournament->repeat_days);

                    $newTournament = $tournament->replicate();
                    $newTournament->status = 'waiting';
                    $newTournament->start = $start->format('Y-m-d H:i:s');
                    $newTournament->end = $end->format('Y-m-d H:i:s');
                    $newTournament->repeat_days = $tournament->repeat_days;
                    $newTournament->repeat_number = $tournament->repeat_number - 1;

                    if($tournament->image != ''){
                        $custom_file_name = rand(1000,9999).'-'.$tournament->image;
                        $newTournament->image = $custom_file_name;
                        Storage::copy(
                            'public/tournaments/' . $tournament->image,
                            'public/tournaments/' . $custom_file_name
                        );
                    }

                    $newTournament->save();

                    if( $tournament->prizes ){
                        foreach ($tournament->prizes AS $prize){
                            $newPrize = $prize->replicate();
                            $newPrize->tournament_id = $newTournament->id;
                            $newPrize->save();
                        }
                    }

                    if( $tournament->categories ){
                        foreach ($tournament->categories AS $category){
                            $newCategory = $category->replicate();
                            $newCategory->tournament_id = $newTournament->id;
                            $newCategory->save();
                        }
                    }

                    if( $tournament->games ){
                        foreach ($tournament->games AS $game){
                            $newGame = $game->replicate();
                            $newGame->tournament_id = $newTournament->id;
                            $newGame->save();
                        }
                    }

                    if( $tournament->users ){
                        foreach ($tournament->users AS $user){
                            $newUser = $user->replicate();
                            $newUser->tournament_id = $newTournament->id;
                            $newUser->save();
                        }
                    }

                    //}

                    $tournament->repeat_days = NULL;
                    $tournament->repeat_number = NULL;
                    $tournament->save();

                }
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('Tournaments');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }
}