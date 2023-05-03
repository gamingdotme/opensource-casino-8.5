<?php 

namespace Intergo\SmsTo\Facades;

use Illuminate\Support\Facades\Facade;

class SmsToFacade extends Facade {
    
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-smsto';
    }
}