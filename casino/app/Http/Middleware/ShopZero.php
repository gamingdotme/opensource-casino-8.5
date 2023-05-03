<?php 
namespace VanguardLTE\Http\Middleware
{
    class ShopZero
    {
        public function handle($request, \Closure $next)
        {
            if( auth()->check() ) 
            {
            }
            if( auth()->user()->shop_id == 0 ) 
            {
                if( auth()->user()->role_id == 6 && ($request->is('backend/progress*') || $request->is('backend/daily_entries*') || $request->is('backend/invite*') || $request->is('backend/wheelfortune*') || $request->is('backend/happyhours*') || $request->is('backend/refunds*') || $request->is('backend/category*') || $request->is('backend/banks*') || $request->is('backend/pincodes*') || $request->is('backend/jpgame*') || $request->is('backend/game*') || $request->is('backend/permission*') || $request->is('backend/welcome_bonuses*') || $request->is('backend/smsbonuses*') || $request->is('backend/settings*')) ) 
                {
                    return $next($request);
                }
                else
                {
                    abort(403);
                    return redirect()->route('backend.dashboard')->withErrors([trans('app.no_permission')]);
                }
            }
            return $next($request);
        }
    }

}
