<?php

namespace VanguardLTE\Presenters;

use VanguardLTE\Support\Enum\UserStatus;
use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter
{
    public function name()
    {
        return sprintf("%s %s", $this->entity->first_name, $this->entity->last_name);
    }

    public function avatar()
    {
        if (! $this->entity->avatar) {
            return url('/back/img/profile.png');
        }

        return Str::contains($this->entity->avatar, ['http', 'gravatar'])
            ? $this->entity->avatar
            : url("upload/users/{$this->entity->avatar}");
    }

    public function birthday()
    {
        return $this->entity->birthday
            ? $this->entity->birthday->format(config('app.date_format'))
            : 'N/A';
    }

    public function fullAddress()
    {
        $address = '';
        $user = $this->entity;

        if ($user->address) {
            $address .= $user->address;
        }

        if ($user->country_id) {
            $address .= $user->address ? ", {$user->country->name}" : $user->country->name;
        }

        return $address ?: 'N/A';
    }

    public function lastLogin()
    {
        return $this->entity->last_login
            ? $this->entity->last_login->diffForHumans()
            : 'N/A';
    }

    /**
     * Determine css class used for status labels
     * inside the users table by checking user status.
     *
     * @return string
     */
    public function labelClass()
    {
        switch ($this->entity->status) {
            case UserStatus::ACTIVE:
                $class = 'green';
                break;

            case UserStatus::BANNED:
                $class = 'red';
                break;

            default:
                $class = 'yellow';
        }

        return $class;
    }
}
