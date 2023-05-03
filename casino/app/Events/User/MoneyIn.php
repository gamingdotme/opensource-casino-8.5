<?php

namespace VanguardLTE\Events\User;

use Carbon\Carbon;
use VanguardLTE\Invite;
use VanguardLTE\Reward;
use VanguardLTE\User;

class MoneyIn
{
    /**
     * @var User
     */
    protected $user;
    protected $sum;

    public function __construct(User $user, $sum)
    {
        $this->user = $user;
        $this->sum = $sum;

        if( $user->shop && $user->shop->invite_active ){

            $inviter = Invite::where(['shop_id' => $user->shop_id])->first();
            if( $inviter && $user->inviter_id > 0 ){

                $reward = Reward::where(['user_id' => $user->inviter_id, 'referral_id' => $user->id])->first();
                if(!$reward){
                    $reward = Reward::create([
                        'user_id' => $user->inviter_id,
                        'referral_id' => $user->id,
                        'sum' => $inviter->sum,
                        'ref_sum' => $inviter->sum_ref,
                        'until' => Carbon::now()->addDays($inviter->waiting_time),
                        'shop_id' => $user->shop_id
                    ]);
                }

                if( $inviter->type == 'one_pay' && $sum >= $inviter->min_amount ){
                    $reward->update(['payed' => $sum]);
                }
                if( $inviter->type == 'sum_pay' ){
                    $reward->increment('payed', $sum);
                }

                if( $reward->payed >= $inviter->min_amount ){
                    $reward->update(['activated' => 1]);
                }

            }
        }


    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getSum()
    {
        return $this->sum;
    }
}
