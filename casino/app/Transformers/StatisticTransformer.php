<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\Statistic;
use VanguardLTE\Transaction;

class StatisticTransformer extends TransformerAbstract
{
    public function transform(Statistic $statistic)
    {

        $return = [];
        $fields = ['admin', 'agent', 'title', 'distributor','shop','cashier','type','user',
            'agent_in', 'agent_out', 'distributor_in', 'distributor_out', 'type_in', 'type_out',
            'credit_in', 'credit_out', 'money_in', 'money_out', 'created_at'];
        foreach($fields AS $field){
            $return[$field] = '';
        }

        $return['type'] = $statistic->type;
        $return['title'] = $statistic->title;
        $return['created_at'] = $statistic->created_at;

        if( $statistic->system == 'shop'){
            if( $statistic->user && $statistic->user->hasRole(['distributor'])){
                $return['distributor'] = $statistic->user->username;
            }
            if( $statistic->shop ) {
                $return['shop'] = $statistic->shop->name;
            }
        }
        if( in_array($statistic->system, ['jpg','bank'])){
            if( $statistic->payeer && $statistic->payeer->hasRole(['admin'])){
                $return['admin'] = $statistic->payeer->username;
            }elseif( $statistic->user && $statistic->user->hasRole(['admin'])){
                $return['admin'] = $statistic->user->username;
            }
        }
        if( in_array($statistic->system, ['user','handpay','interkassa','coinbase','btcpayserver','invite','progress','tournament','welcome_bonus','sms_bonus','wheelfortune'])){
            if( $statistic->payeer && $statistic->payeer->hasRole(['admin'])){
                $return['admin'] = $statistic->payeer->username;
            }elseif( $statistic->user && $statistic->user->hasRole(['admin'])){
                $return['admin'] = $statistic->user->username;
            }

            if( $statistic->payeer && $statistic->payeer->hasRole(['agent'])){
                $return['agent'] = $statistic->payeer->username;
            }elseif( $statistic->user && $statistic->user->hasRole(['agent'])){
                $return['agent'] = $statistic->user->username;
            }

            if( $statistic->user && $statistic->user->hasRole(['distributor'])){
                $return['distributor'] = $statistic->user->username;
            }

            if( $statistic->payeer && $statistic->payeer->hasRole(['cashier'])){
                $return['cashier'] = $statistic->payeer->username;
            }

            if( $statistic->user && $statistic->user->hasRole(['user'])){
                $return['user'] = $statistic->user->username;
            }
        }

        if($statistic->add){
            foreach(['agent_in', 'agent_out', 'distributor_in', 'distributor_out', 'type_in', 'type_out',
                        'credit_in', 'credit_out', 'money_in', 'money_out'] AS $field){
                $return[$field] = $statistic->add->$field != null ?$statistic->add->$field : '';
            }
        }


        if( !auth()->user()->hasRole('admin') ){
            unset($return['admin']);
            unset($return['type_in']);
            unset($return['type_out']);
        }

        if( !auth()->user()->hasRole(['admin','agent']) ){
            unset($return['agent']);
            unset($return['agent_in']);
            unset($return['agent_out']);
        }

        if( !auth()->user()->hasRole(['admin','agent','distributor']) ){
            unset($return['distributor']);
            unset($return['distributor_in']);
            unset($return['distributor_out']);

            unset($return['shop']);
            unset($return['credit_in']);
            unset($return['credit_out']);
        }


        return $return;

    }
}
