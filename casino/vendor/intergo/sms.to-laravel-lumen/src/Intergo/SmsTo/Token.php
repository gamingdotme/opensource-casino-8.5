<?php 

namespace Intergo\SmsTo;

use Carbon\Carbon;
use Intergo\SmsTo\Http\Client as SmsToClient;

class Token {
    
    public function getAccessToken()
    {
        $accessToken = null;

        // Check if we have accessToken saved already
        if ( ! file_exists(storage_path('smsto-accessToken'))) {
            $client = new SmsToClient(
                config('smsto.client_id'),
                config('smsto.client_secret'),
                config('smsto.username'),
                config('smsto.password')
            );
            $response = $client->getAccessToken();

            if ($response) {
                if (isset($response['access_token'])) {
                    $date = Carbon::now()->addSeconds($response['expires_in']);
                    file_put_contents(storage_path('smsto-accessTokenExpiredOn'), $date->toDateTimeString());
                    file_put_contents(storage_path('smsto-accessToken'), $response['access_token']);
                    $accessToken = $response['access_token'];
                }
            }
        } else {
            $dateExpired = file_get_contents(storage_path('smsto-accessTokenExpiredOn'));
            if ($dateExpired > Carbon::now()->toDateTimeString()) {
                $accessToken = file_get_contents(storage_path('smsto-accessToken'));
            }
        }

        return $accessToken;
    }
}
