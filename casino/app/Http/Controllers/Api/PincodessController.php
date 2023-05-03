<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class PincodessController extends ApiController
    {
        private $max_shops = 1000;
        public function __construct()
        {
            $this->middleware('auth');
            $this->middleware('permission_api:pincodes.manage');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            $pincodes = \VanguardLTE\Pincode::select('pincodes.*')->where('pincodes.shop_id', auth()->user()->shop_id);
            if( $request->search != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.code', 'like', '%' . preg_replace('[^0-9A-Z]', '', $request->search) . '%');
            }
            if( $request->sum_from != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.nominal', '>=', $request->sum_from);
            }
            if( $request->sum_to != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.nominal', '<=', $request->sum_to);
            }
            if( $request->status != '' ) 
            {
                $pincodes = $pincodes->where('pincodes.status', $request->status);
            }
            $pincodes = $pincodes->orderBy('id', 'desc')->paginate(200000);
            return $this->respondWithPagination($pincodes, new \VanguardLTE\Transformers\PincodeTransformer());
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            $validatedData = $request->validate(['code' => 'required|unique:pincodes|max:255']);
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            if( !$request->nominal || $request->nominal <= 0 ) 
            {
                return $this->errorWrongArgs(trans('app.wrong_sum'));
            }
            $code = preg_replace('/[^0-9a-z]/', '', $request->code);
            if( strlen($code) != 20 ) 
            {
                return $this->errorWrongArgs(trans('app.wrong_code'));
            }
            $data = $request->all();
            $data['shop_id'] = auth()->user()->shop_id;
            $pincode = \VanguardLTE\Pincode::create($data);
            return $this->setStatusCode(201)->respondWithItem($pincode, new \VanguardLTE\Transformers\PincodeTransformer());
        }
        public function mass(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            $shop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
            if( !$shop ) 
            {
                return $this->errorNotFound();
            }
            if( !$request->count || $request->count <= 0 ) 
            {
                return $this->errorWrongArgs('Wrong Count');
            }
            if( !$request->nominal || $request->nominal <= 0 ) 
            {
                return $this->errorWrongArgs(trans('app.wrong_sum'));
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
                        'status' => $request->status, 
                        'shop_id' => auth()->user()->shop_id
                    ];
                    \VanguardLTE\Pincode::create($data);
                }
            }
            return $this->setStatusCode(201)->respondWithSuccess();
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Pincode $pincode)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            $data = $request->only(['status']);
            if( !in_array($pincode->shop_id, auth()->user()->availableShops()) ) 
            {
                return $this->errorNotFound();
            }
            $pincode->update($data);
            return $this->setStatusCode(201)->respondWithSuccess();
        }
        public function destroy(\VanguardLTE\Pincode $pincode)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            if( !in_array($pincode->shop_id, auth()->user()->availableShops()) ) 
            {
                return $this->errorNotFound();
            }
            \VanguardLTE\Pincode::where('id', $pincode->id)->delete();
            return $this->setStatusCode(201)->respondWithSuccess();
        }
    }

}
