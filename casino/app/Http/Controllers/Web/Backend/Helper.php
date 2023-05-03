<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class Helper extends Controller
{
    static function is_online($last_online)
    {
        $current_time   = time();
        $last_online    = strtotime($last_online);
        if (round(abs($current_time - $last_online) / 60, 2) <= 5) {
            $is_online = true;
        } else {
            $is_online = false;
        }
    }
}
