<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class PincodeController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:pincodes.manage');
            $this->middleware('shop_not_zero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $pincodes = \VanguardLTE\Pincode::select('pincodes.*')->where('pincodes.shop_id', auth()->user()->shop_id);
            if( $request->download ) 
            {
                $pincodes = $pincodes->select('code', 'nominal', 'created_at', 'status');
            }
            if( $request->pincode != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.code', 'like', '%' . preg_replace('[^0-9A-Z]', '', $request->pincode) . '%');
            }
            if( $request->sum_from != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.nominal', '>=', $request->sum_from);
            }
            if( $request->sum_to != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.nominal', '<=', $request->sum_to);
            }
            if( $request->created != '' ) 
            {
                $dates = explode(' - ', $request->created);
                $pincodes = $pincodes->where('pincodes.created_at', '>=', $dates[0]);
                $pincodes = $pincodes->where('pincodes.created_at', '<=', $dates[1]);
            }
            if( $request->status != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.status', $request->status);
            }
            $pincodes = $pincodes->orderBy('id', 'desc')->get();
            if( $request->download ) 
            {
                $keys = [
                    'PIN code', 
                    'Nominal', 
                    'Date', 
                    'Status'
                ];
                $data = $pincodes->toArray();
                foreach( $data as &$pincode ) 
                {
                    $pincode['created_at'] = \Carbon\Carbon::parse($pincode['created_at'])->format('Y-m-d H:i:s');
                }
                $downloader = new \VanguardLTE\Lib\Downloader();
                $downloader->download_send_headers('pincodes_export_' . date('Y-m-d') . '.csv');
                echo $downloader->array2csv($data, $keys);
                exit();
            }
            return view('backend.pincodes.list', compact('pincodes'));
        }
        public function create()
        {
            return view('backend.pincodes.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => auth()->user()->shop_id, 
                'end_date' => null
            ])->first();
            if( !$open_shift ) 
            {
                return redirect()->back()->withErrors([trans('app.shift_not_opened')]);
            }
            $validatedData = $request->validate(['code' => 'required|unique:pincodes|max:255']);
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                abort(404);
            }
            if( !$request->nominal || $request->nominal <= 0 ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_sum')]);
            }
            $code = preg_replace('/[^0-9a-z]/', '', $request->code);
            if( strlen($code) != 20 ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_code')]);
            }
            $data = $request->all();
            $data['shop_id'] = auth()->user()->shop_id;
            \VanguardLTE\Pincode::create($data);
            return redirect()->route('backend.pincode.list')->withSuccess(trans('app.pincode_created'));
        }
        public function massadd(\Illuminate\Http\Request $request)
        {
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => auth()->user()->shop_id, 
                'end_date' => null
            ])->first();
            if( !$open_shift ) 
            {
                return redirect()->back()->withErrors([trans('app.shift_not_opened')]);
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                abort(404);
            }
            if( !$request->nominal || $request->nominal <= 0 ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_sum')]);
            }
            if( isset($request->count) && is_numeric($request->count) && isset($request->nominal) && is_numeric($request->nominal) ) 
            {
                for( $i = 0; $i < $request->count; $i++ ) 
                {
                    $pincode = '';
                    $nonUniq = true;
                    while( $nonUniq ) 
                    {
                        $str = md5(rand(100000, 999999));
                        $pincode = mb_strtoupper(implode('-', [
                            substr($str, 0, 4), 
                            substr($str, 4, 4), 
                            substr($str, 8, 4), 
                            substr($str, 12, 4), 
                            substr($str, 16, 4)
                        ]));
                        $nonUniq = \VanguardLTE\Pincode::where('code', $pincode)->count();
                    }
                    $data = [
                        'code' => $pincode, 
                        'nominal' => $request->nominal, 
                        'shop_id' => auth()->user()->shop_id
                    ];
                    \VanguardLTE\Pincode::create($data);
                }
            }
            return redirect()->route('backend.pincode.list')->withSuccess(trans('app.pincode_created'));
        }
        public function edit($pincode)
        {
            $pincode = \VanguardLTE\Pincode::where([
                'id' => $pincode, 
                'shop_id' => auth()->user()->shop_id
            ])->firstOrFail();
            if( !in_array($pincode->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            return view('backend.pincodes.edit', compact('pincode'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Pincode $pincode)
        {
            $data = $request->only(['status']);
            if( !in_array($pincode->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            \VanguardLTE\Pincode::where('id', $pincode->id)->update($data);
            return redirect()->route('backend.pincode.list')->withSuccess(trans('app.pincode_updated'));
        }
        public function delete(\VanguardLTE\Pincode $pincode)
        {
            if( !in_array($pincode->shop_id, auth()->user()->availableShops()) ) 
            {
                abort(404);
            }
            \VanguardLTE\Pincode::where('id', $pincode->id)->delete();
            return redirect()->route('backend.pincode.list')->withSuccess(trans('app.pincode_deleted'));
        }
        public function security()
        {
        }
    }

}
