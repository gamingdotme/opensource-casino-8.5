<?php

namespace VanguardLTE\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use VanguardLTE\User;

class SessionTransformer extends TransformerAbstract
{
    public function transform($session)
    {
        return [
            'id' => $session->id,
            'user_id' => (int) $session->user_id,
            'ip_address' => $session->ip_address,
            'user_agent' => $session->user_agent,
            'browser' => $session->browser,
            'platform' => $session->platform,
            'device' => $session->device,
            'last_activity' => (string) $session->last_activity
        ];
    }
}
