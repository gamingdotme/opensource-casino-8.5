<?php 
namespace VanguardLTE\Http\Controllers\Api\Profile
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class DetailsController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index()
        {
            return $this->respondWithItem(auth()->user(), new \VanguardLTE\Transformers\UserTransformer());
        }
        public function update(\VanguardLTE\Http\Requests\User\UpdateProfileDetailsRequest $request, \VanguardLTE\Repositories\User\UserRepository $users)
        {
            $user = $request->user();
            $data = collect($request->all());
            $data = $data->only([
                'username', 
                'language', 
                'password'
            ])->toArray();
            if( isset($data['language']) ) 
            {
                $data['language'] = mb_strtolower($data['language']);
            }
            if( !$data['password'] ) 
            {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            if( $request->token ) 
            {
                if( $users->findByConfirmationToken($request->token) ) 
                {
                    $users->update($user->id, [
                        'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE, 
                        'confirmation_token' => null
                    ]);
                }
                else
                {
                    return $this->errorWrongArgs('Confirmation Token not finded');
                }
            }
            $request->validate(['username' => 'required|unique:users,username,' . $user->id]);
            $user = $users->update($user->id, $data);
            event(new \VanguardLTE\Events\User\UpdatedProfileDetails());
            return $this->respondWithItem($user, new \VanguardLTE\Transformers\UserTransformer());
        }
        public function refunds(\VanguardLTE\Http\Requests\User\UpdateProfileDetailsRequest $request, \VanguardLTE\Repositories\User\UserRepository $users)
        {
            $user = $request->user();
            if( !auth()->user()->hasRole('user') ) 
            {
                return $this->errorWrongArgs(trans('app.only_for_users'));
            }
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            $sum = floatval($user->refunds);
            $refund = false;
            if( $shop && $shop->progress_active ) 
            {
                $refund = \VanguardLTE\Progress::where([
                    'shop_id' => $user->shop_id, 
                    'rating' => $user->rating
                ])->first();
            }
            if( $sum ) 
            {
                if( $refund && $refund->min_balance < $user->balance ) 
                {
                    return response()->json([
                        'fail' => 'fail', 
                        'value' => 0, 
                        'balance' => $user->balance, 
                        'text' => 'Min Balance "' . $refund->min_balance . '"'
                    ], 200);
                }
                $result = $user->addBalance('add', $sum, $user->referral, false, 'refund', false, $refund);
                $result = json_decode($result, true);
                if( isset($result['status']) && $result['status'] == 'error' ) 
                {
                    return response()->json([
                        'fail' => 'fail', 
                        'value' => 0, 
                        'balance' => $user->balance, 
                        'text' => $result['message']
                    ], 200);
                }
                event(new \VanguardLTE\Events\User\MoneyIn($user, $sum));
                return response()->json([
                    'success' => 'success', 
                    'value' => number_format($sum, 2, '.', ''), 
                    'balance' => number_format($user->balance, 2, '.', ''), 
                    'refunds' => number_format($user->refunds, 2, '.', ''), 
                    'currency' => $shop->currency
                ], 200);
            }
            return response()->json([
                'success' => 'success', 
                'value' => 0, 
                'balance' => number_format($user->balance, 2, '.', ''), 
                'currency' => $shop->currency
            ], 200);
        }
        public function check(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasRole('user') ) 
            {
                return $this->errorWrongArgs(trans('app.only_for_users'));
            }
            if( !settings('payment_pin') ) 
            {
                return $this->errorWrongArgs('System is not available');
            }
            $user = \VanguardLTE\User::find(auth()->user()->id);
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            if( !$request->pincode ) 
            {
                return $this->errorWrongArgs('Please enter pincode');
            }
            $pincode = \VanguardLTE\Pincode::where([
                'code' => $request->pincode, 
                'shop_id' => auth()->user()->shop_id
            ])->first();
            if( !$pincode ) 
            {
                return $this->errorWrongArgs('Pincode not exist');
            }
            if( !$pincode->status ) 
            {
                return $this->errorWrongArgs('Wrong Pincode');
            }
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => auth()->user()->shop_id, 
                'end_date' => null
            ])->first();
            if( !$open_shift ) 
            {
                return $this->errorWrongArgs(trans('app.shift_not_opened'));
            }
            if( $shop->balance < $pincode->nominal ) 
            {
                return $this->errorWrongArgs(trans('app.not_enough_money_in_the_shop', [
                    'name' => $shop->name, 
                    'balance' => $shop->balance
                ]));
            }
            $shop->decrement('balance', $pincode->nominal);
            $user->update([
                'balance' => $user->balance + $pincode->nominal, 
                'count_balance' => $user->count_balance + $pincode->nominal, 
                'refunds' => $user->refunds + \VanguardLTE\Lib\Functions::refunds($pincode->nominal, $user->shop_id, $user->rating), 
                'total_in' => $user->total_in + $pincode->nominal
            ]);
            $open_shift->increment('balance_out', abs($pincode->nominal));
            $open_shift->increment('money_in', abs($pincode->nominal));
            $open_shift->increment('transfers');
            event(new \VanguardLTE\Events\User\MoneyIn($user, $pincode->nominal));
            \VanguardLTE\Statistic::create([
                'user_id' => auth()->user()->id, 
                'type' => 'add', 
                'payeer_id' => auth()->user()->parent_id, 
                'sum' => abs($pincode->nominal), 
                'system' => 'pincode', 
                'shop_id' => $user->shop_id, 
                'item_id' => $pincode->code, 
                'title' => 'PIN ' . $pincode->code
            ]);
            $pincode->delete();
            return $this->setStatusCode(201)->respondWithArray(['success' => true]);
        }
        public function sms(\Illuminate\Http\Request $request)
        {
            if( !(auth()->user()->shop && auth()->user()->shop->invite_active) ) 
            {
                return $this->errorWrongArgs(trans('app.no_permission'));
            }
            $phone = preg_replace('/[^0-9]/', '', $request->phone);
            if( !$phone ) 
            {
                return $this->errorWrongArgs(trans('app.phone_empty'));
            }
            $phone = '+' . $phone;
            $inviter = \VanguardLTE\Invite::where([
                'shop_id' => auth()->user()->shop_id, 
                'status' => 1
            ])->first();
            if( !$inviter ) 
            {
                return $this->errorWrongArgs(trans('app.error'));
            }
            if( !$inviter->status ) 
            {
                return $this->errorWrongArgs(trans('app.error'));
            }
            $sms = \VanguardLTE\SMS::where('user_id', auth()->user()->id)->where('status', '!=', 'DELIVERED')->count();
            if( config('smsto.max_invites') <= $sms ) 
            {
                return $this->errorWrongArgs(__('app.max_invites_error', ['max' => config('smsto.max_invites')]));
            }
            $uniq = \VanguardLTE\User::where(['phone' => $phone])->count();
            if( $uniq ) 
            {
                return $this->errorWrongArgs(__('validation.unique', ['attribute' => 'phone']));
            }
            $username = rand(111111111, 999999999);
            $password = rand(111111111, 999999999);
            foreach( [
                'url' => $request->base_url, 
                'login' => $username, 
                'password' => $password
            ] as $key => $value ) 
            {
                $inviter->message = str_replace(':' . $key, $value, $inviter->message);
            }
            $user = \VanguardLTE\User::create([
                'phone' => $phone, 
                'username' => $username, 
                'password' => $password, 
                'role_id' => 1, 
                'parent_id' => auth()->user()->id, 
                'shop_id' => auth()->user()->shop_id, 
                'status' => \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED
            ]);
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            \VanguardLTE\ShopUser::create([
                'shop_id' => auth()->user()->shop_id, 
                'user_id' => $user->id
            ]);
            $return = \VanguardLTE\Lib\SMS_sender::send($phone, $inviter->message, auth()->user()->id);
            \VanguardLTE\SMS::create([
                'user_id' => auth()->user()->id, 
                'new_user_id' => $user->id, 
                'message' => $inviter->message, 
                'message_id' => $return['message_id'], 
                'shop_id' => auth()->user()->shop_id, 
                'status' => 'Sent'
            ]);
            if( isset($return['success']) ) 
            {
                return $this->respondWithArray([
                    'success' => 'success', 
                    'text' => __('app.sms_sent')
                ]);
            }
            return $this->errorWrongArgs(trans('app.sms_error'));
        }
        public function balance(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->hasRole('user') ) 
            {
                return $this->errorWrongArgs(trans('app.only_for_users'));
            }
            $shop_id = auth()->user()->shop_id;
            $shop = \VanguardLTE\Shop::find($shop_id);
            if( !$request->sum ) 
            {
                return $this->errorWrongArgs(__('app.sum_is_empty'));
            }
            if( !$request->system ) 
            {
                return $this->errorWrongArgs('System is empty');
            }
            if( !in_array($request->system, [
                'coinbase', 
                'btcpayserver'
            ]) && strripos($request->system, 'interkassa') === false ) 
            {
                return $this->errorWrongArgs('Wrong System');
            }
            $amount = str_replace(',', '.', trim($request->sum));
            $amount = number_format(floatval($amount), 2, '.', '');
            if( $amount < settings('minimum_payment_amount') ) 
            {
                $error = __('app.minimum_payment_amount') . ' ' . settings('minimum_payment_amount') . ' ' . $shop->currency;
                return $this->errorWrongArgs($error);
            }
            if( settings('maximum_payment_amount') < $amount ) 
            {
                $error = __('app.maximum_payment_amount') . ' ' . settings('maximum_payment_amount') . ' ' . $shop->currency;
                return $this->errorWrongArgs($error);
            }
            if( $shop->balance < $amount ) 
            {
                $error = trans('app.not_enough_money_in_the_shop', [
                    'name' => $shop->name, 
                    'balance' => $shop->balance
                ]);
                return $this->errorWrongArgs($error);
            }
            if( strripos($request->system, 'interkassa') !== false ) 
            {
                if( !settings('payment_interkassa') ) 
                {
                    return $this->errorWrongArgs('System is not available');
                }
                if( !\VanguardLTE\Lib\Setting::is_available('interkassa', auth()->user()->shop_id) ) 
                {
                    return $this->errorWrongArgs(__('app.something_went_wrong'));
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'currency' => auth()->user()->shop->currency, 
                    'system' => 'interkassa', 
                    'shop_id' => auth()->user()->shop_id
                ]);
                $data = [];
                if( $request->success ) 
                {
                    $data['ik_suc_u'] = $request->success;
                }
                if( $request->wait ) 
                {
                    $data['ik_pnd_u'] = $request->wait;
                }
                if( $request->fail ) 
                {
                    $data['ik_fal_u'] = $request->fail;
                }
                $form = \VanguardLTE\Lib\Interkassa::get_form(auth()->user()->id, auth()->user()->shop_id, $payment->id, $amount, $request->system, $data);
                if( isset($form['success']) ) 
                {
                    $data = $form['form'];
                    if( is_array($data) ) 
                    {
                        $data['fields'] = $data['parameters'];
                        unset($data['parameters']);
                    }
                    else
                    {
                        $data = $form;
                    }
                    return $this->respondWithArray($data);
                }
                else
                {
                    return $this->errorWrongArgs(__('app.something_went_wrong'));
                }
            }
            if( $request->system == 'btcpayserver' ) 
            {
                if( !settings('payment_' . $request->system) ) 
                {
                    return $this->errorWrongArgs('System is not available');
                }
                if( !\VanguardLTE\Lib\Setting::is_available('btcpayserver', auth()->user()->shop_id) ) 
                {
                    return $this->errorWrongArgs(__('app.something_went_wrong'));
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'currency' => auth()->user()->shop->currency, 
                    'system' => 'btcpayserver', 
                    'shop_id' => auth()->user()->shop_id
                ]);
                $data = [
                    'method' => 'POST', 
                    'action' => \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'server', auth()->user()->shop_id) . '/api/v1/invoices', 
                    'charset' => 'UTF-8', 
                    'fields' => [
                        'storeId' => \VanguardLTE\Lib\Setting::get_value('btcpayserver', 'store_id', auth()->user()->shop_id), 
                        'orderId' => $payment->id, 
                        'price' => $amount, 
                        'currency' => auth()->user()->shop->currency, 
                        'checkoutDesc' => 'Account replenishment for a client #' . auth()->user()->id, 
                        'serverIpn' => route('payment.btcpayserver.result')
                    ]
                ];
                if( $request->redirect_url ) 
                {
                    $data['fields']['browserRedirect'] = $request->redirect_url;
                }
                if( $request->result_url ) 
                {
                    $data['fields']['serverIpn'] = $request->result_url;
                }
                return $this->respondWithArray($data);
            }
            if( $request->system == 'coinbase' ) 
            {
                if( !settings('payment_' . $request->system) ) 
                {
                    return $this->errorWrongArgs('System is not available');
                }
                if( !\VanguardLTE\Lib\Setting::is_available('coinbase', auth()->user()->shop_id) ) 
                {
                    return $this->errorWrongArgs(trans('app.something_went_wrong'));
                }
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => auth()->user()->id, 
                    'sum' => $amount, 
                    'currency' => auth()->user()->shop->currency, 
                    'system' => 'coinbase', 
                    'shop_id' => auth()->user()->shop_id
                ]);
                $data = [
                    'name' => 'Payment ID #' . $payment->id, 
                    'description' => 'Account replenishment for a client #' . auth()->user()->id, 
                    'local_price' => [
                        'amount' => $amount, 
                        'currency' => auth()->user()->shop->currency
                    ], 
                    'pricing_type' => 'fixed_price', 
                    'metadata' => ['payment_id' => $payment->id]
                ];
                if( $request->success ) 
                {
                    $data['redirect_url'] = $request->success;
                }
                if( $request->fail ) 
                {
                    $data['cancel_url'] = $request->fail;
                }
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-CC-Api-Key' => \VanguardLTE\Lib\Setting::get_value('coinbase', 'api_key', auth()->user()->shop_id), 
                    'X-CC-Version' => '2018-03-22'
                ])->post('https://api.commerce.coinbase.com/charges', $data);
                if( isset($response['data']['hosted_url']) ) 
                {
                    return $this->respondWithArray(['url' => $response['data']['hosted_url']]);
                }
                else
                {
                    return $this->errorWrongArgs(__('app.something_went_wrong'));
                }
            }
            return $this->errorWrongArgs(__('app.something_went_wrong'));
        }
    }

}
