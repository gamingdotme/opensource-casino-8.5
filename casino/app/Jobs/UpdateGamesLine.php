<?php

namespace VanguardLTE\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VanguardLTE\Game;
use VanguardLTE\Task;


class UpdateGamesLine implements ShouldQueue
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
    public function __construct($ids, $data) {
        $this->ids = $ids;
        $this->data = $data;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){

        $models = Game::whereIn('id', $this->ids)->get();

        if( $models && is_array($this->data) && count($this->data) > 0 ){

            foreach($models AS $model){

                $to_save = [];

                // line_spin
                if( isset($this->data['line_spin']) && count($this->data['line_spin']) ){
                    foreach($this->data['line_spin'] AS $line_index=>$line_data){
                        foreach($line_data AS $inner_index=>$inner_value){
                            if( $inner_value == NULL ){
                                $to_save['lines_percent_config_spin'][$line_index][$inner_index] = $model->get_line_value($model->lines_percent_config_spin, $line_index, $inner_index, true);
                            } else{
                                $to_save['lines_percent_config_spin'][$line_index][$inner_index] = $inner_value;
                            }
                        }
                    }
                }

                // line_spin_bonus
                if( isset($this->data['line_spin_bonus']) && count($this->data['line_spin_bonus']) ){
                    foreach($this->data['line_spin_bonus'] AS $line_index=>$line_data){
                        foreach($line_data AS $inner_index=>$inner_value){
                            if( $inner_value == NULL ){
                                $to_save['lines_percent_config_spin_bonus'][$line_index][$inner_index] = $model->get_line_value($model->lines_percent_config_spin_bonus, $line_index, $inner_index, true);
                            } else{
                                $to_save['lines_percent_config_spin_bonus'][$line_index][$inner_index] = $inner_value;
                            }
                        }
                    }
                }

                // line_bonus
                if( isset($this->data['line_bonus']) && count($this->data['line_bonus']) ){
                    foreach($this->data['line_bonus'] AS $line_index=>$line_data){
                        foreach($line_data AS $inner_index=>$inner_value){
                            if( $inner_value == NULL ){
                                $to_save['lines_percent_config_bonus'][$line_index][$inner_index] = $model->get_line_value($model->lines_percent_config_bonus, $line_index, $inner_index, true);
                            } else{
                                $to_save['lines_percent_config_bonus'][$line_index][$inner_index] = $inner_value;
                            }
                        }
                    }
                }

                // line_bonus_bonus
                if( isset($this->data['line_bonus_bonus']) && count($this->data['line_bonus_bonus']) ){
                    foreach($this->data['line_bonus_bonus'] AS $line_index=>$line_data){
                        foreach($line_data AS $inner_index=>$inner_value){
                            if( $inner_value == NULL ){
                                $to_save['lines_percent_config_bonus_bonus'][$line_index][$inner_index] = $model->get_line_value($model->lines_percent_config_bonus_bonus, $line_index, $inner_index, true);
                            } else{
                                $to_save['lines_percent_config_bonus_bonus'][$line_index][$inner_index] = $inner_value;
                            }
                        }
                    }
                }


                if( count($to_save) ){
                    if( isset($to_save['lines_percent_config_spin'])){
                        $model->lines_percent_config_spin = json_encode($to_save['lines_percent_config_spin']);
                    }
                    if( isset($to_save['lines_percent_config_spin_bonus'])){
                        $model->lines_percent_config_spin_bonus = json_encode($to_save['lines_percent_config_spin_bonus']);
                    }
                    if( isset($to_save['lines_percent_config_bonus'])){
                        $model->lines_percent_config_bonus = json_encode($to_save['lines_percent_config_bonus']);
                    }
                    if( isset($to_save['lines_percent_config_bonus_bonus'])){
                        $model->lines_percent_config_bonus_bonus = json_encode($to_save['lines_percent_config_bonus_bonus']);
                    }
                }



                $model->save();



            }


        }
    }
}
