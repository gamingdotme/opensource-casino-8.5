<?php 
namespace VanguardLTE\Http\Middleware
{
    class SocialLogin
    {
        public function handle($request, \Closure $next)
        {
            $provider = $request->route()->parameter('provider');
            if( !in_array($provider, config('auth.social.providers')) ) 
            {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
            return $next($request);
        }
    }

}
