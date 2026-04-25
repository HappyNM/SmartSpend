<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletWithdrawalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly float $amount,
        private readonly string $source,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Wallet Withdrawal Alert')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A withdrawal has been made from your wallet.')
            ->line('Amount: $' . number_format($this->amount, 2))
            ->line('Source: ' . ucfirst($this->source))
            ->action('View Wallet', url('/wallet'))
            ->line('If this was not you, please review your account activity immediately.');
    }
}
