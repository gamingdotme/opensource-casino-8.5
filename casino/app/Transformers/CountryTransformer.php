<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Country;

class CountryTransformer extends TransformerAbstract
{
    public function transform(Country $country)
    {
        return [
            'id' => (int) $country->id,
            'name' => $country->name,
            'full_name' => $country->full_name,
            'capital' => $country->capital,
            'citizenship' => $country->citizenship,
            'country_code' => (int) $country->country_code,
            'currency' => $country->currency,
            'currency_code' => $country->currency_code,
            'currency_sub_unit' => $country->currency_sub_unit,
            'currency_symbol' => $country->currency_symbol,
            'iso_3166_2' => $country->iso_3166_2,
            'iso_3166_3' => $country->iso_3166_3,
            'region_code' => (int) $country->region_code,
            'sub_region_code' => (int) $country->sub_region_code,
            'eea' => (boolean) $country->eea,
            'calling_code' => (int) $country->calling_code,
            'flag' => $country->flag ? url("flags/{$country->flag}") : null
        ];
    }
}
