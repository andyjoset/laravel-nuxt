<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserAccountGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;
    public $actionUrl;

    /**
     * Create a new notification instance.
     *
     * @param  string  $password
     * @return void
     */
    public function __construct($password)
    {
        $this->password = $password;
        $this->actionUrl = env('SPA_URL') . '/a/login';
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
        $appName = env('APP_NAME');

        return (new MailMessage)
                    ->subject(trans('Welcome to :appName', ['appName' => $appName]))
                    ->greeting(trans('Hello :name!', ['name' => $notifiable->name]))
                    ->line(trans('Welcome to :appName, your account is ready!', ['appName' => $appName]))
                    ->line(trans('Your password is: :password', ['password' => $this->password]))
                    ->action(trans('Login'), $this->actionUrl)
                    ->salutation(env('APP_NAME'));
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
