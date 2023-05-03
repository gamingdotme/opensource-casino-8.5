<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ProgressController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:progress.manage');
            $this->middleware('shopzero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $progress = \VanguardLTE\Progress::where('shop_id', auth()->user()->shop_id);
            if( $request->search != '' ) 
            {
            }
            if( $request->sum_from != '' ) 
            {
                $progress = $progress->where('sum', '>=', $request->sum_from);
            }
            if( $request->sum_to != '' ) 
            {
                $progress = $progress->where('sum', '<=', $request->sum_to);
            }
            if( $request->spins_from != '' ) 
            {
                $progress = $progress->where('spins', '>=', $request->spins_from);
            }
            if( $request->spins_to != '' ) 
            {
                $progress = $progress->where('spins', '<=', $request->spins_to);
            }
            if( $request->bet_from != '' ) 
            {
                $progress = $progress->where('bet', '>=', $request->bet_from);
            }
            if( $request->bet_to != '' ) 
            {
                $progress = $progress->where('bet', '<=', $request->bet_to);
            }
            if( $request->type != '' ) 
            {
                $progress = $progress->where('type', $request->type);
            }
            if( $request->status != '' ) 
            {
                $progress = $progress->where('status', $request->status);
            }
            $progress = $progress->orderBy('rating', 'asc')->get();
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            return view('backend.progress.list', compact('progress', 'shop'));
        }
        public function create()
        {
            return view('backend.progress.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $request->validate([
                'percent' => 'required|in:' . implode(',', \VanguardLTE\Progress::$values['percent']), 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\Progress::$values['wager']))
            ]);
            $data = $request->only([
                'sum', 
                'type', 
                'spins', 
                'bet', 
                'shop_id', 
                'rating', 
                'bonus', 
                'day', 
                'min', 
                'max', 
                'percent', 
                'min_balance', 
                'wager', 
                'status', 
                'days_active'
            ]);
            $data['shop_id'] = auth()->user()->shop_id;
            \VanguardLTE\Progress::create($data);
            return redirect()->route('backend.progress.list')->withSuccess(trans('app.progress_created'));
        }
        public function edit($progress)
        {
            $progress = \VanguardLTE\Progress::where([
                'id' => $progress, 
                'shop_id' => auth()->user()->shop_id
            ])->firstOrFail();
            if( !in_array($progress->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            return view('backend.progress.edit', compact('progress'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Progress $progress)
        {
            $request->validate([
                'percent' => 'required|in:' . implode(',', \VanguardLTE\Progress::$values['percent']), 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\Progress::$values['wager']))
            ]);
            $data = $request->only([
                'sum', 
                'type', 
                'spins', 
                'bet', 
                'shop_id', 
                'bonus', 
                'day', 
                'min', 
                'max', 
                'percent', 
                'min_balance', 
                'wager', 
                'status', 
                'days_active'
            ]);
            if( !in_array($progress->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            if( $data['max'] < $data['min'] ) 
            {
                return redirect()->back()->withErrors([trans('app.min_more_max')]);
            }
            \VanguardLTE\Progress::where('id', $progress->id)->update($data);
            return redirect()->route('backend.progress.list')->withSuccess(trans('app.progress_updated'));
        }
        public function delete(\VanguardLTE\Progress $progress)
        {
            if( !in_array($progress->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            \VanguardLTE\Progress::where('id', $progress->id)->delete();
            return redirect()->route('backend.progress.list')->withSuccess(trans('app.pprogress_deleted'));
        }
        public function status($status)
        {
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( $shop && auth()->user()->hasPermission('progress.edit') ) 
            {
                if( $status == 'disable' ) 
                {
                    $shop->update(['progress_active' => 0]);
                }
                else
                {
                    $shop->update(['progress_active' => 1]);
                }
            }
            return redirect()->route('backend.progress.list')->withSuccess(trans('app.progress_updated'));
        }
        public function security()
        {
        }
    }

}
