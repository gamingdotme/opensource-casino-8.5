<?php

namespace VanguardLTE\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class AtmControler extends Controller
{
    public function atmPing(Request $request)
    {
        $response = [
            'sucsess' => true,
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

    public function activateUser(Request $request)
    {
        dd('AtmControler');
    }

    public function checkBarcodeAsync(Request $request)
    {
        dd('checkBarcodeAsync');
    }

    public function saveBarcodeAsync(Request $request)
    {
        dd('AtmControler');
    }

    public function PendingCashIN(Request $request)
    {
        dd('AtmControler');
    }

    public function CashINAsync(Request $request)
    {
        dd('AtmControler');
    }

    public function forgotPass(Request $request)
    {
        dd('AtmControler');
    }

    public function resetPass(Request $request)
    {
        dd('AtmControler');
    }

    public function checkMagCardAsync(Request $request)
    {
        dd('AtmControler');
    }

    public function checkForPanic(Request $request)
    {
        dd('AtmControler');
    }

    public function pingServer(Request $request)
    {
        dd('AtmControler');
    }

    public function createUser(Request $request)
    {
        dd('AtmControler');
    }

    public function updateRecServer(Request $request)
    {
        dd('AtmControler');
    }

    public function checkServerCreditsAsync(Request $request)
    {
        dd('AtmControler');
    }


    public function createUserAsync(Request $request)
    {
        dd('AtmControler');
    }

    public function createUpdateCode(Request $request)
    {
        dd('AtmControler');
    }
    public function SaveSettings(Request $request)
    {
        dd('AtmControler');
    }
    public function checkSignInAsync(Request $request)
    {
        dd('AtmControler');
    }
    public function checkVoucherAsync(Request $request)
    {
        dd('AtmControler');
    }
    public function createWithdrawCodeAsync(Request $request)
    {
        dd('AtmControler');
    }
    public function CashOUTAsync(Request $request)
    {
        dd('AtmControler');
    }
}
