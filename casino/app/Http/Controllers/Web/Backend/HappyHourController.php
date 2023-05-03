<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class HappyHourController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:happyhours.manage');
            $this->middleware('shopzero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            $happyhours = \VanguardLTE\HappyHour::where('shop_id', \Auth::user()->shop_id)->get();
            return view('backend.happyhours.list', compact('happyhours', 'shop'));
        }
        public function create()
        {
            return view('backend.happyhours.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $request->validate([
                'multiplier' => 'required|in:' . implode(',', \VanguardLTE\HappyHour::$values['wager']), 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\HappyHour::$values['wager']))
            ]);
            $uniq = \VanguardLTE\HappyHour::where([
                'time' => $request->time, 
                'shop_id' => auth()->user()->shop_id
            ])->count();
            if( $uniq ) 
            {
                return redirect()->route('backend.happyhour.list')->withErrors(trans('validation.unique', ['attribute' => 'time']));
            }
            $data = $request->all();
            $data['shop_id'] = auth()->user()->shop_id;
            $happyhour = \VanguardLTE\HappyHour::create($data);
            event(new \VanguardLTE\Events\HappyHours\NewHappyHour($happyhour));
            return redirect()->route('backend.happyhour.list')->withSuccess(trans('app.happyhour_created'));
        }
        public function edit($happyhour)
        {
            $happyhour = \VanguardLTE\HappyHour::where('id', $happyhour)->first();
            if( !in_array($happyhour->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            $activity = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                'system' => 'happyhour', 
                'item_id' => $happyhour->id
            ])->take(2)->get();
            return view('backend.happyhours.edit', compact('happyhour', 'activity'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\HappyHour $happyhour)
        {
            if( !in_array($happyhour->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            $request->validate([
                'multiplier' => 'required|in:' . implode(',', \VanguardLTE\HappyHour::$values['wager']), 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\HappyHour::$values['wager']))
            ]);
            $data = $request->only([
                'multiplier', 
                'wager', 
                'time', 
                'status'
            ]);
            $uniq = \VanguardLTE\HappyHour::where([
                'time' => $request->time, 
                'shop_id' => auth()->user()->shop_id
            ])->where('id', '!=', $happyhour->id)->count();
            if( $uniq ) 
            {
                return redirect()->route('backend.happyhour.list')->withErrors(trans('validation.unique', ['attribute' => 'time']));
            }
            $happyhour->update($data);
            return redirect()->route('backend.happyhour.list')->withSuccess(trans('app.happyhour_updated'));
        }
        public function delete(\VanguardLTE\HappyHour $happyhour)
        {
            if( !in_array($happyhour->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            event(new \VanguardLTE\Events\HappyHours\DeleteHappyHour($happyhour));
            \VanguardLTE\HappyHour::where('id', $happyhour->id)->delete();
            return redirect()->route('backend.happyhour.list')->withSuccess(trans('app.happyhour_deleted'));
        }
        public function status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( $shop && auth()->user()->hasPermission('happyhours.edit') ) 
            {
                if( $status == 'disable' ) 
                {
                    $shop->update(['happyhours_active' => 0]);
                }
                else
                {
                    $shop->update(['happyhours_active' => 1]);
                }
            }
            return redirect()->route('backend.happyhour.list')->withSuccess(trans('app.happyhour_updated'));
        }
        public function security()
        {
        }
    }

}
