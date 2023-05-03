<?php

namespace VanguardLTE\Services\Auth\Api;

use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

trait ExtendsJwtValidation
{
    protected $jtiIsValid;

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        $payload = parent::getPayload();

        $jti = $payload->get('jti');
        $id = $payload->get('sub');

        if (! $this->jtiIsValid($jti, $id)) {
            throw new TokenInvalidException("Invalid jti claim.");
        }

        return $payload;
    }

    /**
     * Check if jti claim is valid. For jti claim we use our random
     * generated token that is being stored inside the database, so
     * we can easily revoke it anytime we want.
     *
     * Remember, the jti claim is not a token itself that is being used
     * for API authentication. It is just a unique string (more like token ID)
     * attached to each JWT token to allow us to easily revoke that JWT token later.
     * @param $jti
     * @param $userId
     * @return bool
     */
    private function jtiIsValid($jti, $userId)
    {
        if (is_null($this->jtiIsValid)) {
            $count = Token::where('id', $jti)
                ->where('user_id', $userId)
                ->where('expires_at', '>', Carbon::now())
                ->count();

            $this->jtiIsValid = $count == 1;
        }


        return $this->jtiIsValid;
    }

    /**
     * {@inheritdoc}
     */
    public function invalidate($forceForever = true)
    {
        $this->requireToken();

        Token::where('id', $this->getClaim('jti'))->delete();

        $this->jtiIsValid = null;

        return $this;
    }
}
