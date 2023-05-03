<?php

namespace VanguardLTE\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserWithdrawRequest extends Mailable
{
    use Queueable, SerializesModels;

	/**
     * Email confirmation pincode.
     *
     * @var string
     */
    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Withdrawal Request')->markdown('emails.user_withdraw_request')->with('details', $this->details);
    }
}
