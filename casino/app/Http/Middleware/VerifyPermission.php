<?php 
namespace VanguardLTE\Http\Middleware
{
    class VerifyPermission
    {
        protected $auth = null;
        public function __construct(\Illuminate\Contracts\Auth\Guard $auth)
        {
            $this->auth = $auth;
        }
        public function handle($request, \Closure $next, $permission)
        {
            if( $this->auth->check() && $this->auth->user()->hasPermission($permission) ) 
            {
                return $next($request);
            }
            $response = Response::json(['error' => __('app.no_permission')], 403, []);
            $response->header('Content-Type', 'application/json');
            return $response;
            return response()->json(['error' => __('app.no_permission')]);
            exit();
        }
    }

}
