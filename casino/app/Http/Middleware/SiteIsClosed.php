<?php 
namespace VanguardLTE\Http\Middleware
{
    class SiteIsClosed
    {
        public function handle($request, \Closure $next)
        {
            if( auth()->check() && auth()->user()->role_id == 6 ) 
            {
                return $next($request);
            }
            if( $request->session()->has('beforeUser') ) 
            {
                return $next($request);
            }
            if( settings('siteisclosed') ) 
            {
                return response()->view('system.pages.siteisclosed', [], 200)->header('Content-Type', 'text/html');
            }
            return $next($request);
        }
    }

}
