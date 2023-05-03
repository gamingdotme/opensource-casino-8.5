<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class TournamentsController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            if( \Illuminate\Support\Facades\Auth::check() && !auth()->user()->hasRole('user') ) 
            {
                return redirect()->route('backend.dashboard');
            }
            if( !\Illuminate\Support\Facades\Auth::check() ) 
            {
                return redirect()->route('frontend.auth.login');
            }
            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? auth()->user()->shop_id : 0);
            $shop = \VanguardLTE\Shop::find($shop_id);
            $currentSliderNum = -1;
            $category1 = '';
            $frontend = settings('frontend');
            if( $shop_id && $shop ) 
            {
                $frontend = $shop->frontend;
            }
            $games = \VanguardLTE\Game::where([
                'view' => 1, 
                'shop_id' => $shop_id
            ]);
            $detect = new \Detection\MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) 
            {
                $games = $games->whereIn('device', [
                    0, 
                    2
                ]);
            }
            else
            {
                $games = $games->whereIn('device', [
                    1, 
                    2
                ]);
            }
            $games = $games->get();
            $categories = false;
            if( $games ) 
            {
                $cat_ids = \VanguardLTE\GameCategory::whereIn('game_id', \VanguardLTE\Game::where([
                    'view' => 1, 
                    'shop_id' => $shop_id
                ])->pluck('original_id'))->groupBy('category_id')->pluck('category_id');
                if( count($cat_ids) ) 
                {
                    $categories = \VanguardLTE\Category::whereIn('id', $cat_ids)->orderBy('position', 'ASC')->get();
                }
            }
            $tournament = \VanguardLTE\Tournament::where('shop_id', $shop_id)->where('start', '<=', \Carbon\Carbon::now())->where('end', '>=', \Carbon\Carbon::now())->orderBy('end', 'ASC')->first();
            if( !$tournament ) 
            {
                $tournament = \VanguardLTE\Tournament::where('shop_id', $shop_id)->where('start', '>=', \Carbon\Carbon::now())->where('end', '>=', \Carbon\Carbon::now())->orderBy('end', 'ASC')->first();
            }
            $activeTake = 5;
            if( $tournament ) 
            {
                $activeTake = 4;
            }
            $activeTournaments = \VanguardLTE\Tournament::where([
                'shop_id' => $shop_id, 
                'status' => 'active'
            ]);
            if( $tournament ) 
            {
                $activeTournaments->where('id', '!=', $tournament->id);
            }
            $activeTournaments = $activeTournaments->orderBy('end', 'ASC')->take($activeTake)->get();
            $waitingTournaments = \VanguardLTE\Tournament::where([
                'shop_id' => $shop_id, 
                'status' => 'waiting'
            ]);
            if( $tournament ) 
            {
                $waitingTournaments->where('id', '!=', $tournament->id);
            }
            $waitingTournaments = $waitingTournaments->orderBy('start', 'ASC')->take(4)->get();
            $completedTournaments = \VanguardLTE\Tournament::where([
                'shop_id' => $shop_id, 
                'status' => 'completed'
            ]);
            if( $tournament ) 
            {
                $completedTournaments->where('id', '!=', $tournament->id);
            }
            $completedTournaments = $completedTournaments->orderBy('end', 'ASC')->take(4)->get();
            return view('frontend.' . $frontend . '.tournaments.list', compact('activeTournaments', 'waitingTournaments', 'completedTournaments', 'tournament', 'categories', 'currentSliderNum', 'category1'));
        }
        public function view(\Illuminate\Http\Request $request, \VanguardLTE\Tournament $tournament)
        {
            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? auth()->user()->shop_id : 0);
            $shop = \VanguardLTE\Shop::find($shop_id);
            $currentSliderNum = -1;
            $category1 = '';
            $frontend = settings('frontend');
            if( $shop_id && $shop ) 
            {
                $frontend = $shop->frontend;
            }
            $games = \VanguardLTE\Game::where([
                'view' => 1, 
                'shop_id' => $shop_id
            ]);
            $detect = new \Detection\MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) 
            {
                $games = $games->whereIn('device', [
                    0, 
                    2
                ]);
            }
            else
            {
                $games = $games->whereIn('device', [
                    1, 
                    2
                ]);
            }
            $games = $games->get();
            $categories = false;
            if( $games ) 
            {
                $cat_ids = \VanguardLTE\GameCategory::whereIn('game_id', \VanguardLTE\Game::where([
                    'view' => 1, 
                    'shop_id' => $shop_id
                ])->pluck('original_id'))->groupBy('category_id')->pluck('category_id');
                if( count($cat_ids) ) 
                {
                    $categories = \VanguardLTE\Category::whereIn('id', $cat_ids)->orderBy('position', 'ASC')->get();
                }
            }
            return view('frontend.' . $frontend . '.tournaments.view', compact('tournament', 'categories', 'currentSliderNum', 'category1'));
        }
        public function security()
        {
        }
    }

}
