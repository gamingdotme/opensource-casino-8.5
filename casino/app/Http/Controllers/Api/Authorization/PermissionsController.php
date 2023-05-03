<?php 
namespace VanguardLTE\Http\Controllers\Api\Authorization
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class PermissionsController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        private $permissions = null;
        public function __construct(\VanguardLTE\Repositories\Permission\PermissionRepository $permissions)
        {
            $this->permissions = $permissions;
            $this->middleware('auth');
            $this->middleware('permission_api:permissions.manage');
        }
        public function index()
        {
            return $this->respondWithCollection($this->permissions->all(), new \VanguardLTE\Transformers\PermissionTransformer());
        }
        public function store(\VanguardLTE\Http\Requests\Permission\CreatePermissionRequest $request)
        {
            $permission = $this->permissions->create($request->only([
                'name', 
                'display_name', 
                'description'
            ]));
            return $this->respondWithItem($permission, new \VanguardLTE\Transformers\PermissionTransformer());
        }
        public function show(\VanguardLTE\Permission $permission)
        {
            return $this->respondWithItem($permission, new \VanguardLTE\Transformers\PermissionTransformer());
        }
        public function update(\VanguardLTE\Permission $permission, \VanguardLTE\Http\Requests\Permission\UpdatePermissionRequest $request)
        {
            $input = collect($request->all());
            $permission = $this->permissions->update($permission->id, $input->only([
                'name', 
                'display_name', 
                'description'
            ])->toArray());
            return $this->respondWithItem($permission, new \VanguardLTE\Transformers\PermissionTransformer());
        }
        public function destroy(\VanguardLTE\Permission $permission, \VanguardLTE\Http\Requests\Permission\RemovePermissionRequest $request)
        {
            $this->permissions->delete($permission->id);
            return $this->respondWithSuccess();
        }
    }

}
