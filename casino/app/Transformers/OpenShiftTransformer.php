<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Repositories\Country\CountryRepository;
use VanguardLTE\Repositories\Role\RoleRepository;
use VanguardLTE\OpenShift;

class OpenShiftTransformer extends TransformerAbstract
{
    public function transform(OpenShift $stat)
    {

        $data = [
            'shift' => $stat->id,
            'user' => $stat->user ? $stat->user->username : '',
            'start_date' => $stat->start_date,
            'end_date' => $stat->end_date,
            ];

        if( !auth()->user()->hasRole('cashier') ){
            $data = $data + [
                    'credit' => $stat->balance,
                    'credit_in' => $stat->balance_in,
                    'credit_out' => $stat->balance_out,
                ];
        }

        $data = $data + [
            'credit_total' => number_format ($stat->balance + $stat->balance_in - $stat->balance_out, 4, ".", ""),
            'money' => $stat->end_date == NULL ? $stat->get_money() : $stat->users,
            'money_in' => $stat->money_in,
            'money_out' => $stat->money_out,
            'money_total' => number_format ($stat->money_in - $stat->money_out, 2, ".", ""),
            'transfers' => $stat->transfers,
            'payout' => number_format ($stat->money_in > 0 ? ($stat->money_out / $stat->money_in) * 100 : 0, 4, ".", "")
            ];

        if( auth()->user()->hasRole('admin') ){
            $data = $data + ['profit' => number_format ($stat->profit(), 4, ".", "") ];
        }


        return $data;
    }
}
