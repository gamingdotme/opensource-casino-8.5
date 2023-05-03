<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class PermissionsController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $roles = null;
        private $permissions = null;
        public function __construct(\VanguardLTE\Repositories\Role\RoleRepository $roles, \VanguardLTE\Repositories\Permission\PermissionRepository $permissions)
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
            $this->roles = $roles;
            $this->permissions = $permissions;
        }
        public function index()
        {
            $roles = \VanguardLTE\Role::get();
            $permissions = \jeremykenedy\LaravelRoles\Models\Permission::orderBy('rank', 'asc')->get();
            $data = [
                '1' => [], 
                '2' => [], 
                '27' => [], 
                '9' => [], 
                '22' => [], 
                '15' => [], 
                '18' => [], 
                '17' => [], 
                '25' => [], 
                '26' => [], 
                '7' => [], 
                '14' => [], 
                '5' => [], 
                [], 
                '29' => [], 
                '12' => [], 
                '19' => [], 
                '8' => []
            ];
            $hidden = [];
            foreach( $permissions as $permission ) 
            {
                if( !in_array($permission->id, [
                    1, 
                    2, 
                    120
                ]) ) 
                {
                    $data[$permission->group_id][] = $permission;
                }
                else
                {
                    $hidden[] = $permission;
                }
            }
            return view('backend.permission.index', compact('roles', 'data', 'hidden'));
        }
        public function create()
        {
            $edit = false;
            return view('backend.permission.add-edit', compact('edit'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $validatedData = $request->validate([
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:permissions', 
                'name' => 'required'
            ]);
            \jeremykenedy\LaravelRoles\Models\Permission::create($request->all());
            return redirect()->route('backend.permission.index')->withSuccess(trans('app.permission_created_successfully'));
        }
        public function edit(\jeremykenedy\LaravelRoles\Models\Permission $permission)
        {
            $edit = true;
            return view('backend.permission.add-edit', compact('edit', 'permission'));
        }
        public function update(\jeremykenedy\LaravelRoles\Models\Permission $permission, \Illuminate\Http\Request $request)
        {
            $validatedData = $request->validate([
                'slug' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:permissions,slug,' . $permission->id, 
                'name' => 'required'
            ]);
            $permission->update($request->all());
            return redirect()->route('backend.permission.index')->withSuccess(trans('app.permission_updated_successfully'));
        }
        public function delete(\jeremykenedy\LaravelRoles\Models\Permission $permission)
        {
            $permission->delete();
            return redirect()->route('backend.permission.index')->withSuccess(trans('app.permission_deleted_successfully'));
        }
        public function saveRolePermissions(\Illuminate\Http\Request $request)
        {
            $roles = $request->get('roles');
            $allRoles = \VanguardLTE\Role::get();
            foreach( $allRoles as $role ) 
            {
                $permissions = array_get($roles, $role->id, []);
                if( $role->id == 6 ) 
                {
                    $key = array_search(131, $permissions);
                    if( $key ) 
                    {
                        unset($permissions[$key]);
                    }
                }
                $role->syncPermissions($permissions);
            }
            event(new \VanguardLTE\Events\Role\PermissionsUpdated());
            return redirect()->route('backend.permission.index')->withSuccess(trans('app.permissions_saved_successfully'));
        }
        public function security()
        {
        }
    }

}
