<?php 
namespace VanguardLTE\Http\Middleware
{
    class Registration
    {
        public function handle($request, \Closure $next)
        {
            if( !settings('reg_enabled') && false ) 
            {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
            return $next($request);
        }
    }

}
