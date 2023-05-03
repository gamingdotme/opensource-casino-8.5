<?php

namespace VanguardLTE\Repositories\Country;

use VanguardLTE\Country;

class EloquentCountry implements CountryRepository
{
    /**
     * {@inheritdoc}
     */
    public function lists($column = 'name', $key = 'id')
    {
        return Country::orderBy('name')->pluck($column, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Country::all();
    }
}
