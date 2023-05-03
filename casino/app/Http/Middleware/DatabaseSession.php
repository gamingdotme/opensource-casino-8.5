<?php 
namespace VanguardLTE\Http\Middleware
{
    class DatabaseSession
    {
        public function handle($request, \Closure $next)
        {
            if( config('session.driver') != 'database' ) 
            {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('The entity you are looking for does not exist.');
            }
            return $next($request);
        }
    }

}
