<?php
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RefundsController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:refunds.manage');
            $this->middleware('shopzero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            /*$checked = new \VanguardLTE\Lib\LicenseDK();
            $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
            if( $license_notifications_array['notification_case'] != 'notification_license_ok' )
            {
                return redirect()->route('frontend.page.error_license');
            }
            if( !$this->security() )
            {
                return redirect()->route('frontend.page.error_license');
            }*/
            $refunds = \VanguardLTE\Refund::where('shop_id', auth()->user()->shop_id)->get();
            return view(auth()->user()->hasRole('distributor') ? 'backend.returns.list-distributor' : 'backend.refunds.list', compact('refunds'));
        }
        public function create()
        {
            return redirect()->route('backend.refunds.list');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            return redirect()->route('backend.refunds.list');
            $data = $request->all();
            $request->validate(['percent' => 'required|in:' . implode(',', \VanguardLTE\Refund::$values['percent'])]);
            $data['shop_id'] = auth()->user()->shop_id;
            $return = \VanguardLTE\Refund::create($data);
            return redirect()->route('backend.refunds.list')->withSuccess(trans('app.return_created'));
        }
        public function edit($refund)
        {
            $refund = \VanguardLTE\Refund::where('id', $refund)->first();
            if( !$refund )
            {
                abort(404);
            }
            if( !in_array($refund->shop_id, auth()->user()->availableShops()) )
            {
                return redirect()->back()->withErrors([trans('app.wrong_shop')]);
            }
            $activity = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                'system' => 'refund',
                'item_id' => $refund->id
            ])->take(2)->get();
            return view('backend.refunds.edit', compact('refund', 'activity'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Refund $refund)
        {
            if( !in_array($refund->shop_id, auth()->user()->availableShops()) )
            {
                return redirect()->back()->withErrors([trans('app.wrong_shop')]);
            }
            $request->validate(['percent' => 'required|in:' . implode(',', \VanguardLTE\Refund::$values['percent'])]);
            $data = $request->only([
                'min_pay',
                'max_pay',
                'percent',
                'min_balance',
                'status'
            ]);
            $refund->update($data);
            return redirect()->route('backend.refunds.list')->withSuccess(trans('app.refund_updated'));
        }
        public function delete(\VanguardLTE\Refund $refund)
        {
            return redirect()->route('backend.refunds.list');
            if( !in_array($refund->shop_id, auth()->user()->availableShops()) )
            {
                return redirect()->back()->withErrors([trans('app.wrong_shop')]);
            }
            event(new \VanguardLTE\Events\Refunds\DeleteRefund($refund));
            \VanguardLTE\Refund::where('id', $refund->id)->delete();
            return redirect()->route('backend.refunds.list')->withSuccess(trans('app.refund_deleted'));
        }
        /* public function security()
        {
            if( config('LicenseDK.DK_KEY') != 'wi9qydosuimsnls5zoe5q298evkhim0ughx1w16qybs2fhlcpn' )
            {
                return false;
            }
            if( md5_file(base_path() . '/config/LicenseDK.php') != '673b2d189a08ec44ba40a8157f316813' )
            {
                return false;
            }
            if( md5_file(base_path() . '/app/Lib/LicenseDK.php') != '063414b162b5788e30dedf3e0840e48c' )
            {
                return false;
            }
            return true;
        }*/
    }

}
