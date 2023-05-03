<?php

namespace VanguardLTE\Http\Controllers\Utility;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Model\CrudModel;

class ApiController extends Controller
{
    static function createNewApiKey()
    {
        $payload = [
            'keygen' => hpRandStr(25),
            'shop_id' => auth()->user()->shop_id,
            'status' => 1,
        ];
        $api_key_id = CrudModel::createNewRecord('apis', $payload);
        return $api_key_id;
    }

    static function generateNewApiKey($where)
    {
        $payload = [
            'keygen' => hpRandStr(25)
        ];
        CrudModel::updateRecord('apis', $payload, $where);
    }

    static function deleteApiKey($where)
    {
        CrudModel::deleteRecord('apis', $where);
    }
}
