<?php

namespace VanguardLTE\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Model\CrudModel;

class CreditController extends Controller
{
    // deposit amount to user
    public function creditsDeposit(Request $request)
    {
        $payload = $request->json()->all();
        if ($payload['userhash'] == "") {
            $response = [
                'status' => false,
                'msg' => 'userhash should not be blank!'
            ];
            return response()->json($response);
        }
        $user = CrudModel::readData('users', 'auth_token="' . $payload['userhash'] . '"', '', 1);
        if ($user) {
            $payloadUpdate = [
                'balance' => $user->balance + $payload['amount'],
                'count_balance' => $user->balance + $payload['amount'],
                'total_in' => $user->total_in + $payload['amount']
            ];
            CrudModel::updateRecord('users', $payloadUpdate, 'id=' . $user->id);
            $response = [
                'status' => true,
                'msg' => 'credit has been deposited successfully!'
            ];
        } else {
            $response = [
                'status' => false,
                'msg' => 'user not found!'
            ];
        }
        return response()->json($response);
    }

    // withdraw some amount from user's account balance
    public function creditsWithdraw(Request $request)
    {
        $payload = $request->json()->all();
        if ($payload['userhash'] == "") {
            $response = [
                'status' => false,
                'msg' => 'userhash should not be blank!'
            ];
            return response()->json($response);
        }
        $user = CrudModel::readData('users', 'auth_token="' . $payload['userhash'] . '"', '', 1);
        if ($user) {
            $payloadUpdate = [
                'balance' => $user->balance - $payload['amount'],
                'count_balance' => $user->balance + $payload['amount'],
                'total_out' => $user->total_out + $payload['amount']
            ];
            CrudModel::updateRecord('users', $payloadUpdate, 'id=' . $user->id);
            $response = [
                'status' => true,
                'msg' => 'Amount has been withdrawn successfully!'
            ];
        } else {
            $response = [
                'status' => false,
                'msg' => 'user not found!'
            ];
        }
        return response()->json($response);
    }

    // withdraw available balance to get same in out 
    public function creditsWithdrawAndCashOut(Request $request)
    {
        $payload = $request->json()->all();
        if ($payload['userhash'] == "") {
            $response = [
                'status' => false,
                'msg' => 'userhash should not be blank!'
            ];
            return response()->json($response);
        }
        $user = CrudModel::readData('users', 'auth_token="' . $payload['userhash'] . '"', '', 1);
        if ($user) {
            $payloadUpdate = [
                'balance' => 0,
                'count_balance' => 0,
                'total_out' => $user->total_out + $payload['amount']
            ];
            CrudModel::updateRecord('users', $payloadUpdate, 'id=' . $user->id);
            $response = [
                'status' => true,
                'msg' => 'All amount has been withdrawn successfully!'
            ];
        } else {
            $response = [
                'status' => false,
                'msg' => 'user not found!'
            ];
        }
        return response()->json($response);
    }

    // Payout Ticket
    public function payoutTicket(Request $request)
    {
        $payload = $request->json()->all();
        if ($payload['userhash'] == "") {
            $response = [
                'status' => false,
                'msg' => 'userhash should not be blank!'
            ];
            return response()->json($response);
        }
        $user = CrudModel::readData('users', 'auth_token="' . $payload['userhash'] . '"', '', 1);
        if ($user) {
            $payloadInsert = [
                'user_id' => $user->id,
                'ticket_amount' => $payload['amount'],
                'ticket_pin' => hpRand((13)),
                'ticket_status' => 1,
            ];
            CrudModel::createNewRecord('pay_tickets', $payloadInsert);
            $response = [
                'status' => true,
                'msg' => 'Payout ticket has been generated successfully!',
                'payload' => $payloadInsert
            ];
        } else {
            $response = [
                'status' => false,
                'msg' => 'user not found!'
            ];
        }
        return response()->json($response);
    }
}
