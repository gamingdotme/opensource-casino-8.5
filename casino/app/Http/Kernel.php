<?php 
namespace VanguardLTE\Http
{
    class Kernel extends \Illuminate\Foundation\Http\Kernel
    {
        protected $middleware = [
            'VanguardLTE\Http\Middleware\VerifyInstallation', 
            'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode', 
            'VanguardLTE\Http\Middleware\TrimStrings', 
            'Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull', 
            'VanguardLTE\Http\Middleware\TrustProxies'
        ];
        protected $middlewareGroups = [
            'web' => [
                'VanguardLTE\Http\Middleware\EncryptCookies', 
                'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse', 
                'Illuminate\Session\Middleware\StartSession', 
                'Illuminate\View\Middleware\ShareErrorsFromSession', 
                'VanguardLTE\Http\Middleware\VerifyCsrfToken', 
                'Illuminate\Routing\Middleware\SubstituteBindings', 
                'VanguardLTE\Http\Middleware\SelectLanguage'
            ], 
            'api' => [
                'VanguardLTE\Http\Middleware\UseApiGuard', 
                'throttle:60,1', 
                'bindings'
            ]
        ];
        protected $routeMiddleware = [
            'auth' => 'VanguardLTE\Http\Middleware\Authenticate', 
            'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth', 
            'guest' => 'VanguardLTE\Http\Middleware\RedirectIfAuthenticated', 
            'registration' => 'VanguardLTE\Http\Middleware\Registration', 
            'session.database' => 'VanguardLTE\Http\Middleware\DatabaseSession', 
            'bindings' => 'Illuminate\Routing\Middleware\SubstituteBindings', 
            'throttle' => 'Illuminate\Routing\Middleware\ThrottleRequests', 
            'cache.headers' => 'Illuminate\Http\Middleware\SetCacheHeaders', 
            'role' => 'jeremykenedy\LaravelRoles\App\Http\Middleware\VerifyRole', 
            'permission' => 'jeremykenedy\LaravelRoles\App\Http\Middleware\VerifyPermission', 
            'level' => 'jeremykenedy\LaravelRoles\App\Http\Middleware\VerifyLevel', 
            'ipcheck' => 'VanguardLTE\Http\Middleware\IpMiddleware', 
            'siteisclosed' => 'VanguardLTE\Http\Middleware\SiteIsClosed', 
            'localization' => 'VanguardLTE\Http\Middleware\SelectLanguage', 
            'shopzero' => 'VanguardLTE\Http\Middleware\ShopZero', 
            'shop_not_zero' => 'VanguardLTE\Http\Middleware\ShopNotZero', 
            'only_for_admin' => 'VanguardLTE\Http\Middleware\OnlyForAdmin', 
            'permission_api' => 'VanguardLTE\Http\Middleware\VerifyPermission', 
            'checker' => 'VanguardLTE\Http\Middleware\Checker', 
            '2fa' => 'PragmaRX\Google2FALaravel\Middleware'
        ];
    }

}
