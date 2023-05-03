<?php

namespace VanguardLTE\Services\Logging\UserActivity;

use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Contracts\Auth\Factory;
use VanguardLTE\Lib\GeoData;
use VanguardLTE\Repositories\Activity\ActivityRepository;
use VanguardLTE\User;
use Illuminate\Http\Request;

use VanguardLTE\Helpers\UserSystemInfoHelper;
use GeoIp2\Database\Reader;

use GeoIp2\Exception\InvalidRequestException;

class Logger
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Factory
     */
    private $auth;

    /**
     * @var User|null
     */
    protected $user = null;
    /**
     * @var ActivityRepository
     */
    private $activities;

    public function __construct(Request $request, Factory $auth, ActivityRepository $activities)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->activities = $activities;
    }

    /**
     * Log user action.
     *
     * @param $description
     * @return static
     */
    public function log($description, $original = '', $type = 'user', $system = null, $item_id = null, $shop_id = false)
    {

        //$geo = geoip()->getLocation();

        if (session()->exists('beforeUser')){
            return true;
        }

        $data = GeoData::get_data();

        if(!$shop_id){
            $shop_id = auth()->check() ? auth()->user()->shop_id : $this->getShopId();
        }

        return $this->activities->log([
            'old' => $original,
            'description' => $description,
            'type' => $type,
            'system' => $system,
            'item_id' => $item_id,
            'user_id' => auth()->check() ? $this->getUserId() : 1,
            'shop_id' => $shop_id,
            'ip_address' => $this->request->server('REMOTE_ADDR'),
            'user_agent' => $this->getUserAgent()
        ] + $data);
    }

    /**
     * Get id if the user for who we want to log this action.
     * If user was manually set, then we will just return id of that user.
     * If not, we will return the id of currently logged user.
     *
     * @return int|mixed|null
     */
    private function getUserId()
    {
        if ($this->user) {
            return $this->user->id;
        }

        $id = 1;

        try{
            $id = $this->auth->guard()->id();
        } catch (\Exception $e) {
            $id = 1;
        }

        return $id;
    }

    private function getShopId()
    {
        if ($this->user) {
            return $this->user->shop_id;
        }

        try{
            $shop_id = $this->auth->guard()->user()->shop_id;
        } catch (\Exception $e) {
            $shop_id = 0;
        }

        return $shop_id;
    }

    /**
     * Get user agent from request headers.
     *
     * @return string
     */
    private function getUserAgent()
    {
        return substr((string) $this->request->header('User-Agent'), 0, 500);
    }

    /**
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
