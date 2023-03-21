<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserAccountGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public string $password;
    public string $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
        $this->actionUrl = env('SPA_URL') . '/a/login';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
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
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
