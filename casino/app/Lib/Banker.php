<?php

namespace VanguardLTE\Lib;

use VanguardLTE\FishBank;
use VanguardLTE\GameBank;

class Banker {

    public static function get_bank($shop_id, $bank){

        if( $bank == 'fish' ){
            $fish = FishBank::where(['shop_id' => $shop_id])->first();
            if($fish){
                return $fish->fish;
            }
        } else {
            $banker = GameBank::where(['shop_id' => $shop_id])->first();
            if($banker){
                if( $bank == 'table' ){
                    $bank = 'table_bank';
                }
                return $banker->$bank;
            }
        }

        return 0;

    }

    public static function get_all_banks($shop_id){

        $fish = FishBank::where(['shop_id' => $shop_id])->first();
        $banker = GameBank::where(['shop_id' => $shop_id])->first();

        if($fish && $banker){
            return [$banker->slots, $banker->bonus, $fish->fish, $banker->table_bank, $banker->little];
        }

        return [0,0,0,0,0];

    }


    public static function update_bank($shop_id, $bank, $value, $type='update'){

        if( $bank == 'fish' ){
            $fish = FishBank::where('shop_id', $shop_id)->first();
            if($fish){
                if( $type == 'update' ){
                    $fish->update(['fish' => $value]);
                }
                if( $type == 'inc' ){
                    $fish->increment('fish', $value);
                }
                if( $type == 'dec' ){
                    $fish->decrement('fish', $value);
                }
            }
        } else {
            $banker = GameBank::where('shop_id', $shop_id)->first();
            if($banker){
                if( $bank == 'table' ){
                    $bank = 'table_bank';
                }
                if( $type == 'update' ){
                    $banker->update([$bank => $value]);
                }
                if( $type == 'inc' ){
                    $banker->increment($bank, $value);
                }
                if( $type == 'dec' ){
                    $banker->decrement($bank, $value);
                }
            }
        }

        return true;

    }
}