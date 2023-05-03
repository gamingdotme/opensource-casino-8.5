<?php

namespace VanguardLTE\Repositories\Activity;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

interface ActivityRepository
{
    /**
     * Log user activity.
     *
     * @param $data array Array with following fields:
     *      description (string) - Description of user activity.
     *      user_id (int) - User unique identifier.
     *      ip_address (string) - Ip address from which user is accessing the website.
     *      user_agent (string) - User's browser info.
     * @return mixed
     */
    public function log($data);

    /**
     * Paginate activities for user.
     *
     * @param $userId
     * @param int $perPage
     * @param null $search
     * @return mixed
     */
    public function paginateActivitiesForUser($userId, $perPage = 20, $search = null);

    /**
     * Get specified number of latest user activity logs.
     *
     * @param $userId
     * @param int $activitiesCount
     * @return mixed
     */
    public function getLatestActivitiesForUser($userId, $activitiesCount = 10);

    /**
     * Paginate all activity records.
     *
     * @param int $perPage
     * @param null $search
     * @return Paginator
     */
    public function paginateActivities($perPage = 20, $search = null, $userids = []);

    /**
     * Get count of user activities per day for given period of time.
     *
     * @param $userId
     * @param $from
     * @param $to
     * @return mixed
     */
    public function userActivityForPeriod($userId, Carbon $from, Carbon $to);
}