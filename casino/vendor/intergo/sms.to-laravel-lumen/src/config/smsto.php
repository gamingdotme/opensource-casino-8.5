<?php

return [
    'client_id' => env('SMSTO_CLIENT_ID'),
    'client_secret' => env('SMSTO_CLIENT_SECRET'),
    'username'=> env('SMSTO_EMAIL'),
    'password' => env('SMSTO_PASSWORD'),
    'scope' => '*',
    'sender_id' => env('SMSTO_SENDER_ID'),
    'callback_url' => env('SMSTO_CALLBACK_URL'),
    'environment' => env('SMSTO_ENVIRONMENT', 'sandbox'),
    'base_url' => env('SMSTO_BASE_URL', 'https://api.sms.to/v1'),
];
