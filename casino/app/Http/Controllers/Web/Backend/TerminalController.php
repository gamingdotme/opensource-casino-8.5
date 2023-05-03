<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Model\CrudModel;
use VanguardLTE\Model\UserModel;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{
    // List of terminal
    // Will return all the users those whose role is terminal.
    public function index(Request $request)
    {
        $userId = auth()->user()->shop_id;
        $where = 'w_users.role_id=7';
        $where .= ' AND shop_id= ' . auth()->user()->shop_id;
        $terminals = UserModel::getTerminals($where, 5);
        $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
        $response = [
            'statuses' => $statuses,
            'terminals' => $terminals
        ];
        return view('backend.terminal.list', ['response' => $response]);
    }

    // Create new terminal
    // Create a new record in users table with role terminal.
    public function craeteTerminal(Request $request)
    {
        $formData = $request->input();
        if ($formData['password'] != $formData['cpassword']) {
            return redirect()->back()->withErrors('Password mismatch!');
        }
        $payload = [
            'username' => $formData['username'],
            'shop_id' => auth()->user()->shop_id,
            'role_id' => 7,
            'parent_id' => 1,
            'status' => $formData['status'],
            'password' => $formData['password'],
        ];
        CrudModel::createNewRecord('users', $payload);
        return redirect()->back()->with('success', 'Terminal has been created successfully!');
    }

    // Details of a terminal
    // All the information of a selected terminal
    public function detailsTerminal($id, Request $request)
    {
        $terminal_id = decoded($id);
        $where = 'id=' . $terminal_id;
        $terminal = CrudModel::readData('users', $where, '', 1);
        $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
        $langs = [];
        foreach (glob(resource_path() . '/lang/*', GLOB_ONLYDIR) as $fileinfo) {
            $dirname = basename($fileinfo);
            $langs[$dirname] = $dirname;
        }
        $user_activity = CrudModel::readData('user_activity', 'user_id=' . $terminal_id, 'id DESC');
        $pay_tickets = CrudModel::readData('pay_tickets', 'user_id=' . $terminal_id, 'id DESC');
        $response = [
            'terminal' => $terminal,
            'statuses' => $statuses,
            'langs' => $langs,
            'shop' => auth()->user()->shop,
            'userActivity' => $user_activity,
            'payTickets' => $pay_tickets,
        ];
        //dd($response);
        return view('backend.terminal.details', ['response' => $response]);
    }

    // Update terminal information
    public function terminalUpdate($id, Request $request)
    {
        $terminal_id = decoded($id);
        $formData = $request->input();
        $where = 'id=' . $terminal_id;

        $payload = [
            "username" => $formData['username'],
            "status" => $formData['status'],
            "language" => $formData['language'],
            "password" => $formData['password'],
        ];
        CrudModel::updateRecord('users', $payload, $where);
        return redirect()->back()->with('success', 'Terminal has been updated successfully!');
    }

    // Balance In
    public function balanceAdd(Request $request)
    {
        $formData = $request->input();
        $shop = auth()->user()->shop;
        if ($shop->balance < $formData['amount']) {
            return redirect()->back()->withErrors('Shop has no balance!');
        }
        $user = CrudModel::readData('users', 'id="' . $formData['user_id'] . '"', '', 1);
        if ($user) {
            $payloadUpdate = [
                'balance' => $user->balance + $formData['amount'],
                'count_balance' => $user->balance + $formData['amount'],
                'total_in' => $user->total_in + $formData['amount']
            ];
            CrudModel::updateRecord('users', $payloadUpdate, 'id=' . $user->id);
            return redirect()->back()->with('success', 'Amount has been added successfully!');
        } else {
            return redirect()->back()->withErrors('User not found!');
        }
    }

    // Balance out
    public function balanceOut(Request $request)
    {
        $formData = $request->input();
        $user = CrudModel::readData('users', 'id="' . $formData['user_id'] . '"', '', 1);
        if ($user->balance < $formData['amount']) {
            return redirect()->back()->withErrors('Not enough balance!');
        }
        if ($user) {
            $payloadUpdate = [
                'balance' => $user->balance - $formData['amount'],
                'count_balance' => $user->balance + $formData['amount'],
                'total_out' => $user->total_out + $formData['amount']
            ];
            CrudModel::updateRecord('users', $payloadUpdate, 'id=' . $user->id);
            return redirect()->back()->with('success', 'Amount has been withdraw successfully!');
        } else {
            return redirect()->back()->withErrors('User not found!');
        }
    }

    // All the information of a selected terminal
    public function ajaxPayTickets(Request $request)
    {
        $formData = $request->input();
        $terminal_id = $formData['terminalId'];
        $pay_tickets = CrudModel::readData('pay_tickets', 'user_id=' . $terminal_id, 'id DESC');
        if ($pay_tickets) {
            $response = $pay_tickets;
        } else {
            $response = null;
        }
        return response()->json($response);
    }
}
