<?php

namespace VanguardLTE\Lib;


use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Exception\IpAddressNotFoundException;
use VanguardLTE\Helpers\UserSystemInfoHelper;

class GeoData {

    public static function get_data($ip=false, $user_agenta=false){

        $data = [
            'country' => '',
            'city' => '',
            'os' => UserSystemInfoHelper::get_os(),
            'device' => UserSystemInfoHelper::get_device(),
            'browser' => UserSystemInfoHelper::get_browsers()
        ];

        if($ip){
            $data = $data + ['ip_address' => UserSystemInfoHelper::get_ip()];
        }

        if($user_agenta){
            $data = $data + ['user_agent' => substr((string) request()->header('User-Agent'), 0, 500)];

        }

        if( UserSystemInfoHelper::get_ip() == 'UNKNOWN'){
            return $data;
        }

        $reader = new Reader(storage_path() . '/app/GeoIP2-City_20201006/GeoIP2-City.mmdb');

        try {
            $record = $reader->city(UserSystemInfoHelper::get_ip());
            if (isset($record->country->name)) {
                $data['country'] = $record->country->name;
            }
            if (isset($record->city->name)) {
                $data['city'] = $record->city->name;
            }
        } catch (IpAddressNotFoundException $e){
            $data['country'] = 'Unknown';
            $data['city'] = 'Unknown';
        } catch (AddressNotFoundException $e) {
            $data['country'] = 'Unknown';
            $data['city'] = 'Unknown';
        }

        return $data;
    }

}