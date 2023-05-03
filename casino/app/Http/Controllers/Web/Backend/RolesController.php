<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RolesController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $roles = null;
        public function __construct(\VanguardLTE\Repositories\Role\RoleRepository $roles)
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->roles = $roles;
        }
        public function index()
        {
            return redirect()->route('backend.dashboard');
            $roles = $this->roles->getAllWithUsersCount();
            return view('backend.role.index', compact('roles'));
        }
        public function create()
        {
            $edit = false;
            return redirect()->route('backend.dashboard');
            return view('backend.role.add-edit', compact('edit'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $validatedData = $request->validate([
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:roles', 
                'name' => 'required'
            ]);
            \jeremykenedy\LaravelRoles\Models\Role::create($request->all());
            return redirect()->route('backend.role.index')->withSuccess(trans('app.role_created'));
        }
        public function edit(\jeremykenedy\LaravelRoles\Models\Role $role)
        {
            $edit = true;
            return redirect()->route('backend.dashboard');
            return view('backend.role.add-edit', compact('edit', 'role'));
        }
        public function update(\jeremykenedy\LaravelRoles\Models\Role $role, \Illuminate\Http\Request $request)
        {
            $validatedData = $request->validate([
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:roles,slug,' . $permission->id, 
                'name' => 'required'
            ]);
            $role->update($request->all());
            return redirect()->route('backend.role.index')->withSuccess(trans('app.role_updated'));
        }
        public function delete(\jeremykenedy\LaravelRoles\Models\Role $role)
        {
            return redirect()->route('backend.dashboard');
            $role->detachAllPermissions();
            $role->delete();
            return redirect()->route('backend.role.index')->withSuccess(trans('app.role_deleted'));
        }
        public function security()
        {
        }
    }

}
