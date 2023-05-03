<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class InfoController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $info = \VanguardLTE\Info::get();
            if( !auth()->user()->hasRole('admin') ) 
            {
                $info = \VanguardLTE\Info::where('user_id', auth()->user()->id);
            }
            else
            {
                $info = \VanguardLTE\Info::orderBy('id', 'desc');
            }
            if( $request->search != '' ) 
            {
                $info = $info->where('title', 'like', '%' . $request->search . '%');
            }
            if( $request->role != '' ) 
            {
                $info = $info->where('roles', $request->role);
            }
            $info = $info->get();
            $roles = \jeremykenedy\LaravelRoles\Models\Role::where('level', '<', auth()->user()->level())->pluck('name')->toArray();
            return view('backend.info.list', compact('info', 'roles'));
        }
        public function create()
        {
            $roles = [];
            return view('backend.info.add', compact('roles'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'title', 
                'text', 
                'roles', 
                'days'
            ]);
            $request->validate([
                'title' => 'required', 
                'text' => 'required'
            ]);
            if( isset($data['roles']) ) 
            {
                $data['roles'] = implode('|', $data['roles']);
            }
            \VanguardLTE\Info::create($data + ['user_id' => auth()->user()->id]);
            return redirect()->route('backend.info.list')->withSuccess(trans('app.info_created'));
        }
        public function edit(\VanguardLTE\Info $info)
        {
            if( !$info ) 
            {
                return redirect()->route('backend.info.list')->withErrors([trans('app.wrong_link')]);
            }
            if( !auth()->user()->hasRole('admin') && $info->user_id != auth()->user()->id ) 
            {
                return redirect()->route('backend.info.list')->withErrors([trans('app.wrong_link')]);
            }
            $roles = explode('|', $info->roles);
            return view('backend.info.edit', compact('info', 'roles'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Info $info)
        {
            $data = $request->only([
                'title', 
                'text', 
                'roles', 
                'days'
            ]);
            if( isset($data['roles']) ) 
            {
                $data['roles'] = implode('|', $data['roles']);
            }
            $request->validate([
                'title' => 'required', 
                'text' => 'required'
            ]);
            \VanguardLTE\Info::where('id', $info->id)->update($data);
            return redirect()->route('backend.info.list')->withSuccess(trans('app.info_updated'));
        }
        public function delete(\VanguardLTE\Info $info)
        {
            \VanguardLTE\Info::where('id', $info->id)->delete();
            return redirect()->route('backend.info.list')->withSuccess(trans('app.info_deleted'));
        }
        public function security()
        {
        }
    }

}
