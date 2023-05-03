<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class TournamentController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:tournaments.manage');
            $this->middleware('shop_not_zero');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $tournaments = \VanguardLTE\Tournament::where('shop_id', auth()->user()->shop_id);
            if( $request->search != '' ) 
            {
                $tournaments = $tournaments->where('name', 'like', '%' . $request->search . '%');
            }
            if( $request->type != '' ) 
            {
                $tournaments = $tournaments->where('type', $request->type);
            }
            if( $request->status != '' ) 
            {
                $tournaments = $tournaments->where('status', $request->status);
            }
            if( $request->bet_from != '' ) 
            {
                $tournaments = $tournaments->where('bet', '>=', $request->bet_from);
            }
            if( $request->bet_to != '' ) 
            {
                $tournaments = $tournaments->where('bet', '<=', $request->bet_to);
            }
            if( $request->spins_from != '' ) 
            {
                $tournaments = $tournaments->where('spins', '>=', $request->spins_from);
            }
            if( $request->spins_to != '' ) 
            {
                $tournaments = $tournaments->where('spins', '<=', $request->spins_to);
            }
            if( $request->prize_from != '' ) 
            {
                $tournaments = $tournaments->where('sum_prizes', '>=', $request->prize_from);
            }
            if( $request->prize_to != '' ) 
            {
                $tournaments = $tournaments->where('sum_prizes', '<=', $request->prize_to);
            }
            if( $request->dates != '' ) 
            {
                $dates = explode(' - ', $request->dates);
                $tournaments = $tournaments->where('start', '>=', $dates[0]);
                $tournaments = $tournaments->where('start', '<=', $dates[1]);
            }
            if( $request->end_dates != '' ) 
            {
                $dates = explode(' - ', $request->end_dates);
                $tournaments = $tournaments->where('end', '>=', $dates[0]);
                $tournaments = $tournaments->where('end', '<=', $dates[1]);
            }
            $tournaments = $tournaments->orderByRaw('FIELD(status, "active", "waiting", "completed")')->orderByRaw("\r\n            CASE WHEN status = \"active\" THEN end \r\n                WHEN status = \"waiting\" THEN start \r\n                WHEN status = \"active\" THEN end \r\n                ELSE end END \r\n                asc\r\n            ")->get();
            return view('backend.tournaments.list', compact('tournaments'));
        }
        public function create(\Illuminate\Http\Request $request)
        {
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            $games = $this->games($request, [0]);
            $denied = false;
            return view('backend.tournaments.add', compact('categories', 'games', 'denied'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            if( auth()->user()->shop && auth()->user()->shop->pending ) 
            {
                return redirect()->route('backend.tournament.list')->withErrors([__('app.shop_is_creating') . '. ' . __('app.tournaments_could_be_added_later')]);
            }
            $data = $request->only([
                'name', 
                'start', 
                'end', 
                'type', 
                'bet', 
                'spins', 
                'bots', 
                'bots_time', 
                'bots_step', 
                'bots_limit', 
                'wager', 
                'repeat_number', 
                'repeat_days', 
                'image', 
                'image', 
                'description', 
                'status'
            ]);
            $request->validate([
                'name' => 'required', 
                'start' => 'required|date', 
                'end' => 'required|date|after:start', 
                'type' => 'required', 
                'bet' => 'required', 
                'spins' => 'required', 
                'bots' => 'required', 
                'bots_time' => 'required', 
                'bots_step' => 'required', 
                'bots_limit' => 'required', 
                'description' => 'required', 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\Tournament::$values['wager'])), 
                'image' => 'required|image'
            ], $request->all());
            if( isset($data['description']) ) 
            {
                $data['description'] = str_replace('&nbsp;', ' ', $data['description']);
            }
            $prev = 0;
            if( isset($request->prize) && count($request->prize) ) 
            {
                foreach( $request->prize as $index => $price ) 
                {
                    if( !$price ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.empty_prize_field')]);
                    }
                    if( !is_numeric($price) ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.not_numeric_prize_field')]);
                    }
                    if( $index > 0 && $prev < $price ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.prizes_should_decrease')]);
                    }
                    $prev = $price;
                }
            }
            $tournament = \VanguardLTE\Tournament::create($data + ['shop_id' => auth()->user()->shop_id]);
            if( $request->hasFile('image') && $request->file('image')->isValid() ) 
            {
                $image = $request->file('image')->store('public/tournaments');
                $tournament->update(['image' => basename($image)]);
            }
            $prizes = 0;
            if( isset($request->prize) && count($request->prize) ) 
            {
                foreach( $request->prize as $index => $prize ) 
                {
                    \VanguardLTE\TournamentPrize::create([
                        'tournament_id' => $tournament->id, 
                        'prize' => floatval($prize)
                    ]);
                    $prizes += $prize;
                }
            }
            $tournament->update(['sum_prizes' => $prizes]);
            if( isset($request->categories) && count($request->categories) ) 
            {
                foreach( $request->categories as $category ) 
                {
                    \VanguardLTE\TournamentCategory::create([
                        'tournament_id' => $tournament->id, 
                        'category_id' => $category
                    ]);
                }
            }
            if( isset($request->games) && count($request->games) ) 
            {
                $tournament->update(['games_selected' => 1]);
                foreach( $request->games as $game ) 
                {
                    \VanguardLTE\TournamentGame::create([
                        'tournament_id' => $tournament->id, 
                        'game_id' => $game
                    ]);
                }
            }
            else
            {
                $tournament->update(['games_selected' => 0]);
            }
            if( !$tournament->games_selected ) 
            {
                $games = $this->games($request, $request->categories);
                if( count($games) ) 
                {
                    $many = [];
                    foreach( $games as $game_id => $game_name ) 
                    {
                        $many[] = [
                            'tournament_id' => $tournament->id, 
                            'game_id' => $game_id
                        ];
                    }
                    if( count($many) ) 
                    {
                        \VanguardLTE\TournamentGame::insert($many);
                    }
                }
            }
            if( $request->bots > 0 ) 
            {
                for( $i = 0; $i < $request->bots; $i++ ) 
                {
                    $username = '';
                    $nonUniqBot = true;
                    $nonUniqUser = true;
                    while( $nonUniqBot && $nonUniqUser ) 
                    {
                        $username = rand(111111111, 999999999);
                        $nonUniqBot = \VanguardLTE\TournamentBot::where('username', $username)->count();
                        $nonUniqUser = \VanguardLTE\User::where('username', $username)->count();
                    }
                    \VanguardLTE\TournamentBot::create([
                        'username' => $username, 
                        'tournament_id' => $tournament->id
                    ]);
                }
            }
            if( $tournament->is_waiting() ) 
            {
                $tournament->update(['status' => 'waiting']);
            }
            else if( $tournament->is_completed() ) 
            {
                $tournament->update(['status' => 'completed']);
            }
            else
            {
                $tournament->update(['status' => 'active']);
            }
            return redirect()->route('backend.tournament.list')->withSuccess(trans('app.tournament_created'));
        }
        public function edit(\Illuminate\Http\Request $request, \VanguardLTE\Tournament $tournament)
        {
            if( !$tournament ) 
            {
                return redirect()->route('backend.tournament.list')->withErrors([trans('app.wrong_link')]);
            }
            if( !auth()->user()->hasRole('admin') && $tournament->shop_id != auth()->user()->shop_id ) 
            {
                return redirect()->route('backend.tournament.list')->withErrors([trans('app.wrong_link')]);
            }
            $categories = \VanguardLTE\Category::where(['parent' => 0])->get();
            $cats = \VanguardLTE\TournamentCategory::where('tournament_id', $tournament->id)->pluck('category_id')->toArray();
            $gams = \VanguardLTE\TournamentGame::where('tournament_id', $tournament->id)->pluck('game_id')->toArray();
            $games = $this->games($request, $cats);
            $denied = (\Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->start), false) <= 0 ? true : false);
            return view('backend.tournaments.edit', compact('tournament', 'categories', 'cats', 'games', 'gams', 'denied'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Tournament $tournament)
        {
            if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->start), false) <= 0 ) 
            {
                return redirect()->back()->withErrors(trans('app.tournament_started_edition_denied'));
            }
            if( !auth()->user()->hasRole('admin') && $tournament->shop_id != auth()->user()->shop_id ) 
            {
                return redirect()->route('backend.tournament.list')->withErrors([trans('app.wrong_link')]);
            }
            $data = $request->only([
                'name', 
                'start', 
                'end', 
                'type', 
                'bet', 
                'spins', 
                'bots', 
                'bots_time', 
                'bots_step', 
                'bots_limit', 
                'wager', 
                'repeat_number', 
                'repeat_days', 
                'image', 
                'image', 
                'description', 
                'status'
            ]);
            $request->validate([
                'name' => 'required', 
                'start' => 'required|date', 
                'end' => 'required|date|after:start', 
                'type' => 'required', 
                'bet' => 'required', 
                'spins' => 'required', 
                'bots' => 'required', 
                'bots_time' => 'required', 
                'bots_step' => 'required', 
                'bots_limit' => 'required', 
                'description' => 'required', 
                'wager' => 'required|in:' . implode(',', array_keys(\VanguardLTE\Tournament::$values['wager'])), 
                'image' => 'sometimes|image'
            ], $request->all());
            if( isset($data['description']) ) 
            {
                $data['description'] = str_replace('&nbsp;', ' ', $data['description']);
            }
            $prev = 0;
            if( isset($request->prize) && count($request->prize) ) 
            {
                foreach( $request->prize as $index => $price ) 
                {
                    if( !$price ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.empty_prize_field')]);
                    }
                    if( !is_numeric($price) ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.not_numeric_prize_field')]);
                    }
                    if( $index > 0 && $prev < $price ) 
                    {
                        return redirect()->back()->withInput()->withErrors([__('app.prizes_should_decrease')]);
                    }
                    $prev = $price;
                }
            }
            \VanguardLTE\Tournament::where('id', $tournament->id)->update($data);
            \VanguardLTE\TournamentStat::where(['tournament_id' => $tournament->id])->delete();
            if( $request->hasFile('image') && $request->file('image')->isValid() ) 
            {
                if( $tournament->image != '' ) 
                {
                    \Illuminate\Support\Facades\Storage::delete('public/tournaments/' . $tournament->image);
                }
                $image = $request->image->store('public/tournaments');
                $tournament->update(['image' => basename($image)]);
            }
            $prizes = 0;
            \VanguardLTE\TournamentPrize::where(['tournament_id' => $tournament->id])->delete();
            if( isset($request->prize) && count($request->prize) ) 
            {
                foreach( $request->prize as $index => $prize ) 
                {
                    \VanguardLTE\TournamentPrize::create([
                        'tournament_id' => $tournament->id, 
                        'prize' => floatval($prize)
                    ]);
                    $prizes += $prize;
                }
            }
            $tournament->update(['sum_prizes' => $prizes]);
            \VanguardLTE\TournamentCategory::where(['tournament_id' => $tournament->id])->delete();
            if( isset($request->categories) && count($request->categories) ) 
            {
                foreach( $request->categories as $category ) 
                {
                    \VanguardLTE\TournamentCategory::create([
                        'tournament_id' => $tournament->id, 
                        'category_id' => $category
                    ]);
                }
            }
            \VanguardLTE\TournamentGame::where(['tournament_id' => $tournament->id])->delete();
            if( isset($request->games) && count($request->games) ) 
            {
                $tournament->update(['games_selected' => 1]);
                foreach( $request->games as $game ) 
                {
                    \VanguardLTE\TournamentGame::create([
                        'tournament_id' => $tournament->id, 
                        'game_id' => $game
                    ]);
                }
            }
            else
            {
                $tournament->update(['games_selected' => 0]);
            }
            if( !$tournament->games_selected ) 
            {
                \VanguardLTE\TournamentGame::where('tournament_id', $tournament->id)->delete();
                $games = $this->games($request, $request->categories);
                if( count($games) ) 
                {
                    $many = [];
                    foreach( $games as $game_id => $game_name ) 
                    {
                        $many[] = [
                            'tournament_id' => $tournament->id, 
                            'game_id' => $game_id
                        ];
                    }
                    if( count($many) ) 
                    {
                        \VanguardLTE\TournamentGame::insert($many);
                    }
                }
            }
            \VanguardLTE\TournamentBot::where(['tournament_id' => $tournament->id])->delete();
            if( $request->bots > 0 ) 
            {
                for( $i = 0; $i < $request->bots; $i++ ) 
                {
                    $username = '';
                    $nonUniqBot = true;
                    $nonUniqUser = true;
                    while( $nonUniqBot && $nonUniqUser ) 
                    {
                        $username = rand(111111111, 999999999);
                        $nonUniqBot = \VanguardLTE\TournamentBot::where('username', $username)->count();
                        $nonUniqUser = \VanguardLTE\User::where('username', $username)->count();
                    }
                    \VanguardLTE\TournamentBot::create([
                        'username' => $username, 
                        'tournament_id' => $tournament->id
                    ]);
                }
            }
            if( $tournament->is_waiting() ) 
            {
                $tournament->update(['status' => 'waiting']);
            }
            else if( $tournament->is_completed() ) 
            {
                $tournament->update(['status' => 'completed']);
            }
            else
            {
                $tournament->update(['status' => 'active']);
            }
            return redirect()->route('backend.tournament.list')->withSuccess(trans('app.tournament_updated'));
        }
        public function games(\Illuminate\Http\Request $request, $ids = [])
        {
            if( !$ids || !is_array($ids) || !count($ids) ) 
            {
                $ids = (isset($request->id) ? $request->id : []);
            }
            $return = [];
            $game_ids = [];
            if( count($ids) ) 
            {
                $categories = \VanguardLTE\Category::whereIn('parent', $ids)->where('shop_id', 0)->pluck('id')->toArray();
                $categories = array_merge($categories, $ids);
                $game_ids = \VanguardLTE\GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id');
            }
            if( count($game_ids) ) 
            {
                $games = \VanguardLTE\Game::where('shop_id', 0)->whereIn('id', $game_ids)->get();
                if( count($games) ) 
                {
                    $return = \VanguardLTE\Game::where('shop_id', auth()->user()->shop_id)->whereIn('original_id', $games->pluck('original_id'))->get();
                }
            }
            if( count($return) ) 
            {
                return $return->pluck('name', 'id');
            }
            $return = \VanguardLTE\Game::where('shop_id', auth()->user()->shop_id)->get();
            if( count($return) ) 
            {
                return $return->pluck('name', 'id');
            }
            return [];
        }
        public function delete(\VanguardLTE\Tournament $tournament)
        {
            if( $tournament->image != '' ) 
            {
                \Illuminate\Support\Facades\Storage::delete('public/tournaments/' . $tournament->image);
            }
            $tournament->delete();
            return redirect()->route('backend.tournament.list')->withSuccess(trans('app.tournament_deleted'));
        }
        public function security()
        {
        }
    }

}
