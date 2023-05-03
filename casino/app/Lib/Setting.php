<?php

namespace VanguardLTE\Lib;

use VanguardLTE\PaymentSetting;

class Setting {

    public static function get_value($system, $field, $shop_id){
        $setting = PaymentSetting::where(['system' => $system, 'field' => $field, 'shop_id' => $shop_id])->first();
        if($setting){
            return $setting->value;
        }
        return '';
    }

    public static function set_value($system, $field, $value, $shop_id){
        $setting = PaymentSetting::where(['system' => $system, 'field' => $field, 'shop_id' => $shop_id])->first();
        if($setting){
            $setting->update(['value' => $value]);
        } else{
            PaymentSetting::create(['system' => $system, 'field' => $field, 'shop_id' => $shop_id, 'value' => $value]);
        }
    }

    public static function is_available($system, $shop_id){

        foreach(config('payments.'.$system.'.required') AS $field){
            $setting = PaymentSetting::where(['system' => $system, 'field' => $field, 'shop_id' => $shop_id])->first();
            if( !$setting ){
                return false;
            }
            if(!$setting->value){
                return false;
            }
        }

        return true;
    }
}