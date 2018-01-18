<?php

namespace Shamarkellman\AuthLogger\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Shamarkellman\AuthLogger\Models\AuthLog;

class SigninFromNewDevice extends Notification
{
    use Queueable;

    /**
     * The authentication log.
     *
     * @var AuthLog
     */
    public $authLog;

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
        return $notifiable->notifyAuthenticationLogVia();
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
            ->subject(trans('auth-logger::messages.new-device-subject'))
            ->markdown('auth-logger::emails.new-device', [
                'account' => $notifiable,
                'time' => $this->authLog->login_at,
                'ipAddress' => $this->authLog->ip_address,
                'location' => $this->authLog->location,
                'browser' => $this->authLog->user_agent,
            ]);
    }
}
