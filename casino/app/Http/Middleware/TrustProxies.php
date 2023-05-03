<?php 
namespace VanguardLTE\Http\Middleware
{
    class TrustProxies extends \Fideloper\Proxy\TrustProxies
    {
        protected $proxies = null;
        protected $headers = \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL;
    }

}
