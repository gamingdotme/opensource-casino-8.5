<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ApiController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:api.manage');
            $this->middleware('shop_not_zero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $api = \VanguardLTE\Api::where('shop_id', auth()->user()->shop_id)->orderBy('id', 'DESC');
            if( $request->search != '' ) 
            {
                $api = $api->where('keygen', 'like', '%' . $request->search . '%')->orWhere('ip', 'like', '%' . $request->search . '%');
            }
            if( $request->status != '' ) 
            {
                $api = $api->where('status', $request->status);
            }
            $api = $api->get();
            return view('backend.api.list', compact('api'));
        }
        public function json(\Illuminate\Http\Request $request)
        {
            $shops = auth()->user()->shops_array(true);
            $api = [];
            if( $request->shop_id ) 
            {
                if( !(count($shops) && in_array($request->shop_id, $shops)) ) 
                {
                    return json_encode([]);
                }
                $apis = \VanguardLTE\Api::where('shop_id', $request->shop_id)->get();
                foreach( $apis as $key ) 
                {
                    $api[$key->keygen] = $key->keygen . ' / ' . $key->ip;
                }
            }
            return json_encode($api);
        }
        public function create()
        {
            if( !\VanguardLTE\Shop::count() ) 
            {
                return redirect()->route('backend.shop.create');
            }
            if( !\VanguardLTE\Shop::find(auth()->user()->shop_id) ) 
            {
                return redirect()->route('backend.shop.create');
            }
            $shops = auth()->user()->shops_array();
            return view('backend.api.add', compact('shops'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( !\VanguardLTE\Shop::count() ) 
            {
                return redirect()->route('backend.shop.create');
            }
            $data = $request->only([
                'keygen', 
                'ip', 
                'shop_id', 
                'status'
            ]);
            $shops = auth()->user()->shops_array(true);
            if( count($shops) && !in_array($data['shop_id'], $shops) ) 
            {
                abort(404);
            }
            \VanguardLTE\Api::create($data);
            return redirect()->route('backend.api.list')->withSuccess(trans('app.api_created'));
        }
        public function edit(\VanguardLTE\Api $api)
        {
            $shops = auth()->user()->shops_array();
            if( !in_array($api->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            return view('backend.api.edit', compact('api', 'shops'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Api $api)
        {
            if( !in_array($api->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            $data = $request->only([
                'keygen', 
                'ip', 
                'shop_id', 
                'status'
            ]);
            \VanguardLTE\Api::where('id', $api->id)->update($data);
            return redirect()->route('backend.api.list')->withSuccess(trans('app.api_updated'));
        }
        public function delete(\VanguardLTE\Api $api)
        {
            if( !in_array($api->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            \VanguardLTE\Api::where('id', $api->id)->delete();
            return redirect()->route('backend.api.list')->withSuccess(trans('app.api_deleted'));
        }
        public function generate()
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for( $i = 0; $i < 25; $i++ ) 
            {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return response()->json([
                'success' => true, 
                'key' => $randomString
            ]);
        }
        public function security()
        {
        }
    }

}
