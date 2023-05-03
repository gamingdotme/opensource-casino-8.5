<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class SettingsController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('shopzero');
        }
        public function general($tab)
        {
            if( $tab == 'payment' ) 
            {
                if( !auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('settings.payment') ) 
                {
                    abort(403);
                }
            }
            else if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            $shops = \VanguardLTE\Shop::get();
            $directories = [];
            foreach( glob($_SERVER['DOCUMENT_ROOT'] . '/frontend/*', GLOB_ONLYDIR) as $fileinfo ) 
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            return view('backend.settings.' . $tab, compact('shops', 'directories', 'tab'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository, $tab)
        {
            if( $tab == 'payment' ) 
            {
                if( !auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('settings.payment') ) 
                {
                    abort(403);
                }
            }
            else if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            $data = $request->except('_token');
            if( count($data) ) 
            {
                foreach( $data as &$item ) 
                {
                    if( !is_array($item) ) 
                    {
                        $item = \VanguardLTE\Lib\Functions::remove_emoji($item);
                    }
                }
            }
            if( $tab == 'sms' ) 
            {
                if( isset($data['smsto_alert_phone']) ) 
                {
                    $data['smsto_alert_phone'] = preg_replace('/[^0-9]/', '', $data['smsto_alert_phone']);
                }
                if( isset($data['smsto_alert_phone']) ) 
                {
                    $data['smsto_alert_phone_2'] = preg_replace('/[^0-9]/', '', $data['smsto_alert_phone_2']);
                }
            }
            if( $tab == 'payment' && isset($data['system']) ) 
            {
                foreach( $data['system'] as $system => $system_data ) 
                {
                    foreach( $system_data as $field => $field_data ) 
                    {
                        foreach( $field_data as $shop_id => $value ) 
                        {
                            $value = \VanguardLTE\Lib\Functions::remove_emoji($value);
                            \VanguardLTE\Lib\Setting::set_value($system, $field, $value, $shop_id);
                        }
                    }
                }
                unset($data['system']);
            }
            $this->updateSettings($data);
            if( $tab == 'general' ) 
            {
                foreach( [
                    'blocked_phone_prefixes', 
                    'blocked_countries', 
                    'blocked_domains'
                ] as $key ) 
                {
                    if( !$request->$key ) 
                    {
                        \Settings::set($key, '');
                        \Settings::save();
                    }
                }
                if( $request->siteisclosed ) 
                {
                    $users = \VanguardLTE\User::where('role_id', '!=', 6)->get();
                    foreach( $users as $user ) 
                    {
                        $sessionRepository->invalidateAllSessionsForUser($user->id);
                    }
                }
                $blocked = [];
                $users = \VanguardLTE\User::where('role_id', '!=', 6)->get();
                if( count($users) ) 
                {
                    foreach( $users as $user ) 
                    {
                        if( \VanguardLTE\Lib\Filter::phone_filtered($user->phone) ) 
                        {
                            $blocked[] = $user->id;
                        }
                    }
                }
                if( count($users) ) 
                {
                    foreach( $users as $user ) 
                    {
                        if( \VanguardLTE\Lib\Filter::country_filtered($user) ) 
                        {
                            $blocked[] = $user->id;
                        }
                    }
                }
                if( count($users) ) 
                {
                    foreach( $users as $user ) 
                    {
                        if( $return = \VanguardLTE\Lib\Filter::domain_filtered($user->email) ) 
                        {
                            $blocked[] = $user->id;
                        }
                    }
                }
                if( count($blocked) ) 
                {
                    $blocked = array_unique($blocked);
                    $users = \VanguardLTE\User::whereIn('id', $blocked)->get();
                    if( count($users) ) 
                    {
                        foreach( $users as $user ) 
                        {
                            \DB::table('sessions')->where('user_id', $user->id)->delete();
                            $user->update(['remember_token' => null]);
                        }
                    }
                }
            }
            return redirect()->back()->withSuccess(trans('app.settings_updated'));
        }
        private function updateSettings($input)
        {
            foreach( $input as $key => $value ) 
            {
                \Settings::set($key, $value);
            }
            \Settings::save();
            event(new \VanguardLTE\Events\Settings\Updated());
        }
        public function optimization()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            \VanguardLTE\Task::create([
                'category' => 'optimization', 
                'action' => 'do', 
                'item_id' => '0', 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->back()->withSuccess(trans('app.optimization_complete'));
        }
        public function gelete_stat()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            \VanguardLTE\Task::create([
                'category' => 'gelete_stat', 
                'action' => 'delete', 
                'item_id' => '0', 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->back()->withSuccess(trans('app.stat_game_deleted'));
        }
        public function gelete_log()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                abort(403);
            }
            \VanguardLTE\Task::create([
                'category' => 'gelete_log', 
                'action' => 'delete', 
                'item_id' => '0', 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->back()->withSuccess(trans('app.game_log_deleted'));
        }
        public function sync()
        {
            if( !\Auth::user()->hasRole('admin') ) 
            {
                abort(403);
            }
            \VanguardLTE\Task::create([
                'category' => 'settings', 
                'action' => 'sync', 
                'item_id' => 0, 
                'user_id' => auth()->user()->id, 
                'shop_id' => auth()->user()->shop_id
            ]);
            return redirect()->route('backend.settings.list', 'general')->withSuccess(trans('app.games_sync_started'));
        }
        public function shop_block(\Illuminate\Http\Request $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $shop = \VanguardLTE\Shop::find(\Auth::user()->shop_id);
            $users = \VanguardLTE\User::where('shop_id', $shop->id)->whereIn('role_id', [1])->get();
            if( $users ) 
            {
                foreach( $users as $user ) 
                {
                    $sessions = $sessionRepository->getUserSessions($user->id);
                    if( count($sessions) ) 
                    {
                        foreach( $sessions as $session ) 
                        {
                            $sessionRepository->invalidateSession($session->id);
                        }
                    }
                }
            }
            $shop->update(['is_blocked' => 1]);
            return redirect()->back()->withSuccess(trans('app.settings_updated'));
        }
        public function shop_unblock(\Illuminate\Http\Request $request)
        {
            $shop = \VanguardLTE\Shop::find(\Auth::user()->shop_id);
            $shop->update(['is_blocked' => 0]);
            return redirect()->back()->withSuccess(trans('app.settings_updated'));
        }
    }

}
