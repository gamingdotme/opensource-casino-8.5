<?php

namespace VanguardLTE\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use VanguardLTE\User;

class UserRegistered extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * Create a new notification instance.
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = sprintf("%s - %s", settings('app_name'), trans('app.new_user_registration'));

        return (new MailMessage)
            ->subject($subject)
            ->line(trans('app.new_user_was_registered_on', ['app' => settings('app_name')]))
            ->line(trans('app.to_view_details_visit_link_below'))
            ->action(trans('app.view_user'), route('frontend.user.show', $this->registeredUser->id))
            ->line(trans('app.thank_you_for_using_our_app'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
