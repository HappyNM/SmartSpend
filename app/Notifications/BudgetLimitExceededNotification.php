<?php

namespace App\Notifications;

use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetLimitExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Budget $budget,
        private readonly Expense $expense,
        private readonly float $spentAmount,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $budgetName = $this->budget->category?->name ?? 'Overall Budget';
        $budgetLimit = (float) $this->budget->amount;
        $overBy = max(0, $this->spentAmount - $budgetLimit);

        return (new MailMessage)
            ->subject('Budget Limit Exceeded')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('You have exceeded one of your budget limits.')
            ->line('Budget: ' . $budgetName)
            ->line('Limit: $' . number_format($budgetLimit, 2))
            ->line('Current spent: $' . number_format($this->spentAmount, 2))
            ->line('Exceeded by: $' . number_format($overBy, 2))
            ->line('Latest expense: ' . $this->expense->title . ' ($' . number_format((float) $this->expense->amount, 2) . ')')
            ->action('Review Budgets', url('/budgets'))
            ->line('Consider adjusting your spending or updating your budget.');
    }
}
