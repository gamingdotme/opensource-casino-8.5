<?php 
namespace VanguardLTE\Http\Controllers\Api\Jackpots
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class JackpotsController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $jackpots = \VanguardLTE\JPG::where('shop_id', auth()->user()->shop_id)->orderBy('date_time', 'DESC');
            if( $request->id != '' ) 
            {
                $ids = explode('|', $request->id);
                $jackpots = $jackpots->whereIn('id', (array)$ids);
            }
            if( $request->search != '' ) 
            {
                $jackpots = $jackpots->where('name', 'like', '%' . $request->search . '%');
            }
            $jackpots = $jackpots->paginate(100000);
            return $this->respondWithPagination($jackpots, new \VanguardLTE\Transformers\JackpotTransformer());
        }
    }

}
