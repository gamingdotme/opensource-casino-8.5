<?php 
namespace VanguardLTE\Http\Middleware
{
    class VerifyInstallation
    {
        public function handle($request, \Closure $next)
        {
            if( !file_exists(base_path('.env')) && !$request->is('install*') ) 
            {
                return redirect()->to('install');
            }
            if( file_exists(base_path('.env')) && $request->is('install*') && !$request->is('install/complete') ) 
            {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
            return $next($request);
        }
    }

}
