<?php 
namespace VanguardLTE\Http\Controllers\Api\Authorization
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RolesController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        private $roles = null;
        public function __construct(\VanguardLTE\Repositories\Role\RoleRepository $roles)
        {
            $this->roles = $roles;
            $this->middleware('auth');
            $this->middleware('permission_api:roles.manage');
        }
        public function index()
        {
            return $this->respondWithCollection($this->roles->getAllWithUsersCount(), new \VanguardLTE\Transformers\RoleTransformer());
        }
        public function store(\VanguardLTE\Http\Requests\Role\CreateRoleRequest $request)
        {
            $role = $this->roles->create($request->only([
                'name', 
                'display_name', 
                'description'
            ]));
            return $this->respondWithItem($role, new \VanguardLTE\Transformers\RoleTransformer());
        }
        public function show(\VanguardLTE\Role $role)
        {
            return $this->respondWithItem($role, new \VanguardLTE\Transformers\RoleTransformer());
        }
        public function update(\VanguardLTE\Role $role, \VanguardLTE\Http\Requests\Role\UpdateRoleRequest $request)
        {
            $input = collect($request->all());
            $role = $this->roles->update($role->id, $input->only([
                'name', 
                'display_name', 
                'description'
            ])->toArray());
            return $this->respondWithItem($role, new \VanguardLTE\Transformers\RoleTransformer());
        }
        public function destroy(\VanguardLTE\Role $role, \VanguardLTE\Repositories\User\UserRepository $users, \VanguardLTE\Http\Requests\Role\RemoveRoleRequest $request)
        {
            $userRole = $this->roles->findByName('User');
            $users->switchRolesForUsers($role->id, $userRole->id);
            $this->roles->delete($role->id);
            Cache::flush();
            return $this->respondWithSuccess();
        }
    }

}
