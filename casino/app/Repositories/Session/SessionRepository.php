<?php

namespace VanguardLTE\Repositories\Session;

interface SessionRepository
{
    /**
     * Find session by id.
     * @param $sessionId
     * @return mixed
     */
    public function find($sessionId);

    /**
     * Get all active sessions for specified user.
     *
     * @param $userId
     * @return mixed
     */
    public function getUserSessions($userId);

    /**
     * Invalidate specified session for provided user
     *
     * @param $sessionId
     * @return mixed
     */
    public function invalidateSession($sessionId);

    /**
     * Invalidate all sessions for user with given id.
     * @param $userId
     * @return mixed
     */
    public function invalidateAllSessionsForUser($userId);
}
