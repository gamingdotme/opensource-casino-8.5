<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class WelcomeBonusController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:welcome_bonuses.manage');
            $this->middleware('shopzero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $welcome_bonuses = \VanguardLTE\WelcomeBonus::where('shop_id', \Auth::user()->shop_id)->get();
            return view('backend.welcomebonuses.list', compact('welcome_bonuses', 'shop'));
        }
        public function edit(\VanguardLTE\WelcomeBonus $welcome_bonus)
        {
            if( !in_array($welcome_bonus->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            return view('backend.welcomebonuses.edit', compact('welcome_bonus'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\WelcomeBonus $welcome_bonus)
        {
            if( !in_array($welcome_bonus->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            $request->validate(['wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\WelcomeBonus::$values['wager']))]);
            $data = $request->only([
                'pay', 
                'sum', 
                'bonus', 
                'wager', 
                'status'
            ]);
            $welcome_bonus->update($data);
            return redirect()->route('backend.welcome_bonus.list')->withSuccess(trans('app.welcome_bonus_updated'));
        }
        public function status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( $shop && auth()->user()->hasPermission('welcome_bonuses.edit') ) 
            {
                if( $status == 'disable' ) 
                {
                    $shop->update(['welcome_bonuses_active' => 0]);
                }
                else
                {
                    $shop->update(['welcome_bonuses_active' => 1]);
                }
            }
            return redirect()->route('backend.welcome_bonus.list')->withSuccess(trans('app.welcome_bonus_updated'));
        }
        public function security()
        {
        }
    }

}
