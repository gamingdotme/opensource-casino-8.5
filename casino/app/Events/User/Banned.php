<?php

namespace VanguardLTE\Events\User;

use VanguardLTE\Support\Enum\UserStatus;
use VanguardLTE\User;

class Banned
{
    /**
     * @var User
     */
    protected $bannedUser;

    public function __construct(User $bannedUser)
    {
        $this->bannedUser = $bannedUser;

        /*
        $users = User::whereIn('id', [$bannedUser->id] + $bannedUser->hierarchyUsers())->get();
        if($users){
            foreach($users AS $userElem){
                \DB::table('sessions')
                    ->where('user_id', $userElem->id)
                    ->delete();
                $userElem->update(['remember_token' => null, 'status' => UserStatus::BANNED]);
            }
        }
        */
    }

    /**
     * @return User
     */
    public function getBannedUser()
    {
        return $this->bannedUser;
    }
}
