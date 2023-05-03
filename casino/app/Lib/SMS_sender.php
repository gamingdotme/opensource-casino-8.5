<?php
/**
 * Created by PhpStorm.
 * User: Omen
 * Date: 07.09.2019
 * Time: 17:15
 */

namespace VanguardLTE\Lib;

use Carbon\Carbon;
use GuzzleHttp\Client;
use VanguardLTE\SMS;

use GuzzleHttp\Exception\RequestException;
use VanguardLTE\SMSMailing;
use VanguardLTE\User;

class SMS_sender {

    public static function send($phone, $message, $user_id=false){

        if(!$user_id){
            $user_id = auth()->user()->id;
        }

        $user = User::where('id', $user_id)->first();

        $minutes = SMS::where('user_id', $user_id)->where('created_at', '>', Carbon::now()->subMinutes(settings('smsto_time'))->format('Y-m-d H:i:s'))->count();
        if($minutes){
            return ['error' => true, 'text' =>__('app.limit_between_messages', ['minutes' => settings('smsto_time')])];
        }
        $sms_day = SMS::where('user_id', $user_id)->where('created_at', '>', Carbon::now()->subDay()->format('Y-m-d H:i:s'))->count();
        if($sms_day > settings('smsto_limit')){
            return ['error' => true, 'text' =>__('app.limit_messages_per_day', ['minutes' => settings('smsto_limit')])];
        }

        if( !$user->hasRole('admin') && Filter::phone_filtered($phone)){
            return ['error' => true, 'text' =>__('app.blocked_phone_zone')];
        }

        /*
        if( !$user->hasRole('admin') && settings('phone_prefix_check') && count(settings('blocked_phone_prefixes'))){
            foreach (settings('blocked_phone_prefixes') AS $prefix){
                $prefix = preg_replace('/[^0-9]/', '', $prefix);
                if( strpos($phone, $prefix) === 0){
                    return ['error' => true, 'text' =>__('app.blocked_phone_zone')];
                }
            }
        }
        */

        $client = new Client([
            'base_uri' => config('smsto.base_url'),
            'timeout'  => 3,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . settings('smsto_client_api_key')
            ]
        ]);

        try{
            $result = $client->request('POST', '/sms/send',
                [
                    'form_params' => [
                        "message"   => $message,
                        "to" => $phone,
                        "sender_id" => config('smsto.sender_id'),
                        "callback_url" => route('sms.callback')

                    ]
                ]
            );
        } catch (RequestException $exception){
            Info($exception->getMessage());
            return ['error' => true, 'text' => __('app.something_went_wrong')];
        }


        $decode = json_decode($result->getBody()->getContents(), true);
        $code = $result->getStatusCode();

        return $decode;

    }

    public static function mailing(SMSMailing $mailing){

        $client = new Client([
            'base_uri' => config('smsto.base_url'),
            'timeout'  => 3,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . settings('smsto_client_api_key')
            ]
        ]);

        $data = [
            'messages' => [],
            'sender_id' => config('smsto.sender_id'),
            'callback_url' => '',
            //'scheduled_for' => $mailing->date_start,
            //'timezone' => config('app.timezone')
        ];

        if( $mailing->sms_messages ){
            foreach ($mailing->sms_messages AS $sms_message){
                if($sms_message->user->phone && !$sms_message->sent){
                    $message =  $mailing->message;
                    foreach(
                        [
                            'username' => $sms_message->user->username,
                            'email' => $sms_message->user->email,
                            'role' => $sms_message->user->role->name,
                            'balance' => $sms_message->user->balance,
                            'status' => $sms_message->user->status,
                        ] AS $key=>$value)
                    {
                        $message = str_replace(':' . $key, $value, $message);
                    }
                    $data['messages'][] = [
                        'message' => $message,
                        'to' => $sms_message->user->phone
                    ];
                    $sms_message->update(['sent' => 1]);
                }
            }
        }

        if( !count($data['messages']) ){
            return false;
        }

        $result = $client->request('POST', '/sms/send', [ 'form_params' => $data ]);
        $decode = json_decode($result->getBody()->getContents(), true);
        $code = $result->getStatusCode();

        if( $code == 200 ){
            return $decode;
        }

        return false;

    }


    public static function notify($message){

        if(!settings('smsto_alert_phone') && !settings('smsto_alert_phone_2') ){
            return ['error' => true, 'text' => __('app.something_went_wrong')];
        }

        $client = new Client([
            'base_uri' => config('smsto.base_url'),
            'timeout'  => 3,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . settings('smsto_client_api_key')
            ]
        ]);

        foreach (['smsto_alert_phone', 'smsto_alert_phone_2'] AS $field){

            if(!settings($field)){
                continue;
            }

            try{
                $res = $client->request('POST', '/sms/send',
                    [
                        'form_params' => [
                            "message"   => $message,
                            "to" => settings($field),
                            "sender_id" => config('smsto.sender_id'),
                            "callback_url" => route('sms.callback')

                        ]
                    ]
                );
            } catch (RequestException $exception){
                Info($exception->getMessage());
                return ['error' => true, 'text' => __('app.something_went_wrong')];
            }

        }

        return ['success' => true];

    }

}