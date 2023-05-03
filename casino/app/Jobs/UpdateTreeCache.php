<?php

namespace VanguardLTE\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use VanguardLTE\Game;
use VanguardLTE\User;


class UpdateTreeCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids) {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        if( count($this->ids) ){
            $members = User::whereIn('id', $this->ids)->where('role_id', '>', 3)->get();
            if(count($members)){
                foreach($members AS $member){
                    $tree = [];
                    $users = User::where('id', $member->id)->get();
                    if( $member->hasRole('admin') ){
                        $users = User::where('role_id', 5)->get();
                    }
                    if( $member->hasRole(['admin','agent']) ){
                        $tree['agents'] = [];
                    } else{
                        $tree['distributors'] = [];
                    }
                    foreach ($users AS $user){
                        if($user->hasRole('agent')){
                            $tree['agents'][$user->id] = [
                                'href' => route('backend.user.edit', ['user' => $user->id], false),
                                'text' => $user->username ?: trans('app.n_a'),
                                'balance' => $user->balance,
                                'rowspan' => $user->getRowspan(),
                                'distributors' => []
                            ];
                            if( $distributors = $user->getInnerUsers() ){
                                foreach($distributors AS $distributor){
                                    $tree['agents'][$user->id]['distributors'][$distributor->id] = $distributor->distributor();
                                }
                            }
                        }
                        if($user->hasRole('distributor')){
                            $tree['distributor'] = $user->distributor();
                        }
                    }
                    //Info($tree);
                    Cache::put('tree:'.$member->id, $tree, 11*60);
                }
            }
        }
    }
}
