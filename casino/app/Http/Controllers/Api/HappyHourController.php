<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class HappyHourController extends ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( !auth()->user()->shop_id ) 
            {
                return $this->errorWrongArgs(trans('app.choose_shop'));
            }
            $happyhours = \VanguardLTE\HappyHour::where('shop_id', auth()->user()->shop_id)->paginate(25);
            return $this->respondWithPagination($happyhours, new \VanguardLTE\Transformers\HappyHourTransformer());
        }
    }

}
