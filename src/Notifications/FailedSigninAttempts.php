<?php

namespace Shamarkellman\AuthLogger\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Shamarkellman\AuthLogger\Models\AuthLog;

class FailedSigninAttempts extends Notification
{
    use Queueable;
    /**
     * @var AuthLog
     */
    private $authLog;

    /**
     * Create a new notification instance.
     *
     * @param AuthLog $authLog
     */
    public function __construct(AuthLog $authLog)
    {
        $this->authLog = $authLog;
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
        return (new MailMessage)
            ->subject(trans('auth-logger::messages.failed-subject'))
            ->markdown('auth-logger::emails.failed-attempts', [
                'account' => $notifiable,
                'time' => $this->authLog->created_at,
                'ipAddress' => $this->authLog->ip_address,
                'location' => $this->authLog->location,
                'browser' => $this->authLog->user_agent,
            ]);
    }
}
