<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FundsLockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly float $amount,
        private readonly string $lockType,
        private readonly string $goalName,
    ) {
        $this->onConnection('database');
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $humanLockType = ucwords(str_replace('_', ' ', $this->lockType));

        return (new MailMessage)
            ->subject('Funds Locked Successfully')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your wallet funds have been locked successfully.')
            ->line('Amount: $' . number_format($this->amount, 2))
            ->line('Lock type: ' . $humanLockType)
            ->line('Goal: ' . $this->goalName)
            ->action('View Savings Goals', url('/wallet/goals'))
            ->line('Thank you for using SmartSpend.');
    }
}
