<?php

namespace VanguardLTE\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class NewAnswer extends Notification
{
    /**
     * Email confirmation token.
     *
     * @var string
     */
    public $answer;
    public $ticket;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($answer, $ticket)
    {
        $this->answer = $answer;
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = "Ticket #". $this->answer->ticket_id ." Answered";

        //if( !$this->answer->user->hasRole('admin') ){
            //$notifiable->email = setting('tickets_email');
        //}

        return (new MailMessage)
            ->subject($subject)
            ->line(new HtmlString(view('emails.new-answer', ['answer' => $this->answer, 'ticket' =>  $this->ticket]))
            );

    }
}
