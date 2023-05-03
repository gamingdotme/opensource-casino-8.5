<?php

namespace VanguardLTE\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class AtmController extends Controller
{
    public function index(Request $request)
    {
        $payload = $request->json()->all();
        $method = $payload['controller'] . ucwords($payload['action']);
        return $this->$method($request);
    }

    function atmPing($request)
    {
        $response = [
            'success' => "true",
            'data' =>
            [
                'atm_id' => 'ide2WtBxo8sC7M6yF$#@ImBx',
                'atm_name' => '',
                'atm_parent_id' => '5538',
                'atm_in' => '0',
                'atm_out' => '0',
                'atm_rec' => '0',
                'atm_rec5' => '0',
                'atm_rec10' => '0',
                'atm_rec20' => '0',
                'atm_rec50' => '0',
                'atm_rec100' => '0',
                'atm_rec200' => '0',
                'atm_rec500' => '0',
                'atm_enabled' => '1',
                'atm_emptyrecycle' => '0',
                'atm_forceupdate' => '0',
            ]
        ];
        return response()->json($response);
    }

    public function atmreadterminals(Request $request)
    {
        $response = [
            'success' => "true",
            'data' =>
            [
                [
                    "id" => "5540",
                    "name" => "Math",
                    "terminal" => "Mestlux",
                    "score" => "9500"
                ],
                [
                    "id" => "5540",
                    "name" => "Math",
                    "terminal" => "Mestlux",
                    "score" => "8000"
                ]
            ]
        ];
        return response()->json($response);
    }

    public function playerReadcredits(Request $request)
    {
        $response = [
            'success' => "true",
            'data' =>
            [
                "player_id" => "5540",
                "player_name" => "",
                "player_score" => "9500"
            ]
        ];
        return response()->json($response);
    }
}
