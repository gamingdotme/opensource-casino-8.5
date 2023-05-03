<?php

namespace VanguardLTE\Extension;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Arr;
use VanguardLTE\Helpers\UserSystemInfoHelper;
use VanguardLTE\Lib\GeoData;

class CustomDatabaseSessionHandler extends DatabaseSessionHandler {

    protected function performInsert($sessionId, $payload)
    {
        try {

            $data = GeoData::get_data(true, true);

            $data = $data + [
                'user_id' => \Auth::check() ? auth()->user()->id : null,
            ];

            return $this->getQuery()->insert(Arr::set($payload, 'id', $sessionId) + $data);
        } catch (QueryException $e) {
            $this->performUpdate($sessionId, $payload);
        }
    }


}
