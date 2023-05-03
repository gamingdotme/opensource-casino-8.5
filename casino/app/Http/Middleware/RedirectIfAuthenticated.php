<?php 
namespace VanguardLTE\Http\Middleware
{
    class RedirectIfAuthenticated
    {
        protected $auth = null;
        public function __construct(\Illuminate\Contracts\Auth\Guard $auth)
        {
            $this->auth = $auth;
        }
        public function handle($request, \Closure $next)
        {
            if( $this->auth->check() ) 
            {
                return redirect('/');
            }
            return $next($request);
        }
    }

}
