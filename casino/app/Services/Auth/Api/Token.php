<?php

namespace VanguardLTE\Services\Auth\Api;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'api_tokens';

    public $incrementing = false;
}
