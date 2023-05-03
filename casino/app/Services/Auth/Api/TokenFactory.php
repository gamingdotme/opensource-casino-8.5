<?php

namespace VanguardLTE\Services\Auth\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use VanguardLTE\User;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class TokenFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ConfigContract
     */
    private $config;

    /**
     * TokenFactory constructor.
     * @param Request $request
     * @param ConfigContract $config
     */
    public function __construct(Request $request, ConfigContract $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Create new token for specified user.
     * @param User $user
     * @return Token
     */
    public function forUser(User $user)
    {
        $ttl = $this->config->get('jwt.ttl');

        $token = (new Token)->forceFill([
            'id' => str_random(40),
            'user_id' => $user->id,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->getUserAgent(),
            'expires_at' => is_null($ttl) ? null : Carbon::now()->addMinutes($ttl)
        ]);

        $token->save();

        if ($this->shouldCleanUp()) {
            Token::where('expires_at', '<=', Carbon::now())->delete();
        }

        return $token;
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
     * Determine if we should clean up expired tokens.
     * @return bool
     */
    protected function shouldCleanUp()
    {
        $lottery = $this->config->get('jwt.lottery');

        return random_int(1, $lottery[1]) <= $lottery[0];
    }
}
