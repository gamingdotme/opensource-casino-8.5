<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class SMSBonusController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:sms_bonuses.manage');
            $this->middleware('shopzero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $sms_bonuses = \VanguardLTE\SMSBonus::where('shop_id', auth()->user()->shop_id)->orderBy('days', 'ASC')->get();
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            return view('backend.sms_bonuses.list', compact('sms_bonuses', 'shop'));
        }
        public function create()
        {
            return view('backend.sms_bonuses.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $request->validate(['wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\SMSBonus::$values['wager']))]);
            $uniq = \VanguardLTE\SMSBonus::where([
                'days' => $request->days, 
                'shop_id' => auth()->user()->shop_id
            ])->count();
            if( $uniq ) 
            {
                return redirect()->route('backend.sms_bonus.list')->withErrors([__('app.sms_bonus_day_exist')]);
            }
            $data = $request->only([
                'days', 
                'bonus', 
                'status', 
                'wager'
            ]);
            \VanguardLTE\SMSBonus::create($data + ['shop_id' => auth()->user()->shop_id]);
            return redirect()->route('backend.sms_bonus.list')->withSuccess(trans('app.sms_bonus_created'));
        }
        public function edit($sms_bonus)
        {
            $sms_bonus = \VanguardLTE\SMSBonus::where([
                'id' => $sms_bonus, 
                'shop_id' => auth()->user()->shop_id
            ])->firstOrFail();
            if( !in_array($sms_bonus->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            return view('backend.sms_bonuses.edit', compact('sms_bonus'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\SMSBonus $sms_bonus)
        {
            $request->validate(['wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\SMSBonus::$values['wager']))]);
            $data = $request->only([
                'days', 
                'bonus', 
                'status', 
                'wager'
            ]);
            if( !in_array($sms_bonus->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            $uniq = \VanguardLTE\SMSBonus::where([
                'days' => $request->days, 
                'shop_id' => auth()->user()->shop_id
            ])->where('id', '!=', $sms_bonus->id)->count();
            if( $uniq ) 
            {
                return redirect()->route('backend.sms_bonus.list')->withErrors([__('app.sms_bonus_day_exist')]);
            }
            \VanguardLTE\SMSBonus::where('id', $sms_bonus->id)->update($data);
            return redirect()->route('backend.sms_bonus.list')->withSuccess(trans('app.sms_bonus_updated'));
        }
        public function delete(\VanguardLTE\SMSBonus $sms_bonus)
        {
            if( !in_array($sms_bonus->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            \VanguardLTE\SMSBonus::where('id', $sms_bonus->id)->delete();
            return redirect()->route('backend.sms_bonus.list')->withSuccess(trans('app.sms_bonus_deleted'));
        }
        public function status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( $shop && auth()->user()->hasPermission('sms_bonuses.edit') ) 
            {
                if( $status == 'disable' ) 
                {
                    $shop->update(['sms_bonuses_active' => 0]);
                }
                else
                {
                    $shop->update(['sms_bonuses_active' => 1]);
                }
            }
            return redirect()->route('backend.sms_bonus.list')->withSuccess(trans('app.sms_bonus_updated'));
        }
        public function security()
        {
        }
    }

}
