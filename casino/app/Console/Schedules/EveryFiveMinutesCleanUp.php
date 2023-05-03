<?php


namespace VanguardLTE\Console\Schedules;


use Carbon\Carbon;
use Illuminate\Support\Str;
use VanguardLTE\Info;
use VanguardLTE\Invite;
use VanguardLTE\Message;
use VanguardLTE\Notification;
use VanguardLTE\Progress;
use VanguardLTE\ProgressUser;
use VanguardLTE\Session;
use VanguardLTE\SMS;
use VanguardLTE\Support\Enum\UserStatus;
use VanguardLTE\Task;
use VanguardLTE\User;

class EveryFiveMinutesCleanUp
{


    public $max_time_in_sec;

    public function __construct($max_time_in_sec=5){
        $this->max_time_in_sec = $max_time_in_sec;
    }


    public function __invoke()
    {
        // TODO: Implement __invoke() method.

        $start = microtime(true);

        Notification::where('created_at', '<', Carbon::now()->subDays(5)->format('Y-m-d H:i:s'))->delete();
        Message::where('status', 1)->delete();
        Session::where('user_id', null)->delete();
        Session::where('user_id', '')->delete();
        Task::where('finished', 1)->delete();

        $infos = Info::get();
        if($infos){
            foreach ($infos AS $info){
                $times = Carbon::now()->diffInDays(Carbon::parse($info->created_at), false);
                if($times < 0 && $info->days > 0 && abs($times) >= $info->days){
                    $info->delete();
                }
            }
        }

        // if demo agent activated to not demo user
        $users = User::withoutGlobalScopes()
            ->where(['status' => UserStatus::ACTIVE, 'role_id' => 5, 'is_demo_agent' => 1])->get();
        if( $users ){
            foreach ($users AS $user){
                $user->update(['is_demo_agent' => 0]);
            }
        }

        // delete demo agents
        $users = User::withoutGlobalScopes()
            ->where(['status' => UserStatus::UNCONFIRMED, 'role_id' => 5, 'is_demo_agent' => 1])
            ->where('created_at', '<', Carbon::now()->subDays(1)->format('Y-m-d H:i:s'))->get();
        if( $users ){
            foreach ($users AS $user){
                $distributors = User::where(['parent_id' => $user->id, 'role_id' => 4])->get();
                if($distributors){
                    foreach ($distributors AS $distributor){
                        if($distributor->rel_shops){
                            foreach ($distributor->rel_shops AS $shop){
                                $shop->shop->delete();
                                Task::create(['category' => 'shop', 'action' => 'delete', 'item_id' => $shop->shop_id, 'user_id' => auth()->user()->id, 'shop_id' => auth()->user()->shop_id]);
                                $usersToDelete = User::whereIn('role_id', [1,2,3])->where('shop_id', $shop->shop_id)->get();
                                if($usersToDelete){
                                    foreach($usersToDelete AS $userDelete){
                                        $userDelete->delete();
                                    }
                                }

                            }
                        }
                        $distributor->delete();
                    }
                }
                $user->delete();
            }
        }


        // delete invited users
        $users = User::where('inviter_id', '>', 0)
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, last_online)<=1')
            ->where('created_at', '<', Carbon::now()->subDays(5)->format('Y-m-d H:i:s'))->get();
        if($users){
            foreach ($users AS $user){
                $user->delete();
            }
        }

        $invites = Invite::where('shop_id', '!=', '0')->get();
        if(count($invites)){
            foreach ($invites AS $invite){
                SMS::where('status', '!=', 'DELIVERED')
                    ->where('created_at', '<', Carbon::now()->subDays($invite->waiting_time)->format('Y-m-d H:i:s'))
                    ->delete();
            }
        }
        $users = User::where('role_id', '!=', 6)->get();
        if($users){
            foreach ($users AS $user){
                $user->update(['auth_token' => Str::random(64)]);
            }
        }


        // decrease progress
        $users = User::where('role_id', 1)->get();
        if(count($users) ){
            foreach ($users AS $user){
                if( $user->rating > 0 && $user->shop && $user->shop->progress_active ){
                    $diffInDays =  Carbon::now()->diffInDays(Carbon::parse($user->last_progress));
                    if($diffInDays > 0){
                        $progress = Progress::where(['shop_id' => $user->shop_id, 'rating' => $user->rating])->first();
                        if($progress){
                            if( $progress->days_active <= $diffInDays ){
                                ProgressUser::where(['user_id' => $user->id, 'rating' => $user->rating])->delete();
                                $user->decrement('rating');
                                $user->update(['last_progress' => Carbon::now()]);
                            }
                        }
                    }
                }

            }
        }


        $time_elapsed_secs = microtime(true) - $start;
        if($time_elapsed_secs > $this->max_time_in_sec){
            Info('------------------');
            Info('EveryFiveMinutesCleanUp');
            Info('exec time ' . $time_elapsed_secs . ' sec');
        }

    }

}
