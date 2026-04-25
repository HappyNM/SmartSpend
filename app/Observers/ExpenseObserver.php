<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Expense;
use App\Notifications\BudgetLimitExceededNotification;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        $user = $expense->user;

        if (! $user) {
            return;
        }

        $budgets = Budget::with('category')
            ->where('user_id', $expense->user_id)
            ->where('month', (int) $expense->date->month)
            ->where('year', (int) $expense->date->year)
            ->get();

        foreach ($budgets as $budget) {
            if (! $this->expenseAffectsBudget($expense, $budget)) {
                continue;
            }

            $spentAfter = (float) $budget->getSpentAmount();
            $spentBefore = max(0, $spentAfter - (float) $expense->amount);
            $budgetLimit = (float) $budget->amount;

            // Notify only when this new expense crosses the limit boundary.
            if ($spentAfter > $budgetLimit && $spentBefore <= $budgetLimit) {
                $user->notify(new BudgetLimitExceededNotification($budget, $expense, $spentAfter));
            }
        }
    }

    private function expenseAffectsBudget(Expense $expense, Budget $budget): bool
    {
        if ($budget->category_id !== null) {
            return (int) $budget->category_id === (int) $expense->category_id;
        }

        return $expense->category_id === null;
    }
}
