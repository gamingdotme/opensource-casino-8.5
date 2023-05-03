<?php

namespace VanguardLTE\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class NewTicket extends Notification
{
    /**
     * Email confirmation token.
     *
     * @var string
     */
    public $ticket;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = json_decode($ticket, true);
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
        $subject = "New Ticket #". $this->ticket['id'];


        //if( !$this->ticket['admin'] ){
            //$notifiable->email = setting('tickets_email');
        //}


        return (new MailMessage)
            ->subject($subject)
            ->line($this->ticket['theme'])
            ->line(new HtmlString($this->ticket['text']));
    }
}
