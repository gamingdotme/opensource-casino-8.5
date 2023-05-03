<?php 
namespace VanguardLTE\Http\Middleware
{
    class CheckPermissions
    {
        protected $auth = null;
        public function __construct(\Illuminate\Contracts\Auth\Guard $auth)
        {
            $this->auth = $auth;
        }
        public function handle($request, \Closure $next, $permissions)
        {
            if( !is_array($permissions) ) 
            {
                $permissions = explode('|', $permissions);
            }
            if( $this->auth->guest() || !$request->user()->hasPermission($permissions) ) 
            {
                abort(403, 'Forbidden.');
            }
            return $next($request);
        }
    }

}
