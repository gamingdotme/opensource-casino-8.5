<?php

namespace VanguardLTE\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    use HasFactory;

    static function getTerminals($where, $limit = 20)
    {
//        $user_tbl = '';
        $result = DB::table('users')
            ->select(
                'users.*'
            )
            ->whereRaw($where)
            ->paginate($limit);
        return $result;
    }
}
