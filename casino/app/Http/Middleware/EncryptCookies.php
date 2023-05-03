<?php 
namespace VanguardLTE\Http\Middleware
{
    class EncryptCookies extends \Illuminate\Cookie\Middleware\EncryptCookies
    {
        protected $except = ['sidebar'];
    }

}
