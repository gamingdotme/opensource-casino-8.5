<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Http\Controllers\Utility\ApiController;
use VanguardLTE\Model\CrudModel;

class AtmController extends Controller
{
    // ATM list data
    // Return the list of atms available for selected shop
    public function index(Request $request)
    {
        $where = 'shop_id=' . auth()->user()->shop_id;
        $atms = CrudModel::readData('atm', $where, '', 1);
        $response = [
            'atms' => $atms,
            'shop' => auth()->user()->shop
        ];
        return view('backend.atm.list', ['response' => $response]);
    }

    // Create New ATM
    // Create a new record in atm table
    public function createNewAtm(Request $request)
    {
        // create new API key
        $api_key_id = ApiController::createNewApiKey();

        // Create new ATM
        $payload = [
            'atm_name' => hpRand(10),
            'shop_id' => auth()->user()->shop_id,
            'api_key_id' => $api_key_id,
        ];
        CrudModel::createNewRecord('atm', $payload);
        return redirect()->back()->with('success', 'ATM has been created successfully!');
    }

    // Generate New API key
    public function resetAtm()
    {
        $where = 'shop_id=' . auth()->user()->shop_id;
        $payload = [
            'atm_in' => "",
            'atm_out' => "",
            'atm_recycle' => "",
            'atm_rec_5' => "",
            'atm_rec_10' => "",
            'atm_rec_20' => "",
            'atm_rec_50' => "",
            'atm_rec_100' => "",
            'atm_rec_200' => "",
        ];
        CrudModel::updateRecord('atm', $payload, $where);
        return redirect()->back()->with('success', 'ATM has been reset successfully!');
    }

    // Generate New API key
    public function newApiKey($api_id)
    {
        $api_id = decoded($api_id);
        $where = 'id=' . $api_id;
        ApiController::generateNewApiKey($where);
        return redirect()->back()->with('success', 'A new API key has been created successfully!');
    }

    // Delete ATM
    public function deleteATM($id, $api_id)
    {
        // delete record from atm
        $where = 'id=' . decoded($id);
        CrudModel::deleteRecord('atm', $where);

        // delete record from apis
        $where = 'id=' . decoded($api_id);
        ApiController::deleteApiKey($where);

        return redirect()->back()->with('success', 'ATM has been deleted successfully!');
    }

    // Update status of ATM
    public function statusUpdate($status)
    {
        $where = 'shop_id=' . auth()->user()->shop_id;
        $payload = [
            'atm_status' => decoded($status)
        ];
        CrudModel::updateRecord('atm', $payload, $where);

        return redirect()->back()->with('success', 'ATM has been updated successfully!');
    }
}
