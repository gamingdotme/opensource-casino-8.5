<?php 
namespace VanguardLTE\Http\Middleware
{
    class OnlyForAdmin
    {
        public function handle($request, \Closure $next)
        {
            if( !auth()->check() ) 
            {
                return $next($request);
            }
            if( auth()->user()->role_id != 6 ) 
            {
                abort(403);
            }
            return $next($request);
        }
    }

}
