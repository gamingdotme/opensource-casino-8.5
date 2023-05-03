<?php 
namespace VanguardLTE\Http\Middleware
{
    class UseApiGuard
    {
        protected $auth = null;
        public function __construct(\Illuminate\Contracts\Auth\Factory $auth)
        {
            $this->auth = $auth;
        }
        public function handle($request, \Closure $next)
        {
            $this->auth->shouldUse('api');
            $request->headers->set('Accept', 'application/json');
            return $next($request);
        }
    }

}
