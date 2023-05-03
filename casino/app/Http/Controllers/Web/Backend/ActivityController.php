<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ActivityController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $activities = null;
        public function __construct(\VanguardLTE\Repositories\Activity\ActivityRepository $activities)
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->middleware('permission:users.activity');
            $this->activities = $activities;
        }
        public function index(\Illuminate\Http\Request $request, $type = '')
        {
            $perPage = 20;
            $adminView = true;
            $shops = auth()->user()->availableShops();
            $ids = auth()->user()->availableUsers();
            $activities = \VanguardLTE\Services\Logging\UserActivity\Activity::select('user_activity.*')->orderBy('created_at', 'DESC');
            if( $request->search != '' ) 
            {
                $activities = $activities->where('user_activity.description', 'like', '%' . $request->search . '%');
            }
            if( $request->ip != '' ) 
            {
                $activities = $activities->where('user_activity.ip_address', 'like', '%' . $request->ip . '%');
            }
            if( count($ids) ) 
            {
                $activities = $activities->whereIn('user_activity.user_id', $ids);
            }
            $activities = $activities->whereIn('user_activity.shop_id', $shops);
            if( $request->username != '' ) 
            {
                $activities = $activities->join('users', 'users.id', '=', 'user_activity.user_id');
                $activities = $activities->where('users.username', 'like', '%' . $request->username . '%');
            }
            if( $type != '' && in_array($type, [
                'system', 
                'user'
            ]) ) 
            {
                $activities = $activities->where('user_activity.type', $type);
            }
            $activities = $activities->paginate($perPage)->withQueryString();
            return view('backend.activity.index', compact('activities', 'adminView', 'type'));
        }
        public function system(\Illuminate\Http\Request $request)
        {
            $type = '';
            $perPage = 20;
            $adminView = true;
            $shops = auth()->user()->availableShops();
            $ids = auth()->user()->availableUsers();
            $activities = \VanguardLTE\Services\Logging\UserActivity\Activity::select('user_activity.*')->orderBy('created_at', 'DESC');
            if( $request->search != '' ) 
            {
                $activities = $activities->where('user_activity.description', 'like', '%' . $request->search . '%');
            }
            if( $request->ip != '' ) 
            {
                $activities = $activities->where('user_activity.ip_address', 'like', '%' . $request->ip . '%');
            }
            if( count($ids) ) 
            {
                $activities = $activities->whereIn('user_activity.user_id', $ids);
            }
            $activities = $activities->whereIn('user_activity.shop_id', $shops);
            if( $request->username != '' ) 
            {
                $activities = $activities->join('users', 'users.id', '=', 'user_activity.user_id');
                $activities = $activities->where('users.username', 'like', '%' . $request->username . '%');
            }
            if( !(auth()->user()->hasPermission(['activity.system']) && auth()->user()->hasPermission(['activity.user'])) ) 
            {
                if( auth()->user()->hasPermission(['activity.system']) ) 
                {
                    $activities = $activities->where('user_activity.type', 'system');
                }
                if( auth()->user()->hasPermission(['activity.user']) ) 
                {
                    $activities = $activities->where('user_activity.type', 'user');
                }
            }
            $activities = $activities->paginate($perPage)->withQueryString();
            return view('backend.activity.index', compact('activities', 'adminView', 'type'));
        }
        public function user(\Illuminate\Http\Request $request, $type = '')
        {
            $type = 'user';
            $perPage = 20;
            $adminView = true;
            $shops = auth()->user()->availableShops();
            $ids = auth()->user()->availableUsers();
            $activities = \VanguardLTE\Services\Logging\UserActivity\Activity::select('user_activity.*')->orderBy('created_at', 'DESC');
            if( $request->search != '' ) 
            {
                $activities = $activities->where('user_activity.description', 'like', '%' . $request->search . '%');
            }
            if( $request->ip != '' ) 
            {
                $activities = $activities->where('user_activity.ip_address', 'like', '%' . $request->ip . '%');
            }
            if( count($ids) ) 
            {
                $activities = $activities->whereIn('user_activity.user_id', $ids);
            }
            $activities = $activities->whereIn('user_activity.shop_id', $shops);
            if( $request->username != '' ) 
            {
                $activities = $activities->join('users', 'users.id', '=', 'user_activity.user_id');
                $activities = $activities->where('users.username', 'like', '%' . $request->username . '%');
            }
            if( $type != '' && in_array($type, [
                'system', 
                'user'
            ]) ) 
            {
                $activities = $activities->where('user_activity.type', $type);
            }
            $activities = $activities->paginate($perPage)->withQueryString();
            return view('backend.activity.index', compact('activities', 'adminView', 'type'));
        }
        public function userActivity(\VanguardLTE\User $user, \Illuminate\Http\Request $request)
        {
            $perPage = 20;
            $adminView = true;
            if( !auth()->user()->isAvailable($user) ) 
            {
                return redirect()->back()->withErrors([trans('app.wrong_user')]);
            }
            $activities = $this->activities->paginateActivities($perPage, $request->get('search'), [$user->id]);
            return view('backend.activity.index', compact('activities', 'user', 'adminView'));
        }
        public function clear()
        {
            if( auth()->user()->hasRole('admin') ) 
            {
                \VanguardLTE\UserActivity::where('id', '>', '0')->delete();
                return redirect()->back()->withSuccess(trans('app.logs_removed'));
            }
            else
            {
                abort(403);
            }
        }
        public function security()
        {
        }
    }

}
