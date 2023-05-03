<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class CreditController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasRole([
                'admin', 
                'agent'
            ]) ) 
            {
                abort(403);
            }
            $credits = \VanguardLTE\Credit::orderBy('credit', 'desc')->get();
            return view('backend.credits.list', compact('credits'));
        }
        public function create()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            return view('backend.credits.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            $data = $request->all();
            \VanguardLTE\Credit::create($data);
            return redirect()->route('backend.credit.list')->withSuccess(trans('app.credit_created'));
        }
        public function edit($credit)
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            $credit = \VanguardLTE\Credit::where(['id' => $credit])->firstOrFail();
            return view('backend.credits.edit', compact('credit'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Credit $credit)
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            $data = $request->only([
                'price', 
                'credit'
            ]);
            \VanguardLTE\Credit::where('id', $credit->id)->update($data);
            return redirect()->route('backend.credit.list')->withSuccess(trans('app.credit_updated'));
        }
        public function buy(\VanguardLTE\Credit $credit)
        {
            if( !auth()->user()->hasRole('agent') ) 
            {
                abort(403);
            }
            $interkassa = (settings('payment_interkassa') ? \VanguardLTE\Lib\Interkassa::get_systems(auth()->user()->id, 0) : '');
            return view('backend.credits.buy', compact('credit', 'interkassa'));
        }
        public function payment(\Illuminate\Http\Request $request, \VanguardLTE\Credit $credit, $system)
        {
            $amount = number_format(floatval($credit->price), 2, '.', '');
            if( strripos($system, 'interkassa') !== false ) 
            {
                if( !settings('payment_interkassa') ) 
                {
                    return redirect()->back()->withErrors(trans('app.system_is_not_available'));
                }
                if( !\VanguardLTE\Lib\Setting::is_available('interkassa', 0) ) 
                {
                    return redirect()->back()->withErrors([__('app.something_went_wrong')]);
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'credit_id' => $credit->id, 
                    'currency' => settings('default_currency'), 
                    'system' => 'interkassa', 
                    'shop_id' => 0
                ]);
                $form = \VanguardLTE\Lib\Interkassa::get_form(auth()->user()->id, 0, $payment->id, $amount, $system);
                if( isset($form['success']) ) 
                {
                    $data = $form['form'];
                    if( is_array($data) ) 
                    {
                        $data['fields'] = $data['parameters'];
                        unset($data['parameters']);
                    }
                    return view('backend.credits.payment', compact('data'));
                }
                return redirect()->route('backend.credit.list')->withErrors(__('app.something_went_wrong'));
            }
            if( $system == 'coinbase' ) 
            {
                if( !settings('payment_coinbase') ) 
                {
                    return redirect()->back()->withErrors(trans('app.system_is_not_available'));
                }
                if( !\VanguardLTE\Lib\Setting::is_available('coinbase', 0) ) 
                {
                    return redirect()->back()->withErrors([__('app.something_went_wrong')]);
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'currency' => settings('default_currency'), 
                    'credit_id' => $credit->id, 
                    'system' => 'coinbase', 
                    'shop_id' => 0
                ]);
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-CC-Api-Key' => \VanguardLTE\Lib\Setting::get_value('coinbase', 'api_key', 0), 
                    'X-CC-Version' => '2018-03-22'
                ])->post('https://api.commerce.coinbase.com/charges', [
                    'name' => 'Payment ID #' . $payment->id, 
                    'description' => 'Account replenishment for a client #' . auth()->user()->id, 
                    'local_price' => [
                        'amount' => $amount, 
                        'currency' => settings('default_currency')
                    ], 
                    'pricing_type' => 'fixed_price', 
                    'metadata' => ['payment_id' => $payment->id]
                ]);
                if( isset($response['data']['hosted_url']) ) 
                {
                    return redirect()->to($response['data']['hosted_url']);
                }
                else
                {
                    return redirect()->route('backend.credit.list')->withErrors([__('app.something_went_wrong')]);
                }
            }
            if( $system == 'btcpayserver' ) 
            {
                if( !settings('payment_btcpayserver') ) 
                {
                    return redirect()->back()->withErrors(trans('app.system_is_not_available'));
                }
                if( !\VanguardLTE\Lib\Setting::is_available('btcpayserver', 0) ) 
                {
                    return redirect()->back()->withErrors([__('app.something_went_wrong')]);
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'credit_id' => $credit->id, 
                    'currency' => settings('default_currency'), 
                    'system' => 'btcpayserver', 
                    'shop_id' => 0
                ]);
                $data = [
                    'method' => 'POST', 
                    'action' => \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'server', 0) . '/api/v1/invoices', 
                    'charset' => 'UTF-8', 
                    'fields' => [
                        'storeId' => \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'store_id', 0), 
                        'orderId' => $payment->id, 
                        'price' => $amount, 
                        'currency' => settings('default_currency'), 
                        'checkoutDesc' => 'Account replenishment for a client #' . auth()->user()->id, 
                        'serverIpn' => route('payment.btcpayserver.result'), 
                        'browserRedirect' => route('payment.btcpayserver.redirect')
                    ]
                ];
                return view('backend.credits.payment', compact('data'));
            }
            return redirect()->route('backend.credit.list')->withErrors([__('app.something_went_wrong')]);
        }
        public function delete(\VanguardLTE\Credit $credit)
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            \VanguardLTE\Credit::where('id', $credit->id)->delete();
            return redirect()->route('backend.credit.list')->withSuccess(trans('app.credit_deleted'));
        }
        public function security()
        {
        }
    }

}
