<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    public function __construct(
        private readonly string $otp,
        private readonly string $customerName = 'there',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ' . config('app.name') . ' verification code')
            ->greeting("Hi {$this->customerName}!")
            ->line('Use the code below to verify your identity. It expires in **10 minutes**.')
            ->line('')
            ->line('## ' . $this->otp)
            ->line('')
            ->line('If you didn\'t request this code, you can safely ignore this email.')
            ->salutation('The ' . config('app.name') . ' team');
    }
}
