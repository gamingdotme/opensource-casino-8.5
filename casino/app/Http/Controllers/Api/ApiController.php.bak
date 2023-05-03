<?php 
namespace VanguardLTE\Http\Controllers\Api
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    abstract class ApiController extends \VanguardLTE\Http\Controllers\Controller
    {
        protected $statusCode = 200;
        protected $fractal = null;
        protected function fractal()
        {
            if( $this->fractal ) 
            {
                return $this->fractal;
            }
            $fractal = app('League\Fractal\Manager');
            $fractal->setRecursionLimit(2);
            $fractal->setSerializer(new \VanguardLTE\Support\DataArraySerializer());
            if( $includes = request('include') ) 
            {
                $fractal->parseIncludes($includes);
            }
            return $this->fractal = $fractal;
        }
        public function getStatusCode()
        {
            return $this->statusCode;
        }
        public function setStatusCode($statusCode)
        {
            $this->statusCode = $statusCode;
            return $this;
        }
        protected function respondWithItem($item, $callback)
        {
            if( $includes = $this->getValidIncludes($callback) ) 
            {
                $item->load($includes);
            }
            $resource = new \League\Fractal\Resource\Item($item, $callback);
            $rootScope = $this->fractal()->createData($resource);
            return $this->respondWithArray($rootScope->toArray());
        }
        protected function respondWithCollection($collection, $callback)
        {
            if( $includes = $this->getValidIncludes($callback) ) 
            {
                $collection->load($includes);
            }
            $resource = new \League\Fractal\Resource\Collection($collection, $callback);
            $rootScope = $this->fractal()->createData($resource);
            return $this->respondWithArray($rootScope->toArray());
        }
        protected function respondWithPagination(\Illuminate\Contracts\Pagination\Paginator $paginator, $callback)
        {
            if( $includes = $this->getValidIncludes($callback) ) 
            {
                $paginator->load($includes);
            }
            $queryParams = array_diff_key($_GET, array_flip(['page']));
            $paginator->appends($queryParams);
            $resource = new \League\Fractal\Resource\Collection($paginator, $callback, 'data');
            $resource->setPaginator(new \League\Fractal\Pagination\IlluminatePaginatorAdapter($paginator));
            $rootScope = $this->fractal()->createData($resource);
            return $this->respondWithArray($rootScope->toArray());
        }
        private function getValidIncludes($callback)
        {
            $includes = $this->fractal()->getRequestedIncludes();
            if( !$includes ) 
            {
                return null;
            }
            return array_intersect($includes, $callback->getAvailableIncludes());
        }
        protected function respondWithSuccess($statusCode = 200)
        {
            return $this->setStatusCode($statusCode)->respondWithArray(['success' => true]);
        }
        protected function respondWithArray(array $array, array $headers = [])
        {
            $response = \Response::json($array, $this->statusCode, $headers);
            $response->header('Content-Type', 'application/json');
            return $response;
        }
        protected function respondWithError($message)
        {
            if( $this->statusCode === 200 ) 
            {
                trigger_error('You better have a really good reason for erroring on a 200...', E_USER_WARNING);
            }
            return $this->respondWithArray(['error' => $message]);
        }
        public function errorForbidden($message = 'Forbidden')
        {
            return $this->setStatusCode(403)->respondWithError($message);
        }
        public function errorInternalError($message = 'Internal Error')
        {
            return $this->setStatusCode(500)->respondWithError($message);
        }
        public function errorNotFound($message = 'Resource Not Found')
        {
            return $this->setStatusCode(404)->respondWithError($message);
        }
        public function errorUnauthorized($message = 'Unauthorized')
        {
            return $this->setStatusCode(401)->respondWithError($message);
        }
        public function errorWrongArgs($message = 'Wrong Arguments')
        {
            return $this->setStatusCode(400)->respondWithError($message);
        }
    }

}
