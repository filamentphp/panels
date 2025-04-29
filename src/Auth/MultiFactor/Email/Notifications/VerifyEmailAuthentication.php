<?php

namespace Filament\Auth\MultiFactor\Email\Notifications;

use Exception;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailAuthentication extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $codeWindow,
    ) {}

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if (! ($notifiable instanceof HasEmailAuthentication)) {
            throw new Exception('The user model must implement the [' . HasEmailAuthentication::class . '] interface to use email authentication.');
        }

        $expiryMinutes = ceil($this->codeWindow / 2);

        return (new MailMessage)
            ->subject(__('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.subject'))
            ->line(trans_choice('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.lines.0', $expiryMinutes, ['code' => $this->code, 'minutes' => $expiryMinutes]))
            ->line(trans_choice('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.lines.1', $expiryMinutes, ['code' => $this->code, 'minutes' => $expiryMinutes]));
    }
}
