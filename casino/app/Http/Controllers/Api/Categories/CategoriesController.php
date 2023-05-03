<?php 
namespace VanguardLTE\Http\Controllers\Api\Categories
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class CategoriesController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $categories = \VanguardLTE\Category::orderBy('id', 'ASC');
            if( $request->id != '' ) 
            {
                $categories = $categories->where('id', $request->id);
            }
            if( $request->skip_empty != '' ) 
            {
                $categories = $categories->whereHas('games', function($query) use ($request)
                {
                    $query->whereHas('game', function($query2) use ($request)
                    {
                        if( $request->view ) 
                        {
                            $query2->where('view', 1);
                        }
                        if( $request->device ) 
                        {
                            $devices = explode('|', $request->device);
                            $query2->whereIn('device', (array)$devices);
                        }
                    });
                });
            }
            $categories = $categories->paginate(100);
            return $this->respondWithPagination($categories, new \VanguardLTE\Transformers\CategoryTransformer());
        }
    }

}
