<?php 
namespace VanguardLTE\Http\Middleware
{
    class IpMiddleware
    {
        public function handle($request, \Closure $next)
        {
            if( auth()->check() ) 
            {
                if( auth()->user()->hasRole([
                    'user', 
                    'cashier', 
                    'manager'
                ]) ) 
                {
                    if( !$request->key ) 
                    {
                        $response = Response::json(['error' => 'Key is empty or not exist'], 401, []);
                        $response->header('Content-Type', 'application/json');
                        return $response;
                    }
                    $key = \VanguardLTE\Api::where([
                        'keygen' => $request->key, 
                        'shop_id' => auth()->user()->shop_id, 
                        'status' => 1
                    ])->first();
                    if( !$key ) 
                    {
                        $response = Response::json(['error' => 'Key/Shop not exist'], 401, []);
                        $response->header('Content-Type', 'application/json');
                        return $response;
                    }
                    if( $key->ip && $request->ip() != $key->ip ) 
                    {
                        $response = Response::json(['error' => 'IP not in White List. Your ip is ' . $request->ip()], 401, []);
                        $response->header('Content-Type', 'application/json');
                        return $response;
                    }
                }
                else if( auth()->user()->hasRole([
                    'admin', 
                    'agent', 
                    'distributor'
                ]) ) 
                {
                    if( isset($request->shop_id) ) 
                    {
                        if( !$request->shop_id ) 
                        {
                            if( auth()->user()->hasRole(['distributor']) ) 
                            {
                                $response = Response::json(['error' => 'No access to shop'], 401, []);
                                $response->header('Content-Type', 'application/json');
                                return $response;
                            }
                            else
                            {
                                auth()->user()->update(['shop_id' => $request->shop_id]);
                            }
                        }
                        else
                        {
                            $shops = auth()->user()->shops_array(true);
                            if( !(count($shops) && in_array($request->shop_id, $shops)) ) 
                            {
                                $response = Response::json(['error' => 'No access to shop'], 401, []);
                                $response->header('Content-Type', 'application/json');
                                return $response;
                            }
                            if( auth()->user()->shop_id != $request->shop_id ) 
                            {
                                auth()->user()->update(['shop_id' => $request->shop_id]);
                            }
                        }
                    }
                    else
                    {
                        auth()->user()->update(['shop_id' => 0]);
                    }
                }
                else
                {
                    $response = Response::json(['error' => 'Wrong  role'], 401, []);
                    $response->header('Content-Type', 'application/json');
                    return $response;
                }
            }
            return $next($request);
        }
    }

}
