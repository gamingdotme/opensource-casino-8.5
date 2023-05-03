<?php 
namespace VanguardLTE\Http\Middleware
{
    class Checker
    {
        public function handle($request, \Closure $next)
        {
            if( !auth()->check() ) 
            {
                return $next($request);
            }
            if( $request->session()->has('beforeUser') ) 
            {
                return $next($request);
            }
            $user = \VanguardLTE\User::find(auth()->user()->id);
            $user->update(['last_online' => date('Y-m-d H:i:s')]);
            if( !auth()->user()->hasRole('user') ) 
            {
                return $next($request);
            }
            $activity = \VanguardLTE\Services\Logging\UserActivity\Activity::where([
                'type' => 'user', 
                'user_id' => auth()->user()->id
            ])->orderBy('id', 'DESC')->first();
            if( !$activity ) 
            {
                return $next($request);
            }
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            if( auth()->user()->hasRole('user') && !count($shop->countries) && !count($shop->oss) && !count($shop->devices) ) 
            {
                return $next($request);
            }
            if( !auth()->user()->hasRole('user') ) 
            {
                return $next($request);
            }
            foreach( [
                'countries' => 'country', 
                'oss' => 'os', 
                'devices' => 'device'
            ] as $index => $item ) 
            {
                if( !count($shop->$index) ) 
                {
                    continue;
                }
                if( !($shop->access && $shop->$index->filter(function($value, $key) use ($activity, $item)
                {
                    return $value->$item == $activity->$item || strpos($activity->$item, $value->$item) !== false;
                })->count() || !$shop->access && !$shop->$index->filter(function($value, $key) use ($activity, $item)
                {
                    return $value->$item == $activity->$item || strpos($activity->$item, $value->$item) !== false;
                })->count()) ) 
                {
                    Auth::logout();
                    return redirect()->route('frontend.auth.login')->withErrors(trans('app.shop_is_not_available_to_you'));
                }
            }
            return $next($request);
        }
    }

}
