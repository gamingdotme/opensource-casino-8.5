<?php 
namespace VanguardLTE\Http\Middleware
{
    class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
    {
        protected $except = [
            '/game/*/server', 
            '/payment/interkassa/result', 
            '/payment/coinbase/result', 
            '/payment/btcpayserver/result', 
            '/sms/callback', 
            '/profile/contact',
            'register'
        ];
    }

}
