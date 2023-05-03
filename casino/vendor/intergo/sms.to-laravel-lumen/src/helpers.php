<?php

if (!function_exists('smsto')) {
	function smsto()
	{
		return app(\Intergo\SmsTo\SmsTo::class);
	}
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}
