<?php

namespace VanguardLTE\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VanguardLTE\Game;


class UpdateGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $model;
    protected $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $ids, $data) {
        $this->model = $model;
        $this->ids = $ids;
        $this->data = $data;
		
		
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
		
		//Info($this->data);
		//Info($this->ids);
		
        if( $this->model == 'game' ){
            $models = Game::whereIn('id', $this->ids)->get();
        }


		
        if( $models && is_array($this->data) && count($this->data) > 0 ){
						
            foreach($models AS $model){
                foreach($this->data AS $key=>$value){
					//Info($model->$key);
                    if( $model->$key !== '' ){
						//Info($model->id . ' > ' . $key . ' >> ' . $value);
                        $model->$key = trim($value);
                    } else{
						//Info($model->id . ' > ' . $key . ' >> empty');
					}
                }
				if($model->isDirty()){
					//Info($model->id . ' isDirty ');
					$model->save();
				}
                
            }
        }
    }
}
