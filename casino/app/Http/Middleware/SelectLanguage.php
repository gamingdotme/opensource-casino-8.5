<?php 
namespace VanguardLTE\Http\Middleware
{
    class SelectLanguage
    {
        public function handle($request, \Closure $next)
        {
            if( auth()->check() ) 
            {
                \App::setLocale(auth()->user()->language);
            }
            
            if (isset($_COOKIE['language'])) {
                $laut = htmlspecialchars($_COOKIE['language']);
                \App::setLocale($laut);
            }
            else
            {
                \App::setLocale("en");
            }

            return $next($request);
        }
    }

}
