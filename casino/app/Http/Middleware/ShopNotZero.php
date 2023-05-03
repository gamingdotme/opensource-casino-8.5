<?php 
namespace VanguardLTE\Http\Middleware
{
    class ShopNotZero
    {
        public function handle($request, \Closure $next)
        {
            if( !auth()->check() ) 
            {
                return $next($request);
            }
            if( auth()->user()->shop_id == 0 ) 
            {
                abort(403);
            }
            return $next($request);
        }
    }

}
