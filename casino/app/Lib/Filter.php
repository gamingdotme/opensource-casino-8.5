<?php
/**
 * Created by PhpStorm.
 * User: Omen
 * Date: 07.09.2019
 * Time: 17:15
 */

namespace VanguardLTE\Lib;


class Filter {

    public static function country_filtered($user, $country=false){

        if(!settings('country_check') || !settings('blocked_countries')){
            return false;
        }
        if(!is_array(settings('blocked_countries'))){
            return false;
        }
        if(!count(settings('blocked_countries'))){
            return false;
        }
        if( !$user ){
            return false;
        }
        if( $user->hasRole('admin') ){
            return false;
        }
        if(!$country){
            $country = $user->country;
        }

        if( $country != '' && in_array($country, settings('blocked_countries')) ){
            return true;
        }

        return false;
    }

    public static function domain_filtered($email){

        if(!settings('domain_check') || !settings('blocked_domains')){
            return false;
        }
        if(!is_array(settings('blocked_domains'))){
            return false;
        }
        if(!count(settings('blocked_domains'))){
            return false;
        }

        foreach (settings('blocked_domains') AS $domain){
            if( preg_match('/'. $domain .'$/', $email)){
                return [
                    'success' => true,
                    'domain' => $domain
                ];
            }
        }

        return false;
    }

    public static function phone_filtered($phone){

        if(!settings('phone_prefix_check') || !settings('blocked_phone_prefixes')){
            return false;
        }
        if(!is_array(settings('blocked_phone_prefixes'))){
            return false;
        }
        if(!count(settings('blocked_phone_prefixes'))){
            return false;
        }

        foreach (settings('blocked_phone_prefixes') AS $prefix){
            $prefix = preg_replace('/[^0-9]/', '', $prefix);
            if( $phone != '' && strpos($phone, $prefix) === 0){
                return true;
            }
        }

        return false;
    }
}