<?php 
namespace VanguardLTE\Http\Middleware
{
    class CheckRole
    {
        protected $auth = null;
        public function __construct(\Illuminate\Contracts\Auth\Guard $auth)
        {
            $this->auth = $auth;
        }
        public function handle($request, \Closure $next, $role)
        {
            if( $this->auth->guest() || !$request->user()->hasRole($role) ) 
            {
                abort(403);
            }
            return $next($request);
        }
    }

}
