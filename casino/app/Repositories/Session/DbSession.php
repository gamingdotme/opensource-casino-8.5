<?php

namespace VanguardLTE\Repositories\Session;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use VanguardLTE\Repositories\User\UserRepository;
use DB;

class DbSession implements SessionRepository
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var Agent
     */
    private $agent;

    /**
     * DbSession constructor.
     * @param UserRepository $users
     * @param Agent $agent
     */
    public function __construct(UserRepository $users, Agent $agent)
    {
        $this->users = $users;
        $this->agent = $agent;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserSessions($userId)
    {
        $validTimestamp = Carbon::now()->subMinutes(config('session.lifetime'))->timestamp;

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', $validTimestamp)
            ->get();
        /*
            ->map(function ($session) {
                return $this->mapSessionAttributes($session);
            });
        */
    }

    private function mapSessionAttributes($session)
    {
        $this->agent->setUserAgent($session->user_agent);

        $session->last_activity = Carbon::createFromTimestamp($session->last_activity);
        $session->platform = $this->agent->platform();
        $session->browser = $this->agent->browser();
        $session->device = $this->agent->device();

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateSession($sessionId)
    {
        $user = $this->users->findBySessionId($sessionId);

        DB::table('sessions')
            ->where('id', $sessionId)
            ->delete();

        $this->users->update($user->id, ['remember_token' => null]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($sessionId)
    {
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();

        return $session
            ? $this->mapSessionAttributes($session)
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateAllSessionsForUser($userId)
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->delete();

        $this->users->update($userId, ['remember_token' => null]);
    }
}
